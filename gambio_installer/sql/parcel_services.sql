DROP TABLE IF EXISTS `parcel_services`;
CREATE TABLE IF NOT EXISTS `parcel_services` (
  `parcel_service_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `default` TINYINT NOT NULL,
  PRIMARY KEY (`parcel_service_id`))
ENGINE = MyISAM;

INSERT INTO `parcel_services` (`parcel_service_id`, `name`, `default`) VALUES (1, 'DHL', 1);
INSERT INTO `parcel_services` (`parcel_service_id`, `name`, `default`) VALUES (2, 'DPD', 0);
INSERT INTO `parcel_services` (`parcel_service_id`, `name`, `default`) VALUES (3, 'GLS', 0);
INSERT INTO `parcel_services` (`parcel_service_id`, `name`, `default`) VALUES (4, 'Hermes', 0);
INSERT INTO `parcel_services` (`parcel_service_id`, `name`, `default`) VALUES (5, 'UPS', 0);
INSERT INTO `parcel_services` (`parcel_service_id`, `name`, `default`) VALUES (6, 'Shipcloud', 0);
