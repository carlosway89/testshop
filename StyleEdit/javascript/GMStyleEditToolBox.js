/* --------------------------------------------------------------
  StyleEdit v2.0
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  --------------------------------------------------------------
*/

function GMStyleEditToolBox()
{
	/*
	*
	*/
	var v_se								= '#style_edit_layer';
	
	/*
	*
	*/
	var v_se_colorizer						= '#style_edit_color';
	
	/*
	*
	*/
	var v_se_expert_mode					= '#style_edit_expert';
	
	/*
	*
	*/
	var v_side_layer_pos_x_active	= 224;
	
	/*
	*
	*/
	var v_side_layer_pos_x_inactive	= 30;
	
	/*
	*
	*/
	var v_side_layer_status = new Array();

	/*
	*
	*/
	var v_se_w_normal;
	
	/*
	*
	*/
	var v_se_w_collapsed;	
	

	/*
	*
	*/
	this.ie_fix_png = function(p_element) 
	{
		DD_belatedPNG.fix(p_element);
	}
	
	/*
	*
	*/
	this.set_se_w_normal = function() 
	{
		v_se_w_normal = $(v_se).outerWidth();
	}	

	/*
	*
	*/
	this.get_se_w_normal = function() 
	{
		return v_se_w_normal;
	}	

	/*
	*
	*/
	this.set_se_w_collapsed = function() 
	{
		v_se_w_collapsed = $(v_se).outerWidth() + v_side_layer_pos_x_active;
	}
	
	/*
	*
	*/
	this.get_se_w_collapsed = function() 
	{
		return v_se_w_collapsed;
	}	

	/*
	*
	*/
	this.set_side_layer_status = function(p_layer, p_status) 
	{		
		v_side_layer_status[p_layer] = p_status;
	}
	
	/*
	*
	*/
	this.get_side_layer_status = function(p_layer) 
	{
		return v_side_layer_status[p_layer];
	}

	/*
	*
	*/
	this.get_side_layer_pos_x = function(p_layer)
	{
		var t_pos_x = v_side_layer_pos_x_active;
		if(gmStyleEditToolBox.get_side_layer_status(p_layer) == false)
		{
			t_pos_x = v_side_layer_pos_x_inactive;
		}
		return t_pos_x;
	}	
	
	/*
	*
	*/
	this.get_expert_button_class = function(p_old_class)
	{
		var t_button_class;

		if(p_old_class == 'style_edit_expert_button_r_active' || p_old_class == 'style_edit_expert_button_r')
		{
			t_button_class = 'style_edit_expert_button_r_active';
			if(gmStyleEditToolBox.get_side_layer_status(v_se_expert_mode) == false)
			{
				t_button_class = 'style_edit_expert_button_r';
			}
		}
		else
		{
			t_button_class = 'style_edit_expert_button_l_active';
			if(gmStyleEditToolBox.get_side_layer_status(v_se_expert_mode) == false)
			{
				t_button_class = 'style_edit_expert_button_l';
			}
		}
		return t_button_class;
	}

	/*
	*
	*/
	this.init = function(p_event)
	{
		var t_url = window.location.href;
		if(t_url.search('#') !== -1)
		{
			t_url = t_url.substr(0, t_url.search('#'));
		}

		/* load toolbox */
		var t_style_edit_layer = jQuery.ajax(
			{
				data:	'module=se_toolbox&current_template=' + style_edit_config_CURRENT_TEMPLATE + '&url=' + encodeURIComponent(t_url) + '&token='+style_edit_sectoken,
				url:	'StyleEdit/style_edit_request.php',
				type:	"POST",
				async:	false
			}
		).responseText;

		$(v_se).remove();
		$('body').append(t_style_edit_layer);

		gmStyleEditToolBox.set_se_w_normal();
		gmStyleEditToolBox.set_se_w_collapsed();



		$(v_se).draggable(
			{
                stop: function(p_event, ui)
				{
					gmStyleEditToolBox.switch_layers(ui);
				},
				cursor:			'move',
				containment:	'document'
			}
		);

		gmStyleEditHandler.ini(v_se);

        /* ie6 hack for transparent pngs and invisible select boxes */
		if (navigator.appVersion.match(/MSIE [0-6]\./)) 
		{		
			$('.wrap_shop select').hide();
			gmStyleEditToolBox.ie_fix_png('#style_edit_background');
			gmStyleEditToolBox.ie_fix_png('#style_edit_foreground');
			gmStyleEditToolBox.ie_fix_png('#style_edit_color');
			gmStyleEditToolBox.ie_fix_png('#style_edit_expert');
			gmStyleEditToolBox.ie_fix_png('#style_edit_tabs ul');
		}

		v_side_layer_status['#style_edit_expert']	= false;
		v_side_layer_status['#style_edit_color']	= false;
		
		
		gmStyleEditToolBox.init_side_layer('#se_boxes, #se_backup, #style_edit_button',			'#style_edit_color',	'style_edit_color_bg_l',	'style_edit_color_bg_r',	p_event, 1050);
		gmStyleEditToolBox.init_side_layer('#se_boxes, #se_backup, #style_edit_button',			v_se_expert_mode,		'style_edit_expert_bg_l',	'style_edit_expert_bg_r',	p_event, 1060);	
		gmStyleEditToolBox.init_side_layer('.se_color_display',			'#style_edit_color',	'style_edit_color_bg_l',	'style_edit_color_bg_r',	p_event, 1050);
		gmStyleEditToolBox.init_side_layer('.se_color_input',			'#style_edit_color',	'style_edit_color_bg_l',	'style_edit_color_bg_r',	p_event, 1050);
		gmStyleEditToolBox.init_side_layer('#style_edit_expert_button', v_se_expert_mode,		'style_edit_expert_bg_l',	'style_edit_expert_bg_r',	p_event, 1060);	
	}

	/*
	*
	*/
	this.switch_layers = function(p_ui)
	{
		var t_element_pos_left	= p_ui.offset.left + gmStyleEditToolBox.get_se_w_collapsed();
		var t_browser_width		= $(document).width();
		var t_pos_x;
		var t_pos_x_colorizer;
		var t_element_expert;
		var t_element_colorizer;
		var t_old_class;
		var t_new_class;
		
		// switch to left side
		if(t_element_pos_left > t_browser_width)
		{		
			t_pos_x_expert_mode		= (gmStyleEditToolBox.get_side_layer_pos_x(v_se_expert_mode) * -1) +7;
			t_pos_x_colorizer		= (gmStyleEditToolBox.get_side_layer_pos_x(v_se_colorizer)	* -1) +7;
			t_element_expert		= '.style_edit_expert_bg_l';			
			t_element_colorizer		= '.style_edit_color_bg_l';			
			t_old_class				= $('#style_edit_expert_button').attr('class');
			t_new_class				= t_old_class.split('_r').join('_l');			

			gmStyleEditToolBox.switch_class('#style_edit_expert', 'style_edit_expert_bg_r', 'style_edit_expert_bg_l');
			gmStyleEditToolBox.switch_class('#style_edit_color', 'style_edit_color_bg_r', 'style_edit_color_bg_l');
		}
		// switch to right side
		else
		{
			t_pos_x_expert_mode		= gmStyleEditToolBox.get_side_layer_pos_x(v_se_expert_mode);
			t_pos_x_colorizer		= gmStyleEditToolBox.get_side_layer_pos_x(v_se_colorizer);
			t_element_expert		= '.style_edit_expert_bg_r';
			t_element_colorizer		= '.style_edit_color_bg_r';		
			t_old_class				= $('#style_edit_expert_button').attr('class');
			t_new_class				= t_old_class.split('_l').join('_r');

			gmStyleEditToolBox.switch_class('#style_edit_expert', 'style_edit_expert_bg_l', 'style_edit_expert_bg_r');
			gmStyleEditToolBox.switch_class('#style_edit_color', 'style_edit_color_bg_l', 'style_edit_color_bg_r');
		}
		
		/* add new css-style/position*/
		gmStyleEditToolBox.switch_class('#style_edit_expert_button', t_old_class, t_new_class);
		$(t_element_colorizer).css("left", t_pos_x_colorizer + "px");
		$(t_element_expert).css("left", t_pos_x_expert_mode + "px");
	}

	/*
	*
	*/
	this.init_side_layer = function(p_button, p_layer, p_layer_l, p_layer_r, p_event, p_zindex)
	{
		$(p_button).click(
			function(p_event) 
			{
				var t_layer_class = $(p_layer).attr("class");
				var t_pos_x;
				
				$('.se_side_layer_content').hide();

				/* set layer in/active */
				if(gmStyleEditToolBox.get_side_layer_status(p_layer) == false)
				{
					$(p_layer).css('z-index', 1070);
					gmStyleEditToolBox.set_side_layer_status(p_layer, true);
				}
				else
				{
					if(p_button.indexOf('se_color_input') == -1 && p_button.indexOf('se_color_display') == -1)
					{						
						$(p_layer).css('z-index', p_zindex);
						gmStyleEditToolBox.set_side_layer_status(p_layer, false);
					}
				}

				if(p_button == '#se_boxes, #se_backup, #style_edit_button')
				{
					gmStyleEditToolBox.set_side_layer_status(p_layer, false);
					$(p_layer).css('z-index', p_zindex);
				}	
				
				/* switch left layer position */
				if(t_layer_class == p_layer_r)
				{
					t_pos_x = gmStyleEditToolBox.get_side_layer_pos_x(p_layer);
				}
				else if(t_layer_class == p_layer_l)
				{
					t_pos_x = (gmStyleEditToolBox.get_side_layer_pos_x(p_layer) * -1) +7;
				}

				/* animate new position */
				$("." + t_layer_class).animate(
					{ 	
						left:  t_pos_x + "px"
					},
					400,
					'',
					gmStyleEditToolBox.animate_side_layer(p_layer, p_button)
				);				
			}
		);		
	}

	/*
	*	function to animate the side layers 
	*/
	this.animate_side_layer = function(p_layer, p_button)
	{
		/* handle expert mode animation	*/
		if(p_layer == '#style_edit_expert')
		{
			if(gmStyleEditToolBox.get_side_layer_status(p_layer) == true)
			{
				setTimeout(
					function()
					{
						if(gmStyleEditHandler.v_edit == true)
						{
							$('.se_side_layer_content').show(); 
						}						
					}, 400
				);				
			}
			else
			{
				$('.se_side_layer_content').hide(); 
			}
			var t_old_class	= $('#style_edit_expert_button').attr('class');
			var t_new_class	= gmStyleEditToolBox.get_expert_button_class(t_old_class);
			gmStyleEditToolBox.switch_class('#style_edit_expert_button', t_old_class, t_new_class);			
		}
		else
		{
			if(gmStyleEditToolBox.get_side_layer_status(p_layer) == true)
			{
				$('#style_edit_color').show();
				/* ie 6 hack */
				if(gmStyleEditHandler.v_expert_check == true && navigator.appVersion.match(/MSIE [0-6]\./))
				{
					$(v_se_expert_mode).hide();
				}
			}
			else
			{		
				setTimeout(
					function()
					{
						$('#style_edit_color').hide();
					}, 400
				);

				/* ie 6 hack */
				if(gmStyleEditHandler.v_expert_check == true && navigator.appVersion.match(/MSIE [0-6]\./))
				{
					$(v_se_expert_mode).show();
				}
			
				if(gmStyleEditToolBox.get_side_layer_status(v_se_expert_mode) == true)
				{
					$('.se_side_layer_content').show(); 
				}				
			}
		}
	}

	/*
	*	helper
	*/
	this.switch_class = function(p_element, p_old_class, p_new_class)
	{
		$(p_element).removeClass(p_old_class);
		$(p_element).addClass(p_new_class);
	}

	/*
	*	function to disable mouse selection
	*	@param boolean p_status 
	*/
	this.handle_mouse_selection = function(p_status)
	{
		document.onselectstart = function()
		{ 
			return p_status; 
		} 

		document.onmousedown = function() 
		{ 
			return p_status; 
		}
	}

	/*
	*	function to hide expert layer if it is not in use  
	*/
	this.hide_expert_layer = function()
	{		
		var t_pos_inactive = 30;
		if(gmStyleEditHandler.v_expert_check == true)
		{
			$('#style_edit_expert').show();				
		}
		else
		{
			$('.se_side_layer_content').hide(); 

			gmStyleEditToolBox.set_side_layer_status('#style_edit_expert', false);

			var t_layer_class = $('#style_edit_expert').attr("class");
			
			if(t_layer_class.indexOf('bg_r') != -1)
			{
				t_pos_x = t_pos_inactive;
			}
			else if(t_layer_class.indexOf('bg_l') != -1)
			{
				t_pos_x = (t_pos_inactive * -1) +7;
			}

			/* animate new position */
			$('#style_edit_expert').animate(
				{ 	
					left:  t_pos_x + "px"
				},
				400,
				''					
			);	
			setTimeout(
				function()
				{
					$('#style_edit_expert').hide()
				}, 400
			);								
		}
	}
}