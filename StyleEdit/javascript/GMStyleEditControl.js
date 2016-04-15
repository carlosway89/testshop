/* --------------------------------------------------------------
 StyleEdit v2.0
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 --------------------------------------------------------------
 */

function GMStyleEditControl()
{
    /*
     *	@var string
     *	-> se_styles, se_boxes, se_backup
     */
    var v_se_active_tab;

    /*
     *	@var string
     *	-> se_styles, se_boxes, se_backup
     */
    var v_se_start = false;

    /*
     *	init control
     */
    this.init = function()
    {
        $('.se_accord').click(
            function()
            {
                /* ie6 slider fix */
                gmStyleEditControl.se_ie6_slider_fix(this, '0', '#se_slide_font-size');
                $('#se_message').html('');
                $('#se_tooltip').hide();
            }
        );

        $('.se_tab').click(
            function()
            {
                gmStyleEditControl.v_se_active_tab = $(this).attr('id');
                gmStyleEditControl.control();
                $('#se_message').html('');
                $('#se_tooltip').hide();
            }
        );

        gmStyleEditControl.control();

        $('.se_control').click(
            function()
            {
                var t_id = $(this).attr('id');
                gmStyleEditControl.control_mode(t_id);
            }
        );
    }

    /*
     *	function to control tab actions
     */
    this.control = function()
    {
        /* ie6 slider fix */
        gmStyleEditControl.se_ie6_slider_fix('0', false, '#se_slide_font-size');
        gmStyleEditControl.se_ie6_slider_fix('0', false, '#se_archivizer_slider');
        gmStyleEditControl.se_ie6_slider_fix('0', false, '#se_archivizer_content');
        switch(this.v_se_active_tab)
        {
            case 'se_boxes':
                /* hide expert layer */
                $('#style_edit_expert').hide();
                $('#se_pseudo_classes').hide();

                $('.style_edit_info, #style_edit_save_button, #style_edit_button').hide();
                $('#style_edit_boxes').show();

                if(gmBoxesMaster.get_boxes_edit_active() == false)
                {
                    $('#style_boxes_step_2').hide();
                    $('#style_boxes_step_1').show();
                }
                else
                {
                    $('#style_boxes_step_1').hide();
                    $('#style_boxes_step_2').show();
                }
                break;

            case 'se_backup':
                /* hide expert layer */
                $('#style_edit_expert').hide();
                $('#se_pseudo_classes').hide();
                /* ie6 slider fix */
                if(navigator.appVersion.match(/MSIE [0-6]\./) && $('#se_archivizer').hasClass('ui-accordion-content-active'))
                {
                    $('#se_archivizer_slider').show();
                    $('#se_archivizer_content').show();
                }

                if(gmStyleEditControl.v_se_act_accord.indexOf('se_archive') != -1)
                {
                    gmStyleEditControl.se_ie6_slider_fix('0', true, '#se_archivizer_slider');
                    gmStyleEditControl.se_ie6_slider_fix('0', true, '#se_archivizer_content');
                }

                $('.style_edit_info, .style_boxes_info, #style_edit_save_button, #style_edit_button, #style_edit_boxes').hide();
                $("#" + gmStyleEditControl.v_se_active_tab + "_accordion").accordion();
                gmStyleEditControl.control_archive();
                $('#se_message').css('top', '370px');

                break;

            default:
                $('#se_pseudo_classes').show();
                /* ie6 slider fix */
                if(navigator.appVersion.match(/MSIE [0-6]\./) && $('#se_area_fonts').hasClass('ui-state-active'))
                {
                    $('#se_slide_font-size').show();
                }

                /* show expert layer */
                if(gmStyleEditHandler.v_expert_check == true && gmStyleEditHandler.v_edit == true)
                {
                    $('#style_edit_expert').show();
                }

                $('#style_edit_boxes, .se_side_layer_content, .style_boxes_info').hide();
                $('#style_edit_button').show();

                if(gmslc.get_style_edit_active() == true)
                {
                    $('#style_edit_step_1').hide();
                    $('#style_edit_step_2').show();
                }

                if(gmslc.get_style_edit_active() == false)
                {
                    $('#style_edit_step_1').show();
                }

                if(gmslc.get_style_edit_active() == true && gmStyleEditHandler.v_edit == true)
                {
                    $('#style_edit_save_button').show();
                    $('.style_edit_info').hide();
                }

                $('#se_message').css('top', '310px');

                break;
        }
    }
    /*
     *	function to control general actions
     */
    this.control_mode = function(p_id)
    {
        switch(p_id)
        {
            case 'style_edit_button':
                /* ie6 slider fix */
                gmStyleEditControl.se_ie6_slider_fix('0', false, '#se_slide_font-size');

                /* start editing styles */
                if($('#style_edit_button').attr('class').indexOf('style_edit_button_start') != -1)
                {
                    $('#style_edit_step_1').hide();
                    $('#se_message, #style_edit_step_2').show();
                    $('#style_edit_button').removeClass('style_edit_button_start');
                    $('#style_edit_button').addClass('style_edit_button_stop');
                    $('#style_edit_boxes').removeClass('style_edit_boxes_stop');
                    $('#style_edit_boxes').addClass('style_edit_boxes_start');
                    gmStyleEditControl.v_se_start = true;
                    gmBoxesMaster.set_boxes_edit_active(false);
                    gmslc.set_style_edit_active(true);
                    gmStyleEditControl.v_se_active_tab = 'se_styles';
                }
                /* stop editing styles */
                else
                {
                    $('#se_pseudo_classes').hide();
                    $('#style_edit_save_button, #style_edit_step_2, #se_styles_accordion, #se_message, .se_side_layer_content').hide();
                    $('#style_edit_step_1').show();
                    $('#style_edit_button').removeClass('style_edit_button_stop');
                    $('#style_edit_button').addClass('style_edit_button_start');
                    gmStyleEditControl.v_se_start = false;
                    gmslc.set_style_edit_active(false);
                    gmStyleEditHandler.v_edit = false;
                    /* hide expert layer */
                    $('#style_edit_expert').hide();
                }
                break;

            case 'style_edit_boxes':
                /* start editing boxes */
                if($('#style_edit_boxes').attr('class').indexOf('style_edit_boxes_start') != -1)
                {
                    $('#se_message, #style_edit_step_1, #style_edit_step_2, #se_styles_accordion, #style_boxes_step_1').hide();
                    $('#style_boxes_step_2').show();
                    $('#style_edit_boxes').removeClass('style_edit_boxes_start');
                    $('#style_edit_boxes').addClass('style_edit_boxes_stop');
                    $('#style_edit_button').removeClass('style_edit_button_stop');
                    $('#style_edit_button').addClass('style_edit_button_start');
                    gmslc.set_style_edit_active(false);
                    gmBoxesMaster.set_boxes_edit_active(true);
                    gmStyleEditHandler.v_edit = false;
                    gmStyleEditControl.v_se_start = false;
                    gmStyleEditHandler.v_expert_check = false;
                }
                /* stop editing boxes */
                else
                {
                    $('#style_edit_boxes').removeClass('style_edit_boxes_stop');
                    $('#style_edit_boxes').addClass('style_edit_boxes_start');
                    $('#style_boxes_step_2').hide();
                    $('#style_boxes_step_1').show();
                    gmBoxesMaster.set_boxes_edit_active(false);
                }
                break;

            case 'se_close':
                var t_response = new Object();
                jQuery.ajax({
                    data:		'module=se_close&token='+style_edit_sectoken + '&current_template=' + style_edit_config_CURRENT_TEMPLATE,
                    url:		'StyleEdit/style_edit_request.php',
                    dataType:	'json',
                    type:		"POST",
                    async:		false,
                    success:	function(p_response)
                    {
                        t_response  = p_response;
                    }
                });

                t_confirm = confirm(t_response.message);

                if(t_confirm == true)
                {
                    window.location.href = style_edit_config_EXIT_CALL;
                    return false;
                }
                break;

            case 'se_template_conf':

                var t_admin_conf_link = style_edit_config_FANCY_BOX_ADMIN_CONF_LINK + '?' + style_edit_config_SESSION_NAME + '=' + style_edit_config_SESSION_ID + '&current_template=' + style_edit_config_CURRENT_TEMPLATE;

                $.fancybox({
                    'width'				: style_edit_config_FANCY_BOX_WIDTH,
                    'height'			: style_edit_config_FANCY_BOX_HEIGHT,
                    'autoScale'			: false,
                    'transitionIn'		: 'none',
                    'transitionOut'		: 'none',
                    'type'				: 'iframe',
                    'href'				: t_admin_conf_link
                });
                break;

            case 'se_css_editor':

                var t_display = $('#style_monitor_layer').css('display');

                if(t_display == 'block')
                {
                    gmStyleMonitor.hide_monitor();
                }
                else
                {
                    gmStyleMonitor.show_monitor();
                }

                break;
        }
    }

    /*
     *	function to control the archive
     */
    this.control_archive = function()
    {
        $('#se_upload_field').unbind('click');
        $('#se_export_field').unbind('click');
        $('#se_upload_field').unbind('uploader');
        $('#se_archive_heading').unbind('click');

        $('#se_upload_field').uploader(
            {
                module		: 'se_upload',
                status_box	: '#se_message',
                type		: 'archive',
                se_id		: '',
                se_selector	: '',
                se_button	: '#se_upload_field'
            }
        );

        $('#se_export_name').export_name();
        $('#se_export_field').exporter();

        gmStyleEditControl.load_archive('.se_archive_heading');
    }

    /*
     *	load archive
     */
    this.load_archive = function(p_elem)
    {
        $(p_elem).click(
            function()
            {
                var t_response = new Object();

                jQuery.ajax({
                    data:		'module=se_archive&token=' + style_edit_sectoken + '&current_template=' + style_edit_config_CURRENT_TEMPLATE,
                    url:		'StyleEdit/style_edit_request.php',
                    dataType:	'json',
                    type:		"POST",
                    async:		false,
                    beforeSend:	function()
                    {
                        $('#se_message').show();
                        $('#se_message').html(style_edit_img_loading);
                    },
                    success:	function(p_response)
                    {
                        $('#se_message').html('');
                        t_response  = p_response;
                        /* check session */
                        gmStyleEditControl.control_session(t_response);
                    }
                });

                /* ie 6 fix */
                if(navigator.appVersion.match(/MSIE [0-6]\./))
                {
                    $('#style_edit_layer').append('<div id="se_archivizer_content">&nbsp;</div>');
                    if($('#se_archivizer').hasClass('ui-accordion-content-active'))
                    {
                        $('#se_archivizer_content').show();
                    }
                    else
                    {
                        $('#se_archivizer_content').hide();
                    }
                }
                else
                {
                    $('#se_archivizer').append('<div id="se_archivizer_content">&nbsp;</div>');
                }


                if(t_response.error == 11)
                {
                    $("#se_archivizer_slider").hide();
                    $("#se_archivizer_content").html(t_response.message);
                }
                else
                {
                    $("#se_archivizer_content").html(t_response.data);

                    if(t_response.data_count > 5)
                    {
                        /* ie 6 fix */
                        if(navigator.appVersion.match(/MSIE [0-6]\./))
                        {
                            $('#style_edit_layer').append('<div id="se_archivizer_slider">&nbsp;</div>');
                        }
                        else
                        {
                            $('#se_archivizer').append('<div id="se_archivizer_slider">&nbsp;</div>');
                        }

                        $("#se_archivizer_slider").mouseover(
                            function()
                            {
								$(gmStyleEditHandler.v_se).draggable({'disable':true});
                            }
                        );

                        $("#se_archivizer_slider").mouseout(
                            function()
                            {
                                $(gmStyleEditHandler.v_se).draggable('enable');
                            }
                        );

                        if($('#se_archivizer').hasClass('ui-accordion-content-active'))
                        {
                            $("#se_archivizer_slider").show();
                        }

                        $("#se_archivizer_slider").slider(
                            {
                                animate: true,
                                orientation: "vertical",
                                value: 97,
                                min: 0,
                                max: 97,
                                change: function(event, ui)
                                {
                                    var t_scroll_height = $("#se_archivizer_content").prop("scrollHeight");
                                    var t_height		= $("#se_archivizer_content").outerHeight();
                                    var t_new_height	= t_scroll_height - t_height;
                                    var t_val			= (100 - ui.value) * ( t_new_height / 100);
                                    $("#se_archivizer_content").prop({scrollTop: t_val});
                                },
                                slide: function(event, ui)
                                {
                                    $("#se_archivizer_slider").slider( 'enable' );
                                    var t_scroll_height = $("#se_archivizer_content").prop("scrollHeight");
                                    var t_height		= $("#se_archivizer_content").outerHeight();
                                    var t_new_height	= t_scroll_height - t_height;
                                    var t_val			= (100 - ui.value) * ( t_new_height / 100);
                                    $("#se_archivizer_content").prop({scrollTop: t_val});
                                }
                            });
                    }
                    else
                    {
                        $("#se_archivizer_slider").hide();
                    }

                    $(".se_import").unbind('click');
                    $(".se_delete").unbind('click');
                    $(".se_open").unbind('click');

                    $(".se_import").importer();

                    $(".se_delete").deleter(
                        {
                            module_confirm:		'se_delete_confirm',
                            status_box:			'#se_message',
                            se_id:				'',
                            se_selector:		''
                        }
                    );

                    $(".se_open").viewer();
                }
            }
        );
    }

    /*
     *	return message if user logged off
     */
    this.control_session = function(p_request)
    {
        t_confirmation = false;
        if(p_request.error == 666)
        {
            $('#se_message').html('');
            t_confirmation = confirm(p_request.message);
            if(t_confirmation == true)
            {
                location.reload();
            }
        }
    }

    /*
     *	ie 6 fix
     */
    this.se_ie6_slider_fix = function(p_id, p_type, p_element)
    {
        if (navigator.appVersion.match(/MSIE [0-6]\./))
        {
            if(p_type == '0' && p_id != '0')
            {
                var t_id = $(p_id).attr('id');
                gmStyleEditControl.v_se_act_accord = t_id;

                if(t_id == 'se_accord_fonts' || t_id == 'se_area_fonts')
                {
                    $('#se_slide_font-size').show();
                }
                else
                {
                    $('#se_slide_font-size').hide();
                }

                if((t_id == 'se_archive_heading' || t_id == 'se_archive'))
                {
                    $('#se_archivizer_slider').show();
                    $('#se_archivizer_content').show();
                }
                else
                {
                    $('#se_archivizer_slider').hide();
                    $('#se_archivizer_content').hide();
                }
            }
            else
            {
                if(p_type == true)
                {
                    $(p_element).show();
                }
                else
                {
                    $(p_element).hide();
                }
            }
        }
    }
}