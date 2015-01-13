-- MySQL dump 10.15  Distrib 10.0.15-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: eabdb
-- ------------------------------------------------------
-- Server version	10.0.15-MariaDB-1~trusty-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `arrival_time_table`
--

DROP TABLE IF EXISTS `arrival_time_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arrival_time_table` (
  `arrival_time_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) NOT NULL COMMENT 'References the role_id in the `role_table`',
  `program_id` bigint(20) NOT NULL COMMENT 'References the program_id in the `program_table`',
  `arrival_time` time DEFAULT NULL,
  PRIMARY KEY (`arrival_time_id`),
  KEY `FK_arrival_time_role_id` (`role_id`),
  KEY `FK_arrival_time_table_program_id` (`program_id`),
  CONSTRAINT `FK_arrival_time_role_id` FOREIGN KEY (`role_id`) REFERENCES `role_table` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_arrival_time_table_program_id` FOREIGN KEY (`program_id`) REFERENCES `program_table` (`program_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arrival_time_table`
--

LOCK TABLES `arrival_time_table` WRITE;
/*!40000 ALTER TABLE `arrival_time_table` DISABLE KEYS */;
INSERT INTO `arrival_time_table` VALUES (1,1,1,'13:30:00'),(2,1,2,'16:30:00'),(3,2,1,'13:00:00'),(4,3,1,'12:30:00'),(5,4,1,'12:30:00'),(6,4,2,'15:30:00'),(7,5,1,'12:30:00'),(8,6,1,'12:00:00'),(9,7,1,'12:30:00'),(10,8,1,'12:30:00'),(11,9,1,'12:30:00'),(12,12,1,'12:30:00'),(13,13,2,'15:00:00'),(14,14,2,'15:30:00'),(15,15,1,'13:00:00'),(16,15,2,'16:00:00'),(17,18,1,'13:00:00'),(18,19,1,'12:30:00'),(19,23,1,'12:30:00'),(20,23,2,'15:30:00'),(21,10,3,'13:10:00'),(22,11,3,'13:10:00'),(23,15,3,'13:10:00'),(24,16,3,'13:10:00'),(25,17,3,'13:10:00'),(26,19,3,'13:10:00'),(27,20,3,'13:10:00'),(28,21,3,'13:10:00'),(29,22,3,'13:10:00');
/*!40000 ALTER TABLE `arrival_time_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gender_table`
--

DROP TABLE IF EXISTS `gender_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gender_table` (
  `gender_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `gender` varchar(15) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`gender_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gender_table`
--

LOCK TABLES `gender_table` WRITE;
/*!40000 ALTER TABLE `gender_table` DISABLE KEYS */;
INSERT INTO `gender_table` VALUES (1,'Female'),(2,'Male'),(3,'Other');
/*!40000 ALTER TABLE `gender_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `level_table`
--

DROP TABLE IF EXISTS `level_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `level_table` (
  `level_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `level_name` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`level_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `level_table`
--

LOCK TABLES `level_table` WRITE;
/*!40000 ALTER TABLE `level_table` DISABLE KEYS */;
INSERT INTO `level_table` VALUES (1,'Attending Physician'),(2,'Fellow'),(3,'Resident'),(4,'Resident - Intern Year'),(5,'Pharmacist'),(6,'MS1'),(7,'MS2'),(8,'MS3'),(9,'MS4'),(10,'P1'),(11,'P2'),(12,'P3'),(13,'P4'),(14,'Physical Therapist'),(15,'PT1'),(16,'PT2'),(17,'PT3'),(18,'Optometrist'),(19,'O1'),(20,'O2'),(21,'O3'),(22,'O4'),(23,'Dentist'),(24,'D1'),(25,'D2'),(26,'D3'),(27,'D4'),(28,'Public Health Professional'),(29,'PH1'),(30,'PH2'),(31,'Undergrad');
/*!40000 ALTER TABLE `level_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location_table`
--

DROP TABLE IF EXISTS `location_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location_table` (
  `location_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `location_name` varchar(25) COLLATE utf8_bin DEFAULT NULL,
  `address` varchar(75) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location_table`
--

LOCK TABLES `location_table` WRITE;
/*!40000 ALTER TABLE `location_table` DISABLE KEYS */;
INSERT INTO `location_table` VALUES (1,'Church of the Reconciler','112 14th St N, Birmingham, AL (Church of the Reconciler)'),(2,'M-Power Ministries','4022 4th Ave S, Birmingham, AL'),(3,'Screening locations',NULL),(4,'Test','Test');
/*!40000 ALTER TABLE `location_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_relation_table`
--

DROP TABLE IF EXISTS `login_relation_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_relation_table` (
  `login_relation_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `person_id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`login_relation_id`),
  KEY `FK_login_relation_person_id` (`person_id`),
  KEY `FK_login_relation_table_user_id` (`user_id`),
  CONSTRAINT `FK_login_relation_person_id` FOREIGN KEY (`person_id`) REFERENCES `person_table` (`person_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_login_relation_table_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_relation_table`
--

LOCK TABLES `login_relation_table` WRITE;
/*!40000 ALTER TABLE `login_relation_table` DISABLE KEYS */;
INSERT INTO `login_relation_table` VALUES (2,2,2),(5,5,5);
/*!40000 ALTER TABLE `login_relation_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_table`
--

DROP TABLE IF EXISTS `person_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person_table` (
  `person_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `fname` varchar(25) COLLATE utf8_bin DEFAULT NULL,
  `mname` varchar(25) COLLATE utf8_bin DEFAULT NULL,
  `lname` varchar(25) COLLATE utf8_bin DEFAULT NULL,
  `suffname` varchar(25) COLLATE utf8_bin DEFAULT NULL,
  `gender_id` bigint(20) NOT NULL DEFAULT '3',
  `dob` date DEFAULT NULL,
  `phone_number` varchar(20) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`person_id`),
  KEY `FK_person_gender_id` (`gender_id`),
  CONSTRAINT `FK_person_gender_id` FOREIGN KEY (`gender_id`) REFERENCES `gender_table` (`gender_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_table`
--

LOCK TABLES `person_table` WRITE;
/*!40000 ALTER TABLE `person_table` DISABLE KEYS */;
INSERT INTO `person_table` VALUES (2,'Tim','','Kennell','Jr.',2,'1990-07-06','(678) 997-3500'),(5,'Tim','','Kennell','',1,'2015-01-01','(123) 456-7890');
/*!40000 ALTER TABLE `person_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_training_relation_table`
--

DROP TABLE IF EXISTS `person_training_relation_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person_training_relation_table` (
  `user_training_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `login_relation_id` bigint(20) NOT NULL COMMENT 'References the login_relation_id in `login_relation_table`',
  `training_id` bigint(20) NOT NULL COMMENT 'References the training_id in the `training_table`',
  PRIMARY KEY (`user_training_id`),
  KEY `FK_user_training_training_id` (`training_id`),
  KEY `FK_person_training_relation_table_login_relation_id` (`login_relation_id`),
  CONSTRAINT `FK_person_training_relation_table` FOREIGN KEY (`login_relation_id`) REFERENCES `login_relation_table` (`login_relation_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_person_training_relation_table_login_relation_id` FOREIGN KEY (`login_relation_id`) REFERENCES `person_table` (`person_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_training_relation_table`
--

LOCK TABLES `person_training_relation_table` WRITE;
/*!40000 ALTER TABLE `person_training_relation_table` DISABLE KEYS */;
INSERT INTO `person_training_relation_table` VALUES (4,2,1);
/*!40000 ALTER TABLE `person_training_relation_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `position_relation_table`
--

DROP TABLE IF EXISTS `position_relation_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `position_relation_table` (
  `position_relation_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `login_relation_id` bigint(20) NOT NULL COMMENT 'References the login_relation_id in the `login_relation_table`',
  `position_id` bigint(20) NOT NULL COMMENT 'References the `position_id` column in the `position_table`',
  PRIMARY KEY (`position_relation_id`),
  KEY `FK_position_relation_position_id` (`position_id`),
  KEY `FK_position_relation_table_login_relation_id` (`login_relation_id`),
  CONSTRAINT `FK_position_relation_position_id` FOREIGN KEY (`position_id`) REFERENCES `position_table` (`position_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_position_relation_table_login_relation_id` FOREIGN KEY (`login_relation_id`) REFERENCES `login_relation_table` (`login_relation_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `position_relation_table`
--

LOCK TABLES `position_relation_table` WRITE;
/*!40000 ALTER TABLE `position_relation_table` DISABLE KEYS */;
INSERT INTO `position_relation_table` VALUES (2,2,1),(5,5,2);
/*!40000 ALTER TABLE `position_relation_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `position_table`
--

DROP TABLE IF EXISTS `position_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `position_table` (
  `position_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `position_name` varchar(25) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`position_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `position_table`
--

LOCK TABLES `position_table` WRITE;
/*!40000 ALTER TABLE `position_table` DISABLE KEYS */;
INSERT INTO `position_table` VALUES (1,'Officer'),(2,'Volunteer');
/*!40000 ALTER TABLE `position_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `program_relation_table`
--

DROP TABLE IF EXISTS `program_relation_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_relation_table` (
  `program_relation_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `program_id` bigint(20) NOT NULL COMMENT 'References the `program_id` column in the `program_table`',
  `location_id` bigint(20) NOT NULL COMMENT 'References the `location_id` column in the `location_table`',
  `date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  PRIMARY KEY (`program_relation_id`),
  KEY `FK_program_relation_location_id` (`location_id`),
  KEY `FK_program_relation_program_id` (`program_id`),
  CONSTRAINT `FK_program_relation_location_id` FOREIGN KEY (`location_id`) REFERENCES `location_table` (`location_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_program_relation_program_id` FOREIGN KEY (`program_id`) REFERENCES `program_table` (`program_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `program_relation_table`
--

LOCK TABLES `program_relation_table` WRITE;
/*!40000 ALTER TABLE `program_relation_table` DISABLE KEYS */;
INSERT INTO `program_relation_table` VALUES (14,1,1,'2015-01-14','13:00:00','18:00:00'),(15,1,1,'2015-01-21','13:00:00','18:00:00'),(16,1,1,'2015-01-28','13:00:00','18:00:00'),(17,1,1,'2015-02-01','13:00:00','18:00:00'),(18,1,1,'2015-01-11','13:00:00','18:00:00'),(19,1,1,'2015-01-18','13:00:00','18:00:00'),(20,1,1,'2015-01-25','13:00:00','18:00:00'),(21,3,4,'2015-01-01','13:10:00','13:10:00');
/*!40000 ALTER TABLE `program_relation_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `program_table`
--

DROP TABLE IF EXISTS `program_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_table` (
  `program_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `program_name` varchar(25) COLLATE utf8_bin DEFAULT NULL,
  `program_type_id` bigint(20) NOT NULL COMMENT 'References the program_type_id in the `program_type_table`',
  `description_field` text COLLATE utf8_bin,
  PRIMARY KEY (`program_id`),
  KEY `FK_program_table_program_type_id` (`program_type_id`),
  CONSTRAINT `FK_program_table_program_type_id` FOREIGN KEY (`program_type_id`) REFERENCES `program_type_table` (`program_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `program_table`
--

LOCK TABLES `program_table` WRITE;
/*!40000 ALTER TABLE `program_table` DISABLE KEYS */;
INSERT INTO `program_table` VALUES (1,'EAB Clinic',1,'EAB\'s long-term clinic'),(2,'M-Power Clinic',2,'EAB\'s acute care partner'),(3,'EAB Screening',3,NULL);
/*!40000 ALTER TABLE `program_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `program_type_table`
--

DROP TABLE IF EXISTS `program_type_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_type_table` (
  `program_type_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `program_type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`program_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `program_type_table`
--

LOCK TABLES `program_type_table` WRITE;
/*!40000 ALTER TABLE `program_type_table` DISABLE KEYS */;
INSERT INTO `program_type_table` VALUES (1,'EAB Clinic'),(2,'M-Power Clinic'),(3,'EAB Screening');
/*!40000 ALTER TABLE `program_type_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_level_relation_table`
--

DROP TABLE IF EXISTS `role_level_relation_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_level_relation_table` (
  `role_level_relation_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) NOT NULL,
  `level_id` bigint(20) NOT NULL,
  PRIMARY KEY (`role_level_relation_id`),
  KEY `FK_role_level_relation_role_id` (`level_id`),
  KEY `FK_role_level_relation_level_id` (`role_id`),
  CONSTRAINT `FK_role_level_relation_level_id` FOREIGN KEY (`role_id`) REFERENCES `role_table` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_role_level_relation_role_id` FOREIGN KEY (`level_id`) REFERENCES `level_table` (`level_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_level_relation_table`
--

LOCK TABLES `role_level_relation_table` WRITE;
/*!40000 ALTER TABLE `role_level_relation_table` DISABLE KEYS */;
INSERT INTO `role_level_relation_table` VALUES (1,1,1),(2,1,2),(3,2,3),(4,2,4),(5,3,5),(6,4,6),(7,4,7),(8,4,8),(9,4,9),(10,5,10),(11,5,11),(12,5,12),(13,5,13),(14,6,6),(15,6,7),(16,6,8),(17,6,9),(18,7,6),(19,7,7),(20,7,8),(21,7,9),(22,8,6),(23,8,7),(24,8,8),(25,8,9),(26,8,10),(27,8,11),(28,8,12),(29,8,13),(30,8,31),(31,9,6),(32,9,7),(33,9,8),(34,9,9),(35,10,6),(36,10,7),(37,10,8),(38,10,9),(39,10,31),(40,11,6),(41,11,7),(42,11,8),(43,11,9),(44,11,31),(45,12,6),(46,12,7),(47,12,8),(48,12,9),(49,13,6),(50,13,7),(51,13,8),(52,13,9),(53,14,6),(54,14,7),(55,14,8),(56,14,9),(57,15,6),(58,15,7),(59,15,8),(60,15,9),(61,16,6),(62,16,7),(63,16,8),(64,16,9),(65,16,31),(66,17,6),(67,17,7),(68,17,8),(69,17,9),(70,18,31),(71,19,14),(72,19,15),(73,19,16),(74,19,17),(75,20,18),(76,20,19),(77,20,20),(78,20,21),(79,20,22),(80,21,23),(81,21,24),(82,21,25),(83,21,26),(84,21,27),(85,22,28),(86,22,29),(87,22,30),(88,23,6),(89,23,31);
/*!40000 ALTER TABLE `role_level_relation_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_position_relation_table`
--

DROP TABLE IF EXISTS `role_position_relation_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_position_relation_table` (
  `role_position_relation_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) NOT NULL COMMENT 'References the `role_id` column in the `role`',
  `position_id` bigint(20) NOT NULL COMMENT 'References the `position_id` in the `position`',
  PRIMARY KEY (`role_position_relation_id`),
  KEY `FK_role_position_relation_role_id` (`role_id`),
  KEY `FK_role_position_relation_position_id` (`position_id`),
  CONSTRAINT `FK_role_position_relation_position_id` FOREIGN KEY (`position_id`) REFERENCES `position_table` (`position_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_role_position_relation_role_id` FOREIGN KEY (`role_id`) REFERENCES `role_table` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_position_relation_table`
--

LOCK TABLES `role_position_relation_table` WRITE;
/*!40000 ALTER TABLE `role_position_relation_table` DISABLE KEYS */;
INSERT INTO `role_position_relation_table` VALUES (1,1,2),(2,2,2),(3,3,2),(4,4,1),(5,4,2),(6,5,2),(7,6,1),(8,7,1),(9,7,2),(10,8,1),(11,8,2),(12,9,1),(13,10,1),(14,10,2),(15,11,1),(16,11,2),(17,12,1),(18,13,1),(19,14,1),(20,15,1),(21,15,2),(22,16,1),(23,16,2),(24,17,1),(25,17,2),(26,18,2),(27,19,2),(28,20,2),(29,21,2),(30,22,2),(31,23,2);
/*!40000 ALTER TABLE `role_position_relation_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_program_type_relation_table`
--

DROP TABLE IF EXISTS `role_program_type_relation_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_program_type_relation_table` (
  `role_program_relation_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) NOT NULL COMMENT 'References the `role_id` column in the `role`',
  `program_type_id` bigint(20) NOT NULL,
  PRIMARY KEY (`role_program_relation_id`),
  KEY `FK_role_program_type_relation_table_role_id` (`role_id`),
  KEY `FK_role_program_type_relation_table_program_type_id` (`program_type_id`),
  CONSTRAINT `FK_role_program_type_relation_table_program_type_id` FOREIGN KEY (`program_type_id`) REFERENCES `program_type_table` (`program_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_role_program_type_relation_table_role_id` FOREIGN KEY (`role_id`) REFERENCES `role_table` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_program_type_relation_table`
--

LOCK TABLES `role_program_type_relation_table` WRITE;
/*!40000 ALTER TABLE `role_program_type_relation_table` DISABLE KEYS */;
INSERT INTO `role_program_type_relation_table` VALUES (1,1,1),(2,1,2),(3,2,1),(4,3,1),(5,4,1),(6,4,2),(7,5,1),(8,6,1),(9,7,1),(10,8,1),(11,9,1),(12,10,3),(13,11,3),(14,12,1),(15,13,2),(16,14,2),(17,15,1),(18,15,2),(19,15,3),(20,16,3),(21,17,3),(22,18,1),(23,19,1),(24,19,3),(25,20,3),(26,21,3),(27,22,3),(28,23,1),(29,23,2);
/*!40000 ALTER TABLE `role_program_type_relation_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_table`
--

DROP TABLE IF EXISTS `role_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_table` (
  `role_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(33) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_table`
--

LOCK TABLES `role_table` WRITE;
/*!40000 ALTER TABLE `role_table` DISABLE KEYS */;
INSERT INTO `role_table` VALUES (1,'Attending'),(2,'Resident'),(3,'Pharmacist'),(4,'Medical H&P'),(5,'Pharmacy H&P'),(6,'Clinic Leader'),(7,'Dispensary Leader'),(8,'Dispensary Assistant'),(9,'Check In/Check Out Officer'),(10,'Check In'),(11,'Check Out'),(12,'Records Officer'),(13,'Front Officer'),(14,'Back Officer'),(15,'Health Educator'),(16,'Vitals'),(17,'HIV Screener'),(18,'Social Work'),(19,'Physical Therapy'),(20,'Optometry'),(21,'Dentistry'),(22,'Public Health'),(23,'Shadowing Student');
/*!40000 ALTER TABLE `role_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_training_relation_table`
--

DROP TABLE IF EXISTS `role_training_relation_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_training_relation_table` (
  `role_training_relation_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) NOT NULL COMMENT 'References the `role_id` column in the `role`',
  `training_id` bigint(20) NOT NULL COMMENT 'References the `training_id` column in the `training`',
  PRIMARY KEY (`role_training_relation_id`),
  KEY `FK_role_training_table_role_id` (`role_id`),
  KEY `FK_role_training_table_training_id` (`training_id`),
  CONSTRAINT `FK_role_training_table_role_id` FOREIGN KEY (`role_id`) REFERENCES `role_table` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_role_training_table_training_id` FOREIGN KEY (`training_id`) REFERENCES `training_table` (`training_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_training_relation_table`
--

LOCK TABLES `role_training_relation_table` WRITE;
/*!40000 ALTER TABLE `role_training_relation_table` DISABLE KEYS */;
INSERT INTO `role_training_relation_table` VALUES (1,4,1),(2,6,1),(3,7,1),(4,8,1),(5,9,1),(6,10,1),(7,11,1),(8,12,1),(9,13,1),(10,14,1),(11,15,1),(12,15,2),(13,16,1),(14,17,1),(15,17,3);
/*!40000 ALTER TABLE `role_training_relation_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_relation_table`
--

DROP TABLE IF EXISTS `school_relation_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `school_relation_table` (
  `school_relation_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `login_relation_id` bigint(20) NOT NULL COMMENT 'References the login_relation_id in the `login_relation_table`',
  `school_id` bigint(20) NOT NULL COMMENT 'References school_id in the `school_table`',
  `level_id` bigint(20) NOT NULL COMMENT 'References level_id in the `level_table`',
  PRIMARY KEY (`school_relation_id`),
  KEY `FK_school_relation_table_school_id` (`school_id`),
  KEY `FK_school_relation_table_level_id` (`level_id`),
  KEY `FK_school_relation_table_login_relation_id` (`login_relation_id`),
  CONSTRAINT `FK_school_relation_table_level_id` FOREIGN KEY (`level_id`) REFERENCES `level_table` (`level_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_school_relation_table_login_relation_id` FOREIGN KEY (`login_relation_id`) REFERENCES `login_relation_table` (`login_relation_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_school_relation_table_school_id` FOREIGN KEY (`school_id`) REFERENCES `school_table` (`school_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_relation_table`
--

LOCK TABLES `school_relation_table` WRITE;
/*!40000 ALTER TABLE `school_relation_table` DISABLE KEYS */;
INSERT INTO `school_relation_table` VALUES (3,2,1,7),(6,5,1,1);
/*!40000 ALTER TABLE `school_relation_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_table`
--

DROP TABLE IF EXISTS `school_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `school_table` (
  `school_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `school_name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_table`
--

LOCK TABLES `school_table` WRITE;
/*!40000 ALTER TABLE `school_table` DISABLE KEYS */;
INSERT INTO `school_table` VALUES (1,'UAB School of Medicine'),(2,'UAB School of Dentistry'),(3,'UAB School of Physical Therapy'),(4,'UAB School of Optometry'),(5,'UAB School of Public Health'),(6,'UAB Undergrad'),(7,'Harrison School of Pharmacy'),(8,'McWhorter School of Pharmacy');
/*!40000 ALTER TABLE `school_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `signup_table`
--

DROP TABLE IF EXISTS `signup_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `signup_table` (
  `signup_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `login_relation_id` bigint(20) NOT NULL,
  `program_relation_id` bigint(20) NOT NULL,
  `role_id` bigint(20) NOT NULL,
  PRIMARY KEY (`signup_id`),
  KEY `FK_signup_table_login_relation_id` (`login_relation_id`),
  KEY `FK_signup_table_role_id` (`role_id`),
  KEY `FK_signup_table_program_relation_id` (`program_relation_id`),
  CONSTRAINT `FK_signup_table_login_relation_id` FOREIGN KEY (`login_relation_id`) REFERENCES `login_relation_table` (`login_relation_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_signup_table_program_relation_id` FOREIGN KEY (`program_relation_id`) REFERENCES `program_relation_table` (`program_relation_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_signup_table_role_id` FOREIGN KEY (`role_id`) REFERENCES `role_table` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `signup_table`
--

LOCK TABLES `signup_table` WRITE;
/*!40000 ALTER TABLE `signup_table` DISABLE KEYS */;
INSERT INTO `signup_table` VALUES (1,2,18,4),(2,2,14,4),(3,2,19,4);
/*!40000 ALTER TABLE `signup_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_table`
--

DROP TABLE IF EXISTS `training_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `training_table` (
  `training_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `training_name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`training_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_table`
--

LOCK TABLES `training_table` WRITE;
/*!40000 ALTER TABLE `training_table` DISABLE KEYS */;
INSERT INTO `training_table` VALUES (1,'General'),(2,'Education - Diabetes & HTN'),(3,'HIV OraQuick'),(4,'Phlebotomy'),(5,'I-STAT');
/*!40000 ALTER TABLE `training_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `undergrad_relation_table`
--

DROP TABLE IF EXISTS `undergrad_relation_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `undergrad_relation_table` (
  `undergrad_relation_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `level_id` bigint(20) NOT NULL,
  `undergrad_type` varchar(25) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`undergrad_relation_id`),
  KEY `FK_undergrad_relation_table_level_id` (`level_id`),
  CONSTRAINT `FK_undergrad_relation_table_level_id` FOREIGN KEY (`level_id`) REFERENCES `level_table` (`level_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `undergrad_relation_table`
--

LOCK TABLES `undergrad_relation_table` WRITE;
/*!40000 ALTER TABLE `undergrad_relation_table` DISABLE KEYS */;
INSERT INTO `undergrad_relation_table` VALUES (1,31,'Social Work Student');
/*!40000 ALTER TABLE `undergrad_relation_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s name, unique',
  `user_password_hash` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s password in salted and hashed format',
  `user_email` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s email, unique',
  `user_password_reset_hash` char(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'user''s password reset code',
  `user_password_reset_timestamp` bigint(20) DEFAULT NULL COMMENT 'timestamp of the password reset request',
  `user_failed_logins` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'user''s failed login attemps',
  `user_last_failed_login` int(10) DEFAULT NULL COMMENT 'unix timestamp of last failed login attempt',
  `user_registration_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_rememberme_token` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'user''s remember-me cookie token',
  `user_password_change` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 or 1 boolean holding random password change status',
  `admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 or 1 boolean determining user''s admin status',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'tikenn','$2y$10$isxi0.N8p.cuqPJU7i2dfO.Yqu4evuRyPRpY3OucNGIf.QRM.9Wdm','tikenn@uab.com',NULL,NULL,0,NULL,'0000-00-00 00:00:00',NULL,1,1),(5,'tikenn7792','$2y$10$hMNkU1f7SdlDN95Dq.z/OuOZYCfM1MPApBpa8jFZG1DUOIs8Cnj/O','tikenn7792@gmail.com',NULL,NULL,0,NULL,'2015-01-09 10:17:08',NULL,1,0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-01-09 18:41:39
