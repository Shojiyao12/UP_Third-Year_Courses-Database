-- MySQL dump 10.13  Distrib 8.0.34, for Win64 (x86_64)
--
-- Host: localhost    Database: thirdyear_courses
-- ------------------------------------------------------
-- Server version	8.0.34

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `coursetaken`
--

DROP TABLE IF EXISTS `coursetaken`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coursetaken` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Student_ID` int NOT NULL,
  `Subject_ID` int NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `coursetaken_ibfk_1_idx` (`Student_ID`),
  KEY `coursetaken_ibfk_2_idx` (`Subject_ID`),
  CONSTRAINT `coursetaken_ibfk_1` FOREIGN KEY (`Student_ID`) REFERENCES `students` (`Student_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=287 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coursetaken`
--

LOCK TABLES `coursetaken` WRITE;
/*!40000 ALTER TABLE `coursetaken` DISABLE KEYS */;
INSERT INTO `coursetaken` VALUES (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,1,5),(6,1,6),(7,1,7),(8,1,8),(9,1,9),(10,1,10),(11,1,11),(12,1,12),(13,1,0),(14,1,0),(15,1,0),(16,2,1),(17,2,2),(18,2,3),(19,2,4),(20,2,5),(21,2,6),(22,2,7),(23,2,8),(24,2,9),(25,2,10),(26,2,11),(27,2,0),(28,2,0),(29,2,0),(30,2,0);
/*!40000 ALTER TABLE `coursetaken` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `Student_ID` int NOT NULL,
  `Name` varchar(45) NOT NULL,
  `Gender` varchar(45) NOT NULL,
  `Email_Address` varchar(45) NOT NULL,
  PRIMARY KEY (`Student_ID`),
  UNIQUE KEY `Email_Address_UNIQUE` (`Email_Address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,'Shaw Jie Yao','Male','sayao@up.edu.ph'),(2,'Charles Roy Phillips JR.','Male','crphillips@up.edu.ph');
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects` (
  `Subject_ID` int NOT NULL,
  `Name_of_Subject` varchar(255) DEFAULT NULL,
  `Type_of_Class` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Subject_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,'CMSC 121','Lec'),(2,'CMSC 121','Lab'),(3,'CMSC 127','Lec'),(4,'CMSC 127','Lab'),(5,'CMSC 131','Lec'),(6,'CMSC 131','Lab'),(7,'CMSC 141','Lec'),(8,'CMSC 141','Lab'),(9,'STAT 105','Lec'),(10,'STAT 105','Lab'),(11,'Soc Sci 5','Lec'),(12,'Physics 71','Lec'),(13,'Science 11','Lec'),(14,'CMSC 128','Lec');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects_schedule`
--

DROP TABLE IF EXISTS `subjects_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects_schedule` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Subject_ID` int DEFAULT NULL,
  `Schedule_ID` int DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Subject_ID` (`Subject_ID`),
  KEY `Schedule_ID` (`Schedule_ID`),
  CONSTRAINT `subjects_schedule_ibfk_1` FOREIGN KEY (`Subject_ID`) REFERENCES `subjects` (`Subject_ID`),
  CONSTRAINT `subjects_schedule_ibfk_2` FOREIGN KEY (`Schedule_ID`) REFERENCES `timecategory` (`Schedule_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects_schedule`
--

LOCK TABLES `subjects_schedule` WRITE;
/*!40000 ALTER TABLE `subjects_schedule` DISABLE KEYS */;
INSERT INTO `subjects_schedule` VALUES (1,1,2),(2,2,2),(3,1,5),(4,2,5),(5,3,1),(6,4,1),(7,3,4),(8,4,4),(9,5,1),(10,6,1),(11,5,4),(12,6,4),(13,7,2),(14,8,2),(15,7,5),(16,8,5),(17,9,2),(18,10,2),(19,9,5),(20,10,5),(21,11,2),(22,11,5),(23,12,2),(24,13,2),(25,14,2);
/*!40000 ALTER TABLE `subjects_schedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teachers` (
  `Teacher_ID` int NOT NULL,
  `Name` varchar(45) NOT NULL,
  `Gender` varchar(45) NOT NULL,
  `Email_Address` varchar(45) NOT NULL,
  `Password` varchar(20) DEFAULT NULL,
  `ADMIN` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`Teacher_ID`),
  UNIQUE KEY `Email_Address_UNIQUE` (`Email_Address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teachers`
--

LOCK TABLES `teachers` WRITE;
/*!40000 ALTER TABLE `teachers` DISABLE KEYS */;
INSERT INTO `teachers` VALUES (0,'ADMIN','Male','sayao@up.edu.ph','shawjie12345',1),(1,'Allan Amistoso','Male','anamistoso@up.edu.ph','allan12345',0),(2,'Bea Santiago','Female','bdsantiago@up.edu.ph','bea12345',0),(3,'Rosabella Montes','Female','rbmontes@up.edu.ph','rosabella12345',0),(4,'Claire Bacong','Female','ccbacong@up.edu.ph','claire12345',0),(5,'Bogart Jr.','Male','bogartAK@gmail.com','bogart12345',0);
/*!40000 ALTER TABLE `teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teaches`
--

DROP TABLE IF EXISTS `teaches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teaches` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Teacher_ID` int DEFAULT NULL,
  `Subject_ID` int DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Teacher_ID` (`Teacher_ID`),
  KEY `Subject_ID` (`Subject_ID`),
  CONSTRAINT `teaches_ibfk_1` FOREIGN KEY (`Teacher_ID`) REFERENCES `teachers` (`Teacher_ID`),
  CONSTRAINT `teaches_ibfk_2` FOREIGN KEY (`Subject_ID`) REFERENCES `subjects` (`Subject_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teaches`
--

LOCK TABLES `teaches` WRITE;
/*!40000 ALTER TABLE `teaches` DISABLE KEYS */;
INSERT INTO `teaches` VALUES (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,2,5),(6,2,6),(7,1,7),(8,1,8),(9,3,9),(10,3,10),(11,5,12),(12,4,11),(13,5,13);
/*!40000 ALTER TABLE `teaches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timecategory`
--

DROP TABLE IF EXISTS `timecategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `timecategory` (
  `Schedule_ID` int NOT NULL,
  `Day` varchar(45) NOT NULL,
  `Time_Start` varchar(45) NOT NULL,
  `Time_End` varchar(45) NOT NULL,
  PRIMARY KEY (`Schedule_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timecategory`
--

LOCK TABLES `timecategory` WRITE;
/*!40000 ALTER TABLE `timecategory` DISABLE KEYS */;
INSERT INTO `timecategory` VALUES (1,'Monday','12:00pm','5:30pm'),(2,'Tuesday','9:00am','6:30pm'),(4,'Thursday','12:00pm','5:30pm'),(5,'Friday','9:00am','6:30pm');
/*!40000 ALTER TABLE `timecategory` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-01-09 13:12:20
