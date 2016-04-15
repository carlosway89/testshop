ALTER TABLE `admin_access` ADD `affiliprint_module` INT( 1 ) NOT NULL DEFAULT '0';
UPDATE `admin_access` SET `affiliprint_module` = '1' WHERE `admin_access`.`customers_id` = '1';
