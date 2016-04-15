<?php
/* --------------------------------------------------------------
   ApiController.php 2015-07-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2ControllerInterface');

/**
 * Class HttpApiV2Controller
 *
 * Contains common functionality for all the GX2 APIv2 controllers. You can use the $api instance in the
 * child-controllers in order to gain access to request and response information. The $uri variable is an
 * array that contains the requested resource path.
 *
 * You can use a protected "__initialize" method in your child controllers for performing common operations
 * without overriding the parent constructor method.
 *
 * This class contains some private methods that define the core operations of each controller and should
 * not be called from a child-controller (like validation, authorization, rate limiting). The only way to
 * disable the execution of these methods is to override the controller.
 *
 * @todo     Add _cacheResponse() helper function which will cache request data and it will provide the required
 *           headers.
 *
 * @category System
 * @package  ApiV2Controllers
 */
class HttpApiV2Controller implements HttpApiV2ControllerInterface
{
	/**
	 * Defines the default page offset for responses that return multiple items.
	 *
	 * @var int
	 */
	const DEFAULT_PAGE_ITEMS = 50;

	/**
	 * Default controller to be loaded when no resource was selected.
	 *
	 * @var string
	 */
	const DEFAULT_CONTROLLER_NAME = 'DefaultApiV2Controller';

	/**
	 * Defines the maximum request limit for an authorized client.
	 *
	 * @var int
	 */
	const DEFAULT_RATE_LIMIT = 5000;

	/**
	 * Defines the duration of an API session in minutes.
	 *
	 * @var int
	 */
	const DEFAULT_RATE_RESET_PERIOD = 15;

	/**
	 * Slim Framework instance is used to manipulate the request or response data.
	 *
	 * @var \Slim\Slim
	 */
	protected $api;

	/**
	 * Contains the request URI segments after the root api version segment.
	 *
	 * Example:
	 *    URI  - api.php/v2/customers/73/addresses
	 *    CODE - $this->uri[1]; // will return '73'
	 *
	 * @var array
	 */
	protected $uri;


	/**
	 * HttpApiV2Controller Constructor
	 *
	 * Call this constructor from every child controller class in order to set the
	 * Slim instance and the request routes arguments to the class.
	 *
	 * @param \Slim\Slim $api Slim framework instance, used for request/response manipulation.
	 * @param array      $uri This array contains all the segments of the current request, starting from the resource.
	 */
	public function __construct(\Slim\Slim $api, array $uri)
	{
		$this->api = $api;
		$this->uri = $uri;

		if(method_exists($this, '__initialize')) // Method for child-controller initialization stuff ...
		{
			call_user_func(array($this, '__initialize'));
		}

		$this->_validateRequest();
		$this->_prepareResponse();
	}


	/**
	 * [PRIVATE] Validate request before proceeding with response.
	 *
	 * This method will validate the request headers, user authentication and other parameters
	 * before the controller proceeds with the response.
	 *
	 * Not available to child-controllers (private method).
	 *
	 * @throws HttpApiV2Exception If validation fails - 415 Unsupported media type.
	 */
	private function _validateRequest()
	{
		$requestMethod = $this->api->request->getMethod();
		$contentType   = $this->api->request->headers->get('Content-Type');

		if(($requestMethod === 'POST' || $requestMethod === 'PUT' || $requestMethod === 'PATCH')
		   && empty($_FILES)
		   && $contentType !== 'application/json'
		)
		{
			throw new HttpApiV2Exception('Unsupported Media Type HTTP', 415);
		}

		$this->_authorize();
		$this->_setRateLimitHeader();
	}


	/**
	 * [PRIVATE] Prepare response headers.
	 *
	 * This method will prepare default attributes of the API responses. Further response
	 * settings must be set explicitly from each controller method separately.
	 *
	 * Not available to child-controllers (private method).
	 */
	private function _prepareResponse()
	{
		$this->api->response->setStatus(200);
		$this->api->response->headers->set('Content-Type', 'application/json; charset=utf-8');
	}


