<?php
/* --------------------------------------------------------------
   ShippingAndPaymentMatrixAssistentAjaxHandler.inc.php 2014-07-01 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ShippingAndPaymentMatrixAssistentAjaxHandler extends AjaxHandler
{
	public function get_permission_status($p_customers_id=NULL)
	{
		if($_SESSION['customers_status']['customers_status_id'] === '0')
		{
			#admins only
			return true;
		}
		return false;
	}
	
	public function proceed()
	{
		$t_output_array = array();
		$t_enable_json_output = false;
		
		$t_action_request = $this->v_data_array['GET']['action'];
		
		switch($t_action_request)
		{
			case 'save':
				if($_SESSION['coo_page_token']->is_valid($this->v_data_array['POST']['page_token']))
				{
					$coo_shipping_and_payment_source = MainFactory::create_object('ShippingAndPaymentMatrixAssistentSource');

					$coo_shipping_and_payment_source->delete_matrix();

					$coo_shipping_and_payment_source->save_shipping_and_payment_matrix($this->v_data_array['POST']['shipping_info'], $this->v_data_array['POST']['payment_info'], $this->v_data_array['POST']['shipping_time']);
				}
				break;
			default:
				print_r($_GET);
				trigger_error('t_action_request not found: '. htmlentities($t_action_request));
				return false;
		}
		
		if($t_enable_json_output)
		{
			$coo_json = MainFactory::create_object('GMJSON', array(false));
			$t_output_json = $coo_json->encode($t_output_array);

			$this->v_output_buffer = $t_output_json;
		}
		return true;
	}
}