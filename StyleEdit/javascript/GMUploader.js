/* --------------------------------------------------------------
  StyleEdit v2.0
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2010 Gambio GmbH
  --------------------------------------------------------------
*/

(function($)
{
	/*
	*	attach method to jQuery  
	*/
 	$.fn.extend(
	{ 
		/*
		*	plugin definition
		*/
		uploader: function(p_options) 
		{

			/*	set default values	*/
			var v_defaults = 
			{
				module:		'',
				status_box:	'',
				type:		'',
				se_id:		'',
				se_selector:'',
				se_button:	''
			};
			
			/*	extend defaults with main options	*/
			var v_options = $.extend(v_defaults, p_options);
			
			/*	iterate & reformat each matched element	*/
    		return this.each(
				function() 
				{
					/*	build element specific options	*/
					var v_element_opt = v_options;
					
					/* call method on mouseover */
					$(v_element_opt.se_button).mouseover(
						function()
						{
							$(this).upload(v_element_opt);
						}
					);
    			}
			);
    	},

		upload: function(p_options) 
		{				
			new AjaxUpload
			(
				p_options.se_button, 
				{
					action: 'StyleEdit/style_edit_request.php',
					data: 
					{
						'module'			: p_options.module,
						'type'				: p_options.type,
						'se_id'				: p_options.se_id,
						'token'				: style_edit_sectoken,
						'current_template'	: style_edit_config_CURRENT_TEMPLATE
					},
					responseType: "json",
					onSubmit: function(file , ext)
					{					
						this.setData(
							{
								'module'			: p_options.module,
								'type'				: p_options.type,
								'se_id'				: p_options.se_id,
								'token'				: style_edit_sectoken,
								'current_template'	: style_edit_config_CURRENT_TEMPLATE
							}
						);							
						$(p_options.status_box).html(style_edit_img_loading);					
					},
					onComplete: function(t_file, t_response)
					{		
						/* check session */
						if(t_response.error == 666)
						{
							gmStyleEditControl.control_session(t_response);
						}
						else
						{							
							/* return message */
							$(p_options.status_box).html(t_response.message);		
							if(p_options.module == 'se_image_upload' && t_response.error == -1)
							{	
								$('#style_edit_save_backward').css('visibility', 'visible');
								$('#style_edit_save_forward').css('visibility', 'hidden');		

								var t_new_image = t_response.data.image.replace('\\', '');
								
								if(typeof(gmStyleEditHandler.v_act_json) != 'undefined')
								{
									gmStyleEditHandler.v_act_json['background-image'] = t_new_image;
								}				

								gmStyleEditHandler.init_background_image_info(p_options.se_id, true);
								gmStyleEditHandler.v_act_bg_img = t_new_image;
								$(p_options.se_selector).css('background-image', t_new_image);
								gmStyleEditHandler.change_style('background-image', t_new_image);
							}
						}
					}		
				}
			);
		},

		jsoning: function(p_response) 
		{		
			if (typeof (JSON) !== 'undefined' && typeof (JSON.parse) === 'function')
			{
				return JSON.parse(p_response);
			}
			else
			{
				return eval('(' + p_response + ')');
			}
		}
	});
})(jQuery);


(function($)
{
	/*
	*	attach method to jQuery  
	*/
 	$.fn.extend(
	{ 
		/*
		*	plugin definition
		*/
		exporter: function(p_options) 
		{
			/*	set default values	*/
			var v_defaults = {};
			
			/*	extend defaults with main options	*/
			var v_options = $.extend(v_defaults, p_options);
			
			/*	iterate & reformat each matched element	*/
    		return this.each(
				function() 
				{
					/*	build element specific options	*/
					var v_element_opt = v_options;
										
					/* call method on click event */
					$(this).click(
						function()
						{
							$(this)._export();
						}
					);
    			}
			);
    	},

		_export: function() 
		{		
			var t_response		= new Object();
			var t_se_filename	= $('#se_export_name').val();

			jQuery.ajax({ 
				data:		'module=se_export&se_filename=' + t_se_filename + '&token='+style_edit_sectoken + '&current_template=' + style_edit_config_CURRENT_TEMPLATE, 
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
					t_response  = p_response; 
					/* check session */
					if(t_response.error == 666)
					{
						gmStyleEditControl.control_session(t_response);
					}
					else
					{
						$('#se_message').html(t_response.message);
					}
				}				 
			});			
		}
	});
})(jQuery);


