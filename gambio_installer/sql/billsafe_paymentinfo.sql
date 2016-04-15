DROP TABLE IF EXISTS `billsafe_paymentinfo`;
CREATE TABLE `billsafe_paymentinfo` (
  `orders_id` int(11) NOT NULL,
  `received` datetime NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `recipient` varchar(100) NOT NULL,
  `bankCode` varchar(8) NOT NULL,
  `accountNumber` varchar(10) NOT NULL,
  `bankName` varchar(100) NOT NULL,
  `bic` varchar(11) NOT NULL,
  `iban` varchar(34) NOT NULL,
  `reference` varchar(50) NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `currencyCode` varchar(3) NOT NULL,
  `paymentPeriod` int(11) NOT NULL,
  `note` varchar(200) NOT NULL,
  `legalNote` text NOT NULL,
  PRIMARY KEY (`orders_id`),
  KEY `transaction_id` (`transaction_id`)
) ENGINE=MyISAM;