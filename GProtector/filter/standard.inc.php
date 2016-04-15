<?php
/* --------------------------------------------------------------
  G-Protector v1.2
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  --------------------------------------------------------------
*/

$this->add_filter('Alphabetisch-Filter 1', 'admin/gm_emails.php', '_GET["gm_type"]', 'only_alphabetic');
$this->add_filter('Alphabetisch-Filter 2', 'admin/yatego.php', array('_GET["section"]', 
																	'_GET["selectArticles"]', 
																	'_GET["selectCategoryArticles"]'), 'only_alphabetic');
$this->add_filter('Alphabetisch-Filter 3', 'admin/gm_offline.php', '_POST["shop_offline"]', 'only_alphabetic');
$this->add_filter('Alphabetisch-Filter 4', 'admin/template_configuration.php', array('_POST["show_gallery"]', 
																					'_POST["show_wishlist"]'), 'only_alphabetic');
$this->add_filter('Alphabetisch-Filter 5', 'admin/magnalister.php', array('_GET["expert"]', 
																			'_GET["MLDEBUG"]', 
																			'_GET["update"]'), 'only_alphabetic');
$this->add_filter('Alphabetisch-Filter 6', 'admin/banner_statistics.php', '_GET["type"]', 'only_alphabetic');
$this->add_filter('Alphabetisch-Filter 7', array('advanced_search_result.php',
												'index.php'), '_GET["currency"]', 'only_alphabetic');

$this->add_filter('Alphanumerisch-Filter 1', 'admin/products_attributes.php', '_GET["option_order_by"]', 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 2', array('admin/categories.php', 
													'admin/create_account.php', 
													'admin/countries.php', 
													'admin/customers.php', 
													'admin/gm_gprint.php',
													'admin/orders.php', 
													'admin/paypal.php',
													'admin/show_logs.php',
													'admin/specials.php'), '_GET["language"]', 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 3', 'admin/customers.php', '_GET["sorting"]', 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 4', array('admin/accounting.php', 
													'admin/backup.php', 
													'admin/banner_manager.php', 
													'admin/blacklist.php', 
													'admin/campaigns.php', 
													'admin/categories.php', 
													'admin/configuration.php', 
													'admin/content_manager.php', 
													'admin/countries.php', 
													'admin/coupon_admin.php', 
													'admin/create_account.php', 
													'admin/cross_sell_groups.php', 
													'admin/csv_backend.php', 
													'admin/currencies.php', 
													'admin/customers.php', 
													'admin/customers_status.php', 
													'admin/econda.php', 
													'admin/geo_zones.php', 
													'admin/gm_backup_files_zip.php.php', 
													'admin/gm_gprint.php', 
													'admin/gm_module_export.php', 
													'admin/gm_module_part_export.php', 
													'admin/gm_opensearch.php', 
													'admin/gm_product_export.php', 
													'admin/gm_trusted_shops_id.php', 
													'admin/gm_trusted_shops_widget.php', 
													'admin/gm_sitemap.php', 
													'admin/gv_mail.php', 
													'admin/gm_meta.php', 
													'admin/languages.php', 
													'admin/mail.php', 
													'admin/manufacturers.php', 
													'admin/mediafinanz.php', 
													'admin/modules.php', 
													'admin/module_export.php', 
													'admin/module_newsletter.php', 
													'admin/orders.php', 
													'admin/orders_edit.php', 
													'admin/orders_status.php', 
													'admin/paypal.php', 
													'admin/products_attributes.php', 
													'admin/products_vpe.php',
													'admin/properties_combis.php',
													'admin/request_port.php',
													'admin/shipping_status.php', 
													'admin/specials.php', 
													'admin/tax_classes.php', 
													'admin/tax_rates.php',
													'admin/zones.php'), '_GET["action"]', 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 5', 'admin/categories.php', array('_GET["cPath"]', '_GET["sorting"]'), 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 6', 'admin/gm_logo.php', '_GET["gm_logo"]', 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 7', 'admin/paypal.php', '_GET["view"]', 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 8', 'admin/geo_zones.php', '_GET["saction"]', 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 10', 'admin/orders_edit.php', '_GET["edit_action"]', 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 11', array('admin/gm_module_export.php', 
													'admin/gm_module_part_export.php', 
													'admin/modules.php', 
													'admin/module_export.php'), array('_GET["module"]', '_GET["set"]'), 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 12', 'admin/mediafinanz.php', array('_POST["clientLicence"]', 
																			 '_GET["options"]', 
																			 '_POST["options"]'), 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 13', 'admin/magnalister.php', '_GET["do"]', 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 14', array('admin/ekomi.php', 
													'admin/gm_guestbook.php', 
													'admin/gm_miscellaneous.php'), '_GET["content"]', 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 15', 'admin/content_manager.php', '_GET["special"]', 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 16', array('admin/gm_module_part_export.php', 'admin/yatego.php'), '_GET["module"]', 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 17', 'admin/stats_sales_report.php', '_GET["payment"]', 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 18', 'admin/stats_campaigns.php', '_GET["campaign"]', 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 19', 'admin/properties_combis.php', '_GET["cPath"]', 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 20', array('admin/nc_clickandbuy.php', 'admin/yoochoose.php'), '_GET["page"]', 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 21', 'admin/mail.php', '_GET["selected_box"]', 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 22', 'admin/intraship.php', array('_POST["intraship"]["ekp"]',
																			'_POST["intraship"]["zone_1_product"]',
																			'_POST["intraship"]["zone_2_product"]',
																			'_POST["intraship"]["zone_3_product"]',
																			'_POST["intraship"]["zone_4_product"]',
																			'_POST["intraship"]["zone_1_partner_id"]',
																			'_POST["intraship"]["zone_2_partner_id"]',
																			'_POST["intraship"]["zone_3_partner_id"]',
																			'_POST["intraship"]["zone_4_partner_id"]'), 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 23', 'admin/orders.php', '_GET["oID"]', 'only_alphanumeric');
