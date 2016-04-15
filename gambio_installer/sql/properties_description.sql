DROP TABLE IF EXISTS `properties_description`;
CREATE TABLE `properties_description` (
  `properties_description_id` int(10) unsigned NOT NULL auto_increment,
  `properties_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `properties_name` varchar(255) NOT NULL,
  `properties_admin_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`properties_description_id`),
  KEY `properties_id` (`properties_id`,`language_id`)
) ENGINE=MyISAM ;

INSERT INTO `properties_description` VALUES(1, 1, 2, 'Größe', '');
INSERT INTO `properties_description` VALUES(2, 1, 1, 'Size', '');
INSERT INTO `properties_description` VALUES(3, 2, 2, 'Farbe', '');
INSERT INTO `properties_description` VALUES(4, 2, 1, 'Color', '');