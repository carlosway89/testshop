<?php
/* --------------------------------------------------------------
   protectedshops.php 2015-09-28 gm
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

defined('GM_HTTP_SERVER') or define('GM_HTTP_SERVER', HTTP_SERVER);
define('PAGE_URL', GM_HTTP_SERVER.DIR_WS_ADMIN.basename(__FILE__));

function getContentPages()
{
	$t_language_id = 2;
	$t_query = 'SELECT `content_title`, `content_group` FROM `content_manager` WHERE `languages_id` = '.(int)$t_language_id;
	$t_content_pages = array();
	$t_result = xtc_db_query($t_query);
	while($t_row = xtc_db_fetch_array($t_result))
	{
		$t_content_pages[$t_row['content_group']] = $t_row;
	}
	return $t_content_pages;
}


$coo_ps = MainFactory::create_object('ProtectedShops', array());
$t_config = $coo_ps->getConfig();

$messages_ns = 'messages_'.basename(__FILE__);
if(!isset($_SESSION[$messages_ns])) {
	$_SESSION[$messages_ns] = array();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	if(empty($_POST['config']) == false)
	{
		$coo_ps->setConfig($_POST['config']);
		$_SESSION[$messages_ns][] = $coo_ps->get_text('configuration_saved');
	}

	if(isset($_POST['cmd']) && $_POST['cmd'] == 'update_document')
	{
		try
		{
			$coo_ps->updateDocument($_POST['document_name'], null, true);
			$_SESSION[$messages_ns][] = $coo_ps->get_text('document_updated');
		}
		catch(Exception $e)
		{
			$_SESSION[$messages_ns][] = $coo_ps->get_text('document_update_failed').': '.$e->getMessage();
		}
	}

	if(isset($_POST['cmd']) && $_POST['cmd'] == 'use_document')
	{
		$coo_ps->useDocument($_POST['document_name']);
		$_SESSION[$messages_ns][] = $coo_ps->get_text('using_document_as_per_configuration');
	}

	if(isset($_POST['cmd']) && $_POST['cmd'] == 'update_and_use_all')
	{
		try
		{
			$coo_ps->updateAndUseAll();
			$_SESSION[$messages_ns][] = $coo_ps->get_text('all_documents_updated_and_used');
		}
		catch(Exception $e)
		{
			$_SESSION[$messages_ns][] = $coo_ps->get_text('an_error_occurred_during_update_of_documents');
		}

	}

	xtc_redirect(PAGE_URL);
}

$messages = $_SESSION[$messages_ns];
$_SESSION[$messages_ns] = array();

if($coo_ps->isConfigured())
{
	$t_docinfo_array = array();
	$t_localdocs_array = array();

	try
	{
		$t_docinfo_array = $coo_ps->getDocumentInfo();
		foreach($t_docinfo_array as $t_docname => $t_docdate)
		{
			$t_localdocs_array[$t_docname] = array();
			foreach($coo_ps->valid_formats as $t_format)
			{
				$t_localdocs_array[$t_docname][$t_format] = $coo_ps->getLatestDocument($t_docname, $t_format);
			}
		}
	}
	catch(Exception $e)
	{
		$messages[] = $coo_ps->get_text('protected_shops_unreachable');
	}

	$t_content_pages = getContentPages();

	if($t_config['use_for_pdf_conditions'] == true)
	{
		$t_cb_use_for_pdf_conditions_yes = 'checked="checked"';
		$t_cb_use_for_pdf_conditions_no = '';
	}
	else
	{
		$t_cb_use_for_pdf_conditions_yes = '';
		$t_cb_use_for_pdf_conditions_no = 'checked="checked"';
	}

	switch($t_config['use_for_pdf_withdrawal'])
	{
		case 'widerruf':
			$t_cb_use_for_pdf_withdrawal_widerruf = 'checked="checked"';
			$t_cb_use_for_pdf_withdrawal_rueckgabe = '';
			$t_cb_use_for_pdf_withdrawal_no = '';
			break;
		case 'rueckgabe':
			$t_cb_use_for_pdf_withdrawal_widerruf = '';
			$t_cb_use_for_pdf_withdrawal_rueckgabe = 'checked="checked"';
			$t_cb_use_for_pdf_withdrawal_no = '';
			break;
		default:
			$t_cb_use_for_pdf_withdrawal_widerruf = '';
			$t_cb_use_for_pdf_withdrawal_rueckgabe = '';
			$t_cb_use_for_pdf_withdrawal_no = 'checked="checked"';
	}
}

ob_start();
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
			dl.adminform {position: relative; overflow: auto; }
			dl.adminform dd, dl.adminform dt {float: left; }
			dl.adminform dt {clear: left; width: 15em; }
			dl.adminform input.long { width: 75%; }
			dl.adminform input[type="radio"] { vertical-align: middle; }
			input[type="submit"].btn_wide {width: auto; } form.bluegray {font-size: 0.9em; } form.bluegray fieldset {border: none; padding: 0; margin: 1ex 0 0 0; }
			form.bluegray legend {font-weight: bolder; font-size: 1.4em; background: #585858; color: #FFFFFF; padding: .2ex 0.5%; width: 99%; }
			form.bluegray dl.adminform {margin: 0; }
			form.bluegray dl.adminform dt, form.bluegray dl.adminform dd {line-height: 20px; padding: 3px 0; margin: 0; }
			form.bluegray dl.adminform dt {width: 18%; float: left; font-weight: bold; padding: 2px;}
			form.bluegray dl.adminform dd {border-bottom: 1px dotted rgb(90, 90, 90); width: 80%; float: none; padding-left: 20%; background-color: #F7F7F7; }
			form.bluegray dl.adminform dd:nth-child(4n) {background: #D6E6F3; }
			table.docinfo { width: 99%; margin: auto; }
			table.docinfo th { background: #ddd; }
			table.docinfo td:nth-child(even) { background: #eee; }
			table.docinfo td { padding: .2ex .5ex; }
			p.cron_info { background: #F7F7F7; border: 1px solid #585858; padding: 1ex 1em; width: 90%; margin: 1em auto; }
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
										<td class="pageHeading" style="padding-left: 0px">##protected_shops</td>
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

								<form class="bluegray" action="<?php echo PAGE_URL ?>" method="POST">
									<fieldset>
										<legend>##credentials</legend>
										<dl class="adminform">
											<dt><label for="shop_id">##shop_id</label></dt>
											<dd><input id="shop_id" name="config[shop_id]" class="long" type="text" value="<?php echo $t_config['shop_id'] ?>"></dd>
										</dl>
									</fieldset>

									<?php if(isset($t_docinfo_array)): ?>
										<fieldset>
											<legend>##use_of_documents</legend>
											<dl class="adminform">
												<?php foreach($t_docinfo_array as $t_docname => $t_docdate): ?>
													<dt><?php echo $t_docname ?></dt>
													<dd>
														<select name="config[content_group_<?php echo strtolower($t_docname) ?>]">
															<option value="-1">##do_not_use</option>
															<?php foreach($t_content_pages as $t_content_page): ?>
																<option value="<?php echo $t_content_page['content_group'] ?>"
																	<?php echo $t_content_page['content_group'] == $t_config['content_group_'.strtolower($t_docname)] ? 'selected="selected"' : ''?>>
																	<?php echo $t_content_page['content_title'] ?>
																</option>
															<?php endforeach ?>
														</select>
													</dd>
												<?php endforeach ?>
											</dl>
										</fieldset>

										<fieldset>
											<legend>##use_in_pdf</legend>
											<dl class="adminform">
												<dt>##use_for_pdf_conditions</dt>
												<dd>
													<input id="ufpc_no" type="radio" <?php echo $t_cb_use_for_pdf_conditions_no ?> name="config[use_for_pdf_conditions]" value="0">
													<label for="ufpc_no">##no</label><br>
													<input id="ufpc_yes" type="radio" <?php echo $t_cb_use_for_pdf_conditions_yes ?> name="config[use_for_pdf_conditions]" value="1">
													<label for="ufpc_yes">##yes</label>
												</dd>
												<dt>##use_for_pdf_withdrawal</dt>
												<dd>
													<input id="ufpc_no" type="radio" <?php echo $t_cb_use_for_pdf_withdrawal_no ?> name="config[use_for_pdf_withdrawal]" value="0">
													<label for="ufpc_no">##no</label><br>
													<input id="ufpc_widerruf" type="radio" <?php echo $t_cb_use_for_pdf_withdrawal_widerruf ?> name="config[use_for_pdf_withdrawal]" value="widerruf">
													<label for="ufpc_widerruf">##widerruf</label><br>
													<input id="ufpc_rueckgabe" type="radio" <?php echo $t_cb_use_for_pdf_withdrawal_rueckgabe ?> name="config[use_for_pdf_withdrawal]" value="rueckgabe">
													<label for="ufpc_rueckgabe">##rueckgabe</label>
												</dd>
											</dl>
										</fieldset>

										<fieldset>
											<legend>##update_configuration</legend>
											<dl class="adminform">
												<dt>##update_interval</dt>
												<dd>
													<input name="config[update_interval]" value="<?php echo $t_config['update_interval'] ?>">
													##update_interval_info
												</dd>
											</dl>
										</fieldset>

									<?php endif ?>

									<input class="button btn_wide" type="submit" value="##save">
								</form>

								<?php if(isset($t_docinfo_array)): ?>
									<h2>##documents_available</h2>

									<table class="docinfo">
										<tr>
											<th>##document</th><th>##last_modified</th>
											<?php foreach($coo_ps->valid_formats as $t_format): ?>
												<th>##format_<?php echo strtolower($t_format); ?></th>
											<?php endforeach ?>
											<th>##update</th>
											<th>##use</th>
										</tr>
										<?php foreach($t_docinfo_array as $t_docname => $t_docdate): ?>
											<tr>
												<td><?php echo $t_docname ?></td>
												<td><?php echo $t_docdate ?></td>
												<?php foreach($coo_ps->valid_formats as $t_format): ?>
													<?php
													if($t_localdocs_array[$t_docname][$t_format] == false)
													{
														$t_localdoc_date = $coo_ps->get_text('not_available');
													}
													else
													{
														$t_localdoc_date = $t_localdocs_array[$t_docname][$t_format]['document_date'];
													}
													?>
													<td><?php echo $t_localdoc_date ?></td>
												<?php endforeach ?>
												<td>
													<form action="<?php echo PAGE_URL ?>" method="POST">
														<input type="hidden" name="cmd" value="update_document">
														<input type="hidden" name="document_name" value="<?php echo $t_docname ?>">
														<input type="submit" value="##update">
													</form>
												</td>
												<td>
													<form action="<?php echo PAGE_URL ?>" method="POST">
														<input type="hidden" name="cmd" value="use_document">
														<input type="hidden" name="document_name" value="<?php echo $t_docname ?>">
														<input type="submit" value="##use">
													</form>
												</td>
											</tr>
										<?php endforeach ?>
									</table>

									<form action="<?php echo PAGE_URL ?>" method="post">
										<input type="hidden" name="cmd" value="update_and_use_all">
										<input type="submit" class="button btn_wide" value="##update_and_use_all">
									</form>

								<?php endif ?>
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
echo $coo_ps->replaceTextPlaceholders(ob_get_clean());
require DIR_WS_INCLUDES . 'application_bottom.php';
