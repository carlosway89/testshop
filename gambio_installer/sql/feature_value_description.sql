DROP TABLE IF EXISTS `feature_value_description`;
CREATE TABLE IF NOT EXISTS `feature_value_description` (
  `feature_value_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `feature_value_text` varchar(45) default NULL,
  PRIMARY KEY  (`feature_value_id`,`language_id`)
) ENGINE=MyISAM ;