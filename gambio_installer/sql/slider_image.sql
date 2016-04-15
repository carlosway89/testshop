DROP TABLE IF EXISTS `slider_image` ;
CREATE  TABLE IF NOT EXISTS `slider_image` (
  `slider_image_id` INT NOT NULL AUTO_INCREMENT ,
  `slider_set_id` INT NOT NULL ,
  `sort_order` INT NULL ,
  `image_file` VARCHAR(45) NULL ,
  `image_preview_file` VARCHAR(45) NULL ,
  `link_url` VARCHAR(255) NULL ,
  `link_window_target` VARCHAR(45) NULL ,
  PRIMARY KEY (`slider_image_id`) )
ENGINE=MyISAM ;