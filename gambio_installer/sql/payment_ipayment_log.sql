DROP TABLE IF EXISTS `payment_ipayment_log`;
CREATE TABLE `payment_ipayment_log` (
  `ip_LOG_ID` int(10) unsigned NOT NULL auto_increment,
  `ip_LOG_MESSAGE` varchar(255) NOT NULL,
  `ip_LOG_INFO` text NOT NULL,
  `ip_LOG_DATE` datetime NOT NULL,
  PRIMARY KEY  (`ip_LOG_ID`)
) ENGINE=MyISAM;