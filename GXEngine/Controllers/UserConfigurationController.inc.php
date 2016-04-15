<?php
/* --------------------------------------------------------------
   UserConfigurationController.inc.php 2015-08-25 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class UserConfigurationController
 */
class UserConfigurationController extends AdminHttpViewController
{
	/**
	 * @var array
	 */
	protected $postDataArray;

	/**
	 * @var array
	 */
	protected $queryParametersArray;

	/**
	 * @var UserConfigurationService
	 */
	private $userConfigurationService;


	/**
	 * Initialize the controller.
	 *
	 * Perform the common operations before the parent class proceeds with the controller
	 * method execution. In this case every method needs the EmailService so it is loaded
	 * once before every method.
	 *
	 * @param HttpContextInterface $httpContext
	 */
	public function proceed(HttpContextInterface $httpContext)
	{
		$this->userConfigurationService = StaticGXCoreLoader::getService('UserConfiguration');
		parent::proceed($httpContext); // proceed http context from parent class
	}


	/**
	 * @return HttpControllerResponse|RedirectHttpControllerResponse
	 */
	public function actionDefault()
	{
		return new JsonHttpControllerResponse(array());
	}


	/**
	 * Sets a user configuration (to DB table user_configuration)
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionSet()
	{
		if(!isset($this->postDataArray['userId']) || !isset($this->postDataArray['configurationKey'])
		   || !isset($this->postDataArray['configurationValue'])
		)
		{
			return new JsonHttpControllerResponse(array('success' => false));
		}

		$this->userConfigurationService->setUserConfiguration(MainFactory::create('Id',
		                                                                          (int)$this->postDataArray['userId']),
		                                                      $this->postDataArray['configurationKey'],
		                                                      $this->postDataArray['configurationValue']);

		return new JsonHttpControllerResponse(array('success' => true));
	}


	/**
	 * Gets a user configuration (from DB table user_configuration)
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionGet()
	{
		if(!isset($this->queryParametersArray['userId']) || !isset($this->queryParametersArray['configurationKey']))
		{
			return new JsonHttpControllerResponse(array('success' => false));
		}

		$configurationValue = $this->userConfigurationService->getUserConfiguration(MainFactory::create('Id',
		                                                                                                (int)$this->queryParametersArray['userId']),
		                                                                            $this->queryParametersArray['configurationKey']);

		return new JsonHttpControllerResponse(array('success' => true, 'configurationValue' => $configurationValue));
	}
}