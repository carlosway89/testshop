<?php
/* --------------------------------------------------------------
 AffiliPrintCheckoutSuccessExtender.inc.php 2011-12-01 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2011 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */
class AffiliPrintCheckoutSuccessExtender extends AffiliPrintCheckoutSuccessExtender_parent
{
	function proceed()
	{
		parent::proceed();
		require_once (DIR_FS_CATALOG . 'affiliprint_module/includes/functions.php');
		
		/* Ausgabe, Gutscheine beziehen, Bestelldaten übergeben */
		$snippet = getVouchers($this->v_data_array);
		if($snippet !== false) {
			$this->v_output_buffer['AFFILIPRINT_SNIPPET'] = $snippet;
		}

		return;
	}
}