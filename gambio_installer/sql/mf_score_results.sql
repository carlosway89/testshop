DROP TABLE IF EXISTS `mf_score_results`;
CREATE TABLE `mf_score_results` (
	`scoreId` smallint(5) unsigned NOT NULL auto_increment,
	`customerId` int(10) unsigned NOT NULL,
	`score` decimal(2,1) NOT NULL,
	`explanation` text NOT NULL,
	`lastCheck` int(10) unsigned NOT NULL,
	`negativeEntryList` text NOT NULL,
PRIMARY KEY  (`scoreId`),
UNIQUE KEY `customerId` (`customerId`)) ENGINE=MyISAM;