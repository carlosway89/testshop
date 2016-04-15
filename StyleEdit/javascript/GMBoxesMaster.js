/* --------------------------------------------------------------
  StyleEdit v2.0
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2010 Gambio GmbH
  --------------------------------------------------------------
*/

function GMBoxesMaster(p_gmBoxesPageMenu)
{
	var boxes_edit_active = false;
	
	
	this.set_boxes_edit_active = function(boxes_edit_on) 
	{
		if(boxes_edit_on == true) {
			activate_drag_mode();		//switch ON
		} else {
			
			deactivate_drag_mode();	//switch OFF
		}
		boxes_edit_active = boxes_edit_on;
	}
	
	this.get_boxes_edit_active = function() 
	{
		return boxes_edit_active;
	}
	
	this.save_box = function(dd_box)
	{
		var box_name = dd_box.children().attr("class");
		var pos_name = dd_box.parent().attr('id');
		
		if(fb)console.log('BOX: ' + box_name);
		if(fb)console.log('POS: ' + pos_name);
		
		jQuery.ajax({
			data: 		'current_template=' + style_edit_config_CURRENT_TEMPLATE + '&box_name='+box_name +'&position='+pos_name,
			url: 		"StyleEdit/style_edit_request.php?module=boxes_edit&act=update_position&token="+style_edit_sectoken,
			type: 		"POST",
			timeout: 	3000,
			error: 		function(){ if(fb)console.log("update_position save_box error"); },
			success: 	function(){ if(fb)console.log("update_position save_box success"); }
		});
	}
	
	this.refresh_status_switch = function()
	{
		$.ajax({
			url: 		'StyleEdit/style_edit_request.php?module=boxes_edit&act=get_status_json&current_template=' + style_edit_config_CURRENT_TEMPLATE + '&token='+style_edit_sectoken,
			data: 		'current_template=' + style_edit_config_CURRENT_TEMPLATE,
			type: 		"POST",
			dataType:	'json',
			cache: 		false,
			success: 	function(cresult)
		  					{
		  						var box_name 		= '';
		  						var box_status 	= '';
		  						$('.c_box_status_switch_active').removeClass('c_box_status_switch_active');
		  						
		    					for(var i=0; i<cresult.box_name.length; i++) 
		    					{
		    						box_name 		= cresult.box_name[i];
		    						box_status 	= cresult.box_status[i];
		    						
		    						if(box_status == '1') {
		    							$('.c_' + box_name).find('.c_box_status_switch_on').addClass('c_box_status_switch_active');
		    						} else {
		    							$('.c_' + box_name).find('.c_box_status_switch_off').addClass('c_box_status_switch_active');
		    						}
		    						
		    						if(fb)console.log('box: ' + box_name + ', status: ' + box_status);
		    					}
		  					}
		});
		//gmBoxesMaster.spread_boxes();
	}
	
	this.spread_boxes = function(start_i, end_i)
	{
		var sorted_boxes 	= new Array();
		
		var box_pos_html 	= '';
		var current_pos		= '';
		var current_dd		= '';
		
		for(var i=start_i; i<=end_i; i++) //COLLECT boxes
		{
			current_pos  = '#gm_box_pos_' + i;
			box_pos_html = $(current_pos + ' .gm_box_container_dd').html();
			
			if(box_pos_html.length > 10) {
				if(fb)console.log('box_pos used: ' + $(current_pos).attr('id') );
				
				sorted_boxes.push(' '); //leave empty
				sorted_boxes.push(box_pos_html);
				
				$(current_pos + ' .gm_box_container_dd').html('&nbsp;');
			}
		}
		if(fb)console.log('boxes added: ' + sorted_boxes.length);

		for(var i=0; i<sorted_boxes.length; i++) //SPREAD boxes
		{
			current_dd  = '#gm_box_pos_' + (i + start_i) + ' .gm_box_container_dd';
			$(current_dd).html(sorted_boxes[i]);
			
			if(sorted_boxes[i].length > 10) {
				//gmBoxesMaster.save_box($(current_dd));
			}
		}
		
		$(".gm_box_container_dd").each(function() {
			if($(this).html().length < 10) $(this).html('&nbsp;');
		});

		$('.c_box_status_container .c_box_status').click(c_box_status_container_CLICK_listener);
		
		$('.c_box_status_container .c_box_page_menu').click(
			function()
			{
				p_gmBoxesPageMenu.load_page_menu(this);
				return false;
			}
		);
	}

	
	function activate_drag_mode() 
	{
		$(".gm_box_container").append(' ');
		$(".gm_box_container").wrapInner('<div class="gm_box_container_dd"></div>');
		
		$(".gm_box_container_dd").each(function() {
			if($(this).html().length < 10) $(this).html('&nbsp;');
		});
		
		$(".gm_box_container_dd").draggable({helper: 'clone'});
		$(".gm_box_container_dd").addClass('gm_box_container_ready');
		
		$(".gm_box_container_dd").droppable({
			accept: ".gm_box_container_dd",
			activeClass: 'gm_box_container_active',
			hoverClass: 'gm_box_container_hover',
			tolerance: 'pointer',
			drop: function(ev, ui)
			{
				var source_html = $(ui.draggable).html();
				var target_html = $(this).html();
				$(this).html(source_html);
				$(ui.draggable).html(target_html);
				
				gmBoxesMaster.save_box($(ui.draggable));
				gmBoxesMaster.save_box($(this));
				
				gmBoxesMaster.spread_boxes(1, 100);
				gmBoxesMaster.spread_boxes(101, 200);
			}
		});
				
//		$('.c_boxhead').prepend('<div class="c_box_status_container"><table border="0" width="100%" cellspacing="2" cellpadding="2"><tr><td valign="middle" align="left"><a class="c_box_status_switch_on c_box_status" href="1">EIN</a> | <a class="c_box_status_switch_off c_box_status" href="0">AUS</a></td><td valign="middle" align="right"><a class="c_box_page_menu" href="0">SEITENAUSWAHL</a></td></tr></table></div>');
		$('.c_boxhead').prepend('<div class="c_box_status_container"><table border="0" width="100%" cellspacing="2" cellpadding="2"><tr><td valign="middle" align="left"><a class="c_box_status_switch_on c_box_status" href="1">EIN</a> | <a class="c_box_status_switch_off c_box_status" href="0">AUS</a></td><td valign="middle" align="right">&nbsp;</td></tr></table></div>');
		$('.c_box_status_container .c_box_status').click(c_box_status_container_CLICK_listener);
		$('.c_box_status_container .c_box_page_menu').click(
			function()
			{
				p_gmBoxesPageMenu.load_page_menu(this);
				return false;
			}
		);

		gmBoxesMaster.refresh_status_switch();
	}
	
	function c_box_status_container_CLICK_listener()
	{
		var box_name 	= $(this).closest('.gm_box_container').find('div[id^="menubox_"]:first').attr('class');
		var box_status 	= $(this).attr('href');
		
		box_status = box_status.split('/');				//IE fix
		box_status = box_status[box_status.length - 1];	//IE fix
		
		if(fb)console.log('BOX: ' 		+ box_name);
		if(fb)console.log('STATUS: ' 	+ box_status);

		jQuery.ajax({
			data: 		'current_template=' + style_edit_config_CURRENT_TEMPLATE + '&box_name='+box_name +'&status='+box_status,
			url: 		"StyleEdit/style_edit_request.php?module=boxes_edit&act=update_status&token="+style_edit_sectoken,
			type: 		"POST",
			timeout: 	3000,
			error: 		function(){ if(fb)console.log("update_status save_box error"); },
			success: 	function(){
									if(fb)console.log("update_status save_box success");
									gmBoxesMaster.refresh_status_switch();
								}
		});
		return false;
	}
	
	function deactivate_drag_mode()
	{
		$(".gm_box_container_dd").draggable('destroy');
		$(".gm_box_container_dd").removeClass('gm_box_container_ready');
		$(".gm_box_container_dd").droppable('destroy');
		
		$('.gm_box_container_dd').each(function(){
			var content_html = $(this).html()
			$(this).parent().html(content_html);
		});
		
		$('.c_box_status_container').remove();
		gmslc.run_activation_function();
	}	
}