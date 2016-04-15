DROP TABLE IF EXISTS `products_item_codes`;
CREATE TABLE `products_item_codes` (
  `products_id` int(11) NOT NULL,
  `code_isbn` varchar(128) default NULL,
  `code_upc` varchar(128) default NULL,
  `code_mpn` varchar(128) default NULL,
  `code_jan` varchar(128) default NULL,
  `google_export_condition` varchar(64) NOT NULL default 'neu',
  `google_export_availability_id` int(10) unsigned NOT NULL,
  `brand_name` varchar(128) NOT NULL,
  `identifier_exists` TINYINT(1) NOT NULL DEFAULT '1',
  `gender` ENUM( '', 'Herren', 'Damen', 'Unisex' ) NOT NULL DEFAULT '',
  `age_group` ENUM( '', 'Erwachsene', 'Kinder' ) NOT NULL DEFAULT '',
  `expiration_date` DATE NOT NULL,
  PRIMARY KEY  (`products_id`),
  KEY `google_export_condition` (`google_export_condition`),
  KEY `brand_name` (`brand_name`)
) ENGINE=MyISAM;