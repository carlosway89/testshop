DROP TABLE IF EXISTS `customers_status_history`;
CREATE TABLE `customers_status_history` (
  `customers_status_history_id` int(11) NOT NULL auto_increment,
  `customers_id` int(11) NOT NULL default '0',
  `new_value` int(5) NOT NULL default '0',
  `old_value` int(5) default NULL,
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `customer_notified` int(1) default '0',
  PRIMARY KEY  (`customers_status_history_id`)
) ENGINE=MyISAM;