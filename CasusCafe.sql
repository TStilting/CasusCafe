-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema casuscafe
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema casuscafe
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `casuscafe` DEFAULT CHARACTER SET utf8 ;
USE `casuscafe` ;

-- -----------------------------------------------------
-- Table `casuscafe`.`Band`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `casuscafe`.`Band` ;

CREATE TABLE IF NOT EXISTS `casuscafe`.`Band` (
  `bandId` INT NOT NULL AUTO_INCREMENT,
  `bandNaam` VARCHAR(32) NOT NULL,
  `genre` VARCHAR(45) NOT NULL,
  `prijs` DOUBLE NOT NULL,
  `herkomst` VARCHAR(45) NOT NULL,
  `omschrijving` VARCHAR(250) NOT NULL,
  PRIMARY KEY (`bandId`),
  UNIQUE INDEX `BandId_UNIQUE` (`bandId` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `casuscafe`.`Event`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `casuscafe`.`Event` ;

CREATE TABLE IF NOT EXISTS `casuscafe`.`Event` (
  `eventId` INT NOT NULL AUTO_INCREMENT,
  `naam` VARCHAR(45) NOT NULL,
  `beginstijd` DATETIME NOT NULL,
  `eindtijd` DATETIME NOT NULL,
  `entreePrijs` DOUBLE NOT NULL,
  `omschrijving` VARCHAR(1500) NOT NULL,
  UNIQUE INDEX `eventId_UNIQUE` (`eventId` ASC),
  PRIMARY KEY (`eventId`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `casuscafe`.`Act`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `casuscafe`.`Act` ;

CREATE TABLE IF NOT EXISTS `casuscafe`.`Act` (
  `Event_eventId` INT NOT NULL,
  `Bands_bandId` INT NOT NULL,
  `actId` INT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`actId`, `Event_eventId`, `Bands_bandId`),
  INDEX `fk_Dagen_has_Bands_Bands1_idx` (`Bands_bandId` ASC),
  INDEX `fk_Event_has_Band_Event1_idx` (`Event_eventId` ASC),
  CONSTRAINT `fk_Dagen_has_Bands_Bands1`
    FOREIGN KEY (`Bands_bandId`)
    REFERENCES `casuscafe`.`Band` (`bandId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Event_has_Band_Event1`
    FOREIGN KEY (`Event_eventId`)
    REFERENCES `casuscafe`.`Event` (`eventId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `casuscafe`.`Login`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `casuscafe`.`Login` ;

CREATE TABLE IF NOT EXISTS `casuscafe`.`Login` (
  `username` VARCHAR(20) NOT NULL,
  `wachtwoord` VARCHAR(45) NOT NULL,
  `userId` INT NOT NULL AUTO_INCREMENT,
  `rol` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`userId`),
  UNIQUE INDEX `username_UNIQUE` (`username` ASC),
  UNIQUE INDEX `userId_UNIQUE` (`userId` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `casuscafe`.`Kaartbestelling`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `casuscafe`.`Kaartbestelling` ;

CREATE TABLE IF NOT EXISTS `casuscafe`.`Kaartbestelling` (
  `bestellingNr` INT NOT NULL AUTO_INCREMENT,
  `naam` VARCHAR(32) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `Event_eventId` INT NOT NULL,
  `kaartAantal` INT NOT NULL,
  PRIMARY KEY (`bestellingNr`, `Event_eventId`),
  INDEX `fk_Kaartbestelling_Event1_idx` (`Event_eventId` ASC),
  UNIQUE INDEX `bestellingNr_UNIQUE` (`bestellingNr` ASC),
  CONSTRAINT `fk_Kaartbestelling_Event1`
    FOREIGN KEY (`Event_eventId`)
    REFERENCES `casuscafe`.`Event` (`eventId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `casuscafe`.`Lineup`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `casuscafe`.`Lineup` ;

CREATE TABLE IF NOT EXISTS `casuscafe`.`Lineup` (
  `BandlidId` INT NOT NULL AUTO_INCREMENT,
  `naam` VARCHAR(45) NOT NULL,
  `tussenvoegsel` VARCHAR(16) NULL,
  `achternaam` VARCHAR(45) NOT NULL,
  `Band_bandId` INT NOT NULL,
  `omschrijving` VARCHAR(1500) NOT NULL,
  `foto` BLOB NOT NULL,
  PRIMARY KEY (`BandlidId`, `Band_bandId`),
  UNIQUE INDEX `BandlidId_UNIQUE` (`BandlidId` ASC),
  INDEX `fk_Lineup_Band1_idx` (`Band_bandId` ASC),
  CONSTRAINT `fk_Lineup_Band1`
    FOREIGN KEY (`Band_bandId`)
    REFERENCES `casuscafe`.`Band` (`bandId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
