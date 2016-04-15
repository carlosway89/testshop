DROP TABLE IF EXISTS `card_blacklist`;
CREATE TABLE `card_blacklist` (
  `blacklist_id` int(5) NOT NULL auto_increment,
  `blacklist_card_number` varchar(20) NOT NULL default '',
  `date_added` datetime default NULL,
  `last_modified` datetime default NULL,
  KEY `blacklist_id` (`blacklist_id`)
) ENGINE=MyISAM;