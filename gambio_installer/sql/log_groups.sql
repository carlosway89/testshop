DROP TABLE IF EXISTS `log_groups`;
CREATE TABLE IF NOT EXISTS `log_groups` (
  `log_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`log_group_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `log_groups` (`log_group_id`, `name`) VALUES(1, 'error_handler');
INSERT INTO `log_groups` (`log_group_id`, `name`) VALUES(2, 'security');
INSERT INTO `log_groups` (`log_group_id`, `name`) VALUES(3, 'payment');
INSERT INTO `log_groups` (`log_group_id`, `name`) VALUES(4, 'shipping');
INSERT INTO `log_groups` (`log_group_id`, `name`) VALUES(5, 'widgets');