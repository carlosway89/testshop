DROP TABLE IF EXISTS `coupon_gv_queue`;
CREATE TABLE `coupon_gv_queue` (
  `unique_id` int(5) NOT NULL auto_increment,
  `customer_id` int(5) NOT NULL default '0',
  `order_id` int(5) NOT NULL default '0',
  `amount` decimal(8,4) NOT NULL default '0.0000',
  `date_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `ipaddr` varchar(32) NOT NULL default '',
  `release_flag` char(1) NOT NULL default 'N',
  PRIMARY KEY  (`unique_id`),
  KEY `uid` (`unique_id`,`customer_id`,`order_id`)
) ENGINE=MyISAM;