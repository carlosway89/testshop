DROP TABLE IF EXISTS `properties_values`;
CREATE TABLE `properties_values` (
  `properties_values_id` int(10) unsigned NOT NULL auto_increment,
  `properties_id` int(10) unsigned NOT NULL,
  `sort_order` int(10) unsigned NOT NULL,
  `value_model` varchar(64) NOT NULL,
  `value_price` decimal(9,4) NOT NULL,
  PRIMARY KEY  (`properties_values_id`),
  KEY `properties_values_id` (`properties_values_id`,`properties_id`,`sort_order`),
  KEY `properties_id` (`properties_id`,`sort_order`)
) ENGINE=MyISAM ;

INSERT INTO `properties_values` VALUES(1, 1, 1, 's', 0.0000);
INSERT INTO `properties_values` VALUES(2, 1, 2, 'm', 0.0000);
INSERT INTO `properties_values` VALUES(3, 1, 3, 'l', 5.0000);
INSERT INTO `properties_values` VALUES(4, 2, 1, 'gold', 0.0000);
INSERT INTO `properties_values` VALUES(5, 2, 2, 'red', 0.0000);
INSERT INTO `properties_values` VALUES(6, 2, 3, 'black', 2.0000);