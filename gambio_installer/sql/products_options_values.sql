DROP TABLE IF EXISTS `products_options_values`;
CREATE TABLE `products_options_values` (
  `products_options_values_id` int(11) NOT NULL default '0',
  `language_id` int(11) NOT NULL default '1',
  `products_options_values_name` varchar(255) NOT NULL default '',
  `gm_filename` varchar(255) NOT NULL,
  PRIMARY KEY  (`products_options_values_id`,`language_id`),
  KEY `products_options_values_name` (`products_options_values_name`,`language_id`),
  FULLTEXT KEY `products_options_values_name_2` (`products_options_values_name`)
) ENGINE=MyISAM;