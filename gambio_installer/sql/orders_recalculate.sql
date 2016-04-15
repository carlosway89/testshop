DROP TABLE IF EXISTS `orders_recalculate`;
CREATE TABLE `orders_recalculate` (
  `orders_recalculate_id` int(11) NOT NULL auto_increment,
  `orders_id` int(11) NOT NULL default '0',
  `n_price` decimal(15,4) NOT NULL default '0.0000',
  `b_price` decimal(15,4) NOT NULL default '0.0000',
  `tax` decimal(19,8) NOT NULL default '0.00000000',
  `tax_rate` decimal(7,4) NOT NULL default '0.0000',
  `class` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`orders_recalculate_id`)
) ENGINE=MyISAM;