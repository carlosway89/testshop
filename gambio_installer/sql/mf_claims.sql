DROP TABLE IF EXISTS `mf_claims`;
CREATE TABLE `mf_claims` (
  `orderId` int(10) unsigned NOT NULL,
  `fileNumber` int(10) unsigned NOT NULL,
  `firstname` VARCHAR(100) NOT NULL,
  `lastname` VARCHAR(100) NOT NULL,
  `transmissionDate` int(10) unsigned NOT NULL,
  `statusCode` smallint(5) unsigned NOT NULL,
  `statusText` text NOT NULL,
  `statusDetails` text NOT NULL,
  `lastChange` datetime NOT NULL,
  UNIQUE KEY `orderId` (`orderId`,`fileNumber`)
) ENGINE=MyISAM;