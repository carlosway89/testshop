DROP TABLE IF EXISTS `amzadvpay_authorizations`;
CREATE TABLE `amzadvpay_authorizations` (
  `amzadvpay_authorizations_id` int(11) NOT NULL AUTO_INCREMENT,
  `orders_id` int(11) NOT NULL,
  `order_reference_id` varchar(64) NOT NULL,
  `authorization_reference_id` varchar(64) NOT NULL,
  `state` varchar(20) NOT NULL,
  `last_update` datetime NOT NULL,
  `last_details` mediumtext NOT NULL,
  PRIMARY KEY (`amzadvpay_authorizations_id`),
  UNIQUE KEY `authorization_reference_id` (`authorization_reference_id`),
  KEY `orders_id` (`orders_id`),
  KEY `order_reference_id` (`order_reference_id`)
) ENGINE=MyISAM;