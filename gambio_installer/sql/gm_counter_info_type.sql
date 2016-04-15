DROP TABLE IF EXISTS `gm_counter_info_type`;
CREATE TABLE `gm_counter_info_type` (
  `gm_counter_info_type_id` int(10) NOT NULL auto_increment,
  `gm_counter_info_type_name` varchar(255) default NULL,
  PRIMARY KEY  (`gm_counter_info_type_id`)
) ENGINE=MyISAM ;

INSERT INTO `gm_counter_info_type` (`gm_counter_info_type_id`, `gm_counter_info_type_name`) VALUES(1, 'browser');
INSERT INTO `gm_counter_info_type` (`gm_counter_info_type_id`, `gm_counter_info_type_name`) VALUES(2, 'platform');
INSERT INTO `gm_counter_info_type` (`gm_counter_info_type_id`, `gm_counter_info_type_name`) VALUES(3, 'resolution');
INSERT INTO `gm_counter_info_type` (`gm_counter_info_type_id`, `gm_counter_info_type_name`) VALUES(4, 'color_depth');
INSERT INTO `gm_counter_info_type` (`gm_counter_info_type_id`, `gm_counter_info_type_name`) VALUES(5, 'origin');