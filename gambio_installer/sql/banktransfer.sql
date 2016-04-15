DROP TABLE IF EXISTS `banktransfer`;
CREATE TABLE `banktransfer` (
  `orders_id` int(11) NOT NULL default '0',
  `banktransfer_owner` varchar(64) default NULL,
  `banktransfer_number` varchar(24) default NULL,
  `banktransfer_bankname` varchar(255) default NULL,
  `banktransfer_blz` varchar(8) default NULL,
  `banktransfer_status` int(11) default NULL,
  `banktransfer_prz` char(2) default NULL,
  `banktransfer_fax` char(2) default NULL,
  KEY `orders_id` (`orders_id`)
) ENGINE=MyISAM;