/* --------------------------------------------------------------
 StyleEdit v2.0
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2014 Gambio GmbH
 --------------------------------------------------------------
 */


var t_attr;

function GMStyleEditHandler(p_gmslc)
{

    /*
     *	@var string containing id of toolbox
     */
    var v_edit = false;

    /*
     *	@var string containing id of toolbox
     */
    var v_expert_check = false;

    /*
     *	@var string containing id of toolbox
     */
    var v_se;

    /*
     *	@var string containing actual text alignment
     */
    var v_act_text_align;

    /*
     *	@var string containing actual selector
     */
    var v_actor;

    /*
     *	@var string containing actual selector
     */
    var v_actor_id;

    /*
     *	@var string containing actual sliding element
     */
    var v_act_bg_img;

    /*
     *	@var string containing actual sliding element
     */
    var v_act_slider;

    /*
     *	@var string containing actual sliding element
     */
    var v_act_slider_change;

    /*
     *	@var string containing actual sliding element
     */
    var v_act_slider_value;

    /*
     *	@var string containing actual sliding element
     */
    var v_slider_value;

    /*
     *	@var string containing actual unit
     */
    var v_act_unit;

    /*
     *	@var string containing actual attribute
     */
    var v_act_attr;

    /*
     *	@var string containing actual attribute
     */
    var v_act_tolerance;

    /*
     *	@var string containing actual attribute
     */
    var v_act_number;

    /*
     *	@var boolean
     */
    var v_global_border;

    /*
     *	@var boolean
     */
    var v_global_margin;

    /*
     *	@var boolean
     */
    var v_global_padding;

    /*
     *	@var object containing actual set of styles
     */
    var v_act_json = null;

    /*
     *	@var object containing actual set of styles
     */
    var v_act_changes;

    /*
     *	ini
     */
    this.ini = function(p_se)
    {
        $('#style_edit_tabs').tabs({
            cache: false
        });

        this.v_se = p_se;

        return;
    }

    /*
     *	function to load the actual set of styles selected by user
     *	@param string p_style_name
     */
    this.load_styles = function(p_style_name, p_type)
    {
        $('#se_message').html(style_edit_img_loading);

        /* reset styles if user changed act selected element without saving */
        if(gmStyleEditHandler.v_act_json != null)
        {
            gmStyleEditHandler.reset_styles(p_style_name);
        }

        if(p_type == 'forward' || p_type == 'backward')
        {
            /* load history styles */
            jQuery.ajax({
				data:		'module=se_load_history&se_history_type=' + p_type + '&token=' + style_edit_sectoken + '&current_template=' + style_edit_config_CURRENT_TEMPLATE, 
				url:		'StyleEdit/style_edit_request.php', 
				dataType:	'json', 
				type:		"POST", 
				async:		false, 
				success:	function(p_styles) 
                {
                    if(p_styles != null)
                    {
                        if(p_styles.count == 0)
                        {
                            $('#style_edit_save_' + p_type).css('visibility', 'hidden');
                        }
                    }
                    gmStyleEditHandler.proceed_styles(p_styles);
                }
            });
        }
        else
        {
            /* load styles */
            jQuery.ajax({
				data:		'module=se_load_styles&se_selector=' + escape(p_style_name) + '&token='+style_edit_sectoken + '&current_template=' + style_edit_config_CURRENT_TEMPLATE, 
				url:		'StyleEdit/style_edit_request.php', 
				dataType:	'json', 
				type:		"POST", 
				async:		false, 
				success:	function(p_styles) 
                {
                    $('#style_edit_save_forward').css('visibility', 'hidden');
                    gmStyleEditHandler.proceed_styles(p_styles);
                }
            });
        }
    }

    /*
     *	function to proceed loaded set of styles
     *	@param string p_styles
     */
    this.proceed_styles = function(p_styles)
    {
        if(p_styles != null)
        {
            if(p_styles.error == 666)
            {
                gmStyleEditControl.control_session(p_styles);
            }
            else
            {
                /* save act set of styles */
				gmStyleEditHandler.v_act_json		= p_styles; 
				gmStyleEditHandler.v_pseudo_class	= p_styles.pseudo_class;				
				gmStyleEditHandler.v_actor			= p_styles.selector;
				gmStyleEditHandler.v_actor_id		= p_styles.id;
				gmStyleEditHandler.v_act_changes	= null;
				gmStyleEditHandler.v_act_changes	= new Array();

				gmStyleEditHandler.v_act_bg_img		= null;
				gmStyleEditHandler.v_expert_check	= false;
				gmStyleEditHandler.v_global_border	= false;
				gmStyleEditHandler.v_global_margin	= false;
				gmStyleEditHandler.v_global_padding	= false;		
				gmStyleEditHandler.v_edit			= true;	
                /* get active accordion pane */
                //var t_active_pane = $("#se_styles_accordion").accordion("option", "active");
                /* reload styles */
                gmStyleEditHandler.reload_styles();
                /* hide inactive accordion elements */
                gmStyleEditHandler.hide_inactive_accordion_elements(p_styles.areas);

                /* load pseudo_classes */
                if(typeof(p_styles.pseudo_classes) != 'undefined')
                {
                    gmStyleEditHandler.proceed_pseudo_classes(p_styles.pseudo_classes);
                }

                $(p_styles.selector).removeAttr('style');
                /* var for the css monitor */
                var t_styles = '';
                /* load act set of styles */
                $.each(gmStyleEditHandler.v_act_json, function(p_attribute, p_value)
                {
                    if(p_value === 'inherit')
                    {
						p_value = $(gmStyleEditHandler.v_act_json.selector).css(p_attribute);
						p_value = String(p_value);
						
                        if(p_value.indexOf('rgb') != -1)
                        {
                            var parts = p_value.match(/rgb\((\d+),\s*(\d+),\s*(\d+)\)/);
                            delete (parts[0]);
							for (var i = 1; i <= 3; ++i) 
                            {
                                parts[i] = parseInt(parts[i]).toString(16);
								if (parts[i].length == 1) parts[i] = '0' + parts[i];
                            }
                            p_value = '#' + parts.join('');

                        }
                    }
                    if(p_attribute != 'id' && p_attribute != 'selector' && p_attribute != 'areas' && p_attribute != 'pseudo_classes' && p_attribute != 'pseudo_class')
                    {
                        //(p_attribute);
                        /* build string for the css monitor */
                        t_styles += "\n" + p_attribute + ": " + p_value + ";";
                        gmStyleEditHandler.change_style(p_attribute, p_value);
                        gmStyleEditHandler.style_switcher(gmStyleEditHandler.v_act_json.id, gmStyleEditHandler.v_act_json.selector, p_attribute, p_value);
                    }
                });

                /* BOF STYLE MONITOR */
                gmStyleMonitor.load_actual_style(gmStyleEditHandler.v_actor, t_styles);
                /* EOF STYLE MONITOR */

                /* hide expert layer */
                gmStyleEditToolBox.hide_expert_layer();

                /* init save buttons */
                gmStyleEditHandler.init_save_buttons();
                $('#se_message').html("<b>" + p_styles.selector + '</b> geladen.');
            }
        }
    }

    /*
     *	function to hide inactive accordion elements
     *	@param array p_style_areas
     */
    this.hide_inactive_accordion_elements = function(p_style_areas)
    {
        var t_accordion_panes = new Array(
            '#se_area_fonts',
            '#se_area_backgrounds',
            '#se_area_borders',
            '#se_area_dimensions',
            '#se_area_mouse_actions'
        );
        ///var t_active_pane = $("#se_styles_accordion").accordion("option", "active");
        var t_help = false;
        $.each(p_style_areas, function(p_area_name, p_area_isset)
        {
            if(p_area_isset === true && t_help === false)
            {
				$("#se_styles_accordion").accordion("option", "active", '#se_area_' + p_area_name);
                t_help = true;
            }
        });
        //$("#se_styles_accordion").accordion("activate", t_accordion_panes[p_active_pane]);
        $.each(p_style_areas, function(p_area_name, p_area_isset)
        {
            $('#se_area_' + p_area_name).show();
        });
        $.each(p_style_areas, function(p_area_name, p_area_isset)
        {
            if(p_area_isset === false)
            {
                $('#se_area_' + p_area_name).hide();
                $('#se_area_' + p_area_name).next().hide();
            }
        });
    }

    /*
     *	function to proceed pseudo classes
     */
    this.proceed_pseudo_classes = function(p_pseudo_classes)
    {
        $('#se_area_mouse_actions').show();
        $('#se_box_mouse_actions').html('');
        for(var t_id in p_pseudo_classes)
        {
            $('#se_box_mouse_actions').append('<div class="se_button_large ie_png_fix se_pseudo_class" title="' + t_id + '">' + p_pseudo_classes[t_id] + '</div>');
        }
        $('.se_pseudo_class').unbind('click');
        $('.se_pseudo_class').click(
            function()
            {
                var style_name = $(this).attr('title');
                //alert(style_name);
                if(typeof(style_name) != 'undefined' && style_name != '')
                {
                    gmStyleEditHandler.load_styles(style_name);
                }
            }
        );
    }
    /*
     *	function to reset styles if user changed act selected element without saving
     */
    this.reload_styles = function()
    {
		$("#se_styles_accordion").accordion().accordion("destroy");
        $("#se_styles_accordion").show();

        $("#se_styles_accordion").accordion();
        $('#se_message').html('');
        $('#se_pseudo_classes').html('');
        $('#style_edit_save').unbind('click');
        $('.se_history').unbind('click');

        var t_styles_array = Array(
            '.style_edit_info',
            '#se_tooltip',
            '#se_font-family',
            '#se_color',
            '#se_font_styles',
            '#se_text-align',
            '#se_font-size',
            '#se_background-color',
            '#se_background-image',
            '#se_border',
            '#se_width',
            '#se_height',
            '#se_padding',
            '#se_margin',
            '#se_position',
            '#se_positions',
            '#se_float',
            '#se_clear',
            '#se_overflow',
            '#se_display',
            '#se_vertical-align',
            '#se_white-space',
            '#se_cursor',
            '#se_list-style-position',
            '#se_list-style-type',
            '#se_background-position',
            '#se_line-height',
            '#se_background_gradient',
            '#se_area_mouse_actions'
        );

        for(var i = 0; i < t_styles_array.length; i++)
        {
            $(t_styles_array[i]).hide();
        }
    }

    /*
     *	function to save styles
     */
    this.save_styles = function()
    {
        /* build params */
        var t_param = '&se_id=' + gmStyleEditHandler.v_actor_id;
        var t_count = 0;
        for(var t_item in gmStyleEditHandler.v_act_changes)
        {
            t_count++;
			t_param += '&se_styles[' +  escape(t_item) + ']=' + escape(gmStyleEditHandler.v_act_changes[t_item]);
        }

        /* save styles */
        var t_request = new Object;
        jQuery.ajax({
			data:		'module=se_save_styles' + t_param + '&token='+style_edit_sectoken + '&current_template=' + style_edit_config_CURRENT_TEMPLATE, 
			url:		'StyleEdit/style_edit_request.php', 
			dataType:	'json', 
			type:		"POST", 
			async:		false, 
			beforeSend:	function() 
            {
                $('#se_message').html(style_edit_img_loading);
            },
			success:	function(p_response) 
            {
				t_request  = p_response; 
            }
        });

        /* check session */
        if(t_request.error == 666)
        {
            gmStyleEditControl.control_session(t_request);
        }
        else
        {
            if(typeof(gmStyleEditHandler.v_act_changes['background-image']) != 'undefined' && t_request.data.file != null)
            {
                gmStyleEditHandler.v_act_changes['background-image'] = t_request.data.file.replace('\\', '');
            }

            if(typeof(gmStyleEditHandler.v_act_changes['background-color']) != 'undefined')
            {
                if(typeof(gmStyleEditHandler.v_pseudo_class) == 'undefined'
                    || (typeof(gmStyleEditHandler.v_pseudo_class) != 'undefined' && gmStyleEditHandler.v_pseudo_class.indexOf(':') == -1))
                {
                    p_gmslc.set_css_backup_background(gmStyleEditHandler.v_actor, gmStyleEditHandler.v_act_changes['background-color']);
                }
            }

            $('#style_edit_save_backward').css('visibility', 'visible');
            $('#style_edit_save_forward').css('visibility', 'hidden');

            // reset actual changes & actual set
			gmStyleEditHandler.v_act_changes		= null;
			gmStyleEditHandler.v_act_changes		= new Array();
            ///gmStyleEditHandler.load_history();
			gmStyleEditHandler.v_act_json			= null;
			gmStyleEditHandler.v_act_json			= new Object();		

            $('#se_message').html(t_request.message);
        }
    }

    /*
     *	function to reset styles if user changed act selected element without saving
     */
    this.init_save_buttons = function()
    {
        $('#style_edit_save').click(
            function()
            {
                gmStyleEditHandler.save_styles();
            }
        );

        $('.se_history').click(
            function()
            {
                var t_id = $(this).attr('id');

                if(t_id == 'style_edit_save_backward')
                {
                    $('#style_edit_save_forward').css('visibility', 'visible');
                    gmStyleEditHandler.load_styles('', 'backward');
                }
                else if(t_id == 'style_edit_save_forward')
                {
                    $('#style_edit_save_backward').css('visibility', 'visible');
                    gmStyleEditHandler.load_styles('', 'forward');
                }
            }
        );
    }

    /*
     *	function to reset styles if user changed act selected element without saving
     *	@param string p_style_name
     */
    this.reset_styles = function(p_style_name)
    {
        gmStyleEditHandler.v_act_bg_img = null;
		if(typeof(gmStyleEditHandler.v_pseudo_class) != 'undefined' && gmStyleEditHandler.v_pseudo_class.indexOf(':') != -1 && 	p_style_name.indexOf(':') == -1)
        {
            gmStyleEditHandler.v_pseudo_class = 'undefined';
            gmStyleEditHandler.load_styles(gmStyleEditHandler.v_actor);
        }

        $.each(gmStyleEditHandler.v_act_json, function(p_attribute, p_value)
        {
            gmStyleEditHandler.change_style(p_attribute, p_value);
        });
    }

    /*
     *	function to load all available font face which are saved in db
     *	@param string p_font
     */
    this.load_font_styles = function(p_font)
    {
        var t_styles = new Object();

        /* load styles */
        jQuery.ajax({
			data:		'module=se_load_font_styles&se_font=' + escape(p_font) + '&token='+style_edit_sectoken + '&current_template=' + style_edit_config_CURRENT_TEMPLATE, 
			url:		'StyleEdit/style_edit_request.php', 
			dataType:	'html', 
			type:		"POST", 
			async:		false, 
            success: function(p_font_styles)
            {
                $('#se_font-family_select').html(p_font_styles);
            }
        });
    }

    /*
     *	function to handle styles
     *	@param string p_id
     *	@param string p_style_name
     *	@param string p_attribute
     *	@param string p_value
     */
    this.style_switcher = function(p_id, p_style_name, p_attribute, p_value)
    {
		switch (p_attribute) 
        {
            case "font-family":
                gmStyleEditHandler.load_font_styles(p_value);

                $('#font-family').change(
                    function()
                    {
                        var t_val = $('#font-family').val();
                        gmStyleEditHandler.change_style(p_attribute, t_val);
                    }
                );
                $('#se_font-family').show();
                break;

            case "color":
                gmStyleEditHandler.init_color('#font_color', p_id, p_style_name, p_attribute, p_value);
                $('#se_color').show();
                break;

            case "font-size":
                gmStyleEditHandler.init_slider(p_id, p_style_name, p_attribute, p_value);
                $('#se_font-size').show();
                break;

            case "font-weight":
                gmStyleEditHandler.init_text_format(p_id, p_style_name, p_attribute, p_value);
                $('#se_font_styles').show();
                break;

            case "font-style":
                gmStyleEditHandler.init_text_format(p_id, p_style_name, p_attribute, p_value);
                $('#se_font_styles').show();
                break;

            case "text-decoration":
                gmStyleEditHandler.init_text_format(p_id, p_style_name, p_attribute, p_value);
                $('#se_font_styles').show();
                break;

            case "text-align":
                gmStyleEditHandler.init_text_align(p_id, p_style_name, p_attribute, p_value);
                $('#se_text-align').show();
                break;

            case "background-color":
                gmStyleEditHandler.init_color('#background_color', p_id, p_style_name, p_attribute, p_value);
                $('#se_background-color').show();
                break;

            case "background-image":
                //alert(gmslc.get_css_backup_background_img(gmStyleEditHandler.v_actor));
                gmStyleEditHandler.init_background_image(p_id, p_style_name, p_attribute, p_value);
                $('#se_background-image').show();
                break;

            case "width":
            case "height":
                gmStyleEditHandler.init_dimensions(p_id, p_style_name, p_attribute, p_value);
                $('#se_' + p_attribute).show();
                break;

            case "padding":
            case "padding-top":
            case "padding-right":
            case "padding-bottom":
            case "padding-left":
                if(gmStyleEditHandler.v_global_padding == false)
                {
					gmStyleEditHandler.init_dimensions(p_id, p_style_name, 'padding',			'');
					gmStyleEditHandler.init_dimensions(p_id, p_style_name, 'padding-top',		'');
					gmStyleEditHandler.init_dimensions(p_id, p_style_name, 'padding-right',		'');
					gmStyleEditHandler.init_dimensions(p_id, p_style_name, 'padding-bottom',	'');
					gmStyleEditHandler.init_dimensions(p_id, p_style_name, 'padding-left',		'');
                    gmStyleEditHandler.v_global_padding = true;
                }
				gmStyleEditHandler.init_dimensions(p_id, p_style_name, p_attribute,			p_value);				
                $('#se_padding').show();
                break;

            case "margin":
            case "margin-top":
            case "margin-right":
            case "margin-bottom":
            case "margin-left":
                if(gmStyleEditHandler.v_global_margin == false)
                {
					gmStyleEditHandler.init_dimensions(p_id, p_style_name, 'margin',			'');
					gmStyleEditHandler.init_dimensions(p_id, p_style_name, 'margin-top',		'');
					gmStyleEditHandler.init_dimensions(p_id, p_style_name, 'margin-right',		'');
					gmStyleEditHandler.init_dimensions(p_id, p_style_name, 'margin-bottom',		'');
					gmStyleEditHandler.init_dimensions(p_id, p_style_name, 'margin-left',		'');
                    gmStyleEditHandler.v_global_margin = true;
                }
				gmStyleEditHandler.init_dimensions(p_id, p_style_name, p_attribute,			p_value);
                $('#se_margin').show();
                break;

            /* BOF EXPERT */
            case "position":
            case "float":
            case "clear":
            case "overflow":
            case "display":
            case "white-space":
            case "vertical-align":
            case "cursor":
            case "list-style-position":
            case "list-style-type":
                $('#' + p_attribute + ' option[value="' + p_value + '"]').attr('selected', 'selected');
                $('#' + p_attribute).change(
                    function()
                    {
                        gmStyleEditHandler.change_style(p_attribute, $('#' + p_attribute).val());
                    }
                );
                $('#se_' + p_attribute).show();
                gmStyleEditHandler.v_expert_check = true;
                break;

            case "top":
            case "right":
            case "bottom":
            case "left":
                $('#se_' + p_attribute).val(p_value);
                $('#se_' + p_attribute).change(
                    function()
                    {
                        gmStyleEditHandler.change_style(p_attribute, $('#se_' + p_attribute).val());
                    }
                );
                $('#se_positions').show();
                gmStyleEditHandler.v_expert_check = true;
                break;

            case "background-position":
            case "line-height":
                $('#' + p_attribute).val(p_value);
                $('#' + p_attribute).change(
                    function()
                    {
                        gmStyleEditHandler.change_style(p_attribute, $('#' + p_attribute).val());
                    }
                );
                $('#se_' + p_attribute).show();
                gmStyleEditHandler.v_expert_check = true;
                break;
            /* EOF EXPERT */

            default:

                /* border handling */
                if(p_attribute.indexOf('border') != -1)
                {
                    if(gmStyleEditHandler.v_global_border == false)
                    {
                        var v_border = Array('', 'top-', 'right-', 'bottom-', 'left-');

                        for(var i = 0; i < v_border.length; i++)
                        {
                            gmStyleEditHandler.init_border(p_id, p_style_name, 'border-' + v_border[i] + 'width', '0px');
                            gmStyleEditHandler.init_border(p_id, p_style_name, 'border-' + v_border[i] + 'style', 'none');
                            gmStyleEditHandler.init_border(p_id, p_style_name, 'border-' + v_border[i] + 'color', '');
                        }
                        gmStyleEditHandler.v_global_border = true;
                    }
                    gmStyleEditHandler.init_border(p_id, p_style_name, p_attribute, p_value);
                    $('#se_border').show();
                }
                break;
        }
		$("#se_styles_accordion").accordion( "refresh" );
    }

    /*
     *	function to handle background images
     *	@param string p_id
     *	@param string p_style_name
     *	@param string p_attribute
     *	@param string p_value
     */
    this.init_dimensions = function(p_id, p_style_name, p_attribute, p_value)
    {
        $('#' + p_attribute).click(
            function()
            {
                gmStyleEditHandler.v_act_slider = p_attribute;

                gmStyleEditHandler.init_tooltip('#' + p_attribute, p_id, p_style_name, p_attribute, p_value);
            }
        );

        $('#' + p_attribute).val(p_value);

        $('#' + p_attribute).change(
            function()
            {
                if(p_attribute == 'margin' || p_attribute == 'padding')
                {
                    var t_val = $('#' + p_attribute).val();
                    gmStyleEditHandler.batch_dimensions_update(p_attribute, t_val);
                }
                else
                {
                    gmStyleEditHandler.change_style(p_attribute, $('#' + p_attribute).val());
                }
            }
        );
    }

    /*
     *	function to handle background images
     *	@param string p_attribute
     *	@param string p_value
     */
    this.batch_dimensions_update = function(p_attribute, p_value)
    {
        var t_directions = Array('-top', '-right', '-bottom', '-left');

        $('.se_' + p_attribute).val(p_value);

        for(var i = 0; i < t_directions.length; i++)
        {
            gmStyleEditHandler.change_style(p_attribute + t_directions[i], p_value);
        }
    }

    /*
     *	function to handle background images
     *	@param string p_id
     *	@param string p_style_name
     *	@param string p_attribute
     *	@param string p_value
     */
    this.init_border = function(p_id, p_style_name, p_attribute, p_value)
    {
        $('#' + p_attribute).change(
            function()
            {
				var t_element_id	= $(this).attr('id');
				var t_border_type	= t_element_id.replace('width',		'');
				t_border_type		= t_border_type.replace('style',	'');
				t_border_type		= t_border_type.replace('color',	'');
				t_border_type		= t_border_type.replace('border',	'');
				t_border_type		= t_border_type.replace(/-/g,		'');

                if(t_border_type.length < 2)
                {
                    gmStyleEditHandler.batch_border_update(p_attribute, p_value);
                }
                else
                {
                    gmStyleEditHandler.change_style('border-' + t_border_type + '-width', $('#border-' + t_border_type + '-width').val());
                    gmStyleEditHandler.change_style('border-' + t_border_type + '-style', $('#border-' + t_border_type + '-style').val());
                    gmStyleEditHandler.change_style('border-' + t_border_type + '-color', $('#border-' + t_border_type + '-color').val());
                }
            }
        );

        if(p_attribute.indexOf('style') != -1)
        {
            $('#' + p_attribute + ' option[value="' + p_value + '"]').attr('selected', 'selected');
        }
        else
        {
            $('#' + p_attribute).val(p_value);
        }

        if(p_attribute.indexOf('color') != -1)
        {
            gmStyleEditHandler.init_color('#' + p_attribute, p_id, p_style_name, p_attribute, $('#' + p_attribute).val());
        }

        if(p_attribute.indexOf('width') != -1)
        {

            $('#' + p_attribute).click(
                function()
                {
                    gmStyleEditHandler.v_act_slider = p_attribute;

                    gmStyleEditHandler.init_tooltip('#' + p_attribute, p_id, gmStyleEditHandler.v_actor, p_attribute, p_value);
                }
            );
        }
    }

    /*
     *	function to update all four borders
     *	@param string p_attribute
     *	@param string p_value
     */
    this.batch_border_update = function(p_attribute, p_value)
    {
        var t_border_width = $('#border-width').val();
        var t_border_style = $('#border-style').val();
        var t_border_color = $('#border-color').val();

        //if(p_attribute.indexOf('border-width') != -1)
        //{
        $('.border_width').val(t_border_width);
        //}

        //if(p_attribute.indexOf('border-style') != -1)
        //{
        $('.border_style' + ' option[value="' + t_border_style + '"]').attr('selected', 'selected');
        //}

        //if(p_attribute.indexOf('border-color') != -1 && t_border_color != '' && t_border_color != undefined)
        //{
        $('.border_color').val(t_border_color);
        //}

        var v_border = Array('', 'top-', 'right-', 'bottom-', 'left-');

        for(var i = 0; i < v_border.length; i++)
        {
            gmStyleEditHandler.change_style('border-' + v_border[i] + 'width', t_border_width);
            gmStyleEditHandler.change_style('border-' + v_border[i] + 'style', t_border_style);
            gmStyleEditHandler.change_style('border-' + v_border[i] + 'color', t_border_color);
        }
    }

    /*
     *	function to handle background images
     *	@param string p_value
     */
    this.init_background_transparency = function(p_value)
    {
        if(p_value.indexOf('transparent') != -1)
        {
            $('#background_color').val('');
            $('#background_color_display').css('background-color', 'transparent');
            $('#transparency').attr('selected', 'selected');
        }
        else
        {
            $('#no_transparency').attr('selected', 'selected');
        }
    }

    /*
     *	function to handle background images
     *	@param string p_id
     */
    this.init_background_image_info = function(p_id, p_upload, p_backup)
    {
        var t_response = new Object();
        $('.se_image_open').unbind('click');
        jQuery.ajax({
			data:		'module=se_image_info&se_id=' + p_id + '&backup=' + p_backup + '&token='+style_edit_sectoken + '&current_template=' + style_edit_config_CURRENT_TEMPLATE, 
			url:		'StyleEdit/style_edit_request.php', 
			dataType:	'json', 
			type:		"POST", 
			async:		false, 
			success:	function(p_response) 
            {
				t_response  = p_response; 				
            }
        });

        $('.se_image_open').click(
            function()
            {
                $.fancybox('<img src="' + style_edit_config_BACKGROUNDS_DIR + $('#se_background_images').val().replace('\\', '') + '" />');
            }
        );

        if(typeof(t_response.message) == 'object')
        {
            $('#se_background_image_info').html('<select id="se_background_images"></select>');

            $.each(t_response.message, function(t_val, image_data)
            {
                if(image_data['active'] == false)
                {
                    $('#se_background_images').append($('<option></option>').val(image_data['filename']).html(image_data['filename'])).addClass('se_input_box');
                }
                else
                {
                    $('#se_background_images').append($('<option></option>').val(image_data['filename']).html(image_data['filename']).attr('selected', true)).addClass('se_input_box');
                }
            });

            $('#se_background_images').change(function()
            {
                gmStyleEditHandler.v_act_bg_img = null;

                $('#background_gradient option:first').attr('selected', true);

                if($(this).val() != '')
                {
                    gmStyleEditHandler.change_style('background-image', 'url(' + style_edit_config_BACKGROUNDS_DIR + $(this).val() + ')');
                    $('.se_image_delete').show();
                    $('.se_image_open').show();
                    $("#se_background_repeat").show();
                }
                else
                {
                    gmStyleEditHandler.change_style('background-image', 'none');
                    $('.se_image_delete').hide();
                    $('.se_image_open').hide();
                    $("#se_background_repeat").hide();
                }
            });

            $('#se_background_images').die('keyup');
            $('#se_background_images').live('focus', function()
            {
                $('#se_background_images').die('keyup');
                $('#se_background_images').live('keyup', function(event)
                {
                    var t_keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
                    if(t_keycode == 37 || t_keycode == 38 || t_keycode == 39 || t_keycode == 40) // arrow-keys
                    {
                        $('#se_background_images').change();
                    }
                });
            });
        }
        else
        {
            $('#se_background_image_info').html(t_response.message);
        }

        $("#se_background_image_info").show();

        if(t_response.error != 13)
        {
            $('#se_background_image_info').attr('title', t_response.data.replace('\\', ''));
            //$("#se_background_image_data").show();
            $(".se_image_delete").show();
            $(".se_image_open").show();
            $("#se_background_repeat").show();
        }
        else
        {
            $(".se_image_delete").hide();
            $(".se_image_open").hide();
            $("#se_background_repeat").hide();
        }
    }

    /*
     *	function to handle background repeat
     */
    this.init_background_positions = function()
    {
        var t_position = $(gmStyleEditHandler.v_actor).css('background-position');

        $('#se_background-position').show();

        $('#se_background-position').val(t_position);

        $('#se_background-position').change(
            function()
            {
                gmStyleEditHandler.change_style('background-position', $("#background-position").val());
            }
        );
    }

    /*
     *	function to handle background repeat
     */
    this.init_background_repeat = function()
    {
        var t_repeat = $(gmStyleEditHandler.v_actor).css('background-repeat');
        if(t_repeat != undefined)
        {
            if(t_repeat.indexOf('repeat') != -1)
            {
                $('#' + t_repeat).attr('selected', 'selected');
            }
            $("#se_background_repeat").change(
                function()
                {
                    gmStyleEditHandler.change_style('background-repeat', $("#background_repeat").val());
                }
            );
        }
    }
    /*
     *	function to handle background repeat
     */
    this.init_background_gradient = function(p_value)
    {
        /* save styles */
        var t_request = new Object;
        jQuery.ajax({
			data:		'module=se_gradients&se_selected_gradient=' + p_value + '&token='+style_edit_sectoken + '&current_template=' + style_edit_config_CURRENT_TEMPLATE, 
			url:		'StyleEdit/style_edit_request.php', 
			dataType:	'html', 
			type:		"POST", 
			async:		false, 
			success:	function(p_response) 
            {
				t_request  = p_response; 
            }
        });
        if(p_value.indexOf(style_edit_config_GRADIENTS_DIR) != -1)
        {
            $(".se_image_delete").hide();
        }
        if(parseInt(t_request) != 0)
        {
            $('#se_background_gradient').html(t_request);
            $('#se_background_gradient').show();
            $('#se_background_repeat').show();
        }
        /*
         if(t_repeat.indexOf('repeat') != -1)
         {
         $('#' + t_repeat).attr('selected', 'selected');
         }
         */
        $('#background_gradient').change(
            function()
            {
                var t_val = $(this).val();

                if(parseInt(t_val) == 0)
                {
                    return;
                }
                if(parseInt(t_val) != 0)
                {
                    $(".se_image_delete").hide();
                }
                if(typeof(t_val) == 'undefined' || t_val == '')
                {
                    t_val = 'none';
                    $('#se_background_images option:first').attr('selected', true);
                }
                else if(typeof(t_val) == 'string' && (t_val.search('url') != -1 || t_val == ''))
                {
                    $('#se_background_images option:first').attr('selected', true);
                }
                $('#se_background_repeat').show();
                gmStyleEditHandler.v_act_bg_img = null;
                gmStyleEditHandler.change_style('background-image', t_val);
            }
        );
    }
    /*
     *	function to handle background images
     *	@param string p_id
     *	@param string p_style_name
     *	@param string p_attribute
     *	@param string p_value
     */
    this.init_background_image = function(p_id, p_style_name, p_attribute, p_value)
    {
        gmStyleEditHandler.init_background_image_info(p_id, false, p_value);
        gmStyleEditHandler.init_background_repeat();
        gmStyleEditHandler.init_background_gradient(p_value);
        gmStyleEditHandler.init_background_positions();

        $('.se_image_delete').unbind('click');
        $('.se_image_upload').unbind('click');

        $('.se_image_upload').uploader(
            {
											module		: 'se_image_upload',
											status_box	: '#se_message',
											type		: 'image',
											se_id		: gmStyleEditHandler.v_actor_id,
											se_selector	: gmStyleEditHandler.v_actor,
											se_button	: '#se_image_uploader'
            }
        );

        $('.se_image_delete').deleter(
            {
											module_confirm	: 'se_image_delete_confirm',
											status_box		: '#se_message',
											se_id			: gmStyleEditHandler.v_actor_id,
											se_selector		: gmStyleEditHandler.v_actor,
											v_gmslc			: p_gmslc
            }
        );
    }

    /*
     *	function to handle colorizing
     *	@param string p_element
     *	@param string p_id
     *	@param string p_style_name
     *	@param string p_attribute
     *	@param string p_value
     */
    this.init_color = function(p_element, p_id, p_style_name, p_attribute, p_value)
    {
        $(p_element + '_display').css('backgroundColor', p_value);

        if(p_value == 'transparent')
        {
            $(p_element).val('');
            p_value = '#ffffff';
        }
        else
        {
            if(p_attribute.indexOf('border') == -1)
            {
                $(p_element).val(p_value);
            }
        }

        var t_element = p_element + ', ' + p_element + '_display';

        if(p_element.indexOf('border') != -1)
        {
            t_element = p_element;
        }

        $(t_element).click(
            function()
            {
                $("#GMColorizer").colorize(
                    {
						hex_color:	$(p_element).val(), 
						id:			p_id, 
						selector:	p_style_name, 
						attribute:	p_attribute,
						display:	p_element
                    }
                );
            }
        );

        $(p_element).change(
            function()
            {
                var t_color = $(p_element).val();

                if(t_color.substr(0, 1) != '#')
                {
                    t_color += '#';
                    $(p_element).val(t_color);
                }
                $(p_element + '_display').css('backgroundColor', t_color);

                if(p_attribute.indexOf('background') != -1)
                {
                    gmStyleEditHandler.init_background_transparency('');
                }
                gmStyleEditHandler.change_style(p_attribute, t_color);
            }
        );

        if(p_attribute.indexOf('background') != -1)
        {
            $('#background_tansparency').change(
                function()
                {
                    var t_val = $("#background_tansparency").val();

                    if(t_val == 'transparency')
                    {
                        $('#background_color').val('');
                        $('#background_color_display').css('background-color', 'transparent');
                        gmStyleEditHandler.change_style('background-color', 'transparent');
                    }
                }
            );
            gmStyleEditHandler.init_background_transparency(p_value);
        }
    }

    /*
     *	function to handle text format
     *	@param string p_id
     *	@param string p_style_name
     *	@param string p_attribute
     *	@param string p_value
     */
    this.init_text_format = function(p_id, p_style_name, p_attribute, p_value)
    {
		var t_element			= '#' + p_attribute;
		var t_active_class		= 'active_' + p_attribute.replace('-', '_');
		var t_inactive_class	= t_active_class.replace('active', 'inactive');

        /* general reset set all buttons inactive */
        $(t_element).removeClass(t_active_class);
        $(t_element).removeClass(t_inactive_class);
        $(t_element).addClass(t_inactive_class);

        /* set text format */
        if(p_value != 'normal' && p_value != 'none')
        {
            $(t_element).addClass(t_active_class);
        }

        $(t_element).unbind('click');

        /* handle user event */
        $(t_element).click(
            function()
            {
                var t_id = $(this).attr('id');
                var t_add;
                var t_remove;

                if(t_id.indexOf('font-weight') != -1)
                {
                    if($(t_element).hasClass('active_font_weight'))
                    {
						t_add	= 'inactive_font_weight';
						t_remove= 'active_font_weight';
                        p_value = 'normal';
                    }
                    else
                    {
						t_add	= 'active_font_weight';
						t_remove= 'inactive_font_weight';
                        p_value = 'bold';
                    }
                }
                else if(t_element.indexOf('font-style') != -1)
                {
                    if($(t_element).hasClass('active_font_style'))
                    {
						t_add	= 'inactive_font_style';
						t_remove= 'active_font_style';
                        p_value = 'normal';
                    }
                    else
                    {
						t_add	= 'active_font_style';
						t_remove= 'inactive_font_style';
                        p_value = 'italic';
                    }
                }
                else if(t_element.indexOf('text-decoration') != -1)
                {
                    if($(t_element).hasClass('active_text_decoration'))
                    {
						t_add	= 'inactive_text_decoration';
						t_remove= 'active_text_decoration';
                        p_value = 'none';
                    }
                    else
                    {
						t_add	= 'active_text_decoration';
						t_remove= 'inactive_text_decoration';
                        p_value = 'underline';
                    }
                }

                gmStyleEditHandler.change_style(t_id, p_value);
                $('#' + t_id).removeClass(t_remove);
                $('#' + t_id).addClass(t_add);
            }
        );
    }

    /*
     *	function to handle text alignment
     *	@param string p_id
     *	@param string p_style_name
     *	@param string p_attribute
     *	@param string p_value
     */
    this.init_text_align = function(p_id, p_style_name, p_attribute, p_value)
    {
        $('#se_text_align div').each(
            function()
            {
				var t_alignment = this.id.replace('se_text-align_','');
                $(this).removeClass('active_align_' + t_alignment);
                $(this).addClass('inactive_align_' + t_alignment);
                $('#se_text-align_' + p_value).removeClass('inactive_align_' + p_value);
                $('#se_text-align_' + p_value).addClass('active_align_' + p_value);

                gmStyleEditHandler.v_act_text_align = p_value;

                $('#' + this.id).click(
                    function()
                    {
                        var t_old_class = this.className;
                        if(t_old_class.indexOf('inactive') != -1)
                        {
                            if(gmStyleEditHandler.v_act_text_align != undefined)
                            {
                                $('#se_text-align_' + gmStyleEditHandler.v_act_text_align).removeClass('active_align_' + gmStyleEditHandler.v_act_text_align);
								$('#se_text-align_' + gmStyleEditHandler.v_act_text_align).addClass('inactive_align_' +	gmStyleEditHandler.v_act_text_align);
                                $(this).addClass(t_new_class);
                            }

                            var t_new_class = t_old_class.replace('inactive', 'active');

                            $(this).removeClass(t_old_class);
                            $(this).addClass(t_new_class);

							var t_align = this.id.replace('se_text-align_','');
                            gmStyleEditHandler.v_act_text_align = t_align;
                            gmStyleEditHandler.change_style(p_attribute, t_align);
                        }
                    }
                );
            }
        );
    }

    /*
     *	function to handle the slider
     *	@param string p_id
     *	@param string p_style_name
     *	@param string p_attribute
     *	@param string p_value
     */
    this.init_tooltip = function(p_element, p_id, p_style_name, p_attribute, p_value)
    {
        $('#se_tooltip').mouseover(
            function()
            {
				$(gmStyleEditHandler.v_se).draggable({'disable':true});
            }
        );

        $('#se_tooltip').mouseout(
            function()
            {
                $(gmStyleEditHandler.v_se).draggable('enable');
            }
        );

        $('#se_tooltip').draggable(
            {
                handle: '#se_tooltip_top'
            }
        );

        $('#se_tooltip').show();

        /*ie 6 z-index fix */
		if (navigator.appVersion.match(/MSIE [0-6]\./)) 
        {
            $('#se_tooltip').bgiframe();
        }

        $('#se_close_tooltip').click(
            function()
            {
                $('#se_tooltip').hide();
            }
        );

        if(p_attribute.indexOf('border') != -1 || p_attribute.indexOf('padding') != -1)
        {
            $('#se_unit_auto_row').hide();
        }
        else
        {
            $('#se_unit_auto_row').show();
        }

		var t_val						= $(p_element).val();
		var t_unit						= t_val.match(/[a-zA-Z]{2}/);

		gmStyleEditHandler.v_act_number	= parseInt(t_val.match(/\d+/));	
		gmStyleEditHandler.v_act_unit	= t_unit;
		gmStyleEditHandler.v_act_attr	= p_attribute;

        if(isNaN(gmStyleEditHandler.v_act_number))
        {
            gmStyleEditHandler.v_act_number = 0;
        }


        /* check "auto" unit in prepared checkbox if set */
        if(gmStyleEditHandler.v_act_unit == 'auto')
        {
            $('#se_unit_auto').prop('checked', true);
            gmStyleEditHandler.v_act_number = 0;
        }
        else
        {
            $('#se_unit_auto').prop('checked', false);
        }

        /* display unit in prepared select list */
        if(t_unit != null)
        {
			//$('#se_unit_select option[value="' + t_unit + '"]').attr('selected', 'selected');
			
			$('#se_unit_select option').filter(function()
			{
				var t_ret = false;
				$(this).val() == t_unit ? t_ret = true: t_ret = false;
				
				return t_ret;
			}).attr('selected', 'selected');
		}
        else
        {
            gmStyleEditHandler.v_act_unit = 'px';
            $('#se_unit_select option[value="px"]').attr('selected', 'selected');
        }

        /* handle "auto" if set */
        $('#se_unit_auto').click(
            function()
            {
                var t_auto_value;

                if($('#se_unit_auto').prop('checked') == true)
                {
                    t_auto_value = 'auto';
                    $('#' + gmStyleEditHandler.v_act_slider).val(t_auto_value);

                    if(gmStyleEditHandler.v_act_attr.indexOf('border') != -1)
                    {
                        gmStyleEditHandler.batch_border_update(gmStyleEditHandler.v_act_attr, t_auto_value);
                    }
                    else if(gmStyleEditHandler.v_act_attr == 'margin' || gmStyleEditHandler.v_act_attr == 'padding')
                    {
                        gmStyleEditHandler.batch_dimensions_update(gmStyleEditHandler.v_act_attr, t_auto_value);
                    }
                    else
                    {
                        gmStyleEditHandler.change_style(gmStyleEditHandler.v_act_attr, t_auto_value);
                    }
                }
                else
                {
                    t_auto_value = '';
                    //t_auto_value = $('#se_slider').slider('value') + '' + gmStyleEditHandler.v_act_unit;
                    //$('#' + gmStyleEditHandler.v_act_slider).val(t_auto_value);
                    //gmStyleEditHandler.change_style(p_id, p_style_name, gmStyleEditHandler.v_act_attr, t_auto_value);
                }

                if(gmStyleEditHandler.v_act_attr == 'margin' || gmStyleEditHandler.v_act_attr == 'padding')
                {
                    $('.se_' + gmStyleEditHandler.v_act_attr).val(t_auto_value);
                }
            }
        );

        /* handle unit if set */
        $('#se_unit_select').change(
            function()
            {
				t_unit		= $('#se_unit_select').val();
				t_val		= $('#' + gmStyleEditHandler.v_act_attr).val();
				t_val_unit	= t_val.match(/\D+/);

                if(t_val_unit != null)
                {
					t_new_val	= t_val.replace(t_val_unit, t_unit)
                }
                else
                {
					t_new_val	= t_val + '' + t_unit;
                }

                $('#' + gmStyleEditHandler.v_act_attr).val(t_new_val);
                gmStyleEditHandler.v_act_unit = t_unit;

                if(gmStyleEditHandler.v_act_attr.indexOf('border') != -1)
                {
                    gmStyleEditHandler.batch_border_update(gmStyleEditHandler.v_act_attr, t_new_val);
                }
                else if(gmStyleEditHandler.v_act_attr == 'margin' || gmStyleEditHandler.v_act_attr == 'padding')
                {
                    gmStyleEditHandler.batch_dimensions_update(gmStyleEditHandler.v_act_attr, t_new_val);
                }
                else
                {
                    gmStyleEditHandler.change_style(gmStyleEditHandler.v_act_attr, t_new_val);
                }
            }
        );

        gmStyleEditHandler.v_act_unit = t_unit;

        $('#se_tooltip').css(
            {
				top:	$(p_element).position().top  + ($(p_element).outerHeight()) - ($('#se_tooltip').outerHeight() /2) - ($(p_element).outerHeight()/2) + 'px',
				left:	($(p_element).position().left + $(p_element).outerWidth() + 15)			+ 'px'
            }
        );

		gmStyleEditHandler.v_act_slider_value	= gmStyleEditHandler.v_act_number;
        if(gmStyleEditHandler.v_act_number < 130)
        {
			gmStyleEditHandler.v_act_slider_change	= false;
            $('#se_slider_pointer').css('left', gmStyleEditHandler.v_act_number + 'px');
        }
        else
        {
            gmStyleEditHandler.v_act_slider_change = true;
            $('#se_slider_pointer').css('left', '65px');
            gmStyleEditHandler.v_act_slider_value -= 65;
        }

        $('#se_slider_pointer').draggable(
            {
                start: function(p_event, p_ui)
                {
					var t_val	= $('#' + gmStyleEditHandler.v_act_attr).val();

					t_val		= parseInt(t_val);	

                    if(isNaN(t_val))
                    {
                        t_val = 0;
                    }

                    gmStyleEditHandler.v_slider_value = $('#se_slider_pointer').css('left');

                    gmStyleEditHandler.v_act_slider_value = t_val;

                    $('#se_unit_auto').prop('checked', false);
                },
                stop: function(p_event, p_ui)
                {
					var t_val	= $('#' + gmStyleEditHandler.v_act_attr).val();

					t_val		= parseInt(t_val);						

                    if((p_ui.position.left == 130 || p_ui.position.left == 0) && (gmStyleEditHandler.v_act_slider_value >= 0 || gmStyleEditHandler.v_act_attr.indexOf('margin') != -1 || gmStyleEditHandler.v_act_attr.indexOf('padding') != -1))
                    {
                        $('#se_slider_pointer').css('left', '65px');
                    }
                },
                drag: function(p_event, p_ui)
                {
                    if(gmStyleEditHandler.v_act_unit == 'auto' || gmStyleEditHandler.v_act_unit == null)
                    {
                        gmStyleEditHandler.v_act_unit = 'px';
                    }

					var t_slider_pointer_value			= parseInt(gmStyleEditHandler.v_slider_value.replace(gmStyleEditHandler.v_act_unit, ''));

					var t_slider_pos					= p_ui.position.left - t_slider_pointer_value;		

                    gmStyleEditHandler.v_act_slider_value += t_slider_pos;

                    if(gmStyleEditHandler.v_act_slider_value >= 0 || gmStyleEditHandler.v_act_attr.indexOf('margin') != -1 || gmStyleEditHandler.v_act_attr.indexOf('padding') != -1)
                    {
						var t_slider_value					= gmStyleEditHandler.v_act_slider_value + gmStyleEditHandler.v_act_unit;

						gmStyleEditHandler.v_slider_value	= p_ui.position.left + gmStyleEditHandler.v_act_unit;

                        $('#' + gmStyleEditHandler.v_act_attr).val(t_slider_value);

                        if(gmStyleEditHandler.v_act_attr.indexOf('border') != -1)
                        {
                            gmStyleEditHandler.batch_border_update(gmStyleEditHandler.v_act_attr, t_slider_value);
                        }
                        else if(gmStyleEditHandler.v_act_attr == 'margin' || gmStyleEditHandler.v_act_attr == 'padding')
                        {
                            gmStyleEditHandler.batch_dimensions_update(gmStyleEditHandler.v_act_attr, t_slider_value);
                        }
                        else
                        {
                            gmStyleEditHandler.change_style(gmStyleEditHandler.v_act_attr, t_slider_value);
                        }
                    }
                },
                containment: '#se_sliderer',
                axis: 'x'
            }
        );

    }

    /*
     *	function to handle the slider
     *	@param string p_id
     *	@param string p_style_name
     *	@param string p_attribute
     *	@param string p_value
     */
    this.init_slider = function(p_id, p_style_name, p_attribute, p_value)
    {
        $('#se_slide_font-size').remove();

        /* ie6 slider fix */
		if (navigator.appVersion.match(/MSIE [0-6]\./)) 
        {
            $('#style_edit_layer').append('<div id="se_slide_font-size">&nbsp;</div>');


            if($('#se_area_fonts').hasClass('ui-state-active'))
            {
                $('#se_slide_font-size').show();
            }
            else
            {
                $('#se_slide_font-size').hide();
            }

            $('#' + p_attribute).click(
                function()
                {
                    $('#se_slide_font-size').show();
                }
            );
        }
        else
        {
            $('#se_font-size').append('<div id="se_slide_font-size">&nbsp;</div>');
        }

        var t_value = p_value.replace('px', '');

		$('#se_slide_' + p_attribute).slider().slider('destroy');

        $('#se_slide_' + p_attribute).mouseover(
            function()
            {
                $(gmStyleEditHandler.v_se).draggable('disable');
            }
        );

        $('#se_slide_' + p_attribute).mouseout(
            function()
            {
                $(gmStyleEditHandler.v_se).draggable('enable');
            }
        );

        $('#se_slide_' + p_attribute).slider(
            {
                range: "min",
                value: t_value,
                min: 1,
                max: 100,
                orientation: 'horizontal',
                slide: function(event, ui)
                {
                    t_value = ui.value + 'px';
                    $('#' + p_attribute).val(t_value);
                    gmStyleEditHandler.change_style(p_attribute, t_value);
                }
            });

        $('#' + p_attribute).val(p_value);

        $('#se_slide_' + p_attribute).slider('value', t_value);

        $('#' + p_attribute).keyup(
            function()
            {
                t_value = $('#' + p_attribute).val();

                t_value = t_value.replace('px', '');

                $('#se_slide_' + p_attribute).slider('value', t_value);

                gmStyleEditHandler.change_style(p_attribute, t_value + 'px');
            }
        );
    }

    /*
     *	function to apply changed styles
     *	@param string p_attribute
     *	@param string p_value
     */
    this.change_style = function(p_attribute, p_value)
    {
        //if(p_attribute != 'id' && p_attribute != 'selector' && p_attribute != 'border-width' && p_attribute != 'border-style' && p_attribute != 'border-color')
        if(p_attribute != 'id' && p_attribute != 'selector')
        {
            // handle new styles
            $(gmStyleEditHandler.v_actor).css(p_attribute, p_value);

            gmStyleEditHandler.v_act_changes[p_attribute] = p_value;

            if(p_attribute.indexOf('image') != -1 && p_value != 'url()' && p_value != 'none')
            {
                if(gmStyleEditHandler.v_act_bg_img != null)
                {
                    return;
                }

                var t_image_src = p_value.split('/');

                if(typeof(t_image_src) != 'undefined')
                {
					var t_image_name = t_image_src[t_image_src.length-1];
                    t_image_name = t_image_name.replace(')', '');
                    t_image_name = t_image_name.replace('"', '');
                    t_image_name = t_image_name.replace("'", '');

                    var t_image_path = style_edit_config_BACKGROUNDS_DIR;
                    if(p_value.indexOf(style_edit_config_GRADIENTS_DIR) != -1)
                    {
                        t_image_path = style_edit_config_GRADIENTS_DIR_RELATIVE;
                    }
                    p_value = "url(" + t_image_path + t_image_name + ")";

                    gmStyleEditHandler.v_act_bg_img = p_value;

                    $(gmStyleEditHandler.v_actor).css(p_attribute, p_value);
                    gmStyleEditHandler.v_act_changes[p_attribute] = p_value;
                }
            }
        }
    }

    /*
     *	function to get background image filename from selected option in dropdown-field
     */
    this.get_background_image_filename = function()
    {
        var t_filename = '';

        if($('#se_background_images').length > 0 && $('#se_background_images').val() != '')
        {
            var t_filename_array = $('#se_background_images').val().split('/');
            t_filename = t_filename_array[t_filename_array.length - 1];
        }

        return t_filename;
    }
}

/*
 depracted?
 */
function se_in_array(item,arr) 
{
	for(p=0;p<arr.length;p++)
    {
		if (item == arr[p])
        {
            return true;
        }
    }
    return false;
}