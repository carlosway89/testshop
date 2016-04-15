DROP TABLE IF EXISTS `products_google_categories`;
CREATE TABLE `products_google_categories` (
  `products_google_categories_id` int(11) NOT NULL auto_increment,
  `products_id` int(11) default NULL,
  `google_category` text,
  PRIMARY KEY  (`products_google_categories_id`),
  KEY `products_id` (`products_id`)
) ENGINE=MyISAM ;