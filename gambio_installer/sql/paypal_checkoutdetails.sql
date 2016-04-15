DROP TABLE IF EXISTS `paypal_checkoutdetails`;
CREATE TABLE IF NOT EXISTS `paypal_checkoutdetails` (
  `orders_id` int(11) NOT NULL,
  `retrievaltime` datetime NOT NULL,
  `checkoutdetails` text NOT NULL,
  PRIMARY KEY (`orders_id`)
) ENGINE=MyISAM;