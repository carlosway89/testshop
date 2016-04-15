DROP TABLE IF EXISTS `billsafe_directpayments`;
CREATE TABLE `billsafe_directpayments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orders_id` int(10) unsigned NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_id` (`orders_id`)
) ENGINE=MyISAM;