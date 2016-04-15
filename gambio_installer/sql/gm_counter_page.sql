DROP TABLE IF EXISTS `gm_counter_page`;
CREATE TABLE `gm_counter_page` (
  `gm_counter_page_id` int(10) NOT NULL auto_increment,
  `gm_counter_page_name` varchar(255) default NULL,
  `gm_counter_page_type` varchar(255) default NULL,
  `gm_counter_page_date` datetime NOT NULL,
  PRIMARY KEY  (`gm_counter_page_id`),
  KEY `gm_counter_page_date` (`gm_counter_page_date`)
) ENGINE=MyISAM;