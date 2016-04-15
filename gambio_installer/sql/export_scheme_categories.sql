DROP TABLE IF EXISTS `export_scheme_categories`;
CREATE TABLE `export_scheme_categories` (
  `scheme_id` int(11) NOT NULL,
  `categories_id` int(11) NOT NULL,
  `selection_state` enum('self_all_sub','self_some_sub','self_no_sub','no_self_all_sub','no_self_some_sub','no_self_no_sub') NOT NULL DEFAULT 'no_self_no_sub',
  PRIMARY KEY (`scheme_id`,`categories_id`)
) ENGINE=MyISAM;