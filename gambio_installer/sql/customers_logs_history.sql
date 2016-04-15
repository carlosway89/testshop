DROP TABLE IF EXISTS `customers_logs_history`;
CREATE TABLE `customers_logs_history` (
  `customers_logs_history_id` int(10) unsigned NOT NULL auto_increment,
  `customers_id` int(10) unsigned NOT NULL,
  `confirmation_date` varchar(32) NOT NULL,
  `logfile` varchar(255) NOT NULL,
  PRIMARY KEY  (`customers_logs_history_id`),
  UNIQUE KEY `customers_id` (`customers_id`,`logfile`)
) ENGINE=MyISAM;