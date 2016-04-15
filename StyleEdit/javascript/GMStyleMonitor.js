/* --------------------------------------------------------------
  StyleEdit v2.0
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  --------------------------------------------------------------
*/


function GMStyleMonitor()
{
	/*
	*	@var String 
	*/
	var v_style_monitor_layer = '#style_monitor_layer';	
	
	/*
	*	@var object 
	*/
	var coo_this = this;
	
	/*
	*	Constructor
	*	@return void
	*/
	this.init = function(p_event)
	{
		var t_url = window.location.href;
		if(t_url.search('#') !== -1)
		{
			t_url = t_url.substr(0, t_url.search('#'));
		}
		
		/* load toolbox */
		var t_style_monitor_layer = jQuery.ajax(
			{ 
				data:	'module=se_monitor&current_template=' + style_edit_config_CURRENT_TEMPLATE + '&url=' + encodeURIComponent(t_url) + '&token='+style_edit_sectoken, 
				url:	'StyleEdit/style_edit_request.php', 
				type:	"POST", 
				async:	false 
			}
		).responseText;

		$(v_style_monitor_layer).remove();

		$('body').append(t_style_monitor_layer);

		this.hide_monitor();

	    //$('#style_monitor_tabs').tabs();
        
		$(v_style_monitor_layer).draggable(
			{ 
				handle:			'#style_editor_tabs',
				cursor:			'move', 
				containment:	'body'
			}
		);
		
		$('.se_monitor_multi_save').click(
			function()			
			{
				coo_this.save_actual_style('', '#se_monitor_multi_input');
			}
		);

		$('.se_monitor_single_save').click(
			function()			
			{
				var t_selector = $('.se_monitor_single_save').attr('title');

				coo_this.save_actual_style(t_selector, '#se_monitor_single_input');

				//$(t_selector).unbind('click').unbind('mouseover').unbind('mouseout');

			}
		);

		$('.se_monitor_delete').click(
			function()			
			{
				var t_selector = $('.se_monitor_delete').attr('title');

				coo_this.delete_actual_style(t_selector);
			}
		);

		$('#se_monitor_close').click(
			function()			
			{
				coo_this.hide_monitor(); 
			}
		);		
	}

	/*
	*	function to delete the actual style
	*	@param String p_selector
	*	@return void
	*/
	this.delete_actual_style = function(p_selector)
	{
		var t_url = window.location.href;
		if(t_url.search('#') !== -1)
		{
			t_url = t_url.substr(0, t_url.search('#'));
		}
		
		var t_selector = jQuery.ajax(
			{ 
				data:	'module=se_monitor_delete&current_template=' + style_edit_config_CURRENT_TEMPLATE + '&url=' + encodeURIComponent(t_url) + '&token='+style_edit_sectoken + "&se_selector=" + p_selector, 
				url:	'StyleEdit/style_edit_request.php', 
				type:	"POST", 
				async:	false 
			}
		).responseText;
		
		$(p_selector).unbind('click').unbind('mouseover').unbind('mouseout');

		return;
	}

	/*
	*	function to show the StyleMonitor
	*	@param String p_selector
	*	@return void
	*/
	this.save_actual_style = function(p_selector, p_textarea_id)
	{
		var t_url = window.location.href;
		if(t_url.search('#') !== -1)
		{
			t_url = t_url.substr(0, t_url.search('#'));
		}
		
		var t_styles = $(p_textarea_id).val();

		var t_selector = jQuery.ajax(
			{ 
				data:	'module=se_monitor_save&current_template=' + style_edit_config_CURRENT_TEMPLATE + '&url=' + encodeURIComponent(t_url) + '&token='+style_edit_sectoken + '&se_styles=' + t_styles + "&se_selector=" + p_selector, 
				url:	'StyleEdit/style_edit_request.php', 
				type:	"POST", 
				async:	false 
			}
		).responseText;
		
		gmStyleEditHandler.load_styles(t_selector);

		return;
	}

	/*
	*	function to show the StyleMonitor
	*	@return void
	*/
	this.load_actual_style = function(p_selector, p_styles)
	{
		var t_styles = p_selector + "\n{" + p_styles + "\n}";
		
		$('.se_monitor_single_save, .se_monitor_delete').attr('title', p_selector);

		$('#se_monitor_single_input').val(t_styles);

		return;
	}

	/*
	*	function to show the StyleMonitor
	*	@return void
	*/
	this.show_monitor = function()
	{
		$(v_style_monitor_layer).show();

        $('#style_editor_tabs').tabs("option", "active", 0);
		return;
	}

	/*
	*	function to show the StyleMonitor
	*	@return void
	*/
	this.hide_monitor = function()
	{
		$(v_style_monitor_layer).hide();
		return;
	}
}