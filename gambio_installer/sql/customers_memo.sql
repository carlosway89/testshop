DROP TABLE IF EXISTS `customers_memo`;
CREATE TABLE `customers_memo` (
  `memo_id` int(11) NOT NULL auto_increment,
  `customers_id` int(11) NOT NULL default '0',
  `memo_date` date NOT NULL default '0000-00-00',
  `memo_title` text NOT NULL,
  `memo_text` text NOT NULL,
  `poster_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`memo_id`)
) ENGINE=MyISAM;