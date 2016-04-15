DROP TABLE IF EXISTS `newsletter_recipients`;
CREATE TABLE `newsletter_recipients` (
  `mail_id` int(11) NOT NULL auto_increment,
  `customers_email_address` varchar(96) NOT NULL default '',
  `customers_id` int(11) NOT NULL default '0',
  `customers_status` int(5) NOT NULL default '0',
  `customers_firstname` varchar(32) NOT NULL default '',
  `customers_lastname` varchar(32) NOT NULL default '',
  `mail_status` int(1) NOT NULL default '0',
  `mail_key` varchar(32) NOT NULL default '',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`mail_id`)
) ENGINE=MyISAM;