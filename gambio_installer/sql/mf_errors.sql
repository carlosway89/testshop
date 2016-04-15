DROP TABLE IF EXISTS `mf_errors`;
CREATE TABLE `mf_errors` (
  `errorId` smallint(5) unsigned NOT NULL auto_increment,
  `errorText` text NOT NULL,
  `customerId` int(10) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`errorId`)
) ENGINE=MyISAM;