$this->add_filter('Alphanumerisch-Filter 24', 'admin/orders.php', '_GET["ptd_rand"]', 'only_alphanumeric');

$this->add_filter('Rekursiv-Integer-Filter 1', 'admin/gm_feature_control.php', '_POST["featureMode"]', 'recursive_integer_value');
$this->add_filter('Rekursiv-Integer-Filter 2', 'admin/gm_meta.php', '_POST["gm_delete"]', 'recursive_integer_value');
$this->add_filter('Rekursiv-Integer-Filter 3', array('product_info.php',
													'request_port.php'), '_POST["properties_values_ids"]', 'recursive_integer_value');

$this->add_filter('Dateinamen-Filter 1', 'admin/backup.php', '_GET["file"]', 'basename');
$this->add_filter('Dateinamen-Filter 2', 'admin/csv_backend.php', '_POST["select_file"]', 'basename');
$this->add_filter('Dateinamen-Filter 3', 'admin/show_logs.php', array('_GET["file"]', '_GET["hidden_file"]'), 'basename');
$this->add_filter('Dateinamen-Filter 4', 'admin/content_manager.php', '_GET["select_file"]', 'basename');
$this->add_filter('Dateinamen-Filter 5', 'admin/languages.php', '_POST["image"]', 'basename');

$this->add_filter('htmlentities-Filter 1', 'admin/gv_mail.php', array('_POST["email_to"]', '_GET["mail_sent_to"]'), 'htmlentities');
$this->add_filter('htmlentities-Filter 2', 'admin/mail.php', array('_GET["mail_sent_to"]', '_GET["customer"]'), 'htmlentities');
$this->add_filter('htmlentities-Filter 3', 'admin/gm_bookmarks_action.php', '_GET["gm_result"]', 'htmlentities');
$this->add_filter('htmlentities-Filter 4', 'admin/gm_opensearch.php', array('_POST["GM_OPENSEARCH_CONTACT"]',
																			'_POST["GM_OPENSEARCH_DESCRIPTION"]',
																			'_POST["GM_OPENSEARCH_LINK"]',
																			'_POST["GM_OPENSEARCH_LONGNAME"]',
																			'_POST["GM_OPENSEARCH_SHORTNAME"]',
																			'_POST["GM_OPENSEARCH_TAGS"]',
																			'_POST["GM_OPENSEARCH_TEXT"]'), 'htmlentities');
