<?php
/* --------------------------------------------------------------
   iloxx.php 2015-09-28 gm
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

require('includes/application_top.php');
AdminMenuControl::connect_with_page('admin.php?do=ModuleCenter');
defined('GM_HTTP_SERVER') or define('GM_HTTP_SERVER', HTTP_SERVER);
define('PAGE_URL', GM_HTTP_SERVER.DIR_WS_ADMIN.basename(__FILE__));

$messages_ns = 'messages_'.basename(__FILE__);
if(!isset($_SESSION[$messages_ns])) {
	$_SESSION[$messages_ns] = array();
}

$iloxx = new GMIloxx();

if($_SERVER['REQUEST_METHOD'] == "POST") {
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	switch($_POST['cmd']) {
		case 'save_cfg':
			$iloxx->userid = xtc_db_input($_POST['userid']);
			$iloxx->usertoken = xtc_db_input($_POST['usertoken']);
			$iloxx->oslabelacquired = xtc_db_input($_POST['os_label_acquired']);
			$iloxx->ostracking = xtc_db_input($_POST['os_tracking']);
			$iloxx->use_weight_options = xtc_db_input($_POST['use_weight_options']);
			$iloxx->default_ship_service = xtc_db_input($_POST['default_ship_service']);
			$iloxx->default_ship_service_cod = xtc_db_input($_POST['default_ship_service_cod']);
			$iloxx->parcelservice_id = xtc_db_input($_POST['parcelservice_id']);
			$_SESSION[$messages_ns][] = $iloxx->get_text('configuration_saved');
			break;
		case 'get_transaction_list':
			$pdfdata = $iloxx->getDailyTransactionList($_POST['gdtl_date'], $_POST['gdtl_type']);
			if($pdfdata !== false) {
				header('Content-Type: application/pdf');
				header('Content-Disposition: attachment;filename=tagesabschluss_'.$_POST['gdtl_date'].'.pdf');
				echo $pdfdata;
			}
			break;
		default:
			die('What do you think you are doing, Dave?');
	}
	xtc_redirect(PAGE_URL);
}


$parcelServiceReader = MainFactory::create('ParcelServiceReader');
$parcelServices = $parcelServiceReader->getAllParcelServices();

$t_page_token = $_SESSION['coo_page_token']->generate_token();

$messages = $_SESSION[$messages_ns];
$_SESSION[$messages_ns] = array();

$orders_status = xtc_get_orders_status();

ob_start();
?>
<!DOCTYPE html>
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
		<link rel="stylesheet" type="text/css" href="includes/stylesheet_iloxx.css">
	</head>
	<body>
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
					<table border="0" width="100%" cellspacing="0" cellpadding="0" class="credits">
						<tr>
							<td>
								<table border="0" width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td class="pageHeading" style="padding-left: 0px">MyDPD Business / iloxx</td>
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

								<form class="bluegray" action="<?php echo PAGE_URL ?>" method="post">
									<input type="hidden" name="page_token" value="<?php echo $t_page_token ?>">
									<input type="hidden" name="cmd" value="save_cfg">
									<fieldset class="">
										<legend>##configuration</legend>
										<dl class="adminform">
											<dt><label for="userid">##user_id</label></dt>
											<dd>
												<input id="userid" name="userid" type="text" value="<?php echo $iloxx->userid ?>">
											</dd>
											<dt><label for="usertoken">##user_token</label></dt>
											<dd>
												<input id="usertoken" name="usertoken" type="text" value="<?php echo $iloxx->usertoken ?>">
											</dd>
											<dt><label for="os_label_acquired">##os_label_acquired</label></dt>
											<dd>
												<select name="os_label_acquired" size="1">
													<?php foreach($orders_status as $os): ?>
													<option value="<?php echo $os['id'] ?>"<?php echo $os['id'] == $iloxx->oslabelacquired ? ' selected="selected"' : '' ?>>
														<?php echo $os['text'] ?>
													</option>
													<?php endforeach ?>
												</select>
											</dd>
											<dt><label for="os_tracking">##os_tracking</label></dt>
											<dd>
												<select name="os_tracking" size="1">
													<?php foreach($orders_status as $os): ?>
													<option value="<?php echo $os['id'] ?>"<?php echo $os['id'] == $iloxx->ostracking ? ' selected="selected"' : '' ?>>
														<?php echo $os['text'] ?>
													</option>
													<?php endforeach ?>
												</select>
											</dd>
											<dt>
												<label for="use_weight_options">##use_weight_options</label>
											</dt>
											<dd>
												<input name="use_weight_options" id="use_weight_options"
													value="1" type="checkbox" <?php echo ($iloxx->use_weight_options == true) ? 'checked="checked"' : '' ?>>
											</dd>
											<dt>
												<label for="default_ship_service">##default_ship_service</label>
											</dt>
											<dd>
												<select name="default_ship_service">
													<?php foreach($iloxx->getShipServices() as $key => $name): ?>
														<option value="<?php echo $key ?>" <?php echo $iloxx->default_ship_service == $key ? 'selected="selected"' : '' ?>><?php echo $name ?></option>
													<?php endforeach ?>
												</select>
											</dd>
											<dt>
												<label for="default_ship_service_cod">##default_ship_service_cod</label>
											</dt>
											<dd>
												<select name="default_ship_service_cod">
													<?php foreach($iloxx->getShipServices() as $key => $name): ?>
														<option value="<?php echo $key ?>" <?php echo $iloxx->default_ship_service_cod == $key ? 'selected="selected"' : '' ?>><?php echo $name ?></option>
													<?php endforeach ?>
												</select>
											</dd>
											<dt><label for="parcelservice_id">##parcelservice_id</label></dt>
											<dd>
												<select id="parcelservice_id" name="parcelservice_id">
													<option value="0" <?php if($iloxx->parcelservice_id == 0) echo 'selected="selected"' ?>>##parcelservice_none</option>
													<?php foreach($parcelServices as $parcelService): ?>
														<option value="<?php echo $parcelService->GetId() ?>" <?php if($iloxx->parcelservice_id == $parcelService->getId()) echo 'selected="selected"' ?>>
															<?php echo $parcelService->getName() ?>
														</option>
													<?php endforeach ?>
												</select>
											</dd>

										</dl>
										<div class="buttons">
											<input type="submit" value="##save" class="button">
										</div>
									</fieldset>
								</form>

								<form class="bluegray" action="<?php echo PAGE_URL ?>" method="post">
									<input type="hidden" name="page_token" value="<?php echo $t_page_token ?>">
									<input type="hidden" name="cmd" value="get_transaction_list">
									<fieldset class="">
										<legend>##day_end_list</legend>
										<dl class="adminform">
											<dt><label for="gdtl_date">##date</label></dt>
											<dd>
												<input type="text" name="gdtl_date" value="<?php echo date('Y-m-d') ?>">
											</dd>
											<dt><label for="gdtl_type">##gdtl_type</label></dt>
											<dd>
												<select name="gdtl_type">
													<option value="DPD">DPD</option>
													<option value="Grosspaket">Gro&szlig;paket</option>
												</select>
											</dd>
										</dl>
										<div class="buttons">
											<input type="submit" value="##retrieve" class="button">
										</div>
									</fieldset>
								</form>

								<p class="partnerinfo">
									##partnerinfo <a class="plink" target="_new" href="http://www.iloxx.de/net/Kooperationen/gambio.aspx">##here</a>.
								</p>

							</td><!--  main  -->
						</tr>
					</table>
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
<?php
echo $iloxx->replaceTextPlaceholders(ob_get_clean());
require(DIR_WS_INCLUDES . 'application_bottom.php');
