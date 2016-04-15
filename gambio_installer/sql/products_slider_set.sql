DROP TABLE IF EXISTS `products_slider_set` ;
CREATE  TABLE IF NOT EXISTS`products_slider_set` (
  `products_slider_set_id` INT NOT NULL AUTO_INCREMENT ,
  `slider_set_id` INT NOT NULL ,
  PRIMARY KEY (`products_slider_set_id`) ,
  INDEX `fk_products_slider_set_slider_set1` (`slider_set_id` ASC)
)
ENGINE = MyISAM;