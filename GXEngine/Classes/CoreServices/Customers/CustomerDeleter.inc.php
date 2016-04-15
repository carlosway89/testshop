<?php
/* --------------------------------------------------------------
   CustomerDeleter.inc.php 2015-03-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerDeleterInterface');

/**
 * Class CustomerDeleter
 * 
 * This class is used for deleting customer data
 *
 * @category System
 * @package Customers
 * @implements CustomerDeleterInterface
 */
class CustomerDeleter implements CustomerDeleterInterface
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	/**
	 * Constructor of the class CustomerDeleter
	 * 
	 * @param CI_DB_query_builder $dbQueryBuilder
	 */
	public function __construct(CI_DB_query_builder $dbQueryBuilder)
	{
		$this->db = $dbQueryBuilder;
	}

	/**
	 * This method will delete all data of specific customer.
	 *
	 * @param CustomerInterface $customer
	 */
	public function delete(CustomerInterface $customer)
	{
		$customerId = (int)(string)$customer->getId();
		$this->db->delete('address_book', array('customers_id' => $customerId));
		$this->db->delete('admin_access', array('customers_id' => $customerId));
		$this->db->delete('customers', array('customers_id' => $customerId));
		$this->db->delete('customers_basket', array('customers_id' => $customerId));
		$this->db->delete('customers_basket_attributes', array('customers_id' => $customerId));
		$this->db->delete('customers_info', array('customers_info_id' => $customerId));
		$this->db->delete('customers_ip', array('customers_id' => $customerId));
		$this->db->delete('customers_status_history', array('customers_id' => $customerId));
		$this->db->delete('customers_wishlist', array('customers_id' => $customerId));
		$this->db->delete('customers_wishlist_attributes', array('customers_id' => $customerId));
		$this->db->delete('coupon_gv_customer', array('customer_id' => $customerId));
		$this->db->delete('coupon_gv_queue', array('customer_id' => $customerId));
		$this->db->delete('coupon_redeem_track', array('customer_id' => $customerId));
		$this->db->delete('gm_gprint_cart_elements', array('customers_id' => $customerId));
		$this->db->delete('gm_gprint_wishlist_elements', array('customers_id' => $customerId));
		$this->db->delete('products_notifications', array('customers_id' => $customerId));
		$this->db->delete('whos_online', array('customer_id' => $customerId));

		$this->db->update('coupon_redeem_track', array('customer_id' => 0), array('customer_id' => $customerId));
		$this->db->update('gm_gprint_uploads', array('customers_id' => 0), array('customers_id' => $customerId));
		$this->db->update('newsletter_recipients', array('customers_id' => 0), array('customers_id' => $customerId));
		$this->db->update('orders', array('customers_id' => 0), array('customers_id' => $customerId));
		$this->db->update('withdrawals', array('customer_id' => 0), array('customer_id' => $customerId));
	}
}