/* ipayment.js <?php
#   --------------------------------------------------------------
#   ipayment.js 2013-08-19 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2013 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
$(function(){if(typeof(ipayment_silentmode)=='number'&&ipayment_silentmode==0){$('#ipayment_form').submit()}$('select#ipay_addr_country').change(function(e){var country=$(this).val();if(country=='US'||country=='CA'){$('select.usca_only').show();$('select.usca_only').removeAttr('disabled');if(country=='US'){$('option.usa_only').show();$('option.canada_only').hide()}if(country=='CA'){$('option.canada_only').show();$('option.usa_only').hide()}$('select#ipay_addr_state').val($('select#ipay_addr_state option:visible').first().val())}else{$('select.usca_only').hide();$('select.usca_only').attr('disabled','disabled')}});$('select#ipay_addr_country').change();$('#ipay_cc_typ').change(function(e){$('div.solo_only').hide();var card=$(this).val();if(card=='SoloCard'||card=='MaestroCard'){$('div.solo_only').show()}});$('#ipay_cc_typ').change()});
/*<?php
}
else
{
?>*/
$(function() {
	if(typeof(ipayment_silentmode) == 'number' && ipayment_silentmode == 0) {
		$('#ipayment_form').submit();
	}

	$('select#ipay_addr_country').change(function(e) {
		var country = $(this).val();
		if(country == 'US' || country == 'CA') {
			$('select.usca_only').show();
			$('select.usca_only').removeAttr('disabled');
			if(country == 'US') {
				$('option.usa_only').show();
				$('option.canada_only').hide();
			}
			if(country == 'CA') {
				$('option.canada_only').show();
				$('option.usa_only').hide();
			}
			$('select#ipay_addr_state').val($('select#ipay_addr_state option:visible').first().val());
		}
		else {
			$('select.usca_only').hide();
			$('select.usca_only').attr('disabled', 'disabled');
		}
	});
	$('select#ipay_addr_country').change();

	$('#ipay_cc_typ').change(function(e) {
		$('div.solo_only').hide();
		var card = $(this).val();
		if(card == 'SoloCard' || card == 'MaestroCard') {
			$('div.solo_only').show();
		}
	});
	$('#ipay_cc_typ').change();
});
/*<?php
}
?>*/