(function($)
{
	/*
	*	attach method to jQuery  
	*/
 	$.fn.extend(
	{ 
		/*
		*	plugin definition
		*/
		export_name: function(p_options) 
		{
			/*	set default values	*/
			var v_defaults = {};
			
			/*	extend defaults with main options	*/
			var v_options = $.extend(v_defaults, p_options);
			
			/*	iterate & reformat each matched element	*/
    		return this.each(
				function() 
				{
					/*	build element specific options	*/
					var v_element_opt = v_options;
										
					/* call method on click event */
					$(this).get_export_name();
    			}
			);
    	},

		get_export_name: function() 
		{		
			var t_response = new Object();

			jQuery.ajax({ 
				data:		'module=se_export_name' + '&token='+style_edit_sectoken + '&current_template=' + style_edit_config_CURRENT_TEMPLATE, 
				url:		'StyleEdit/style_edit_request.php', 
				dataType:	'json', 
				type:		"POST", 
				async:		false, 
				success:	function(p_response) 
				{ 
					t_response  = p_response; 
				
					/* check session */
					if(t_response.error == 666)
					{
						gmStyleEditControl.control_session(t_response);
					}
					else
					{
						$('#se_export_name').val(t_response.message);	
					}
				}				 
			});				
		}
	});
})(jQuery);

(function($)
{
	/*
	*	attach method to jQuery  
	*/
 	$.fn.extend(
	{ 
		/*
		*	plugin definition
		*/
		importer: function(p_options) 
		{
			/*	set default values	*/
			var v_defaults = {};
			
			/*	extend defaults with main options	*/
			var v_options = $.extend(v_defaults, p_options);
			
			/*	iterate & reformat each matched element	*/
    		return this.each(
				function() 
				{
					/*	build element specific options	*/
					var v_element_opt = v_options;
										
					/* call method on click event */
					$(this).click(
						function()
						{
							$(this)._import();
						}
					);
    			}
			);
    	},

		_import: function() 
		{		
			var t_response = new Object();

			var t_file_input_id = $(this).attr('id');

			var t_file_id =t_file_input_id.replace('se_import_id_', '');

			jQuery.ajax({ 
				data:		'module=se_import' + '&file_id=' + t_file_id + '&token='+style_edit_sectoken + '&current_template=' + style_edit_config_CURRENT_TEMPLATE, 
				url:		'StyleEdit/style_edit_request.php', 
				dataType:	'json', 
				type:		"POST", 
				async:		true,
				beforeSend:	function() 
				{
					$('body').addClass('wait');					
				},
				success:	function(p_response) 
				{
					$('body').removeClass('wait');
					t_response  = p_response;

					/* check session */
					if(t_response.error == 666)
					{
						gmStyleEditControl.control_session(t_response);
					}
					else
					{
						t_confirm = confirm(t_response.message);

						if(t_confirm == true && style_edit_config_SOS == false)
						{
							location.reload();
						}
						else if(t_confirm == true && style_edit_config_SOS == true)
						{
							location.href = style_edit_config_EDIT_CALL;
						}
					}
				}				 
			});			
		}
	});
})(jQuery);


