DROP TABLE IF EXISTS `orders_products`;
CREATE TABLE `orders_products` (
  `orders_products_id` int(11) NOT NULL auto_increment,
  `orders_id` int(11) NOT NULL default '0',
  `products_id` int(11) NOT NULL default '0',
  `products_model` varchar(64) default NULL,
  `products_name` varchar(255) NOT NULL default '',
  `products_price` decimal(15,4) NOT NULL default '0.0000',
  `products_discount_made` decimal(4,2) default NULL,
  `products_shipping_time` varchar(255) default NULL,
  `final_price` decimal(15,4) NOT NULL default '0.0000',
  `products_tax` decimal(7,4) NOT NULL default '0.0000',
  `products_quantity` decimal(15,4) NOT NULL default '0.0000',
  `allow_tax` int(1) NOT NULL default '0',
  `product_type` int(11) NOT NULL DEFAULT '1',
  `properties_combi_price` decimal(15,4) NOT NULL default '0.0000',
  `properties_combi_model` varchar(64) NOT NULL,
  `checkout_information` text,
  PRIMARY KEY (`orders_products_id`),
  KEY `orders_id` (`orders_id`),
  KEY `products_id` (`products_id`,`orders_id`)
) ENGINE=MyISAM;