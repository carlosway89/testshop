DROP TABLE IF EXISTS `email_contacts`;
CREATE TABLE IF NOT EXISTS `email_contacts` (
  `email_id` INT(11) NOT NULL,
  `email_address` VARCHAR(128) NOT NULL, 
  `contact_type` VARCHAR(32) NOT NULL,
  `contact_name` VARCHAR(128) DEFAULT '',
  KEY `email_id_index` (`email_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;