DROP TABLE IF EXISTS `export_cronjobs`;
CREATE TABLE `export_cronjobs` (
  `cronjob_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `scheme_id` int(10) unsigned NOT NULL,
  `due_date` datetime NOT NULL,
  PRIMARY KEY (`cronjob_id`),
  KEY `scheme_id` (`scheme_id`)
) ENGINE=MyISAM;