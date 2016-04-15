<?php
/* --------------------------------------------------------------
  wcp.php 2014-07-17 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */
/**
    Shop System Plugins - Terms of use

    This terms of use regulates warranty and liability between Wirecard
    Central Eastern Europe (subsequently referred to as WDCEE) and it's
    contractual partners (subsequently referred to as customer or customers)
    which are related to the use of plugins provided by WDCEE.

    The Plugin is provided by WDCEE free of charge for it's customers and
    must be used for the purpose of WDCEE's payment platform integration
    only. It explicitly is not part of the general contract between WDCEE
    and it's customer. The plugin has successfully been tested under
    specific circumstances which are defined as the shopsystem's standard
    configuration (vendor's delivery state). The Customer is responsible for
    testing the plugin's functionality before putting it into production
    enviroment.
    The customer uses the plugin at own risk. WDCEE does not guarantee it's
    full functionality neither does WDCEE assume liability for any
    disadvantage related to the use of this plugin. By installing the plugin
    into the shopsystem the customer agrees to the terms of use. Please do
    not use this plugin if you do not agree to the terms of use!

	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]

*/

define('TABLE_PAYMENT_WCP', 'payment_wirecard_checkout_page');
define('FORM_URL', 'https://checkout.wirecard.com/page/init.php');
define('WCP_PLUGIN_VERSION', '1.4.0');
define('MODULE_PAYMENT_WCP_WINDOW_NAME', 'wirecardCheckoutPageIFrame');


class wcp_core {
    var $code, $title, $description, $enabled;

    var $process_cart_id;
    /// @note will be overwritten by child classes
    var $payment_type = 'SELECT';

    var $has_minmax_amount = false;

    /// @brief initialize wirecard_checkout_page module
    function init() {
        $this->code        = get_class($this);
        $c = strtoupper($this->code);
        $this->title       = wcp_core::constant("MODULE_PAYMENT_{$c}_TEXT_TITLE");
        $this->description = wcp_core::constant("MODULE_PAYMENT_{$c}_TEXT_DESCRIPTION");
        $this->info        = wcp_core::constant("MODULE_PAYMENT_{$c}_TEXT_INFO");

        $this->min_order   = wcp_core::constant("MODULE_PAYMENT_{$c}_MIN_ORDER");
        $this->sort_order  = wcp_core::constant("MODULE_PAYMENT_{$c}_SORT_ORDER");
        $this->enabled     = ((wcp_core::constant("MODULE_PAYMENT_{$c}_STATUS") == 'True') ? true : false);
    }

    function constant($name) {
      return (defined($name)) ? constant($name) : NULL;
    }

    /// @brief collect data and create a array with wirecard checkout page infos
    function get_order_post_variables_array() {
        global $order, $xtPrice, $insert_id;
        $c = strtoupper($this->code);
        $pluginVersion = base64_encode('GambioGX; '.PROJECT_VERSION.'; ; webteam/gambio; '. WCP_PLUGIN_VERSION);
        // check language
        $result = xtc_db_query("SELECT code FROM languages WHERE languages_id = '".(int)$_SESSION['languages_id']."'");
        list($lang_code) = mysql_fetch_row($result);

		// set total price
		if(isset($order->info['pp_total']))
		{
			$total = $order->info['pp_total'];
		}
		else
		{
			$total = $order->info['total'];
		}
        
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1)
            $total = $total + $order->info['tax'];

        $consumerID = $_SESSION['customer_id'];
        $deliveryInformation = $order->delivery;
        
        if($deliveryInformation['country_iso_2'] == 'US' || $deliveryInformation['country_iso_2'] == 'CA')
        {
            $deliveryState = $this->_getZoneCodeByName($deliveryInformation['state']);
        }
        else
        {
            $deliveryState = $deliveryInformation['state'];
        }

        $billingInformation  = $order->billing;
        if($billingInformation['country_iso_2'] == 'US' || $billingInformation['country_iso_2'] == 'CA')
        {
            $billingState = $this->_getZoneCodeByName($billingInformation['state']);
        }
        else
        {
            $billingState = $billingInformation['state'];
        }

