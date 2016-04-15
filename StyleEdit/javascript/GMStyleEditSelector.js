/* --------------------------------------------------------------
  StyleEdit v2.0
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2010 Gambio GmbH
  --------------------------------------------------------------
*/

function GMStyleEditSelector()
{
	var coo_this						= this;
	var style_count;
	var style_edit_active 				= false;
	var hover_active 					= false;	
	this.hover_css_orig_background_img	= new Object();
	var hover_css_orig_background		= new Object();
	var hover_css_orig_front 		 	= new Object();
	var hover_css_orig_cursor		 	= new Object();
	
	var activation_function = function(){};	
	
	this.update_css_backup_background = function(style_name, style_value) 
	{
		set_css_backup_background(style_name, style_value);
		if(fb)console.log('updated: ' + style_name +' '+ style_value);
	}
	
	this.set_css_backup_background = function(style_name, style_value) 
	{
		hover_css_orig_background[style_name] = style_value;
	}

	function get_css_backup_background(style_name) 
	{		
		if(gmStyleEditHandler.v_actor == style_name && gmStyleEditHandler.v_act_changes['background-color'] != undefined)
		{
			return gmStyleEditHandler.v_act_changes['background-color'];
		}
		else
		{
			return hover_css_orig_background[style_name];
		}		
	}
	
	function set_css_backup_background_img(style_name, style_value) 
	{		
		if(is_css_image(style_value))
		{			
			var t_image_src = style_value.split('/');
			if(t_image_src != undefined)
			{				
				var t_image_name = t_image_src[t_image_src.length-1];
				t_image_name = t_image_name.replace(')', '');
				t_image_name = t_image_name.replace('"', '');
				t_image_name = t_image_name.replace("'", '');
				
				if(style_value.indexOf(style_edit_config_GRADIENTS_DIR) != -1)
				{
					coo_this.hover_css_orig_background_img[style_name] = "url(" + style_edit_config_GRADIENTS_DIR_RELATIVE + t_image_name + ")";				
				}
				else
				{
					coo_this.hover_css_orig_background_img[style_name] = "url(" + style_edit_config_BACKGROUNDS_DIR + t_image_name + ")";							
				}	
			}		
		}
	}

	function get_css_backup_background_img(style_name) 
	{
		if(gmStyleEditHandler.v_actor == style_name && gmStyleEditHandler.v_act_bg_img != null)
		{
			coo_this.hover_css_orig_background_img[style_name] = gmStyleEditHandler.v_act_bg_img;
			return gmStyleEditHandler.v_act_bg_img;
		}
		else
		{
			return coo_this.hover_css_orig_background_img[style_name];
		}
	}	

	this.delete_css_backup_background_img = function(style_name, style_value) 
	{
		coo_this.hover_css_orig_background_img[style_name] = style_value;
	}


	function is_css_image(p_image) 
	{
		if(p_image.indexOf('css.php') == -1 && p_image != '' && typeof(p_image) != 'undefined' && p_image != 'none' && p_image != 'url()')
		{
			return true;
		}
		else
		{
			return false;
		}
	}	

	function set_css_backup_front(style_name, style_value) 
	{
		hover_css_orig_front[style_name] = style_value;
	}

	function get_css_backup_front(style_name) 
	{
		return hover_css_orig_front[style_name];
	}

	function set_css_backup_cursor(style_name, style_value) 
	{
		hover_css_orig_cursor[style_name] = style_value
	}

	function get_css_backup_cursor(style_name) 
	{
		return hover_css_orig_cursor[style_name];
	}
	
	this.set_style_edit_active = function(style_edit_on) 
	{
		style_edit_active = style_edit_on;
	}

	this.get_style_edit_active = function() 
	{
		return style_edit_active;
	}
	 
	this.set_hover_active = function(hover_active_on) 
	{
		hover_active = hover_active_on;
	}

	this.get_hover_active = function() 
	{
		return hover_active;
	}
	 
	this.set_activation_function = function(fn) 
	{
		activation_function = fn;
	}

	this.run_activation_function = function() 
	{
		$('#se_message').html('Lade...');
		setTimeout
		(
			function()
			{
				activation_function();
				$('#se_message').html('Styles geladen.');
			},
			1
		); 	
	}		

	this.bnd = function(style_name, background_color, background_image)
	{		
		set_css_backup_background_img(style_name, background_image);
		this.set_css_backup_background(style_name, background_color);

		$(style_name).unbind('click');
		$(style_name).click(function(event) 
		{		
			if(gmslc.get_style_edit_active() == false || gmStyleEditControl.v_se_active_tab != 'se_styles') 
			{
				return true;
			} 
			else
			{
				gmStyleEditHandler.load_styles(style_name);
				$('#style_edit_save_button').show();
				$('#style_edit_step_2').hide();
				return false;
			}
		});

		$(style_name).unbind('mouseover');
		$(style_name).mouseover(function() 
		{
			if(gmStyleEditControl.v_se_active_tab == 'se_styles')
			{
				$('#se_message').html(style_name);
			}

			var t_background_img = get_css_backup_background_img(style_name);			

			if(gmslc.get_hover_active()	   == true)	 return;
			if(gmslc.get_style_edit_active() == false) return;									
						
			set_css_backup_cursor(style_name, $(style_name).css("cursor"));
				
			if(typeof(t_background_img) != 'undefined')
			{
				$(style_name).css('backgroundImage', "none");
			}

			$(style_name).css(
				{
					backgroundColor: 	"yellow",
					cursor:				"crosshair"
				}
			);

			gmslc.set_hover_active(true);

			return false;
		});

		$(style_name).unbind('mouseout');
		$(style_name).mouseout(function() 
		{
			var t_background_img = get_css_backup_background_img(style_name);					

			if(typeof(t_background_img) != 'undefined')
			{
				$(style_name).css('backgroundImage', t_background_img);
			}

			$(style_name).css(
				{
					backgroundColor: 	get_css_backup_background(style_name),
					cursor:				get_css_backup_cursor(style_name)
				}
			);
			
			gmslc.set_hover_active(false);
		});			
	}
}