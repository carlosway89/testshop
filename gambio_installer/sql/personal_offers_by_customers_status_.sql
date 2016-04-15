DROP TABLE IF EXISTS `personal_offers_by_customers_status_`;
CREATE TABLE `personal_offers_by_customers_status_` (
  `price_id` int(11) NOT NULL auto_increment,
  `products_id` int(11) NOT NULL default '0',
  `quantity` decimal(15,4) default NULL,
  `personal_offer` decimal(15,4) default NULL,
  PRIMARY KEY  (`price_id`)
) ENGINE=MyISAM;