DROP TABLE IF EXISTS `counter_history`;
CREATE TABLE `counter_history` (
  `month` char(8) default NULL,
  `counter` int(12) default NULL
) ENGINE=MyISAM;