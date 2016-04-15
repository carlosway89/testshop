DROP TABLE IF EXISTS `user_configuration`;
CREATE TABLE `user_configuration` (
  `customer_id` int(11) NOT NULL,
  `configuration_key` varchar(255) NOT NULL,
  `configuration_value` varchar(255) NOT NULL,
  PRIMARY KEY (`customer_id`,`configuration_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;