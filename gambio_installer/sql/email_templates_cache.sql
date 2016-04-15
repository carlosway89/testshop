DROP TABLE IF EXISTS `email_templates_cache`;
CREATE TABLE `email_templates_cache` (
  `name` varchar(32) NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `type` enum('txt','html') NOT NULL DEFAULT 'txt',
  `content` mediumtext NOT NULL,
  `source` varchar(255) NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`name`,`language_id`,`type`)
) ENGINE=MyISAM;