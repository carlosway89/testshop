DROP TABLE IF EXISTS `products_attributes`;
CREATE TABLE `products_attributes` (
  `products_attributes_id` int(11) NOT NULL auto_increment,
  `products_id` int(11) NOT NULL default '0',
  `options_id` int(11) NOT NULL default '0',
  `options_values_id` int(11) NOT NULL default '0',
  `options_values_price` decimal(15,4) NOT NULL default '0.0000',
  `price_prefix` char(1) NOT NULL default '',
  `attributes_model` varchar(64) default NULL,
  `attributes_stock` decimal(15,4) default NULL,
  `options_values_weight` decimal(15,4) NOT NULL default '0.0000',
  `weight_prefix` char(1) NOT NULL default '',
  `sortorder` int(11) default NULL,
  `products_vpe_id` int(11) UNSIGNED default '0' NOT NULL ,
  `gm_vpe_value` decimal(15,4 ) default '0.0000' NOT NULL ,
  `gm_ean` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`products_attributes_id`),
  KEY `products_id` (`products_id`,`options_id`,`options_values_id`,`sortorder`),
  KEY `options_values_id` (`options_values_id`),
  KEY `sortorder` (`sortorder`),
  FULLTEXT KEY `attributes_model` (`attributes_model`)
) ENGINE=MyISAM ;