DROP TABLE IF EXISTS `customers_ip`;
CREATE TABLE `customers_ip` (
  `customers_ip_id` int(11) NOT NULL auto_increment,
  `customers_id` int(11) NOT NULL default '0',
  `customers_ip` varchar(15) NOT NULL default '',
  `customers_ip_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `customers_host` varchar(255) NOT NULL default '',
  `customers_advertiser` varchar(30) default NULL,
  `customers_referer_url` varchar(255) default NULL,
  PRIMARY KEY  (`customers_ip_id`),
  KEY `customers_id` (`customers_id`)
) ENGINE=MyISAM;