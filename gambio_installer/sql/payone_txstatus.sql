DROP TABLE IF EXISTS `payone_txstatus`;
CREATE TABLE `payone_txstatus` (
  `payone_txstatus_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orders_id` int(11) NOT NULL,
  `received` datetime NOT NULL,
  PRIMARY KEY (`payone_txstatus_id`),
  KEY `orders_id` (`orders_id`)
) ENGINE=MyISAM;