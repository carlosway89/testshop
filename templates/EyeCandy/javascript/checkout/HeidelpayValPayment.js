document.addEventListener('DOMContentLoaded', function(){
	$(document).ready(function(){
		var orgLink = jQuery('#checkout_payment').attr('action');
		var prefix = 'hp';

		jQuery('#checkout_payment').click(function(e){
			if(jQuery('.payment_item').length > 1){
					setTimeout(function(){
						//change form action
						var checkedOpt = jQuery('#checkout_payment .items .payment_item input:radio:checked').attr('value');
						var checkedOptPos = checkedOpt.indexOf(prefix);

						if(checkedOptPos >= 0){
							var pm = checkedOpt.substr(checkedOptPos+prefix.length);
							
							//reuse data
							if((jQuery('.reuseBox_'+pm).length > 0) && !(jQuery('.reuseBox_'+pm).is(':checked'))){
								var reuse = true;
							}else{
								var reuse = false;
							}

							if(formUrl != null){
								if((formUrl[pm] == undefined) || (formUrl[pm] == '') || (reuse)){ jQuery('#checkout_payment').attr('action', orgLink); }
								else{ jQuery('#checkout_payment').attr('action', formUrl[pm]); }
							}
						}else{
							jQuery('#checkout_payment').attr('action', orgLink);
						}
					}, 100);
			}else{
				var checkedOpt = jQuery('.payment_item input').attr('value');
				var checkedOptPos = checkedOpt.indexOf(prefix);
				var pm = checkedOpt.substr(checkedOptPos+prefix.length);

				//reuse data
				if((jQuery('.reuseBox_'+pm).length > 0) && !(jQuery('.reuseBox_'+pm).is(':checked'))){
					var reuse = true;
				}else{
					var reuse = false;
				}

				if(formUrl != null){
					if((formUrl[pm] == undefined) || (formUrl[pm] == '') || (reuse)){ jQuery('#checkout_payment').attr('action', orgLink); }
					else{ jQuery('#checkout_payment').attr('action', formUrl[pm]); }
				}
			}
		});
		
		// REUSE PAYMENT
		jQuery('#checkout_payment .payment_item input:checkbox').click(function(){
			var pm = jQuery(this).attr('class').substring(jQuery(this).attr('class').indexOf('_'));
			
			jQuery('.reuse'+pm).toggle(500);
			jQuery('.newreg'+pm).toggle(500);
		});

		// VALIDATE FORM
		jQuery('#checkout_payment .continue_button').click(function(e){
			if(jQuery('#main_inside .order_payment .errorText').length == 0){				
				jQuery('#main_inside .order_payment h1').after('<div class="errorText"><ul></ul></div>');
			}
		
			if(jQuery('.payment_item .radiobox input').attr('type') == 'hidden'){
				var checkedOpt = jQuery('.payment_item .radiobox input').val();
			}else{
				if(jQuery('#checkout_payment input:radio:checked').length != 0){
					var checkedOpt = jQuery('.payment_item .radiobox input:radio:checked').val();
				}else{
					jQuery('.errorText ul li').remove();
					jQuery('.errorText ul').append('<li>'+jQuery('.msg_checkPymnt').html()+'</li>');
					jQuery('#center .errorText').show();
					jQuery('html, body').animate({scrollTop: 0}, 0);
					return false;
				}
			}
			
			var gE_return = getErrors(checkedOpt);
			var pm = gE_return['pm'];
			var errors = gE_return['errors'];
			
			if((jQuery('div.newreg_'+pm+' .instyle_error').length > 0)){
				jQuery('.errorText ul li').remove();
				jQuery('.errorText ul').append('<li>'+jQuery('.msg_fill').html()+'</li>');
				
				jQuery.each(errors, function(key, value){
					jQuery('.errorText ul').append('<li>'+jQuery(value).html()+'</li>');
				});
				
				jQuery('#center .errorText').show();
				jQuery('html, body').animate({ scrollTop: 0 }, 0);
				return false;
			}else{
				// disable all other input fields
				jQuery('#checkout_payment .items .payment_item input').attr('disabled', true);
				jQuery('#checkout_payment .items .payment_item select').attr('disabled', true);
				jQuery('#checkout_payment .items .payment_item.module_option_checked').find('input').removeAttr('disabled');
				jQuery('#checkout_payment .items .payment_item.module_option_checked').find('select').removeAttr('disabled');				
				return true;
			}
		});
	});
});


