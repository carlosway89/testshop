DROP TABLE IF EXISTS `shipping_status_to_google_availability`;
CREATE TABLE `shipping_status_to_google_availability` (
  `shipping_status_id` int(10) unsigned NOT NULL,
  `google_export_availability_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`shipping_status_id`)
) ENGINE=MyISAM;

INSERT INTO `shipping_status_to_google_availability` (`shipping_status_id`, `google_export_availability_id`) VALUES(1, 1);
INSERT INTO `shipping_status_to_google_availability` (`shipping_status_id`, `google_export_availability_id`) VALUES(2, 1);
INSERT INTO `shipping_status_to_google_availability` (`shipping_status_id`, `google_export_availability_id`) VALUES(3, 3);