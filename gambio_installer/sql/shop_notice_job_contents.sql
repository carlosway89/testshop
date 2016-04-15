DROP TABLE IF EXISTS `shop_notice_job_contents`;
CREATE TABLE IF NOT EXISTS `shop_notice_job_contents` (
  `shop_notice_job_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `topbar_content` text NOT NULL,
  `popup_content` text NOT NULL,
  PRIMARY KEY (`shop_notice_job_id`,`language_id`)
) ENGINE=MyISAM;