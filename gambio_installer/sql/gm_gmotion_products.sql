DROP TABLE IF EXISTS `gm_gmotion_products`;
CREATE TABLE `gm_gmotion_products` (
  `gm_gmotion_products_id` int(10) unsigned NOT NULL auto_increment,
  `products_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`gm_gmotion_products_id`),
  UNIQUE KEY `products_id` (`products_id`)
) ENGINE=MyISAM ;

INSERT INTO `gm_gmotion_products` (`gm_gmotion_products_id`, `products_id`) VALUES(1, 1);