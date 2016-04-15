DROP TABLE IF EXISTS `gm_counter_page_history`;
CREATE TABLE `gm_counter_page_history` (
  `gm_counter_page_history_id` int(10) NOT NULL auto_increment,
  `gm_counter_page_history_name` varchar(255) default NULL,
  `gm_counter_page_history_type` varchar(255) default NULL,
  `gm_counter_page_history_hits` int(10) default NULL,
  `gm_counter_page_history_date` datetime NOT NULL,
  PRIMARY KEY  (`gm_counter_page_history_id`)
) ENGINE=MyISAM;