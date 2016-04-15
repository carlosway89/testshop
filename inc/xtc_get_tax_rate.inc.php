<?php
/* --------------------------------------------------------------
  xtc_get_tax_rate.inc.php 2013-12-17 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2013 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com
  (c) 2003	 nextcommerce (xtc_get_tax_rate.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_get_tax_rate.inc.php 862 2005-04-16 10:26:29Z mz $)

  Released under the GNU General Public License
  ----------------------------------------------------------------------------------------- */

function xtc_get_tax_rate($p_class_id, $p_country_id = -1, $p_zone_id = -1, $p_customer_b2b = -1)
{
	static $t_tax_rates_array;

	$c_class_id = (int)$p_class_id;
	$c_country_id = (int)$p_country_id;
	$c_zone_id = (int)$p_zone_id;

	if(($c_country_id === -1 || $c_country_id === 0) && ($c_zone_id === -1 || $c_zone_id === 0))
	{
		$c_country_id = (isset($_SESSION['customer_country_id'])) ? (int)$_SESSION['customer_country_id'] : (int)STORE_COUNTRY;
		
		if(isset($_SESSION['customer_zone_id']))
		{
			$c_zone_id = (int)$_SESSION['customer_country_id'];
		}
		elseif(!isset($_SESSION['customer_zone_id']) && (!isset($_SESSION['customer_country_id']) || $_SESSION['customer_country_id'] == STORE_COUNTRY))
		{
			$c_zone_id = (int)STORE_ZONE;
		}
		else
		{
			$c_zone_id = 0;
		}
	}

	if($_SESSION['customers_status']['customers_status_id'] === '0')
	{
		$c_country_id = (int)STORE_COUNTRY;
		$c_zone_id = (int)STORE_ZONE;
	}
	elseif(country_eu_status_by_country_id($c_country_id) == true)
	{
		if($p_customer_b2b != -1)
		{
			$t_customer_b2b = $p_customer_b2b;
		}
		elseif(isset($_SESSION['customer_b2b_status']) == true)
		{
			$t_customer_b2b = $_SESSION['customer_b2b_status'];
		}
		else
		{
			$t_customer_b2b = false;
		}

		if($t_customer_b2b == true)
		{
			// OVERWRITE country and zone, if customer is B2B in EU
			$c_country_id = (int)STORE_COUNTRY;
			$c_zone_id = (int)STORE_ZONE;
		}
	}


	$t_key = $c_class_id . '_' . $c_country_id . '_' . $c_zone_id;
	
	if($t_tax_rates_array !== null && isset($t_tax_rates_array[$t_key]))
	{
		return $t_tax_rates_array[$t_key];
	}
	else
	{
		$t_sql = 'SELECT SUM(tax_rate) AS tax_rate 
					FROM
						' . TABLE_TAX_RATES . ' tr 
					LEFT JOIN ' . TABLE_ZONES_TO_GEO_ZONES . ' za ON (tr.tax_zone_id = za.geo_zone_id) 
					LEFT JOIN ' . TABLE_GEO_ZONES . ' tz ON (tz.geo_zone_id = tr.tax_zone_id) 
					WHERE
						(za.zone_country_id IS NULL OR 
							za.zone_country_id = "0" OR 
							za.zone_country_id = "' . (int)$c_country_id . '") AND 
						(za.zone_id IS NULL OR 
							za.zone_id = "0" OR 
							za.zone_id = "' . (int)$c_zone_id . '") AND 
						tr.tax_class_id = "' . (int)$c_class_id . '" 
					GROUP BY tr.tax_priority';
		$t_result = xtc_db_query($t_sql);
		
		if(xtc_db_num_rows($t_result, true))
		{
			$t_tax_multiplier = 1.0;
			while($t_tax_array = xtc_db_fetch_array($t_result, true))
			{
				$t_tax_multiplier *= 1.0 + ($t_tax_array['tax_rate'] / 100);
			}
			
			$t_tax_rate = ($t_tax_multiplier - 1.0) * 100;
			
			$t_tax_rates_array[$t_key] = $t_tax_rate;
			
			return $t_tax_rate;
		}
		else
		{
			$t_tax_rates_array[$t_key] = 0;
			
			return 0;
		}
	}
}

