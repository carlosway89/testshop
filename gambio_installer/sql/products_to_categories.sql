DROP TABLE IF EXISTS `products_to_categories`;
CREATE TABLE `products_to_categories` (
  `products_id` int(11) NOT NULL default '0',
  `categories_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`products_id`,`categories_id`),
  KEY `categories_id` (`categories_id`,`products_id`)
) ENGINE=MyISAM;

INSERT INTO `products_to_categories` (`products_id`, `categories_id`) VALUES(1, 1);