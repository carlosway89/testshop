<?php
/* --------------------------------------------------------------
   HttpViewController.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpViewControllerInterface');

/**
 * Class HttpViewController
 *
 * This class contains some helper methods for handling view requests. Be careful
 * always when outputting raw user data to HTML or when handling POST requests because
 * insufficient protection will lead to XSS and CSRF vulnerabilities.
 *
 * @link       http://en.wikipedia.org/wiki/Cross-site_scripting
 * @link       http://en.wikipedia.org/wiki/Cross-site_request_forgery
 *
 * @category   System
 * @package    Http
 * @implements HttpViewControllerInterface
 */
class HttpViewController implements HttpViewControllerInterface
{
	/**
	 * @var \HttpContextReaderInterface
	 */
	protected $httpContextReader;
	/**
	 * @var \HttpResponseProcessorInterface
	 */
	protected $httpResponseProcessor;
	/**
	 * @var \ContentViewInterface
	 */
	protected $contentView;

	/**
	 * @var array
	 */
	protected $queryParametersArray;
	/**
	 * @var array
	 */
	protected $postDataArray;

	/**
	 * @var AssetCollectionInterface Contain the assets needed to be included in the view HTML.
	 */
	protected $assets;


	/**
	 * @param \HttpContextReaderInterface     $httpContextReader
	 * @param \HttpResponseProcessorInterface $httpResponseProcessor
	 * @param \ContentViewInterface           $defaultContentView
	 */
	public function __construct(HttpContextReaderInterface $httpContextReader,
	                            HttpResponseProcessorInterface $httpResponseProcessor,
	                            ContentViewInterface $defaultContentView)
	{
		$this->httpContextReader     = $httpContextReader;
		$this->httpResponseProcessor = $httpResponseProcessor;
		$this->contentView           = $defaultContentView;
		$this->assets                = MainFactory::create('AssetCollection');
	}


	/**
	 * Process HttpContext Object
	 *
	 * Re-implement this function in the child controller and disable the XSS and CSRF protection on demand.
	 *
	 * @param \HttpContextInterface $httpContext
	 *
	 * @throws \LogicException
	 */
	public function proceed(HttpContextInterface $httpContext)
	{
		$this->queryParametersArray = $this->httpContextReader->getQueryParameters($httpContext);
		$this->postDataArray        = $this->httpContextReader->getPostData($httpContext);

		$actionName = $this->httpContextReader->getActionName($httpContext);
		$response   = $this->_callActionMethod($actionName);

		$this->httpResponseProcessor->proceed($response);
	}


	/**
	 * @return \HttpControllerResponseInterface
	 */
	public function actionDefault()
	{
		return new HttpControllerResponse('');
	}


	/**
	 * @param string $p_actionName
	 *
	 * @return HttpControllerResponseInterface
	 * @throws \LogicException
	 */
	protected function _callActionMethod($p_actionName)
	{
		if(empty($p_actionName))
		{
			$methodName = 'actionDefault';
		}
		else
		{
			$methodName = 'action' . $p_actionName;
		}

		if(method_exists($this, $methodName) == false)
		{
			throw new LogicException('Action method not found for: ' . htmlspecialchars($p_actionName));
		}

		$response = call_user_func(array($this, $methodName));

		return $response;
	}


	/**
	 * @param string $p_templateFile
	 * @param array  $contentArray
	 *
	 * @return string
	 */
	protected function _render($p_templateFile, array $contentArray)
	{
		$this->contentView->set_content_template($p_templateFile);

		foreach($contentArray as $contentItemKey => $contentItemValue)
		{
			$this->contentView->set_content_data($contentItemKey, $contentItemValue);
		}

		return $this->contentView->get_html();
	}


	/**
	 * @return KeyValueCollection
	 * @throws \InvalidArgumentException
	 */
	protected function _getQueryParametersCollection()
	{
		return MainFactory::create('KeyValueCollection', $this->queryParametersArray);
	}


	/**
	 * @return KeyValueCollection
	 * @throws \InvalidArgumentException
	 */
	protected function _getPostDataCollection()
	{
		return MainFactory::create('KeyValueCollection', $this->postDataArray);
	}


	/**
	 * @param string $p_keyName
	 *
	 * @return string|null
	 */
	protected function _getQueryParameter($p_keyName)
	{
		if(isset($this->queryParametersArray[$p_keyName]) == false)
		{
			return null;
		}

		return $this->queryParametersArray[$p_keyName];
	}


	/**
	 * Get post data value.
	 *
	 * @param string $p_keyName
	 *
	 * @return string|null
	 */
	protected function _getPostData($p_keyName)
	{
		if(isset($this->postDataArray[$p_keyName]) == false)
		{
			return null;
		}

		return $this->postDataArray[$p_keyName];
	}


	/**
	 * Check if the $_POST['pageToken'] or $_GET['pageToken'] variable is provided and if it's valid.
	 *
	 * Example:
	 *   public function proceed(HttpContextInterface $httpContext)
	 *   {
	 *     parent::proceed($httpContext); // proceed http context from parent class
	 *     if($_SERVER['REQUEST_METHOD'] === 'POST')
	 *     {
	 *        $this->_validatePageToken(); // CSRF Protection
	 *     }
	 *   }
	 *
	 * @param string $customExceptionMessage (optional) You can specify a custom exception message.
	 *
	 * @throws Exception If the validation fails.
	 */
	protected function _validatePageToken($customExceptionMessage = null)
	{
		$pageToken = $this->_getPostData('pageToken') ?: $this->_getQueryParameter('pageToken'); 
		
		if($pageToken === null)
		{
			throw new Exception($customExceptionMessage ? : '$_POST["pageToken"] variable was not provided with the POST request.');
		}

		if(!$_SESSION['coo_page_token']->is_valid($pageToken))
		{
			throw new Exception($customExceptionMessage ? : 'Provided $_POST["pageToken"] variable is not valid.');
		}
	}
}