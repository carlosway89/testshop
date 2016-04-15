DROP TABLE IF EXISTS `paypal_paylink`;
CREATE TABLE IF NOT EXISTS `paypal_paylink` (
  `orders_id` int(10) unsigned NOT NULL,
  `paycode` varchar(32) NOT NULL,
  `amount` decimal(15,4) DEFAULT NULL,
  PRIMARY KEY (`orders_id`),
  KEY `paycode` (`paycode`)
) ENGINE=MyISAM;