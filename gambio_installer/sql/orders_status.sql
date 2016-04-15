DROP TABLE IF EXISTS `orders_status`;
CREATE TABLE `orders_status` (
  `orders_status_id` int(11) NOT NULL default '0',
  `language_id` int(11) NOT NULL default '1',
  `orders_status_name` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`orders_status_id`,`language_id`),
  KEY `idx_orders_status_name` (`orders_status_name`)
) ENGINE=MyISAM;

INSERT INTO `orders_status` (`orders_status_id`, `language_id`, `orders_status_name`) VALUES(0, 1, 'Not validated');
INSERT INTO `orders_status` (`orders_status_id`, `language_id`, `orders_status_name`) VALUES(0, 2, 'Nicht bestätigt');
INSERT INTO `orders_status` (`orders_status_id`, `language_id`, `orders_status_name`) VALUES(1, 1, 'Pending');
INSERT INTO `orders_status` (`orders_status_id`, `language_id`, `orders_status_name`) VALUES(1, 2, 'Offen');
INSERT INTO `orders_status` (`orders_status_id`, `language_id`, `orders_status_name`) VALUES(2, 1, 'Processing');
INSERT INTO `orders_status` (`orders_status_id`, `language_id`, `orders_status_name`) VALUES(2, 2, 'In Bearbeitung');
INSERT INTO `orders_status` (`orders_status_id`, `language_id`, `orders_status_name`) VALUES(3, 1, 'Delivered');
INSERT INTO `orders_status` (`orders_status_id`, `language_id`, `orders_status_name`) VALUES(3, 2, 'Versendet');
INSERT INTO `orders_status` (`orders_status_id`, `language_id`, `orders_status_name`) VALUES(99, 1, 'Canceled');
INSERT INTO `orders_status` (`orders_status_id`, `language_id`, `orders_status_name`) VALUES(99, 2, 'Storniert');
INSERT INTO `orders_status` (`orders_status_id`, `language_id`, `orders_status_name`) VALUES(149, 2, 'Rechnung erstellt');
INSERT INTO `orders_status` (`orders_status_id`, `language_id`, `orders_status_name`) VALUES(149, 1, 'Invoice created');
INSERT INTO `orders_status` (`orders_status_id`, `language_id`, `orders_status_name`) VALUES(160, 1, 'ipayment temporary');
INSERT INTO `orders_status` (`orders_status_id`, `language_id`, `orders_status_name`) VALUES(160, 2, 'ipayment temporaer');
INSERT INTO `orders_status` (`orders_status_id`, `language_id`, `orders_status_name`) VALUES(161, 1, 'ipayment paid');
INSERT INTO `orders_status` (`orders_status_id`, `language_id`, `orders_status_name`) VALUES(161, 2, 'ipayment bezahlt');
INSERT INTO `orders_status` (`orders_status_id`, `language_id`, `orders_status_name`) VALUES(162, 1, 'ipayment error');
INSERT INTO `orders_status` (`orders_status_id`, `language_id`, `orders_status_name`) VALUES(162, 2, 'ipayment Fehler');