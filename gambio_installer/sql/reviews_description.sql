DROP TABLE IF EXISTS `reviews_description`;
CREATE TABLE `reviews_description` (
  `reviews_id` int(11) NOT NULL default '0',
  `languages_id` int(11) NOT NULL default '0',
  `reviews_text` text NOT NULL,
  PRIMARY KEY  (`reviews_id`,`languages_id`)
) ENGINE=MyISAM;