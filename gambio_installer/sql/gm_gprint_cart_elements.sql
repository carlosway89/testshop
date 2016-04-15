DROP TABLE IF EXISTS `gm_gprint_cart_elements`;
CREATE TABLE `gm_gprint_cart_elements` (
  `gm_gprint_cart_elements_id` int(10) unsigned NOT NULL auto_increment,
  `gm_gprint_elements_id` int(10) unsigned default NULL,
  `products_id` tinytext,
  `customers_id` int(10) unsigned default NULL,
  `elements_value` text,
  `gm_gprint_uploads_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`gm_gprint_cart_elements_id`),
  UNIQUE KEY `gm_gprint_uploads_id` (`gm_gprint_uploads_id`)
) ENGINE=MyISAM ;