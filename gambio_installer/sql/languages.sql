DROP TABLE IF EXISTS `languages`;
CREATE TABLE `languages` (
  `languages_id` int(11) NOT NULL auto_increment,
  `name` varchar(32) NOT NULL default '',
  `code` char(2) NOT NULL default '',
  `image` varchar(64) default NULL,
  `directory` varchar(32) default NULL,
  `sort_order` int(3) default NULL,
  `language_charset` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`languages_id`),
  KEY `IDX_LANGUAGES_NAME` (`name`)
) ENGINE=MyISAM ;

INSERT INTO `languages` (`languages_id`, `name`, `code`, `image`, `directory`, `sort_order`, `language_charset`, `status`) VALUES(1, 'English', 'en', 'icon.gif', 'english', 2, 'utf-8', 1);
INSERT INTO `languages` (`languages_id`, `name`, `code`, `image`, `directory`, `sort_order`, `language_charset`, `status`) VALUES(2, 'Deutsch', 'de', 'icon.gif', 'german', 1, 'utf-8', 1);