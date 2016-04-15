DROP TABLE IF EXISTS `orders_klarna_returnamount`;
CREATE TABLE `orders_klarna_returnamount` (
  `ok_returnamount_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orders_id` int(10) unsigned NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `vat` decimal(15,4) NOT NULL,
  `description` varchar(255) NOT NULL,
  `sent_time` datetime NOT NULL,
  PRIMARY KEY (`ok_returnamount_id`),
  KEY `orders_id` (`orders_id`)
) ENGINE=MyISAM;