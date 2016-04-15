DROP TABLE IF EXISTS `slider_image_description` ;
CREATE  TABLE IF NOT EXISTS `slider_image_description` (
  `slider_image_id` INT NOT NULL ,
  `language_id` INT NOT NULL ,
  `image_title` VARCHAR(255) NULL ,
  `image_alt_text` VARCHAR(255) NULL ,
  PRIMARY KEY (`language_id`, `slider_image_id`) )
ENGINE=MyISAM ;