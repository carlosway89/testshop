<?php
/* --------------------------------------------------------------
   product_images.inc.php 2015-11-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

// Requirements
require_once (DIR_FS_INC.'xtc_get_products_mo_images.inc.php');
$GMAltText  = new GMAltText();

// Primary image
$hasProductImage    = !empty($pInfo->products_image);
$primaryImageTexts  = $GMAltText->getPrimaryImageAltText($pInfo->products_id);

// Mo pics
$moPics = xtc_get_products_mo_images($pInfo->products_id, true);
$hasMoPics = is_array($moPics) && count($moPics) > 0 ? true : false;
$lastMoPicId = $hasMoPics ? end($moPics) : array('image_nr' => 0);
$lastMoPicId = $lastMoPicId['image_nr'];

// G-Motion
if($_GET['action'] == 'new_product')
{
	$t_gm_gmotion_data_array = $coo_gm_gmotion->get_form_data();
}
?>

<!-- Template: Image row - Mo pics (front end) -->
<script type="text/template" id="images-row-template">
    <div class="product-image-wrapper new-product-image" data-id="{{id}}">
        <div class="product-preview-image">
            <img class="preview-image" style="max-width: 150px; max-height: 150px;" src="">
        </div>
        <div class="product-image-data">
            <div class="grid control-group input-row">
                <div class="span6">
                    <label><?php echo TXT_NEW_IMAGE; ?></label>
                </div>
                <div class="span6">
                    <div style="width: 50%;">
                        <label for="mo_pics_{{id}}" class="btn cursor-pointer">
                            <i class="fa fa-fw fa-plus"></i>
                            <?php echo TXT_PIC_ADD; ?>
                        </label>
                        <input style="display:none;" type="file" id="mo_pics_{{id}}" name="mo_pics_{{id}}" accept="image/gif,image/png,image/x-png,image/jpg,image/jpeg,image/gif,image/pjpeg">
                    </div>
                </div>
            </div>
            <!-- Image Filename -->
            <div class="grid control-group">
                <div class="span6">
                    <label><?php echo TEXT_CATEGORIES_FILE_LABEL; ?></label>
                </div>
                <div class="span4">
                    <input type="text" name="gm_prd_img_name_{{id}}" value="">
                </div>
                <div class="span2 text-center">
                    &nbsp;
                </div>
            </div>
            <!-- Loop: Alt text for each language -->
            <?php foreach($languagesArray as $language): ?>
                <div class="grid control-group">
                    <div class="span6">
                        <label><?php echo GM_PRODUCTS_ALT_TEXT; ?></label>
                    </div>
                    <div class="span4">
                        <input type="hidden" data-language-id="<?php echo $language['id']; ?>" name="gm_alt_id[{{idPlusOne}}][<?php echo $language['id']; ?>]">
                        <input type="text" data-language-id="<?php echo $language['id']; ?>" name="gm_alt_text[{{idPlusOne}}][<?php echo $language['id']; ?>]" value="">
                    </div>
                    <div class="span2 text-center">
                        <?php echo xtc_image(DIR_WS_LANGUAGES.$language['directory'].'/admin/images/'.$language['image']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
	        <!-- Use as product image container -->
	        <div class="grid control-group">
		        <div class="span6">
			        <label><?php echo GM_GMOTION_SHOW_IMAGE_TEXT; ?></label>
		        </div>
		        <div class="span6">
			        <div data-gx-widget="checkbox">
				        <input type="checkbox" name="gm_gmotion_product_image_{{idPlusOne}}" value="1" checked>
			        </div>
		        </div>
	        </div>
        </div>
    </div>
</script>


<div class="span12">
    <div
        data-gx-compatibility="categories/products_upload_controller"
        data-products_upload_controller-counter="<?php echo count($moPics) > 0 ? count($moPics) - 1 : -1 ?>"
        data-products_upload_controller-has-mopics="<?php echo $hasMoPics; ?>"
        data-products_upload_controller-last-id="<?php echo $lastMoPicId; ?>"
    >

        <!-- Primary image -->
        <br>
        <div
            class="primary-image"
            data-gx-extension="gmotion"
            data-gx-compatibility="categories/products_image_controller"
            data-products_image_controller-has-primary-image="<?php echo $hasProductImage ? 'true' : 'false'; ?>"
            data-gmotion-is-primary-image="<?php echo $hasProductImage ? 'true' : 'false'; ?>"
            data-gmotion-position-from="<?php echo $t_gm_gmotion_data_array['POSITION_FROM']; ?>"
            data-gmotion-position-to="<?php echo $t_gm_gmotion_data_array['POSITION_TO']; ?>"
            data-gmotion-zoom-from="<?php echo $t_gm_gmotion_data_array['ZOOM_FROM']; ?>"
            data-gmotion-zoom-to="<?php echo $t_gm_gmotion_data_array['ZOOM_TO']; ?>"
            data-gmotion-duration="<?php echo $t_gm_gmotion_data_array['DURATION']; ?>"
            data-gmotion-sort="<?php echo $t_gm_gmotion_data_array['SORT_ORDER']; ?>"
        >
            <div class="product-image-wrapper">
                <div class="product-preview-image">
                    <img class="preview-image" style="max-width: 150px; max-height: 150px;" src="<?php echo $hasProductImage ? DIR_WS_CATALOG_THUMBNAIL_IMAGES.$pInfo->products_image : ''; ?>">
                </div>
                <div class="product-image-data">
                    <div class="grid control-group js-toggle-visibility">
                        <div class="span6">
                            <label class="bold"><?php echo TEXT_PRODUCTS_IMAGE; ?></label>
                        </div>
                        <div class="span4">
                            <label class="bold file-name"><?php echo $hasProductImage ? $pInfo->products_image : ''; ?></label>
                        </div>
                        <div class="span2 delete-image text-center" data-gx-widget="checkbox">
                            <div class="js-delete-checkbox">
                                <input class="data-gx-widget" type="checkbox" name="del_pic"
                                       value="<?php echo $hasProductImage ? $pInfo->products_image : ''; ?>" data-single_checkbox>
                                <?php echo TEXT_DELETE; ?>
	                            <?php echo xtc_draw_hidden_field('products_previous_image_0', $pInfo->products_image); ?>
                            </div>
                        </div>
                    </div>
                    <div class="grid control-group">
                        <div class="span6">
                            <label><?php echo TXT_NEW_IMAGE; ?></label>
                        </div>
                        <div class="span6">
                            <div style="width: 50%;">
                                <label for="product-main-image" class="btn cursor-pointer">
                                    <i class="fa fa-fw fa-plus"></i>
                                    <?php echo TXT_PIC_ADD; ?>
                                </label>
                                <input id="product-main-image" style="display:none;" type="file" name="products_image" accept="image/gif,image/png,image/x-png,image/jpg,image/jpeg,image/gif,image/pjpeg">
                            </div>
                        </div>
                    </div>
                    <!-- Image Filename -->
                    <div class="grid control-group">
                        <div class="span6">
                            <label><?php echo TEXT_CATEGORIES_FILE_LABEL; ?></label>
                        </div>
                        <div class="span4">
                            <input type="text" name="gm_prd_img_name" value="">
                        </div>
                        <div class="span2 text-center">
                            &nbsp;
                        </div>
                    </div>
                    <!-- Loop: Alt text for each language -->
                    <?php foreach($languagesArray as $language): ?>
                        <div class="grid control-group">
                            <div class="span6">
                                <label><?php echo GM_PRODUCTS_ALT_TEXT; ?></label>
                            </div>
                            <div class="span4">
                                <input type="hidden" name="gm_alt_id[0][<?php echo $language['id']; ?>]">
                                <input type="text" name="gm_alt_text[0][<?php echo $language['id']; ?>]" value="<?php echo $primaryImageTexts[$language['id']]; ?>">
                            </div>
                            <div class="span2 text-center">
                                <?php echo xtc_image(DIR_WS_LANGUAGES.$language['directory'].'/admin/images/'.$language['image']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <!-- Use as product image container -->
                    <div class="grid control-group">
                        <div class="span6">
                            <label><?php echo GM_GMOTION_SHOW_IMAGE_TEXT; ?></label>
                        </div>
                        <div class="span6">
                            <div data-gx-widget="checkbox">
                                <input type="checkbox" name="gm_gmotion_product_image_0" value="1" <?php echo $pInfo->gm_show_image == '1' ? 'checked=""' : ''; ?>>
                            </div>
                        </div>
                    </div>
                    <!-- Use G-Motion checkbox container -->
                    <div class="grid control-group gmotion-setting hidden">
                        <div class="span6">
                            <label><?php echo GM_GMOTION_IMAGE_TEXT; ?></label>
                        </div>
                        <div class="span6">
                            <div data-gx-widget="checkbox">
                                <input type="checkbox" name="gm_gmotion_image_0" id="gm_gmotion_image_0" class="gm_gmotion_image" value="1" <?php echo (boolean) strlen($t_gm_gmotion_data_array['IMAGE']) ? 'checked=""' : ''; ?>>
                            </div>
                        </div>
                    </div>
                    <!-- G-Motion Controls container -->
                    <div class="js-gmotion-panel gmotion-setting hidden">
                        <!-- Image, swing and zoom options -->
                        <div class="grid control-group">
                            <!-- Title and image -->
                            <div class="span6">
                                <div class="add-margin-left-20" style="position: absolute;">
                                    <!-- Picture -->
                                    <img draggable="false" class="js-gmotion-image untouched" style="width: 200px; " src="<?php echo $hasProductImage ? DIR_WS_CATALOG_THUMBNAIL_IMAGES.$pInfo->products_image : ''; ?>">
                                    <!-- Start Dragger -->
                                    <i class="fa fa-circle gmotion-icon gm_gmotion_start" id="gm_gmotion_start_0" style="position: absolute;"></i>
                                    <!-- End Dragger -->
                                    <i class="fa fa-circle gmotion-icon gm_gmotion_end" id="gm_gmotion_end_0" style="position: absolute;"></i>
                                </div>
                            </div>
                            <div class="span6" style="float: right;">
                                <!-- Swing from -->
                                <div class="grid">
                                    <div class="span6">
                                        <label class="no-horizontal-padding"><?php echo GM_GMOTION_POSITION_FROM_TEXT; ?></label>
                                    </div>
                                    <div class="span6">
                                        <input type="text" style="border: 1px solid #6afe6b;" class="gm_gmotion_position_from" name="gm_gmotion_position_from_0" id="gm_gmotion_position_from_0">
                                    </div>
                                </div>
                                <!-- Swing to -->
                                <div class="grid">
                                    <div class="span6">
                                        <label class="no-horizontal-padding"><?php echo GM_GMOTION_POSITION_TO_TEXT; ?></label>
                                    </div>
                                    <div class="span6">
                                        <input type="text" style="border: 1px solid red;" class="gm_gmotion_position_to" name="gm_gmotion_position_to_0" id="gm_gmotion_position_to_0">
                                    </div>
                                </div>
                                <!-- Swing info text -->
                                <div class="grid">
                                    <div class="span12">
                                        <label class="no-horizontal-padding">
                                            <small>
                                                <?php echo GM_GMOTION_POSITION_INFO_TEXT; ?>
                                            </small>
                                        </label>
                                    </div>
                                </div>
                                <!-- Zoom from -->
                                <div class="grid">
                                    <div class="span6">
                                        <label class="no-horizontal-padding"><?php echo GM_GMOTION_ZOOM_FROM_TEXT; ?></label>
                                    </div>
                                    <div class="span6">
                                        <?php echo xtc_draw_pull_down_menu('gm_gmotion_zoom_from_0', $coo_gm_gmotion->get_zoom_array(0.1, 2.0, 0.1), $t_gm_gmotion_data_array['ZOOM_FROM']); ?>
                                    </div>
                                </div>
                                <!-- Zoom to -->
                                <div class="grid">
                                    <div class="span6">
                                        <label class="no-horizontal-padding"><?php echo GM_GMOTION_ZOOM_TO_TEXT; ?></label>
                                    </div>
                                    <div class="span6">
                                        <?php echo xtc_draw_pull_down_menu('gm_gmotion_zoom_to_0', $coo_gm_gmotion->get_zoom_array(0.1, 2.0, 0.1), $t_gm_gmotion_data_array['ZOOM_TO']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Duration  -->
                        <div class="grid control-group">
                            <div class="span6">
                                <label><?php echo GM_GMOTION_DURATION_TEXT; ?></label>
                            </div>
                            <div class="span6">
                                <?php echo xtc_draw_input_field('gm_gmotion_duration_0', $t_gm_gmotion_data_array['DURATION'], 'style="width: 30px;"'); ?>
                                <span style="margin-left: 10px;"><?php echo GM_GMOTION_DURATION_UNIT_TEXT; ?></span>
                            </div>
                        </div>
                        <!-- Sorting  -->
                        <div class="grid control-group">
                            <div class="span6">
                                <label><?php echo GM_GMOTION_SORT_ORDER_TEXT; ?></label>
                            </div>
                            <div class="span6">
                                <?php echo xtc_draw_input_field('gm_gmotion_sort_order_0', $t_gm_gmotion_data_array['SORT_ORDER'], 'style="width: 30px;"'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Primary image -->

        <!--  Fetched MoPics-->
        <?php if ($hasMoPics) { ?>
            <div class="uploaded-list">
                <?php foreach($moPics as $image): ?>

	                <?php
	                    // Fetch G-Motion values for additional picture.
	                    $t_gm_gmotion_data_array = $coo_gm_gmotion->get_form_data((int) $image['image_nr']);
	                ?>

                    <div
	                    class="product-image-wrapper"
	                    data-gx-compatibility="categories/products_image_controller"
	                    data-gx-extension="gmotion"
	                    data-products_image_controller-has-primary-image="false"
	                    data-gmotion-is-primary-image="false"
	                    data-gmotion-position-from="<?php echo $t_gm_gmotion_data_array['POSITION_FROM']; ?>"
	                    data-gmotion-position-to="<?php echo $t_gm_gmotion_data_array['POSITION_TO']; ?>"
	                    data-gmotion-zoom-from="<?php echo $t_gm_gmotion_data_array['ZOOM_FROM']; ?>"
	                    data-gmotion-zoom-to="<?php echo $t_gm_gmotion_data_array['ZOOM_TO']; ?>"
	                    data-gmotion-duration="<?php echo $t_gm_gmotion_data_array['DURATION']; ?>"
	                    data-gmotion-sort="<?php echo $t_gm_gmotion_data_array['SORT_ORDER']; ?>"
                    >
                        <div class="product-preview-image">
                            <img class="preview-image" style="max-width: 150px; max-height: 150px;" src="<?php echo DIR_WS_CATALOG_THUMBNAIL_IMAGES.$image['image_name']; ?>">
                        </div>
                        <div class="product-image-data">
                            <div class="grid control-group">
                                <div class="span6">
                                    <label class="bold"><?php echo TEXT_PRODUCTS_IMAGE; ?></label>
                                </div>
                                <div class="span4">
                                    <label class="bold file-name"><?php echo $image['image_name']; ?></label>
                                </div>
                                <div class="span2 delete-image text-center" data-gx-widget="checkbox">
                                    <div class="js-delete-checkbox">
	                                    <input class="data-gx-widget" type="checkbox" name="del_mo_pic[]" value="<?php echo $image['image_name']; ?>" data-single_checkbox>
	                                    <?php echo TEXT_DELETE; ?>
                                        <?php echo xtc_draw_hidden_field('products_previous_image_' . $image['image_nr'], $image['image_name']); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="grid control-group">
                                <div class="span6">
                                    <label><?php echo TXT_NEW_IMAGE; ?></label>
                                </div>
                                <div class="span6">
                                    <div style="width: 50%;">
                                        <label for="mo_pics_<?php echo (int) $image['image_nr']; ?>" class="btn cursor-pointer">
                                            <i class="fa fa-fw fa-plus"></i>
                                            <?php echo TXT_PIC_ADD; ?>
                                        </label>
                                        <input style="display:none;" type="file" id="mo_pics_<?php echo (int) $image['image_nr']; ?>" name="mo_pics_<?php echo (int) $image['image_nr']; ?>" accept="image/gif,image/png,image/x-png,image/jpg,image/jpeg,image/gif,image/pjpeg">
                                    </div>
                                </div>
                            </div>
                            <!-- Image Filename -->
                            <div class="grid control-group">
                                <div class="span6">
                                    <label><?php echo TEXT_CATEGORIES_FILE_LABEL; ?></label>
                                </div>
                                <div class="span4">
                                    <input type="text" name="gm_prd_img_name_<?php echo (int) $image['image_nr']; ?>" value="">
                                </div>
                                <div class="span2 text-center">
                                    &nbsp;
                                </div>
                            </div>
                            <!-- Loop: Alt text for each language -->
                            <?php foreach($languagesArray as $language): ?>
                                <div class="grid control-group">
                                    <div class="span6">
                                        <label><?php echo GM_PRODUCTS_ALT_TEXT; ?></label>
                                    </div>
                                    <div class="span4">
                                        <input type="hidden" name="gm_alt_id[<?php echo (int) $image['image_nr']; ?>][<?php echo $language['id']; ?>]">
                                        <input type="text" name="gm_alt_text[<?php echo (int) $image['image_nr']; ?>][<?php echo $language['id']; ?>]" value="<?php $thatMoPic = $GMAltText->getMoPicAltText($pInfo->products_id, $image['image_id']);  echo $thatMoPic[$language['id']]; ?>">
                                    </div>
                                    <div class="span2 text-center">
                                        <?php echo xtc_image(DIR_WS_LANGUAGES.$language['directory'].'/admin/images/'.$language['image']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
	                        <!-- Use as product image container -->
	                        <div class="grid control-group">
		                        <div class="span6">
			                        <label><?php echo GM_GMOTION_SHOW_IMAGE_TEXT; ?></label>
		                        </div>
		                        <div class="span6">
			                        <div data-gx-widget="checkbox">
				                        <input type="checkbox" name="gm_gmotion_product_image_<?php echo (int) $image['image_nr']; ?>" value="1" <?php echo $image['gm_show_image'] == '1' ? 'checked=""' : ''; ?>>
			                        </div>
		                        </div>
	                        </div>
	                        <!-- Use G-Motion checkbox container -->
	                        <div class="grid control-group gmotion-setting hidden">
		                        <div class="span6">
			                        <label><?php echo GM_GMOTION_IMAGE_TEXT; ?></label>
		                        </div>
		                        <div class="span6">
			                        <div data-gx-widget="checkbox">
				                        <input type="checkbox" name="gm_gmotion_image_<?php echo (int) $image['image_nr']; ?>" id="gm_gmotion_image_<?php echo (int) $image['image_nr']; ?>" class="gm_gmotion_image" value="1" <?php echo (boolean) strlen($t_gm_gmotion_data_array['IMAGE']) ? 'checked=""' : ''; ?>>
			                        </div>
		                        </div>
	                        </div>
	                        <!-- G-Motion Controls container -->
	                        <div class="js-gmotion-panel gmotion-setting hidden">
		                        <!-- Image, swing and zoom options -->
		                        <div class="grid control-group">
			                        <!-- Title and image -->
			                        <div class="span6">
				                        <div class="add-margin-left-20" style="position: absolute;">
					                        <!-- Picture -->
					                        <img draggable="false" class="js-gmotion-image untouched" style="width: 200px; " src="<?php echo DIR_WS_CATALOG_THUMBNAIL_IMAGES.$image['image_name']; ?>">
					                        <!-- Start Dragger -->
					                        <i class="fa fa-circle gmotion-icon gm_gmotion_start" id="gm_gmotion_start_<?php echo (int) $image['image_nr']; ?>" style="position: absolute;"></i>
					                        <!-- End Dragger -->
					                        <i class="fa fa-circle gmotion-icon gm_gmotion_end" id="gm_gmotion_end_<?php echo (int) $image['image_nr']; ?>" style="position: absolute;"></i>
				                        </div>
			                        </div>
			                        <div class="span6" style="float: right;">
				                        <!-- Swing from -->
				                        <div class="grid">
					                        <div class="span6">
						                        <label class="no-horizontal-padding"><?php echo GM_GMOTION_POSITION_FROM_TEXT; ?></label>
					                        </div>
					                        <div class="span6">
						                        <input type="text" style="border: 1px solid #6afe6b;" class="gm_gmotion_position_from" name="gm_gmotion_position_from_<?php echo (int) $image['image_nr']; ?>" id="gm_gmotion_position_from_<?php echo (int) $image['image_nr']; ?>">
					                        </div>
				                        </div>
				                        <!-- Swing to -->
				                        <div class="grid">
					                        <div class="span6">
						                        <label class="no-horizontal-padding"><?php echo GM_GMOTION_POSITION_TO_TEXT; ?></label>
					                        </div>
					                        <div class="span6">
						                        <input type="text" style="border: 1px solid red;" class="gm_gmotion_position_to" name="gm_gmotion_position_to_<?php echo (int) $image['image_nr']; ?>" id="gm_gmotion_position_to_<?php echo (int) $image['image_nr']; ?>">
					                        </div>
				                        </div>
				                        <!-- Swing info text -->
				                        <div class="grid">
					                        <div class="span12">
						                        <label class="no-horizontal-padding">
							                        <small>
								                        <?php echo GM_GMOTION_POSITION_INFO_TEXT; ?>
							                        </small>
						                        </label>
					                        </div>
				                        </div>
				                        <!-- Zoom from -->
				                        <div class="grid">
					                        <div class="span6">
						                        <label class="no-horizontal-padding"><?php echo GM_GMOTION_ZOOM_FROM_TEXT; ?></label>
					                        </div>
					                        <div class="span6">
						                        <?php echo xtc_draw_pull_down_menu('gm_gmotion_zoom_from_' . $image['image_nr'], $coo_gm_gmotion->get_zoom_array(0.1, 2.0, 0.1), $t_gm_gmotion_data_array['ZOOM_FROM']); ?>
					                        </div>
				                        </div>
				                        <!-- Zoom to -->
				                        <div class="grid">
					                        <div class="span6">
						                        <label class="no-horizontal-padding"><?php echo GM_GMOTION_ZOOM_TO_TEXT; ?></label>
					                        </div>
					                        <div class="span6">
						                        <?php echo xtc_draw_pull_down_menu('gm_gmotion_zoom_to_' . $image['image_nr'], $coo_gm_gmotion->get_zoom_array(0.1, 2.0, 0.1), $t_gm_gmotion_data_array['ZOOM_TO']); ?>
					                        </div>
				                        </div>
			                        </div>
		                        </div>
		                        <!-- Duration  -->
		                        <div class="grid control-group">
			                        <div class="span6">
				                        <label><?php echo GM_GMOTION_DURATION_TEXT; ?></label>
			                        </div>
			                        <div class="span6">
				                        <?php echo xtc_draw_input_field('gm_gmotion_duration_' . $image['image_nr'], $t_gm_gmotion_data_array['DURATION'], 'style="width: 30px;"'); ?>
				                        <span style="margin-left: 10px;"><?php echo GM_GMOTION_DURATION_UNIT_TEXT; ?></span>
			                        </div>
		                        </div>
		                        <!-- Sorting  -->
		                        <div class="grid control-group">
			                        <div class="span6">
				                        <label><?php echo GM_GMOTION_SORT_ORDER_TEXT; ?></label>
			                        </div>
			                        <div class="span6">
				                        <?php echo xtc_draw_input_field('gm_gmotion_sort_order_' . $image['image_nr'], $t_gm_gmotion_data_array['SORT_ORDER'], 'style="width: 30px;"'); ?>
			                        </div>
		                        </div>
	                        </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php } ?>

        <!-- To upload MoPics -->
        <div class="list"></div>

        <!-- Image uploader -->
        <button type="button" class="btn cursor-pointer product-image-uploader">
            <i class="fa fa-fw fa-cloud-upload"></i>
            <?php echo TXT_MO_PICS_ADD; ?>
        </button>
    </div>
</div>
