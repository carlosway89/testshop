DROP TABLE IF EXISTS `orders_products_properties`;
CREATE TABLE IF NOT EXISTS `orders_products_properties` (
  `orders_products_properties_id` int(10) unsigned NOT NULL auto_increment,
  `orders_products_id` int(10) unsigned default NULL,
  `products_properties_combis_id` int(10) unsigned default NULL,
  `properties_name` varchar(255) NOT NULL,
  `values_name` varchar(255) NOT NULL,
  `properties_price_type` varchar(8) NOT NULL,
  `properties_price` decimal(16,4) NOT NULL,
  PRIMARY KEY  (`orders_products_properties_id`)
) ENGINE=MyISAM ;