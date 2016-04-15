DROP TABLE IF EXISTS `products_hermesoptions`;
CREATE TABLE `products_hermesoptions` (
  `products_id` int(11) NOT NULL,
  `min_pclass` enum('XS','S','M','L','XL','XXL') NOT NULL,
  PRIMARY KEY (`products_id`)
) ENGINE=MyISAM;