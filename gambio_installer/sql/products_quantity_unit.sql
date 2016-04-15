DROP TABLE IF EXISTS `products_quantity_unit` ;
CREATE  TABLE IF NOT EXISTS `products_quantity_unit` (
  `products_id` INT NOT NULL ,
  `quantity_unit_id` INT NOT NULL ,
  PRIMARY KEY (`products_id`) )
ENGINE=MyISAM ;