DROP TABLE IF EXISTS `gm_gprint_surfaces_groups`;
CREATE TABLE `gm_gprint_surfaces_groups` (
  `gm_gprint_surfaces_groups_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  PRIMARY KEY  (`gm_gprint_surfaces_groups_id`)
) ENGINE=MyISAM ;