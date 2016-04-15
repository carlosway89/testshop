<?php
/* --------------------------------------------------------------
   properties_combis.php 2015-09-23 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

   based on: 
   (c) 2000-2001 The Exchange Project 
   (c) 2002-2003 osCommerce coding standards (a typical file) www.oscommerce.com
   (c) 2003      nextcommerce (start.php,1.5 2004/03/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: start.php 1235 2005-09-21 19:11:43Z mz $)

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

	require('includes/application_top.php');
	?>

	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


	<html <?php echo HTML_PARAMS; ?>>
		<head>
			<meta http-equiv="x-ua-compatible" content="IE=edge">
			<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>" /> 
			<title><?php echo TITLE; ?></title>
			<link rel="stylesheet" type="text/css" href="includes/stylesheet.css" />
			<link rel="stylesheet" type="text/css" href="gm/css/buttons.css" />
			<link rel="stylesheet" type="text/css" href="gm/css/lightbox.css" />

			<?php
			$coo_js_options_control = MainFactory::create_object('JSOptionsControl', array(false));
			$t_js_options_array =  $coo_js_options_control->get_options_array($_GET);
			?>
			<script type="text/javascript"> var js_options = <?php echo json_encode($t_js_options_array) ?>; </script>
		</head>
		<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" style="position: relative;">
			<!-- header //-->
			<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
			<!-- header_eof //-->

			<!-- body //-->
			<table border="0" width="100%" cellspacing="2" cellpadding="2">
				<tr>
					<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
						<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
							<!-- left_navigation //-->
							<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
							<!-- left_navigation_eof //-->
						</table>
					</td>
					<!-- body_text //-->
					<td class="boxCenter" width="100%" valign="top">

<?php
if(isset($_GET['products_id']) && (int)$_GET['products_id'] > 0)
{
	$c_products_id = (int)$_GET['products_id'];
	$c_cPath = 0;
	if(isset($_GET['cPath']) && $_GET['cPath'] != '')
	{
		$c_cPath = $_GET['cPath'];
	}
	 if(isset($_GET['page']) && (int)$_GET['page'] > 0){
		$t_page = (int)$_GET['page'];
	 }else{
		$t_page = 1;
	 }
	$coo_combis_admin_view = MainFactory::create_object('PropertiesCombisAdminContentView');
	$t_html = $coo_combis_admin_view->get_html(array('template' => 'combis_main', 'page' => $t_page, 'language_id' => $_SESSION['languages_id'], 'products_id' => $c_products_id, 'cPath' => $c_cPath));
}
else
{
	$t_html = 'invalid products_id';
}
echo $t_html;
?>


					</td>
					<!-- body_text_eof //-->
				</tr>
			</table>
			<!-- body_eof //-->

			<!-- footer //-->
			<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
			<!-- footer_eof //-->
		</body>
	</html>
	<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>