DROP TABLE IF EXISTS `orders_klarna_creditpart`;
CREATE TABLE `orders_klarna_creditpart` (
  `ok_creditpart_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orders_id` int(10) unsigned NOT NULL,
  `products_model` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `sent_time` datetime NOT NULL,
  PRIMARY KEY (`ok_creditpart_id`),
  KEY `orders_id` (`orders_id`)
) ENGINE=MyISAM;