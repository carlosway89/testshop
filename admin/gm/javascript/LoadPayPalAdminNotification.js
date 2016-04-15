/* LoadPayPalAdminNotification.js <?php
#   --------------------------------------------------------------
#   LoadPayPalAdminNotification.js 2012-01-20 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2012 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#
#   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
#   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
#   NEW GX-ENGINE LIBRARIES INSTEAD.
#   --------------------------------------------------------------


#   based on:
#   (c) 2003	  nextcommerce (install_finished.php,v 1.5 2003/08/17); www.nextcommerce.org
#   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: start.php 899 2011-01-24 02:40:57Z hhgag $)
#
#   Released under the GNU General Public License
#   --------------------------------------------------------------
?>*/

function LoadPayPalAdminNotification()
{
    this.load_admin_notification = function(p_oid, p_action)
    {
		jQuery.ajax(
        {
			data:	 'module=PayPalNotification&oID=' + p_oid + '&action=' + p_action,
			url: 	 'request_port.php',
			type: 	 'GET',
			async:	 true,
			timeout: 60000,
			success: function(t_url_html)
			{
				$('#paypal_admin_notification_text').html(t_url_html);
				$('#paypal_admin_notification_text').show();
				$('#paypal_admin_notification_loader').hide();
			},
			error: function()
			{
				$('#paypal_admin_notification_error').show();
				$('#paypal_admin_notification_loader').hide();
			}
        });
    }
}