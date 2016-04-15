DROP TABLE IF EXISTS `feature_value`;
CREATE TABLE IF NOT EXISTS `feature_value` (
  `feature_value_id` int(11) NOT NULL auto_increment,
  `feature_id` int(11) default NULL,
  `sort_order` int(11) default NULL,
  PRIMARY KEY  (`feature_value_id`)
) ENGINE=MyISAM ;