        $sql = 'SELECT customers_dob, customers_fax FROM ' . TABLE_CUSTOMERS .' WHERE customers_id="'.(int)$consumerID.'" LIMIT 1;';
        $result = xtc_db_query($sql);
        $consumerInformation = mysql_fetch_assoc($result);
        if($consumerInformation['customers_dob'] != '0000-00-00 00:00:00')
        {
            $consumerBirthDateTimestamp = strtotime($consumerInformation['customers_dob']);
            $consumerBirthDate = date('Y-m-d', $consumerBirthDateTimestamp);
        }
        else
        {
            $consumerBirthDate = '';
        }
        $orderDescription = $billingInformation['firstname'].' '.$billingInformation['lastname'].' - '.$order->customer['email_address'];
        $customerStatement = wcp_core::constant("MODULE_PAYMENT_{$c}_STATEMENT") . ' #' . $insert_id;
        $post_variables = array(
            'customerId'            => wcp_core::constant("MODULE_PAYMENT_{$c}_CUSTOMER_ID"),
            'order_id'              => $insert_id,
            'amount'                => round($total, $xtPrice->get_decimal_places($_SESSION['currency'])),
            'currency'              => $_SESSION['currency'],
            'paymentType'           => $this->payment_type,
            'paymentCode'           => $this->code,
            'language'              => $lang_code,
            'confirmLanguage'       => $_SESSION['language'],
            'confirmLanguageId'     => $_SESSION['language_id'],
            'orderDescription'      => $orderDescription,
            'customerStatement'     => $customerStatement,
            'orderReference'        => $insert_id,
            'displayText'           => wcp_core::constant("MODULE_PAYMENT_{$c}_DISPLAY_TEXT"),

            'successURL'            => xtc_href_link('callback/wirecard/checkout_page_callback.php', '', 'SSL'),
            'cancelURL'             => xtc_href_link('callback/wirecard/checkout_page_callback.php', 'cancel=1', 'SSL'),
            'pendingURL'            => xtc_href_link('callback/wirecard/checkout_page_callback.php', 'pending=1', 'SSL'),
            'failureURL'            => xtc_href_link('callback/wirecard/checkout_page_callback.php', 'failure=1', 'SSL'),
            'serviceURL'            => wcp_core::constant("MODULE_PAYMENT_{$c}_SERVICE_URL"),
            'confirmURL'            => xtc_href_link('callback/wirecard/checkout_page_confirm.php', '', 'SSL', false),
            'windowName'            => MODULE_PAYMENT_WCP_WINDOW_NAME,
            'pluginVersion'         => $pluginVersion,

            'consumerShippingFirstName'    => $deliveryInformation['firstname'],
            'consumerShippingLastName'     => $deliveryInformation['lastname'],
            'consumerShippingAddress1'     => $deliveryInformation['street_address'],
            'consumerShippingAddress2'     => $deliveryInformation['suburb'],
            'consumerShippingCity'         => $deliveryInformation['city'],
            'consumerShippingZipCode'      => $deliveryInformation['postcode'],
            'consumerShippingState'        => $deliveryState,
            'consumerShippingCountry'      => $deliveryInformation['country_iso_2'],
            'consumerShippingPhone'        => $order->customer['telephone'],
            'consumerBillingFirstName'     => $billingInformation['firstname'],
            'consumerBillingLastName'      => $billingInformation['lastname'],
            'consumerBillingAddress1'      => $billingInformation['street_address'],
            'consumerBillingAddress2'      => $billingInformation['suburb'],
            'consumerBillingCity'          => $billingInformation['city'],
            'consumerBillingZipCode'       => $billingInformation['postcode'],
            'consumerBillingState'         => $billingState,
            'consumerBillingCountry'       => $billingInformation['country_iso_2'],
            'consumerBillingPhone'         => $order->customer['telephone'],
            'consumerEmail'                => $order->customer['email_address'],
            'duplicateRequestCheck'        => 'Yes',
        );
        if($consumerBirthDate != '')
        {
            $post_variables['consumerBirthDate'] = $consumerBirthDate;
        }
        
        if($consumerInformation['customers_fax'] != '' && $consumerInformation['customers_fax'] != null)
        {
            $post_variables['consumerShippingFax'] = $consumerInformation['customers_fax'];
            $post_variables['consumerBillingFax'] = $consumerInformation['customers_fax'];
        }

        // set shop id if isset
        if(constant("MODULE_PAYMENT_{$c}_SHOP_ID"))
            $post_variables['shopId'] = wcp_core::constant("MODULE_PAYMENT_{$c}_SHOP_ID");

        // set shop logo if desired
        if(constant("MODULE_PAYMENT_{$c}_LOGO_INCLUDE") == 'True') {
          require_once (DIR_FS_CATALOG . 'gm/classes/GMLogoManager.php');
          $gm_logo = new GMLogoManager("gm_logo_shop");
          $post_variables['imageURL'] = $gm_logo->logo_path . $gm_logo->logo_file;
        }

