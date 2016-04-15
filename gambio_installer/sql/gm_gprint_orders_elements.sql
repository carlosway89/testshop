DROP TABLE IF EXISTS `gm_gprint_orders_elements`;
CREATE TABLE `gm_gprint_orders_elements` (
  `gm_gprint_orders_elements_id` int(10) unsigned NOT NULL auto_increment,
  `gm_gprint_orders_surfaces_id` int(10) unsigned NOT NULL,
  `position_x` int(10) NOT NULL default '0',
  `position_y` int(10) NOT NULL default '0',
  `height` int(10) unsigned NOT NULL default '0',
  `width` int(10) unsigned NOT NULL default '0',
  `z_index` int(10) NOT NULL default '0',
  `show_name` tinyint(1) unsigned NOT NULL default '0',
  `group_type` varchar(255) default NULL,
  `name` varchar(255) default NULL,
  `elements_value` text,
  `gm_gprint_uploads_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`gm_gprint_orders_elements_id`),
  KEY `gm_gprint_uploads_id` (`gm_gprint_uploads_id`)
) ENGINE=MyISAM ;