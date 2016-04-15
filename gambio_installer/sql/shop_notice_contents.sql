DROP TABLE IF EXISTS `shop_notice_contents`;
CREATE TABLE IF NOT EXISTS `shop_notice_contents` (
  `shop_notice_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`shop_notice_id`,`language_id`)
) ENGINE=MyISAM;

INSERT INTO `shop_notice_contents` (`shop_notice_id`, `language_id`, `content`) VALUES(1, 1, '');
INSERT INTO `shop_notice_contents` (`shop_notice_id`, `language_id`, `content`) VALUES(1, 2, '');
INSERT INTO `shop_notice_contents` (`shop_notice_id`, `language_id`, `content`) VALUES(2, 1, '');
INSERT INTO `shop_notice_contents` (`shop_notice_id`, `language_id`, `content`) VALUES(2, 2, '');