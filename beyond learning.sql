-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: beyond_learning
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=298 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (22,6,'Logged out','::1','2025-09-21 04:01:40'),(23,7,'Registered new account','::1','2025-09-21 04:10:53'),(24,7,'Logged in','::1','2025-09-21 04:11:02'),(25,7,'Logged out','::1','2025-09-21 04:11:12'),(32,8,'Registered new account','::1','2025-09-23 03:52:56'),(33,8,'Logged in','::1','2025-09-23 03:53:08'),(34,8,'Logged out','::1','2025-09-23 03:53:36'),(45,5,'Logged in','::1','2025-09-30 06:56:08'),(46,5,'Logged out','::1','2025-09-30 06:56:15'),(47,5,'Logged in','::1','2025-09-30 06:56:21'),(48,5,'Logged out','::1','2025-09-30 06:56:25'),(49,8,'Failed login attempt (wrong password)','::1','2025-09-30 06:56:30'),(65,9,'Logged in','::1','2025-10-09 06:59:39'),(66,9,'Logged out','::1','2025-10-09 07:27:54'),(82,5,'Failed login attempt (wrong password)','::1','2025-10-09 09:43:07'),(83,5,'Failed login attempt (wrong password)','::1','2025-10-09 09:43:13'),(90,10,'Logged in','::1','2025-10-09 09:51:06'),(91,10,'Enrolled in class: Mathematics Fundamentals',NULL,'2025-10-09 09:52:03'),(92,10,'Favorited class: Mathematics Fundamentals',NULL,'2025-10-09 09:52:08'),(93,10,'Unfavorited class: Mathematics Fundamentals',NULL,'2025-10-09 09:52:12'),(94,10,'Logged out','::1','2025-10-09 09:52:23'),(104,5,'Failed login attempt (wrong password)','::1','2025-10-09 11:50:17'),(105,5,'Failed login attempt (wrong password)','::1','2025-10-09 11:50:23'),(106,7,'Failed login attempt (wrong password)','::1','2025-10-09 11:50:37'),(107,5,'Failed login attempt (wrong password)','::1','2025-10-09 11:50:42'),(108,5,'Failed login attempt (wrong password)','::1','2025-10-09 11:50:48'),(109,11,'Logged in','::1','2025-10-09 11:55:07'),(110,11,'Logged out','::1','2025-10-09 11:57:16'),(111,5,'Failed login attempt (wrong password)','::1','2025-10-09 11:57:25'),(112,5,'Logged in','::1','2025-10-09 11:57:29'),(113,5,'Logged out','::1','2025-10-09 11:58:11'),(115,4,'Failed login attempt (wrong password)','::1','2025-10-21 06:54:35'),(116,12,'Logged in','::1','2025-10-21 06:54:53'),(117,12,'Logged out','::1','2025-10-21 06:58:16'),(118,12,'Logged in','::1','2025-10-21 06:58:32'),(119,12,'Enrolled in class: Mathematics Fundamentals',NULL,'2025-10-21 06:59:21'),(120,12,'Logged out','::1','2025-10-21 06:59:52'),(158,13,'Logged in','::1','2025-11-17 03:06:27'),(179,15,'Admin logged in','::1','2025-11-19 07:40:08'),(181,15,'Admin logged out','::1','2025-11-19 07:46:34'),(186,15,'Admin logged in','::1','2025-11-19 07:50:25'),(187,15,'Admin logged out','::1','2025-11-19 07:50:45'),(188,15,'Admin logged in','::1','2025-11-19 07:50:56'),(191,15,'Added new class: ','::1','2025-11-19 08:46:14'),(192,15,'Added new class: Readings in the Philippine History','::1','2025-11-19 08:47:45'),(193,15,'Updated account password','::1','2025-11-19 08:49:20'),(194,15,'Edited class: Readings in the Philippine History → Readings','::1','2025-11-19 08:51:11'),(195,15,'Deleted class: Readings','::1','2025-11-19 08:51:20'),(197,15,'Admin logged out','::1','2025-11-19 08:53:03'),(198,15,'Admin logged in','::1','2025-11-19 08:56:06'),(199,15,'Admin logged out','::1','2025-11-19 08:56:10'),(203,15,'Admin logged in','::1','2025-11-19 23:26:54'),(204,15,'Added new class: CCNA','::1','2025-11-19 23:38:04'),(205,15,'Edited class: Advanced Database Systems → Advanced Database Systems','::1','2025-11-19 23:42:41'),(206,15,'Edited class: Advanced Database Systems → Advanced Database Systems','::1','2025-11-19 23:48:31'),(212,15,'Edited class: CCNA → CCNA','::1','2025-11-20 00:06:19'),(213,15,'Added new class: Readings in the Philippine History','::1','2025-11-20 00:24:53'),(215,15,'Admin logged out','::1','2025-11-20 02:38:18'),(229,15,'Admin logged in','::1','2025-11-20 02:44:30'),(231,15,'Admin logged out','::1','2025-11-20 03:02:13'),(232,15,'Admin logged in','::1','2025-11-20 03:02:29'),(234,15,'Added new class: Life and Works of Rizal','::1','2025-11-20 03:18:53'),(235,15,'Added new class: Barbie','::1','2025-11-20 03:19:48'),(236,15,'Admin logged out','::1','2025-11-20 03:31:48'),(243,15,'Admin logged in','::1','2025-11-20 03:39:26'),(245,15,'Admin logged out','::1','2025-11-20 03:41:21'),(261,15,'Admin logged in','::1','2025-11-20 03:49:26'),(262,15,'Admin logged out','::1','2025-11-20 03:49:39'),(276,15,'Admin logged in','::1','2025-11-20 05:50:39'),(278,15,'Deleted user ID 1','::1','2025-11-20 05:51:47'),(281,20,'Logged in','::1','2025-11-20 05:57:26'),(282,20,'Enrolled in class: Advanced Database Systems (Ref: ENR-20251120-543)','::1','2025-11-20 05:57:43'),(283,20,'Dropped class: Advanced Database Systems',NULL,'2025-11-20 05:57:47'),(284,20,'Enrolled in class: Advanced Database Systems (Ref: ENR-20251120-930)','::1','2025-11-20 05:57:55'),(285,15,'Admin logged in','::1','2025-11-20 05:58:23'),(286,15,'Added new class: Programming 101','::1','2025-11-20 06:15:52'),(287,20,'Enrolled in class: Programming 101 (Ref: ENR-20251120-522)','::1','2025-11-20 06:16:04'),(288,15,'Admin logged out','::1','2025-11-20 06:16:08'),(289,20,'Logged in','::1','2025-11-20 08:06:46'),(290,20,'Archived class: Advanced Database Systems',NULL,'2025-11-20 08:07:42'),(291,20,'User logged out','::1','2025-11-20 08:07:55'),(292,15,'Admin logged in','::1','2025-11-20 08:08:07'),(293,15,'Admin logged out','::1','2025-11-20 08:08:20'),(294,20,'Failed login attempt (wrong password)','::1','2025-11-20 08:08:28'),(295,20,'Failed login attempt (wrong password)','::1','2025-11-20 08:08:33'),(296,20,'Logged in','::1','2025-11-20 08:08:39'),(297,20,'User logged out','::1','2025-11-20 08:08:52');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_requests`
--

DROP TABLE IF EXISTS `class_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_requests` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `class_title` varchar(100) NOT NULL,
  `schedule` datetime DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `student_id` int(11) NOT NULL,
  PRIMARY KEY (`request_id`),
  KEY `fk_user` (`user_id`),
  CONSTRAINT `class_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_requests`
--

LOCK TABLES `class_requests` WRITE;
/*!40000 ALTER TABLE `class_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `class_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `schedule` varchar(50) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `tutor_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `schedule_datetime` datetime DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  PRIMARY KEY (`class_id`),
  KEY `tutor_id` (`tutor_id`),
  CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
