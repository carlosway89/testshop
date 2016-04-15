<?php
/* --------------------------------------------------------------
   EmbeddedModuleController.inc.php 2015-12-03 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

class EmbeddedModuleController extends AdminHttpViewController
{
	/**
	 * Initializes the controller
	 *
	 * @param \HttpContextInterface $httpContext
	 */
	public function proceed(HttpContextInterface $httpContext)
	{
		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/');
		parent::proceed($httpContext); // proceed http context from parent class
	}


	/**
	 * Returns the embedded module page
	 *
	 * @param string $title
	 * @param string $modulePath
	 *
	 * @return \AdminPageHttpControllerResponse
	 */
	public function actionDefault($title = '', $modulePath = '')
	{
		$html = $this->_render('embedded_module.html',
		                       array(
			                       'module' => $modulePath
		                       ));

		return new AdminPageHttpControllerResponse($title, $html);
	}
}