	/**
	 * [PRIVATE] Authorize request with HTTP Basic Authorization
	 *
	 * Call this method in every API operation that needs to be authorized with the HTTP Basic
	 * Authorization technique.
	 *
	 * @link http://php.net/manual/en/features.http-auth.php
	 *
	 * Not available to child-controllers (private method).
	 *
	 * @throws HttpApiV2Exception If request does not provide the "Authorization" header or if the
	 *                            credentials are invalid.
	 *
	 * @todo Use LoginService when it's implemented.
	 */
	private function _authorize()
	{
		if(!isset($_SERVER['PHP_AUTH_USER']))
		{
			$this->api->response->headers->set('WWW-Authenticate', 'Basic realm="Gambio GX2 APIv2 Login"');
			throw new HttpApiV2Exception('Unauthorized', 401);
		}

		$username = $_SERVER['PHP_AUTH_USER'];
		$password = $_SERVER['PHP_AUTH_PW'];

		$query = '
			SELECT customers_password
			FROM customers
			WHERE
				customers_email_address = "' . xtc_db_input($username) . '" AND
				customers_password = "' . xtc_db_input(md5($password)) . '" AND
				customers_status = 0
		';

		$result = xtc_db_query($query, 'db_link', false);

		if(xtc_db_num_rows($result) === 0)
		{
			throw new HttpApiV2Exception('Invalid Credentials', 401);
		}
		// Credentials were correct, continue execution ...
	}


	/**
	 * [PRIVATE] Handle rate limit headers.
	 *
	 * There is a cache file that will store each user session and provide a security
	 * mechanism that will protect the shop from DOS attacks or service overuse. Each
	 * session will use the hashed "Authorization header" to identify the client. When
	 * the limit is reached a "HTTP/1.1 429 Too Many Requests" will be returned.
	 *
	 * Headers:
	 *   X-Rate-Limit-Limit     >> Max number of requests allowed.
	 *   X-Rate-Limit-Remaining >> Number of requests remaining.
	 *   X-Rate-Limit-Reset     >> UTC epoch seconds until the limit is reset.
	 *
	 * Important: This method will be executed in every API call and it might slow the
	 * response time due to filesystem operations. If the difference is significant
	 * then it should be optimized.
	 *
	 * Not available to child-controllers (private method).
	 *
	 * @throws HttpApiV2Exception If request limit exceed - 429 Too Many Requests
	 */
	private function _setRateLimitHeader()
	{
		// Load or create cache file. 
		$cacheFilePath = DIR_FS_CATALOG . 'cache/gxapi_v2_sessions_' . FileLog::get_secure_token();
		if(!file_exists($cacheFilePath))
		{
			touch($cacheFilePath);
			$sessions = array();
		}
		else
		{
			$sessions = unserialize(file_get_contents($cacheFilePath));
		}

		// Clear expired sessions. 
		foreach($sessions as $index => $session)
		{
			if($session['reset'] < time())
			{
				unset($sessions[$index]);
			}
		}

		// Get session identifier from request. 
		$identifier = md5($this->api->request->headers->get('Authorization'));
		if(empty($identifier))
		{
			throw new HttpApiV2Exception('Remote address value was not provided.', 400);
		}

		// Check session entry, if not found create one.
		if(!isset($sessions[$identifier]))
		{
			$sessions[$identifier] = array(
					'limit'     => self::DEFAULT_RATE_LIMIT,
					'remaining' => self::DEFAULT_RATE_LIMIT,
					'reset'     => time() + (self::DEFAULT_RATE_RESET_PERIOD * 60)
			);
		}
		else if($sessions[$identifier]['remaining'] <= 0)
		{
			throw new HttpApiV2Exception('Request limit was reached.', 429);
		}

		// Set rate limiting headers to response. 
		$sessions[$identifier]['remaining']--;
		$this->api->response->headers->set('X-Rate-Limit-Limit', $sessions[$identifier]['limit']);
		$this->api->response->headers->set('X-Rate-Limit-Remaining', $sessions[$identifier]['remaining']);
		$this->api->response->headers->set('X-Rate-Limit-Reset', $sessions[$identifier]['reset']);

		file_put_contents($cacheFilePath, serialize($sessions));
	}


