DROP TABLE IF EXISTS `email_attachments`;
CREATE TABLE IF NOT EXISTS `email_attachments` (
  `email_id` INT(11) NOT NULL,
  `path` TEXT NOT NULL,
  `name` VARCHAR(255) DEFAULT '',
  KEY `email_id_index` (`email_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;