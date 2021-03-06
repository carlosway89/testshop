<?php
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

require_once(dirname(__FILE__).'/wcp.php');

class wcp_invoice extends wcp_core {
    var $payment_type = 'INVOICE';

    var $has_minmax_amount = true;

    /// @brief initialize wirecard_checkout_page module
    function wcp_invoice() {
        parent::init();
    }

    /**
     * @return bool
     */
    function _preCheck()
    {
        global $order, $customer, $currencies, $xtPrice;

        $c = strtoupper($this->code);
        $consumerID = xtc_session_is_registered('customer_id') ? $_SESSION['customer_id'] : "";

        $currency = $order->info['currency'];
        $total = $order->info['total'];
        $amount = round($xtPrice->xtcCalculateCurrEx($total,$currency), $xtPrice->get_decimal_places($currency));

        $sql = 'SELECT (COUNT(*) > 0) as cnt FROM ' . TABLE_CUSTOMERS . ' WHERE DATEDIFF(NOW(), customers_dob) > 6574 AND customers_id="' . $consumerID . '"';

        $result = mysql_fetch_assoc(xtc_db_query($sql));

        $ageCheck = (bool)$result['cnt'];
        $country_code = $order->billing['country']['iso_code_2'];

        return ($ageCheck &&
            ($amount >= wcp_core::constant("MODULE_PAYMENT_{$c}_MIN_AMOUNT") && $amount <= wcp_core::constant("MODULE_PAYMENT_{$c}_MAX_AMOUNT")) &&
            ($currency == 'EUR') &&
            (in_array($country_code, Array('AT', 'DE', 'CH'))) &&
            ($order->delivery === $order->billing));
    }

}