$this->add_filter('htmlentities-Filter 6', 'admin/gm_security.php', array('_POST["GM_RECAPTCHA_PUBLIC_KEY"]',
																		'_POST["GM_RECAPTCHA_PRIVATE_KEY"]'), 'htmlentities');

$this->add_filter('Integer-Filter 1', 'admin/gm_emails.php', array('_GET["id"]', '_GET["lang"]'), 'convert_to_integer');
$this->add_filter('Integer-Filter 2', array('admin/banner_manager.php', 
											'admin/banner_statistics.php', 
											'admin/blacklist.php'), '_GET["bID"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 3', array('admin/blacklist.php', 
											'admin/banner_manager.php', 
											'admin/banner_statistics.php', 
											'admin/campaigns.php', 
											'admin/countries.php', 
											'admin/coupon_admin.php', 
											'admin/cross_sell_groups.php', 
											'admin/currencies.php', 
											'admin/customers.php', 
											'admin/customers_status.php', 
											'admin/gm_feature_control.php',
											'admin/gm_slider.php',
											'admin/languages.php', 
											'admin/lettr_de.php', 
											'admin/manufacturers.php', 
											'admin/orders.php', 
											'admin/orders_status.php', 
											'admin/products_vpe.php',
											'admin/quantity_units.php', 
											'admin/specials.php', 
											'admin/shipping_status.php', 
											'admin/show_logs.php', 
											'admin/tax_classes.php', 
											'admin/tax_rates.php',
											'admin/yatego.php', 
											'admin/zones.php'), '_GET["page"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 4', 'admin/categories.php', array('_POST["products_id"]', 
																	'_POST["parent_id"]', 
																	'_POST["categories_id"]',
																	'_GET["pID"]',
																	'_POST["gm_gprint_delete_assignment"]',
																	'_GET["flag"]',
																	'_POST["show_sub_products"]'), 'convert_to_integer');
$this->add_filter('Integer-Filter 5', array('admin/campaigns.php',
											'admin/categories.php', 
											'admin/countries.php', 
											'admin/coupon_manager.php', 
											'admin/currencies.php', 
											'admin/customers.php', 
											'admin/customers_status.php', 
											'admin/orders.php', 
											'admin/orders_edit',
											'admin/zones.php'), '_GET["cID"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 6', 'admin/geo_zones.php', array('_GET["sID"]', 
																	'_GET["spage"]', 
																	'_GET["zID"]', 
																	'_GET["zpage"]'), 'convert_to_integer');
$this->add_filter('Integer-Filter 7', 'admin/gm_gprint.php', array('_POST["categories_id"]', 
																	'_GET["categories_id"]', 
																	'_GET["id"]', 
																	'_GET["languages_id"]'), 'convert_to_integer');
$this->add_filter('Integer-Filter 8', array('admin/gm_meta.php', 
											'admin/gm_scroller', 
											'admin/gm_statusbar'), '_GET["lang_id"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 9', 'admin/gm_slider.php', array('_REQUEST["lang_all"]', 
																	'_GET["newPIC"]',
																	'_GET["newTHUMB"]',
																	'_REQUEST["slider_set_id"]', 
																	'_POST["sliderWidth"]'), 'convert_to_integer');
$this->add_filter('Integer-Filter 10', array('admin/orders_edit.php', 
											'admin/orders_status.php', 
											'admin/products_vpe', 
											'admin/shipping_status.php'), '_GET["oID"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 11', 'admin/products_attributes.php', array('_POST["option_id"]',
																				'_GET["option_page"]', 
																				'_POST["value_id"]',
																				'_GET["value_page"]'), 'convert_to_integer');
