DROP TABLE IF EXISTS `zones_to_geo_zones`;
CREATE TABLE `zones_to_geo_zones` (
  `association_id` int(11) NOT NULL auto_increment,
  `zone_country_id` int(11) NOT NULL default '0',
  `zone_id` int(11) NOT NULL,
  `geo_zone_id` int(11) NOT NULL,
  `last_modified` datetime NOT NULL,
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`association_id`),
  KEY `zone_id` (`zone_id`),
  KEY `geo_zone_id` (`geo_zone_id`, `zone_country_id`),
  KEY `zone_country_id` (`zone_country_id`,`zone_id`)
) ENGINE=MyISAM;