function getErrors(checkedOpt){
	if(checkedOpt.indexOf('hp') == 0){

		//remove all 'errors'
		jQuery('.instyle_error').removeClass('instyle_error');
		var pm = checkedOpt.substr(checkedOpt.indexOf('hp')+2);

		//check if 'newreg' is shown
		if(jQuery('.newreg_'+pm).is(':visible')){
			//set 'error' to empty inputs
			jQuery('.newreg_'+pm).find('input').each(function(){
				if(jQuery(this).val() == ''){
					jQuery(this).addClass('instyle_error');
				}else{
					jQuery(this).removeClass('instyle_error');
				}
			});

			if((pm == 'cc') || (pm == 'dc')){
				var errors = valInputCard(jQuery('.newreg_'+pm+' #cardBrand').find(":selected").val(), jQuery('.newreg_'+pm+' #cardNumber').val(), jQuery('.newreg_'+pm+' #cardVerification').val(), pm);
			}else if(pm == 'dd'){					
				if(jQuery('.newreg_'+pm+' #iban_switch').find(":selected").val() == 'iban'){
					var errors = valInputDdIban(jQuery('.newreg_'+pm+' #iban').val(), jQuery('.newreg_'+pm+' #bic').val(), pm);
				}else{
					var errors = valInputDdAccount(jQuery('.newreg_'+pm+' #account').val(), jQuery('.newreg_'+pm+' #bankcode').val());
				}
			}else if(pm == 'gp'){			
				var errors = valInputDdIban(jQuery('.newreg_'+pm+' #iban').val(), jQuery('.newreg_'+pm+' #bic').val(), pm);
				if(jQuery('.newreg_'+pm+' #accHolder').val() == ''){
					errors[i++] = '.msg_holder';				
				}
			}else if(pm == 'idl'){			
				var errors = {};
				var i = 0;
				if(jQuery('.newreg_'+pm+' #accHolder').val() == ''){
					errors[i++] = '.msg_holder';				
				}
			}	
		}
	}
	
	var returnVal = [];
	returnVal['pm'] = pm;
	returnVal['errors'] = errors;
	
	return returnVal;
}


