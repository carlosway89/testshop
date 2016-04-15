CREATE TABLE IF NOT EXISTS `orders_payment_instruction` (
	 `orders_payment_instruction_id` int(11) NOT NULL AUTO_INCREMENT,
	 `orders_id` int(11) NOT NULL,
	 `reference` varchar(255) NOT NULL,
	 `bank_name` varchar(255) NOT NULL,
	 `account_holder` varchar(255) NOT NULL,
	 `iban` varchar(34) NOT NULL,
	 `bic` varchar(11) NOT NULL,
	 `value` decimal(10,4) NOT NULL,
	 `currency` varchar(3) NOT NULL,
	 `due_date` date NOT NULL,
	 PRIMARY KEY (`orders_payment_instruction_id`),
	 KEY `orders_id` (`orders_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;
