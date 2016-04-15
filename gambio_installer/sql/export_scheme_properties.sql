DROP TABLE IF EXISTS `export_scheme_properties`;
CREATE TABLE `export_scheme_properties` (
  `scheme_id` int(11) unsigned NOT NULL,
  `properties_column` varchar(100) NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`scheme_id`,`properties_column`),
  KEY `scheme_id` (`scheme_id`,`sort_order`)
) ENGINE=MyISAM;