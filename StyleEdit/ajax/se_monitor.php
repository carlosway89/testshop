<?php
/* --------------------------------------------------------------
  StyleEdit v2.0
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  --------------------------------------------------------------
*/

if(defined('_STYLE_EDIT_VALID_CALL') === false)
{
	die(''); 
}

$t_url = '';
if(isset($_POST['url']))
{
	$t_url = htmlspecialchars($_POST['url']);
}

?>
<!-- File se_monitor.php -->
<div id="style_monitor_layer">
	<div id="style_editor_tabs">
		<ul>
			<li id="se_start_tab">
				<a href="<?php echo($t_url);?>#style_monitor_tab_1" class="se_monitor_tab ie_png_fix">
					<?php
						echo "Aktueller Style";
					?>
				</a>
			</li>
			<li>
				<a href="<?php echo($t_url);?>#style_monitor_tab_2" class="se_monitor_tab ie_png_fix">
					<?php
						echo "Neue Styles";
					?>
				</a>
			</li>
			<li>
				<a id="se_monitor_close" href="<?php echo($t_url);?>#style_monitor_tab_3" class="se_monitor_tab ie_png_fix">
					<?php
						echo "Schlie&szlig;en";
					?>
				</a>
			</li>
		</ul>
		<br />
		<br />
		<div id="style_monitor_tab_1" class="se_monitor_box">
			<textarea id="se_monitor_single_input" class="se_monitor_textarea"></textarea>			
			<br />
			<br />
			<span class="se_monitor_single_save se_monitor_button">
				Speichern
			</span>&nbsp;
			<span class="se_monitor_delete se_monitor_button">
				L&ouml;schen
			</span>
		</div>
		<div id="style_monitor_tab_2" class="se_monitor_box">
			<textarea id="se_monitor_multi_input" class="se_monitor_textarea"></textarea>			
			<br />
			<br />
			<span class="se_monitor_multi_save se_monitor_button">
				Speichern
			</span>
		</div>
		<div id="style_monitor_tab_3" class="se_monitor_box">
			&nbsp;
		</div>
		
		<script>
			$( "#style_editor_tabs" ).tabs();
		</script>
	</div>
</div>