DROP TABLE IF EXISTS `protectedshops`;
CREATE TABLE `protectedshops` (
  `ps_id` int(11) NOT NULL AUTO_INCREMENT,
  `document_name` varchar(255) NOT NULL,
  `document_date` datetime NOT NULL,
  `md5` varchar(32) NOT NULL,
  `document_type` varchar(32) NOT NULL,
  `content` mediumtext NOT NULL,
  PRIMARY KEY (`ps_id`),
  KEY `document_name` (`document_name`,`document_date`,`document_type`)
) ENGINE=MyISAM;