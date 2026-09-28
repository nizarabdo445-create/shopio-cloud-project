-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: project
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `coustmrs`
--

DROP TABLE IF EXISTS `coustmrs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coustmrs` (
  `user_id` int(100) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(100) NOT NULL,
  `user_gmail` varchar(100) NOT NULL,
  `user_pass` varchar(100) NOT NULL,
  `user_per` int(11) NOT NULL DEFAULT 0,
  `creat_acoount` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `update_acount` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coustmrs`
--

LOCK TABLES `coustmrs` WRITE;
/*!40000 ALTER TABLE `coustmrs` DISABLE KEYS */;
INSERT INTO `coustmrs` VALUES (20,'Nizar','nizarabdo554@gmail.com','$2y$10$6fmPyC/fVyD1cxcnkux4/ev7/vQxi8SkejMqr8RpvWxXWK6TGVdfm',1,'2025-02-07 20:42:47','2025-02-07 17:42:47'),(21,'jhsbf','cec@vdjhs.com','$2y$10$qBAmzPwvfcu7rjTLHCjJfuDE9wy69ATN6EGUxZxFrOY4lyVeQwYJ.',0,'2025-02-07 20:52:19','2025-02-07 17:52:19'),(23,'hashem','hashem@gmail.com','$2y$10$XX9x91LL8/P2SbhfhCMxjOl7b.j7ekoCVrsdWuv4ccS0ncy16VMYS',0,'2025-02-08 12:49:05','2025-02-08 09:49:05'),(24,'bassmh','bassmh@gmail.com','$2y$10$cEjyQ8KCx7xQ2jbJdTghK.vs2kK9yuFnWmQJeQuyUYWH55Z/hpxNO',0,'2025-02-08 14:09:15','2025-02-08 11:09:15'),(25,'nizar','nizarabdo@gmail.com','$2y$10$kWMVoBNfhAkRtPuggeX3UuwyyoA5TPsb5KrmFeuDOhpjRkUYFNleC',0,'2025-02-11 01:42:46','2025-02-10 22:42:46'),(26,' Nizar','nizarabdo554@gmail.com','$2y$10$79MEeV0vb1QVOk09.rAWsemkvhpnTCN2aTuf.u8WOez9o3a3xfOt6',0,'2025-03-07 22:49:40','2025-03-07 19:49:40'),(27,'sultan','cec@vdjhs.com','$2y$10$svqrLDeOIOFRwq6kWecH4ubePeQn425z5DVyora9KX4clFQds9mAi',0,'2025-03-07 22:52:15','2025-03-07 19:52:15'),(28,'sultan','cec@vdjhs.com','sss123#',0,'2025-03-07 22:57:31','2025-03-07 19:57:31');
/*!40000 ALTER TABLE `coustmrs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prod`
--

DROP TABLE IF EXISTS `prod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prod_name` varchar(100) NOT NULL,
  `prod_price` varchar(100) NOT NULL,
  `prod_img` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prod`
--

LOCK TABLES `prod` WRITE;
/*!40000 ALTER TABLE `prod` DISABLE KEYS */;
INSERT INTO `prod` VALUES
(57,'Iphone-16','5000','img/iphone-16.jpeg'),
(58,'Xiaomi Redmi Note 12','1100','img/xiaomi-redmi-note-12.jpeg'),
(61,'Iphone-16','500$','img/cdma-phones-saudi.jpeg'),
(62,'Asos','1000$','img/samsung-galaxy-a55.jpeg'),
(63,'Iphone-16','450$','img/iphone-12-blue.jpeg'),
(64,'ASUS ROG','1800','img/asus-rog.jpg'),
(65,'Dell XPS','1600','img/dell-xps.jpg'),
(66,'Google Pixel 9','900','img/google-pixel-9.jpg'),
(67,'HP Spectre','1500','img/hp-spectre.jpg'),
(68,'iPhone 16 Pro Max','1400','img/iphone-16-pro-max.jpg'),
(69,'Lenovo ThinkPad','1300','img/lenovo-thinkpad.jpg'),
(70,'MacBook Pro','2200','img/macbook-pro.jpg'),
(71,'OnePlus 13','850','img/oneplus-13.jpg'),
(72,'Samsung Galaxy S25 Ultra','1300','img/samsung-galaxy-s25-ultra.jpg'),
(73,'Xiaomi Redmi Note 14','400','img/xiaomi-redmi-note-14.jpg');
/*!40000 ALTER TABLE `prod` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-14 18:55:36
-- ==========================================================
-- PHASE 2 MIGRATION SCRIPT
-- Maps legacy tables (coustmrs, prod) to API tables (users, products)
-- Adds necessary columns and creates new E-commerce tables
-- ==========================================================

-- 1. Migrate Users Table
RENAME TABLE `coustmrs` TO `users`;

ALTER TABLE `users`
  CHANGE `user_gmail` `user_email` varchar(100) NOT NULL,
  CHANGE `user_per` `user_role` int(11) NOT NULL DEFAULT 0,
  CHANGE `creat_acoount` `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  CHANGE `update_acount` `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  ADD COLUMN `api_token` varchar(255) DEFAULT NULL AFTER `user_role`;


-- 2. Migrate Products Table
RENAME TABLE `prod` TO `products`;

-- Clean price string (e.g. remove $ signs) before converting to decimal
UPDATE `products` SET `prod_price` = REPLACE(`prod_price`, '$', '');

ALTER TABLE `products`
  CHANGE `prod_price` `prod_price` decimal(10,2) NOT NULL,
  ADD COLUMN `created_at` timestamp NOT NULL DEFAULT current_timestamp();


-- 3. Create Cart & Order Tables
CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(100) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `user_product_unique` (`user_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(100) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
