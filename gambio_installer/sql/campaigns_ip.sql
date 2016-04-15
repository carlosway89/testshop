DROP TABLE IF EXISTS `campaigns_ip`;
CREATE TABLE `campaigns_ip` (
  `user_ip` varchar(15) NOT NULL default '',
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  `campaign` varchar(32) NOT NULL default ''
) ENGINE=MyISAM;