// VALIDATE FORM ON GATEWAY
function valGatewayForm(){
	checkedOpt = jQuery('.gateway form table').attr('class');
	var pm = checkedOpt.substr(checkedOpt.indexOf('_')+1);
	
	if((pm == 'cc') || (pm == 'dc')){
		var errors = valInputCard(jQuery('.newreg_'+pm+' #cardBrand').find(":selected").val(), jQuery('.newreg_'+pm+' #cardNumber').val(), jQuery('.newreg_'+pm+' #cardVerification').val(), pm);
	}else if(pm == 'dd'){
		if(jQuery('.newreg_'+pm+' #sepa_switch').find(":selected").val() == 'iban'){
			var errors = valInputDdIban(jQuery('.newreg_'+pm+' #iban').val(), jQuery('.newreg_'+pm+' #bic').val());
		}else{
			var errors = valInputDdAccount(jQuery('.newreg_'+pm+' #account').val(), jQuery('.newreg_'+pm+' #bankcode').val());
		}
	}else if(pm = 'gp'){
		var errors = {};
		var i = 0;

		if(jQuery('.newreg_'+pm+' #cardHolder').val() == ''){
			jQuery('.newreg_'+pm+' #cardHolder').addClass('instyle_error');
		}else{
			jQuery('.newreg_'+pm+' #cardHolder').removeClass('instyle_error');
		}
		
		if(jQuery('.newreg_'+pm+' #sepa_switch').find(":selected").val() == 'iban'){
			if(jQuery('.newreg_'+pm+' #iban').val() == ''){
				jQuery('.newreg_'+pm+' #iban').addClass('instyle_error');
				errors[i++] = '.msg_iban';				
			}else{
				jQuery('.newreg_'+pm+' #iban').removeClass('instyle_error');
			}
			if(jQuery('.newreg_'+pm+' #bic').val() == ''){
				jQuery('.newreg_'+pm+' #bic').addClass('instyle_error');
				errors[i++] = '.msg_bic';
			}else{
				jQuery('.newreg_'+pm+' #bic').removeClass('instyle_error');
			}
		}else{
			if(jQuery('.newreg_'+pm+' #account').val() == ''){ 
				jQuery('.newreg_'+pm+' #account').addClass('instyle_error');
				errors[i++] = '.msg_account';				
			}else{
				jQuery('.newreg_'+pm+' #account').removeClass('instyle_error');
			}			
			if(jQuery('.newreg_'+pm+' #bankcode').val() == ''){
				jQuery('.newreg_'+pm+' #bankcode').addClass('instyle_error');
				errors[i++] = '.msg_bank';
			}else{
				jQuery('.newreg_'+pm+' #bankcode').removeClass('instyle_error');
			}
		}
	}
	
	jQuery('.gatewayError').hide();
	jQuery('.gatewayError div').hide();
	
	if((jQuery('.newreg_'+pm+' .instyle_error').length > 0)){		
		jQuery('.gatewayError').show();
		jQuery('.gatewayError .msg_fill').show();
		
		jQuery.each(errors, function(key, value){
			jQuery('.gatewayError '+value).show();
		});
		return false;
	}
}


function valInputCard(brand, cardnr, cvv, pm){
	var regexCvv	= new RegExp('^[0-9]{3}$');
	var errors = {};
	var i = 0;	
	
	var errorCrdnr = false;
	var errorCvv = false;
	
	cardnr = cardnr.trim();
	
	// CREDIT CARDS
	if(brand == 'AMEX'){
		var regexCNr	= new RegExp('^[0-9]{15}$');
		regexCvv			= new RegExp('^[0-9]{4}$');
		var frstCrdNr	= new RegExp('^[3]');
	}
	if(brand == 'MASTER'){
		var regexCNr	= new RegExp('^[0-9]{16}$');
		var frstCrdNr	= new RegExp('^[5]');
	}	
	if(brand == 'VISA'){
		var regexCNr	= new RegExp('^[0-9]{13,16}$');
		var frstCrdNr	= new RegExp('^[4]');
	}
	if(brand == 'DISCOVERY'){
		var regexCNr	= new RegExp('^[0-9]{16}$');
		var frstCrdNr	= new RegExp('^[6]');
	}	
	if(brand == 'JCB'){
		var regexCNr	= new RegExp('^[0-9]{16}$');
		var frstCrdNr	= new RegExp('^[3]');
	}

	// DEBIT CARDS
	if(brand == 'VISAELECTRON'){
		var regexCNr	= new RegExp('^[0-9]{16}$');
		var frstCrdNr	= new RegExp('^[4]');
	}
	if(brand == 'SOLO'){
		var regexCNr	= new RegExp('^[0-9]{16}$|^[0-9]{18}$|^[0-9]{19}$');
		var frstCrdNr	= new RegExp('^[6]');
	}
	if(brand == 'SERVIRED'){
		var regexCNr	= new RegExp('^[0-9]{16}$');
		var frstCrdNr	= new RegExp('^[4]');
	}	
	if(brand == 'FOURB'){
		var regexCNr	= new RegExp('^[0-9]{16}$');
		var frstCrdNr	= new RegExp('^[5]');
	}
	if(brand == 'CARTEBLEUE'){
		var regexCNr	= new RegExp('^[0-9]{16}$');
		var frstCrdNr	= new RegExp('^[4]');
	}	
	if(brand == 'EURO6000'){
		var regexCNr	= new RegExp('^[0-9]{12,19}$');
		var frstCrdNr	= new RegExp('^[0]|^[5]|^[6]');
	}
	if(brand == 'MAESTRO'){
		var regexCNr	= new RegExp('^[0-9]{12,19}$');
		var frstCrdNr	= new RegExp('^[0]|^[5]|^[6]');
	}	
	if(brand == 'POSTEPAY'){
		var regexCNr	= new RegExp('^[0-9]{16}$');
		var frstCrdNr	= new RegExp('^[4]');
	}
	if(brand == 'DANKORT'){
		var regexCNr	= new RegExp('^[0-9]{16}$');
		var frstCrdNr	= new RegExp('^[5]');
	}

	// set errors
	if((cardnr.search(regexCNr) == '-1') || (cardnr.search(frstCrdNr) == '-1') || !(checkLuhnAlgo(cardnr))){
		jQuery('.newreg_'+pm+' #cardNumber').addClass('instyle_error');
		errors[i++] = '.msg_crdnr';
	}else{
		jQuery('.newreg_'+pm+' #cardNumber').removeClass('instyle_error');
	}
	
	if(cvv.search(regexCvv) == '-1'){
		jQuery('.newreg_'+pm+' #cardVerification').addClass('instyle_error');
		errors[i++] = '.msg_cvv';
	}else{
		jQuery('.newreg_'+pm+' #cardVerification').removeClass('instyle_error');
	}

	return errors;
}


