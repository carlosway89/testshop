DROP TABLE IF EXISTS `amzadvpay_captures`;
CREATE TABLE `amzadvpay_captures` (
  `amzadvpay_captures_id` int(11) NOT NULL AUTO_INCREMENT,
  `orders_id` int(11) NOT NULL,
  `order_reference_id` varchar(64) NOT NULL,
  `authorization_reference_id` varchar(64) NOT NULL,
  `capture_reference_id` varchar(64) NOT NULL,
  `state` varchar(20) NOT NULL,
  `last_update` datetime NOT NULL,
  `last_details` mediumtext NOT NULL,
  PRIMARY KEY (`amzadvpay_captures_id`),
  KEY `orders_id` (`orders_id`,`order_reference_id`,`authorization_reference_id`,`capture_reference_id`)
) ENGINE=MyISAM;