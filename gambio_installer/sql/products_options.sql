DROP TABLE IF EXISTS `products_options`;
CREATE TABLE `products_options` (
  `products_options_id` int(11) NOT NULL default '0',
  `language_id` int(11) NOT NULL default '1',
  `products_options_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`products_options_id`,`language_id`)
) ENGINE=MyISAM;