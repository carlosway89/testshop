DROP TABLE IF EXISTS `ts_protection`;
CREATE TABLE `ts_protection` (
  `orders_id` int(11) NOT NULL,
  `application_number` int(11) NOT NULL,
  `tsid` varchar(33) NOT NULL,
  `result` int(11) DEFAULT NULL,
  PRIMARY KEY (`orders_id`),
  KEY `application_number` (`application_number`)
) ENGINE=MyISAM;