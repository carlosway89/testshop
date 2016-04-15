DROP TABLE IF EXISTS `ekomi_order_information`;
CREATE TABLE `ekomi_order_information` (
  `orders_id` int(11) NOT NULL,
  `link` varchar(255) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `date_sent` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`orders_id`)
) ENGINE=MyISAM;