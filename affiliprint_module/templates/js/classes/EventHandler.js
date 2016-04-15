/**
 * jQuery Erweiterung: Click-Handler
 *
 * Klick-Aktionen auf Elemente, die die Klasse "clickHandler"
 * und das Attribut "elementAction"haben, können in
 * entsprechende Actions verwaltet werden. Die Namen der
 * entsprechenden Aktionen befinden sich im zuvor genannten
 * Attribut "elementAction".
 *
 * @package AffiliPRINT
 * @author Patrick Taddey <p.taddey@affiliprint.de>
 * @version 1.1
 * @copyright Copyright (c) 2014 AffiliPRINT GmbH (http://www.affiliprint.de/)
 * @license Released under the GNU General Public License (Version 2) [http://www.gnu.org/licenses/gpl-2.0.html]
 */
(function($)
{
	/**
	 * Plugin registrieren
	 */
	$.fn.extend({
		/**
		 * Plugin-Definition
		 *
		 * @param object options
		 * @return void
		 */
		ClickHandler : function(options)
		{
			/*	Standard-Werte setzen */
			var defaults = {};

			/*	Standard-Werte erweitern */
			var options = $.extend(defaults, options);

			/* Elemente iterieren */
			return this.each(function(event)
			{
				/* Objekt-Variablen erweitern */
				var elementOptions = options;
				$(this).unbind('click');
				/* Click-Event an das Element binden */
				$(this).click(function(event)
				{
					$(this)._actionHandler(event, elementOptions);
				});
			});
		},
		/**
		 * Action generieren und Aufrufen
		 *
		 * @param object event
		 * @param object elementOptions
		 * @return void
		 */
		_actionHandler : function(event, elementOptions)
		{
			consoleLog($(this).attr('elementAction') + ' released');

			/* Status-Message wieder verstecken */
			$('#apStatus').hide();

			/* Standard-Click-Event unterbinden */
			if($(this).attr('type') != 'radio' && $(this).attr('checkbox')) {
				event.preventDefault();
			}

			/* Methode bzw. Action generieren und ausführen */
			var elementAction = '$(this)._' + $(this).attr('elementAction') + 'Action(elementOptions)';

			/* versuche die Methode auszuführen */
			try {
				eval(elementAction);
			} catch(e) {
				consoleLog(elementAction + ' existiert nicht');
			}

			return;
		},
		/**
		 * Authentifizierung vornehmen und Informationen beziehen
		 *
		 * @param object elementOptions
		 * @return void
		 */
		_authenticationAction : function(elementOptions)
		{
			/* Submit-Button beziehen */
			var button = $(this);

			/* Aktivierungscode beziehen */
			var authenticationToken = $("#publisherUid").val();

			/* Formular-Validierung zurücksetzen */
			var formError = false;

			/* wenn Speichervorgang bereits ausgelöst, Funktion verlassen
			 * */
			if($(button).hasClass('disabled') === true) {
				return;
			}

			/* alte Fehler- und Erfolgsmeldungen entfernen */
			$("#publisherUid").closest('.control-group').removeClass('error').removeClass('success');

			/* Eingabefeld überprüfen */
			if($("#publisherUid").attr('placeholder') == authenticationToken || isEmpty(authenticationToken) || authenticationToken.length < 32) {
				formError = true;
			}

			/* Fehler- und Erfolgsmeldung an das Element heften */
			if(formError === true) {
				$("#publisherUid").closest('.control-group').addClass('error');
				showStatusMessage('error', MESSAGE_FORM_EMPTY);
			} else {
				$("#publisherUid").closest('.control-group').addClass('success');

				ajaxRequest('action=authentication&authenticationToken=' + authenticationToken, function(response)
				{
					/* Ladeanimation entfernen */
					$(button).removeClass('disabled');
					$('.loadingImg').remove();

					/* Rückgabe verarbeiten */
					consoleLog('Response: ' + response.message);
					showStatusMessage(response.className, response.message);
					/* Übersicht im Erfolgsfall anzeigen */
					if(response.success === true) {
						
						if(response.module_status == 1) {
							$('.apForm').show();
							$(".btnModuleStatus[elementValue='1']").prop('checked', true);
						}
						$('#apVoucherStatus').removeClass('success warning error');
						$('#apVoucherStatus').addClass(response.cssClass);
						$('#apVoucherStatus').html(response.status_message);
							
						$('.apBox').hide();
						$('#apConfig').show();
					} else {
						$("#publisherUid").closest('.control-group').removeClass('error').removeClass('success');
						$("#publisherUid").closest('.control-group').addClass('error');
					}
				}, function()
				{
					/* Ladeanimation einfügen */
					$('.loadingImg').remove();
					$(button).append('<div class="loadingImg">&nbsp;</div>');
					$(button).addClass('disabled');
				});
			}

			return;
		},
		/**
		 * Registrierung abschließen
		 *
		 * @param object elementOptions
		 * @return void
		 */
		_authenticationFormAction : function(elementOptions)
		{
			$('.apBox').hide();
			$('#apAuth').show();

			return;
		},
		/**
		 * Registrierung abschließen
		 *
		 * @param object elementOptions
		 * @return void
		 */
		_defaultAction : function(elementOptions)
		{
			$('.apBox').hide();
			$('#apInstall').show();

			return;
		},
		/**
		 * Modulstatus setzen
		 *
		 * @param object elementOptions
		 * @return void
		 */
		_moduleStatusAction : function(elementOptions)
		{
			$('.btnModuleStatus').removeClass('active');
			$(this).addClass('active');
			var status = $(this).attr('elementValue');
			ajaxRequest('action=moduleStatus&status=' + status, function(response)
			{
				/* Rückgabe verarbeiten */
				consoleLog('Response: ' + response.message);
				$('#apVoucherStatus').removeClass('success warning error');
				if(parseInt(status) == 1) {
					$('#apVoucherStatus').addClass('success');
				} else {
					$('#apVoucherStatus').addClass('error');
				}
				$('#apVoucherStatus').html(response.message);
			});
			return;
		},
		/**
		 * Modulstatus anzeigen
		 *
		 * @param boolean success
		 * @param String message
		 * @return void
		 */
		_showModulStatusAction : function(success, message)
		{
			$('.btnModuleStatus').prop('checked', false);
			$('#apVoucherStatus').removeClass('success warning error');

			if(success === true) {
				$('#apVoucherStatus').addClass('success');
				$(".btnModuleStatus[elementValue='1']").prop('checked', true);
			} else {
				$('#apVoucherStatus').addClass('error');
				$(".btnModuleStatus[elementValue='0']").prop('checked', true);
			}

			$('#apVoucherStatus').html(message);
			return;
		},
		/**
		 * Zusätzliche Daten übertragen?
		 *
		 * @param object elementOptions
		 * @return void
		 */
		_useAdditionalDataAction : function(elementOptions)
		{
			var isChecked = $(this).prop('checked');
			var useAdditionalData = 0;
			if(isChecked === true) {
				useAdditionalData = 1;
			}
			ajaxRequest('action=useAdditionalData&useAdditionalData=' + useAdditionalData, function(response)
			{
			});
			return true;
		}

	});
})(jQuery);
