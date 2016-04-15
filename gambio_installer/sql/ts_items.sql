DROP TABLE IF EXISTS `ts_items`;
CREATE TABLE `ts_items` (
  `ts_items_id` int(11) NOT NULL AUTO_INCREMENT,
  `ts_id` varchar(33) NOT NULL,
  `retrievaldate` datetime NOT NULL,
  `creationdate` datetime NOT NULL,
  `id` int(11) NOT NULL,
  `currency` varchar(3) NOT NULL,
  `grossfee` decimal(15,5) NOT NULL,
  `netfee` decimal(15,5) NOT NULL,
  `protectedamount` decimal(15,5) NOT NULL,
  `protectionduration` int(11) NOT NULL,
  `tsproductid` varchar(255) NOT NULL,
  PRIMARY KEY (`ts_items_id`),
  KEY `ts_id` (`ts_id`)
) ENGINE=MyISAM;