$this->add_filter('Integer-Filter 12', array('admin/tax_classes.php', 'admin/tax_rates.php'), '_GET["tID"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 13', 'admin/yatego.php', array('_GET["topseller"]', '_GET["selectall"]'), 'convert_to_integer');
$this->add_filter('Integer-Filter 14', 'admin/customers.php', '_GET["status"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 15', 'admin/template_configuration.php', '_POST["show_flyover"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 16', 'admin/orders_edit.php', '_GET["cID"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 17', 'admin/languages.php', '_GET["lID"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 18', 'admin/gm_sitemap.php', '_GET["update"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 19', 'admin/gm_feature_control.php', array('_REQUEST["feature_id"]', '_REQUEST["lang_all"]') , 'convert_to_integer');
$this->add_filter('Integer-Filter 20', 'admin/banner_statistics.php', array('_GET["month"]', '_GET["year"]') , 'convert_to_integer');
$this->add_filter('Integer-Filter 21', 'admin/configuration.php', '_GET["gID"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 22', 'admin/content_manager.php', array('_GET["coID"]', '_POST["coID"]'), 'convert_to_integer');
$this->add_filter('Integer-Filter 23', 'admin/econda.php', '_GET["u"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 24', 'admin/gm_security.php', array('_POST["GM_LOGIN_TRYOUT"]', 
																		'_POST["GM_LOGIN_TIMELINE"]', 
																		'_POST["GM_LOGIN_TIMEOUT"]', 
																		'_POST["GM_SEARCH_TIMELINE"]', 
																		'_POST["GM_SEARCH_TIMEOUT"]', 
																		'_POST["GM_SEARCH_TRYOUT"]'), 'convert_to_integer');
$this->add_filter('Integer-Filter 25', 'admin/stats_sales_report.php', array('_GET["detail"]', 
																				'_GET["endD"]',
																				'_GET["endM"]',
																				'_GET["endY"]',
																				'_GET["export"]',
																				'_GET["max"]',
																				'_GET["report"]',
																				'_GET["sort"]',
																				'_GET["startD"]',
																				'_GET["startM"]',
																				'_GET["startY"]',
																				'_GET["status"]'), 'convert_to_integer');
$this->add_filter('Integer-Filter 26', 'admin/stats_campaigns.php', array('_GET["endD"]',
																			'_GET["endM"]',
																			'_GET["endY"]',
																			'_GET["export"]',
																			'_GET["report"]',
																			'_GET["startD"]',
																			'_GET["startM"]',
																			'_GET["startY"]',
																			'_GET["status"]'), 'convert_to_integer');
$this->add_filter('Integer-Filter 27', 'admin/specials.php', '_GET["sID"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 28', 'admin/popup_memo.php', '_GET["ID"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 29', 'admin/properties_combis.php', '_GET["products_id"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 30', 'admin/quantity_units.php', array('_REQUEST["lang_all"]',
																		 '_REQUEST["quanitity_unit_id"]',
																		 '_GET["quanitity_unit_id"]'), 'convert_to_integer');
$this->add_filter('Integer-Filter 31', array('admin/create_account.php',
						'admin/customers.php'), '_POST["entry_country_id"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 32', 'admin/create_account.php', '_POST["default_address_id"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 33', 'admin/csv_backend.php', '_POST["gm_delete_categories"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 34', 'admin/manufacturers.php', '_GET["mID"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 35', 'admin/orders.php', '_POST["status"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 36', 'admin/gm_ebay.php', '_POST["GM_EBAY_COUNT"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 37', 'admin/coupon_admin.php', '_GET["cid"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 38', 'admin/gm_opensearch.php', '_GET["lang_id"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 39', 'admin/new_attributes.php', array('_POST["current_product_id"]',
																		'_POST["copy_product_id"]'), 'convert_to_integer');
