<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2015 Shopgate GmbH
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */




include_once DIR_FS_CATALOG . '/shopgate/shopgate_library/shopgate.php';
include_once DIR_FS_CATALOG . 'shopgate/gambiogx/ShopgateTools.php';

define('SHOPGATE_SETTING_VARIATION_TYPE_BOTH', 0);
define('SHOPGATE_SETTING_VARIATION_TYPE_PROPERTY', 1);
define('SHOPGATE_SETTING_VARIATION_TYPE_ATTRIBUTE', 2);
define('SHOPGATE_SETTING_EXPORT_PRICE_ON_REQUEST_WITHOUT_PRICE', 0);
define('SHOPGATE_SETTING_EXPORT_PRICE_ON_REQUEST_WITH_PRICE', 1);
define('SHOPGATE_SETTING_EXPORT_DESCRIPTION', 0);
define('SHOPGATE_SETTING_EXPORT_SHORTDESCRIPTION', 1);
define('SHOPGATE_SETTING_EXPORT_DESCRIPTION_SHORTDESCRIPTION', 2);
define('SHOPGATE_SETTING_EXPORT_SHORTDESCRIPTION_DESCRIPTION', 3);

/**
 * Class ShopgateConfigGambioGx
 */
class ShopgateConfigGambioGx extends ShopgateConfig
{
    
    /**
     * @var
     */
    protected $redirect_languages;
    /**
     * @var
     */
    protected $shipping;
    /**
     * @var
     */
    protected $tax_zone_id;
    /**
     * @var
     */
    protected $variation_type;
    /**
     * @var
     */
    protected $export_price_on_request;
    /**
     * @var
     */
    protected $export_products_content_managed_files;
    /**
     * @var
     */
    protected $order_status_open;
    /**
     * @var
     */
    protected $order_status_shipped;
    /**
     * @var
     */
    protected $order_status_shipping_blocked;
    /**
     * @var
     */
    protected $order_status_canceled;
    /**
     * @var
     */
    protected $reverse_categories_sort_order;
    /**
     * @var
     */
    protected $reverse_items_sort_order;
    /**
     * @var
     */
    protected $export_description_type;
    /**
     * @var
     */
    protected $shopgate_table_version;
    /**
     * @var
     */
    protected $maximum_category_export_depth;
    /**
     * @var
     */
    protected $export_option_as_input_field;
    /**
     * @var string
     */
    protected $export_filters_as_properties;
    /**
     * @var string
     */
    protected $payment_name_mapping;
    /**
     * @var array
     */
    protected $disabled_redirect_category_ids = array();
    
