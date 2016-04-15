<?php
/* --------------------------------------------------------------
   itrecht_config.php 2015-09-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/

require_once 'includes/application_top.php';

AdminMenuControl::connect_with_page('admin.php?do=ModuleCenter');

define('PAGE_URL', HTTP_SERVER.DIR_WS_ADMIN.basename(__FILE__));

ob_start();

$messages_ns = 'messages_'.basename(__FILE__);
if(!isset($_SESSION[$messages_ns])) {
	$_SESSION[$messages_ns] = array();
}

function replaceLanguagePlaceholders($content)
{
	$coo_txt = MainFactory::create_object('LanguageTextManager', array('itrecht', $_SESSION['languages_id']));
	while(preg_match('/##(\w+)\b/', $content, $matches) == 1) {
		$replacement = $coo_txt->get_text($matches[1]);
		if(empty($replacement)) {
			$replacement = $matches[1];
		}
		$content = preg_replace('/##'.$matches[1].'/', $replacement.'$1', $content, 1);
	}
	return $content;
}

function filelink($file) {
	$fullpath = DIR_FS_CATALOG.$file;
	if(file_exists($fullpath)) {
		$fdate = filemtime($fullpath);
		$text = date('c', $fdate);
		$url = HTTP_SERVER.DIR_WS_CATALOG.$file;
		$out = '<a href="'.$url.'" target="_new">'.$text.'</a>';
	}
	else {
		$out = "<em>$file ##not_received_yet</em>";
	}
	return $out;
}

function getCmGroupIdForType($type) {
	$mapping = array(
		'agb' => 3,
		'impressum' => 4,
		'datenschutz' => 2,
		'widerruf' => 3889896
	);
	if(array_key_exists($type, $mapping)) {
		return $mapping[$type];
	}
	return false;
}


function cmConfigured($languages_id, $type, $filename) {
	$group_id = getCmGroupIdForType($type);
	if($group_id === false) {
		return false;
	}
	$query = "SELECT content_id FROM content_manager WHERE content_group = ".$group_id." AND languages_id = ".$languages_id." AND content_file = '".$filename."'";
	$result = xtc_db_query($query);
	if(xtc_db_num_rows($result) > 0) {
		return true;
	}
	return false;
}

$supported_languages = array('de');
$languages_query = "SELECT languages_id, code FROM languages";
$languages_result = xtc_db_query($languages_query);
$languages = array();
while($lang_row = xtc_db_fetch_array($languages_result)) {
	if(in_array($lang_row['code'], $supported_languages)) {
		$languages[$lang_row['code']] = $lang_row['languages_id'];
	}
}

$rechtstext_types = array('agb', 'impressum', 'datenschutz', 'widerruf');

$files = array();

foreach($languages as $code => $l_id) {
	foreach($rechtstext_types as $rttype) {
		if(!isset($files[$rtype])) {
			$file[$rtype] = array();
		}
		$files[$rttype][$code] = array(
			'txt' => 'media/content/'.$rttype.'_'.$code.'.txt',
			'html' => 'media/content/'.$rttype.'_'.$code.'.html',
			'pdf' => 'media/content/'.$rttype.'_'.$code.'.pdf',
		);
	}
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	if(isset($_POST['gen_token'])) {
		$token = md5(uniqid().uniqid());
		gm_set_conf('ITRECHT_TOKEN', $token);
		$_SESSION[$messages_ns][] = '##token_generated';
	}
	else if(isset($_POST['use_in_cm'])) {
		$lang_id = $languages[$_POST['lang']];
		$content_group = getCmGroupIdForType($_POST['type']);
		if($content_group !== false) {
			$query = "UPDATE content_manager SET content_file = ':file' WHERE languages_id = :languages_id AND content_group = :content_group";
			$query = strtr($query, array(':file' => xtc_db_input($_POST['file']), ':languages_id' => (int)$lang_id, ':content_group' => $content_group));
			xtc_db_query($query);
			$_SESSION[$messages_ns][] = '##legal_text_copied_to_content_manager';
		}
		else {
			$_SESSION[$messages_ns][] = '##not_copied_to_cm_type_incompatible';
		}
	}
	else {
		// save configuration
		/*
		gm_set_conf('ITRECHT_API_USER', $_POST['api_username']);
		gm_set_conf('ITRECHT_API_PASSWORD', $_POST['api_password']);
		gm_set_conf('ITRECHT_USER', $_POST['username']);
		gm_set_conf('ITRECHT_PASSWORD', $_POST['password']);
		*/
		gm_set_conf('ITRECHT_TOKEN', xtc_db_input(trim($_POST['token'])));
		gm_set_conf('ITRECHT_USE_AGB_IN_PDF', ($_POST['use_agb_in_pdf'] == true ? '1' : '0'));
		gm_set_conf('ITRECHT_USE_WITHDRAWAL_IN_PDF', ($_POST['use_withdrawal_in_pdf'] == true ? '1' : '0'));

		foreach($languages as $code => $l_id) {
			if($_POST['use_agb_in_pdf'] == true && file_exists(DIR_FS_CATALOG.$files['agb'][$code]['txt'])) {
				$conditions_txt = file_get_contents(DIR_FS_CATALOG.$files['agb'][$code]['txt']);
				gm_set_content('GM_PDF_CONDITIONS', $conditions_txt, 2);
			}
			if($_POST['use_withdrawal_in_pdf'] == true && file_exists(DIR_FS_CATALOG.$files['widerruf'][$code]['txt'])) {
				$withdrawal_txt = file_get_contents(DIR_FS_CATALOG.$files['widerruf'][$code]['txt']);
				gm_set_content('GM_PDF_WITHDRAWAL', $withdrawal_txt, 2);
			}
		}

		$_SESSION[$messages_ns][] = '##configuration_saved';
	}

	xtc_redirect(PAGE_URL);
}

