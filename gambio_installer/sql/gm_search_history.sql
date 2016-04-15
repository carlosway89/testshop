DROP TABLE IF EXISTS `gm_search_history`;
CREATE TABLE `gm_search_history` (
  `gm_search_history_id` int(10) NOT NULL auto_increment,
  `gm_search_ip` varchar(15) NOT NULL default '',
  `gm_search_date` datetime NOT NULL,
  PRIMARY KEY  (`gm_search_history_id`)
) ENGINE=MyISAM;