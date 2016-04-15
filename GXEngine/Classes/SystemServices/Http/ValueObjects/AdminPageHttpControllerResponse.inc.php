<?php
/* --------------------------------------------------------------
   AdminPageHttpControllerResponse.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpControllerResponse');

/**
 * Value object
 *
 * Class AdminPageHttpControllerResponse
 *
 * Important: 
 * This class will load the admin section of the shop, something that cannot be 
 * integrated into unit tests. That is why it is not unit tested.
 * 
 * @category   System
 * @package    Http
 * @subpackage ValueObjects
 * @extends    HttpControllerResponse
 */
class AdminPageHttpControllerResponse extends HttpControllerResponse
{

	/**
	 * @param string                   $p_title
	 * @param array|null               $p_mainContent
	 * @param AssetCollectionInterface $assets (optional)
	 * @param array                    $jsLanguageSections
	 */
	public function __construct($p_title,
	                            $p_mainContent,
	                            AssetCollectionInterface $assets = null,
	                            array $jsLanguageSections = array())
	{
		$this->httpBody = $this->_getAdminPageBody(array(
				                                           'title'        => $p_title,
				                                           'main_content' => $p_mainContent
		                                           ), $assets, $jsLanguageSections);
	}


	/**
	 * @param array                    $contentArray
	 * @param AssetCollectionInterface $assets
	 * @param array                    $jsLanguageSections
	 *
	 * @return string
	 */
	protected function _getAdminPageBody(array $contentArray,
	                                     AssetCollectionInterface $assets = null,
	                                     array $jsLanguageSections = array())
	{
		$varTitle       = '';
		$varMainContent = '';

		if(isset($contentArray['title']))
		{
			$varTitle = $contentArray['title'];
		}
		if(isset($contentArray['main_content']))
		{
			$varMainContent = $contentArray['main_content'];
		}

		// Load language translations for JavaScript module engine. 
		$jsEngineLanguage = array(); // this variable is used in the "header.php" in the EngineConfiguration object
		foreach($jsLanguageSections as $section)
		{
			$languageTextManager         = MainFactory::create_object('LanguageTextManager',
			                                                          array($section, $_SESSION['languages_id']));
			$jsEngineLanguage[$section] = $languageTextManager->get_section_array($section);
		}

		ob_start();
		?>
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
		<html <?php echo HTML_PARAMS; ?>>
			<head>
				<meta http-equiv="Content-Type"
				      content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
				<title><?php echo $varTitle ?></title>
				<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
				<link rel="stylesheet" type="text/css" href="gm/css/lightbox.css">
				<link rel="stylesheet" type="text/css" href="gm/css/buttons.css">
				<link rel="stylesheet" type="text/css" href="includes/export_schemes.css">
				<link rel="stylesheet" type="text/css" href="gm/css/tooltip_plugin.css">
				<script type="text/javascript" src="includes/general.js"></script>
			</head>

			<body marginwidth="0"
			      marginheight="0"
			      topmargin="0"
			      bottommargin="0"
			      leftmargin="0"
			      rightmargin="0"
			      bgcolor="#FFFFFF">

				<!-- header //-->
				<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
				<script type="text/javascript" src="gm/javascript/lightbox_plugin.js"></script>
				<script type="text/javascript" src="gm/javascript/tooltip_plugin.js"></script>
				<!-- header_eof //-->

				<!-- assets //-->
				<?php
				if($assets)
				{
					echo $assets->getHtml();
				}
				?>
				<!-- assets_eof //-->

				<!-- body //-->
				<table border="0" width="100%">
					<tr>
						<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
							<table border="0"
							       width="<?php echo BOX_WIDTH; ?>"
							       class="columnLeft">
								<!-- left_navigation //-->
								<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
								<!-- left_navigation_eof //-->
							</table>
						</td>
						<!-- body_text //-->
						<td class="boxCenter" width="100%" valign="top">
							<table border="0" width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td>
										<!-- gm_module //-->
										<div class="pageHeading"
										     style="background-image:url(images/gm_icons/hilfsprogr1.png)">
											<?php echo $varTitle ?>
										</div>

										<div id="container">
											<?php echo $varMainContent ?>
										</div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
			</body>
		</html>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
}