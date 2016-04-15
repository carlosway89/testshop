DROP TABLE IF EXISTS `billsafe_products_shipped`;
CREATE TABLE `billsafe_products_shipped` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orders_id` int(10) unsigned NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `shipping_date` date NOT NULL,
  `parcel_service` varchar(255) NOT NULL,
  `parcel_company` varchar(255) NOT NULL DEFAULT '',
  `parcel_trackingid` varchar(255) NOT NULL,
  `article_number` varchar(255) NOT NULL,
  `article_name` varchar(255) NOT NULL,
  `article_type` varchar(20) NOT NULL,
  `article_quantity` int(5) NOT NULL,
  `article_grossprice` decimal(15,4) NOT NULL,
  `article_tax` decimal(4,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_id` (`orders_id`)
) ENGINE=MyISAM;