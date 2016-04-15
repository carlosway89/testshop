DROP TABLE IF EXISTS `orders_paypal`;
CREATE TABLE IF NOT EXISTS `orders_paypal` (
  `orders_id` int(10) unsigned NOT NULL,
  `correlation_id` varchar(255) DEFAULT NULL,
  `payer_id` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `paymentaction` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`orders_id`),
  KEY `correlation_id` (`correlation_id`)
) ENGINE=MyISAM;