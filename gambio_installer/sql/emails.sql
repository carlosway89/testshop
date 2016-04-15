DROP TABLE IF EXISTS `emails`;
CREATE TABLE `emails` (
  `email_id` INT(11) NOT NULL AUTO_INCREMENT,
  `subject` VARCHAR(256) NOT NULL,
  `content_plain` TEXT DEFAULT '',
  `content_html` LONGTEXT DEFAULT '',
  `is_pending` TINYINT(4) DEFAULT 1,
  `creation_date` DATETIME NOT NULL,
  `sent_date` DATETIME DEFAULT NULL,
  PRIMARY KEY (`email_id`),
  KEY `email_id_index` (`email_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;