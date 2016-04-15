/* lettr_de.js <?php
#   --------------------------------------------------------------
#   lettr_de.js
#   Digineo GmbH
#   http://www.digineo.de
#   Copyright (c) 2011 Digineo GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#
#   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
#   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
#   NEW GX-ENGINE LIBRARIES INSTEAD.
#   --------------------------------------------------------------
?>*/

$(document).ready(function(){
  // 2011-08-01 rp @ digineo: Wir benötigen Sprachunterstützung für Deutsch und Englisch (de, en)
  var lang = $('HTML').attr('lang');
  $('#val_apikey').bind('change', function(e){
    var key = $(this).val();
    // apikey verifizieren
    if (key.length > 30) {
       $.ajax({
         url: document.location,
         dataType: 'json',
         data: {
           "key": key,
           "go": "verify_apikey"
         },
         success: function(d) {
           if (d.status == 200){
             if (d.response.setting.user_id) {
               $('INPUT[name=validapikey]').val('1');
               $('#out_apikey').html( ((lang == 'de') ? 'Schl&uuml;ssel wurde verifiziert.' : 'Key verified') ).css('color', '#00A000');
               $('#news_export_code').html('http://newsletter_export:' + key.substr(0, 15) + '@' + d.domain.substr( d.domain.indexOf('/') + 2 , 999) + '/lettr/export.php').css('font-style', 'normal').css('font-weight', 'bold');
               $('INPUT[name=importurl]').val('http://newsletter_export:' + key.substr(0, 15) + '@' + d.domain.substr( d.domain.indexOf('/') + 2 , 999) + '/lettr/export.php');
             }
           }
         },
         error: function(xhr, status, err) {
           $('#out_apikey').html( ((lang == 'de') ? 'Schl&uuml;ssel konnte nicht verifiziert werden.' : 'Cannot verify key') ).css('color', '#C00000');
           $('#news_export_code').html( ((lang == 'de') ? 'Wird angezeigt, sobald der API-Key verifiziert wurde...' : 'Will be displayed after the successful validation of your API-Key...') ).css('font-style', 'italic').css('font-weight', 'normal');
           $('INPUT[name=validapikey]').val('0');
            $('INPUT[name=importurl]').val('');
         }
       });
    } else {
      $('INPUT[name=validapikey]').val('0');
      $('INPUT[name=importurl]').val('');
      $('#news_export_code').html( ((lang == 'de') ? 'Wird angezeigt, sobald der API-Key verifiziert wurde...' : 'Will be displayed after the successful validation of your API-Key...') ).css('font-style', 'italic').css('font-weight', 'normal');
      if (key.length > 0) {
        $('#out_apikey').html( ((lang == 'de') ? 'Schl&uuml;ssel nicht lang genug' : 'key too short') ).css('color', '#C00000');
      } else {
        $('#out_apikey').html('');
      }
    }
  });
  // OnChange-Event abfeuern
  $('#val_apikey').trigger('change');
});
