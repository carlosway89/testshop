DROP TABLE IF EXISTS `payone_transactions`;
CREATE TABLE `payone_transactions` (
  `payone_transactions_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orders_id` int(10) unsigned NOT NULL,
  `status` varchar(255) NOT NULL,
  `txid` varchar(100) NOT NULL,
  `userid` varchar(100) NOT NULL,
  `created` datetime DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`payone_transactions_id`),
  KEY `orders_id` (`orders_id`,`txid`)
) ENGINE=MyISAM;