	/**
	 * [PRIVATE] Set header pagination links.
	 *
	 * Useful for GET responses that return multiple items to the client. The client
	 * can use the links to navigate through the records without having to construct
	 * them on its own.
	 *
	 * @link http://www.w3.org/wiki/LinkHeader
	 *
	 * Not available to child-controllers (private method).
	 *
	 * @param int $p_currentPage    Current request page number.
	 * @param int $p_itemsPerPage   The number of items to be returned in each page.
	 * @param int $p_totalItemCount Total number of the resource items.
	 *
	 * @throws HttpApiV2Exception If one of the parameters are invalid.
	 */
	private function _setPaginationHeader($p_currentPage, $p_itemsPerPage, $p_totalItemCount)
	{
		if($p_itemsPerPage <= 0)
		{
			throw new HttpApiV2Exception('Items per page number must not be below 1.', 400);
		}

		$totalPages  = ceil($p_totalItemCount / $p_itemsPerPage);
		$linksArray  = array();
		$baseLinkUri = HTTP_SERVER . $this->api->request->getRootUri() . $this->api->request->getResourceUri();
		$getParams   = $this->api->request->get();

		if($p_currentPage > 1)
		{
			$getParams['page']   = 1;
			$linksArray['first'] = '<' . $baseLinkUri . '?' . http_build_query($getParams) . '>; rel="first"';

			$getParams['page']      = $p_currentPage - 1;
			$linksArray['previous'] = '<' . $baseLinkUri . '?' . http_build_query($getParams) . '>; rel="previous"';
		}

		if($p_currentPage < $totalPages)
		{
			$getParams['page']  = $p_currentPage + 1;
			$linksArray['next'] = '<' . $baseLinkUri . '?' . http_build_query($getParams) . '>; rel="next"';

			$getParams['page']  = $totalPages;
			$linksArray['last'] = '<' . $baseLinkUri . '?' . http_build_query($getParams) . '>; rel="last"';
		}

		$this->api->response->headers->set('Link', implode(',' . PHP_EOL, $linksArray));
	}


	/**
	 * Sort response array with the "sort" GET parameter.
	 *
	 * This method supports nested sort values, so by providing a "+address.street" value
	 * to the "sort" GET parameter the records will be sort by street value in ascending
	 * order. Method supports sorting up to 5 fields.
	 *
	 * Important #1:
	 *    This method has some advantages and disadvantages over the classic database sort mechanism. First it
	 *    does not need mapping between the API fields and the database fields. Second it does not depend on
	 *    external system code to sort the response items, so if for example a domain-service does not support
	 *    sorting the result can still be sorted before sent to the client. The disadvantages are that it will
	 *    only support a predefined number of fields and this is a trade-off because the method should not use
	 *    the "eval" function, which will introduce security risks. Furthermore it might be a bit slower than
	 *    the database sorting.
	 *
	 * Important #2:
	 *    This method is using PHP's array_multisort which by default will sort strings in a case sensitive
	 *    manner. That means that strings starting with a capital letter will come before strings starting
	 *    with a lowercase letter.
	 *    http://php.net/manual/en/function.array-multisort.php
	 *
	 * Example:
	 *   // will sort ascending by customer ID and descending by customer company
	 *   api.php/v2/customers?sort=+id,-address.company
	 *
	 * @param array $response Passed by reference, contains an array of the multiple items
	 *                        that will returned as a response to the client.
	 */
	protected function _sortResponse(array &$response)
	{
		if($this->api->request->get('sort') === null)
		{
			return; // no sort parameter was provided
		}

		$params = explode(',', $this->api->request->get('sort'));

		for($i = 0; $i < 5; $i++)
		{
			$sort[$i] = array(
					'array'     => array_fill(0, count($response), ''),
					'direction' => SORT_ASC // default
			);
		}

		foreach($params as $paramIndex => &$param)
		{
			$fields = explode('.', substr($param, 1));

			foreach($response as $itemIndex => $item)
			{
				$value = $item;
				foreach($fields as $field)
				{
					$value = $value[$field];
				}

				$sort[$paramIndex]['direction']         = (substr($param, 0, 1) === '-') ? SORT_DESC : SORT_ASC;
				$sort[$paramIndex]['array'][$itemIndex] = $value;
			}
		}

		// Multisort array (currently supports up to 5 sort fields).
		array_multisort($sort[0]['array'], $sort[0]['direction'], $sort[1]['array'], $sort[1]['direction'],
		                $sort[2]['array'], $sort[2]['direction'], $sort[3]['array'], $sort[3]['direction'],
		                $sort[4]['array'], $sort[4]['direction'], $response);
	}


	/**
	 * Minimize response using the $fields parameter.
	 *
	 * APIv2 supports the GET "fields" parameter which enables the client to select the
	 * exact fields to be included in the response. It does not support nested fields,
	 * only first-level.
	 *
	 * You can provide both associative (single response item) or sequential (multiple response
	 * items) arrays and this method will adjust the links accordingly.
	 *
	 * @param array $response Passed by reference, it will be minified to the required fields.
	 */
	protected function _minimizeResponse(array &$response)
	{
		if($this->api->request->get('fields') === null)
		{
			return; // no minification parameter was provided
		}

		$fields = explode(',', $this->api->request->get('fields'));
		$map    = array();
		foreach($fields as $field)
		{
			$field       = array_shift(explode('.', $field)); // take only the first field
			$map[$field] = array();
		}

		// If $response array is associative then converted to sequential array.
		$revertBackToAssociative = false;
		if(key($response) !== 0 && !is_array($response[0]))
		{
			$response                = array($response);
			$revertBackToAssociative = true;
		}

		// Minimize all the items. 
		foreach($response as &$item)
		{
			$item = array_intersect_key($item, $map);
		}

		// Revert back to associative (if necessary).
		if($revertBackToAssociative)
		{
			$response = $response[0];
		}
	}


