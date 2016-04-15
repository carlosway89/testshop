DROP TABLE IF EXISTS `feature_index`;
CREATE TABLE IF NOT EXISTS `feature_index` (
  `feature_set_id` int(11) unsigned NOT NULL,
  `date_created` datetime default NULL,
  `feature_value_index` text,
  PRIMARY KEY  (`feature_set_id`),
  FULLTEXT KEY `feature_value_index` (`feature_value_index`)
) ENGINE=MyISAM ;