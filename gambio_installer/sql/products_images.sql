DROP TABLE IF EXISTS `products_images`;
CREATE TABLE `products_images` (
  `image_id` int(11) NOT NULL auto_increment,
  `products_id` int(11) NOT NULL default '0',
  `image_nr` smallint(6) NOT NULL default '0',
  `image_name` varchar(254) NOT NULL default '',
  `gm_show_image` int(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`image_id`),
  UNIQUE KEY `products_id` (`products_id`,`image_nr`)
) ENGINE=MyISAM ;

INSERT INTO `products_images` (`image_id`, `products_id`, `image_nr`, `image_name`, `gm_show_image`) VALUES(7, 1, 3, 'artikelbild_1_3.jpg', 1);
INSERT INTO `products_images` (`image_id`, `products_id`, `image_nr`, `image_name`, `gm_show_image`) VALUES(6, 1, 2, 'artikelbild_1_2.jpg', 1);
INSERT INTO `products_images` (`image_id`, `products_id`, `image_nr`, `image_name`, `gm_show_image`) VALUES(5, 1, 1, 'artikelbild_1_1.jpg', 1);