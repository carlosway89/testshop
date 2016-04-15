DROP TABLE IF EXISTS `shop_notice_jobs`;
CREATE TABLE IF NOT EXISTS `shop_notice_jobs` (
  `shop_notice_job_id` int(11) NOT NULL AUTO_INCREMENT,
  `waiting_ticket_id` int(11) NOT NULL,
  `shop_active` tinyint(4) NOT NULL,
  `shop_offline_content` text NOT NULL,
  `topbar_active` int(11) NOT NULL,
  `topbar_color` varchar(45) NOT NULL,
  `topbar_mode` varchar(45) NOT NULL,
  `popup_active` tinyint(4) NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  PRIMARY KEY (`shop_notice_job_id`)
) ENGINE=MyISAM;