$this->add_filter('Integer-Filter 40', 'admin/yatego.php', '_GET["category"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 41', 'admin/print_intraship_label.php', '_GET["oID"]', 'convert_to_integer');
$this->add_filter('Integer-Filter 42', 'admin/intraship.php', array('_POST["intraship"]["active"]',
																	'_POST["intraship"]["debug"]',
																	'_POST["intraship"]["send_email"]',
																	'_POST["intraship"]["send_announcement"]',
																	'_POST["intraship"]["bpi_use_premium"]',
																	'_POST["intraship"]["use_postfinder"]',
																	'_POST["intraship"]["status_id_storno"]',
																	'_POST["intraship"]["status_id_sent"]'), 'convert_to_integer');
$this->add_filter('Integer-Filter 43', 'admin/orders.php', '_GET["ptd_order_id"]', 'convert_to_integer');

$this->add_filter('Nummerisch-Filter 1', 'admin/gm_gmotion.php', array('_POST["gm_gmotion_standard_zoom_from"]', '_POST["gm_gmotion_standard_zoom_to"]'), 'only_numeric');
$this->add_filter('Nummerisch-Filter 2', 'admin/orders.php', '_GET["status"]', 'only_numeric');
$this->add_filter('Nummerisch-Filter 3', 'admin/specials.php', array('_GET["cboMonth"]', '_GET["cboYear"]'), 'only_numeric');
$this->add_filter('Nummerisch-Filter 4', 'admin/intraship.php', array('_POST["intraship"]["cod_account_number"]',
																	'_POST["intraship"]["cod_bank_number"]'), 'only_numeric');

$this->add_filter('Preis-Filter 1', 'admin/specials.php', '_POST["specials_price"]', 'filter_price');

$this->add_filter('Text-Filter 1', 'admin/products_attributes.php', array('_POST["option_name"]', '_POST["value_name"]'), 'filter_text');
$this->add_filter('Text-Filter 2', 'admin/gm_gprint.php', '_GET["surfaces_group_name"]', 'filter_text');
$this->add_filter('Text-Filter 3', 'admin/quantity_units.php', '_POST["unitNew"]', 'filter_text');
$this->add_filter('Text-Filter 4', 'admin/clear_cache.php', array('_GET["manual_categories_index"]', 
																'_GET["manual_categories_index"]', 
																'_GET["manual_data_cache"]', 
																'_GET["manual_feature_index"]', 
																'_GET["manual_output"]', 
																'_GET["manual_products_properties_index"]', 
																'_GET["manual_submenu"]'), 'filter_text');
$this->add_filter('Text-Filter 5', 'admin/customers.php', '_GET["search"]', 'filter_text');
$this->add_filter('Text-Filter 6', 'admin/magnalister.php', array('_GET["search"]',
																	'_POST["conf"]["general.firstactivation"]',
																	'_POST["conf"]["general.passphrase"]'), 'filter_text');
$this->add_filter('Text-Filter 7', 'admin/intraship.php', array('_POST["intraship"]["password"]',
																'_POST["intraship"]["shipper_name"]',
																'_POST["intraship"]["shipper_contact"]',
																'_POST["intraship"]["shipper_city"]',
																'_POST["intraship"]["shipper_street"]',
																'_POST["intraship"]["cod_account_holder"]',
																'_POST["intraship"]["cod_bank_name"]'), 'filter_text');
$this->add_filter('Text-Filter 8', 'admin/gm_slider.php', array('_POST["sliderName"]',
                                                                           '_POST["sliderNew"]'), 'filter_text');

$this->add_filter('Zeichen-Filter 1', 'admin/coupon_admin.php', '_GET["status"]', 'only_safe_characters');
$this->add_filter('Zeichen-Filter 2', 'admin/categories.php', array('_POST["products_sorting"]', '_POST["products_sorting2"]'), 'only_safe_characters');
$this->add_filter('Zeichen-Filter 3', 'admin/mail.php', '_POST["customers_email_address"]', 'only_safe_characters');
$this->add_filter('Zeichen-Filter 4', 'admin/gm_product_export.php', '_GET["module"]', 'only_safe_characters');
$this->add_filter('Zeichen-Filter 5', 'admin/languages.php', array('_POST["charset"]',
																	'_POST["directory"]'), 'only_safe_characters');
