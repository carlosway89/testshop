<?php
/**
 * Template für die Administration des Modules
 * 
 * @package AffiliPRINT
 * @author Patrick Taddey <p.taddey@affiliprint.de>
 * @version 1.1
 * @copyright Copyright (c) 2014 AffiliPRINT GmbH (http://www.affiliprint.de/)
 * @license Released under the GNU General Public License (Version 2) [http://www.gnu.org/licenses/gpl-2.0.html]
 */
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS;?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset'];?>">
		<title><?php echo TITLE;?></title>
		<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
		<link rel="stylesheet" type="text/css" href="<?php echo AFFILIPRINT_DIR_CSS;?>">
	</head>
	<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
		<?php
		require (DIR_WS_INCLUDES . 'header.php');
		?>
		<?php if(IFRAME_ACTION !== true) { ?>
		<table border="0" width="100%" cellspacing="2" cellpadding="2">
			<tr>
				<td class="columnLeft2" width="<?php echo BOX_WIDTH;?>" valign="top">
					<table border="0" width="<?php echo BOX_WIDTH;?>" cellspacing="1" cellpadding="1" class="columnLeft">
						<?php
						require (DIR_WS_INCLUDES . 'column_left.php');
						?>
					</table>
				</td>
				<td class="boxCenter" width="100%" valign="top">
					<table border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td>
								<div class="pageHeading" style="background-image:url(images/gm_icons/gambio.png)">
									<?php echo AFFILIPRINT_TITLE;?>
								</div>
								<br />
								<table border="0" width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td elementAction="defaultAction" style="border-right: 0px" class="clickHandler dataTableHeadingContent">
											<?php echo AFFILIPRINT_TITLE_CONFIG;?>
										</td>
									</tr>
								</table>
								<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
									<tr>
										<td valign="top" class="main apArea">
											<div id="apStatus" class="hide">
												<button class="close clickHandler" type="button">
													x
												</button>
												<div id="apStatusMessage">
													&nbsp;
												</div>
											</div>
											<div>
												<!-- START Auswahl / Info -->
												<div id="apInstall" class="apBox <?php if(AUTHENTICATION_STATUS == 1)	echo 'hide'; ?>">
													<div id="apChoice">
														<?php echo INFO_TEXT_START_PAGE; ?>
														<br /> 
														<br />
														<br />
														<span elementaction="authenticationForm" class="btnStatus clickHandler btn-success">
															<?php echo BUTTON_AUTHENTICATED;?>
														</span>&nbsp;
														<a href="<?php echo IFRAME_URL; ?>" class="btn-warning" target="_blank">
															<?php echo BUTTON_AUTHENTICATION;?>
														</a>
 													</div>
												</div>
												<!-- ENDE Auswahl / Info -->
												<!-- START Authentifizierung -->
												<div id="apAuth" class="apBox hide">
													<?php echo INFO_TEXT_AUTH; ?>
													<div class="control-group">
														<input type="text" class="formField required apInput" id="publisherUid" value="" placeholder="<?php echo PLACEHOLDER_AUTH_CODE; ?>" />
													</div>
													<div class="control-group">
														<span elementAction="default" class="clickHandler btn-warning">
															<?php echo BUTTON_BACK; ?>
														</span>
														&nbsp;
														<span elementAction="authentication" formElementId="#authenticationBox" class="clickHandler btn-success">
															<?php echo BUTTON_SAVE; ?>
														</span>
														<a href="<?php echo IFRAME_URL; ?>" class="formInfoLink" target="_blank">
															<?php echo INFO_TITLE_TOKEN_REGISTRATION_LINK; ?>
														</a>
													</div>
												</div>
												<!-- ENDE Authentifizierung -->
												<!-- START Konfiguration -->
												<div id="apConfig" class="<?php if(AUTHENTICATION_STATUS == 0) echo 'hide';?>">
													<div id="apOverview">
														<?php
															$infoText = INFO_TEXT_CONFIGURATION;
															$vcpUrl = VCP_URL;
															if(empty($vcpUrl) === false) {
																$infoText = str_replace("#VCP_URL#", $vcpUrl, $infoText);
															} 
															echo $infoText; 
														?>
														<br />
														<br />
														<br />
														<div id="apVoucherStatus" class="<?php echo MODULE_STATUS_CSS_CLASS; ?>">
															<?php
																if(MODULE_STATUS == 0) {
																	echo MESSAGE_STATUS_CHANGED_INACTIVE; 
																} elseif(MODULE_STATUS == 1) {
 																	echo MESSAGE_STATUS_CHANGED_ACTIVE;																	
																} elseif(MODULE_STATUS == 2) {
																	 echo MESSAGE_STATUS_NO_CAMPAIGNS; 
																}
															?>
														</div>
														
														<div class="apForm <?php if(MODULE_STATUS == 2) { echo "hide"; } ?>">
															<?php echo INFO_TEXT_FORM;?>
															<div>
																<input value="1" name="status" type="radio" elementValue="1" elementAction="moduleStatus" class="btnModuleStatus clickHandler" <?php if(MODULE_STATUS == 1) { echo 'checked="checked"';} ?> />
																<?php echo TEXT_MODUL_ACTIVE;?>
																<input value="0"  name="status" type="radio" elementValue="0" elementAction="moduleStatus" class="btnModuleStatus clickHandler" <?php if(MODULE_STATUS == 0) { echo 'checked="checked"';} ?> />
																<?php echo TEXT_MODUL_INACTIVE;?>
															</div>
															<br />
															<?php if(API_USE_ADDITIONAL_DATA == 1) { ?>
															<div>
																<input value="1" name="useAdditionalData" type="checkbox" elementValue="1" elementAction="useAdditionalData" class="clickHandler" <?php if(USE_ADDITIONAL_DATA == 1) { echo 'checked="checked"';} ?>/>
																<?php echo TEXT_MODUL_ADDITIONAL_DATA;?>
															</div>
															<?php } ?>
														</div>														
													</div>
												</div>												
												<!-- ENDE Konfiguration -->
											</div>
										</td>
										<td valign="top" align="right">
											<a href="<?php echo AFFILIPRINT_LINK; ?>" target="_blank">
												<img class="affiliPrintLogo" src="<?php echo AFFILIPRINT_LOGO; ?>" border="0" />
											</a>
											<div class="affiliPrintNurse main">
												<span class="affiliPrintNurseHead"><?php echo INFO_TITLE_HELP; ?></span>
												<br />
												<br />
												<img class="affiliPrintNurseImg" src="<?php echo AFFILIPRINT_NURSE; ?>" border="0" />
												<?php echo INFO_TEXT_HELP; ?>												
											</div>
										</td>
									</tr>
								</table>
								<br>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?php 
			}
		?>		
		<?php
		require (DIR_WS_INCLUDES . 'footer.php');
		?>
		<script type="text/javascript" language="javascript"><?php require_once (AFFILIPRINT_DIR . "templates/js/config.js.php"); ?></script>
		<script type="text/javascript" language="javascript" src="<?php echo AFFILIPRINT_DIR_JS;?>classes/EventHandler.js"></script>
		<script type="text/javascript" language="javascript" src="<?php echo AFFILIPRINT_DIR_JS;?>main.js"></script>
	</body>
</html>
<?php
require (DIR_WS_INCLUDES . 'application_bottom.php');
?>
