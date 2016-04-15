<?php
/* --------------------------------------------------------------
  CheckoutProcessProcess.inc.php 2015-03-02 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(checkout_process.php,v 1.128 2003/05/28); www.oscommerce.com
  (c) 2003	 nextcommerce (checkout_process.php,v 1.30 2003/08/24); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_process.php 1277 2005-10-01 17:02:59Z mz $)

  Released under the GNU General Public License
  ----------------------------------------------------------------------------------------
  Third Party contribution:

  Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

  Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
  http://www.oscommerce.com/community/contributions,282
  Copyright (c) Strider | Strider@oscworks.com
  Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
  Copyright (c) Andre ambidex@gmx.net
  Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once(DIR_WS_CLASSES . 'payment.php');
require_once(DIR_WS_CLASSES . 'order_total.php');
require_once(DIR_FS_CATALOG . 'gm/inc/set_shipping_status.php');

MainFactory::load_class('DataProcessing');

class CheckoutProcessProcess extends CheckoutControl
{
	protected $coo_order;
	protected $coo_order_total;
	protected $coo_payment;
	protected $coo_properties;
	protected $coo_shipping;
	protected $tmp_order;
	protected $tmp_status;
	protected $order_id;
	protected $order_totals_array;

	public function __construct()
	{
		parent::__construct();

		if(isset($GLOBALS['tmp']))
		{
			$this->tmp_order = $GLOBALS['tmp'];
		}
		else
		{
			$this->tmp_order = false;
		}
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['coo_order']          = array('type' => 'object', 'object_type' => 'order');
		$this->validation_rules_array['coo_order_total']    = array('type' => 'object', 'object_type' => 'order_total');
		$this->validation_rules_array['coo_payment']        = array('type' => 'object', 'object_type' => 'payment');
		$this->validation_rules_array['coo_properties']     = array('type' => 'object',	'object_type' => 'PropertiesControl');
		$this->validation_rules_array['coo_shipping']       = array('type' => 'object', 'object_type' => 'shipping');
		$this->validation_rules_array['tmp_order']          = array('type' => 'bool');
		$this->validation_rules_array['tmp_status']         = array('type' => 'int');
		$this->validation_rules_array['order_id']           = array('type' => 'int');
		$this->validation_rules_array['order_totals_array'] = array('type' => 'array');
	}

	public function proceed()
	{
		if($this->check_redirect())
		{
			return true;
		}

		if(isset($_SESSION['credit_covers']))
		{
			$_SESSION['payment'] = ''; //ICW added for CREDIT CLASS
		}

		$this->coo_payment = new payment($_SESSION['payment']);

		// load the selected shipping module
		$this->coo_shipping = new shipping($_SESSION['shipping']);

		$GLOBALS['order'] = new order();
		$this->coo_order = $GLOBALS['order'];

		// load the before_process function from the payment modules
		$this->coo_payment->before_process();

		$GLOBALS['order_total_modules'] = new order_total();
		$this->coo_order_total = $GLOBALS['order_total_modules'];
		$this->order_totals_array = $this->coo_order_total->process();
		$GLOBALS['order_totals'] =& $this->order_totals_array;

		# PropertiesControl Object
		$this->coo_properties = MainFactory::create_object('PropertiesControl');

		// check if tmp order id exists
		if(isset($_SESSION['tmp_oID']) && is_int($_SESSION['tmp_oID']))
		{
			$GLOBALS['tmp'] = false;
			$this->tmp_order =& $GLOBALS['tmp'];
			$GLOBALS['insert_id'] = $_SESSION['tmp_oID'];
			$this->order_id = $GLOBALS['insert_id'];
		}
		else
		{
			// check if tmp order need to be created
			//if (isset ($GLOBALS[$_SESSION['payment']]->form_action_url) && $GLOBALS[$_SESSION['payment']]->tmpOrders) {
			if($GLOBALS[$_SESSION['payment']]->tmpOrders == true)
			{
				$GLOBALS['tmp'] = true;
				$this->tmp_order =& $GLOBALS['tmp'];
				$this->tmp_status = $GLOBALS[$_SESSION['payment']]->tmpStatus;
			}
			else
			{
				$GLOBALS['tmp'] = false;
				$this->tmp_order =& $GLOBALS['tmp'];
				$this->tmp_status = $this->coo_order->info['order_status'];
			}

			$this->save_order();
			$this->save_order_total();
			$this->save_module_data();
			$this->save_order_status_history();
			$this->process_products();
			$this->save_tracking_data();

			// redirect to payment service
			if($this->tmp_order)
			{
				$this->coo_payment->payment_action();
			}
		}

		if($this->tmp_order == false)
		{
			// NEW EMAIL configuration !
			$this->coo_order_total->apply_credit();

			$this->send_order_mail();

			// load the after_process function from the payment modules
			$this->coo_payment->after_process();

			$this->reset();

			$this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL'));
			return true;
		}

		return true;
	}

	public function check_redirect()
	{
		// check if cart items are still in stock
		if($this->check_stock() === false)
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_SHOPPING_CART));
			return true;
		}

		// if the customer is not logged on, redirect them to the login page
		if(!isset($_SESSION['customer_id']))
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
			return true;
		}

		if($_SESSION['customers_status']['customers_status_show_price'] != '1')
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_DEFAULT, '', 'NONSSL'));
			return true;
		}

		if(!isset($_SESSION['sendto']))
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
			return true;
		}

		if((xtc_not_null(MODULE_PAYMENT_INSTALLED)) && (!isset($_SESSION['payment'])) && (!isset($_SESSION['credit_covers'])))
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
			return true;
		}

		// avoid hack attempts during the checkout procedure by checking the internal cartID
		if(isset($_SESSION['cart']->cartID) && isset($_SESSION['cartID']) && $_SESSION['cart']->cartID != $_SESSION['cartID'])
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
			return true;
		}

		return false;
	}

	public function save_order()
	{
		if(strtolower(CC_ENC) == 'true')
		{
			$t_cc_number = $this->coo_order->info['cc_number'];
			$this->coo_order->info['cc_number'] = changedatain($t_cc_number, CC_KEYCHAIN);
		}

		if($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1)
		{
			$t_discount = $_SESSION['customers_status']['customers_status_ot_discount'];
		}
		else
		{
			$t_discount = '0.00';
		}

		if(($this->v_data_array['POST']['gm_log_ip'] == 'save' && gm_get_conf("GM_LOG_IP") == '1') || (gm_get_conf("GM_SHOW_IP") == '1' && gm_get_conf("GM_LOG_IP") == '1'))
		{
			if($_SERVER["HTTP_X_FORWARDED_FOR"])
			{
				$t_customer_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			}
			else
			{
				$t_customer_ip = $_SERVER["REMOTE_ADDR"];
			}
		}

		if(isset($this->v_data_array['POST']['comments']) && empty($this->coo_order->info['comments']))
		{
			$this->coo_order->info['comments'] = gm_prepare_string($this->v_data_array['POST']['comments'], true);
		}
		elseif(isset($_SESSION['comments']) && empty($this->coo_order->info['comments']))
		{
			$this->coo_order->info['comments'] = $_SESSION['comments'];
		}

		$t_sql_data_array = array('customers_id' => $_SESSION['customer_id'],
								'customers_gender' => $this->coo_order->customer['gender'],
								'customers_name' => $this->coo_order->customer['firstname'] . ' ' . $this->coo_order->customer['lastname'],
								'customers_firstname' => $this->coo_order->customer['firstname'],
								'customers_lastname' => $this->coo_order->customer['lastname'],
								'customers_cid' => $this->coo_order->customer['csID'],
								'customers_vat_id' => $_SESSION['customer_vat_id'],
								'customers_company' => $this->coo_order->customer['company'],
								'customers_status' => $_SESSION['customers_status']['customers_status_id'],
								'customers_status_name' => $_SESSION['customers_status']['customers_status_name'],
								'customers_status_image' => $_SESSION['customers_status']['customers_status_image'],
								'customers_status_discount' => $t_discount,
								'customers_street_address' => $this->coo_order->customer['street_address'],
								'customers_suburb' => $this->coo_order->customer['suburb'],
								'customers_city' => $this->coo_order->customer['city'],
								'customers_postcode' => $this->coo_order->customer['postcode'],
								'customers_state' => $this->coo_order->customer['state'],
								'customers_country' => $this->coo_order->customer['country']['title'],
								'customers_telephone' => $this->coo_order->customer['telephone'],
								'customers_email_address' => $this->coo_order->customer['email_address'],
								'customers_address_format_id' => $this->coo_order->customer['format_id'],
								'delivery_name' => $this->coo_order->delivery['firstname'] . ' ' . $this->coo_order->delivery['lastname'],
								'delivery_firstname' => $this->coo_order->delivery['firstname'],
								'delivery_lastname' => $this->coo_order->delivery['lastname'],
								'delivery_gender' => $this->coo_order->delivery['gender'],
								'delivery_company' => $this->coo_order->delivery['company'],
								'delivery_street_address' => $this->coo_order->delivery['street_address'],
								'delivery_suburb' => $this->coo_order->delivery['suburb'],
								'delivery_city' => $this->coo_order->delivery['city'],
								'delivery_postcode' => $this->coo_order->delivery['postcode'],
								'delivery_state' => $this->coo_order->delivery['state'],
								'delivery_country' => $this->coo_order->delivery['country']['title'],
								'delivery_country_iso_code_2' => $this->coo_order->delivery['country']['iso_code_2'],
								'delivery_address_format_id' => $this->coo_order->delivery['format_id'],
								'billing_name' => $this->coo_order->billing['firstname'] . ' ' . $this->coo_order->billing['lastname'],
								'billing_firstname' => $this->coo_order->billing['firstname'],
								'billing_lastname' => $this->coo_order->billing['lastname'],
								'billing_gender' => $this->coo_order->billing['gender'],
								'billing_company' => $this->coo_order->billing['company'],
								'billing_street_address' => $this->coo_order->billing['street_address'],
								'billing_suburb' => $this->coo_order->billing['suburb'],
								'billing_city' => $this->coo_order->billing['city'],
								'billing_postcode' => $this->coo_order->billing['postcode'],
								'billing_state' => $this->coo_order->billing['state'],
								'billing_country' => $this->coo_order->billing['country']['title'],
								'billing_country_iso_code_2' => $this->coo_order->billing['country']['iso_code_2'],
								'billing_address_format_id' => $this->coo_order->billing['format_id'],
								'payment_method' => $this->coo_order->info['payment_method'],
								'payment_class' => $this->coo_order->info['payment_class'],
								'shipping_method' => $this->coo_order->info['shipping_method'],
								'shipping_class' => $this->coo_order->info['shipping_class'],
								'cc_type' => $this->coo_order->info['cc_type'],
								'cc_owner' => $this->coo_order->info['cc_owner'],
								'cc_number' => $this->coo_order->info['cc_number'],
								'cc_expires' => $this->coo_order->info['cc_expires'],
								'cc_start' => $this->coo_order->info['cc_start'],
								'cc_cvv' => $this->coo_order->info['cc_cvv'],
								'cc_issue' => $this->coo_order->info['cc_issue'],
								'date_purchased' => date('Y-m-d H:i:s'),
								'orders_status' => $this->tmp_status,
								'currency' => $this->coo_order->info['currency'],
								'currency_value' => $this->coo_order->info['currency_value'],
								'customers_ip' => $t_customer_ip,
								'language' => $_SESSION['language'],
								'comments' => $this->coo_order->info['comments']);

		$t_sql_data_array['orders_hash'] = md5(time() + mt_rand());

		$t_sql_data_array['abandonment_download'] = 0;
		$t_sql_data_array['abandonment_service'] = 0;

		if(isset($_SESSION['abandonment_download']) && $_SESSION['abandonment_download'] == 'true')
		{
			$t_sql_data_array['abandonment_download'] = 1;
		}
		if(isset($_SESSION['abandonment_service']) && $_SESSION['abandonment_service'] == 'true')
		{
			$t_sql_data_array['abandonment_service'] = 1;
		}
		
		$this->add_order_data($t_sql_data_array);

		$this->wrapped_db_perform(__FUNCTION__, TABLE_ORDERS, $t_sql_data_array);
		$GLOBALS['insert_id'] = xtc_db_insert_id();
		$this->order_id = $GLOBALS['insert_id'];
		$_SESSION['tmp_oID'] = $this->order_id;
	}

	public function save_order_total()
	{
		for($i = 0, $n = sizeof($this->order_totals_array); $i < $n; $i ++)
		{
			$t_sql_data_array = array('orders_id' => (int)$this->order_id,
									'title' => $this->order_totals_array[$i]['title'],
									'text' => $this->order_totals_array[$i]['text'],
									'value' => $this->order_totals_array[$i]['value'],
									'class' => $this->order_totals_array[$i]['code'],
									'sort_order' => $this->order_totals_array[$i]['sort_order']);

			$this->add_order_total_data($t_sql_data_array, $this->order_totals_array[$i]);

			$this->wrapped_db_perform(__FUNCTION__, TABLE_ORDERS_TOTAL, $t_sql_data_array);
		}
	}

	public function save_module_data()
	{
		/* magnalister v1.0.1 */
		if(function_exists('magnaExecute'))
		{
			magnaExecute('magnaInsertOrderDetails', array('oID' => (int)$this->order_id), array('order_details.php'));
		}

		if(function_exists('magnaExecute'))
		{
			magnaExecute('magnaInventoryUpdate', array('action' => 'inventoryUpdateOrder'), array('inventoryUpdate.php'));
		}
		/* END magnalister */
	}

	public function save_order_status_history()
	{
		$t_customer_notification = (SEND_EMAILS == 'true') ? '1' : '0';
		$t_sql_data_array = array('orders_id' => (int)$this->order_id,
								'orders_status_id' => $this->coo_order->info['order_status'],
								'date_added' => 'now()',
								'customer_notified' => $t_customer_notification,
								'comments' => $this->coo_order->info['comments']);

		$this->add_order_status_history_data($t_sql_data_array);

		$this->wrapped_db_perform(__FUNCTION__, TABLE_ORDERS_STATUS_HISTORY, $t_sql_data_array);
	}

	public function process_products()
	{
		// initialized for the email confirmation
		$GLOBALS['products_ordered'] = '';
		$GLOBALS['products_ordered_html'] = '';
		$GLOBALS['subtotal'] = 0;
		$GLOBALS['total_tax'] = 0;

		for($i = 0, $n = sizeof($this->coo_order->products); $i < $n; $i ++)
		{
			// check if combis exists
			$t_combis_id = $this->coo_properties->extract_combis_id($this->coo_order->products[$i]['id']);

			$this->update_stock($this->coo_order->products[$i], $t_combis_id);

			$t_order_products_id = $this->save_order_product( $this->coo_order->products[$i]);

			// GX-Customizer
			$this->save_customizer_data($t_order_products_id, $this->coo_order->products[$i]['id']);

			$this->save_order_product_quantity_unit($t_order_products_id, $this->coo_order->products[$i]);

			# save selected properties_combi in product
			$this->save_property_data($this->coo_order->products[$i], $t_order_products_id, $t_combis_id);

			$this->update_special($this->coo_order->products[$i]);

			$this->coo_order_total->update_credit_account($i); // GV Code ICW ADDED FOR CREDIT CLASS SYSTEM

			$this->process_attributes($t_order_products_id, $this->coo_order->products[$i]);

			$GLOBALS['total_weight'] += ($this->coo_order->products[$i]['qty'] * $this->coo_order->products[$i]['weight']);
		}
	}

	public function update_stock($p_product_array, $p_combis_id)
	{
		$updateProductShippingStatus = false;
		
		$t_products_sql_data_array = array();

		// Stock Update - Joao Correia
		if(STOCK_LIMITED == 'true')
		{
			if(DOWNLOAD_ENABLED == 'true')
			{
				$t_stock_query = "SELECT
												p.products_quantity,
												pad.products_attributes_filename
											FROM
												" . TABLE_PRODUCTS . " p
												LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " pa ON p.products_id=pa.products_id
												LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad ON pa.products_attributes_id=pad.products_attributes_id
											WHERE p.products_id = '" . xtc_get_prid($p_product_array['id']) . "'";

				// Will work with only one option for downloadable products
				// otherwise, we have to build the query dynamically with a loop
				$t_product_attributes_array = $p_product_array['attributes'];

				if(is_array($t_product_attributes_array))
				{
					$t_stock_query = "SELECT
													p.products_quantity,
													pad.products_attributes_filename
												FROM
													" . TABLE_PRODUCTS . " p
													LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " pa ON (p.products_id=pa.products_id AND pa.options_id = '" . (int)$t_product_attributes_array[0]['option_id'] . "' AND pa.options_values_id = '" . (int)$t_product_attributes_array[0]['value_id'] . "')
													LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad ON pa.products_attributes_id=pad.products_attributes_id
												WHERE p.products_id = '" . xtc_get_prid($p_product_array['id']) . "'";
				}
				$t_stock_result = xtc_db_query($t_stock_query, "db_link", false);
			}
			else
			{
				$t_stock_result = xtc_db_query("SELECT products_quantity
														FROM " . TABLE_PRODUCTS . "
														WHERE products_id = '" . xtc_get_prid($p_product_array['id']) . "'", "db_link", false);
			}

			if(xtc_db_num_rows($t_stock_result) > 0)
			{
				if(empty($p_combis_id) == false)
				{
					$coo_combis_admin_control = MainFactory::create_object("PropertiesCombisAdminControl");
					$t_use_combis_quantity = $coo_combis_admin_control->get_use_properties_combis_quantity(xtc_get_prid($p_product_array['id']));
				}
				else
				{
					$t_use_combis_quantity = 0;
				}

				$t_stock_values_array = xtc_db_fetch_array($t_stock_result);

				// do not decrement quantities if products_attributes_filename exists
				if(!$t_stock_values_array['products_attributes_filename'] &&
				   ((empty($p_combis_id) && STOCK_CHECK == 'true') ||
					(!empty($p_combis_id) && (($t_use_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK != 'true') || $t_use_combis_quantity == 1))))
				{
					$t_stock_left = $t_stock_values_array['products_quantity'] - $p_product_array['qty'];

					$t_products_sql_data_array['products_quantity'] = $t_stock_left;

					$updateProductShippingStatus = true;
				}
				else
				{
					$t_stock_left = $t_stock_values_array['products_quantity'];
				}

				if(($t_stock_left <= 0) && (STOCK_ALLOW_CHECKOUT == 'false') && GM_SET_OUT_OF_STOCK_PRODUCTS == 'true')
				{
					if(!empty($p_combis_id) && (($t_use_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'true') || $t_use_combis_quantity == 2))
					{
						$t_available_combi_exists = $this->coo_properties->available_combi_exists((int)xtc_get_prid($p_product_array['id']), $p_combis_id);

						if($t_available_combi_exists == false)
						{
							$t_products_sql_data_array['products_status'] = '0';
						}
					}
					else if(empty($p_combis_id) || $t_use_combis_quantity == 4)
					{
						$t_products_sql_data_array['products_status'] = '0';
					}
				}

				$t_only_combi_check = !empty($p_combis_id) && (($t_use_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'true') || $t_use_combis_quantity == 2);
				$t_restock_level_reached = $t_stock_left <= STOCK_REORDER_LEVEL;
				
				// stock_notifier
				if(SEND_EMAILS == 'true' && STOCK_CHECK == 'true' && $t_restock_level_reached && (!$t_only_combi_check || empty($p_combis_id)))
				{
					$t_products_name_query = xtc_db_query("SELECT products_name
																	FROM products_description
																	WHERE
																		products_id = '" . xtc_get_prid($p_product_array['id']) . "' AND
																		language_id = '" . $_SESSION['languages_id'] . "'");
					$t_product_result = mysql_fetch_array($t_products_name_query);

					$t_subject = GM_OUT_OF_STOCK_NOTIFY_TEXT . ' ' . $t_product_result['products_name'];
					$t_body =	$t_product_result['products_name'] . "\n" .
								$p_product_array['model'] . "\n" .
								GM_OUT_OF_STOCK_NOTIFY_TEXT . ': ' . (double)$t_stock_left . "\n" .
								HTTP_SERVER . DIR_WS_CATALOG . 'product_info.php?info=p' . xtc_get_prid($p_product_array['id']) . "\n" .
								HTTP_SERVER . DIR_WS_CATALOG . 'admin/categories.php?pID=' . xtc_get_prid($p_product_array['id']) . '&action=new_product';

					// send mail
					$this->send_mail($t_subject, $t_body);
				}
				// stock_notifier
			}
		}

		// Update products_ordered (for bestsellers list)
		$t_products_sql_data_array['products_ordered'] = 'products_ordered + ' . (double)$p_product_array['qty'];

		$this->update_product($t_products_sql_data_array, xtc_get_prid($p_product_array['id']));

		if($updateProductShippingStatus)
		{
			// set products_shippingtime:
			set_shipping_status($p_product_array['id']);
		}
	}

	public function update_product($p_products_sql_data_array, $p_products_id)
	{
		$this->add_product_data($p_products_sql_data_array, $p_products_id);

		$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS, $p_products_sql_data_array, 'update', 'products_id = "' . (int)$p_products_id . '"', 'db_link', false);
	}

	public function send_mail($p_subject, $p_body)
	{
		return xtc_php_mail(STORE_OWNER_EMAIL_ADDRESS, STORE_NAME, STORE_OWNER_EMAIL_ADDRESS, STORE_NAME, '', STORE_OWNER_EMAIL_ADDRESS, STORE_NAME, '', '', $p_subject, nl2br(htmlentities_wrapper($p_body)), $p_body);
	}

	public function save_order_product($p_product_array)
	{
		$coo_properties = MainFactory::create_object('PropertiesControl');
		$t_combis_id = $coo_properties->extract_combis_id($p_product_array['id']);
		$t_shipping_time = $p_product_array['shipping_time'];
		
		if(empty($t_combis_id) == false)
		{
			$t_query = 'SELECT use_properties_combis_shipping_time FROM products WHERE products_id = "' . $p_product_array['id'] . '"';
			$t_result = xtc_db_query($t_query);
			$t_row = xtc_db_fetch_array($t_result);
			if($t_row['use_properties_combis_shipping_time'] == '1')
			{
				require_once(DIR_FS_CATALOG . 'includes/classes/main.php');
				$coo_main = new main();
				$t_query = 'SELECT combi_shipping_status_id FROM products_properties_combis WHERE products_properties_combis_id = "' . $t_combis_id . '"';
				$t_result = xtc_db_query($t_query);
				$t_row = xtc_db_fetch_array($t_result);
				$t_shipping_time = $coo_main->getShippingStatusName($t_row['combi_shipping_status_id']);
			}
		}
		
		$t_sql_data_array = array('orders_id' => (int)$this->order_id,
								'products_id' => xtc_get_prid($p_product_array['id']),
								'products_model' => $p_product_array['model'],
								'products_name' => $p_product_array['name'],
								'products_shipping_time' => $t_shipping_time,
								'products_price' => $p_product_array['price'],
								'final_price' => $p_product_array['final_price'],
								'products_tax' => $p_product_array['tax'],
								'products_discount_made' => $p_product_array['discount_allowed'],
								'products_quantity' => $p_product_array['qty'],
								'allow_tax' => $_SESSION['customers_status']['customers_status_show_price_tax'],
								'product_type' => $p_product_array['product_type'],
								'checkout_information' => $p_product_array['checkout_information']);

		$this->add_order_product_data($t_sql_data_array, $p_product_array);

		$this->wrapped_db_perform(__FUNCTION__, TABLE_ORDERS_PRODUCTS, $t_sql_data_array);
		$t_order_products_id = xtc_db_insert_id();

		return $t_order_products_id;
	}

	public function save_customizer_data($t_order_products_id, $p_cart_products_id)
	{
		$coo_gm_gprint_order_manager = new GMGPrintOrderManager();
		$coo_gm_gprint_order_manager->save($p_cart_products_id, $t_order_products_id);
	}

	public function save_order_product_quantity_unit($t_order_products_id, $p_products_array)
	{
		if(!empty($p_products_array['quantity_unit_id']))
		{
			$t_sql_data_array = array();
			$t_sql_data_array['orders_products_id'] = (int)$t_order_products_id;
			$t_sql_data_array['quantity_unit_id'] = (int)$p_products_array['quantity_unit_id'];
			$t_sql_data_array['unit_name'] = $p_products_array['unit_name'];

			$this->add_order_product_quantity_data($t_sql_data_array, $p_products_array);

			$this->wrapped_db_perform(__FUNCTION__, 'orders_products_quantity_units', $t_sql_data_array);
		}
	}

	public function save_property_data($p_product_array, $p_order_products_id, $p_combis_id)
	{
		if(is_object($GLOBALS['coo_debugger']))
		{
			$GLOBALS['coo_debugger']->log('checkout_process: $GLOBALS[\'order\']->products[$i][id] ' . $p_product_array['id'], 'Properties');
		}

		if(is_object($GLOBALS['coo_debugger']))
		{
			$GLOBALS['coo_debugger']->log('checkout_process: extract_combis_id ' . $p_combis_id, 'Properties');
		}

		if(empty($p_combis_id) == false)
		{
			$this->coo_properties->add_properties_combi_to_orders_product($p_combis_id, $p_order_products_id);

			if(empty($p_combis_id) == false)
			{
				$coo_combis_admin_control = MainFactory::create_object("PropertiesCombisAdminControl");
				$t_use_combis_quantity = $coo_combis_admin_control->get_use_properties_combis_quantity(xtc_get_prid($p_product_array['id']));
			}
			else
			{
				$t_use_combis_quantity = 0;
			}

			# update properties_combi quantity
			if(STOCK_LIMITED == 'true' && (($t_use_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'true') || $t_use_combis_quantity == 2))
			{
				$t_quantity_change = $p_product_array['qty'] * -1;
				$t_value = $this->coo_properties->change_combis_quantity($p_combis_id, $t_quantity_change);
				
				set_shipping_status($p_product_array['id'], $p_combis_id);

				if(SEND_EMAILS == 'true' && $t_value <= STOCK_REORDER_LEVEL)
				{
					$t_products_name_query = xtc_db_query("SELECT products_name
																	FROM products_description
																	WHERE
																		products_id = '" . xtc_get_prid($p_product_array['id']) . "' AND
																		language_id = '" . $_SESSION['languages_id'] . "'");
					$t_product_result = mysql_fetch_array($t_products_name_query);

					$t_combis_details = $this->coo_properties->get_properties_combis_details($p_combis_id, $_SESSION['languages_id']);
					$t_selection_strings_array = array();

					foreach($t_combis_details as $t_property)
					{
						$t_selection_strings_array[] = $t_property['properties_name'] . ': ' . $t_property['values_name'];
					}

					$t_subject = GM_OUT_OF_STOCK_NOTIFY_TEXT . ' ' . $t_product_result['products_name'] . ' (' . implode(', ', $t_selection_strings_array) . ')';
					$t_body =	$t_product_result['products_name'] . "\n" . implode("\n", $t_selection_strings_array) . "\n" .
								$p_product_array['model'] . "\n" .
								GM_OUT_OF_STOCK_NOTIFY_TEXT . ': ' . (double)$t_value . "\n" .
								HTTP_SERVER . DIR_WS_CATALOG . 'product_info.php?info=p' . xtc_get_prid($p_product_array['id']) . "\n" .
								HTTP_SERVER . DIR_WS_CATALOG . 'admin/categories.php?pID=' . xtc_get_prid($p_product_array['id']) . '&action=new_product';

					// send mail
					$this->send_mail($t_subject, $t_body);
				}
			}
		}
	}

	public function update_special($p_product_array)
	{
		$t_specials_query = xtc_db_query("SELECT
													products_id,
													specials_quantity
												FROM " . TABLE_SPECIALS . "
												WHERE products_id = '" . xtc_get_prid($p_product_array['id']) . "'");

		if(xtc_db_num_rows($t_specials_query))
		{
			$t_special_array = xtc_db_fetch_array($t_specials_query);
			$t_new_quantity = ($t_special_array['specials_quantity'] - $p_product_array['qty']);

			if($t_new_quantity >= 1)
			{
				$t_sql_data_array = array();
				$t_sql_data_array['specials_quantity'] = $t_new_quantity;

				$this->add_special_data($t_sql_data_array, $t_special_array);

				$this->wrapped_db_perform(__FUNCTION__, TABLE_SPECIALS, $t_sql_data_array, 'update', 'products_id = "' . xtc_get_prid($p_product_array['id']) . '"');
			}
			elseif(STOCK_CHECK == 'true')
			{
				$t_sql_data_array = array();
				$t_sql_data_array['status'] = '0';
				$t_sql_data_array['specials_quantity'] = $t_new_quantity;

				$this->add_special_data($t_sql_data_array, $t_special_array);

				$this->wrapped_db_perform(__FUNCTION__, TABLE_SPECIALS, $t_sql_data_array, 'update', 'products_id = "' . xtc_get_prid($p_product_array['id']) . '"');
			}
		}
	}

	public function process_attributes($p_order_products_id, $p_product_array)
	{
		if(isset($p_product_array['attributes']))
		{
			for($j = 0, $n2 = sizeof($p_product_array['attributes']); $j < $n2; $j ++)
			{
				// update attribute stock
				if(STOCK_LIMITED == 'true')
				{
					$this->update_product_attribute($p_product_array, $p_product_array['attributes'][$j]);
				}

				$this->save_order_product_attribute($p_product_array, $p_product_array['attributes'][$j], $p_order_products_id);

				// attributes stock_notifier
				$this->update_attribute_stock($p_product_array, $p_product_array['attributes'][$j]);
			}
		}
	}

	public function update_product_attribute($p_product_array, $p_attribute_array)
	{
		$t_sql_data_array = array();
		$t_sql_data_array['attributes_stock'] = 'attributes_stock - ' . $p_product_array['qty'];
		$t_where_part = 'products_id = "' . (int)$p_product_array['id'] . '" AND
											options_values_id = "' . (int)$p_attribute_array['value_id'] . '" AND
											options_id = "' . (int)$p_attribute_array['option_id'] . '"';

		$this->add_product_attribute_data($t_sql_data_array, $p_attribute_array);

		$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_ATTRIBUTES, $t_sql_data_array, 'update', $t_where_part, 'db_link', false);
	}

	public function save_order_product_attribute($p_product_array, $p_attribute_array, $p_order_products_id)
	{
		if(DOWNLOAD_ENABLED == 'true')
		{
			$t_attributes_query = "SELECT
										popt.products_options_name,
										poval.products_options_values_name,
										pa.options_values_price,
										pa.price_prefix,
										pa.options_id,
										pa.options_values_id,
										pad.products_attributes_maxdays,
										pad.products_attributes_maxcount,
										pad.products_attributes_filename
									FROM
										" . TABLE_PRODUCTS_OPTIONS . " popt,
										" . TABLE_PRODUCTS_OPTIONS_VALUES . " poval,
										" . TABLE_PRODUCTS_ATTRIBUTES . " pa
										LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad ON pa.products_attributes_id=pad.products_attributes_id
									WHERE
										pa.products_id = '" . $p_product_array['id'] . "' AND
										pa.options_id = '" . $p_attribute_array['option_id'] . "' AND
										pa.options_id = popt.products_options_id AND
										pa.options_values_id = '" . $p_attribute_array['value_id'] . "' AND
										pa.options_values_id = poval.products_options_values_id AND
										popt.language_id = '" . $_SESSION['languages_id'] . "' AND
										poval.language_id = '" . $_SESSION['languages_id'] . "'";
			$t_attributes_result = xtc_db_query($t_attributes_query);
		}
		else
		{
			$t_attributes_result = xtc_db_query("SELECT
											popt.products_options_name,
											poval.products_options_values_name,
											pa.options_values_price,
											pa.price_prefix,
											pa.options_id,
											pa.options_values_id
										FROM
											" . TABLE_PRODUCTS_OPTIONS . " popt,
											" . TABLE_PRODUCTS_OPTIONS_VALUES . " poval,
											" . TABLE_PRODUCTS_ATTRIBUTES . " pa
										WHERE
											pa.products_id = '" . $p_product_array['id'] . "' AND
											pa.options_id = '" . $p_attribute_array['option_id'] . "' AND
											pa.options_id = popt.products_options_id AND
											pa.options_values_id = '" . $p_attribute_array['value_id'] . "' AND
											pa.options_values_id = poval.products_options_values_id AND
											popt.language_id = '" . $_SESSION['languages_id'] . "' AND
											poval.language_id = '" . $_SESSION['languages_id'] . "'");
		}

		$t_attributes_array = xtc_db_fetch_array($t_attributes_result);

		$t_sql_data_array = array('orders_id' => (int)$this->order_id,
								'orders_products_id' => $p_order_products_id,
								'products_options' => $t_attributes_array['products_options_name'],
								'products_options_values' => $t_attributes_array['products_options_values_name'],
								'options_values_price' => $t_attributes_array['options_values_price'],
								'price_prefix' => $t_attributes_array['price_prefix'],
								'options_id' => $t_attributes_array['options_id'],
								'options_values_id' => $t_attributes_array['options_values_id']);

		$this->add_order_product_attribute_data($t_sql_data_array, $t_attributes_array);

		$this->wrapped_db_perform(__FUNCTION__, TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $t_sql_data_array);

		if((DOWNLOAD_ENABLED == 'true') && isset($t_attributes_array['products_attributes_filename']) && xtc_not_null($t_attributes_array['products_attributes_filename']))
		{
			$t_sql_data_array = array('orders_id' => (int)$this->order_id,
									'orders_products_id' => $p_order_products_id,
									'orders_products_filename' => $t_attributes_array['products_attributes_filename'],
									'download_maxdays' => $t_attributes_array['products_attributes_maxdays'],
									'download_count' => $t_attributes_array['products_attributes_maxcount']);

			$this->add_order_product_download_data($t_sql_data_array, $t_attributes_array);

			$this->wrapped_db_perform(__FUNCTION__, TABLE_ORDERS_PRODUCTS_DOWNLOAD, $t_sql_data_array);
		}
	}

	public function update_attribute_stock($p_product_array, $p_attribute_array)
	{
		// Avenger
		if(SEND_EMAILS == 'true' && ATTRIBUTE_STOCK_CHECK == 'true')
		{
			$t_attributes_result = xtc_db_query("SELECT
															pd.products_name,
															pa.attributes_stock,
															po.products_options_name,
															pov.products_options_values_name
														FROM
															products_description pd,
															products_attributes pa,
															products_options po,
															products_options_values pov
														WHERE
															pa.products_id = '" . xtc_get_prid($p_product_array['id']) . "' AND
															pa.options_values_id = '" . $p_attribute_array['value_id'] . "' AND
															pa.options_id = '" . $p_attribute_array['option_id'] . "' AND
															po.products_options_id = '" . $p_attribute_array['option_id'] . "' AND
															po.language_id = '" . $_SESSION['languages_id'] . "' AND
															pov.products_options_values_id = '" . $p_attribute_array['value_id'] . "' AND
															pov.language_id = '" . $_SESSION['languages_id'] . "' AND
															pd.products_id = '" . xtc_get_prid($p_product_array['id']) . "' AND
															pd.language_id = '" . $_SESSION['languages_id'] . "'");
			if(xtc_db_num_rows($t_attributes_result) == 1)
			{
				$t_attributes_array = xtc_db_fetch_array($t_attributes_result);

				if($t_attributes_array['attributes_stock'] <= STOCK_REORDER_LEVEL)
				{
					$t_subject = GM_OUT_OF_STOCK_NOTIFY_TEXT . ' ' . $t_attributes_array['products_name'] . ' - ' . $t_attributes_array['products_options_name'] . ': ' . $t_attributes_array['products_options_values_name'];
					$t_body =	$t_attributes_array['products_name'] . ' - ' . $t_attributes_array['products_options_name'] . ': ' . $t_attributes_array['products_options_values_name'] . "\n" .
								$p_product_array['model'] . "\n" .
								GM_OUT_OF_STOCK_NOTIFY_TEXT . ': ' . (double)$t_attributes_array['attributes_stock'] . "\n" .
								HTTP_SERVER . DIR_WS_CATALOG . 'product_info.php?info=p' . xtc_get_prid($p_product_array['id']) . "\n" .
								HTTP_SERVER . DIR_WS_CATALOG . 'admin/categories.php?pID=' . xtc_get_prid($p_product_array['id']) . '&action=new_product';

					$this->send_mail($t_subject, $t_body);
				}
			}
		}
		// Avenger
	}

	public function save_tracking_data()
	{
		$t_sql_data_array = array();

		if(isset($_SESSION['tracking']['refID']))
		{
			$t_sql_data_array['refferers_id'] = $_SESSION['tracking']['refID'];

			// check if late or direct sale
			$t_customers_logon_result = "SELECT customers_info_number_of_logons FROM " . TABLE_CUSTOMERS_INFO . " WHERE customers_info_id  = '" . $_SESSION['customer_id'] . "'";
			$t_customers_logon_result = xtc_db_query($t_customers_logon_result);
			$t_customers_logon_array = xtc_db_fetch_array($t_customers_logon_result);

			if($t_customers_logon_array['customers_info_number_of_logons'] == 0)
			{
				// direct sale
				$t_sql_data_array['conversion_type'] = '1';
			}
			else
			{
				// late sale
				$t_sql_data_array['conversion_type'] = '2';
			}
		}
		else
		{
			$t_customers_result = xtc_db_query("SELECT refferers_id as ref FROM " . TABLE_CUSTOMERS . " WHERE customers_id = '" . $_SESSION['customer_id'] . "'");
			$t_customers_data_array = xtc_db_fetch_array($t_customers_result);

			if(xtc_db_num_rows($t_customers_result))
			{
				$t_sql_data_array['refferers_id'] = $t_customers_data_array['ref'];

				// check if late or direct sale
				$t_customers_logon_result = "SELECT customers_info_number_of_logons FROM " . TABLE_CUSTOMERS_INFO . " WHERE customers_info_id  = '" . $_SESSION['customer_id'] . "'";
				$t_customers_logon_result = xtc_db_query($t_customers_logon_result);
				$t_customers_logon_array = xtc_db_fetch_array($t_customers_logon_result);

				if($t_customers_logon_array['customers_info_number_of_logons'] == 0)
				{
					// direct sale
					$t_sql_data_array['conversion_type'] = '1';
				}
				else
				{
					// late sale
					$t_sql_data_array['conversion_type'] = '2';
				}
			}
		}

		$this->add_tracking_data($t_sql_data_array, $t_customers_logon_array);

		$this->wrapped_db_perform(__FUNCTION__, TABLE_ORDERS, $t_sql_data_array, 'update', 'orders_id = "' . (int)$this->order_id . '"');
	}

	public function reset()
	{
		$_SESSION['cart']->reset(true);

		// unregister session variables used during checkout
		unset($_SESSION['sendto']);
		unset($_SESSION['billto']);
		unset($_SESSION['shipping']);
		unset($_SESSION['payment']);
		unset($_SESSION['comments']);
		unset($_SESSION['last_order']);
		unset($_SESSION['tmp_oID']);
		unset($_SESSION['cc']);
		unset($_SESSION['nvpReqArray']);
		unset($_SESSION['reshash']);
		$GLOBALS['last_order'] = $this->order_id;

		//GV Code Start
		if(isset($_SESSION['credit_covers']))
		{
			unset($_SESSION['credit_covers']);
		}
		$this->coo_order_total->clear_posts(); //ICW ADDED FOR CREDIT CLASS SYSTEM
		// GV Code End

		// GX-Customizer:
		if(is_object($_SESSION['coo_gprint_cart']))
		{
			$_SESSION['coo_gprint_cart']->empty_cart();
		}
	}

	public function send_order_mail()
	{
		// no mail for Heidelpay orders
		if(strpos($this->coo_payment->selected_module, 'hp') === 0)
		{
			return false;
		}

		$coo_send_order_process = MainFactory::create_object('SendOrderProcess');
		$coo_send_order_process->set_('order_id', $this->order_id);
		$t_success = $coo_send_order_process->proceed();

		return $t_success;
	}

	public function add_order_data(&$p_sql_data_array)
	{
		// use for overloading
	}

	public function add_order_total_data(&$p_sql_data_array, $p_order_total_array)
	{
		// use for overloading
	}

	public function add_order_status_history_data(&$p_sql_data_array)
	{
		// use for overloading
	}

	public function add_product_data(&$p_sql_data_array, $p_products_id)
	{
		// use for overloading
	}

	public function add_order_product_data(&$p_sql_data_array, $p_product_array)
	{
		// use for overloading
	}

	public function add_order_product_quantity_data(&$p_sql_data_array, $p_product_array)
	{
		// use for overloading
	}

	public function add_special_data(&$p_sql_data_array, $p_special_array)
	{
		// use for overloading
	}

	public function add_product_attribute_data(&$p_sql_data_array, $p_product_attributes_array)
	{
		// use for overloading
	}

	public function add_order_product_attribute_data(&$p_sql_data_array, $p_product_attributes_array)
	{
		// use for overloading
	}

	public function add_order_product_download_data(&$p_sql_data_array, $p_product_attributes_array)
	{
		// use for overloading
	}

	public function add_tracking_data(&$p_sql_data_array, $p_customer_array)
	{
		// use for overloading
	}
}