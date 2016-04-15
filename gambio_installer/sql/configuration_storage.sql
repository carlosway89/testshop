DROP TABLE IF EXISTS `configuration_storage`;
CREATE TABLE `configuration_storage` (
  `key` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `last_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM;