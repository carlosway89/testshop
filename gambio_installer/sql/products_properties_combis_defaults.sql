DROP TABLE IF EXISTS `products_properties_combis_defaults`;
CREATE TABLE `products_properties_combis_defaults` (
  `products_properties_combis_defaults_id` int(11) unsigned NOT NULL auto_increment,
  `products_id` int(10) unsigned NOT NULL,
  `combi_ean` VARCHAR(20) NOT NULL,
  `combi_quantity` decimal(15,4) NOT NULL default '0.0000',
  `combi_shipping_status_id` int(11) NOT NULL,
  `combi_weight` decimal(15,4) NOT NULL,
  `combi_price_type` ENUM( 'calc', 'fix' ) NOT NULL,
  `combi_price` decimal(15,4) NOT NULL,
  `products_vpe_id` int(11) NOT NULL,
  `vpe_value` decimal(15,4) NOT NULL,
  PRIMARY KEY  (`products_properties_combis_defaults_id`)
) ENGINE=MyISAM ;

INSERT INTO `products_properties_combis_defaults` VALUES(1, 1, '123456789', 100.0000, 2, 2.0000, 'calc', 0.0000, 0, 0.0000);