DROP TABLE IF EXISTS `properties`;
CREATE TABLE `properties` (
  `properties_id` int(10) unsigned NOT NULL auto_increment,
  `sort_order` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`properties_id`),
  KEY `properties_id` (`properties_id`,`sort_order`)
) ENGINE=MyISAM ;

INSERT INTO `properties` VALUES(1, 1);
INSERT INTO `properties` VALUES(2, 2);