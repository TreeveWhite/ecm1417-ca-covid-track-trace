/*
This is the DDL for the platform.

It is used to initialise the tables in the database and fill them with the outstanding predefined data
for any users, reports, visits or locations
*/
-- Dropping the tables if exist
DROP TABLE IF EXISTS
    `ecm1417-ca1`.`users`;
DROP TABLE IF EXISTS
    `ecm1417-ca1`.`visits`;
DROP TABLE IF EXISTS
    `ecm1417-ca1`.`locations`;
DROP TABLE IF EXISTS
    `ecm1417-ca1`.`reports`;
-- Initialiing the Tables
CREATE TABLE IF NOT EXISTS `ecm1417-ca1`.`users`(
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` TEXT NOT NULL,
    `password` TEXT NOT NULL,
    `forename` TEXT NOT NULL,
    `surname` TEXT,
    PRIMARY KEY(`id`)
); CREATE TABLE IF NOT EXISTS `ecm1417-ca1`.`visits`(
    `id` INT NOT NULL AUTO_INCREMENT,
    `userId` INT NOT NULL,
    `locationId` INT NOT NULL,
    `date` TEXT NOT NULL,
    `time` TEXT NOT NULL,
    `Duration` TEXT NOT NULL,
    PRIMARY KEY(`id`)
); CREATE TABLE IF NOT EXISTS `ecm1417-ca1`.`locations`(
    `id` INT NOT NULL AUTO_INCREMENT,
    `x` INT NOT NULL,
    `y` INT NOT NULL,
    PRIMARY KEY(`id`)
); CREATE TABLE IF NOT EXISTS `ecm1417-ca1`.`reports`(
    `id` INT NOT NULL AUTO_INCREMENT,
    `userId` INT NOT NULL,
    `date` TEXT NOT NULL,
    `time` TEXT NOT NULL,
    PRIMARY KEY(`id`)
);