$messages = $_SESSION[$messages_ns];
$_SESSION[$messages_ns] = array();

$data = array(
	/*
	'api_username' => gm_get_conf('ITRECHT_API_USER'),
	'api_password' => gm_get_conf('ITRECHT_API_PASSWORD'),
	'username' => gm_get_conf('ITRECHT_USER'),
	'password' => gm_get_conf('ITRECHT_PASSWORD'),
	*/
	'token' => gm_get_conf('ITRECHT_TOKEN'),
	'use_agb_in_pdf' => gm_get_conf('ITRECHT_USE_AGB_IN_PDF'),
	'use_withdrawal_in_pdf' => gm_get_conf('ITRECHT_USE_WITHDRAWAL_IN_PDF'),
);


?>
<!doctype HTML>
<html <?php echo HTML_PARAMS; ?>>
<head>
	<meta http-equiv="x-ua-compatible" content="IE=edge">
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
	<title><?php echo TITLE; ?></title>
	<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
	<style>
		p.message {margin: .5ex auto; background: rgb(240, 230, 140); border: 1px solid rgb(255, 0, 0); padding: 1em; }
		p.warning {text-align: center; font: bold 1.2em sans-serif; padding: 1em; background-color: #faa; }
		div.green { background-color: #ADD3B5; padding: .3ex .5ex;}
		div.red { background-color: #E2A8A7; padding: .3ex .5ex; }
		dl.adminform {position: relative; overflow: auto; background: #eef; }
		dl.adminform dd, dl.adminform dt {float: left; margin: 1px 0; }
		dl.adminform dt {clear: left; width: 15em; }
		input[type="submit"].btn_wide {width: auto; }
		table.files {border-collapse: collapse; }
		table.files th, table.files td {padding: .3ex .5ex; }
		table.files thead {background: #ddd; } table.files tbody {background: #eee; }

		form.bluegray {font-size: 0.9em; }
		form.bluegray fieldset {border: none; padding: 0; margin: 1ex 0 0 0; }
		form.bluegray legend {font-weight: bolder; font-size: 1.4em; background: #585858; color: #FFFFFF; padding: .2ex 0.5%; width: 99%; }
		form.bluegray dl.adminform {margin: 0; }
		form.bluegray dl.adminform dt, form.bluegray dl.adminform dd {line-height: 1.3; padding: 3px 0; margin: 0; }
		form.bluegray dl.adminform dt {width: 20%; float: left; font-weight: bold; padding: 2px;}
		form.bluegray dl.adminform dd {border-bottom: 1px dotted rgb(90, 90, 90); width: 78%; float: none; padding-left: 22%; background-color: #F7F7F7; min-height: 2.5em; }
		form.bluegray dl.adminform dd:nth-child(4n) {background: #D6E6F3; }
	</style>
</head>
<body>
	<!-- header //-->
	<?php require DIR_WS_INCLUDES . 'header.php'; ?>
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
				<table border="0" width="100%" cellspacing="0" cellpadding="0" class="">
					<tr>
						<td>
							<table border="0" width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td class="pageHeading" style="padding-left: 0px">##config_heading</td>
									<td width="80" rowspan="2">&nbsp;</td>
								</tr>
								<tr>
									<td class="main" valign="top">&nbsp;</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td class="main">
							<?php foreach($messages as $msg): ?>
								<p class="message"><?php echo $msg ?></p>
							<?php endforeach; ?>

							<h2>##configuration</h2>

							<form class="bluegray" action="<?php echo PAGE_URL ?>" method="POST">
								<dl class="adminform">
									<dt><label for="token">##ITRECHTTXT_CONFIG_TOKEN</label></dt>
									<dd>
										<input id="token" name="token" type="text" value="<?php echo $data['token'] ?>" size="40">
										<input type="submit" value="##ITRECHTTXT_CONFIG_GENERATE_TOKEN" name="gen_token">
										<br>
										##your_api_url: <tt><?php echo HTTP_SERVER.DIR_WS_CATALOG; ?></tt>
									</dd>
									<dt><label for="use_agb_in_pdf">##ITRECHTTXT_CONFIG_USE_AGB_IN_PDF</label></dt>
									<dd>
										<input id="use_agb_in_pdf" type="checkbox" value="1" name="use_agb_in_pdf" <?php echo ($data['use_agb_in_pdf'] == true) ? 'checked="checked"' : '' ?>>
										<span class="warning">##ITRECHTTXT_PDF_CONDITIONS_WARNING</span>
									</dd>
									<dt><label for="use_withdrawal_in_pdf">##ITRECHTTXT_CONFIG_USE_WITHDRAWAL_IN_PDF</label></dt>
									<dd>
										<input id="use_withdrawal_in_pdf" type="checkbox" value="1" name="use_withdrawal_in_pdf" <?php echo ($data['use_withdrawal_in_pdf'] == true) ? 'checked="checked"' : '' ?>>
										<span class="warning">##ITRECHTTXT_PDF_CONDITIONS_WARNING</span>
									</dd>
								</dl>
								<input class="button btn_wide" type="submit" value="##ITRECHTTXT_CONFIG_SAVE">
							</form>
							<h2>##texts_received</h2>
							<table class="files">
								<thead>
									<tr><th>##legal_text</th><th>##type_text</th><th>##type_html</th><th>##type_pdf</th><th>&nbsp;</th></tr>
								</thead>
								<tbody>
									<?php foreach($files as $rtype => $lang): ?>
										<?php foreach($lang as $code => $langfiles): ?>
											<tr>
												<td><?php echo $rtype.' ('.$code.')' ?></td>
												<?php foreach($langfiles as $type => $file): ?>
													<td><?php echo filelink($file); ?></td>
												<?php endforeach ?>
												<td>
													<?php
													$cmfile = $rtype.'_'.$code.'.html';
													if(file_exists(DIR_FS_CATALOG.'media/content/'.$cmfile)):
														?>
														<?php if(!cmConfigured($languages[$code], $rtype, $cmfile)): ?>
															<form action="" method="post">
																<input type="hidden" name="lang" value="<?php echo $code ?>">
																<input type="hidden" name="type" value="<?php echo $rtype ?>">
																<input type="hidden" name="file" value="<?php echo $cmfile ?>">
																<input type="submit" name="use_in_cm" value="##use_in_content_manager">
															</form>
														<?php else: ?>
															<div class="green">##used_in_content_manager</div>
														<?php endif ?>
													<?php else: ?>
														<div class="red">##html_file_not_available</div>
													<?php endif ?>
												</td>
											</tr>
										<?php endforeach ?>
									<?php endforeach ?>
								</tbody>
							</table>

						</td>
					</tr>
				</table>
			</td>

			<!-- body_text_eof //-->

		</tr>
	</table>
	<!-- body_eof //-->

	<!-- footer //-->
	<?php require DIR_WS_INCLUDES . 'footer.php'; ?>
	<!-- footer_eof //-->
</body>
</html>
<?php
echo replaceLanguagePlaceholders(ob_get_clean());
require DIR_WS_INCLUDES . 'application_bottom.php';
