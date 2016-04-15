DROP TABLE IF EXISTS `orders_products_quantity_units` ;
CREATE TABLE `orders_products_quantity_units` (
`orders_products_id` INT NOT NULL ,
`quantity_unit_id` INT NOT NULL ,
`unit_name` VARCHAR( 45 ) NOT NULL ,
PRIMARY KEY ( `orders_products_id` )
) ENGINE=MyISAM ;