DROP TABLE IF EXISTS `customers_wishlist_attributes`;
CREATE TABLE `customers_wishlist_attributes` (
  `customers_basket_attributes_id` int(11) NOT NULL auto_increment,
  `customers_id` int(11) NOT NULL default '0',
  `products_id` tinytext NOT NULL,
  `products_options_id` int(11) NOT NULL default '0',
  `products_options_value_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`customers_basket_attributes_id`)
) ENGINE=MyISAM;