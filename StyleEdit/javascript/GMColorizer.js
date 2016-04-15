/* --------------------------------------------------------------
  StyleEdit v2.0
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2010 Gambio GmbH
  --------------------------------------------------------------
*/

/*
*	act used id
*/
var g_id;

/*
*	act used selector
*/
var g_selector;

/*
*	act used attribute
*/
var g_attribute;

/*
*	act used attribute
*/
var g_display;


(function($)
{

	/*
	*	
	*/
	var g_color_gradient = 0;

	/*
	*	containment box of the coordinate pointer  
	*/
	var g_coord_box_pointer		= '#coord_box_pointer';

	/*
	*	containment box of the coordinate layer  
	*/
	var g_coord_box_layer		= '#coord_box_layer';
	
	/*
	*	containment box of the coordinate box  
	*/
	var g_coord_box				= '#coord_box';
	
	/*
	*	containment box of the gradient pointer
	*/	
	var g_gradient_box_pointer	= '#gradient_box_pointer';

	/*
	*	containment box of the gradient layer
	*/
	var g_gradient_box_layer	= '#gradient_box_layer';

	/*
	*	containment box of the gradient
	*/
	var g_gradient_box			= '#gradient_box';

	/*
	*	input box displaying colors
	*/
	var g_color_input_box		= '#colorizer_display';

	/*
	*	input box displaying colors
	*/
	var g_color_box				= '#colorizer_color';

	/*
	*	html structure of the colorizer
	*/
	var g_colorizer_template	= 
									'<div id="color_display">'							+
									'<input class="se_input_box" type="text" maxlength="7" id="colorizer_color" readonly="readonly" disabled="disabled" />' + 	
									'<input class="se_input_box" type="text" maxlength="7" id="colorizer_display" />' +
									'</div>'											+
									''													+
									'<div id="coord_box_layer">'						+ 
									'	<div id="coord_box">'							+
									'		<div id="coord_box_pointer" class="ie_png_fix">'+
									'		</div>'										+
									'	</div>'											+
									'</div>'											+
									''													+
									'<div id="gradient_box_layer">'						+ 
									'	<div id="gradient_box">'						+
									'		<div id="gradient_box_pointer">'			+
									'		</div>'										+
									'	</div>'											+
									'</div>'											+
									''													+
									'<div id="control_box_layer">'						+ 
									'	<div id="control_cancel">'						+
									'		&nbsp;'										+
									'	</div>'											+
									'	<div id="control_apply">'						+
									'		&nbsp;'										+
									'	</div>'											+
									'</div>';
	/*
	*	attach method to jQuery  
	*/
 	$.fn.extend(
	{ 
		/*
		*	plugin definition
		*/
		colorize: function(p_options) 
		{
			/*	set default values	*/
			var v_defaults = 
			{
				hex_color:	'',
				id:			'',
				selector:	'',
				attribute:	'',
				display:	''
			};
			
			/* set selector & attribute */
			g_selector	= p_options.selector;
			g_id		= p_options.id;
			g_attribute	= p_options.attribute;
			g_display	= p_options.display;
			
			/*	extend defaults with main options	*/
			var v_options = $.extend(v_defaults, p_options);

			/*	iterate & reformat each matched element	*/
    		return this.each(
				function(p_event) 
				{
					/*	build element specific options	*/
					var v_element_opt = v_options;
					
					/*	create colorizer */
					$(this).create(v_element_opt, p_event);
    			}
			);
    	},

		/*
		*	create and load elements
		*/
		create: function(p_element_opt, p_event) 
		{	
			/* to do: move to mouseover e.g. */
			//gmStyleEditToolBox.handle_mouse_selection(false);

			if(p_element_opt.hex_color == '' || p_element_opt.hex_color == null || p_element_opt.hex_color == undefined)
			{
				p_element_opt.hex_color = '#ffcc00';
			}

			/* attach html structure*/
			$(this).html(g_colorizer_template);

			/* handle actions */
			gmStyleEditToolBox.init_side_layer('#control_cancel',	'#style_edit_color',	'style_edit_color_bg_l',	'style_edit_color_bg_r',	p_event, 1050);
						
			$('#control_apply').click(
				function()
				{
					var t_hex = $(g_color_input_box).val();
					$(g_display).val(t_hex);
					$(g_display + '_display').css('backgroundColor', t_hex);			
					gmStyleEditHandler.init_background_transparency('');
					
					if(g_display.indexOf('border-color') != -1)
					{
						gmStyleEditHandler.batch_border_update(g_attribute, t_hex);
						gmStyleEditHandler.batch_border_update('border-style');
						gmStyleEditHandler.batch_border_update('border-width');

						$('.border_color').val(t_hex);
					}
					else
					{
						gmStyleEditHandler.change_style(g_attribute, t_hex);
					}
				}
			);

			/* ini pointer position */
			$(this).set_pointer_start_pos(p_element_opt.hex_color);
			
			/* make pointer draggable by mouse	*/
			$(this).pointer();

			$(g_color_input_box).change(function()
			{
				$(this).set_pointer_start_pos($(g_color_input_box).val());
			});
		},

		/*
		*	make pointer draggable by mouse action
		*/
		pointer: function()
		{
			var t_ptr_abs_pos = new Array(2);	
			
			if (navigator.appVersion.match(/MSIE [0-6]\./)) 
			{
				gmStyleEditToolBox.ie_fix_png(g_gradient_box_pointer);
				gmStyleEditToolBox.ie_fix_png(g_coord_box_pointer);
			}
		
			/* coordinate */
			$(g_coord_box).mousedown(
				function(p_event)
				{
					$('#style_edit_layer').draggable({'disable':true});
					t_ptr_abs_pos = $(this).set_pointer_pos(g_coord_box, g_coord_box_pointer, p_event.pageX, p_event.pageY);
					$(this).set_color(t_ptr_abs_pos['x'], t_ptr_abs_pos['y']);		
					
					$(g_coord_box).mousemove(
						function(p_event)
						{
							t_ptr_abs_pos = $(this).set_pointer_pos(g_coord_box, g_coord_box_pointer, p_event.pageX, p_event.pageY);
							$(this).set_color(t_ptr_abs_pos['x'], t_ptr_abs_pos['y']);					
						}
					);
					
					$(g_coord_box).mouseup(
						function(p_event)
						{
							$('#style_edit_layer').draggable('enable');
							$(g_coord_box).unbind('mousemove');
						}
					);	
				}
			);	
			
			$(this).stop_draggable(g_coord_box);

			if (navigator.appVersion.match(/MSIE [0-6]\./)) 
			{
				$(g_coord_box).mouseup(
					function(p_event)
					{
						$(g_coord_box).unbind('mousemove');
					}
				);		
			}			

			$(g_coord_box_pointer).draggable(
				{
					stop: function(event, p_ui)
					{
						$('#style_edit_layer').draggable('enable');
					},
					drag: function(event, p_ui) 
					{ 
						$('#style_edit_layer').draggable('disable');
						$(this).set_color(p_ui.position.left, p_ui.position.top);
					},
					containment: g_coord_box_layer
				}
			);			
			
			/* gradient */
			$(g_gradient_box).mousedown(
				function(p_event)
				{
					$('#style_edit_layer').draggable('disable');
					t_ptr_abs_pos = $(this).set_pointer_pos(g_gradient_box, g_gradient_box_pointer, p_event.pageX, p_event.pageY);						
					$(this).set_color_gradient(t_ptr_abs_pos['x']);

					$(g_gradient_box).mousemove(
						function(p_event)
						{
							t_ptr_abs_pos = $(this).set_pointer_pos(g_gradient_box, g_gradient_box_pointer, p_event.pageX, p_event.pageY);
							$(this).set_color_gradient(t_ptr_abs_pos['x']);					
						}
					);
					
					$(g_gradient_box).mouseout(
						function(p_event)
						{
							$('#style_edit_layer').draggable('enable');
							$(g_gradient_box).unbind('mousemove');
						}
					);	
				}
			);	

			$(this).stop_draggable(g_gradient_box);

			$(g_gradient_box_pointer).draggable(
				{
					stop: function(event, p_ui)
					{
						$('#style_edit_layer').draggable('enable');
					},
					drag: function(event, p_ui) 
					{ 
						$('#style_edit_layer').draggable('disable');
						axis: 'x',
						$(this).set_color_gradient(p_ui.position.left);
					},
					containment: g_gradient_box_layer
				}
			);
		},			
		
		/*
		*	stop draggable on mouseout
		*/
		stop_draggable: function(p_box)
		{			
			$(p_box).mouseout(
				function(p_event)
				{	
					var t_x			= p_event.pageX - $(p_box).offset().left;
					var t_y			= p_event.pageY - $(p_box).offset().top;
					var t_width		= $(p_box).outerWidth();
					var t_height	= $(p_box).outerHeight();
					if(t_x < 0 || t_x > t_width || t_y < 0 || t_y > t_height)
					{						
						$(p_box).draggable('disable');
						$('#style_edit_layer').draggable('enable');
						$(p_box).unbind('mousemove');
					}					
				}
			);
		},

		/*
		*	set color gradient
		*/
		set_color_gradient: function(p_position_x)
		{			
			g_color_gradient = Math.ceil(p_position_x/2*3.6);
			var t_hex = $(this).hsv_to_hex(g_color_gradient, 1, 1);			
			$(this).display_color(t_hex);
			$(g_coord_box).css('backgroundColor', '#' + t_hex);
		},

		/*
		*	set position of pointer
		*/
		set_color: function(p_eventX, p_eventY)
		{
			var t_coord_width	= $(g_coord_box).outerWidth();					
			var t_coord_height	= $(g_coord_box).outerHeight();	
			var t_hex = $(this).hsv_to_hex(g_color_gradient, (p_eventX/t_coord_width), (p_eventY/t_coord_height));								
			$(this).display_color(t_hex);
		},		

		/*
		*	set start position of pointer
		*/
		set_pointer_start_pos: function(p_hex)
		{
			/* set rgb -> hsv */
			var t_rgb	= $(this).hex_to_rgb(p_hex);				
			var t_hsv	= $(this).rgb_to_hsv(t_rgb[0]*255, t_rgb[1]*255, t_rgb[2]*255);	

			/* set coordinate */
			$(g_coord_box_pointer).css('top', (t_hsv[2]*200));
			$(g_coord_box_pointer).css('left',	(t_hsv[1]*200));			
			
			/* set gradient hex */
			var t_hex = $(this).hsv_to_hex(t_hsv[0], 1, 1);					
			$(g_gradient_box_pointer).css('left',	(t_hsv[0]*2/3.6));

			g_color_gradient = t_hsv[0];
			t_hex = $(this).hsv_to_hex(g_color_gradient, 1, 1);			
			$(g_coord_box).css('backgroundColor', '#' + t_hex);

			/* ini pointer position */
			$(this).display_color(p_hex);
		},

		/*
		*	set position of pointer
		*/
		set_pointer_pos: function(p_box, p_pointer, p_eventX, p_eventY)
		{
			var t_ptr_abs_pos	= new Array(2);	

			t_ptr_abs_pos['x']	= (p_eventX - $(p_box).offset().left);
			t_ptr_abs_pos['y']	= (p_eventY - $(p_box).offset().top);				

			if(t_ptr_abs_pos['x'] > 198)
			{
				t_ptr_abs_pos['x'] = 200;
			}

			if(t_ptr_abs_pos['x'] < 0)
			{
				t_ptr_abs_pos['x'] = 0;
			}

			if(t_ptr_abs_pos['y'] > 198)
			{
				t_ptr_abs_pos['y'] = 200;
			}

			if(t_ptr_abs_pos['y'] < 0)
			{
				t_ptr_abs_pos['y'] = 0;
			}

			$(p_pointer).css('left', t_ptr_abs_pos['x'] + 'px');

			if(p_box == g_coord_box)
			{
				$(p_pointer).css('top', t_ptr_abs_pos['y'] + 'px');
			}

			return t_ptr_abs_pos;
		},

		/*
		*	display color
		*/
		display_color: function(p_hex)
		{			
			if (p_hex.length == 6)
			{
				p_hex = "#" + p_hex;
			} 

			$(g_color_input_box).val(p_hex);
			$(g_color_box).css('backgroundColor', p_hex);

			//alert(navigator.appName);
			if (!navigator.appVersion.match(/MSIE [0-8]\./) && navigator.appName.toLowerCase().indexOf('opera') == -1) 
			{	
			//$(gmStyleEditHandler.v_actor).css(g_attribute, p_hex);
			}
		},

		/*
		* -> COLOR CALCULATING FUNCTIONS
		*
		* convert hsv to rgb
		*/
		hsv_to_hex: function(h, s, v)
		{
			if(!h || h >= 357 || h <= 2) 
			{
				h = 0;				
			}

			if(s >= 1) 
			{
				s=1;
			}

			if(s < 0) 
			{
				s=0;
			}

			if(v >= 1) 
			{
				v=1;
			}	
			
			if(v < 0) 
			{
				v=0;
			}
			
			var rgb = new Array(3);
			var i, f, p, q, t;
			
			if( s == 0 ) 
			{
				r = g = b = v;
				rgb[0] = v;
				rgb[1] = v;
				rgb[2] = v;				
				return $(this).rgb_to_hex(rgb[0],rgb[1],rgb[2]);
			} 
			else 
			{
				h /= 60;			
				i = Math.floor(h);
				f = h - i;
				p = v * ( 1 - s );
				q = v * ( 1 - s * f );
				t = v * ( 1 - s * ( 1 - f ) );
				
				switch( i ) {
					case 0:						
						rgb[0] =v;
						rgb[1] =t;
						rgb[2] =p;
					break;
					case 1:
						rgb[0] =q;
						rgb[1] =v;
						rgb[2] =p;
					break;
					case 2:
						rgb[0] =p;
						rgb[1] =v;
						rgb[2] =t;
					break;
					case 3:
						rgb[0] =p;
						rgb[1] =q;
						rgb[2] =v;
					break;
					case 4:
						rgb[0] =t;
						rgb[1] =p;
						rgb[2] =v;
					break;
					default:		
						rgb[0] =v;
						rgb[1] =p;
						rgb[2] =q;
					break;
				}
			  return $(this).rgb_to_hex(rgb[0],rgb[1],rgb[2]);
			}
		},

		/*
		* convert rgb to hex
		*/
		rgb_to_hex: function(r,g,b)
		{	
			return $(this).to_hex(r*255) + $(this).to_hex(g*255) + $(this).to_hex(b*255);
		},

		/*
		* convert to hex
		*/
		to_hex: function(color) 
		{
			color=parseInt(color).toString(16);
			return color.length<2?"0"+color:color;
		},
		
		/*
		* convert hex to rgb
		*/
		hex_to_rgb: function(hex) 
		{			
			if (hex.length == 7) 
			{
				return [parseInt('0x' + hex.substring(1, 3)) / 255,
				parseInt('0x' + hex.substring(3, 5)) / 255,
				parseInt('0x' + hex.substring(5, 7)) / 255];
			} 
			else if (hex.length == 4)
			{
				return [parseInt('0x' + hex.substring(1, 2)) / 15,
				parseInt('0x' + hex.substring(2, 3)) / 15,
				parseInt('0x' + hex.substring(3, 4)) / 15];
			} 
			else 
			{
				return [255,255,255];
			}
		},

		/*
		* convert rgb to hsv
		*/
		rgb_to_hsv: function(r, g, b) 
		{
			var hsv = new Array(3);
			var min, max, delta, h, s, v;

			min = Math.min(Math.min(r,g), b);
			max = Math.max(Math.max(r,g), b);
			v = max/255;				

			delta = max-min;

			if(max!=0)
			{
				s = delta/max;	
			} 
			else 
			{					
				s = 0;
				h = -1;
				hsv[0] = h;
				hsv[1] = s;
				hsv[2] = v;
				return hsv;
			}

			if(r==max) 
			{
				h = (g-b)/delta;
			} 
			else if(g==max) 
			{
				h = 2+((b-r)/delta);
			} 
			else 
			{
				h = 4+((r-g)/delta);
			}

			h = h*60;

			if(h<0) 
			{
				h = h+360;
			}

			hsv[0] = h;
			hsv[1] = s;
			hsv[2] = v;

			return hsv;
		}
	});
})(jQuery);