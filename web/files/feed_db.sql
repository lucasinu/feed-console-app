
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema feed
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `feed` DEFAULT CHARACTER SET utf8 ;
USE `feed` ;

-- -----------------------------------------------------
-- Table `feed`.`category`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `feed`.`category` (
  `idcategory` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NOT NULL,
  PRIMARY KEY (`idcategory`),
  UNIQUE INDEX `idcategory_UNIQUE` (`idcategory` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `feed`.`brand`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `feed`.`brand` (
  `idbrand` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NOT NULL,
  PRIMARY KEY (`idbrand`),
  UNIQUE INDEX `idbrand_UNIQUE` (`idbrand` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `feed`.`product`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `feed`.`product` (
  `idproduct` INT NOT NULL AUTO_INCREMENT,
  `external_id` INT NOT NULL,
  `sku` VARCHAR(45) NULL,
  `name` VARCHAR(128) NULL,
  `price` FLOAT NULL,
  `link` VARCHAR(255) NULL,
  `image` VARCHAR(255) NULL,
  `rating` FLOAT NULL,
  `count` FLOAT NULL,
  `caffeine_type` VARCHAR(45) NULL,
  `flavored` TINYINT NULL,
  `seasonal` TINYINT NULL,
  `in_stock` TINYINT NULL,
  `facebook` TINYINT NULL,
  `is_k_cup` TINYINT NULL,
  `short_description` TEXT NULL,
  `description` TEXT NULL,
  `brand_idbrand` INT NULL,
  `category_idcategory` INT NULL,
  PRIMARY KEY (`idproduct`),
  UNIQUE INDEX `idproduct_UNIQUE` (`idproduct` ASC),
  INDEX `fk_product_brand_idx` (`brand_idbrand` ASC),
  INDEX `fk_product_category1_idx` (`category_idcategory` ASC),
  CONSTRAINT `fk_product_brand`
    FOREIGN KEY (`brand_idbrand`)
    REFERENCES `feed`.`brand` (`idbrand`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_category1`
    FOREIGN KEY (`category_idcategory`)
    REFERENCES `feed`.`category` (`idcategory`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `feed`.`log`
-- -----------------------------------------------------
CREATE TABLE `log` (
  `idlog` int NOT NULL AUTO_INCREMENT,
  `level` varchar(45) DEFAULT NULL,
  `category` varchar(45) DEFAULT NULL,
  `log_time` datetime DEFAULT NULL,
  `note` text,
  `action_name` varchar(45) DEFAULT NULL,
  `instance` varchar(45) DEFAULT NULL,
  `id_instance` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idlog`),
  UNIQUE KEY `idloginfo_UNIQUE` (`idlog`)
) ENGINE=InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
