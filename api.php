<?php
/* --------------------------------------------------------------
   api.php 2015-10-07 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

define('API_V2_ENVIRONMENT', 'production'); // 'development', 'test', 'production'

/**
 * Gambio GX2 - API (implemented with Slim Framework)
 *
 * @link http://www.slimframework.com
 *
 * Hit this file directly with new requests and it will route them to their corresponding API
 * controllers. Controller files reside inside the "GXEngine/Controllers/Api" directory and are
 * separated by version. This separation enables the addition of newer API versions in the future.
 *
 * Since v2 the shop API is RESTful and that means that it supports a variety of HTTP methods
 * in order to implement a semantic interface for client developers. You can use one of the GET,
 * POST, PUT, DELETE, PATCH, HEAD, OPTIONS methods in your controller classes. Check the
 * "HttpApiV2Controller" class for more information on how to create your own controller.
 *
 * @link http://en.wikipedia.org/wiki/Representational_state_transfer
 * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html
 *
 * It is important that each API version is able to route the controllers differently because
 * the codebase will be more flexible and easy to maintain. Expand the current file with new
 * controller-routing rules for future versions.
 *
 * You can generate detailed API documentation through ApiDoc. It is a NodeJS command line tool
 * that parses specific DocBlock comments and creates rich content output. It's always preferable
 * that API methods are well-documented so that is easier for external developers to use them.
 *
 * @link http://apidocjs.com
 *
 * Version 2.0.0 of the API uses HTTP Basic Authentication and that means that authorization
 * credentials are transfered over the wire. Always use HTTPS when accessing the API.
 *
 * http://en.wikipedia.org/wiki/Basic_access_authentication
 */

// ----------------------------------------------------------------------------
// INITIALIZE API - SLIM FRAMEWORK
// ----------------------------------------------------------------------------

switch(API_V2_ENVIRONMENT)
{
	case 'development': // complete verbose output when errors occur
		$config = array(
				'mode'  => 'development',
				'debug' => true
		);
		break;
	case 'test': // includes php errors in the response body
		$config = array(
				'mode'  => 'test',
				'debug' => false
		);
		break;

	case 'production': // will suppress any error information
		$config = array(
				'mode'  => 'production',
				'debug' => false
		);
		break;

	default:
		throw new Exception('Invalid APIv2 environment selected: ' . API_V2_ENVIRONMENT);
}

require __DIR__ . '/includes/application_top.php';

$api = new \Slim\Slim($config);

// ----------------------------------------------------------------------------
// CONTROLLER ROUTING FOR V2
// ----------------------------------------------------------------------------

$api->map('/v2(/:uri+)', function ($uri = array()) use ($api)
{
	$controllerName = (!empty($uri)) ?
			ucfirst($uri[0]) . 'ApiV2Controller' : HttpApiV2Controller::DEFAULT_CONTROLLER_NAME;

	// Check if the resource exists (there is no such method in MainFactory so we use the autoloader function).
	if(!class_exists($controllerName))
	{
		throw new HttpApiV2Exception('Resource not found.', 404);
	}

	$controller = MainFactory::create($controllerName, $api, $uri);
	$method     = strtolower($api->request->getMethod());
	$resource   = array($controller, $method);

	if(!is_callable($resource))
	{
		throw new HttpApiV2Exception('The requested resource is not supported by the API v2.', 400);
	}

	call_user_func($resource);
})->via('GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'); // Supported request methods

// ----------------------------------------------------------------------------
// API ERROR HANDLING
// ----------------------------------------------------------------------------

$api->error(function (\Exception $ex) use ($api)
{
	$responseErrorCode = 500; // The default value for exceptions on server.

	if(is_a($ex, 'HttpApiV2Exception')) // An HttpApiException will contain a specific HTTP status code.
	{
		$responseErrorCode = $ex->getCode();
	}

	$api->response->setStatus($responseErrorCode);
	$api->response->headers->set('Content-Type', 'application/json');

	$response = array(
			'code'    => $ex->getCode(),
			'status'  => 'error',
			'message' => $ex->getMessage(),
			'request' => array(
					'method' => $api->request->getMethod(),
					'url'    => $api->request->getUrl(),
					'path'   => $api->request->getPath(),
					'uri'    => array(
							'root'     => $api->request->getRootUri(),
							'resource' => $api->request->getResourceUri()
					)
			)
	);

	// Provide error stack only in 'test' mode.
	if($api->config('mode') === 'test')
	{
		$response['error'] = array(
				'file'  => $ex->getFile(),
				'line'  => $ex->getLine(),
				'stack' => $ex->getTrace()
		);
	}

	if(defined(JSON_PRETTY_PRINT) && defined(JSON_UNESCAPED_SLASHES))
	{
		$responseJsonString = json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
	}
	else
	{
		$responseJsonString = json_encode($response); // PHP v5.3
	}

	$api->response->write($responseJsonString);
});

// ----------------------------------------------------------------------------
// API EXECUTION
// ----------------------------------------------------------------------------

$api->run();
