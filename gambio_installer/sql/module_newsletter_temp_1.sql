DROP TABLE IF EXISTS `module_newsletter_temp_1`;
CREATE TABLE `module_newsletter_temp_1` (
  `id` int(11) NOT NULL auto_increment,
  `customers_id` int(11) NOT NULL default '0',
  `customers_status` int(11) NOT NULL default '0',
  `customers_firstname` varchar(64) NOT NULL default '',
  `customers_lastname` varchar(64) NOT NULL default '',
  `customers_email_address` text NOT NULL,
  `mail_key` varchar(32) NOT NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `comment` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;