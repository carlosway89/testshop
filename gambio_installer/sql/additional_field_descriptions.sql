DROP TABLE IF EXISTS `additional_field_descriptions`;
CREATE TABLE `additional_field_descriptions` (
  `additional_field_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` text NOT NULL,
  PRIMARY KEY (`additional_field_id`,`language_id`)
) ENGINE=MyISAM;