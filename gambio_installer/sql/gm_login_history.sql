DROP TABLE IF EXISTS `gm_login_history`;
CREATE TABLE `gm_login_history` (
  `gm_login_history_id` int(10) NOT NULL auto_increment,
  `gm_login_ip` varchar(15) NOT NULL default '',
  `gm_login_date` datetime NOT NULL,
  PRIMARY KEY  (`gm_login_history_id`)
) ENGINE=MyISAM ;