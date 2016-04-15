DROP TABLE IF EXISTS `newsletters_history`;
CREATE TABLE `newsletters_history` (
  `news_hist_id` int(11) NOT NULL default '0',
  `news_hist_cs` int(11) NOT NULL default '0',
  `news_hist_cs_date_sent` date default NULL,
  PRIMARY KEY  (`news_hist_id`)
) ENGINE=MyISAM;
