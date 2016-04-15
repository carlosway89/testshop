DROP TABLE IF EXISTS `version_history`;
CREATE TABLE `version_history` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(255) CHARACTER SET utf8 NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `type` enum('master_update', 'service_pack', 'update') NOT NULL DEFAULT 'update',
  `revision` int(11) NOT NULL,
  `is_full_version` tinyint(1) NOT NULL DEFAULT '0',
  `installation_date` datetime NOT NULL,
  `php_version` varchar(255) CHARACTER SET utf8 NOT NULL,
  `mysql_version` varchar(255) CHARACTER SET utf8 NOT NULL,
  `installed` TINYINT NOT NULL DEFAULT '1' COMMENT 'Signalisiert, ob ein Versionseintrag wirklich installiert wurde oder durch die Versionsauswahl angelegt wurde.',
  PRIMARY KEY (`history_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;