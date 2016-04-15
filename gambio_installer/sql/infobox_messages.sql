DROP TABLE IF EXISTS `infobox_messages`;
CREATE TABLE IF NOT EXISTS `infobox_messages` (
  `infobox_messages_id` int(10) unsigned NOT NULL auto_increment,
  `source` varchar(255) NOT NULL,
  `identifier` varchar(255) NOT NULL,
  `status` enum('new','read','hidden','deleted') NOT NULL default 'new',
  `type` enum('info','warning','success') NOT NULL default 'info',
  `visibility` enum('alwayson','hideable','removable') NOT NULL default 'hideable',
  `button_link` varchar(255) NOT NULL,
  `customers_id` int(10) unsigned NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY  (`infobox_messages_id`),
  UNIQUE KEY `identifier` (`identifier`,`customers_id`)
) ENGINE=MyISAM;