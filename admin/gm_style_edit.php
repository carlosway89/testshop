<?php
/* --------------------------------------------------------------
   gm_style_edit.php 2015-09-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(configuration.php,v 1.40 2002/12/29); www.oscommerce.com
   (c) 2003	 nextcommerce (configuration.php,v 1.16 2003/08/19); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: configuration.php 1125 2005-07-28 09:59:44Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

	require('includes/application_top.php');
	require_once (DIR_FS_CATALOG . 'gm/inc/gm_create_corner.inc.php');


	if(!empty($_POST)) {
		@unlink(DIR_FS_CATALOG . 'cache/__dynamics.css');
	}

	function get_style_value_name($style_value_name){
		$new_style_value_name = str_replace('-', '_', strtoupper($style_value_name));
		if(defined('GM_STYLE_VALUE_' . $new_style_value_name)) return constant('GM_STYLE_VALUE_' . $new_style_value_name);
		else return $style_value_name;
	}


	function get_style($class, $attribute, $input_name, $hover = false){

		$value = '';

		if(!$hover){
			$get_style = xtc_db_query("SELECT sc.style_value
																	FROM
																		gm_css_style s,
																		gm_css_style_content sc
																	WHERE
																		s.style_name = '" . $class . "'
																		AND s.gm_css_style_id = sc.gm_css_style_id
																		AND sc.style_attribute = '" . $attribute . "'
																		LIMIT 1");
			if(xtc_db_num_rows($get_style) == 1){
				$row = xtc_db_fetch_array($get_style);
				$value = $row['style_value'];
			}
		}
		else{
			$value = gm_get_conf($class);
		}

		$get_fonts = xtc_db_query("SELECT font FROM gm_css_style_fonts");

		while($row = xtc_db_fetch_array($get_fonts)) {
			$font_family[] = array('id' => $row['font'], 'text' => $row['font']);
		}

		$size[] = array('id' => '6px', 'text' => '6px');
		$size[] = array('id' => '7px', 'text' => '7px');
		$size[] = array('id' => '8px', 'text' => '8px');
		$size[] = array('id' => '9px', 'text' => '9px');
		$size[] = array('id' => '10px', 'text' => '10px');
		$size[] = array('id' => '11px', 'text' => '11px');
		$size[] = array('id' => '12px', 'text' => '12px');
		$size[] = array('id' => '13px', 'text' => '13px');
		$size[] = array('id' => '14px', 'text' => '14px');
		$size[] = array('id' => '15px', 'text' => '15px');
		$size[] = array('id' => '16px', 'text' => '16px');
		$size[] = array('id' => '17px', 'text' => '17px');
		$size[] = array('id' => '18px', 'text' => '18px');
		$size[] = array('id' => '19px', 'text' => '19px');
		$size[] = array('id' => '20px', 'text' => '20px');
		$size[] = array('id' => '22px', 'text' => '22px');
		$size[] = array('id' => '24px', 'text' => '24px');
		$size[] = array('id' => '26px', 'text' => '26px');
		$size[] = array('id' => '28px', 'text' => '28px');
		$size[] = array('id' => '36px', 'text' => '36px');
		$size[] = array('id' => '48px', 'text' => '48px');
		$size[] = array('id' => '72px', 'text' => '72px');

		$font_weight[] = array('id' => 'normal', 'text' => get_style_value_name('normal'));
		$font_weight[] = array('id' => 'bold', 'text' => get_style_value_name('bold'));

		$font_style[] = array('id' => 'normal', 'text' => get_style_value_name('normal'));
		$font_style[] = array('id' => 'italic', 'text' => get_style_value_name('italic'));
		$font_style[] = array('id' => 'oblique', 'text' => get_style_value_name('oblique'));

		$text_align[] = array('id' => 'left', 'text' => get_style_value_name('left'));
		$text_align[] = array('id' => 'center', 'text' => get_style_value_name('center'));
		$text_align[] = array('id' => 'right', 'text' => get_style_value_name('right'));

		$vertical_align[] = array('id' => 'top', 'text' => get_style_value_name('top'));
		$vertical_align[] = array('id' => 'middle', 'text' => get_style_value_name('middle'));
		$vertical_align[] = array('id' => 'bottom', 'text' => get_style_value_name('bottom'));
		$vertical_align[] = array('id' => 'baseline', 'text' => get_style_value_name('baseline'));
		$vertical_align[] = array('id' => 'sub', 'text' => get_style_value_name('sub'));
		$vertical_align[] = array('id' => 'super', 'text' => get_style_value_name('super'));
		$vertical_align[] = array('id' => 'text-top', 'text' => get_style_value_name('text-top'));
		$vertical_align[] = array('id' => 'text-bottom', 'text' => get_style_value_name('text-bottom'));

		$border_style[] = array('id' => 'none', 'text' => get_style_value_name('none'));
		$border_style[] = array('id' => 'hidden', 'text' => get_style_value_name('hidden'));
		$border_style[] = array('id' => 'dotted', 'text' => get_style_value_name('dotted'));
		$border_style[] = array('id' => 'dashed', 'text' => get_style_value_name('dashed'));
		$border_style[] = array('id' => 'solid', 'text' => get_style_value_name('solid'));
		$border_style[] = array('id' => 'double', 'text' => get_style_value_name('double'));
		$border_style[] = array('id' => 'groove', 'text' => get_style_value_name('groove'));
		$border_style[] = array('id' => 'ridge', 'text' => get_style_value_name('ridge'));
		$border_style[] = array('id' => 'inset', 'text' => get_style_value_name('inset'));
		$border_style[] = array('id' => 'outset', 'text' => get_style_value_name('outset'));

		$clear[] = array('id' => 'none', 'text' => get_style_value_name('none'));
		$clear[] = array('id' => 'left', 'text' => get_style_value_name('left'));
		$clear[] = array('id' => 'right', 'text' => get_style_value_name('right'));
		$clear[] = array('id' => 'both', 'text' => get_style_value_name('both'));

		$float[] = array('id' => 'none', 'text' => get_style_value_name('none'));
		$float[] = array('id' => 'left', 'text' => get_style_value_name('left'));
		$float[] = array('id' => 'right', 'text' => get_style_value_name('right'));

		$text_decoration[] = array('id' => 'none', 'text' => get_style_value_name('none'));
		$text_decoration[] = array('id' => 'underline', 'text' => get_style_value_name('underline'));


		// return string
		$style_output = '';

		switch($attribute){
			case 'font-family':
				$style_output .= xtc_draw_pull_down_menu($input_name, $font_family, $value, 'id="' . $input_name . '"');
				break;
			case 'font-size':
				$style_output .= xtc_draw_pull_down_menu($input_name, $size, $value, 'id="' . $input_name . '"');
				break;
			case 'font-weight':
				$style_output .= xtc_draw_pull_down_menu($input_name, $font_weight, $value, 'id="' . $input_name . '"');
				break;
			case 'font-style':
				$style_output .= xtc_draw_pull_down_menu($input_name, $font_style, $value, 'id="' . $input_name . '"');
				break;
			case 'text-align':
				$style_output .= xtc_draw_pull_down_menu($input_name, $text_align, $value, 'id="' . $input_name . '"');
				break;
			case 'vertical-align':
				$style_output .= xtc_draw_pull_down_menu($input_name, $vertical_align, $value, 'id="' . $input_name . '"');
				break;
			case 'text-decoration':
				$style_output .= xtc_draw_pull_down_menu($input_name, $text_decoration, $value, 'id="' . $input_name . '"');
				break;
			case 'border-style':
				$style_output .= xtc_draw_pull_down_menu($input_name, $border_style, $value, 'id="' . $input_name . '"');
				break;
			case 'clear':
				$style_output .= xtc_draw_pull_down_menu($input_name, $clear, $value, 'id="' . $input_name . '"');
				break;
			case 'float':
				$style_output .= xtc_draw_pull_down_menu($input_name, $float, $value, 'id="' . $input_name . '"');
				break;
			case 'color':
			case 'background-color':
			case 'border-color':
			case 'border-top-color':
			case 'border-right-color':
			case 'border-bottom-color':
			case 'border-left-color':
				$style_output .= '<input type="text" name="' . $input_name . '" value="'.$value.'" id="' . $input_name . '" class="field_medium_size" />';
				$style_output .= ' <input class="gm_click"  type="button" id="color_'.$input_name.'" style="width:17px; height:17px; border: 1px solid black; cursor: pointer; background-color:'.$value.'" />';
				$style_string .= '	<input type="hidden" value="color_'.$id.' />';
				break;
			case 'background-image':
				if(create_bg_img_tag($input_name) != '') $style_output .= '<input type="file" name="' . $input_name . '" id="' . $input_name . '" /> ' . create_bg_img_tag($input_name) . ' <input style="width: auto" type="checkbox" name="delete_' . $input_name . '" value="1" /> L&ouml;schen';
				else  $style_output .= '<input type="file" name="' . $input_name . '" id="' . $input_name . '" />';
				break;
			default:
				$style_output .= '<input type="text" name="' . $input_name . '" id="' . $input_name . '" value="'.$value.'" class="field_medium_size" />';
		}


		return $style_output;
	}


	function update_style($class, $attribute, $value){
		if($attribute == 'background-image'){
			$value = 'url(backgrounds/' . $value . ')';
		}

		if($attribute == 'gm_corner'){
			$attribute = 'background-image';
		}

		$get_style_ids = xtc_db_query("SELECT gm_css_style_id
																		FROM gm_css_style
																		WHERE style_name LIKE '" . $class . "'");
		while($row = xtc_db_fetch_array($get_style_ids)){
			xtc_db_query("UPDATE gm_css_style_content
										SET style_value = '" . $value . "'
										WHERE
											gm_css_style_id = '" . $row['gm_css_style_id'] . "'
											AND style_attribute = '" . $attribute . "'");

		}
	}

	function delete_background_img($class){
		$get_style_ids = xtc_db_query("SELECT gm_css_style_id
																		FROM gm_css_style
																		WHERE style_name LIKE '" . $class . "'");
		while($row = xtc_db_fetch_array($get_style_ids)){
			$get_img_filenames = xtc_db_query("SELECT
																						style_value
																					FROM gm_css_style_content
																					WHERE
																						gm_css_style_id = '" . $row['gm_css_style_id'] . "'
																						AND style_attribute = 'background-image'");
			if(xtc_db_num_rows($get_img_filenames) == 1){
				$gm_img_css = xtc_db_fetch_array($get_img_filenames);
				$filename = str_replace('url(', '', $gm_img_css['style_value']);
				$filename = str_replace(')', '', $filename);
				$filename = basename($filename);
				@unlink(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/backgrounds/' . $filename);
			}

			xtc_db_query("UPDATE gm_css_style_content
										SET style_value = 'none'
										WHERE
											gm_css_style_id = '" . $row['gm_css_style_id'] . "'
											AND style_attribute = 'background-image'");
		}
	}

	function create_bg_img_tag($class){
		$output = '';

		if(substr_count($class, '_head_') > 0) $class = '#menubox_admin_head';
		elseif(substr_count($class, '_body_') > 0) $class = '#menubox_admin_body';
		else $class = '';

		$get_style = xtc_db_query("SELECT sc.style_value
																FROM
																	gm_css_style s,
																	gm_css_style_content sc
																WHERE
																	s.style_name = '" . $class . "'
																	AND s.gm_css_style_id = sc.gm_css_style_id
																	AND sc.style_attribute = 'background-image'
																	LIMIT 1");
		if(xtc_db_num_rows($get_style) == 1){
			$row = xtc_db_fetch_array($get_style);
			if($row['style_value'] != 'url()' && $row['style_value'] != '' && $row['style_value'] != 'none'){
				$filename = str_replace('url(', '', $row['style_value']);
				$filename = str_replace(')', '', $filename);
				$filename = basename($filename);

				$output = '<img src="' . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/backgrounds/' . $filename . '" />';
			}
		}

		return $output;
	}

	if(isset($_POST['send_shop_align'])){
		if($_POST['shop_align'] != 'justify')
        {
            if($_POST['shop_width']!='auto') $shop_width=gm_prepare_string($_POST['shop_width']);
            else $shop_width='978px';
          xtc_db_query("UPDATE gm_css_style_content
																												SET style_value = '" . $shop_width . "'
																												WHERE
																													gm_css_style_id = '" . gm_prepare_string($_POST['wrap_shop_id']) . "'
																													AND style_attribute = 'width'");

        }

       else xtc_db_query("UPDATE gm_css_style_content
												SET style_value = 'auto'
												WHERE
													gm_css_style_id = '" . gm_prepare_string($_POST['wrap_shop_id']) . "'
													AND style_attribute = 'width'");

		if($_POST['shop_align'] == 'center'){
			xtc_db_query("UPDATE gm_css_style_content
												SET style_value = 'auto'
												WHERE
													gm_css_style_id = '" . gm_prepare_string($_POST['wrap_shop_id']) . "'
													AND style_attribute = 'margin-left'");
			xtc_db_query("UPDATE gm_css_style_content
												SET style_value = 'auto'
												WHERE
													gm_css_style_id = '" . gm_prepare_string($_POST['wrap_shop_id']) . "'
													AND style_attribute = 'margin-right'");
		}
		elseif($_POST['shop_align'] == 'left'){
			xtc_db_query("UPDATE gm_css_style_content
												SET style_value = '3px'
												WHERE
													gm_css_style_id = '" . gm_prepare_string($_POST['wrap_shop_id']) . "'
													AND style_attribute = 'margin-left'");
			xtc_db_query("UPDATE gm_css_style_content
												SET style_value = 'auto'
												WHERE
													gm_css_style_id = '" . gm_prepare_string($_POST['wrap_shop_id']) . "'
													AND style_attribute = 'margin-right'");
		}
		elseif($_POST['shop_align'] == 'right'){
			xtc_db_query("UPDATE gm_css_style_content
												SET style_value = 'auto'
												WHERE
													gm_css_style_id = '" . gm_prepare_string($_POST['wrap_shop_id']) . "'
													AND style_attribute = 'margin-left'");
			xtc_db_query("UPDATE gm_css_style_content
												SET style_value = '3px'
												WHERE
													gm_css_style_id = '" . gm_prepare_string($_POST['wrap_shop_id']) . "'
													AND style_attribute = 'margin-right'");
		}

	}
	elseif(isset($_POST['send_topmenu']))
	{
			update_style('#topmenu_block', 'background-color', gm_prepare_string($_POST['topmenu_block_background_color']));
			@unlink(DIR_FS_CATALOG . DIR_WS_IMAGES . 'logos/gm_corner.gif');
			gm_create_corner();
	}
	elseif(isset($_POST['send_menuboxes'])){

		if($_POST['delete_menubox_head_background_image'] == 1){
			delete_background_img('#menubox_%_head');
		}
		if($_POST['delete_menubox_body_background_image'] == 1){
			delete_background_img('#menubox_%_body');
		}

		$head_background_image = xtc_try_upload('menubox_head_background_image', DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/backgrounds/', '777', array('gif', 'jpg', 'jpeg', 'png'));
		$body_background_image = xtc_try_upload('menubox_body_background_image', DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/backgrounds/', '777', array('gif', 'jpg', 'jpeg', 'png'));


		update_style('#menubox_%_head', 'background-color', gm_prepare_string($_POST['menubox_head_background_color']));
		if(!empty($head_background_image->filename)) update_style('#menubox_%_head', 'background-image', $head_background_image->filename);
		update_style('#menubox_%_head', 'color', gm_prepare_string($_POST['menubox_head_color']));
		update_style('#menubox_%_head', 'font-family', gm_prepare_string($_POST['menubox_head_font_family']));
		update_style('#menubox_%_head', 'font-size', gm_prepare_string($_POST['menubox_head_font_size']));
		update_style('#menubox_%_head', 'font-weight', gm_prepare_string($_POST['menubox_head_font_weight']));
		update_style('#menubox_%_head', 'font-style', gm_prepare_string($_POST['menubox_head_font_style']));
		update_style('#menubox_%_head a', 'font-family', gm_prepare_string($_POST['menubox_head_font_family']));
		update_style('#menubox_%_head a', 'font-size', gm_prepare_string($_POST['menubox_head_font_size']));
		update_style('#menubox_%_head a', 'font-weight', gm_prepare_string($_POST['menubox_head_font_weight']));
		update_style('#menubox_%_head a', 'font-style', gm_prepare_string($_POST['menubox_head_font_style']));
		update_style('#menubox_%_head', 'height', gm_prepare_string($_POST['menubox_head_height']));
		update_style('#menubox_%_head', 'text-decoration', gm_prepare_string($_POST['menubox_head_text_decoration']));

		update_style('#content_box_new_products_default_head', 'background-color', gm_prepare_string($_POST['menubox_head_background_color']));
		if(!empty($head_background_image->filename)) update_style('#content_box_new_products_default_head', 'background-image', $head_background_image->filename);
		update_style('#content_box_new_products_default_head', 'color', gm_prepare_string($_POST['menubox_head_color']));
		update_style('#content_box_new_products_default_head', 'font-family', gm_prepare_string($_POST['menubox_head_font_family']));
		update_style('#content_box_new_products_default_head', 'font-size', gm_prepare_string($_POST['menubox_head_font_size']));
		update_style('#content_box_new_products_default_head', 'font-weight', gm_prepare_string($_POST['menubox_head_font_weight']));
		update_style('#content_box_new_products_default_head', 'font-style', gm_prepare_string($_POST['menubox_head_font_style']));
		update_style('#content_box_new_products_default_head', 'height', gm_prepare_string($_POST['menubox_head_height']));
		update_style('#content_box_new_products_default_head', 'text-decoration', gm_prepare_string($_POST['menubox_head_text_decoration']));

		update_style('#content_box_specials_head', 'background-color', gm_prepare_string($_POST['menubox_head_background_color']));
		if(!empty($head_background_image->filename)) update_style('#content_box_specials_head', 'background-image', $head_background_image->filename);
		update_style('#content_box_specials_head', 'color', gm_prepare_string($_POST['menubox_head_color']));
		update_style('#content_box_specials_head', 'font-family', gm_prepare_string($_POST['menubox_head_font_family']));
		update_style('#content_box_specials_head', 'font-size', gm_prepare_string($_POST['menubox_head_font_size']));
		update_style('#content_box_specials_head', 'font-weight', gm_prepare_string($_POST['menubox_head_font_weight']));
		update_style('#content_box_specials_head', 'font-style', gm_prepare_string($_POST['menubox_head_font_style']));
		update_style('#content_box_specials_head', 'height', gm_prepare_string($_POST['menubox_head_height']));
		update_style('#content_box_specials_head', 'text-decoration', gm_prepare_string($_POST['menubox_head_text_decoration']));


		update_style('#products_media_head', 'background-color', gm_prepare_string($_POST['menubox_head_background_color']));
		if(!empty($head_background_image->filename)) update_style('#content_box_specials_head', 'background-image', $head_background_image->filename);
		update_style('#products_media_head', 'color', gm_prepare_string($_POST['menubox_head_color']));
		update_style('#products_media_head', 'font-family', gm_prepare_string($_POST['menubox_head_font_family']));
		update_style('#products_media_head', 'font-size', gm_prepare_string($_POST['menubox_head_font_size']));
		update_style('#products_media_head', 'font-weight', gm_prepare_string($_POST['menubox_head_font_weight']));
		update_style('#products_media_head', 'font-style', gm_prepare_string($_POST['menubox_head_font_style']));
		update_style('#products_media_head', 'height', gm_prepare_string($_POST['menubox_head_height']));
		update_style('#products_media_head', 'text-decoration', gm_prepare_string($_POST['menubox_head_text_decoration']));

		update_style('#products_reviews_head', 'background-color', gm_prepare_string($_POST['menubox_head_background_color']));
		if(!empty($head_background_image->filename)) update_style('#content_box_specials_head', 'background-image', $head_background_image->filename);
		update_style('#products_reviews_head', 'color', gm_prepare_string($_POST['menubox_head_color']));
		update_style('#products_reviews_head', 'font-family', gm_prepare_string($_POST['menubox_head_font_family']));
		update_style('#products_reviews_head', 'font-size', gm_prepare_string($_POST['menubox_head_font_size']));
		update_style('#products_reviews_head', 'font-weight', gm_prepare_string($_POST['menubox_head_font_weight']));
		update_style('#products_reviews_head', 'font-style', gm_prepare_string($_POST['menubox_head_font_style']));
		update_style('#products_reviews_head', 'height', gm_prepare_string($_POST['menubox_head_height']));
		update_style('#products_reviews_head', 'text-decoration', gm_prepare_string($_POST['menubox_head_text_decoration']));

		update_style('.product_info_add_ons .product_info_add_ons_head', 'background-color', gm_prepare_string($_POST['menubox_head_background_color']));
		if(!empty($head_background_image->filename)) update_style('.product_info_add_ons .product_info_add_ons_head', 'background-image', $head_background_image->filename);
		update_style('.product_info_add_ons .product_info_add_ons_head', 'color', gm_prepare_string($_POST['menubox_head_color']));
		update_style('.product_info_add_ons .product_info_add_ons_head', 'font-family', gm_prepare_string($_POST['menubox_head_font_family']));
		update_style('.product_info_add_ons .product_info_add_ons_head', 'font-size', gm_prepare_string($_POST['menubox_head_font_size']));
		update_style('.product_info_add_ons .product_info_add_ons_head', 'font-weight', gm_prepare_string($_POST['menubox_head_font_weight']));
		update_style('.product_info_add_ons .product_info_add_ons_head', 'font-style', gm_prepare_string($_POST['menubox_head_font_style']));
		update_style('.product_info_add_ons .product_info_add_ons_head', 'height', gm_prepare_string($_POST['menubox_head_height']));
		update_style('.product_info_add_ons .product_info_add_ons_head', 'text-decoration', gm_prepare_string($_POST['menubox_head_text_decoration']));

		update_style('#content_box_new_products_main_head', 'background-color', gm_prepare_string($_POST['menubox_head_background_color']));
		if(!empty($head_background_image->filename)) update_style('#content_box_new_products_main_head', 'background-image', $head_background_image->filename);
		update_style('#content_box_new_products_main_head', 'color', gm_prepare_string($_POST['menubox_head_color']));
		update_style('#content_box_new_products_main_head', 'font-family', gm_prepare_string($_POST['menubox_head_font_family']));
		update_style('#content_box_new_products_main_head', 'font-size', gm_prepare_string($_POST['menubox_head_font_size']));
		update_style('#content_box_new_products_main_head', 'font-weight', gm_prepare_string($_POST['menubox_head_font_weight']));
		update_style('#content_box_new_products_main_head', 'font-style', gm_prepare_string($_POST['menubox_head_font_style']));
		update_style('#content_box_new_products_main_head', 'height', gm_prepare_string($_POST['menubox_head_height']));
		update_style('#content_box_new_products_main_head', 'text-decoration', gm_prepare_string($_POST['menubox_head_text_decoration']));

		update_style('#menubox_categories .categories', 'background-color', gm_prepare_string($_POST['menubox_body_background_color']));
		update_style('#menubox_categories a', 'color', gm_prepare_string($_POST['menubox_body_color']));
		update_style('#menubox_categories a', 'font-family', gm_prepare_string($_POST['menubox_body_font_family']));
		update_style('#menubox_categories a', 'font-size', gm_prepare_string($_POST['menubox_body_font_size']));
		update_style('#menubox_categories a', 'font-weight', gm_prepare_string($_POST['menubox_body_font_weight']));
		update_style('#menubox_categories a', 'font-style', gm_prepare_string($_POST['menubox_body_font_style']));

		update_style('#menubox_%_body', 'background-color', gm_prepare_string($_POST['menubox_body_background_color']));
		if(!empty($body_background_image->filename)) update_style('#menubox_%_body', 'background-image', $body_background_image->filename);
		update_style('#menubox_%_body', 'color', gm_prepare_string($_POST['menubox_body_color']));
		update_style('#menubox_%_body', 'font-family', gm_prepare_string($_POST['menubox_body_font_family']));
		update_style('#menubox_%_body', 'font-size', gm_prepare_string($_POST['menubox_body_font_size']));
		update_style('#menubox_%_body', 'font-weight', gm_prepare_string($_POST['menubox_body_font_weight']));
		update_style('#menubox_%_body', 'font-style', gm_prepare_string($_POST['menubox_body_font_style']));
		update_style('#menubox_%_body a', 'font-family', gm_prepare_string($_POST['menubox_body_font_family']));
		update_style('#menubox_%_body a', 'font-size', gm_prepare_string($_POST['menubox_body_font_size']));
		update_style('#menubox_%_body a', 'font-weight', gm_prepare_string($_POST['menubox_body_font_weight']));
		update_style('#menubox_%_body a', 'font-style', gm_prepare_string($_POST['menubox_body_font_style']));
		update_style('#menubox_%_body a', 'color', gm_prepare_string($_POST['menubox_body_color']));
		update_style('#menubox_%_body', 'text-decoration', gm_prepare_string($_POST['menubox_body_text_decoration']));

		update_style('.cat_link span', 'color', gm_prepare_string($_POST['menubox_body_color']));
	}
	elseif(isset($_POST['send_font'])){

		update_style('%', 'font-family', gm_prepare_string($_POST['font_family']));

	}
	elseif(isset($_POST['send_forms'])){

		update_style('.form_style_%', 'background-color', gm_prepare_string($_POST['form_style_background_color']));
		update_style('.form_style_%', 'color', gm_prepare_string($_POST['form_style_color']));
		update_style('.form_style_%', 'border-color', gm_prepare_string($_POST['form_style_border_color']));
		update_style('.form_style_%', 'border-style', gm_prepare_string($_POST['form_style_border_style']));
		update_style('.form_style_%', 'border-width', gm_prepare_string($_POST['form_style_border_width']));

	}
	elseif(isset($_POST['send_mode1_block'])){
		update_style('#mode1_search_cell', 'background-color', gm_prepare_string($_POST['mode1_block']));
		update_style('#mode1_pathrow', 'background-color', gm_prepare_string($_POST['mode1_block']));
        update_style('#mode1_block', 'background-color', gm_prepare_string($_POST['mode1_block']));
    }
	elseif(isset($_POST['go_save'])){
		if(isset($_POST['gm_topmenu_mode']) && $_POST['gm_topmenu_mode'] == 'mode1'){
			gm_set_conf('GM_TOPMENU_MODE', 'mode1');
		}
		else{
			gm_set_conf('GM_TOPMENU_MODE', 'mode2');
		}

		if(isset($_POST['gm_cart_on_top']) && $_POST['gm_cart_on_top'] == 'true'){
			gm_set_conf('GM_CART_ON_TOP', 'true');
		}
		else{
			gm_set_conf('GM_CART_ON_TOP', 'false');
		}
		
		if(isset($_POST['gm_show_wishlist']) && $_POST['gm_show_wishlist'] == 'true'){
			gm_set_conf('GM_SHOW_WISHLIST', 'true');
		}
		else{
			gm_set_conf('GM_SHOW_WISHLIST', 'false');
		}

      if(isset($_POST['gm_quick_search']) && $_POST['gm_quick_search'] == 'true'){
			gm_set_conf('GM_QUICK_SEARCH', 'true');
		}
		else{
			gm_set_conf('GM_QUICK_SEARCH', 'false');
		}

		if(isset($_POST['gm_gambio_corner']) && $_POST['gm_gambio_corner'] == '1')
		{
			gm_set_conf('GM_GAMBIO_CORNER', '1');
			@unlink(DIR_FS_CATALOG . DIR_WS_IMAGES . 'logos/gm_corner.gif');
			gm_create_corner();
		}
		else
		{
			gm_set_conf('GM_GAMBIO_CORNER', '0');
			@unlink(DIR_FS_CATALOG . DIR_WS_IMAGES . 'logos/gm_corner.gif');
			gm_delete_corner();
		}

		if(isset($_POST['gm_show_flyover']) && $_POST['gm_show_flyover'] == '1'){
			gm_set_conf('GM_SHOW_FLYOVER', '1');
		}
		else{
			gm_set_conf('GM_SHOW_FLYOVER', '0');
		}

		$gm_specials_startpage = trim($_POST['gm_specials_startpage']);
		$gm_new_products_startpage = trim($_POST['gm_new_products_startpage']);

		gm_set_conf('GM_SPECIALS_STARTPAGE', (int)$gm_specials_startpage);
		gm_set_conf('GM_NEW_PRODUCTS_STARTPAGE', (int)$gm_new_products_startpage);
		gm_set_conf('GM_SHOW_CAT', $_POST['GM_SHOW_CAT']);
	}
	elseif(isset($_POST['send_links'])){

		if(isset($_POST['gm_underline_links']) && $_POST['gm_underline_links'] == 'true'){
			gm_set_conf('GM_UNDERLINE_LINKS', 'true');
		}
		else{
			gm_set_conf('GM_UNDERLINE_LINKS', 'false');
		}

	}
	elseif(isset($_POST['send_hover']))
	{

		gm_set_conf('GM_STARTPAGE_HOVER', $_POST['GM_STARTPAGE_HOVER']);
		gm_set_conf('GM_CAT_HOVER', $_POST['GM_CAT_HOVER']);

	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
	<meta http-equiv="x-ua-compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" href="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/plugins/farbtastic/farbtastic.css" type="text/css" />
<style type="text/css">
#gm_style_edit_form input{
	width: 220px;
}
#gm_style_edit_form select {
	width: 220px;
}

#gm_style_edit_form .button {
	width: 120px;
}
</style>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF"><div id="gm_color_box" style="display:none">
	<div id="colorpicker"></div>
	<div align="center">
		<input type="text" id="color" name="color" value="#123456" />
		<input type="hidden" id="actual" value="" /><br /><br />
		<input type="button" class="save button" style="cursor:pointer;width:90px;float:left" value="<?php echo BUTTON_SAVE; ?>">
		<input type="button" class="close button" style="cursor:pointer;width:90px;float:right" value="<?php echo BUTTON_CLOSE; ?>">
	</div></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
			<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
			<!-- left_navigation //-->
			<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
			<!-- left_navigation_eof //-->
    	</table>
		</td>
		<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top">

	<div class="pageHeading" style="background-image:url(images/gm_icons/gambio.png)"><?php echo HEADING_TITLE; ?></div>
	<br />
	<?php
	/* BOF StyleEdit */
	if(is_dir(DIR_FS_CATALOG.'StyleEdit/') !== false)
	{
		if(is_object($GLOBALS['coo_debugger']) == true && $GLOBALS['coo_debugger']->is_enabled('hide_styleedit') == true)
		{
			echo '<font face="arial" size=2>StyleEdit ist zurzeit nicht aktiv.</font>';
		}
		else
		{
			echo '<div class="gx-container">';
			echo '<a style="width:240px;margin-right: 10px;" href="'.xtc_href_link('../index.php', 'style_edit_mode=edit').'" class="btn btn-primary">'.GM_LOAD_EDIT_MODE.'</a>&nbsp;';
			echo '<a style="width:300px;" href="'.xtc_href_link('../index.php', 'style_edit_mode=sos').'" class="btn">'.GM_LOAD_SOS_MODE.'</a><br style="clear:left" /><br />';
			echo '</div>';
		}
	}
	else
	{
		if(file_exists(DIR_WS_INCLUDES.'basic.php') == false)
		{
			echo '<span style="font-size: 24px; color: #fe0000; font-weight: bold; font-family: Verdana, Arial, Tahoma, sans-serif;">' . GM_NO_STYLE_EDIT_INSTALLED_TEXT . '</span>';
		} 
		else {
			include(DIR_WS_INCLUDES.'basic.php');
		}
	}
	/* EOF StyleEdit */
	if(gm_get_env_info('TEMPLATE_VERSION') < FIRST_GX2_TEMPLATE_VERSION)
	{
	?>


	<span class="main">
		<table style="margin-bottom:5px" border="0" cellpadding="0" cellspacing="0" width="100%">
		 <tr class="dataTableHeadingRow">
		 	<td class="dataTableHeadingContent" style="width:1%;padding-right:20px; white-space: nowrap"><a href="<?php echo xtc_href_link('gm_style_edit.php'); ?>"><?php echo HEADING_TITLE; ?></a></td>
		 	<td class="dataTableHeadingContent" style="border-right: 0px"><a href="gm_style_edit.php?content=front_style"><?php echo GM_FRONT_STYLE; ?></a></td>
		 </tr>
		</table>

		<table style="border: 1px solid #dddddd" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr class="dataTableRow">
				<td style="font-size: 12px; padding: 10px 10px 11px 10px; text-align: justify">

					<?php if(empty($_GET['content']) || $_GET['content'] == 'miscellaneous'){ ?>

					<form id="gm_style_edit" name="gm_style_edit" action="<?php echo xtc_href_link('gm_style_edit.php'); ?>" method="post">
						<table border="0" width="100%" cellspacing="2" cellpadding="0" >
							<tr>
								<td width="80" class="main" valign="top" align="left">
									<?php if(gm_get_conf('GM_CART_ON_TOP') == 'true') $gm_checked = 'checked="checked"'; else $gm_checked = '';	?>
									<input type="checkbox" name="gm_cart_on_top" value="true"<?php echo $gm_checked; ?> />
								</td>
								<td class="main">
									<?php echo GM_CART_ON_TOP; ?>
								</td>
							</tr>

							<tr>
								<td width="80" class="main" valign="top" align="left">
									<?php if(gm_get_conf('GM_SHOW_WISHLIST') == 'true') $gm_checked = 'checked="checked"'; else $gm_checked = '';	?>
									<input type="checkbox" name="gm_show_wishlist" value="true"<?php echo $gm_checked; ?> />
								</td>
								<td class="main">
									<?php echo GM_SHOW_WISHLIST; ?>
								</td>
							</tr>
							
							<tr>
								<td width="80" class="main" valign="top" align="left">
									<?php if(gm_get_conf('GM_TOPMENU_MODE') == 'mode1') $gm_checked = 'checked="checked"'; else $gm_checked = '';	?>
									<input type="checkbox" name="gm_topmenu_mode" value="mode1"<?php echo $gm_checked; ?> />
								</td>
								<td class="main">
									<?php echo GM_TOPMENU_MODE; ?>
								</td>
							</tr>

                            	<tr>
								<td width="80" class="main" valign="top" align="left">
									<?php if(gm_get_conf('GM_QUICK_SEARCH') == 'true') $gm_checked = 'checked="checked"'; else $gm_checked = '';	?>
									<input type="checkbox" name="gm_quick_search" value="true"<?php echo $gm_checked; ?> />
								</td>
								<td class="main">
									<?php echo GM_SHOW_QUICK_SEARCH; ?>
								</td>
							</tr>

							<tr>
								<td width="80" class="main" valign="top" align="left">
									<?php if(gm_get_conf('GM_GAMBIO_CORNER') == '1') $gm_checked = 'checked="checked"'; else $gm_checked = '';	?>
									<input type="checkbox" name="gm_gambio_corner" value="1"<?php echo $gm_checked; ?> />
								</td>
								<td class="main">
									<?php echo GM_GAMBIO_CORNER; ?>
								</td>
							</tr>

							<tr>
								<td width="80" class="main" valign="top" align="left">
									<?php if(gm_get_conf('GM_SHOW_FLYOVER') == '1') $gm_checked = 'checked="checked"'; else $gm_checked = '';	?>
									<input type="checkbox" name="gm_show_flyover" value="1"<?php echo $gm_checked; ?> />
								</td>
								<td class="main">
									<?php echo GM_SHOW_FLYOVER; ?>
								</td>
							</tr>

							<tr>
								<td width="80" class="main" valign="top" align="left">
									<input style="width:70px" type="text" name="gm_specials_startpage" value="<?php echo gm_get_conf('GM_SPECIALS_STARTPAGE'); ?>" size="3" />
								</td>
								<td class="main">
									<?php echo GM_SPECIALS_STARTPAGE; ?>
								</td>
							</tr>

							<tr>
								<td width="80" class="main" valign="top" align="left">
									<input style="width:70px" type="text" name="gm_new_products_startpage" value="<?php echo gm_get_conf('GM_NEW_PRODUCTS_STARTPAGE'); ?>" size="3" />
								</td>
								<td class="main">
									<?php echo GM_NEW_PRODUCTS_STARTPAGE; ?>
								</td>
							</tr>

							<tr>
								<td width="80" class="main" valign="top" align="left">
									<select name="GM_SHOW_CAT" style="width:70px">
									<?php
										if(gm_get_conf('GM_SHOW_CAT') == 'none')
										{
											$t_none = 'selected';
										}
									?>
										<option <?php echo $t_none; ?> value="none">
											<?php echo GM_SHOW_CAT_NONE; ?>
										</option>

									<?php
										if(gm_get_conf('GM_SHOW_CAT') == 'child')
										{
											$t_child = 'selected';
										}
									?>
										<option <?php echo $t_child; ?> value="child">
											<?php echo GM_SHOW_CAT_CHILD; ?>
										</option>

									<?php
										if(gm_get_conf('GM_SHOW_CAT') == 'all')
										{
											$t_all = 'selected';
										}
									?>
										<option <?php echo $t_all; ?> value="all">
											<?php echo GM_SHOW_CAT_ALL; ?>
										</option>
									</select>
								</td>
								<td class="main">
									<?php echo GM_SHOW_CAT; ?>
								</td>
							</tr>

							<tr>
								<td width="80" class="main" valign="top" align="left">
									&nbsp;
								</td>
								<td class="main">
									&nbsp;
								</td>
							</tr>

							<tr>
								<td colspan="2" class="main">
									<input type="submit" class="button" name="go_save" value="<?php echo GM_FORM_SUBMIT; ?>" />
								</td>
							</tr>
						</table>
					</form>

					<?php } elseif($_GET['content'] == 'front_style'){ ?>

					<form id="gm_style_edit_form" name="gm_style_edit_form" action="<?php xtc_href_link('gm_style_edit.php', 'content=front_style'); ?>" enctype="multipart/form-data" method="post">

						<table>
							<tr>
								<td class="main" colspan="2" height="30"><strong><?php echo GM_SHOP_ALIGN; ?></strong></td>
							</tr>
							<?php
								$shop_width = '';
								$margin_left = '';
								$margin_right = '';

								$gm_get_width = xtc_db_query("SELECT gm_css_style_id FROM gm_css_style WHERE style_name = '.wrap_shop' LIMIT 1");
								if(xtc_db_num_rows($gm_get_width) == 1){
									$row = xtc_db_fetch_array($gm_get_width);
									$gm_get_width2 = xtc_db_query("SELECT
																									style_value
																								FROM gm_css_style_content
																								WHERE
																									gm_css_style_id = '" . $row['gm_css_style_id'] . "'
																									AND style_attribute = 'width'");
									if(xtc_db_num_rows($gm_get_width2) == 1){
										$row2 = xtc_db_fetch_array($gm_get_width2);
										$shop_width = $row2['style_value'];
									}

									$gm_get_margin_left = xtc_db_query("SELECT
																												style_value
																											FROM gm_css_style_content
																											WHERE
																												gm_css_style_id = '" . $row['gm_css_style_id'] . "'
																												AND style_attribute = 'margin-left'");
									if(xtc_db_num_rows($gm_get_margin_left) == 1){
										$row3 = xtc_db_fetch_array($gm_get_margin_left);
										$margin_left = $row3['style_value'];
									}

									$gm_get_margin_right = xtc_db_query("SELECT
																												style_value
																											FROM gm_css_style_content
																											WHERE
																												gm_css_style_id = '" . $row['gm_css_style_id'] . "'
																												AND style_attribute = 'margin-right'");
									if(xtc_db_num_rows($gm_get_margin_right) == 1){
										$row3 = xtc_db_fetch_array($gm_get_margin_right);
										$margin_right = $row3['style_value'];
									}


									if($shop_width == 'auto'){
										$justify_checked = ' checked="checked"';
										$left_checked = '';
										$right_checked = '';
										$center_checked = '';
									}
									elseif($margin_left == 'auto' && $margin_right == 'auto'){
										$justify_checked = '';
										$left_checked = '';
										$right_checked = '';
										$center_checked = ' checked="checked"';
									}
									elseif($margin_left != 'auto' && $margin_right == 'auto'){
										$justify_checked = '';
										$left_checked = ' checked="checked"';
										$right_checked = '';
										$center_checked = '';
									}
									elseif($margin_left == 'auto' && $margin_right != 'auto'){
										$justify_checked = '';
										$left_checked = '';
										$right_checked = ' checked="checked"';
										$center_checked = '';
									}
								}
							?>
							<tr>
								<td width="230" class="main"><?php echo GM_SHOP_WIDTH; ?></td>
								<td class="main"><input type="text" name="shop_width" value="<?php echo $shop_width; ?>" /> Standard: 978px</td>
							</tr>
							<tr>
								<td class="main" valign="top"><?php echo GM_SHOP_ALIGN_TEXT; ?></td>
								<td class="main">
									<input style="width:auto" type="radio" name="shop_align" value="left"<?php echo $left_checked; ?> /> <?php echo GM_SHOP_ALIGN_LEFT; ?><br />
									<input style="width:auto" type="radio" name="shop_align" value="center"<?php echo $center_checked; ?> /> <?php echo GM_SHOP_ALIGN_CENTER; ?><br />
									<input style="width:auto" type="radio" name="shop_align" value="right"<?php echo $right_checked; ?> /> <?php echo GM_SHOP_ALIGN_RIGHT; ?><br />
									<input style="width:auto" type="radio" name="shop_align" value="justify"<?php echo $justify_checked; ?> /> <?php echo GM_SHOP_ALIGN_JUSTIFY; ?><br />
									<input type="hidden" name="wrap_shop_id" value="<?php echo $row['gm_css_style_id']; ?>" />
								</td>
							</tr>
							<tr height="50">
								<td class="main" valign="top" colspan="2"><input class="button" type="submit" name="send_shop_align" value="<?php echo GM_FORM_SUBMIT; ?>" /></td>
							</tr>
							<tr>
								<td class="main" colspan="2" height="30"><strong><?php echo GM_RESTORE_CORNER; ?></strong></td>
							</tr>
							<tr>
								<td colspan="2" width="230" class="main"><?php echo GM_RESTORE_CORNER_TEXT; ?></td>
							</tr>
							<tr height="50">
								<td class="main" valign="top" colspan="2"><input class="button" type="submit" name="send_restore_corner" value="<?php echo GM_FORM_SUBMIT; ?>" /></td>
							</tr>
							<tr>
								<td class="main" colspan="2" height="30"><strong><?php echo GM_TOPMENU_BACKGROUND_COLOR; ?></strong></td>
							</tr>
							<tr>
								<td width="230" class="main"><?php echo GM_TOPMENU_BACKGROUND_COLOR_TEXT; ?></td>
								<td class="main"><?php echo get_style('#topmenu_block', 'background-color', 'topmenu_block_background_color'); ?></td>
							</td>
							</tr>
							<tr height="50">
								<td class="main" valign="top" colspan="2"><input class="button" type="submit" name="send_topmenu" value="<?php echo GM_FORM_SUBMIT; ?>" /></td>
							</tr>
							<tr>
								<td class="main" colspan="2" height="30"><strong><?php echo GM_MENUBOXES_TITLE; ?></strong></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_MENUBOX_HEAD_BACKGROUND_COLOR; ?></td>
								<td class="main"><?php echo get_style('#menubox_admin_head', 'background-color', 'menubox_head_background_color'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_MENUBOX_HEAD_BACKGROUND_IMAGE; ?></td>
								<td class="main"><?php echo get_style('#menubox_admin_head', 'background-image', 'menubox_head_background_image'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_MENUBOX_HEAD_COLOR; ?></td>
								<td class="main"><?php echo get_style('#menubox_admin_head', 'color', 'menubox_head_color'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_MENUBOX_HEAD_FONT_FAMILY; ?></td>
								<td class="main"><?php echo get_style('#menubox_admin_head', 'font-family', 'menubox_head_font_family'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_MENUBOX_HEAD_FONT_SIZE; ?></td>
								<td class="main"><?php echo get_style('#menubox_admin_head', 'font-size', 'menubox_head_font_size'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_MENUBOX_HEAD_FONT_WEIGHT; ?></td>
								<td class="main"><?php echo get_style('#menubox_admin_head', 'font-weight', 'menubox_head_font_weight'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_MENUBOX_HEAD_FONT_STYLE; ?></td>
								<td class="main"><?php echo get_style('#menubox_admin_head', 'font-style', 'menubox_head_font_style'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_MENUBOX_HEAD_HEIGHT; ?></td>
								<td class="main"><?php echo get_style('#menubox_admin_head', 'height', 'menubox_head_height'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_MENUBOX_HEAD_TEXT_DECORATION; ?></td>
								<td class="main"><?php echo get_style('#menubox_admin_head', 'text-decoration', 'menubox_head_text_decoration'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_MENUBOX_BODY_BACKGROUND_COLOR; ?></td>
								<td class="main"><?php echo get_style('#menubox_admin_body', 'background-color', 'menubox_body_background_color'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_MENUBOX_BODY_BACKGROUND_IMAGE; ?></td>
								<td class="main"><?php echo get_style('#menubox_admin_body', 'background-image', 'menubox_body_background_image'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_MENUBOX_BODY_COLOR; ?></td>
								<td class="main"><?php echo get_style('#menubox_admin_body', 'color', 'menubox_body_color'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_MENUBOX_BODY_FONT_FAMILY; ?></td>
								<td class="main"><?php echo get_style('#menubox_admin_body', 'font-family', 'menubox_body_font_family'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_MENUBOX_BODY_FONT_SIZE; ?></td>
								<td class="main"><?php echo get_style('#menubox_admin_body', 'font-size', 'menubox_body_font_size'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_MENUBOX_BODY_FONT_WEIGHT; ?></td>
								<td class="main"><?php echo get_style('#menubox_admin_body', 'font-weight', 'menubox_body_font_weight'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_MENUBOX_BODY_FONT_STYLE; ?></td>
								<td class="main"><?php echo get_style('#menubox_admin_body', 'font-style', 'menubox_body_font_style'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_MENUBOX_BODY_TEXT_DECORATION; ?></td>
								<td class="main"><?php echo get_style('#menubox_admin_body', 'text-decoration', 'menubox_body_text_decoration'); ?></td>
							</tr>
							<tr height="50">
								<td class="main" valign="top" colspan="2"><input class="button" type="submit" name="send_menuboxes" value="<?php echo GM_FORM_SUBMIT; ?>" /></td>
							</tr>
							<tr>
								<td class="main" colspan="2" height="30"><strong><?php echo GM_GLOBAL_FONT_TITLE; ?></strong></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_FONT_FAMILY; ?></td>
								<td class="main">
									<?php echo get_style('#menubox_admin_body', 'font-family', 'font_family'); ?>
								</td>
							</tr>
							<tr>
								<td class="main" colspan="2"><font size="1"><?php echo GM_FONT_FAMILY_TEXT; ?></font></td>
							</tr>
							<tr height="50">
								<td class="main" valign="top" colspan="2"><input class="button" type="submit" name="send_font" value="<?php echo GM_FORM_SUBMIT; ?>" /></td>
							</tr>
							<tr>
								<td class="main" colspan="2" height="30"><strong><?php echo GM_FORMS_TITLE; ?></strong></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_FORM_BACKGROUND_COLOR; ?></td>
								<td class="main"><?php echo get_style('.form_style_guestbook', 'background-color', 'form_style_background_color'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_FORM_COLOR; ?></td>
								<td class="main"><?php echo get_style('.form_style_guestbook', 'color', 'form_style_color'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_FORM_BORDER_COLOR; ?></td>
								<td class="main"><?php echo get_style('.form_style_guestbook', 'border-color', 'form_style_border_color'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_FORM_BORDER_STYLE; ?></td>
								<td class="main"><?php echo get_style('.form_style_guestbook', 'border-style', 'form_style_border_style'); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_FORM_BORDER_WIDTH; ?></td>
								<td class="main"><?php echo get_style('.form_style_guestbook', 'border-width', 'form_style_border_width'); ?></td>
							</tr>
							<tr height="50">
								<td class="main" valign="top" colspan="2"><input class="button" type="submit" name="send_forms" value="<?php echo GM_FORM_SUBMIT; ?>" /></td>
							</tr>
							<tr>
								<td class="main" colspan="2" height="30"><strong><?php echo GM_LINKS_TITLE; ?></strong></td>
							</tr>
							<?php
							if(gm_get_conf('GM_UNDERLINE_LINKS') == 'true') $gm_checked = ' checked="checked"';
							else $gm_checked = '';
							?>
							<tr>
								<td class="main" colspan="2"><?php echo GM_UNDERLINE_LINKS_TEXT; ?><input style="width:auto" type="checkbox" name="gm_underline_links" value="true"<?php echo $gm_checked; ?> /> </td>
							</tr>
							<tr height="50">
								<td class="main" valign="top" colspan="2"><input class="button" type="submit" name="send_links" value="<?php echo GM_FORM_SUBMIT; ?>" /></td>
							</tr>
							<tr>
								<td class="main" colspan="2" height="30"><strong><?php echo GM_PATH_BAR_TITLE; ?></strong></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_PATH_BAR_BACKGROUND_COLOR; ?></td>
								<td class="main"><?php echo get_style('#mode1_pathrow', 'background-color', 'mode1_block'); ?></td>
							</tr>
							<tr height="50">
								<td class="main" valign="top" colspan="2"><input class="button" type="submit" name="send_mode1_block" value="<?php echo GM_FORM_SUBMIT; ?>" /></td>
							</tr>
							<tr>
								<td class="main" colspan="2" height="30"><strong><?php echo GM_HOVER_TITLE; ?></strong></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_STARTPAGE_HOVER_BACKGROUND_COLOR; ?></td>
								<td class="main"><?php echo get_style('GM_STARTPAGE_HOVER', 'background-color', 'GM_STARTPAGE_HOVER', true); ?></td>
							</tr>
							<tr>
								<td class="main"><?php echo GM_CAT_HOVER_BACKGROUND_COLOR; ?></td>
								<td class="main"><?php echo get_style('GM_CAT_HOVER', 'background-color', 'GM_CAT_HOVER', true); ?></td>
							</tr>
							<tr height="50">
								<td class="main" valign="top" colspan="2"><input class="button" type="submit" name="send_hover" value="<?php echo GM_FORM_SUBMIT; ?>" /></td>
							</tr>
						</table>
					</form>

					<?php } ?>

				</td>
			</tr>
		</table>
	</span>
	<?php
	}
	?>
    </td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br />

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>