DROP TABLE IF EXISTS `language_phrases_cache`;
CREATE TABLE `language_phrases_cache` (
  `language_id` int(11) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `phrase_name` varchar(100) NOT NULL,
  `phrase_text` text NOT NULL,
  `source` varchar(255) NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`language_id`,`section_name`,`phrase_name`),
  KEY `search` (`source`,`language_id`)
) ENGINE=MyISAM;