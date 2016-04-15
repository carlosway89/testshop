<?php
/* --------------------------------------------------------------
	TrustBadgeCheckoutSuccessExtender.inc.php 2015-02-16
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class TrustBadgeCheckoutSuccessExtender extends TrustBadgeCheckoutSuccessExtender_parent
{
	public function proceed()
	{
		parent::proceed();
		$service = MainFactory::create_object('GMTSService');
		$tsid = $service->findRatingID($_SESSION['language_code']);
		$badge_snippet = $service->getBadgeSnippet($tsid);
		if($badge_snippet['enabled'] == true)
		{
			$this->html_output_array['TRUSTBADGE_CONFIRMATION_SNIPPET'] = $this->getConfirmationSnippet();
		}
	}

	protected function getConfirmationSnippet()
	{
		$max_days = $this->getMaxDeliveryDays($this->v_data_array['orders_id']);
		$estDeliveryDate = date('Y-m-d', strtotime('+ '.$max_days.' days'));
		$snippet =
			'<div id="trustedShopsCheckout" style="display: none;">'.PHP_EOL.
			'	<span id="tsCheckoutOrderNr">'.$this->v_data_array['orders_id'].'</span>'.PHP_EOL.
			'	<span id="tsCheckoutBuyerEmail">'.$this->v_data_array['coo_order']->customer['email_address'].'</span>'.PHP_EOL.
			'	<span id="tsCheckoutOrderAmount">'.number_format($this->v_data_array['coo_order']->info['pp_total'], 2, '.', '').'</span>'.PHP_EOL.
			'	<span id="tsCheckoutOrderCurrency">'.$this->v_data_array['coo_order']->info['currency'].'</span>'.PHP_EOL.
			'	<span id="tsCheckoutOrderPaymentType">'.$this->v_data_array['coo_order']->info['payment_method'].'</span>'.PHP_EOL;
		if($max_days !== false)
		{
			$snippet .= '	<span id="tsCheckoutOrderEstDeliveryDate">'.$estDeliveryDate.'</span>'.PHP_EOL;
		}
		$snippet .= '</div>';
		return $snippet;
	}

	protected function getMaxDeliveryDays($orders_id)
	{
		$query =
			'SELECT
				MAX(ss.number_of_days) AS max_days
			FROM
				products p
			LEFT JOIN
				`shipping_status` ss ON ss.shipping_status_id = p.products_shippingtime AND ss.language_id = \':language_id\'
			WHERE
				p.products_id IN (SELECT products_id FROM orders_products WHERE orders_id = \':orders_id\')';
		$query = strtr($query, array(':language_id' => $_SESSION['languages_id'], ':orders_id' => (int)$orders_id));
		$max_days = false;
		$result = xtc_db_query($query);
		while($row = xtc_db_fetch_array($result))
		{
			$max_days = (int)$row['max_days'];
		}
		return $max_days;
	}
}