(function($)
{
	/*
	*	attach method to jQuery  
	*/
 	$.fn.extend(
	{ 
		/*
		*	plugin definition
		*/
		deleter: function(p_options) 
		{
			/*	set default values	*/
			var v_defaults = 
			{
				module_confirm:		'',
				status_box:			'',
				se_id:				'',
				se_selector:		'',
				v_gmslc:			''
			};
			
			/*	extend defaults with main options	*/
			var v_options = $.extend(v_defaults, p_options);
			
			/*	iterate & reformat each matched element	*/
    		return this.each(
				function() 
				{
					/*	build element specific options	*/
					var v_element_opt = v_options;
										
					/* call method on click event */
					$(this).click(
						function()
						{
							$(this)._delete_confirm(v_element_opt);
						}
					);
    			}
			);
    	},

		_delete_confirm: function(p_options) 
		{					
			var t_file_id;

			if(p_options.module_confirm == 'se_delete_confirm')
			{
				var t_file_input_id = $(this).attr('id');
				t_file_id			= t_file_input_id.replace('se_delete_id_', '');
			}
			else
			{
				t_file_id			= p_options.se_id;
			}

			jQuery.ajax({ 
				data:		'module=' + p_options.module_confirm + 
							'&file_id=' + t_file_id +
							'&background_image=' + encodeURIComponent(gmStyleEditHandler.get_background_image_filename()) +
							'&token='+style_edit_sectoken +
							'&current_template=' + style_edit_config_CURRENT_TEMPLATE,
				url:		'StyleEdit/style_edit_request.php', 
				dataType:	'json', 
				type:		"POST", 
				async:		false, 
				success:	function(p_response) 
				{ 
					t_response  = p_response; 

					/* check session */
					if(t_response.error == 666)
					{
						gmStyleEditControl.control_session(t_response);
					}
					else
					{
						t_confirm = confirm(t_response.message);

						if(t_confirm == true)
						{
							if(p_options.module_confirm == 'se_delete_confirm')
							{
								$(this)._delete(t_file_id);
							}
							else 
							{
								$(this)._delete_image(p_options);
							}
						}
					}
				}				 
			});				
		},

		_delete: function(p_id) 
		{		
			var t_response = new Object();

			var t_file_id		= p_id;
			
			var t_archive_id	= 'se_archive_id_' + t_file_id;
			
			t_archive_id = t_archive_id.replace('.', '_');

			jQuery.ajax({ 
				data:		'module=se_delete' + '&file_id=' + t_file_id + '&token='+style_edit_sectoken + '&current_template=' + style_edit_config_CURRENT_TEMPLATE, 
				url:		'StyleEdit/style_edit_request.php', 
				dataType:	'json', 
				type:		"POST", 
				async:		false, 
				success:	function(p_response) 
				{ 
					t_response  = p_response; 
				}				 
			});				

			if(t_response.data_count < 6)
			{
				$("#se_archivizer_slider").hide();
				$("#se_archivizer_content").attr({scrollTop: 0});
			}

			if(t_response.error == -4)
			{
				$('#' + t_archive_id).remove();	
			}
			else
			{
				alert(t_response.message);	
			}
		},

		_delete_image: function(p_options) 
		{	
			var t_response = new Object();

			jQuery.ajax({ 
				data:		'module=se_image_delete' + 
							'&se_id=' + p_options.se_id +
							'&background_image=' + encodeURIComponent(gmStyleEditHandler.get_background_image_filename()) + 
							'&token='+style_edit_sectoken +
							'&current_template=' + style_edit_config_CURRENT_TEMPLATE,
				url:		'StyleEdit/style_edit_request.php', 
				dataType:	'json', 
				type:		"POST", 
				async:		false, 
				beforeSend:	function() 
				{ 
					$(p_options.status_box).html(style_edit_img_loading);	
				},
				success:	function(p_response) 
				{ 
					t_response  = p_response; 
				}				 
			});				

			if(t_response.error == -4)
			{	
				$('#style_edit_save_backward').css('visibility', 'visible');
				$('#style_edit_save_forward').css('visibility', 'hidden');		
				gmStyleEditHandler.v_act_bg_img = 'none';
				gmStyleEditHandler.init_background_image_info(p_options.se_id);		
				gmStyleEditHandler.change_style('background-image','none');
				gmStyleEditHandler.v_act_json['background-image'] = 'none';
				gmslc.delete_css_backup_background_img(p_options.se_selector, 'none');

				// reload page to see all updated background-image-styles
				location.reload();
			}
			$(p_options.status_box).html(t_response.message);
		}
	});
})(jQuery);



(function($)
{
	/*
	*	attach method to jQuery  
	*/
 	$.fn.extend(
	{ 
		/*
		*	plugin definition
		*/
		viewer: function(p_options) 
		{
			/*	set default values	*/
			var v_defaults = {};
			
			/*	extend defaults with main options	*/
			var v_options = $.extend(v_defaults, p_options);
			
			/*	iterate & reformat each matched element	*/
    		return this.each(
				function() 
				{
					/*	build element specific options	*/
					var v_element_opt = v_options;
										
					/* call method on click event */
					$(this).click(
						function()
						{
							var t_response = new Object();

							var t_file_input_id = $(this).attr('id');

							var t_file_id = t_file_input_id.replace('se_open_', '');
							
							window.location.href = 'StyleEdit/style_edit_request.php?module=se_view' + '&file_id=' + t_file_id + '&token='+style_edit_sectoken + '&current_template=' + style_edit_config_CURRENT_TEMPLATE;
						}
					);
    			}
			);
    	}	
	});
})(jQuery);
