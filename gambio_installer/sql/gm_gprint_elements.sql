DROP TABLE IF EXISTS `gm_gprint_elements`;
CREATE TABLE `gm_gprint_elements` (
  `gm_gprint_elements_id` int(10) unsigned NOT NULL auto_increment,
  `gm_gprint_elements_groups_id` int(10) unsigned NOT NULL,
  `gm_gprint_surfaces_id` int(10) unsigned NOT NULL,
  `position_x` int(10) NOT NULL default '0',
  `position_y` int(10) NOT NULL default '0',
  `height` int(10) unsigned NOT NULL default '0',
  `width` int(10) unsigned NOT NULL default '0',
  `z_index` int(10) NOT NULL default '0',
  `max_characters` int(10) NOT NULL default '0',
  `allowed_extensions` VARCHAR( 255 ) NOT NULL default '',
  `show_name` tinyint(1) unsigned NOT NULL default '0',
  `minimum_filesize` decimal(7,4) NOT NULL default '0.0000',
  `maximum_filesize` decimal(7,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`gm_gprint_elements_id`)
) ENGINE=MyISAM ;