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
<div id="style_edit_layer">
	<div id="style_edit_background">
		<div id="style_edit_foreground">
			<!-- BOF INFO TEXT -->
			<div id="style_edit_step_1" class="style_edit_info">
				<?php
					echo INFO_START_STYLE_EDIT;
				?>
			</div>
			<div id="style_edit_step_2" class="style_edit_info">
				<?php
					echo INFO_START_STYLE_EDITING;
				?>
			</div>
			<div id="style_boxes_step_1" class="style_boxes_info">
				<?php
					echo INFO_START_BOXES_EDIT;
				?>
			</div>
			<div id="style_boxes_step_2" class="style_boxes_info">
				<?php
					echo INFO_START_BOXES_EDITING;
				?>
			</div>
			<!-- EOF INFO TEXT -->
			<div id="style_edit_tabs">
				<ul>
					<li id="se_start_tab">
						<a href="<?php echo($t_url);?>#style_edit_tab_1" id="se_styles" class="se_tab ie_png_fix">
							<?php
								echo TABS_TITLE_STYLES;
							?>
						</a>
					</li>
					<li>
						<a href="<?php echo($t_url);?>#style_edit_tab_2"	id="se_boxes" class="se_tab ie_png_fix">
							<?php
								echo TABS_TITLE_BOXES;
							?>
						</a>
					</li>
					<li>
						<a href="<?php echo($t_url);?>#style_edit_tab_3" id="se_backup" class="se_tab ie_png_fix se_archive_heading">
							<?php
								echo TABS_TITLE_BACKUP;
							?>
						</a>
					</li>
				</ul>
				<div id="se_close" class="se_control ie_png_fix">
					&nbsp;
				</div>

				<!-- BOF TAB STYLES -->
				<div id="style_edit_tab_1">
					<div id="se_styles_accordion">
						<h3 id="se_area_fonts" class="se_accord">
							<a href="#" class="se_accord ie_png_fix se_accord" id="se_accord_fonts">
								<?php
									echo ACCORDION_TITLE_FONT;
								?>
							</a>
						</h3>
						<div id="se_fonts">
							<div>
								<table class="se_table" width="235" cellspacing="0" cellpadding="0" border="0">
									<tr height="50">
										<!-- BOF FONT-FAMILY -->									
										<td width="95" valign="top">
											<div id="se_font-family" class="se_form">
												<div class="se_form_heading">
													<?php
														echo FORM_TITLE_FONT_FACE;
													?>
												</div>
												<div id="se_font-family_select" class="ie_png_fix">
													&nbsp;
												</div>
											</div>
										</td>
										<!-- EOF FONT-FAMILY -->		

										<!-- BOF FONT-COLOR -->	
										<td width="130" valign="top">
											<div id="se_color" class="se_form">
												<div class="se_form_heading">
													<?php
														echo FORM_TITLE_FONT_COLOR;
													?>
												</div>										
												<input class="se_input_box se_color_display ie_png_fix" type="text" id="font_color_display" readonly="readonly" onFocus="blur();" />
												<input class="se_input_box se_color_input ie_png_fix" type="text" maxlength="7" id="font_color" />
											</div>
										</td>
										<!-- EOF FONT-COLOR -->	
									</tr>
									
									<tr height="50">
										<!-- BOF FONT-STYLES -->									
										<td width="95" valign="top" align="left">
											<div id="se_font_styles" class="se_form">
												<div class="se_form_heading">
													<?php
														echo FORM_TITLE_FONT_STYLES;
													?>
												</div>
												<div id="se_text_format">
													<div id="font-weight" class="inactive_font_weight ie_png_fix">&nbsp;</div>
													<div id="font-style" class="inactive_font_style ie_png_fix">&nbsp;</div>
													<div id="text-decoration" class="inactive_text_decoration ie_png_fix">&nbsp;</div>
												</div>
											</div>
										</td>
										<!-- EOF FONT-STYLES -->		

										<!-- BOF TEXT-ALIGN -->	
										<td width="130" valign="top" align="left">
											<div id="se_text-align" class="se_form">
												<div class="se_form_heading">
													<?php
														echo FORM_TITLE_FONT_POSITION;
													?>
												</div>
												<div id="se_text_align">
													<div id="se_text-align_left" class="inactive_align_left ie_png_fix">&nbsp;</div>
													<div id="se_text-align_center" class="inactive_align_center ie_png_fix">&nbsp;</div>
													<div id="se_text-align_right" class="inactive_align_right ie_png_fix">&nbsp;</div>
													<div id="se_text-align_justify" class="inactive_align_justify ie_png_fix">&nbsp;</div>
												</div>
											</div>
										</td>
										<!-- EOF TEXT-ALIGN -->
									</tr>
									
									<tr height="50">
										<!-- BOF FONT-SIZE -->									
										<td valign="top" colspan="2">
											<div id="se_font-size" class="se_form">
												<div class="se_form_heading">
													<?php
														echo FORM_TITLE_FONT_SIZE;
													?>
												</div>
												<input id="font-size" class="se_input_box ie_png_fix" type="text" maxlength="5" />
											</div>
										</td>
										<!-- EOF FONT-SIZE -->									
									</tr>
								</table>
							</div>
						</div>
						<h3 id="se_area_backgrounds" class="se_accord">
							<a href="#" class="se_accord ie_png_fix">
								<?php
									echo ACCORDION_TITLE_BACKGROUND;
								?>
							</a>
						</h3>
						<div id="se_backgrounds">
							<div id="se_background">
								<table class="se_table" width="230" cellspacing="0" cellpadding="0" border="0">
									<!-- BOF BACKGROUND COLOR -->									
									<tr height="50">
										<td width="90" valign="top">
											<div id="se_background-color" class="se_form">
												<div class="se_form_heading">
													<?php
														echo FORM_TITLE_BACKGROUND_COLOR;
													?>
												</div>
												<div style="width:230px">
													<input class="se_input_box se_color_display ie_png_fix" type="text" id="background_color_display" readonly="readonly" onFocus="blur();" />
													<input class="se_input_box se_color_input ie_png_fix" type="text" maxlength="7" id="background_color" />
													<select id="background_tansparency" class="se_input_box">	
														<option id="no_transparency" value="no_transparency" selected="selected"><?php echo FORM_TITLE_BACKGROUND_NO_TRANSPARENCY;?></option>	
														<option id="transparency" value="transparency"><?php echo FORM_TITLE_BACKGROUND_TRANSPARENCY;?></option>
													</select>											
												</div>
											</div>
										</td>
									</tr>
									<!-- EOF BACKGROUND COLOR -->

									<!-- BOF BACKGROUND IMAGE -->									
									<tr height="50">
										<td width="90" valign="top">
											<div id="se_background-image" class="se_form">
												<div class="se_form_heading">
													<?php
														echo FORM_TITLE_BACKGROUND_IMAGE;
													?>
												</div>
												<div class="se_row_info" id="background_image" style="width:230px">										
													<div title="" class="background_image_info" id ="se_background_image_info"> 
														&nbsp;
													</div>
													<div id="se_image_uploader" class="se_image_upload ie_png_fix">
													<?php
														echo BUTTON_TITLE_UPLOAD;
													?>
													</div>
													<div class="se_image_delete ie_png_fix">
													<?php
														echo BUTTON_TITLE_DELETE;
													?>
													</div>
													<div class="se_image_open ie_png_fix">
													<?php
														echo BUTTON_TITLE_PREVIEW;
													?>
													</div>
													<div style="overflow:hidden;clear:both">
														<div class="background_image_info" id="se_background_repeat" style="float:left"> 
															<select id="background_repeat" class="se_input_box">	
																<option id="repeat" value="repeat" selected="selected"><?php echo FORM_TITLE_BACKGROUND_REPEAT;?></option>	
																<option id="repeat-x" value="repeat-x"><?php echo FORM_TITLE_BACKGROUND_REPEAT_X;?></option>
																<option id="repeat-y" value="repeat-y"><?php echo FORM_TITLE_BACKGROUND_REPEAT_Y;?></option>
																<option id="no-repeat" value="no-repeat"><?php echo FORM_TITLE_BACKGROUND_REPEAT_NOT;?></option>
															</select>
														</div>
														<div class="background_image_info" id="se_background_gradient"> 
															&nbsp;
														</div>
													</div>
												</div>
											</div>
										</td>
									</tr>
									<!-- EOF BACKGROUND IMAGE -->									
								</table>
							</div>
						</div>
						<h3 id="se_area_borders" class="se_accord">
							<a href="#" class="se_accord ie_png_fix">
								<?php
									echo ACCORDION_TITLE_POSITION;
								?>
							</a>
						</h3>
						<div>
							<div id="se_border">
								<table width="200" cellspacing="0" cellpadding="0" border="0" class="se_table">
							<?php 
								$t_border = array('', 'top-', 'right-', 'bottom-', 'left-');
								
								for($i = 0; $i < 5; $i++)
								{
									$t_border_name = substr($t_border[$i],0,-1);

									if(empty($t_border_name))
									{
										$t_border_name = 'all';
									}
							?>							
									<tr>
										<td valign="middle" align="left">
											<div class="se_form_heading">
												<?php
													echo constant('FORM_TITLE_BORDER_' . strtoupper(substr($t_border[$i],0,-1)));
												?>
											</div>
										</td>
										<td valign="middle" align="left">
											<input class="se_input_box border_width <?php echo $t_border_name; ?> ie_png_fix" type="text" maxlength="7" id="border-<?php echo $t_border[$i]; ?>width" />
										</td>
										<td valign="middle" align="left">
											<select class="se_input_box border_style <?php echo $t_border_name; ?>" id="border-<?php echo $t_border[$i]; ?>style">	
												<option value="none">
													<?php echo FORM_TITLE_BORDER_STYLE_NONE; ?>
												</option>	
												<option value="hidden">
													<?php echo FORM_TITLE_BORDER_STYLE_HIDDEN; ?>
												</option>	
												<option value="dotted">
													<?php echo FORM_TITLE_BORDER_STYLE_DOTTED; ?>
												</option>	
												<option value="dashed">
													<?php echo FORM_TITLE_BORDER_STYLE_DASHED; ?>
												</option>	
												<option value="solid">
													<?php echo FORM_TITLE_BORDER_STYLE_SOLID; ?>
												</option>	
												<option value="double">
													<?php echo FORM_TITLE_BORDER_STYLE_DOUBLE; ?>
												</option>	
												<option value="groove">
													<?php echo FORM_TITLE_BORDER_STYLE_GROOVE; ?>
												</option>	
												<option value="ridge">
													<?php echo FORM_TITLE_BORDER_STYLE_RIDGE; ?>
												</option>	
												<option value="inset">
													<?php echo FORM_TITLE_BORDER_STYLE_INSET; ?>
												</option>	
												<option value="outset">
													<?php echo FORM_TITLE_BORDER_STYLE_OUTSET; ?>
												</option>
											</select>
										</td>
										<td valign="middle" align="left">
											<input class="se_input_box se_color_input border_color <?php echo $t_border_name; ?> ie_png_fix" type="text" maxlength="7" id="border-<?php echo $t_border[$i]; ?>color" />
										</td>
									</tr>
							<?php 
								}
							?>
								</table>
							</div>
						</div>
						<h3 id="se_area_dimensions" class="se_accord">
							<a href="#" class="se_accord ie_png_fix">
								<?php
									echo ACCORDION_TITLE_DIMENSION;
								?>
							</a>
						</h3>
						<div id="se_dimensions">
							<table width="230" cellspacing="0" cellpadding="0" border="0" class="se_table">
								<tr height="30">
									<!-- BOF WIDTH -->									
									<td width="90" valign="top">
										<div id="se_width" class="se_form">
											<div class="se_form_heading">
												<?php
													echo FORM_TITLE_DIMENSIONS_WIDTH;
												?>
												<input class="se_input_box ie_png_fix" type="text" id="width" />
											</div>
											
										</div>
									</td>
									<!-- EOF WIDTH -->									

									<!-- BOF HEIGHT -->									
									<td width="130" valign="top">
										<div id="se_height" class="se_form">
											<div class="se_form_heading">
												<?php
													echo FORM_TITLE_DIMENSIONS_HEIGHT;
												?>
												<input class="se_input_box ie_png_fix" type="text" id="height" />
											</div>	
											
										</div>
									</td>
									<!-- EOF HEIGHT -->									
								</tr>
								
								<!-- BOF MARGIN -->									
								<tr height="60">
									<td valign="top" colspan="2">
										<div id="se_margin" class="se_form">
											<div class="se_form_heading">
												<?php
													echo FORM_TITLE_DIMENSIONS_MARGIN;
												?>
											</div>
											<table width="200" cellspacing="0" cellpadding="0" border="0">
												<tr>
													<td valign="middle" align="left">
														<?php
															echo FORM_TITLE_DIMENSIONS;
														?>
													</td>												
													<td valign="middle" align="left">
														<?php
															echo FORM_TITLE_DIMENSIONS_TOP;
														?>
													</td>												
													<td valign="middle" align="left">
														<?php
															echo FORM_TITLE_DIMENSIONS_RIGHT;
														?>
													</td>
													<td valign="middle" align="left">
														<?php
															echo FORM_TITLE_DIMENSIONS_BOTTOM;
														?>
													</td>
													<td valign="middle" align="left">
														<?php
															echo FORM_TITLE_DIMENSIONS_LEFT;
														?>
													</td>
												</tr>
												<tr>
													<td valign="middle" align="left">
														<input class="se_input_box se_margin ie_png_fix" type="text" id="margin" />
													</td>												
													<td valign="middle" align="left">
														<input class="se_input_box se_margin ie_png_fix" type="text" id="margin-top" />
													</td>												
													<td valign="middle" align="left">
														<input class="se_input_box se_margin ie_png_fix" type="text" id="margin-right" />
													</td>
													<td valign="middle" align="left">
														<input class="se_input_box se_margin ie_png_fix" type="text" id="margin-bottom" />
													</td>
													<td valign="middle" align="left">
														<input class="se_input_box se_margin ie_png_fix" type="text" id="margin-left" />
													</td>
												</tr>
											</table>											
										</div>
									</td>
								</tr>
								<!-- EOF MARGIN -->	
								
								<!-- BOF PADDING -->									
								<tr height="50">
									<td valign="top" colspan="2">
										<div id="se_padding" class="se_form">
											<div class="se_form_heading">
												<?php
													echo FORM_TITLE_DIMENSIONS_PADDING;
												?>
											</div>
											<table width="200" cellspacing="0" cellpadding="0" border="0">
												<tr>
													<td valign="middle" align="left">
														<?php
															echo FORM_TITLE_DIMENSIONS;
														?>
													</td>												
													<td valign="middle" align="left">
														<?php
															echo FORM_TITLE_DIMENSIONS_TOP;
														?>
													</td>												
													<td valign="middle" align="left">
														<?php
															echo FORM_TITLE_DIMENSIONS_RIGHT;
														?>
													</td>
													<td valign="middle" align="left">
														<?php
															echo FORM_TITLE_DIMENSIONS_BOTTOM;
														?>
													</td>
													<td valign="middle" align="left">
														<?php
															echo FORM_TITLE_DIMENSIONS_LEFT;
														?>
													</td>
												</tr>
												<tr>
													<td valign="middle" align="left">
														<input class="se_input_box se_padding ie_png_fix" type="text" id="padding" />
													</td>												
													<td valign="middle" align="left">
														<input class="se_input_box se_padding ie_png_fix" type="text" id="padding-top" />
													</td>												
													<td valign="middle" align="left">
														<input class="se_input_box se_padding ie_png_fix" type="text" id="padding-right" />
													</td>
													<td valign="middle" align="left">
														<input class="se_input_box se_padding ie_png_fix" type="text" id="padding-bottom" />
													</td>
													<td valign="middle" align="left">
														<input class="se_input_box se_padding ie_png_fix" type="text" id="padding-left" />
													</td>
												</tr>
											</table>											
										</div>
									</td>
								</tr>
								<!-- EOF PADDING -->	

							</table>
						</div>
						<h3 id="se_area_mouse_actions" class="se_accord">
							<a href="#" class="se_accord ie_png_fix se_accord" id="se_accord_mouse_actions">
								<?php
									echo ACCORDION_TITLE_MOUSE_ACTIONS;
								?>
							</a>
						</h3>
						<div id="se_mouse_actions">
							<div id="se_box_mouse_actions">
								&nbsp;
							</div>
						</div>
					</div>
				</div>
				<!-- EOF TAB STYLES -->


				<!-- BOF TAB BOXES -->
				<div id="style_edit_tab_2">
					&nbsp;
				</div>					
				<!-- EOF TAB BOXES -->

				<!-- BOF TAB BACKUP -->
				<div id="style_edit_tab_3">
					<div id="se_backup_accordion">
						<h3 class="se_accord">
							<a href="#" class="se_accord ie_png_fix">
								<?php
									echo ACCORDION_TITLE_EXPORT;
								?>
							</a>
						</h3>
						<div>
							<br />
							<div class="se_archive_row_info">
								<?php
									echo INFO_EXPORT;
								?>
								<br />
								<br />
								<?php
									echo TITLE_FILENAME;
								?><input type="text" id="se_export_name" class="se_input_box ie_png_fix" value="<?php echo FORM_TITLE_IMPORT; ?>" />
								<br />
								<div id="se_export_field" class="se_button_large ie_png_fix"><?php echo FORM_TITLE_EXPORT; ?></div>
							</div>
						</div>
						<h3 class="se_accord">
							<a href="#" class="se_accord ie_png_fix">
								<?php
									echo ACCORDION_TITLE_IMPORT;
								?>
							</a>
						</h3>
						<div>
							<br />
							<div class="se_archive_row_info">
								<?php
									echo INFO_UPLOAD;
								?>
								<br />
								<br />
								<div id="se_upload_field" class="se_button_large ie_png_fix"><?php echo FORM_TITLE_IMPORT; ?></div>
							</div>							
						</div>
						<h3 class="se_archive_heading se_accord">
							<a href="#" id="se_archive" class="se_accord ie_png_fix se_archive_heading">
								<?php
									echo ACCORDION_TITLE_ARCHIVE;
								?>
							</a>
						</h3>
						<div id="se_archivizer">
							&nbsp;
						</div>
						<h3 class="se_archive_heading se_accord">
							<a href="#" class="se_accord ie_png_fix">
								<?php
									echo ACCORDION_TITLE_OPTIONS;
								?>
							</a>
						</h3>
						<div>
							<br />
							<div class="se_archive_row_info">
								<div class="se_button_large ie_png_fix se_control" id="se_template_conf">
									<?php
										echo BUTTON_TITLE_TEMPLATE_CONFIGURATION;
									?>								
								</div>
								<div class="se_button_large ie_png_fix se_control" id="se_css_editor">
									<?php
										echo BUTTON_TITLE_CSS_EDITOR;
									?>								
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- EOF TAB BACKUP -->
				<div id="se_message">
					&nbsp;
				</div>

				<div id="style_edit_button" class="style_edit_button_start se_control">
					&nbsp;
				</div>

				<div id="style_edit_boxes" class="style_edit_boxes_start se_control">
					&nbsp;
				</div>

				<div id="style_edit_save_button">
					<div id="style_edit_save_backward" class="se_history">
						&nbsp;
					</div>
					<div id="style_edit_save">
						&nbsp;
					</div>
					<div id="style_edit_save_forward" class="se_history">
						&nbsp;
					</div>
				</div>
			</div>	
		</div>
		<div id="style_edit_expert" class="style_edit_expert_bg_r">			
			<div class="se_side_layer_heading">
				<?php
					echo SIDE_LAYER_EXPERT_HEADING; 
				?>
			</div>
			<div class="se_side_layer_content se_side_layer_break">
				<div id="se_position">
					<?php
						echo FORM_TITLE_EXPERT_POSITION; 
					?>
					<br />
					<select id="position">	
						<option value="static"><?php echo FORM_TITLE_EXPERT_POSITION_STATIC; ?></option>	
						<option value="absolute"><?php echo FORM_TITLE_EXPERT_POSITION_ABSOLUTE; ?></option>	
						<option value="relative"><?php echo FORM_TITLE_EXPERT_POSITION_RELATIVE; ?></option>
						<option value="fixed"><?php echo FORM_TITLE_EXPERT_POSITION_FIXED; ?></option>
					</select>
				</div>
			</div>
			<div class="se_side_layer_content">
				<div id="se_positions">
					<table width="165" cellspacing="0" cellpadding="0" border="0">
						<tr>
							<td valign="middle" align="left">
								<?php
									echo FORM_TITLE_EXPERT_POSITION_TOP; 
								?>
							</td>												
							<td valign="middle" align="left">
								<?php
									echo FORM_TITLE_EXPERT_POSITION_RIGHT; 
								?>
							</td>
							<td valign="middle" align="left">
								<?php
									echo FORM_TITLE_EXPERT_POSITION_BOTTOM; 
								?>
							</td>
							<td valign="middle" align="left">
								<?php
									echo FORM_TITLE_EXPERT_POSITION_LEFT; 
								?>
							</td>
						</tr>
						<tr>
							<td valign="middle" align="left">
								<input class="se_input_box" type="text" id="se_top" />
							</td>												
							<td valign="middle" align="left">
								<input class="se_input_box" type="text" id="se_right" />
							</td>
							<td valign="middle" align="left">
								<input class="se_input_box" type="text" id="se_bottom" />
							</td>
							<td valign="middle" align="left">
								<input class="se_input_box" type="text" id="se_left" />
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="se_side_layer_content">
				<div id="se_background-position">
					<?php
						echo FORM_TITLE_EXPERT_BACKGROUND_POSITION; 
					?>
					<br />
					<input class="se_input_box" type="text" id="background-position" />
				</div>
			</div>
			<div class="se_side_layer_content">
				<table width="165" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td valign="middle" align="left">
							<div id="se_float">
								<?php
									echo FORM_TITLE_EXPERT_FLOAT; 
								?>
								<br />
								<select id="float">	
									<option value="none"><?php echo FORM_TITLE_EXPERT_FLOAT_NONE; ?></option>	
									<option value="left"><?php echo FORM_TITLE_EXPERT_FLOAT_LEFT; ?></option>	
									<option value="right"><?php echo FORM_TITLE_EXPERT_FLOAT_RIGHT; ?></option>
								</select>
							</div>
						</td>												
						<td valign="middle" align="left">
							<div id="se_clear">
								<?php
									echo FORM_TITLE_EXPERT_CLEAR; 
								?>
								<br />
								<select id="clear">	
									<option value="none"><?php echo FORM_TITLE_EXPERT_CLEAR_NONE; ?></option>	
									<option value="left"><?php echo FORM_TITLE_EXPERT_CLEAR_LEFT; ?></option>	
									<option value="right"><?php echo FORM_TITLE_EXPERT_CLEAR_RIGHT; ?></option>
									<option value="right"><?php echo FORM_TITLE_EXPERT_CLEAR_BOTH; ?></option>
								</select>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div class="se_side_layer_content">
				<table width="165" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td valign="middle" align="left">
							<div id="se_overflow">
								<?php
									echo FORM_TITLE_EXPERT_OVERFLOW; 
								?>
								<br />
								<select id="overflow">	
									<option value="visble"><?php echo FORM_TITLE_EXPERT_OVERFLOW_VISIBLE; ?></option>	
									<option value="hidden"><?php echo FORM_TITLE_EXPERT_OVERFLOW_HIDDEN; ?></option>	
									<option value="scroll"><?php echo FORM_TITLE_EXPERT_OVERFLOW_SCROLL; ?></option>	
									<option value="auto"><?php echo FORM_TITLE_EXPERT_OVERFLOW_AUTO; ?></option>	
								</select>
							</div>
						</td>												
						<td valign="middle" align="left">
							<div id="se_display">
								<?php
									echo FORM_TITLE_EXPERT_DISPLAY; 
								?>
								<br />
								<select id="display">	
									<option value="none"><?php echo FORM_TITLE_EXPERT_DISPLAY_NONE; ?></option>	
									<option value="block"><?php echo FORM_TITLE_EXPERT_DISPLAY_BLOCK; ?></option>	
									<option value="inline"><?php echo FORM_TITLE_EXPERT_DISPLAY_INLINE; ?></option>	
									<option value="inline-block"><?php echo FORM_TITLE_EXPERT_DISPLAY_INLINE_BLOCK; ?></option>
									<option value="list-item"><?php echo FORM_TITLE_EXPERT_DISPLAY_LIST_ITEM; ?></option>
								</select>
							</div>
						</td>
					</tr>
				</table>
			</div>

			<div class="se_side_layer_content">
				<table width="165" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td valign="middle" align="left">
							<div id="se_white-space">
								<?php
									echo FORM_TITLE_EXPERT_WHITE_SPACE; 
								?>
								<br />
								<select id="white-space">	
									<option value="normal"><?php echo FORM_TITLE_EXPERT_WHITE_SPACE_NORMAL; ?></option>	
									<option value="pre"><?php echo FORM_TITLE_EXPERT_WHITE_SPACE_PRE; ?></option>	
									<option value="nowrap"><?php echo FORM_TITLE_EXPERT_WHITE_SPACE_NOWRAP; ?></option>	
									<option value="pre-wrap"><?php echo FORM_TITLE_EXPERT_WHITE_SPACE_PRE_WRAP; ?></option>	
									<option value="pre-line"><?php echo FORM_TITLE_EXPERT_WHITE_SPACE_PRE_LINE; ?></option>	
								</select>
							</div>
						</td>												
						<td valign="middle" align="left">
							<div id="se_vertical-align">
								<?php
									echo FORM_TITLE_EXPERT_VERTICAL_ALIGN; 
								?>
								<br />
								<select id="vertical-align">	
									<option value="sub"><?php echo FORM_TITLE_EXPERT_VERTICAL_ALIGN_SUB; ?></option>	
									<option value="super"><?php echo FORM_TITLE_EXPERT_VERTICAL_ALIGN_SUPER; ?></option>	
									<option value="baseline"><?php echo FORM_TITLE_EXPERT_VERTICAL_ALIGN_BASELINE; ?></option>	
									<option value="top"><?php echo FORM_TITLE_EXPERT_VERTICAL_ALIGN_TOP; ?></option>	
									<option value="bottom"><?php echo FORM_TITLE_EXPERT_VERTICAL_ALIGN_BOTTOM; ?></option>	
									<option value="text-top"><?php echo FORM_TITLE_EXPERT_VERTICAL_ALIGN_TEXT_TOP; ?></option>	
									<option value="text-bottom"><?php echo FORM_TITLE_EXPERT_VERTICAL_ALIGN_TEXT_BOTTOM; ?></option>	
								</select>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div class="se_side_layer_content">
				<table width="165" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td valign="middle" align="left">
							<div id="se_line-height">
								<?php
									echo FORM_TITLE_EXPERT_LINE_HEIGHT; 
								?>
								<br />
								<input class="se_input_box" type="text" maxlength="7" id="line-height" />
							</div>
						</td>												
						<td valign="middle" align="left">
							<div id="se_cursor">
								<?php
									echo FORM_TITLE_EXPERT_CURSOR; 
								?>
								<br />
								<select id="cursor">	
									<option style="cursor:auto" value="auto"><?php echo FORM_TITLE_EXPERT_CURSOR_AUTO; ?></option>	
									<option style="cursor:default" value="default"><?php echo FORM_TITLE_EXPERT_CURSOR_DEFAULT; ?></option>	
									<option style="cursor:crosshair" value="crosshair"><?php echo FORM_TITLE_EXPERT_CURSOR_CROSSHAIR; ?></option>	
									<option style="cursor:pointer" value="pointer"><?php echo FORM_TITLE_EXPERT_CURSOR_POINTER; ?></option>	
									<option style="cursor:text" value="text"><?php echo FORM_TITLE_EXPERT_CURSOR_TEXT; ?></option>	
									<option style="cursor:wait" value="wait"><?php echo FORM_TITLE_EXPERT_CURSOR_WAIT; ?></option>	
									<option style="cursor:help" value="help"><?php echo FORM_TITLE_EXPERT_CURSOR_HELP; ?></option>	
									<option style="cursor:move" value="move"><?php echo FORM_TITLE_EXPERT_CURSOR_MOVE; ?></option>	
									<option style="cursor:progress" value="progress"><?php echo FORM_TITLE_EXPERT_CURSOR_PROGRESS; ?></option>	
									<option style="cursor:n-resize" value="n-resize"><?php echo FORM_TITLE_EXPERT_CURSOR_N_RESIZE; ?></option>	
									<option style="cursor:ne-resize" value="ne-resize"><?php echo FORM_TITLE_EXPERT_CURSOR_NE_RESIZE; ?></option>	
									<option style="cursor:e-resize" value="e-resize"><?php echo FORM_TITLE_EXPERT_CURSOR_E_RESIZE; ?></option>	
									<option style="cursor:se-resize" value="se-resize"><?php echo FORM_TITLE_EXPERT_CURSOR_SE_RESIZE; ?></option>	
									<option style="cursor:s-resize" value="s-resize"><?php echo FORM_TITLE_EXPERT_CURSOR_S_RESIZE; ?></option>	
									<option style="cursor:sw-resize" value="sw-resize"><?php echo FORM_TITLE_EXPERT_CURSOR_SW_RESIZE; ?></option>	
									<option style="cursor:w-resize" value="w-resize"><?php echo FORM_TITLE_EXPERT_CURSOR_W_RESIZE; ?></option>	
									<option style="cursor:nw-resize" value="nw-resize"><?php echo FORM_TITLE_EXPERT_CURSOR_NW_RESIZE; ?></option>	
								</select>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div class="se_side_layer_content">
				<table width="165" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td valign="middle" align="left">
							<div id="se_list-style-position">
								<?php
									echo FORM_TITLE_EXPERT_LIST_STYLE_POSITION; 
								?>
								<br />
								<select id="list-style-position">	
									<option value="inside"><?php echo FORM_TITLE_EXPERT_LIST_STYLE_POSITION_INSIDE; ?></option>	
									<option value="outside"><?php echo FORM_TITLE_EXPERT_LIST_STYLE_POSITION_OUTSIDE; ?></option>	
									</select>
							</div>
						</td>												
						<td valign="middle" align="left">
							<div id="se_list-style-type">
								<?php
									echo FORM_TITLE_EXPERT_LIST_STYLE_TYPE; 
								?>
								<br />
								<select id="list-style-type">	
									<option value="none"><?php echo FORM_TITLE_EXPERT_LIST_STYLE_TYPE_NONE; ?></option>	
									<option value="circle"><?php echo FORM_TITLE_EXPERT_LIST_STYLE_TYPE_CIRCLE; ?></option>	
									<option value="square"><?php echo FORM_TITLE_EXPERT_LIST_STYLE_TYPE_SQUARE; ?></option>	
									<option value="disc"><?php echo FORM_TITLE_EXPERT_LIST_STYLE_TYPE_DISC; ?></option>	
									<option value="decimal"><?php echo FORM_TITLE_EXPERT_LIST_STYLE_TYPE_DECIMAL; ?></option>	
									<option value="lower-roman"><?php echo FORM_TITLE_EXPERT_LIST_STYLE_TYPE_LOWER_ROMAN; ?></option>	
									<option value="upper-roman"><?php echo FORM_TITLE_EXPERT_LIST_STYLE_TYPE_UPPER_ROMAN; ?></option>	
								</select>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div id="style_edit_expert_button" class="style_edit_expert_button_r">
				&nbsp;
			</div>
		</div>
		<div id="style_edit_color" class="style_edit_color_bg_r">
			<div id="GMColorizer">
				&nbsp;
			</div>
		</div>
	</div>
	<div id="se_tooltip">
		<div id="se_tooltip_top">
			<?php
				echo TITLE_TOOLBOX; 
			?>
			<div id="se_close_tooltip" class="ie_png_fix">&nbsp;</div>	
		</div>
		<div id="se_tooltip_body">		
			<table width="150" cellspacing="0" cellpadding="2" border="0">
				<tr>
					<td valign="middle" align="left" width="70">
						<?php
							echo FORM_TITLE_UNIT; 
						?>
					</td>												
					<td valign="middle" align="left">
						<select id="se_unit_select" class="se_input_box">	
							<option value="em"><?php	echo FORM_TITLE_UNIT_EM;		?></option>	
							<option value="px"><?php	echo FORM_TITLE_UNIT_PX;		?></option>	
							<option value="%"><?php		echo FORM_TITLE_UNIT_PERCENT;	?></option>
							<option value="pt"><?php	echo FORM_TITLE_UNIT_PT;		?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td valign="middle" align="left" width="70">
						<?php
							echo FORM_TITLE_UNIT_AUTO; 
						?>
					</td>												
					<td valign="middle" align="left">
						<input id="se_unit_auto" type="checkbox" name="" value="" />
					</td>
				</tr>
			</table>
			<div id="se_sliderer">
				<div id="se_slider_pointer"></div>
			</div>
		</div>
	</div>
	<div id="se_monitor_button" class="se_control">
		&nbsp;
	</div>
</div>