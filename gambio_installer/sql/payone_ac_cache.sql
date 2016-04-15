DROP TABLE IF EXISTS `payone_ac_cache`;
CREATE TABLE `payone_ac_cache` (
  `address_hash` varchar(32) NOT NULL,
  `address_book_id` int(11) NOT NULL,
  `received` datetime NOT NULL,
  `secstatus` int(11) NOT NULL,
  `status` varchar(7) NOT NULL,
  `personstatus` varchar(4) NOT NULL,
  `street` varchar(255) NOT NULL,
  `streetname` varchar(255) NOT NULL,
  `streetnumber` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `errorcode` varchar(255) NOT NULL,
  `errormessage` text NOT NULL,
  `customermessage` varchar(255) NOT NULL,
  PRIMARY KEY (`address_hash`),
  KEY `address_book_id` (`address_book_id`)
) ENGINE=MyISAM;