DROP TABLE IF EXISTS `database_version`;
CREATE TABLE `database_version` (
  `version` varchar(32) NOT NULL default ''
) ENGINE=MyISAM;

INSERT INTO `database_version` (`version`) VALUES('3.0.4.0');
