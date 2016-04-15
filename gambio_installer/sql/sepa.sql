DROP TABLE IF EXISTS `sepa`;
CREATE TABLE `sepa` (
  `orders_id` int(11) NOT NULL default '0',
  `sepa_owner` varchar(64),
  `sepa_iban` varchar(35),
  `sepa_bic` varchar(15),
  `sepa_bankname` varchar(255),
  `sepa_status` int(11),
  `sepa_prz` char(2),
  `sepa_fax` char(2),
  PRIMARY KEY (`orders_id`)
) ENGINE=MyISAM;