INSERT INTO `classes` VALUES (4,'Advanced Database Systems','Advanced Database Systems',NULL,NULL,15,200.00,'2025-11-20 13:00:00',60),(7,'CCNA','CCNA / CISCO',NULL,NULL,15,150.00,'2025-11-20 14:00:00',120),(8,'Readings in the Philippine History','Readings in the Philippine History',NULL,NULL,15,200.00,'2025-11-20 08:24:00',60),(9,'Life and Works of Rizal','Noli and El Fili',NULL,NULL,15,500.00,'2025-11-20 12:18:00',180),(10,'Barbie','Barbie Life in the Dreamhouse',NULL,NULL,15,5000.00,'2025-11-20 11:20:00',100),(11,'Programming 101','Introduction to Programming',NULL,NULL,15,500.00,'2025-11-20 16:16:00',200);
/*!40000 ALTER TABLE `classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `completed_lessons`
--

DROP TABLE IF EXISTS `completed_lessons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `completed_lessons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_lesson` (`user_id`,`class_id`,`lesson_id`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `completed_lessons_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `completed_lessons_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `completed_lessons`
--

LOCK TABLES `completed_lessons` WRITE;
/*!40000 ALTER TABLE `completed_lessons` DISABLE KEYS */;
/*!40000 ALTER TABLE `completed_lessons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enrollments`
--

DROP TABLE IF EXISTS `enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enrollments`
--

LOCK TABLES `enrollments` WRITE;
/*!40000 ALTER TABLE `enrollments` DISABLE KEYS */;
/*!40000 ALTER TABLE `enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `learning_progress`
--

DROP TABLE IF EXISTS `learning_progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `learning_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `hours_spent` decimal(5,2) DEFAULT 0.00,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `learning_progress`
--

LOCK TABLES `learning_progress` WRITE;
/*!40000 ALTER TABLE `learning_progress` DISABLE KEYS */;
/*!40000 ALTER TABLE `learning_progress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`payment_id`),
  KEY `user_id` (`user_id`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_classes`
--

DROP TABLE IF EXISTS `user_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_classes` (
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('enrolled','in-progress','completed') DEFAULT 'enrolled',
  `notes` text DEFAULT NULL,
  `archived` tinyint(1) DEFAULT 0,
  `favorite` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`user_id`,`class_id`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `user_classes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_classes_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_classes`
--

LOCK TABLES `user_classes` WRITE;
/*!40000 ALTER TABLE `user_classes` DISABLE KEYS */;
INSERT INTO `user_classes` VALUES (20,4,'2025-11-20 05:57:55','enrolled',NULL,1,0),(20,11,'2025-11-20 06:16:04','enrolled',NULL,0,0);
/*!40000 ALTER TABLE `user_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_notes`
--

DROP TABLE IF EXISTS `user_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `color` varchar(10) DEFAULT '#FFF9C4',
  `type` varchar(20) DEFAULT 'plain',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_notes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_notes`
--

LOCK TABLES `user_notes` WRITE;
/*!40000 ALTER TABLE `user_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (15,'Admin','admin@gmail.com','$2y$10$tBSzGwt29H5BUxlVgS7u4uFst0gAY4JnBMmgMbXlggtLb/JMVqjbe','2025-11-19 07:30:56',1,0,NULL,'admin'),(20,'Cristal','jewellcristall@gmail.com','$2y$10$cIt1gNejTucH69eTo6op..RUMaUMhtVJ01oEyjiuG6aB3E31NPA5S','2025-11-20 05:57:23',1,0,NULL,'user');
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

-- Dump completed on 2025-11-20 17:05:16
