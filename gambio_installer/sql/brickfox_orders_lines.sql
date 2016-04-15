DROP TABLE IF EXISTS `brickfox_orders_lines`;
CREATE TABLE `brickfox_orders_lines` (
  `brickfox_orders_lines_id` INT(11) NOT NULL,
  `orders_products_id` INT(11) NOT NULL,
  PRIMARY KEY  (`brickfox_orders_lines_id`)
) ENGINE=MyISAM;