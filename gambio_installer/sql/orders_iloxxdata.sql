DROP TABLE IF EXISTS `orders_iloxxdata`;
CREATE TABLE IF NOT EXISTS `orders_iloxxdata` (
	`orders_id` INT UNSIGNED NOT NULL ,
	`parcelnumber` VARCHAR(255) NULL DEFAULT NULL ,
	`service` VARCHAR(255) NOT NULL ,
	`weight` DECIMAL(15,4) NOT NULL DEFAULT '0',
	`shipdate` DATE NULL DEFAULT NULL ,
	PRIMARY KEY (`orders_id`),
	INDEX (`parcelnumber`)
) ENGINE=MyISAM;