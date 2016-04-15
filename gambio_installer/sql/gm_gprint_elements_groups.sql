DROP TABLE IF EXISTS `gm_gprint_elements_groups`;
CREATE TABLE `gm_gprint_elements_groups` (
  `gm_gprint_elements_groups_id` int(10) unsigned NOT NULL auto_increment,
  `group_type` varchar(255) default NULL,
  `group_name` varchar(255) default NULL,
  PRIMARY KEY  (`gm_gprint_elements_groups_id`)
) ENGINE=MyISAM ;