    /**
     *
     */
    public function startup()
    {
        $this->plugin_name                    = 'GambioGX / GambioGX2';
        $this->enable_redirect_keyword_update = 24;
        $this->enable_ping                    = 1;
        $this->enable_add_order               = 1;
        $this->enable_update_order            = 1;
        $this->enable_get_orders              = 1;
        $this->enable_get_customer            = 1;
        $this->enable_get_items               = 1;
        $this->enable_get_items_csv           = 1;
        $this->enable_get_categories_csv      = 1;
        $this->enable_get_categories          = 1;
        $this->enable_get_reviews_csv         = 1;
        $this->enable_get_reviews             = 1;
        $this->enable_get_pages_csv           = 0;
        $this->enable_get_settings            = 1;
        $this->enable_get_log_file            = 1;
        $this->enable_mobile_website          = 1;
        $this->enable_cron                    = 1;
        $this->enable_clear_log_file          = 1;
        $this->enable_clear_cache             = 1;
        $this->enable_register_customer       = 1;
        $this->enable_check_stock             = 1;
        $this->enable_check_cart              = 1;
        $this->shop_is_active                 = 1;
        $this->disabled_category_ids          = array();
        
        $gambioVersion = ShopgateTools::getGambioVersion();
        if ($gambioVersion['main_version'] == 2 && $gambioVersion['sub_version'] >= 1
            || $gambioVersion['main_version'] > 2
        ) {
            // GambioGX default encoding since version 2.1 is UTF-8
            $this->encoding = 'UTF-8';
        } else {
            $this->encoding = 'ISO-8859-15';
        }
        
        // default filenames if no language was selected
        $this->items_csv_filename      = 'items-undefined.csv';
        $this->categories_csv_filename = 'categories-undefined.csv';
        $this->reviews_csv_filename    = 'reviews-undefined.csv';
        $this->pages_csv_filename      = 'pages-undefined.csv';
        
        $this->access_log_filename  = 'access-undefined.log';
        $this->request_log_filename = 'request-undefined.log';
        $this->error_log_filename   = 'error-undefined.log';
        $this->debug_log_filename   = 'debug-undefined.log';
        
        $this->redirect_keyword_cache_filename      = 'redirect_keywords-undefined.txt';
        $this->redirect_skip_keyword_cache_filename = 'skip_redirect_keywords-undefined.txt';
        
        // initialize plugin specific stuff
        $this->redirect_languages                    = array();
        $this->shipping                              = '';
        $this->tax_zone_id                           = 5;
        $this->variation_type                        = SHOPGATE_SETTING_VARIATION_TYPE_BOTH;
        $this->export_price_on_request               = SHOPGATE_SETTING_EXPORT_PRICE_ON_REQUEST_WITHOUT_PRICE;
        $this->export_products_content_managed_files = 0;
        $this->order_status_open                     = 1;
        $this->order_status_shipped                  = 3;
        $this->order_status_shipping_blocked         = 1;
        $this->order_status_canceled                 = 99;
        $this->reverse_categories_sort_order         = false;
        $this->reverse_items_sort_order              = false;
        $this->export_description_type               = SHOPGATE_SETTING_EXPORT_DESCRIPTION;
        $this->shopgate_table_version                = '';
        $this->maximum_category_export_depth         = 50;
        $this->max_attributes                        = 50;
        $this->supported_fields_get_settings         = array(
            "allowed_address_countries",
            "allowed_shipping_countries",
            "customer_groups",
            "tax",
        );
        $this->supported_fields_check_cart           = array(
            "shipping_methods",
            "currency",
            "items",
            "customer"
        );
        $this->export_option_as_input_field          = "";
        $this->export_filters_as_properties          = "";
        $this->payment_name_mapping                  = "";
    }
    
    /**
     * @param array $fieldList
     *
     * @return array
     */
    protected function validateCustom(array $fieldList = array())
    {
        $failedFields = array();
        
        foreach ($fieldList as $field) {
            switch ($field) {
                case 'redirect_languages':
                    // at least one redirect language must be selected
                    if (empty($this->redirect_languages)) {
                        $failedFields[] = $field;
                    }
                    break;
            }
        }
        
        return $failedFields;
    }
    
    /**
     * @return array
     */
    public function getDisabledRedirectCategoryIds()
    {
        return $this->disabled_redirect_category_ids;
    }
    
    /**
     * @param $disabled_redirect_category_ids
     */
    public function setDisabledRedirectCategoryIds($disabled_redirect_category_ids)
    {
        $this->disabled_redirect_category_ids = $disabled_redirect_category_ids;
    }
    
    /**
     * @return mixed
     */
    public function getRedirectLanguages()
    {
        return $this->redirect_languages;
    }
    
    /**
     * @return mixed
     */
    public function getShipping()
    {
        return $this->shipping;
    }
    
    /**
     * @return mixed
     */
    public function getTaxZoneId()
    {
        return $this->tax_zone_id;
    }
    
    /**
     * @return mixed
     */
    public function getVariationType()
    {
        return $this->variation_type;
    }
    
    /**
     * @return mixed
     */
    public function getExportPriceOnRequest()
    {
        return $this->export_price_on_request;
    }
    
    /**
     * @return mixed
     */
    public function getExportProductsContentManagedFiles()
    {
        return $this->export_products_content_managed_files;
    }
    
    /**
     * @return mixed
     */
    public function getOrderStatusOpen()
    {
        return $this->order_status_open;
    }
    
