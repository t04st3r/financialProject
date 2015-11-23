
-- MySQL dump 10.13  Distrib 5.5.46, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: financial
-- ------------------------------------------------------
-- Server version	5.5.46-0ubuntu0.14.04.2-log

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
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account` (
  `account_number` int(11) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `account_type` int(11) DEFAULT NULL,
  `currency` varchar(45) DEFAULT NULL,
  `balance` double DEFAULT NULL,
  `activation_date` datetime DEFAULT NULL,
  `flag` enum('active','created','suspended','deleted') DEFAULT NULL,
  `card_number` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`account_number`),
  KEY `fk_account_1_idx` (`branch_id`),
  KEY `fk_account_2_idx` (`customer_id`),
  KEY `fk_account_3_idx` (`account_type`),
  KEY `fk_account_4_idx` (`card_number`),
  CONSTRAINT `fk_account_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_account_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_account_3` FOREIGN KEY (`account_type`) REFERENCES `account_type` (`id_type`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_account_4` FOREIGN KEY (`card_number`) REFERENCES `card` (`card_number`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account`
--

LOCK TABLES `account` WRITE;
/*!40000 ALTER TABLE `account` DISABLE KEYS */;
INSERT INTO `account` VALUES (62743048,1,2,2,'$HK',141099.45,'2013-02-20 00:00:00','active','4023600627430483'),(100159745,1,1,1,'$HK',54736.34,'2014-08-15 10:22:14','active','5333171001597455'),(888888188,1,1,2,'$HK',114453.56,'2015-04-12 09:10:22','active','4012888888881881');
/*!40000 ALTER TABLE `account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `account_type`
--

DROP TABLE IF EXISTS `account_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account_type` (
  `id_type` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(45) DEFAULT NULL,
  `description` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_type`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_type`
--

LOCK TABLES `account_type` WRITE;
/*!40000 ALTER TABLE `account_type` DISABLE KEYS */;
INSERT INTO `account_type` VALUES (1,'credit card account',''),(2,'current account',NULL),(3,'check account',NULL);
/*!40000 ALTER TABLE `account_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `atm`
--

DROP TABLE IF EXISTS `atm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `atm` (
  `id_atm` int(11) NOT NULL AUTO_INCREMENT,
  `longitude` double DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `address` varchar(45) DEFAULT NULL,
  `state` enum('available','busy') DEFAULT NULL,
  PRIMARY KEY (`id_atm`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atm`
--

LOCK TABLES `atm` WRITE;
/*!40000 ALTER TABLE `atm` DISABLE KEYS */;
INSERT INTO `atm` VALUES (1,114.175334,22.295816,'Mody Rd','available'),(2,114.187763,22.338041,'198 Junction Rd','busy'),(3,114.174898,22.337249,'80 Tat Chee Ave','available'),(5,114.168087,22.325736,'Prince Edward Station','available'),(6,114.162458,22.330309,'205 Cheung Sha Wan Rd','busy'),(7,114.208902,22.334649,'11 Clear Water Bay Rd','available'),(9,114.226477,22.312716,'Kwun Tong MTR Station','available'),(10,114.160434,22.317339,'MTR Olympic Station','busy');
/*!40000 ALTER TABLE `atm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch`
--

DROP TABLE IF EXISTS `branch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch` (
  `branch_id` int(11) NOT NULL AUTO_INCREMENT,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `address` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `open_time` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch`
--

LOCK TABLES `branch` WRITE;
/*!40000 ALTER TABLE `branch` DISABLE KEYS */;
INSERT INTO `branch` VALUES (1,22.340352,114.17967,'Sir Run Run Shaw Buidings','Kowloon Tong Hong Kong','+852314159265','Monday-Saturday (9:00AM - 17:00PM)'),(2,22.28163,114.158599,'15 Des Voeux Road Central','Alexandra House Hong Kong','+852473829387','Monday-Saturday (9:00AM - 17:00PM)'),(3,22.279962,114.163448,'93 Queensway','Queensway Plaza Hong Kong','+852857476273','Monday-Saturday (9:00AM - 17:00PM)'),(4,22.297222,114.172144,'609 Nathan Rd','Tsim Sha Tsui Hong Kong','+852703782738','Monday-Saturday (9:00AM - 17:00PM)'),(5,22.30548,114.169343,'201 Jordan Rd','Kwun Chung Hong Kong','+852748372984','Monday-Saturday (9:00AM - 17:00PM)'),(6,22.314628,114.170245,'567 Nathan Rd','Ginza Square Hong Kong','+852758478731','Monday-Saturday (9:00AM - 17:00PM)');
/*!40000 ALTER TABLE `branch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `card`
--

DROP TABLE IF EXISTS `card`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `card` (
  `card_number` varchar(16) NOT NULL,
  `card_circuit` enum('visa','mastercard') DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expire_date` date DEFAULT NULL,
  `flag` enum('active','created','suspended','deleted') DEFAULT NULL,
  PRIMARY KEY (`card_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `card`
--

LOCK TABLES `card` WRITE;
/*!40000 ALTER TABLE `card` DISABLE KEYS */;
INSERT INTO `card` VALUES ('4012888888881881','visa','2015-04-22','2019-04-22','active'),('4023600627430483','visa','2013-02-15','2017-02-15','active'),('5333171001597455','mastercard','2014-08-22','2018-08-22','active');
/*!40000 ALTER TABLE `card` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `surname` varchar(45) DEFAULT NULL,
  `gender` varchar(8) DEFAULT NULL,
  `address` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `phone_num` varchar(45) DEFAULT NULL,
  `ID_number` varchar(45) DEFAULT NULL,
  `register_date` datetime DEFAULT NULL,
  `flag` enum('active','created','suspended','deleted') DEFAULT NULL,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `user_id_UNIQUE` (`customer_id`),
  UNIQUE KEY `user_name_UNIQUE` (`user_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer`
--

LOCK TABLES `customer` WRITE;
/*!40000 ALTER TABLE `customer` DISABLE KEYS */;
INSERT INTO `customer` VALUES (1,'raffo','raffaele.tosti@gmail.com','ec21cdeeaba2f63dd49bfde46f79c4e1','Raffaele','Tosti','M','32, renfrew Road','Kowloon Tong Hong Kong','+85252271362','M480117',NULL,NULL),(2,'john','john.smith@example.com','3b047c5d42705562db69a52a03d6922d','John','Smith','M','15, Nathan Road','Hong Kong','+85212345678','M493821',NULL,NULL);
/*!40000 ALTER TABLE `customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matrix`
--

DROP TABLE IF EXISTS `matrix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrix` (
  `customer_id` int(11) NOT NULL,
  `a11` varchar(45) DEFAULT NULL,
  `a12` varchar(45) DEFAULT NULL,
  `a13` varchar(45) DEFAULT NULL,
  `a21` varchar(45) DEFAULT NULL,
  `a22` varchar(45) DEFAULT NULL,
  `a23` varchar(45) DEFAULT NULL,
  `a31` varchar(45) DEFAULT NULL,
  `a32` varchar(45) DEFAULT NULL,
  `a33` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`customer_id`),
  CONSTRAINT `fk_matrix_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matrix`
--

LOCK TABLES `matrix` WRITE;
/*!40000 ALTER TABLE `matrix` DISABLE KEYS */;
INSERT INTO `matrix` VALUES (1,'0cc175b9c0f1b6a831c399e269772661','92eb5ffee6ae2fec3ad71c777531578f','4a8a08f09d37b73795649038408b5f33','c4ca4238a0b923820dcc509a6f75849b','c81e728d9d4c2f636f067f89cc14862c','eccbc87e4b5ce2fe28308fd9f2a7baf3','7fc56270e7a70fa81a5935b72eacbe29','9d5ed678fe57bcca610140957afab571','0d61f8370cad1d412f80b84d143e1257'),(2,'c4ca4238a0b923820dcc509a6f75849b','c4ca4238a0b923820dcc509a6f75849b','c4ca4238a0b923820dcc509a6f75849b','c81e728d9d4c2f636f067f89cc14862c','c81e728d9d4c2f636f067f89cc14862c','c81e728d9d4c2f636f067f89cc14862c','eccbc87e4b5ce2fe28308fd9f2a7baf3','eccbc87e4b5ce2fe28308fd9f2a7baf3','eccbc87e4b5ce2fe28308fd9f2a7baf3');
/*!40000 ALTER TABLE `matrix` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction`
--

DROP TABLE IF EXISTS `transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction` (
  `transaction_code` varchar(45) NOT NULL,
  `operation_time_date` datetime DEFAULT NULL,
  `account` int(11) DEFAULT NULL,
  `transaction_amount` double DEFAULT NULL,
  `flag` enum('executed','aborted') DEFAULT NULL,
  `dest_account` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `message` longtext,
  `error` longtext,
  PRIMARY KEY (`transaction_code`),
  KEY `fk_transaction_2_idx` (`dest_account`),
  KEY `fk_transaction_1` (`account`),
  CONSTRAINT `fk_transaction_1` FOREIGN KEY (`account`) REFERENCES `account` (`account_number`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_transaction_2` FOREIGN KEY (`dest_account`) REFERENCES `account` (`account_number`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction`
--

LOCK TABLES `transaction` WRITE;
/*!40000 ALTER TABLE `transaction` DISABLE KEYS */;
INSERT INTO `transaction` VALUES ('20151117110930','2015-11-17 11:09:30',100159745,1000,'aborted',100159745,'::1','Bla Bla Bla','Transaction Aborted, customer account cannot be equal to beneficiary account'),('20151117111054','2015-11-17 11:10:54',100159745,1000,'aborted',100159745,'::1','Bla Bla Bla','Transaction Aborted, customer account cannot be equal to beneficiary account'),('20151117111306','2015-11-17 11:13:06',100159745,1000,'aborted',100159745,'::1','Bla Bla Bla','Transaction Aborted:<br/>customer account cannot be equal to beneficiary account'),('20151117111634','2015-11-17 11:16:34',100159745,1000,'aborted',100159745,'::1','Bla Bla Bla','Transaction Aborted:<br/>customer account cannot be equal to beneficiary account'),('20151117112350','2015-11-17 11:23:50',100159745,1000,'aborted',100159745,'::1','Bla Bla Bla','Transaction Aborted:<br/>customer account cannot be equal to beneficiary account'),('20151117112803','2015-11-17 11:28:03',100159745,1000,'aborted',100159745,'127.0.0.1','Bla Bla Bla','Transaction Aborted:<br/>customer account cannot be equal to beneficiary account'),('20151117121552','2015-11-17 12:15:52',100159745,100,'aborted',62743048,'127.0.0.1','Bla Bla','unknown beneficiary name'),('20151117121707','2015-11-17 12:17:07',100159745,100,'aborted',62743048,'127.0.0.1','bla bla ','unknown beneficiary name'),('20151117121935','2015-11-17 12:19:35',100159745,100,'aborted',62743048,'127.0.0.1','bla bla ','unknown beneficiary name'),('20151117121957','2015-11-17 12:19:57',100159745,100,'aborted',62743048,'127.0.0.1','bla bla ','unknown beneficiary name'),('20151117122109','2015-11-17 12:21:09',100159745,100,'aborted',62743048,'127.0.0.1','bla bla ','unknown beneficiary name'),('20151117122313','2015-11-17 12:23:13',100159745,100,'aborted',62743048,'127.0.0.1','bla bla ','unknown beneficiary name'),('20151117122751','2015-11-17 12:27:51',100159745,100,'aborted',62743048,'127.0.0.1','bla bla ','unknown beneficiary name'),('20151117123642','2015-11-17 12:36:42',100159745,100,'aborted',62743048,'127.0.0.1','bla bla ','insufficient funds to perform the transaction'),('20151117124206','2015-11-17 12:42:06',100159745,103,'aborted',62743048,'127.0.0.1','','unknown beneficiary name'),('20151117124402','2015-11-17 12:44:02',100159745,898,'aborted',62743048,'127.0.0.1','','unknown beneficiary name'),('20151117124534','2015-11-17 12:45:34',100159745,80436,'aborted',62743048,'127.0.0.1','','insufficient funds to perform the transaction'),('20151117124617','2015-11-17 12:46:17',100159745,80436,'aborted',62743048,'127.0.0.1','','insufficient funds to perform the transaction'),('20151117144214','2015-11-17 14:42:14',100159745,100,'executed',62743048,'127.0.0.1','Elettricity Bill month November 2015',NULL),('20151117144451','2015-11-17 14:44:51',100159745,9999,'executed',62743048,'127.0.0.1','Payment monthly rent fee November 2015',NULL),('20151117144452','2015-11-20 14:44:55',62743048,31415,'executed',100159745,'127.0.0.1','Annual Fee for exmple.com domain',NULL),('20151117203008','2015-11-17 20:30:08',100159745,14000,'executed',888888188,'127.0.0.1','alibaba.com purchase id product 123456',NULL);
/*!40000 ALTER TABLE `transaction` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-11-18 16:34:46
