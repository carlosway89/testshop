DROP TABLE IF EXISTS `orders_tax_sum_items`;
CREATE TABLE `orders_tax_sum_items` (
  `orders_tax_sum_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_class` varchar(100) NOT NULL,
  `tax_zone` varchar(100) NOT NULL,
  `tax_rate` decimal(15,4) NOT NULL,
  `gross` decimal(15,4) NOT NULL,
  `net` decimal(15,4) NOT NULL,
  `tax` decimal(15,4) NOT NULL,
  `currency` varchar(100) NOT NULL,
  `order_id` int(11) NOT NULL,
  `insert_date` datetime NOT NULL,
  `last_change_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tax_description` varchar(100) NOT NULL,
  PRIMARY KEY (`orders_tax_sum_item_id`),
  INDEX (`order_id`),
  INDEX (`insert_date`, `tax_zone`, `tax_class`, `tax_rate`, `currency`),
  INDEX (`insert_date`, `order_id`, `tax_zone`, `tax_class`, `tax_rate`, `currency`)
) ENGINE=MyISAM;