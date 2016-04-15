<?php
/* ----------------------------------------------------------------------------------------- 
   $Id: econda.php ???? 2009-03-19 10:00:00Z mz $
   
   Modified Version for Gambio GX

	refactored on 2014-01-31 by Gambio GmbH
	
   ----------------------------------------------------------------------------------------
   based on:
   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2005 osCommerce(econda.php,v 1.42 2003/06/10); www.econda.de

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------

   Copyright (c) 2009 ECONDA GmbH Karlsruhe
   All rights reserved.

   ECONDA GmbH
   Eisenlohrstr. 43
   76135 Karlsruhe
   Tel. +49 (721) 663035-0
   Fax +49 (721) 663035-10
   info@econda.de
   www.econda.de

*/

echo "\n<!-- Econda-Monitor -->\n";


function econda_getAttributes($lang_id = 2) {
	static $_options = null;
	if($_options == null) {
		$query = "SELECT 
						po.products_options_id, 
						po.products_options_name, 
						pov.products_options_values_id, 
						pov.products_options_values_name
					FROM
						products_options po,
						products_options_values pov,
						products_options_values_to_products_options pov2po
					WHERE
						po.products_options_id = pov2po.products_options_id AND
						po.language_id = '" . (int)$lang_id . "' AND
						pov.products_options_values_id = pov2po.products_options_values_id AND
						pov.language_id = '" . (int)$lang_id . "'";
		$result = xtc_db_query($query);
		$options = array();
		while($row = xtc_db_fetch_array($result)) {
			if(!is_array($options[$row['products_options_id']])) {
				$options[$row['products_options_id']] = array(
						'name' => $row['products_options_name'],
						'values' => array()
				);
			}
			$options[$row['products_options_id']]['values'][$row['products_options_values_id']] = $row['products_options_values_name'];
		}
		$_options = $options;
	}
	return $_options;
}

