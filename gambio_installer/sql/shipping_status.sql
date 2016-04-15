DROP TABLE IF EXISTS `shipping_status`;
CREATE TABLE `shipping_status` (
  `shipping_status_id` int(11) NOT NULL default '0',
  `language_id` int(11) NOT NULL default '1',
  `shipping_status_name` varchar(32) NOT NULL default '',
  `shipping_status_image` varchar(32) NOT NULL default '',
  `number_of_days` INT(10) NOT NULL default '0',
  `shipping_quantity` DECIMAL(15,4) NULL DEFAULT NULL,
  `info_link_active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`shipping_status_id`,`language_id`),
  KEY `idx_shipping_status_name` (`shipping_status_name`),
  KEY `language_id` (`language_id`)
) ENGINE=MyISAM;

INSERT INTO `shipping_status` (`shipping_status_id`, `language_id`, `shipping_status_name`, `shipping_status_image`, `number_of_days`, `shipping_quantity`) VALUES(1, 1, 'ca. 3-4 days', 'green.png', 4, 999999);
INSERT INTO `shipping_status` (`shipping_status_id`, `language_id`, `shipping_status_name`, `shipping_status_image`, `number_of_days`, `shipping_quantity`) VALUES(1, 2, 'ca. 3-4 Tage', 'green.png', 4, 999999);
INSERT INTO `shipping_status` (`shipping_status_id`, `language_id`, `shipping_status_name`, `shipping_status_image`, `number_of_days`, `shipping_quantity`) VALUES(2, 1, '1 Week', 'orange.png', 7, 0);
INSERT INTO `shipping_status` (`shipping_status_id`, `language_id`, `shipping_status_name`, `shipping_status_image`, `number_of_days`, `shipping_quantity`) VALUES(2, 2, 'ca. 1 Woche', 'orange.png', 7, 0);
INSERT INTO `shipping_status` (`shipping_status_id`, `language_id`, `shipping_status_name`, `shipping_status_image`, `number_of_days`, `shipping_quantity`) VALUES(3, 1, '2 Weeks', 'red.png', 14, 0);
INSERT INTO `shipping_status` (`shipping_status_id`, `language_id`, `shipping_status_name`, `shipping_status_image`, `number_of_days`, `shipping_quantity`) VALUES(3, 2, 'ca. 2 Wochen', 'red.png', 14, 0);