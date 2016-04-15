DROP TABLE IF EXISTS `feature_set_values`;
CREATE TABLE IF NOT EXISTS `feature_set_values` (
  `feature_set_id` int(11) unsigned NOT NULL,
  `feature_value_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`feature_set_id`,`feature_value_id`)
) ENGINE=MyISAM ;