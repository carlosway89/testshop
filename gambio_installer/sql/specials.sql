DROP TABLE IF EXISTS `specials`;
CREATE TABLE `specials` (
  `specials_id` int(11) NOT NULL auto_increment,
  `products_id` int(11) NOT NULL default '0',
  `specials_quantity` decimal(15,4) NOT NULL default '0.0000',
  `specials_new_products_price` decimal(15,4) NOT NULL default '0.0000',
  `specials_date_added` datetime default NULL,
  `specials_last_modified` datetime default NULL,
  `expires_date` datetime default NULL,
  `date_status_change` datetime default NULL,
  `status` int(1) NOT NULL default '1',
  PRIMARY KEY  (`specials_id`),
  UNIQUE KEY `products_id2` (`products_id`),
  KEY `products_id` (`products_id`,`status`,`specials_date_added`),
  KEY `status` (`status`,`expires_date`)
) ENGINE=MyISAM ;

INSERT INTO `specials` (`specials_id`, `products_id`, `specials_quantity`, `specials_new_products_price`, `specials_date_added`, `specials_last_modified`, `expires_date`, `date_status_change`, `status`) VALUES(1, 1, 1857.0000, 12.6050, '2008-08-12 15:40:47', '2008-08-14 19:06:03', '0000-00-00 00:00:00', NULL, 1);