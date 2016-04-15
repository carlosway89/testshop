DROP TABLE IF EXISTS `orders_billsafe`;
CREATE TABLE `orders_billsafe` (
  `orders_id` int(10) unsigned NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  PRIMARY KEY (`orders_id`),
  KEY `transaction_id` (`transaction_id`)
) ENGINE=MyISAM;