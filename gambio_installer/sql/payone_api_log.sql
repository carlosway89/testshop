DROP TABLE IF EXISTS `payone_api_log`;
CREATE TABLE `payone_api_log` (
  `p1_api_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) NOT NULL,
  `date_created` datetime NOT NULL,
  `log_count` int(11) NOT NULL,
  `log_level` int(11) NOT NULL,
  `message` mediumtext NOT NULL,
  `customers_id` int(11) NOT NULL,
  PRIMARY KEY (`p1_api_log_id`),
  KEY `event_id` (`event_id`)
) ENGINE=MyISAM;