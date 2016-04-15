DROP TABLE IF EXISTS `gm_counter_visits`;
CREATE TABLE `gm_counter_visits` (
  `gm_counter_id` int(10) NOT NULL auto_increment,
  `gm_counter_visits_total` int(10) NOT NULL default '0',
  `gm_counter_date` datetime NOT NULL,
  PRIMARY KEY  (`gm_counter_id`),
  KEY `gm_counter_date` (`gm_counter_date`)
) ENGINE=MyISAM ;

INSERT INTO `gm_counter_visits` (`gm_counter_id`, `gm_counter_visits_total`, `gm_counter_date`) VALUES(1, 1, '2008-08-25 00:00:00');