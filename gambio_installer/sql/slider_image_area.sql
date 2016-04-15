DROP TABLE IF EXISTS `slider_image_area`;
CREATE TABLE IF NOT EXISTS `slider_image_area` (
  `slider_image_area_id` int(11) NOT NULL auto_increment,
  `slider_image_id` int(11) NOT NULL,
  `shape` varchar(16) default NULL,
  `coords` varchar(255) default NULL,
  `title` varchar(255) default NULL,
  `link_url` varchar(255) default NULL,
  `link_target` varchar(16) default NULL,
  `flyover_content` TEXT default NULL,
  PRIMARY KEY  (`slider_image_area_id`),
  KEY `fk_slider_image_maparea_slider_image1` (`slider_image_id`) )
ENGINE=MyISAM ;