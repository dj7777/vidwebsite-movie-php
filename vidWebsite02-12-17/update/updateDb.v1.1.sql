SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- version 1.1
ALTER TABLE `configurations` 
CHARACTER SET = utf8 , COLLATE = utf8_general_ci ,
ADD COLUMN `webSiteTitle` VARCHAR(45) NOT NULL DEFAULT 'Movief4u' AFTER `version`,
ADD COLUMN `language` VARCHAR(6) NOT NULL DEFAULT 'en' AFTER `webSiteTitle`,
ADD COLUMN `contactEmail` VARCHAR(45) NOT NULL AFTER `language`,
CHANGE COLUMN `modified` `modified` DATETIME NOT NULL DEFAULT now() AFTER `contactEmail`,
CHANGE COLUMN `created` `created` DATETIME NOT NULL DEFAULT now() ;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;