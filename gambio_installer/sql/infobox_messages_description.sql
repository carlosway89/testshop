DROP TABLE IF EXISTS `infobox_messages_description`;
CREATE TABLE IF NOT EXISTS `infobox_messages_description` (
  `infobox_messages_id` int(10) unsigned NOT NULL,
  `languages_id` int(10) unsigned NOT NULL,
  `headline` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `button_label` varchar(64) NOT NULL,
  PRIMARY KEY  (`infobox_messages_id`,`languages_id`)
) ENGINE=MyISAM;