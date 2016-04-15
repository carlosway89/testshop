DROP TABLE IF EXISTS `brickfox_export`;
CREATE TABLE `brickfox_export` (
  `brickfox_export_id` int(11) NOT NULL auto_increment,
  `type` varchar(50) NOT NULL default '',
  `number_exported` int(11) NOT NULL default '0',
  `date_exported` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`brickfox_export_id`)
) ENGINE=MyISAM;