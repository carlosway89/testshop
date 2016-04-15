<?php
/* --------------------------------------------------------------
   SampleExtender.inc.php 2014-01-01 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SampleExtender extends SampleExtender_parent
{
	function proceed()
	{
		parent::proceed();
		
		// PHP Code
		if(isset($this->v_data_array['POST']['save']) || isset($this->v_data_array['POST']['gm_update']))
		{
			// products_id is always available (insert & update)
			$t_products_id = $this->v_data_array['GET']['pID'];
			
			// set products_status
			$t_query = 'UPDATE
							products
						SET
							products_status = ' . $this->v_data_array['POST']['products_status'] . '
						WHERE
							products_id = "' . $t_products_id . '"';
			xtc_db_query($t_query);
		}		
	}
}