        // create fingerprint
        $requestFingerprintOrder = 'secret,';
        $requestFingerprintSeed  = wcp_core::constant("MODULE_PAYMENT_{$c}_PRESHARED_KEY");
        foreach($post_variables as $key => $value) {
            $requestFingerprintOrder .= $key . ',';
            $requestFingerprintSeed  .= trim($value);
        }
        $requestFingerprintOrder .= 'requestFingerprintOrder';
        $requestFingerprintSeed .= $requestFingerprintOrder;
        $requestfingerprint = md5($requestFingerprintSeed);
        $post_variables['requestFingerprintOrder'] = $requestFingerprintOrder;
        $post_variables['requestFingerprint']      = $requestfingerprint;
        return $post_variables;
    }

    /// @brief nothing to do for update_status
    function update_status() {
        return true;
    }

    /// @brief decorate process button
    function process_button() {
    }

    /// @brief nothing to do for before_process
    function before_process() {
        return true;
    }

    /// @brief nothing to do for update_status
    function payment_action() {
        return true;
    }

    /// @brief finalize payment after order is created
    function after_process() 
    {
        global $coo_template_control;
        $c = strtoupper($this->code);
        $useIFrame = wcp_core::constant("MODULE_PAYMENT_{$c}_USE_IFRAME");
        $timeout = wcp_core::constant("MODULE_PAYMENT_{$c}_REDIRECT_TIMEOUT_SECOUNDS")*1000;
        $disableTimeout = $timeout-50;
        $reEnableTimeout = $timeout*5;
        // redirect
        $process_form = '<form name="wcp_process_form" id="wcp_process_form" method="POST" action="'.(FORM_URL).'" >';
        foreach($this->get_order_post_variables_array() as $key => $value)
          $process_form .= xtc_draw_hidden_field($key, $value);
        $process_js = '<script type="text/javascript">
                            setTimeout("document.getElementById(\"wirecardCheckoutPageCheckoutButton\").disabled=true;",'. $disableTimeout .');
                            setTimeout("document.wcp_process_form.submit();",'.$timeout.');
                            setTimeout("document.getElementById(\"wirecardCheckoutPageCheckoutButton\").disabled=false;",'. $reEnableTimeout .');
                       </script>';
        $translation = array(
            'title'   => wcp_core::constant("MODULE_PAYMENT_{$c}_CHECKOUT_TITLE"),
            'header'  => wcp_core::constant("MODULE_PAYMENT_{$c}_CHECKOUT_HEADER"),
            'content' => wcp_core::constant("MODULE_PAYMENT_{$c}_CHECKOUT_CONTENT")
          );
        $_SESSION['wirecard_checkout_page']['useIFrame'] = $useIFrame;
        $_SESSION['wirecard_checkout_page']['process_form'] = $process_form;
        $_SESSION['wirecard_checkout_page']['process_js'] = $process_js;
        $_SESSION['wirecard_checkout_page']['translation'] = $translation;
        include('checkout_wirecard_checkout_page.php');
        die();
    }

    /// @brief set info for order-payment-module selection
    function selection() {
        if (!$this->_preCheck())
            return false;
        return array ('id' => $this->code, 'module' => $this->title, 'description' => $this->info);
    }
    function javascript_validation() {
        return false;
    }
    function pre_confirmation_check() {
        return false;
    }
    function confirmation() {
        return false;
    }
    function get_error() {
        return false;
    }

    /**
     * @return bool
     */
    function _preCheck()
    {
        return true;
    }

    /// @brief check module status
    function check() {
        if (!isset ($this->_check)) {
            $c = strtoupper($this->code);
            $check_query = xtc_db_query("SELECT configuration_value FROM ".TABLE_CONFIGURATION." WHERE configuration_key='MODULE_PAYMENT_{$c}_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    }

    /// @brief install module
    function install() {
        $cg_id = 6; // represents Modul Configuration by default
        $q = "INSERT INTO ".TABLE_CONFIGURATION."
                (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added)
              VALUES ";
        $s = 1; // represents sort-order at displayed configuration
        $serviceUrl = xtc_href_link('shop_content.php', 'coID=7', 'SSL');
        $selection = "'gm_cfg_select_option(array(\'True\', \'False\'), '";
        $c = strtoupper($this->code);
        $q .= "
            ('MODULE_PAYMENT_{$c}_STATUS',                  'True',       '$cg_id', '".$s++."', $selection, now()),
            ('MODULE_PAYMENT_{$c}_PRESHARED_KEY',           '',           '$cg_id', '".$s++."', '',         now()),
            ('MODULE_PAYMENT_{$c}_CUSTOMER_ID',             '',           '$cg_id', '".$s++."', '',         now()),
            ('MODULE_PAYMENT_{$c}_LOGO_INCLUDE',            'True',       '$cg_id', '".$s++."', $selection, now()),
            ('MODULE_PAYMENT_{$c}_SHOP_ID',                 '',           '$cg_id', '".$s++."', '',         now()),
            ('MODULE_PAYMENT_{$c}_SERVICE_URL',             '".$serviceUrl."',           '$cg_id', '".$s++."', '',         now()),
            ('MODULE_PAYMENT_{$c}_STATEMENT',               '',           '$cg_id', '".$s++."', '',         now()),
            ('MODULE_PAYMENT_{$c}_DISPLAY_TEXT',            '',           '$cg_id', '".$s++."', '',         now()),
            ('MODULE_PAYMENT_{$c}_SORT_ORDER',              '0',          '$cg_id', '".$s++."', '',         now()),
            ('MODULE_PAYMENT_{$c}_ALLOWED',                 '',           '$cg_id', '".$s++."', '',         now()),
            ('MODULE_PAYMENT_{$c}_USE_IFRAME',              'FALSE',      '$cg_id', '".$s++."', $selection, now()) ";

        if ($this->has_minmax_amount)
        {
            $q .= ",
                ('MODULE_PAYMENT_{$c}_MIN_AMOUNT',          '100',          '$cg_id', '".$s++."', '',         now()),
                ('MODULE_PAYMENT_{$c}_MAX_AMOUNT',          '1000',         '$cg_id', '".$s++."', '',         now()) ";
        }
        xtc_db_query($q);

        /// @TODO use for logging
        // create table for saving transaction data and logging
        $q = "CREATE TABLE IF NOT EXISTS ".TABLE_PAYMENT_WCP."
          (id INT(11) NOT NULL AUTO_INCREMENT,
           orders_id INT(11) NOT NULL,
           response TEXT NOT NULL,
           created_at TIMESTAMP NOT NULL DEFAULT now(),
           PRIMARY KEY (id))";
        xtc_db_query($q);
    }

    function remove() {
        xtc_db_query("DELETE FROM ".TABLE_CONFIGURATION." WHERE configuration_key IN ('".implode("', '", $this->keys())."')");
    }

    /**
      * @brief define module configuration keys
      * MODULE_PAYMENT_MODULENAME_STATUS ... activated true/false
      * MODULE_PAYMENT_MODULENAME_PRESHARED_KEY ... secret key
      * MODULE_PAYMENT_MODULENAME_CUSTOMER_ID ... Wirecard CEE customer id
      * MODULE_PAYMENT_MODULENAME_STORE_LOGO_INCLUDE ... use shop logo
      * MODULE_PAYMENT_MODULENAME_SHOP_ID ... Wirecard CEE shop id
      * MODULE_PAYMENT_MODULENAME_SERVICE_URL ... shop support-page url
      * MODULE_PAYMENT_MODULENAME_STATEMENT ... shop info statment
      * MODULE_PAYMENT_MODULENAME_DISPLAY_TEXT ... shop info text
      *
      * following are Gambio-Defaults:
      * MODULE_PAYMENT_MODULENAME_SORT_ORDER ... sort order at payment types selection
      * MODULE_PAYMENT_MODULENAME_ALLOWED ... allowed for which zones
      **/
    function keys() {
        $c = strtoupper($this->code);
        $keys =  array("MODULE_PAYMENT_{$c}_STATUS", "MODULE_PAYMENT_{$c}_PRESHARED_KEY", "MODULE_PAYMENT_{$c}_CUSTOMER_ID",
                    "MODULE_PAYMENT_{$c}_LOGO_INCLUDE", "MODULE_PAYMENT_{$c}_SHOP_ID", "MODULE_PAYMENT_{$c}_SERVICE_URL",
                    "MODULE_PAYMENT_{$c}_STATEMENT", "MODULE_PAYMENT_{$c}_DISPLAY_TEXT",
                    "MODULE_PAYMENT_{$c}_SORT_ORDER", "MODULE_PAYMENT_{$c}_USE_IFRAME");

        if ($this->has_minmax_amount)
        {
            $keys[] = "MODULE_PAYMENT_{$c}_MIN_AMOUNT";
            $keys[] = "MODULE_PAYMENT_{$c}_MAX_AMOUNT";
        }

        return $keys;
    }

    function _getZoneCodeByName($zoneName)
    {
        $sql = 'SELECT zone_code FROM ' . TABLE_ZONES . ' WHERE zone_name=\'' .xtc_db_input($zoneName) .'\' LIMIT 1;';
        $result = xtc_db_query($sql);
        $resultRow = mysql_fetch_row($result);
        return $resultRow[0];
    }
}