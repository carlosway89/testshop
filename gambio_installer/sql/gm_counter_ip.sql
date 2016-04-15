DROP TABLE IF EXISTS `gm_counter_ip`;
CREATE TABLE `gm_counter_ip` (
  `gm_ip_id` int(10) NOT NULL auto_increment,
  `gm_ip_value` varchar(255) default NULL,
  `gm_ip_date` datetime NOT NULL,
  PRIMARY KEY  (`gm_ip_id`),
  KEY `gm_ip_date` (`gm_ip_date`)
) ENGINE=MyISAM;