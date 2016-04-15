DROP TABLE IF EXISTS `brickfox_orders`;
CREATE TABLE `brickfox_orders` (
  `brickfox_orders_id` INT(11) NOT NULL,
  `extern_orders_id` VARCHAR(255) NOT NULL,
  `intern_orders_id` INT(11) NOT NULL,
  PRIMARY KEY  (`brickfox_orders_id`)
) ENGINE=MyISAM;