DROP TABLE IF EXISTS `additional_fields`;
CREATE TABLE `additional_fields` (
  `additional_field_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_key` varchar(255) NOT NULL,
  `item_type` enum('product') NOT NULL DEFAULT 'product',
  `multilingual` TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (`additional_field_id`),
  UNIQUE KEY `field_key` (`field_key`,`item_type`)
) ENGINE=MyISAM;