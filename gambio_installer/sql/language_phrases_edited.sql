DROP TABLE IF EXISTS `language_phrases_edited`;
CREATE TABLE `language_phrases_edited` (
  `language_id` int(11) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `phrase_name` varchar(100) NOT NULL,
  `phrase_text` text NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`language_id`,`section_name`,`phrase_name`),
  KEY `section` (`language_id`,`section_name`)
) ENGINE=MyISAM;