DROP TABLE IF EXISTS `gm_gprint_surfaces`;
CREATE TABLE `gm_gprint_surfaces` (
  `gm_gprint_surfaces_id` int(10) unsigned NOT NULL auto_increment,
  `gm_gprint_surfaces_groups_id` int(10) unsigned NOT NULL,
  `width` int(10) unsigned NOT NULL,
  `height` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`gm_gprint_surfaces_id`)
) ENGINE=MyISAM ;