/* --------------------------------------------------------------
  StyleEdit v2.0
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  --------------------------------------------------------------
*/

function GMBoxesPageMenu()
{
	/*
	*	@var object
	*/
	var v_coo_this = this;

	/*
	*	@var string container of the page menu
	*/
	var v_page_menu_container = 'body';

	/*
	*	@var string containing id of the page menu
	*/
	var v_page_menu_box_id = '#se_boxes_page_menu';

	/*
	*	@var string containing id of the close button
	*/
	var v_page_menu_box_close_id = '#se_boxes_page_menu_close';

	/*
	*	function to laod the page menu
	*	@param	object p_element
	*	@return void
	*/
	this.load_page_menu = function(p_element)
	{
		var t_box_name 	= $(p_element).closest('.gm_box_container').find('div[id^="menubox_"]:first').attr('class');

		var t_page_menu = this.http_request(
			'StyleEdit/style_edit_request.php?module=boxes_edit&act=get_page_menu&token='+style_edit_sectoken,
			'current_template=' + style_edit_config_CURRENT_TEMPLATE + '&box_name='+t_box_name,
			'html',
			'POST'
		);

		$(v_page_menu_box_id).remove();

		$(v_page_menu_container).append(t_page_menu);

		$(v_page_menu_box_id).css(
			{
				position:	'absolute',
				top:		$(p_element).offset().top + 'px',
				left:		10 + $(p_element).offset().left + $(p_element).outerWidth()	+ 'px'
			}			
		);		

		if($('#se_boxes_area_all').prop('checked') === true)
		{
			$(v_page_menu_box_id + ' input[type="checkbox"]').prop('checked', true);
		}

		/*
		*	handle actions in the page menu
		*/
		this.page_menu_handler(t_box_name);
	}

	/*
	*	function to close the page menu
	*	@param string p_box_name
	*	@return	void
	*/
	this.page_menu_handler = function(p_box_name)
	{
		/*
		*	page menu close handler
		*/
		$(v_page_menu_box_close_id).click(
			function()
			{
				$(v_page_menu_box_id).remove();
			}
		);

		/*
		*	page menu checkbox handler
		*/
		$(v_page_menu_box_id + ' input[type="checkbox"]').click(
			function()
			{				
				var t_id			= $(this).attr('id').replace('se_boxes_area_', '');

				var t_page_active	= $(this).prop('checked');

				if(t_id == 'all' && t_page_active === true)
				{
					$(v_page_menu_box_id + ' input[type="checkbox"]').prop('checked', true);
				}
				else if(t_id == 'all' && t_page_active === false)
				{
					$(v_page_menu_box_id + ' input[type="checkbox"]').prop('checked', false);
				}
				
				if(t_id != 'all')
				{
					$('#se_boxes_area_all').prop('checked', false);
				}
					
				var t_page_menu = v_coo_this.http_request(
					'StyleEdit/style_edit_request.php?module=boxes_edit&act=update_page_menu&token='+style_edit_sectoken,
					'current_template=' + style_edit_config_CURRENT_TEMPLATE + '&box_name='+p_box_name + '&page_id=' + t_id + '&page_active=' + t_page_active,
					'html',
					'POST'
				);
			}
		);
		return;
	}

	/*
	*	function to perform a http request
	*	@param	string p_url
	*	@param	string p_param
	*	@param	string p_data_type
	*	@param	string p_request_type
	*	@return object
	*/
	this.http_request = function(p_url, p_param, p_data_type, p_request_type)
	{
		var t_data = new Object;
		jQuery.ajax(
			{ 
				data:		p_param, 
				url:		p_url, 
				dataType:	p_data_type, 
				type:		p_request_type, 
				async:		false, 
				success:	function(p_data) 
			   { 
					t_data = p_data;
			   } 
			}
		);	
		return t_data;	
	}
}