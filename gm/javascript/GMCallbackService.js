/* GMCallbackService.js <?php
 #   --------------------------------------------------------------
 #   GMCallbackService.js 2015-07-29 gambio
 #   Gambio GmbH
 #   http://www.gambio.de
 #   Copyright (c) 2015 Gambio GmbH
 #   Released under the GNU General Public License (Version 2)
 #   [http://www.gnu.org/licenses/gpl-2.0.html]
 #   --------------------------------------------------------------
 ?>*/
/*<?php
 if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
 {
 ?>*/
var gm_callback_service=new GMCallbackService();function GMCallbackService(){this.check_callback=function(){$('#gm_callback_service_error_message').load('request_port.php?module=CallbackService&action=check&name='+encodeURIComponent($('#gm_callback_service_name').val())+'&telephone='+encodeURIComponent($('#gm_callback_service_telephone').val())+'&vvcode='+encodeURIComponent($('#vvcode').val())+'&recaptcha_response_field='+encodeURIComponent($('#recaptcha_response_field').val())+'&recaptcha_challenge_field='+encodeURIComponent($('#recaptcha_challenge_field').val())+'&XTCsid='+gm_session_id,function(errors){if(errors=='')gm_callback_service.send_form();else{$('#gm_callback_service_error_message').html(errors);try{Recaptcha.reload()}catch(e){$('#vvcode').val('');$('#vvcode_image').attr('src','request_port.php?rand='+Math.random()+'&module=CreateVVCode&XTCsid='+gm_session_id)}}})};this.send_form=function(){jQuery.ajax({data:$('#gm_callback_service_form').serialize(),url:'request_port.php?module=CallbackService&action=send&XTCsid='+gm_session_id,type:"POST",success:function(sent_success){$("#gm_callback_service_success").html(sent_success)}})};}$(document).ready(function(){$("#gm_callback_service_send").click(function(){gm_callback_service.check_callback();return false})});
/*<?php
 }
 else
 {
 ?>*/
var gm_callback_service = new GMCallbackService();

function GMCallbackService(){

	this.check_callback = function(){
		$('#gm_callback_service_error_message').load('request_port.php?module=CallbackService&action=check'
			+ '&name=' + encodeURIComponent($('#gm_callback_service_name').val())
			+ '&telephone=' + encodeURIComponent($('#gm_callback_service_telephone').val())
			+ '&vvcode=' + encodeURIComponent($('#vvcode').val())
			+ '&recaptcha_response_field=' +  encodeURIComponent($('#recaptcha_response_field').val())
			+ '&recaptcha_challenge_field=' + encodeURIComponent($('#recaptcha_challenge_field').val())
			+ '&XTCsid=' + gm_session_id,
			function(errors){
				if(errors == '') gm_callback_service.send_form();
				
				else
				{
					$('#gm_callback_service_error_message').html(errors);
					try
					{
						Recaptcha.reload();
					}
					catch (e)
					{
						$('#vvcode').val('');
						$('#vvcode_image').attr('src', 'request_port.php?rand=' + Math.random() + '&module=CreateVVCode&XTCsid=' + gm_session_id);
					}
				}
			}
		);
	}

	this.send_form = function(){
		jQuery.ajax({
			data: 		$('#gm_callback_service_form').serialize(),
			url: 		'request_port.php?module=CallbackService&action=send&XTCsid=' + gm_session_id,
			type: 		"POST",
			success: 	function(sent_success) 
						{
							$("#gm_callback_service_success").html(sent_success);
						}
		});
	}
}

$(document).ready(function()
{
	$("#gm_callback_service_send").click(function()
	{
		gm_callback_service.check_callback();
		return false;
	});
});
/*<?php
}
?>*/