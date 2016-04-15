DROP TABLE IF EXISTS `field_replace_jobs`;
CREATE TABLE IF NOT EXISTS `field_replace_jobs` (
  `field_replace_job_id` int(11) NOT NULL AUTO_INCREMENT,
  `waiting_ticket_id` int(11) NOT NULL,
  `table_name` varchar(255) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `old_value` text NOT NULL,
  `new_value` text NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  PRIMARY KEY (`field_replace_job_id`)
) ENGINE=MyISAM;