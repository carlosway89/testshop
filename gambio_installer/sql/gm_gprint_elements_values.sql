DROP TABLE IF EXISTS `gm_gprint_elements_values`;
CREATE TABLE `gm_gprint_elements_values` (
  `gm_gprint_elements_values_id` int(10) unsigned NOT NULL auto_increment,
  `languages_id` int(10) unsigned NOT NULL,
  `gm_gprint_elements_groups_id` int(10) unsigned NOT NULL,
  `name` varchar(255) default NULL,
  `elements_value` text,
  PRIMARY KEY  (`gm_gprint_elements_values_id`)
) ENGINE=MyISAM ;