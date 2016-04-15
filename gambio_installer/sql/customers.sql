DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `customers_id` int(11) NOT NULL auto_increment,
  `customers_cid` varchar(32) default NULL,
  `customers_vat_id` varchar(20) default NULL,
  `customers_vat_id_status` int(2) NOT NULL default '0',
  `customers_warning` varchar(32) default NULL,
  `customers_status` int(5) NOT NULL default '1',
  `customers_gender` char(1) NOT NULL default '',
  `customers_firstname` varchar(32) NOT NULL default '',
  `customers_lastname` varchar(32) NOT NULL default '',
  `customers_dob` datetime NOT NULL default '0000-00-00 00:00:00',
  `customers_email_address` varchar(96) NOT NULL default '',
  `customers_default_address_id` int(11) NOT NULL default '0',
  `customers_telephone` varchar(32) NOT NULL default '',
  `customers_fax` varchar(32) default NULL,
  `customers_password` varchar(40) NOT NULL default '',
  `customers_newsletter` char(1) default NULL,
  `customers_newsletter_mode` char(1) NOT NULL default '0',
  `member_flag` char(1) NOT NULL default '0',
  `delete_user` char(1) NOT NULL default '1',
  `account_type` int(1) NOT NULL default '0',
  `password_request_key` varchar(32) NOT NULL default '',
  `payment_unallowed` varchar(255) NOT NULL default '',
  `shipping_unallowed` varchar(255) NOT NULL default '',
  `refferers_id` int(5) NOT NULL default '0',
  `customers_date_added` datetime default '0000-00-00 00:00:00',
  `customers_last_modified` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`customers_id`)
) ENGINE=MyISAM;