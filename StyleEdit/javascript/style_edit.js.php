<?php
/* --------------------------------------------------------------
  StyleEdit v2.0
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2010 Gambio GmbH
  --------------------------------------------------------------
*/


if($_SESSION['style_edit_mode'] == 'edit' || $_SESSION['style_edit_mode'] == 'sos')
{
?>
	var gmStyleMonitor					= new GMStyleMonitor();
	var gmStyleEditToolBox				= new GMStyleEditToolBox();
	var gmslc							= new GMStyleEditSelector();
	var gmStyleEditHandler				= new GMStyleEditHandler(gmslc);
	var gmStyleEditControl				= new GMStyleEditControl();

	var gmBoxesPageMenu					= new GMBoxesPageMenu();
	var gmBoxesMaster					= new GMBoxesMaster(gmBoxesPageMenu);

	gmStyleEditHandler.v_se_active		= false;
	gmStyleEditControl.v_se_act_accord	= '';
	
	$(document).ready(
		function(event) 
		{
			if(style_edit_config_SOS)
			{
				$('body').append('<div class="sos">Wiederherstellungsmodus aktiv. Sie k&ouml;nnen nun aus dem Archiv eine Sicherung laden.</div>');
			}

			/* debug 
			$('body').append('<div id="se_style_tester" style="display:none;font-family: System, sans-serif;size:11px;height:100px;overflow:scroll;position:fixed;top:5px;right:5px;z-index:3333;background-color:#EEE;padding:10px;border:1px solid #AAA"></div>');
			*/
			var bind_fn = function() {

				<?php
				if($_SESSION['style_edit_mode'] == 'edit')
				{
					include('StyleEdit/config_StyleEdit.php');
					include_once ('StyleEdit/classes/GMSEDatabase.php');
					$coo_db = new GMSEDatabase();
					$coo_css = new GMCSSManager($coo_db);
					$coo_css->load_styles($_GET['current_template']);
				}
				?>	
			};
			
			gmStyleMonitor.init(event);

			gmStyleEditToolBox.init(event);	

			gmStyleEditControl.init();

			if(style_edit_config_SOS)
			{
				$('#se_backup').click();
				$('#se_archive').click();
			}
			
			gmslc.set_activation_function(bind_fn);

		}		
	);

<?php
}
?>