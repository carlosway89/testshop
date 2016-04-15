DROP TABLE IF EXISTS `gm_boxes_area`;
CREATE TABLE IF NOT EXISTS `gm_boxes_area` (
  `boxes_area_id` int(10) unsigned NOT NULL auto_increment,
  `boxes_id` int(11) NOT NULL,
  `area` varchar(32) NOT NULL,
  PRIMARY KEY  (`boxes_area_id`)
) ENGINE=MyISAM;