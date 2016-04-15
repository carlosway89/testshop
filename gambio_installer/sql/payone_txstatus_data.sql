DROP TABLE IF EXISTS `payone_txstatus_data`;
CREATE TABLE `payone_txstatus_data` (
  `payone_txstatus_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `payone_txstatus_id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`payone_txstatus_data_id`),
  KEY `payone_txstatus_id` (`payone_txstatus_id`)
) ENGINE=MyISAM;