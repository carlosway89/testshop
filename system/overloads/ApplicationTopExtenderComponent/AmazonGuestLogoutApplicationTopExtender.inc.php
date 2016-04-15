<?php
/* --------------------------------------------------------------
	AmazonGuestLogoutApplicationTopExtender.inc.php 2015-10-08
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class AmazonGuestLogoutApplicationTopExtender extends AmazonGuestLogoutApplicationTopExtender_parent
{
	protected $amazonAdvancedPayment;

	public function proceed()
	{
		parent::proceed();

		$isCheckoutPage =
			strpos(basename($GLOBALS['PHP_SELF']), 'checkout') !== false ||
			strpos(basename($GLOBALS['PHP_SELF']), 'request_port') !== false ||
			strpos(basename($GLOBALS['PHP_SELF']), 'mailhive') !== false ||
			strpos(basename($GLOBALS['PHP_SELF']), 'gm_javascript') !== false;
		$isAmazonGuest = isset($_SESSION['amazonadvpay_guest']) && $_SESSION['amazonadvpay_guest'] == true;

		if($isAmazonGuest && !$isCheckoutPage)
		{
			$this->amazonAdvancedPayment = MainFactory::create_object('AmazonAdvancedPayment');
			$this->amazonAdvancedPayment->log('Amazon guest left checkout, destroying guest account, PHP_SELF = '.$GLOBALS['PHP_SELF']);
			$this->deleteAmazonGuestAccount($_SESSION['customer_id']);

			unset($_SESSION['amazonadvpay_order_ref_id']);
			unset($_SESSION['sendto']);
			unset($_SESSION['billto']);
			unset($_SESSION['payment']);

			if(isset($_SESSION['amazonadvpay_guest']))
			{
				unset($_SESSION['account_type']);
				unset($_SESSION['customer_id']);
				unset($_SESSION['customer_first_name']);
				unset($_SESSION['customer_last_name']);
				unset($_SESSION['customer_default_address_id']);
				unset($_SESSION['customer_country_id']);
				unset($_SESSION['customer_zone_id']);
				unset($_SESSION['customer_vat_id']);
				unset($_SESSION['amazonadvpay_guest']);
				unset($_SESSION['amazonadvpay_logout_guest']);
			}
		}
	}

	protected function deleteAmazonGuestAccount($customers_id)
	{
		$this->amazonAdvancedPayment->delete_amazon_address_book_entries($customers_id);
		xtc_db_query('DELETE FROM customers WHERE customers_id = \''.(int)$customers_id.'\'');
		xtc_db_query('DELETE FROM customers_info WHERE customers_info_id = \''.(int)$customers_id.'\'');
	}
}