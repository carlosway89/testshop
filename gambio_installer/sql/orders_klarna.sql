DROP TABLE IF EXISTS `orders_klarna`;
CREATE TABLE `orders_klarna` (
  `orders_id` int(10) unsigned NOT NULL,
  `rno` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `risk_status` varchar(255) NOT NULL,
  `inv_rno` varchar(255) NOT NULL,
  PRIMARY KEY (`orders_id`),
  KEY `rno` (`rno`)
) ENGINE=MyISAM;