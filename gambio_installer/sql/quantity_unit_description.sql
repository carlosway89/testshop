DROP TABLE IF EXISTS `quantity_unit_description` ;
CREATE  TABLE IF NOT EXISTS `quantity_unit_description` (
  `quantity_unit_id` INT NOT NULL ,
  `language_id` INT NOT NULL ,
  `unit_name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`quantity_unit_id`, `language_id`) )
ENGINE=MyISAM ;