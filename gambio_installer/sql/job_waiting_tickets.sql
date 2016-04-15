DROP TABLE IF EXISTS `job_waiting_tickets` ;
CREATE  TABLE IF NOT EXISTS `job_waiting_tickets` (
  `waiting_ticket_id` INT NOT NULL AUTO_INCREMENT ,
  `subject` VARCHAR(45) NOT NULL ,
  `callback` VARCHAR(45) NOT NULL ,
  `due_date` DATETIME NOT NULL ,
  `done_date` DATETIME NOT NULL ,
  PRIMARY KEY (`waiting_ticket_id`) )
ENGINE = MyISAM;