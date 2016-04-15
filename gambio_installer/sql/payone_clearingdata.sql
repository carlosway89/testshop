DROP TABLE IF EXISTS `payone_clearingdata`;
CREATE TABLE `payone_clearingdata` (
  `p1_clearingdata_id` int(11) NOT NULL AUTO_INCREMENT,
  `orders_id` int(10) unsigned NOT NULL,
  `bankaccountholder` varchar(255) NOT NULL,
  `bankcountry` varchar(2) NOT NULL,
  `bankaccount` varchar(32) NOT NULL,
  `bankcode` varchar(32) NOT NULL,
  `bankiban` varchar(32) NOT NULL,
  `bankbic` varchar(32) NOT NULL,
  `bankcity` varchar(64) NOT NULL,
  `bankname` varchar(128) NOT NULL,
  PRIMARY KEY (`p1_clearingdata_id`),
  KEY `orders_id` (`orders_id`)
) ENGINE=MyISAM;