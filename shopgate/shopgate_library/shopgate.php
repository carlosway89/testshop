<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2015 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

if (!defined('DS')) define('DS', '/');

if( file_exists(dirname(__FILE__).DS.'dev.php') )
    require_once(dirname(__FILE__).DS.'dev.php');

// Library
require_once(dirname(__FILE__).DS.'classes'.DS.'core.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'apis.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'configuration.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'customers.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'orders.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'external_orders.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'items.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'redirect.php');

/**
 * global
 */
require_once(dirname(__FILE__).DS.'classes'.DS.'models/Abstract.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/AbstractExport.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/XmlEmptyObject.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/XmlResultObject.php');
/**
 * catalog
 */
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Review.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Product.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Price.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/TierPrice.php');

require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Category.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/CategoryPath.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Shipping.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Manufacturer.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Visibility.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Property.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Stock.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Identifier.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Tag.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Relation.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Attribute.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Input.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Validation.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Option.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/AttributeGroup.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Attribute.php');

/**
 * helper
 */
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/DataStructure.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/Pricing.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/String.php');

/**
 * media
 */
require_once(dirname(__FILE__).DS.'classes'.DS.'models/media/Image.php');

// Shopgate-Vendors
require_once(dirname(__FILE__).DS.'vendors'.DS.'2d_is.php');

// External-Vendors
include_once(dirname(__FILE__).DS.'vendors'.DS.'JSON.php');
