CREATE TABLE IF NOT EXISTS `orders_paypal_payments` (
	  `orders_id` int(11) NOT NULL,
	  `payment_id` varchar(48) NOT NULL,
	  `mode` varchar(8) NOT NULL,
	  PRIMARY KEY (`orders_id`,`payment_id`)
) ENGINE=MyISAM;