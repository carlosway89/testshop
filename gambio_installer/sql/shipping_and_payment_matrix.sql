DROP TABLE IF EXISTS `shipping_and_payment_matrix`;
CREATE TABLE `shipping_and_payment_matrix` (
 `country_code` varchar(255) NOT NULL,
 `language_id` int(11) NOT NULL,
 `shipping_info` text NOT NULL,
 `payment_info` text NOT NULL,
 `shipping_time` text NOT NULL,
 PRIMARY KEY (`country_code`,`language_id`)
) ENGINE=MyISAM;