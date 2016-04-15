/* PayPalApiCheck.js <?php
#   --------------------------------------------------------------
#   PayPalApiCheck.js 2011-03-08 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2011 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#
#   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
#   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
#   NEW GX-ENGINE LIBRARIES INSTEAD.
#   --------------------------------------------------------------


#   based on:
#   (c) 2003	  nextcommerce (install_finished.php,v 1.5 2003/08/17); www.nextcommerce.org
#   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: start.js 899 2011-01-24 02:40:57Z hhgag $)
#
#   Released under the GNU General Public License
#   --------------------------------------------------------------
?>*/

function PayPalApiCheck()
{
    this.do_request = function(module)
    {
        jQuery.ajax(
        {
                data:	 'module=' + module  + '&XTCsid=' + session_id,
                url: 	 'request_port.php?module=PayPalAPICheck',
                type: 	 "POST",
                async:	 true,
                success: function(t_log_html)
                {
                    $('#paypal_result').html(t_log_html);
                }
        }).html;
    }
}