    /**
     * @return mixed
     */
    public function getOrderStatusShipped()
    {
        return $this->order_status_shipped;
    }
    
    /**
     * @return mixed
     */
    public function getOrderStatusShippingBlocked()
    {
        return $this->order_status_shipping_blocked;
    }
    
    /**
     * @return mixed
     */
    public function getOrderStatusCanceled()
    {
        return $this->order_status_canceled;
    }
    
    /**
     * @return mixed
     */
    public function getReverseCategoriesSortOrder()
    {
        return $this->reverse_categories_sort_order;
    }
    
    /**
     * @return mixed
     */
    public function getReverseItemsSortOrder()
    {
        return $this->reverse_items_sort_order;
    }
    
    /**
     * @return mixed
     */
    public function getExportDescriptionType()
    {
        return $this->export_description_type;
    }
    
    /**
     * @return mixed
     */
    public function getShopgateTableVersion()
    {
        return $this->shopgate_table_version;
    }
    
    /**
     * @return mixed
     */
    public function getMaximumCategoryExportDepth()
    {
        return $this->maximum_category_export_depth;
    }
    
    /**
     * @return mixed
     */
    public function getExportOptionAsInputField()
    {
        return $this->export_option_as_input_field;
    }

    /**
     * @return string
     */
    public function getExportFiltersAsProperties()
    {
        return $this->export_filters_as_properties;
    }

    /**
     * @return string
     */
    public function getPaymentNameMapping()
    {
        return $this->payment_name_mapping;
    }
    
    /**
     * @param $value
     */
    public function setRedirectLanguages($value)
    {
        $this->redirect_languages = $value;
    }
    
    /**
     * @param $value
     */
    public function setShipping($value)
    {
        $this->shipping = $value;
    }
    
    /**
     * @param $value
     */
    public function setTaxZoneId($value)
    {
        $this->tax_zone_id = $value;
    }
    
    /**
     * @param $value
     */
    public function setVariationType($value)
    {
        $this->variation_type = $value;
    }
    
    /**
     * @param $value
     */
    public function setExportPriceOnRequest($value)
    {
        $this->export_price_on_request = $value;
    }
    
    /**
     * @param $value
     */
    public function setExportProductsContentManagedFiles($value)
    {
        $this->export_products_content_managed_files = $value;
    }
    
    /**
     * @param $value
     */
    public function setOrderStatusOpen($value)
    {
        $this->order_status_open = $value;
    }
    
    /**
     * @param $value
     */
    public function setOrderStatusShipped($value)
    {
        $this->order_status_shipped = $value;
    }
    
    /**
     * @param $value
     */
    public function setOrderStatusShippingBlocked($value)
    {
        $this->order_status_shipping_blocked = $value;
    }
    
    /**
     * @param $value
     */
    public function setOrderStatusCanceled($value)
    {
        $this->order_status_canceled = $value;
    }
    
    /**
     * @param $value
     */
    public function setReverseCategoriesSortOrder($value)
    {
        $this->reverse_categories_sort_order = $value;
    }
    
    /**
     * @param $value
     */
    public function setReverseItemsSortOrder($value)
    {
        $this->reverse_items_sort_order = $value;
    }
    
    /**
     * @param $value
     */
    public function setExportDescriptionType($value)
    {
        $this->export_description_type = $value;
    }
    
    /**
     * @param $value
     */
    public function setShopgateTableVersion($value)
    {
        $this->shopgate_table_version = $value;
    }
    
    /**
     * @param $value
     */
    public function setMaximumCategoryExportDepth($value)
    {
        $this->maximum_category_export_depth = $value;
    }
    
    /**
     * @param mixed $export_option_as_input_field
     */
    public function setExportOptionAsInputField($export_option_as_input_field)
    {
        $this->export_option_as_input_field = $export_option_as_input_field;
    }
    
    /**
     * @param string $value
     */
    public function setExportFiltersAsProperties($value)
    {
        $this->export_filters_as_properties = $value;
    }

    /**
     * @param string $value
     */
    public function setPaymentNameMapping($value)
    {
        $this->payment_name_mapping = $value;
    }

}
