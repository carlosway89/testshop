<?php
/* --------------------------------------------------------------
   econda.php 2015-09-28 gm
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
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(configuration.php,v 1.40 2002/12/29); www.oscommerce.com
   (c) 2003	 nextcommerce (configuration.php,v 1.16 2003/08/19); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: econda.php 1235 2009-04-20 09:49:00Z mz $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require ('includes/application_top.php');

AdminMenuControl::connect_with_page('admin.php?do=ModuleCenter');
/*
** I18N
*/
$coo_txt = MainFactory::create_object('LanguageTextManager', array('econda', $_SESSION['languages_id']));

function get_text($name) {
	$replacement = $GLOBALS['coo_txt']->get_text($name);
	return $replacement;
}

function replaceTextPlaceholders($content) {
	while(preg_match('/##(\w+)\b/', $content, $matches) == 1) {
		$replacement = get_text($matches[1]);
		if(empty($replacement)) {
			$replacement = $matches[1];
		}
		$content = preg_replace('/##'.$matches[1].'/', $replacement.'$1', $content, 1);
	}
	return $content;
}


$isactivated = @constant('TRACKING_ECONDA_ACTIVE') == 'true';
$activationkey = @constant('TRACKING_ECONDA_ID');

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save')
{
	$updateOK = 0;

	if($_POST['econda_active'] == 'true')
	{
		$setActive = 'true';
	}
	else
	{
		$setActive = 'false';
	}
	$setKey = trim($_POST['econda_key']);

	if(xtc_db_query("UPDATE ".TABLE_CONFIGURATION." SET configuration_value='".$setActive."' where configuration_key='TRACKING_ECONDA_ACTIVE'") !== false)
	{
		$updateOK += 1;
	}
	if(xtc_db_query("UPDATE ".TABLE_CONFIGURATION." SET configuration_value='".$setKey."' where configuration_key='TRACKING_ECONDA_ID'") !== false)
	{
		$updateOK += 1;
	}  
	xtc_redirect('econda.php?u='.$updateOK);
}

ob_start();
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
<head>
	<meta http-equiv="x-ua-compatible" content="IE=edge">
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>"> 
	<title><?php echo TITLE; ?></title>
	<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
	<link rel="stylesheet" type="text/css" href="../includes/econda/style.css">
</head>
<body bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
	<tr>
		<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
			<!-- left_navigation //-->
			<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
			<!-- left_navigation_eof //-->
		</td>
		<!-- body_text //-->
		<td class="boxCenter" width="100%" valign="top">
			<div class="pageHeading" style="background-image:url(images/gm_icons/module.png)">econda Shop Monitor</div>
			<br />
			<span class="main">
				<table style="margin-bottom:5px" border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr class="dataTableHeadingRow">
						<td class="dataTableHeadingContentText">##activation</td>
					</tr>
				</table>
			
				<table style="border: 1px solid #dddddd" border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr class="dataTableRow">
						<td style="font-size: 12px; padding: 0px 10px 11px 10px; text-align: justify">
							<br />
							<form name="econda_form" action="<?php echo xtc_href_link('econda.php'); ?>" method="post">
								<input type="hidden" name="action" value="save">
								##TRACKING_ECONDA_ACTIVE_TITLE&nbsp;<input type="checkbox" name="econda_active" value="true"<?php if($isactivated  == 'true') echo ' checked="checked"'; ?> />&nbsp;&nbsp;##TRACKING_ECONDA_ACTIVE_DESC
								<br />
								<br />
								##TRACKING_ECONDA_ACTIVATE&nbsp;&nbsp;<input type="text" name="econda_key" value="<?php echo $activationkey; ?>" size="15" />&nbsp;##TRACKING_ECONDA_ID_DESC
								<?php
									echo '<input style="margin-left:1px" type="submit" name="go" class="button" onClick="this.blur();" value="' . BUTTON_SAVE . '"/> ';
									if(isset($_GET['u']) && $_GET['u'] == 2)
									{
										echo "<br /><font color=\"green\">##TRACKING_ECONDA_UPDATE_SUCCESS</font>";
									}
									elseif(isset($_GET['u']) && $_GET['u'] != 2)
									{
										echo "<br /><font color=\"red\">##TRACKING_ECONDA_UPDATE_FAILED</font>";
									}
								?>
							</form>

						</td>
					</tr>
				</table>
	
				<div id="content_loader">
					<div id="url_loader">
						<img id="loading" src="../images/loading.gif" />
						<?php echo TEXT_CONTENT_LOADING; ?>
					</div>
					<div class="load_url"><?php echo base64_encode('http://news.gambio.de/econda/conditions.html'); ?></div>
				</div>
			</span>

		</td>
		<!-- body_text_eof //-->
	</tr>
</table>

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<script type="text/javascript" src="gm/javascript/LoadUrl.js"></script>
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
	var coo_load_url = new LoadUrl();
	coo_load_url.load_url('load_content');
});
</script>

</body>
</html>
<?php
echo replaceTextPlaceholders(ob_get_clean());
require(DIR_WS_INCLUDES . 'application_bottom.php');