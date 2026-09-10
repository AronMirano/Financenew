CREATE DATABASE  IF NOT EXISTS `gfms` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `gfms`;
-- MySQL dump 10.13  Distrib 8.0.46, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: gfms
-- ------------------------------------------------------
-- Server version	8.0.46

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
-- Table structure for table `fin_documents`
--

DROP TABLE IF EXISTS `fin_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fin_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `serial` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kind` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `fund_cluster` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payee_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allotment_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `cert_a_officer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cert_a_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cert_b_officer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cert_b_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fin_documents_serial_unique` (`serial`),
  KEY `fin_documents_cert_a_officer_id_foreign` (`cert_a_officer_id`),
  KEY `fin_documents_cert_b_officer_id_foreign` (`cert_b_officer_id`),
  KEY `fin_documents_kind_index` (`kind`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fin_documents`
--

LOCK TABLES `fin_documents` WRITE;
INSERT INTO `fin_documents` VALUES (16,'OBR-2026-0001','obligation','2026-09-08','01-1-01-101','a','admin','a',0.00,NULL,NULL,'p-austria','September 8, 2026','Obligated','2026-09-08 15:41:44','2026-09-08 15:41:44'),(17,'BUR-2026-0001','utilization','2026-09-08','05206441','b','b','b',0.00,'p-austria','September 8, 2026','p-austria','September 8, 2026','Utilized','2026-09-08 15:42:17','2026-09-08 15:42:17'),(18,'OBR-2026-0002','obligation','2026-09-08','01-1-01-101','c','c','c',0.00,'p-austria','September 8, 2026','p-austria','September 8, 2026','Obligated','2026-09-08 15:46:24','2026-09-08 15:46:24'),(19,'OBR-2026-0003','obligation','2026-09-08','01-1-01-101','a',NULL,'16 Quiling Sur, Batac City, Ilocos Norte, Philippines 2906',100000.00,'p-austria','September 8, 2026','p-austria','September 8, 2026','Obligated','2026-09-08 22:40:36','2026-09-08 22:40:37');
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-10 11:02:18
