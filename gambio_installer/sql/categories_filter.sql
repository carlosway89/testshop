DROP TABLE IF EXISTS `categories_filter`;
CREATE TABLE IF NOT EXISTS `categories_filter` (
  `categories_id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL,
  `sort_order` int(11) default NULL,
  `selection_preview_mode` varchar(45) default NULL,
  `selection_template` varchar(45) default NULL,
  `value_conjunction` INT( 1  ) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY  (`categories_id`,`feature_id`)
) ENGINE=MyISAM ;