DROP TABLE IF EXISTS `amzadvpay_orders`;
CREATE TABLE `amzadvpay_orders` (
  `amzadvpay_orders_id` int(11) NOT NULL AUTO_INCREMENT,
  `orders_id` int(11) NOT NULL,
  `order_reference_id` varchar(64) NOT NULL,
  `state` varchar(20) NOT NULL,
  `last_update` datetime NOT NULL,
  `last_details` mediumtext NOT NULL,
  PRIMARY KEY (`amzadvpay_orders_id`),
  UNIQUE KEY `orders_id_2` (`orders_id`),
  KEY `orders_id` (`orders_id`,`order_reference_id`)
) ENGINE=MyISAM;