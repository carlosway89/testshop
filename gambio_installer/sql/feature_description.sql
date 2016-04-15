DROP TABLE IF EXISTS `feature_description`;
CREATE TABLE IF NOT EXISTS `feature_description` (
  `feature_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `feature_name` varchar(45) default NULL,
  `feature_admin_name` VARCHAR(45) NOT NULL,
  PRIMARY KEY  (`feature_id`,`language_id`)
) ENGINE=MyISAM ;