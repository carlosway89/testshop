DROP TABLE IF EXISTS `content_slider_set` ;
CREATE  TABLE IF NOT EXISTS`content_slider_set` (
  `content_slider_set_id` INT NOT NULL AUTO_INCREMENT ,
  `slider_set_id` INT NOT NULL ,
  PRIMARY KEY (`content_slider_set_id`) ,
  INDEX `fk_content_slider_set_slider_set1` (`slider_set_id` ASC)
)
ENGINE = MyISAM;