DROP TABLE IF EXISTS `module_newsletter`;
CREATE TABLE `module_newsletter` (
  `newsletter_id` int(11) NOT NULL auto_increment,
  `title` text NOT NULL,
  `bc` text NOT NULL,
  `cc` text NOT NULL,
  `date` datetime default NULL,
  `status` int(1) NOT NULL default '0',
  `body` text NOT NULL,
  PRIMARY KEY  (`newsletter_id`)
) ENGINE=MyISAM ;