function valInputDdIban(iban, bic, pm){
	var errors = {};
	var i = 0;
	
	var regexIban	= new RegExp('^[A-Z]{2}[0-9]{2}[a-zA-Z0-9]{11,30}$');
	var regexBic	= new RegExp('^[a-zA-Z]{6}[a-zA-Z0-9]{2,5}$');
	
	if(iban.search(regexIban) == '-1'){
		jQuery('.newreg_'+pm+' #iban').addClass('instyle_error');
		errors[i++] = '.msg_iban';
	}else{
		jQuery('.newreg_'+pm+' #iban').removeClass('instyle_error');
	}
	
	if(bic.search(regexBic) == '-1'){
		jQuery('.newreg_'+pm+' #bic').addClass('instyle_error');
		errors[i++] = '.msg_bic';
	}else{
		jQuery('.newreg_'+pm+' #bic').removeClass('instyle_error');
	}
	
	jQuery('.newreg_dd #account').removeClass('instyle_error');
	jQuery('.newreg_dd #bankcode').removeClass('instyle_error');
	
	return errors;
}

function valInputDdAccount(acc, bank){
	var errors = {};
	var i = 0;

	var regexAcc		= new RegExp('^[0-9]{6,16}$');
	var regexBank	= new RegExp('^[0-9]{5,8}$');
	
	if(acc.search(regexAcc) == '-1'){
		jQuery('.newreg_dd #account').addClass('instyle_error');
		errors[i++] = '.msg_account';
	}else{
		jQuery('.newreg_dd #account').removeClass('instyle_error');
	}
	
	if(bank.search(regexBank) == '-1'){
		jQuery('.newreg_dd #bankcode').addClass('instyle_error');
		errors[i++] = '.msg_bank';
	}else{
		jQuery('.newreg_dd #bankcode').removeClass('instyle_error');
	}
	
	jQuery('.newreg_dd #iban').removeClass('instyle_error');
	jQuery('.newreg_dd #bic').removeClass('instyle_error');
	return errors;
}


function checkLuhnAlgo(digitsOnly){
    var actDigit;
    var sumOfAll = parseInt(digitsOnly.substr(digitsOnly.length - 1));

    for (var i = digitsOnly.length - 2; i >= 0; i--){
    	actDigit = parseInt(digitsOnly.substring(i, i + 1));
        if (((digitsOnly.length - 2 - i) % 2) == 0){
        	actDigit <<= 1;
            if (actDigit > 9){
            	actDigit -= 10;
                sumOfAll ++;
            }
        }
        sumOfAll += actDigit;
    }

    if ((sumOfAll % 10) != 0){
        return false;
    }
    return true;
}