	/**
	 * Paginate response using the $page and $per_page GET parameters.
	 *
	 * One of the common functionalities of the APIv2 is the pagination and this can be
	 * easily achieved by this function which will update the response with the records
	 * that need to be returned. This method will automatically set the pagination headers
	 * in the response so that client apps can easily navigate through results.
	 *
	 * @param array $response Passed by reference, it will be paginated according to the provided parameters.
	 */
	protected function _paginateResponse(array &$response)
	{
		if($this->api->request->get('page') === null)
		{
			return; // no pagination parameter was provided
		}

		$limit          = ($this->api->request->get('per_page')
		                   !== null) ? $this->api->request->get('per_page') : self::DEFAULT_PAGE_ITEMS;
		$offset         = $limit * ((int)$this->api->request->get('page') - 1);
		$totalItemCount = count($response);
		$this->_setPaginationHeader($this->api->request->get('page'), $limit, $totalItemCount);
		$response = array_slice($response, $offset, $limit);
	}


	/**
	 * Include links to response resources.
	 *
	 * The APIv2 operates with simple resources that might be linked with other resources. This
	 * architecture promotes flexibility so that API consumers can have a simpler structure. This
	 * method will search for existing external resources and will add a link to the end of each
	 * resource.
	 *
	 * IMPORTANT: If for some reason you need to include custom links to your resources
	 * do not use this method. Include them inside your controller method manually.
	 *
	 * NOTICE #1: This method will only search at the first level of the resource. That means that
	 * nested ID values will not be taken into concern.
	 *
	 * NOTICE #2: You can provide both associative (single response item) or sequential (multiple response
	 * items) arrays and this method will adjust the links accordingly.
	 *
	 * @param array $response Passed by reference, new links will be appended into the end
	 *                        of each resource.
	 */
	protected function _linkResponse(array &$response)
	{
		if($this->api->request->get('disable_links') !== null || count($response) === 0)
		{
			return; // client does not require links
		}

		// Define the link mappings to the resources. 
		$map = array(
				'customerId' => 'customers',
				'addressId'  => 'addresses',
				'countryId'  => 'countries',
				'zoneId'     => 'zones'
		);

		// If $response array is associative then converted to sequential array. 
		$revertBackToAssociative = false;
		if(key($response) !== 0 && !is_array($response[0]))
		{
			$response                = array($response);
			$revertBackToAssociative = true;
		}

		// Parse the resource results and add the links.
		foreach($response as &$item)
		{
			$links = array(); // will be appended to each resource

			foreach($map as $key => $resource)
			{
				if(array_key_exists($key, $item) && $item[$key] !== null)
				{
					$links[str_replace('Id', '', $key)] =
							HTTPS_SERVER . $this->api->request->getRootUri() . '/v2/' . $resource . '/' . $item[$key];
				}
			}

			$item['_links'] = $links;
		}

		if($revertBackToAssociative)
		{
			$response = $response[0];
		}
	}


	/**
	 * Write JSON encoded response data.
	 *
	 * Use this method to write a JSON encoded, pretty printed and unescaped response to
	 * the client consumer. It is very important that the API provides pretty printed responses
	 * because it is easier for users to debug and develop.
	 *
	 * IMPORTANT: PHP v5.3 does not support the JSON_PRETTY_PRINT and JSON_UNESCAPED_SLASHES so
	 * this method will check for their existance and then use them if possible.
	 *
	 * @param array $response     Contains the response data to be written.
	 * @param int   $p_statusCode (optional) Provide a custom status code for the response, default 200 - Success.
	 */
	protected function _writeResponse(array $response, $p_statusCode = 200)
	{
		if($p_statusCode !== 200 && is_numeric($p_statusCode))
		{
			$this->api->response->setStatus((int)$p_statusCode);
		}

		if(defined('JSON_PRETTY_PRINT') && defined('JSON_UNESCAPED_SLASHES'))
		{
			$responseJsonString = json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		}
		else
		{
			$responseJsonString = json_encode($response); // PHP v5.3
		}
		
		$this->api->response->write($responseJsonString);
	}
}