$this->add_filter('Zeichen-Filter 6', 'admin/show_logs.php', array('_GET["file"]',
																	'_GET["hidden_file"]'), 'only_safe_characters');
$this->add_filter('Zeichen-Filter 7', 'admin/products_attributes.php', '_GET["option_order_by"]', 'only_safe_characters');
$this->add_filter('Zeichen-Filter 8', 'admin/amazoncheckout_config.php', array('_REQUEST["accesskey"]',
																				'_REQUEST["button_background"]',
																				'_REQUEST["button_color"]',
																				'_REQUEST["button_size"]',
																				'_REQUEST["customers_status"]',
																				'_REQUEST["marketplace_id"]',
																				'_REQUEST["merchant_id"]',
																				'_REQUEST["mode"]',
																				'_REQUEST["os_cancel"]',
																				'_REQUEST["os_new"]',
																				'_REQUEST["os_ready"]',
																				'_REQUEST["os_shipped"]',
																				'_REQUEST["secretkey"]'), 'only_safe_characters');
$this->add_filter('Zeichen-Filter 9', 'admin/intraship.php', array('_POST["intraship"]["user"]',
																	'_POST["intraship"]["intraship_zone_1_countries"]',
																	'_POST["intraship"]["intraship_zone_2_countries"]',
																	'_POST["intraship"]["intraship_zone_3_countries"]',
																	'_POST["intraship"]["intraship_zone_4_countries"]',
																	'_POST["intraship"]["shipper_house"]',
																	'_POST["intraship"]["shipper_postcode"]',
																	'_POST["intraship"]["shipper_email"]',
																	'_POST["intraship"]["shipper_phone"]',
																	'_POST["intraship"]["cod_iban"]',
																	'_POST["intraship"]["cod_bic"]'), 'only_safe_characters');
$this->add_filter('Zeichen-Filter 10', array('export/xml_export.php',
											'export/cao_import.php'), array('_GET["user"]',
																	'_GET["password"]',
																	'_POST["user"]',
																	'_POST["password"]'), 'only_safe_characters');

$this->add_filter('Rekursiv-Alphanumerisch-Filter 1', 'admin/accounting.php', '_POST["access"]', 'recursive_only_alphanumeric');

$this->add_filter('Rekursiv-Text-Filter 1', 'admin/quantity_units.php', '_POST["unitName"]', 'recursive_filter_text');
$this->add_filter('Rekursiv-Text-Filter 2', 'admin/includes/ckeditor/filemanager/connectors/php/inc/vendor/wideimage/demo/index.php', array('_GET["matrix"]',
																																			'_GET["text"]',
																																			'_GET["x"]',
																																			'_GET["y"]'), 'recursive_filter_text');

$this->add_filter('Rekursiv-Zeichen-Filter 1', 'admin/gm_module_export.php', '_POST["configuration"]', 'recursive_only_safe_characters');

$this->add_filter('Rekursiv-htmlspecialchars-Filter 1', 'admin/gm_feature_control.php', '_POST["featAdminName"]', 'recursive_htmlspecialchars');
$this->add_filter('Rekursiv-htmlspecialchars-Filter 2', 'admin/gm_feature_control.php', '_POST["featName"]', 'recursive_htmlspecialchars');
$this->add_filter('Rekursiv-htmlspecialchars-Filter 3', 'admin/gm_feature_control.php', '_POST["featNew"]', 'recursive_htmlspecialchars');
$this->add_filter('Rekursiv-htmlspecialchars-Filter 4', 'admin/gm_feature_control.php', '_POST["featValueNew"]', 'recursive_htmlspecialchars');
