DROP TABLE IF EXISTS `payment_qenta`;
CREATE TABLE `payment_qenta` (
  `q_TRID` varchar(255) NOT NULL default '',
  `q_DATE` datetime NOT NULL default '0000-00-00 00:00:00',
  `q_QTID` bigint(18) unsigned NOT NULL default '0',
  `q_ORDERDESC` varchar(255) NOT NULL default '',
  `q_STATUS` tinyint(1) NOT NULL default '0',
  `q_ORDERID` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`q_TRID`)
) ENGINE=MyISAM;