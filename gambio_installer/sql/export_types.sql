DROP TABLE IF EXISTS `export_types`;
CREATE TABLE `export_types` (
  `type_id` int(11) unsigned NOT NULL,
  `language_id` int(11) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`type_id`,`language_id`),
  KEY `language_id` (`language_id`)
) ENGINE=MyISAM;

INSERT INTO `export_types` (`type_id`, `language_id`, `name`) VALUES(1, 1, 'Product export');
INSERT INTO `export_types` (`type_id`, `language_id`, `name`) VALUES(1, 2, 'Artikelexport');
INSERT INTO `export_types` (`type_id`, `language_id`, `name`) VALUES(2, 1, 'Price comparison');
INSERT INTO `export_types` (`type_id`, `language_id`, `name`) VALUES(2, 2, 'Preis-Portal');