<?php
/* --------------------------------------------------------------
   setup_shop.php 2016-02-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2003	 nextcommerce (install_step7.php,v 1.26 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: install_step7.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require_once(DIR_FS_INC . 'strtoupper_wrapper.inc.php');
require_once(DIR_FS_INC . 'strtolower_wrapper.inc.php');
require_once(DIR_FS_INC . 'strpos_wrapper.inc.php');
require_once(DIR_FS_INC . 'substr_wrapper.inc.php');
require_once(DIR_FS_INC . 'xtc_rand.inc.php');
require_once(DIR_FS_INC . 'xtc_encrypt_password.inc.php');
require_once(DIR_FS_INC . 'xtc_db_connect.inc.php');
require_once(DIR_FS_INC . 'xtc_db_query.inc.php');
require_once(DIR_FS_INC . 'xtc_db_fetch_array.inc.php');
require_once(DIR_FS_INC . 'xtc_validate_email.inc.php');
require_once(DIR_FS_INC . 'xtc_db_input.inc.php');
require_once(DIR_FS_INC . 'xtc_db_num_rows.inc.php');
require_once(DIR_FS_INC . 'xtc_redirect.inc.php');
require_once(DIR_FS_INC . 'xtc_href_link.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_pull_down_menu.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_input_field.inc.php');
require_once(DIR_FS_INC . 'xtc_get_country_list.inc.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_get_env_info.inc.php');
require_once(DIR_FS_CATALOG . 'system/core/logging/Debugger.inc.php');
require_once(DIR_FS_CATALOG . 'system/core/caching/DataCache.inc.php');

include('language/' . $_SESSION['language'] . '.php');

// connect do database
xtc_db_connect() or die('Unable to connect to database server!');

// get configuration data
$configuration_query = xtc_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION);
while ($configuration = xtc_db_fetch_array($configuration_query)) {
	define($configuration['cfgKey'], $configuration['cfgValue']);
}

if (is_dir(DIR_FS_CATALOG . 'templates/EyeCandy/') == false) {
	# set to gambio template, if eyecandy is not available
	xtc_db_query("UPDATE configuration SET configuration_value = 'gambio' WHERE configuration_key = 'CURRENT_TEMPLATE' ");
}

$process = false;
if (isset($_POST['action']) && ($_POST['action'] == 'setup_shop')) {
	$process = true;

	$status_discount = xtc_db_input($_POST['STATUS_DISCOUNT']);
	$status_ot_discount_flag = xtc_db_input($_POST['STATUS_OT_DISCOUNT_FLAG']);
	$status_ot_discount = xtc_db_input($_POST['STATUS_OT_DISCOUNT']);
	$graduated_price = xtc_db_input($_POST['STATUS_GRADUATED_PRICE']);
	$show_price = xtc_db_input($_POST['STATUS_SHOW_PRICE']);
	$show_tax = xtc_db_input($_POST['STATUS_SHOW_TAX']);


	$status_discount2 = xtc_db_input($_POST['STATUS_DISCOUNT2']);
	$status_ot_discount_flag2 = xtc_db_input($_POST['STATUS_OT_DISCOUNT_FLAG2']);
	$status_ot_discount2 = xtc_db_input($_POST['STATUS_OT_DISCOUNT2']);
	$graduated_price2 = xtc_db_input($_POST['STATUS_GRADUATED_PRICE2']);
	$show_price2 = xtc_db_input($_POST['STATUS_SHOW_PRICE2']);
	$show_tax2 = xtc_db_input($_POST['STATUS_SHOW_TAX2']);

	$error = false;
	// default guests
	if (strlen($status_discount) < '3') {
		$error = true;
	}
	if (strlen($status_ot_discount) < '3') {
		$error = true;
	}
	if (($status_ot_discount_flag != '1') && ($status_ot_discount_flag != '0')) {
		$error = true;
	}
	if (($graduated_price != '1') && ($graduated_price != '0')) {
		$error = true;
	}
	if (($show_price != '1') && ($show_price != '0')) {
		$error = true;
	}
	if (($show_tax != '1') && ($show_tax != '0')) {
		$error = true;
	}

	// default customers
	if (strlen($status_discount2) < '3') {
		$error = true;
	}
	if (strlen($status_ot_discount2) < '3') {
		$error = true;
	}
	if (($status_ot_discount_flag2 != '1') && ($status_ot_discount_flag2 != '0')) {
		$error = true;
	}
	if (($graduated_price2 != '1') && ($graduated_price2 != '0')) {
		$error = true;
	}
	if (($show_price2 != '1') && ($show_price2 != '0')) {
		$error = true;
	}
	if (($show_tax2 != '1') && ($show_tax2 != '0')) {
		$error = true;
	}

	if ($error == false) {

		// BOF GM_MOD
		$rand = rand(1, 20);
		switch($rand)
		{
			case 1:
				$keyword = 'Shopsoftware';
				break;
			case 2:
				$keyword = 'Onlineshop';
				break;
			case 3:
				$keyword = 'Webshop';
				break;
			case 4:
				$keyword = 'Internetshop';
				break;
			case 5:
				$keyword = 'Shopsystem';
				break;
			case 6:
			case 7:
			case 8:
				$keyword = 'E-Commerce Software';
				break;
			case 9:
			case 10:
			case 11:
				$keyword = 'Shoplösung';
				break;
			case 12:
			case 13:
			case 14:
				$keyword = 'Onlineshop Lösung';
				break;
			case 15:
			case 16:
			case 17:
				$keyword = 'Online-Shop';
				break;
			case 18:
			case 19:
			case 20:
				$keyword = 'Onlineshop Software';
				break;
		}
		$year = date('Y');
		$gm_footer = '<a href="http://www.gambio.de" target="_blank">' . $keyword . '</a> by Gambio.de &copy; ' . $year;
		xtc_db_query("UPDATE gm_configuration SET gm_value = '" . $gm_footer . "' WHERE gm_key = 'GM_FOOTER'");
		// BOF GM_MOD
		//
		// admin
		xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax, customers_status_add_tax_ot) VALUES ('0', '1', 'Admin', 0, 'admin_status.gif', '0.00', '1', '0.00', '1', '1', '1', '1')");
		xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax, customers_status_add_tax_ot) VALUES ('0', '2', 'Admin', 0, 'admin_status.gif', '0.00', '1', '0.00', '1', '1', '1', '1')");

		// status Guest
		xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax, customers_status_write_reviews, customers_status_add_tax_ot) VALUES (1, 1, 'Guest', 0, 'guest_status.gif', '" . $status_discount . "', '" . $status_ot_discount_flag . "', '" . $status_ot_discount . "', '" . $graduated_price . "', '" . $show_price . "', '" . $show_tax . "', '0', '1')");
		xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax, customers_status_write_reviews, customers_status_add_tax_ot) VALUES (1, 2, 'Gast', 0, 'guest_status.gif', '" . $status_discount . "', '" . $status_ot_discount_flag . "', '" . $status_ot_discount . "', '" . $graduated_price . "', '" . $show_price . "', '" . $show_tax . "', '0', '1')");

		// status New customer
		xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax, customers_status_add_tax_ot) VALUES (2, 1, 'New customer', 0, 'customer_status.gif', '" . $status_discount2 . "', '" . $status_ot_discount_flag2 . "', '" . $status_ot_discount2 . "', '" . $graduated_price2 . "', '" . $show_price2 . "', '" . $show_tax2 . "', '1')");
		xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax, customers_status_add_tax_ot) VALUES (2, 2, 'Neuer Kunde', 0, 'customer_status.gif', '" . $status_discount2 . "', '" . $status_ot_discount_flag2 . "', '" . $status_ot_discount2 . "', '" . $graduated_price2 . "', '" . $show_price2 . "', '" . $show_tax2 . "', '1')");

		// status Merchant
		xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax, customers_status_add_tax_ot) VALUES (3, 1, 'Merchant', 0, 'merchant_status.gif', '0.00', '0', '0.00', '1', 1, 0, '1')");
		xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax, customers_status_add_tax_ot) VALUES (3, 2, 'Händler', 0, 'merchant_status.gif', '0.00', '0', '0.00', '1', 1, 0, '1')");

		// create Group prices (Admin wont get own status!)
		xtc_db_query("CREATE TABLE `personal_offers_by_customers_status_0` (
						`price_id` int(11) NOT NULL AUTO_INCREMENT,
						`products_id` int(11) NOT NULL,
						`quantity` decimal(15,4) DEFAULT NULL,
						`personal_offer` decimal(15,4) DEFAULT NULL,
						PRIMARY KEY (`price_id`),
						UNIQUE KEY `unique_offer` (`products_id`,`quantity`)
						) ENGINE=MyISAM");
		xtc_db_query("CREATE TABLE `personal_offers_by_customers_status_1` (
						`price_id` int(11) NOT NULL AUTO_INCREMENT,
						`products_id` int(11) NOT NULL,
						`quantity` decimal(15,4) DEFAULT NULL,
						`personal_offer` decimal(15,4) DEFAULT NULL,
						PRIMARY KEY (`price_id`),
						UNIQUE KEY `unique_offer` (`products_id`,`quantity`)
						) ENGINE=MyISAM");
		xtc_db_query("CREATE TABLE `personal_offers_by_customers_status_2` (
						`price_id` int(11) NOT NULL AUTO_INCREMENT,
						`products_id` int(11) NOT NULL,
						`quantity` decimal(15,4) DEFAULT NULL,
						`personal_offer` decimal(15,4) DEFAULT NULL,
						PRIMARY KEY (`price_id`),
						UNIQUE KEY `unique_offer` (`products_id`,`quantity`)
						) ENGINE=MyISAM");
		xtc_db_query("CREATE TABLE `personal_offers_by_customers_status_3` (
						`price_id` int(11) NOT NULL AUTO_INCREMENT,
						`products_id` int(11) NOT NULL,
						`quantity` decimal(15,4) DEFAULT NULL,
						`personal_offer` decimal(15,4) DEFAULT NULL,
						PRIMARY KEY (`price_id`),
						UNIQUE KEY `unique_offer` (`products_id`,`quantity`)
						) ENGINE=MyISAM");
		
		$t_version_history_sql = "INSERT INTO `version_history` 
									SET 
										`version` = '2.6.1.1',
										`name` = 'v2.6.1.1',
										`type` = 'service_pack', 
										`revision` = 0, 
										`is_full_version` = 1, 
										`installation_date` = NOW(), 
										`php_version` = '" . mysql_real_escape_string(PHP_VERSION) . "', 
										`mysql_version` = VERSION()";
		xtc_db_query($t_version_history_sql);

		include(DIR_FS_CATALOG . 'release_info.php');
		$t_installed_version_sql = "INSERT INTO `gm_configuration` 
									SET 
										`gm_key` = 'INSTALLED_VERSION',
										`gm_value` = '" . $gx_version . "'";
		xtc_db_query($t_installed_version_sql);
	}

	$dataCache = DataCache::get_instance();
	$dataCache->clear_cache();
	
	$phraseCacheBuilder = MainFactory::create('PhraseCacheBuilder');
	$phraseCacheBuilder->build();
	
	$mailTemplatesCacheBuilder = MainFactory::create('MailTemplatesCacheBuilder');
	$mailTemplatesCacheBuilder->build();
}
