DROP TABLE IF EXISTS `slider_set` ;
CREATE  TABLE IF NOT EXISTS `slider_set` (
  `slider_set_id` INT NOT NULL AUTO_INCREMENT ,
  `set_name` VARCHAR(255) NULL ,
  `slider_speed` INT NULL ,
  `width` int(10) unsigned NOT NULL default '760',
  `height` int(10) unsigned NOT NULL default '300',
  PRIMARY KEY (`slider_set_id`) )
ENGINE=MyISAM ;