// cPath = id1_id2 => name1/name2
function product_path_by_name($product, $lang) {
	require_once (DIR_FS_INC.'xtc_get_product_path.inc.php');
	$product_path_by_id = xtc_get_product_path($product);
	$product_categories_id = explode("_" , $product_path_by_id);

	$new_product_path_by_name = '';

	for ($i = 0, $n = sizeof($product_categories_id); $i < $n; $i++) {
		$product_path_by_name_query = xtc_db_query("select categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $product_categories_id[$i] . "' and language_id = '". (int)$lang ."'");
		$product_path_by_name = xtc_db_fetch_array($product_path_by_name_query);
		$new_product_path_by_name .= $product_path_by_name['categories_name'];
		if (($i+1) < $n) {
			$new_product_path_by_name .= '/';
		}
	}
	return $new_product_path_by_name;
}

function product_to_EMOSItem($product, $lang, $quant, $cedit_id = 0) {
	require_once (DIR_FS_INC.'xtc_get_tax_rate.inc.php');
	require_once (DIR_FS_CATALOG.'includes/classes/xtcPrice.php');
	$product_to_emos_query = xtc_db_query("select p.products_id, pd.products_name, p.products_model, p.products_price, p.products_tax_class_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$product. "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$lang . "'");
	$product_to_emos = xtc_db_fetch_array($product_to_emos_query);
	$emos_xtPrice = new xtcPrice(DEFAULT_CURRENCY,$_SESSION['customers_status']['customers_status_id']);
	$product_to_emos_price = $emos_xtPrice->xtcGetPrice($product_to_emos['products_id'], false, $quant, $product_to_emos['products_tax_class_id'], $product_to_emos['products_price'], '', $cedit_id);
	if (ECONDA_PRICE_IS_BRUTTO == 'false') {
		$product_to_emos_price = sprintf("%0.2f",($product_to_emos_price / ((xtc_get_tax_rate($product_to_emos['products_tax_class_id']) + 100) / 100)));
	}
	$item = new EMOS_Item();
	$item->productID = $product; # $product_to_emos['products_id'];
	$item->productName = $product_to_emos['products_name'];
	$item->price = $product_to_emos_price;
	$item->productGroup = product_path_by_name((int)$product, (int)$lang)."/". $product_to_emos['products_name'];
	$item->quantity = (int)$quant;

	$item->variant1 = "NULL";
	$item->variant2 = "NULL";
	$item->variant3 = "NULL";

	# determine variants
	$current_variant = 1;
	$options = array();
	$products_properties_combis_id = null;
	if(strpos($product, 'x') !== false)
	{
		list($product, $products_properties_combis_id) = explode('x', $product);
		$prop_query =
			'SELECT `properties_name`, `values_name`
				FROM `products_properties_index`
				WHERE 
					`products_id` = :products_id AND
					`language_id` = :language_id AND
					`products_properties_combis_id` = :ppcid
				ORDER BY `properties_sort_order` ASC, `value_sort_order` ASC';
		$prop_query = strtr($prop_query, array(
				':products_id' => (int)$product,
				':language_id' => (int)$lang,
				':ppcid' => (int)$products_properties_combis_id,
			));
		$prop_result = xtc_db_query($prop_query);
		while($current_variant <= 3 && $prop_row = xtc_db_fetch_array($prop_result))
		{
			$variant_name = 'variant'.$current_variant;
			$variant_str = $prop_row['properties_name'].': '.$prop_row['values_name'];
			$item->$variant_name = $variant_str;
			$current_variant++;
		}
	}

	if($current_variant <= 3 && strpos($product, '{') !== false)
	{
		$attributes_names = econda_getAttributes($lang);
		$options_string = substr($product, strpos($product, '{'));
		$parts = explode('{', $options_string);
		$options = array();
		foreach($parts as $p) {
			if(empty($p)) {
				continue;
			}
			list($options_id, $options_values_id) = explode('}', $p);
			$options[$options_id] = $options_values_id;
			$variant_name = 'variant'.$current_variant;
			$variant_str = $attributes_names[$options_id]['name'] .': '.$attributes_names[$options_id]['values'][$options_values_id];
			$item->$variant_name = $variant_str;
			$current_variant++;
			if($current_variant > 3) {
				break;
			}
		}
	}

	return $item;
}

function breadcrumbToContent($trail) { // breadcrumb to emosContent helper
	$econda_string = '';
	for ($i=0, $n=sizeof($trail); $i<$n; $i++) {
		$econda_string .= $trail[$i]['title'];
		if (($i) < $n) $econda_string .= '/';
	}
	return substr($econda_string,0,-1);
}

$breadcrumb = $GLOBALS['breadcrumb'];
$product = $GLOBALS['product'];
$shop_content_data = $GLOBALS['shop_content_data'];
$listing_split = $GLOBALS['listing_split'];

// new instance
$emos = new EMOS();
$emos->debugMode(0); # 0 = no debug; 1 = debug output, no tracking; 2 = debug output + tracking
$emos->prettyPrint();
$emos->trackMode(2); // switch to new style tracking
$emos->addCdata();

// Startseite >> Katalog >> Kategorie >> .. => Startseite/Katalog/Kategorie/..
//$emos->addContent(htmlspecialchars($breadcrumb->econda()),ENT_QUOTES);
//$emos->addContent(urlencode($breadcrumb->econda()));

$getTrail = $breadcrumb->_trail;
$brToCont = breadcrumbToContent($getTrail);
$emos->addContent($brToCont);


// login erfolgreich
/*
if(isset($_SESSION['login_success'])) {
	if(is_object($GLOBALS['econda'])) {	$GLOBALS['econda']->log('app_bottom: '.'reporting login'); }
	$emos->addLogin($_SESSION['customer_id'],'0');
	unset($_SESSION['login_success']);
}
*/

// $current_page = basename($PHP_SELF);
// $current_page = split('\?', basename($_SERVER['PHP_SELF'])); $current_page = $current_page[0]; // for BadBlue(Win32) webserver compatibility
$current_page = join('',preg_grep("/.+\.php$/", preg_split("/\?|\//", $_SERVER['PHP_SELF'])));

// adds siteID
$getSiteId = $_SERVER['SERVER_NAME'];
#$emos->addLangID($getSiteId);
$emos->addLangID(strtoupper($_SESSION['language_code']));

// adds pageID
$getPageId == "";

if(sizeof($_GET) != 0) {
	if(trim($current_page) != "")
	{
		$getPageId .= $current_page;
	}
	else
	{
		$current_page .= "start";
	}

	if(trim($_GET['cat']) != "") {$getPageId .= "_".$_GET['cat'];}
	if(trim($_GET['info']) != "") {$getPageId .= "_".$_GET['info'];}
	if(trim($_GET['products_id']) != "") {$getPageId .= "_".$_GET['products_id'];}
	if(trim($_GET['coID']) != "") {$getPageId .= "_".$_GET['coID'];}
	if(trim($_GET['keywords']) != "") {$getPageId .= "_suche";}
	if(trim($_GET['manu']) != "") {$getPageId .= "_".$_GET['manu'];}
	if(trim($_GET['order_id']) != "") {$getPageId .= "_".$_GET['order_id'];}
	if(trim($_GET['cPath']) != "") {$getPageId .= "_".$_GET['cPath'];}
	if(trim($_GET['language']) != "") {$getPageId .= "_".$_GET['language'];}

}
else {
	if (trim($current_page) != "") {
		$getPageId .= $current_page;
	}
	else {
		$getPageId .= "start";
	}
}

$emos->addPageID(md5($getPageId));

//Fix: if open basket is deactivated after adding an item
$inBasket = true;
if ($_SESSION['econda_cart']) {
		//for ($i=0, $n=sizeof($_SESSION['econda_cart']); $i<$n; $i++) {
	for ($i=0; $i < sizeof($_SESSION['econda_cart']); $i++) {    		
		if ($_SESSION['econda_cart'][$i]['cart_qty'] != $_SESSION['econda_cart'][$i]['old_qty']) {
			if($current_page != FILENAME_SHOPPING_CART) {
				$inBasket = false;
			}             	
			$current_page = FILENAME_SHOPPING_CART;
		}
	}
}

if(is_object($GLOBALS['econda']))
{
	$GLOBALS['econda']->log('app_bottom: '.'processing for page '.$current_page);
}


switch ($current_page) {
	case FILENAME_PRODUCT_INFO:
		if (is_object($product) && $product->isProduct()) {
			$item = product_to_EMOSItem($product->data['products_id'],$_SESSION['languages_id'], 1);
			$emos->addDetailView($item);
		}
		break;

	case FILENAME_CONTENT:
		if($_GET['coID'] == '7' && $_GET['action'] == 'success') {
			# coID == 7 -> Contact
			$emos->addContact($shop_content_data['content_heading']);
		}
			
		break;

	case FILENAME_ADVANCED_SEARCH_RESULT:
		$numRows = $listing_split->number_of_rows;
		if(!$numRows) $numRows = 0;
		if ($error == 0 || $keyerror == 1) $emos->addSearch($_GET['keywords'],$numRows);
		break;

	case FILENAME_CREATE_ACCOUNT:
		if($messageStack){
			if ($messageStack->size('create_account') > 0) { // Registrierung fehlerhaft
					$emos->addRegister('0','1'); // no customer_id given, dummy id
					$emos->addOrderProcess("2_Anmelden/Neu");
			} elseif ($_SESSION['customer_id']) { // Registrierung erfolgreich
				$emos->addRegister($_SESSION['customer_id'],'0');
				$emos->addOrderProcess("2_Anmelden/Erfolg");
			}
		}
	break;

	case FILENAME_LOGIN:
	case FILENAME_ACCOUNT:
	case FILENAME_DEFAULT:
		if(is_object($GLOBALS['econda'])) {	$GLOBALS['econda']->log('app_bottom: '.'case login/account/default'); }
		if(isset($_SESSION['login_success']))
		{
			if(is_object($GLOBALS['econda'])) {	$GLOBALS['econda']->log('app_bottom: '.'reporting login (login/account)'); }
			switch($_SESSION['login_success'])
			{
				case 1:
					$emos->addOrderProcess("2_Anmelden");
					$emos->addLogin($_SESSION['customer_id'],'0');
					break;

				case -1:
					$emos->addOrderProcess("2_Anmelden/BenutzerUnbekannt");
					$emos->addLogin('0','0');
					break;

				case -2:
					$emos->addOrderProcess("2_Anmelden/PasswortFalsch");
					$emos->addLogin('0','0');
					break;
			}
			unset($_SESSION['login_success']);
		}
		break;

	case FILENAME_SHOPPING_CART:
		if(is_object($GLOBALS['econda'])) {	$GLOBALS['econda']->log('app_bottom: '.'case shopping_cart'); }

		$emos->addOrderProcess("1_Warenkorb");
		if(isset($_SESSION['econda_user_registered']))
		{
			$emos->addRegister($_SESSION['customer_id'], 0);
			unset($_SESSION['econda_user_registered']);	
		}
		$setUpdate = 0;
		if(empty($_SESSION['econda_cart']) === false) {
			if(is_object($GLOBALS['econda'])) {	$GLOBALS['econda']->log('app_bottom: '.'econda_cart is not empty:'.print_r($_SESSION['econda_cart'], true)); }
			//for ($i=0, $n=sizeof($_SESSION['econda_cart']); $i<$n; $i++) {
			for ($i=0; $i < sizeof($_SESSION['econda_cart']); $i++) {
				if(is_object($GLOBALS['econda'])) { $GLOBALS['econda']->log('app_bottom: '."processing econda_cart entry $i"); }
				require_once (DIR_FS_INC.'xtc_check_categories_status.inc.php');
				require_once (DIR_FS_INC.'xtc_get_products_mo_images.inc.php');
				require_once (DIR_FS_INC.'xtc_get_vpe_name.inc.php');
				require_once (DIR_FS_INC.'get_cross_sell_name.inc.php');          	

				//fix wrong cart count
				$bsFix = false;

				$products = $_SESSION['cart']->get_products();

				foreach($products as $product)
				{
					if($product['id'] == $_SESSION['econda_cart'][$i]['id'])
					{
						if($inBasket)
						{
							$bsFix = true;
						}
						$actCartQty = trim((int)$product['quantity']);
					}
				}
				if(is_object($GLOBALS['econda'])) {	$GLOBALS['econda']->log('app_bottom: '."actCartQty = $actCartQty"); }

				if(!$inBasket) {
					# $emos->appendPreScript('NO');
					if($actCartQty > $_SESSION['econda_cart'][$i]['old_qty']) {
						$new_qty = $actCartQty - $_SESSION['econda_cart'][$i]['old_qty'];
						$setUpdate = 1;	
					}

					elseif($actCartQty < $_SESSION['econda_cart'][$i]['old_qty']) {
						$new_qty = $actCartQty - $_SESSION['econda_cart'][$i]['old_qty'];
						$setUpdate = 2;
					}
				}

				else {
					if($_SESSION['econda_cart'][$i]['cart_qty'] > $_SESSION['econda_cart'][$i]['old_qty']) {
						if($bsFix){
							$new_qty = $actCartQty - $_SESSION['econda_cart'][$i]['old_qty'];
						}
						else {
							$new_qty = $_SESSION['econda_cart'][$i]['cart_qty'] - $_SESSION['econda_cart'][$i]['old_qty'];
						}
						$setUpdate = 1;
						# $emos->appendPreScript('YEA '.$_SESSION['econda_cart'][$i]['cart_qty']);
					}

					elseif($_SESSION['econda_cart'][$i]['cart_qty'] < $_SESSION['econda_cart'][$i]['old_qty']) {
						$new_qty = $_SESSION['econda_cart'][$i]['cart_qty'] - $_SESSION['econda_cart'][$i]['old_qty'];
						$setUpdate = 2;
					}                   				
				}							

				$item = product_to_EMOSItem($_SESSION['econda_cart'][$i]['id'],$_SESSION['languages_id'], abs($new_qty));
				if(is_object($GLOBALS['econda'])) {	$GLOBALS['econda']->log('app_bottom: '."product_to_EMOSItem(".$_SESSION['econda_cart'][$i]['id'].",".$_SESSION['languages_id'].",". abs($new_qty).");\n".print_r($item, true)); }

				if($setUpdate == 1) {
					if(is_object($GLOBALS['econda'])) {	$GLOBALS['econda']->log('app_bottom: '.'calling emos->addToBasket'); }
					$emos->addToBasket($item);
				}
				elseif($setUpdate == 2) {
					if(is_object($GLOBALS['econda'])) {	$GLOBALS['econda']->log('app_bottom: '.'calling emos->removeFromBasket'); }
					$emos->removeFromBasket($item);
				} 
				$setUpdate = 0; 
				$bsFix = false;                      	
			}
			unset($_SESSION['econda_cart']);
		}
		break;

	case FILENAME_CHECKOUT_SHIPPING:
		if(isset($_SESSION['login_success']))
		{
			if(is_object($GLOBALS['econda'])) {	$GLOBALS['econda']->log('app_bottom: '.'reporting login (login/account)'); }
			switch($_SESSION['login_success'])
			{
				case 1:
					$emos->addOrderProcess("2_Anmelden");
					$emos->addLogin($_SESSION['customer_id'],'0');
					break;

				case -1:
					$emos->addOrderProcess("2_Anmelden/BenutzerUnbekannt");
					$emos->addLogin('0','0');
					break;

				case -2:
					$emos->addOrderProcess("2_Anmelden/PasswortFalsch");
					$emos->addLogin('0','0');
					break;
			}
			unset($_SESSION['login_success']);
		}

		$emos->addOrderProcess("3_Versand");
		if(isset($_SESSION['econda_user_registered']))
		{
			$emos->addRegister($_SESSION['customer_id'], 0);
			unset($_SESSION['econda_user_registered']);	
		}
		break;

	case FILENAME_CHECKOUT_SHIPPING_ADDRESS:
		$emos->addOrderProcess("3_Versand/Lieferadresse");
		break;

	case FILENAME_CHECKOUT_PAYMENT:
		$emos->addOrderProcess("4_Zahlung");
		break;

	case FILENAME_CHECKOUT_PAYMENT_ADDRESS:
		$emos->addOrderProcess("4_Zahlung/Rechnungsadresse");
		break;

	case FILENAME_CHECKOUT_CONFIRMATION:
		$emos->addOrderProcess("5_Bestaetigung");
		break;

	case FILENAME_CHECKOUT_SUCCESS:
		$emos->addOrderProcess("6_Erfolg");
		// billing daten
		$last_orders_query = xtc_db_query("select orders_id, customers_city, customers_postcode, customers_country from "
			. TABLE_ORDERS . " where customers_id = '" . (int)$_SESSION['customer_id']
			. "' order by date_purchased desc limit 1");
		$last_orders = xtc_db_fetch_array($last_orders_query);
		// basket daten
		$last_orders_products_query = xtc_db_query("select products_id, products_quantity, products_price, products_tax from "
			. TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$last_orders['orders_id']
			. "' order by orders_products_id");
		$count = 0;
		$basket = array();
		$last_orders_totalprice = 0;
		while ($last_orders_products = xtc_db_fetch_array($last_orders_products_query)) {
			if (ECONDA_PRICE_IS_BRUTTO == 'false') {
				$last_orders_totalprice += $last_orders_products['products_price'] * $last_orders_products['products_quantity'] / (1+$last_orders_products['products_tax']/100);
			} else {
				$last_orders_totalprice += $last_orders_products['products_price'] * $last_orders_products['products_quantity'];
			}
			$item = product_to_EMOSItem($last_orders_products['products_id'],$_SESSION['languages_id'], $last_orders_products['products_quantity']);
			$basket[$count] = $item;
			$count++;
		}
		$emos->addEmosBillingPageArray($last_orders['orders_id'],
			$_SESSION['customer_id'],
			sprintf("%0.2f",$last_orders_totalprice),
			$last_orders['customers_country'],
			$last_orders['customers_postcode'],
			$last_orders['customers_city']);
		$emos->addEmosBasketPageArray($basket);
		break;

	default:
		break;
}
// output

echo $emos->toString();

echo "\n<!-- Econda-Monitor -->\n";