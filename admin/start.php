<?php
/* --------------------------------------------------------------
   start.php 2015-10-05
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

require ('includes/application_top.php');

include(DIR_WS_MODULES.FILENAME_SECURITY_CHECK);

	require(DIR_FS_ADMIN . 'gm/classes/GMStart.php');

	$gmStart = new GMStart();

	$gm_listing	= $gmStart->getTopListing();

if(!isset($jsEngineLanguage))
{
	$jsEngineLanguage = array();
}
$languageTextManager = MainFactory::create_object('LanguageTextManager', array(), true);
$jsEngineLanguage['start'] = $languageTextManager->get_section_array('start');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo DIR_WS_ADMIN; ?>includes/stylesheet.css">
		<script type="text/javascript" src="gm/javascript/LoadUrl.js"></script>
	</head>
	<body topmargin="0" leftmargin="0" bgcolor="#FFFFFF" onload="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_start&do=sales');	?>', '', '', '<?php echo xtc_session_id(); ?>')">

		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
		<script type="text/javascript">

			$(document).ready(function(){
				$("#gm_start_sales").click(function(){
					$("#gm_start_heading").html('<?php echo TITLE_SALES; ?>');
				});

				$("#gm_start_orders").click(function(){
					$("#gm_start_heading").html('<?php echo TITLE_ORDERS; ?>');
				});

				$("#gm_start_visitors").click(function(){
					$("#gm_start_heading").html('<?php echo TITLE_VISITOR; ?>');
				});

				$("#gm_start_impressions").click(function(){
					$("#gm_start_heading").html('<?php echo TITLE_IMPRESSIONS; ?>');
				});

				var coo_load_url = new LoadUrl();
				coo_load_url.load_url('LoadNews');
			});

		</script>

		<table border="0" cellspacing="2" cellpadding="0">
			<tr>
				<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top" height="100%">
					<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="0" class="columnLeft">
						<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
					</table>
				</td>
				<td class="boxCenter no-wrap" width="100%" valign="top" height="100%">
					<table border="0" width="100%" cellspacing="0" cellpadding="0">
						<!--
						<tr>
							<td colspan="2">
								<div class="pageHeading" style="background-image:url(images/gm_icons/gambio.png)"><?php echo HEADING_TITLE; ?></div>
								<br />
							</td>
						</tr>
						-->

						<!--
							COMPATIBILITY DASHBOARD
						-->
						<tr style="background-color: #F4F4F4">
							<td>
								<div class="dashboard-title"><?php echo HEADING_DASHBOARD; ?></div>
								<div class="dashboard-content breakpoint-large box-center-wrapper no-wrap" style="margin-bottom: 0; margin-top: 0;"></div>
							</td>
						</tr>
						<tr style="background-color: #F4F4F4">
							<td>
								<div class="breakpoint-large box-center-wrapper no-wrap charts" style="margin-top: 0">
								    <?php require DIR_FS_ADMIN . 'html/compatibility/dashboard.php' ?>
								</div>
							</td>
						</tr>

						<?php if ($_SESSION['customers_status']['customers_status_id'] == '0'  && ($admin_access['gm_counter'] == '1')) { ?>
						<tr class="hidden">
							<td width="300" valign="top" rowspan="2">
								<!-- STATITICS -->
								<table border="0" width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td id="gm_start_heading" width="100%" class="dataTableHeadingContent" style="border-right: 0px;">
											<?php echo TITLE_SALES; ?>
										</td>
									</tr>
								</table>
								<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border_start" height="100%" style="background-color:#d6e6f3">
									<tr>
										<td valign="top" class="main">
											<div id="gm_box_content" style="width:280px; height:268px;">
											</div>
										</td>
									</tr>
								</table>
							</td>
							<td width="70%" height="" valign="top">
								<!-- STATITICS -->
								<table border="0" width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td width="100%" class="dataTableHeadingContent" style="border-right: 0px;">
											<?php echo TITLE_OVERVIEW; ?>
										</td>
									</tr>
								</table>
								<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow" height="100%" style="background-color:#d6e6f3">
									<tr>
										<td valign="top" class="main">
											<table border="0" width="100%" cellspacing="0" cellpadding="0">
												<tr>
													<td width="25%" valign="top" class="main" align="right" style="font-size:11px;">
														&nbsp;
													</td>
													<td width="25%" valign="top" class="main" align="right" style="font-size:11px;">
														<strong>
															<?php echo TITLE_TODAY; ?>
														</strong>
													</td>
													<td width="25%" valign="top" class="main" align="right" style="font-size:11px;">
														<strong>
															<?php echo TITLE_YESTERDAY; ?>
														</strong>
													</td>
													<td width="25%" valign="top" class="main" align="right" style="font-size:11px;">
														<strong>
															<?php echo TITLE_RATE; ?>
														</strong>
													</td>
												</tr>
												<tr>
													<td width="25%" valign="top" class="main" align="left" style="font-size:11px;">
														<span id="gm_start_sales" class="main gm_strong gm_pointer" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_start&do=sales');	?>', '', '')"><?php echo TITLE_SALES; ?></span>
													</td>
													<td valign="top" class="main" align="right" style="font-size:11px;">
														<?php

															echo $gmStart->rates['SALES']['TODAY'];
														?>
													</td>
													<td valign="top" class="main" align="right" style="font-size:11px;">
														<?php
															echo $gmStart->rates['SALES']['YESTERDAY'];
														?>
													</td>
													<td valign="top" class="main" align="right" style="font-size:11px;">
														<?php
															echo $gmStart->rates['SALES']['DIFFERENCE'];
														?>
													</td>
												</tr>
												<tr>
													<td valign="top" class="main" align="left" style="font-size:11px;">
														<span id="gm_start_orders" class="main gm_strong gm_pointer" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_start&do=orders');	?>', '', '')"><?php echo TITLE_ORDERS; ?></span>
													</td>
													<td valign="top" class="main" align="right" style="font-size:11px;">
														<?php
															echo $gmStart->rates['ORDERS']['TODAY'];
														?>
													</td>
													<td valign="top" class="main" align="right" style="font-size:11px;">
														<?php
															echo $gmStart->rates['ORDERS']['YESTERDAY'];
														?>
													</td>
													<td valign="top" class="main" align="right" style="font-size:11px;">
														<?php
															echo $gmStart->rates['ORDERS']['DIFFERENCE'];
														?>
													</td>
												</tr>
												<tr>
													<td valign="top" class="main" align="left" style="font-size:11px;">
														<span id="gm_start_visitors" class="main gm_strong gm_pointer" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_start&do=visits');	?>', '', '')"><?php echo TITLE_VISITOR; ?></span>
													</td>
													<td valign="top" class="main" align="right" style="font-size:11px;">
														<?php
															echo $gmStart->rates['VISITORS']['TODAY'];
														?>
													</td>
													<td valign="top" class="main" align="right" style="font-size:11px;">
														<?php
															echo $gmStart->rates['VISITORS']['YESTERDAY'];
														?>
													</td>
													<td valign="top" class="main" align="right" style="font-size:11px;">
														<?php
															echo $gmStart->rates['VISITORS']['DIFFERENCE'];
														?>
													</td>
												</tr>
												<tr>
													<td valign="top" class="main" align="left" style="font-size:11px;">
														<span id="gm_start_impressions" class="main gm_strong gm_pointer" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_start&do=hits');	?>', '', '')"><?php echo TITLE_IMPRESSIONS; ?></span>
													</td>
													<td valign="top" class="main" align="right" style="font-size:11px;">
														<?php
															echo $gmStart->rates['HITS']['TODAY'];
														?>
													</td>
													<td valign="top" class="main" align="right" style="font-size:11px;">
														<?php
															echo $gmStart->rates['HITS']['YESTERDAY'];
														?>
													</td>
													<td valign="top" class="main" align="right" style="font-size:11px;">
														<?php
															echo $gmStart->rates['HITS']['DIFFERENCE'];
														?>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr class="hidden">
							<td width="70%" height="" valign="top">
								<table border="0" width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td width="100%" class="dataTableHeadingContent" style="border-right: 0px; margin-top:0px;" >
											<?php echo TITLE_TOP_LIST; ?>
										</td>
									</tr>
								</table>
								<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow" height="100%" style="background-color:#d6e6f3">
									<tr>
										<td valign="top" class="main">
											<table border="0" width="100%" cellspacing="0" cellpadding="0">
												<tr>
													<td width="33%" valign="top" class="main nobr" align="left" style="font-size:11px;">
														<strong>
															<?php echo SEARCHTERM_INTERN; ?>
														</strong>
													</td>
													<td width="33%" valign="top" class="main nobr" align="left" style="font-size:11px;">
														<strong>
															<?php echo SEARCHTERM_EXTERN; ?>
														</strong>
													</td>
													<td width="33%" valign="top" class="main nobr" align="left" style="font-size:11px;">
														<strong>
															<?php echo ARTICLE_SOLD; ?>
														</strong>
													</td>
												</tr>

											<?php for($i=0; $i < 5; $i++) { ?>
												<tr>
													<td valign="top" class="main" align="left" style="font-size:10px;">
														<?php
															if(!empty($gm_listing['search_intern'][$i])) echo $i+1 . ". " . $gm_listing['search_intern'][$i];
														?>
													</td>
													<td valign="top" class="main" align="left" style="font-size:10px;">
														<?php
															if(!empty($gm_listing['search_extern'][$i])) echo $i+1 . ". " . $gm_listing['search_extern'][$i];
														?>
													</td>
													<td valign="top" class="main" align="left" style="font-size:10px;">
														<?php
															if(!empty($gm_listing['article_sold'][$i]))  echo $i+1 . ". " . $gm_listing['article_sold'][$i];
														?>
													</td>
												</tr>
												<?php } ?>
											</table>
										</td>
									</tr>

								</table>
							</td>
						</tr>
						<?php } ?>
						<tr style="background-color: #FFF">
							<td colspan="2" style="border-top: 1px solid #E4E4E4;">
								<div class="breakpoint-large box-center-wrapper no-wrap news-wrapper">
									<!-- NEWS -->

									<div class="dashboard-title">
										<?php echo TITLE_NEWS; ?>
									</div>

									<div id="content_loader">
										<div id="url_loader">
											<img id="loading" src="../images/loading.gif" />
											<?php echo TEXT_NEWS_LOADING; ?>
										</div>
										<div class="load_url">
											<?php 
												include DIR_FS_CATALOG . 'release_info.php';
												echo base64_encode('http://news.gambio-support.de/news.php?category=gx2-5&get_news_for_version=' . rawurlencode($gx_version)); 
											?>
										</div>
									</div>
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
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
