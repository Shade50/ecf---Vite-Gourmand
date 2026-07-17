-- MySQL dump 10.13  Distrib 8.4.10, for Linux (x86_64)
--
-- Host: localhost    Database: vite_gourmand
-- ------------------------------------------------------
-- Server version	8.4.10

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
-- Table structure for table `allergene`
--

DROP TABLE IF EXISTS `allergene`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `allergene` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` varchar(100) NOT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `allergene`
--

LOCK TABLES `allergene` WRITE;
/*!40000 ALTER TABLE `allergene` DISABLE KEYS */;
INSERT INTO `allergene` VALUES (1,'Lait','Présence de lait ou de produits dérivés (crème, beurre, fromage, lait en poudre, lactosérum, etc.). Peut provoquer une réaction chez les personnes allergiques aux protéines du lait.'),(2,'Gluten','Présence de céréales contenant du gluten (blé, seigle, orge, avoine...).'),(3,'Œufs','Présence d\'œufs ou de produits dérivés.'),(4,'Arachides','Présence d\'arachides ou de produits à base d\'arachides.'),(5,'Fruits à coque','Présence d\'amandes, noisettes, noix, pistaches, noix de cajou, etc.'),(6,'Soja','Présence de soja ou de produits dérivés.'),(7,'Céleri','Présence de céleri sous toutes ses formes.'),(8,'Moutarde','Présence de moutarde ou de graines de moutarde.'),(9,'Sésame','Présence de graines de sésame ou d\'huile de sésame.'),(10,'Poissons','Présence de poisson ou de produits dérivés.'),(11,'Crustacés','Présence de crustacés (crevette, crabe, homard...).'),(12,'Mollusques','Présence de moules, huîtres, calamars, etc.'),(13,'Sulfites','Présence de sulfites, souvent utilisés comme conservateurs.'),(14,'Lupin','Présence de farine ou de graines de lupin.');
/*!40000 ALTER TABLE `allergene` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20260707194148','2026-07-17 08:42:04',83),('DoctrineMigrations\\Version20260708101140','2026-07-17 08:47:44',54),('DoctrineMigrations\\Version20260709073917','2026-07-17 08:47:44',20),('DoctrineMigrations\\Version20260709075214','2026-07-17 08:47:44',16),('DoctrineMigrations\\Version20260709075836','2026-07-17 08:47:44',19),('DoctrineMigrations\\Version20260709082237','2026-07-17 08:47:44',275),('DoctrineMigrations\\Version20260709084846','2026-07-17 08:47:44',272),('DoctrineMigrations\\Version20260710150743','2026-07-17 08:47:45',217),('DoctrineMigrations\\Version20260710210713','2026-07-17 08:47:45',8),('DoctrineMigrations\\Version20260711150225','2026-07-17 08:47:45',176),('DoctrineMigrations\\Version20260715133212','2026-07-17 08:47:45',226),('DoctrineMigrations\\Version20260715210507','2026-07-17 08:47:45',321);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` longtext NOT NULL,
  `minimum_person` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `conditions` longtext,
  `stock` int NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `theme_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7D053A9359027487` (`theme_id`),
  CONSTRAINT `FK_7D053A9359027487` FOREIGN KEY (`theme_id`) REFERENCES `theme` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,'Menu Tradition','Un menu convivial inspiré de la cuisine française traditionnelle, élaboré à partir de produits frais et de saison. Idéal pour les repas de famille, anniversaires et événements associatifs.',10,32.90,'Commande à partir de 10 personnes. Réservation minimum 72 heures à l\'avance. Livraison disponible selon la zone géographique.',49,'menu-tradition.jpg',1),(2,'Menu Prestige','Une formule gastronomique élaborée à partir de produits nobles et de saison. Idéale pour les mariages, réceptions, repas d\'entreprise et événements haut de gamme.',20,49.90,'Commande à partir de 20 personnes. Réservation minimum 7 jours à l\'avance. Livraison et installation possibles selon la localisation de l\'événement.',30,'menu-prestige.jpg',2),(3,'Menu Cocktail','Une sélection de pièces salées et sucrées à déguster debout, idéale pour les cocktails, inaugurations, séminaires, portes ouvertes et événements professionnels.',15,24.90,'Commande à partir de 15 personnes. Réservation minimum 5 jours à l\'avance. Livraison disponible avec installation sur demande.',40,'menu-cocktail.jpg',3),(4,'Menu Végétarien','Une sélection de plats végétariens savoureux élaborés avec des légumes de saison, des céréales et des produits frais. Une formule gourmande adaptée à tous les convives.',10,29.90,'Commande à partir de 10 personnes. Réservation minimum 72 heures à l\'avance. Livraison disponible selon la zone géographique.',35,'menu-vegetarien.jpg',4),(5,'Menu Festif','Un menu d\'exception conçu pour les fêtes, mariages et grandes réceptions. Des produits raffinés et des saveurs gourmandes pour faire de chaque événement un moment inoubliable.',20,59.90,'Commande à partir de 20 personnes. Réservation minimum 10 jours à l\'avance. Livraison, installation et service possibles selon la prestation choisie.',25,'menu-festif.jpg',6);
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_plat`
--

DROP TABLE IF EXISTS `menu_plat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_plat` (
  `menu_id` int NOT NULL,
  `plat_id` int NOT NULL,
  PRIMARY KEY (`menu_id`,`plat_id`),
  KEY `IDX_E8775249CCD7E912` (`menu_id`),
  KEY `IDX_E8775249D73DB560` (`plat_id`),
  CONSTRAINT `FK_E8775249CCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_E8775249D73DB560` FOREIGN KEY (`plat_id`) REFERENCES `plat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_plat`
--

LOCK TABLES `menu_plat` WRITE;
/*!40000 ALTER TABLE `menu_plat` DISABLE KEYS */;
INSERT INTO `menu_plat` VALUES (1,1),(1,5),(1,6),(2,1),(2,3),(2,4),(3,1),(3,4),(3,5),(4,5),(5,1),(5,3),(5,4);
/*!40000 ALTER TABLE `menu_plat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messenger_messages`
--

LOCK TABLES `messenger_messages` WRITE;
/*!40000 ALTER TABLE `messenger_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messenger_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` varchar(50) NOT NULL,
  `create_at` datetime NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `delivery_date` datetime NOT NULL,
  `number_of_people` int NOT NULL,
  `delivery_adresse` varchar(255) NOT NULL,
  `user_id` int NOT NULL,
  `menu_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F5299398A76ED395` (`user_id`),
  KEY `IDX_F5299398CCD7E912` (`menu_id`),
  CONSTRAINT `FK_F5299398A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_F5299398CCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order`
--

LOCK TABLES `order` WRITE;
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
INSERT INTO `order` VALUES (1,'En attente','2026-07-17 16:52:54',38.92,'2026-07-17 18:51:00',10,'12 Cours de l\'Intendance 33000 Bordeaux',1,1);
/*!40000 ALTER TABLE `order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_status_history`
--

DROP TABLE IF EXISTS `order_status_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_status_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `old_status` varchar(50) NOT NULL,
  `new_status` varchar(50) NOT NULL,
  `changed_at` datetime NOT NULL,
  `commande_id` int NOT NULL,
  `changed_by_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_471AD77E82EA2E54` (`commande_id`),
  KEY `IDX_471AD77E828AD0A0` (`changed_by_id`),
  CONSTRAINT `FK_471AD77E828AD0A0` FOREIGN KEY (`changed_by_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_471AD77E82EA2E54` FOREIGN KEY (`commande_id`) REFERENCES `order` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_status_history`
--

LOCK TABLES `order_status_history` WRITE;
/*!40000 ALTER TABLE `order_status_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_status_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plat`
--

DROP TABLE IF EXISTS `plat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` longtext NOT NULL,
  `type` varchar(50) NOT NULL,
  `regime` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plat`
--

LOCK TABLES `plat` WRITE;
/*!40000 ALTER TABLE `plat` DISABLE KEYS */;
INSERT INTO `plat` VALUES (1,'Suprême de volaille fermière, crème forestière','Suprême de volaille fermière rôti, accompagné d\'une sauce crémeuse aux champignons de saison. Servi avec un gratin dauphinois et une poêlée de légumes frais.','Plat principal','Sans porc'),(2,'Suprême de volaille fermière, crème forestière','Suprême de volaille fermière rôti, accompagné d\'une sauce crémeuse aux champignons de saison. Servi avec un gratin dauphinois et une poêlée de légumes frais.','Plat principal','Sans porc'),(3,'Filet de bœuf, sauce au poivre','Filet de bœuf français grillé, nappé d\'une sauce au poivre concassé, accompagné d\'un gratin dauphinois et de légumes de saison.','Plat principal','Standard'),(4,'Dos de saumon rôti, beurre citronné','Dos de saumon rôti au four, servi avec un beurre citronné, un riz parfumé et une julienne de légumes croquants.','Plat principal','Pescétarien'),(5,'Risotto crémeux aux légumes du soleil','Risotto crémeux au parmesan accompagné de légumes de saison rôtis et de copeaux de parmesan.','Plat principal','Végétarien'),(6,'Lasagnes maison à la bolognaise','Lasagnes préparées avec une sauce bolognaise mijotée, une béchamel maison et un mélange de fromages gratinés.','Plat principal','Standard');
/*!40000 ALTER TABLE `plat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plat_allergene`
--

DROP TABLE IF EXISTS `plat_allergene`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plat_allergene` (
  `plat_id` int NOT NULL,
  `allergene_id` int NOT NULL,
  PRIMARY KEY (`plat_id`,`allergene_id`),
  KEY `IDX_6FA44BBFD73DB560` (`plat_id`),
  KEY `IDX_6FA44BBF4646AB2` (`allergene_id`),
  CONSTRAINT `FK_6FA44BBF4646AB2` FOREIGN KEY (`allergene_id`) REFERENCES `allergene` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_6FA44BBFD73DB560` FOREIGN KEY (`plat_id`) REFERENCES `plat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plat_allergene`
--

LOCK TABLES `plat_allergene` WRITE;
/*!40000 ALTER TABLE `plat_allergene` DISABLE KEYS */;
INSERT INTO `plat_allergene` VALUES (2,1),(2,2),(3,1),(4,1),(4,10),(5,1),(6,1),(6,2),(6,3);
/*!40000 ALTER TABLE `plat_allergene` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'ROLE_USER'),(2,'ROLE_EMPLOYEE'),(3,'ROLE_ADMIN');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `address` varchar(255) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `city` varchar(100) NOT NULL,
  `opening_hours_week` longtext,
  `opening_hours_saturday` longtext,
  `opening_hours_sunday` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
INSERT INTO `site_settings` VALUES (1,'Vite & Gourmand','contact@vite-gourmand.fr','0612345678','Place de la Bourse','33000','Bordeaux','08:00 - 12:00 et 13:00 - 18:00','08:00 - 13:00','Fermé');
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `theme`
--

DROP TABLE IF EXISTS `theme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `theme` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` varchar(100) NOT NULL,
  `description` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `theme`
--

LOCK TABLES `theme` WRITE;
/*!40000 ALTER TABLE `theme` DISABLE KEYS */;
INSERT INTO `theme` VALUES (1,'Menu Tradition','Un menu authentique mettant à l\'honneur les recettes traditionnelles françaises, élaboré à partir de produits de saison et de qualité. Idéal pour les repas de famille, anniversaires et événements conviviaux.'),(2,'Menu Prestige','Une sélection raffinée de mets gastronomiques préparés avec des produits d\'exception pour sublimer vos réceptions et événements haut de gamme.'),(3,'Menu Cocktail','Une formule composée de pièces salées et sucrées, parfaite pour les cocktails, inaugurations, séminaires et événements professionnels.'),(4,'Menu Végétarien','Un menu équilibré et savoureux composé exclusivement de plats végétariens, mettant en valeur les légumes et produits de saison.'),(5,'Menu Enfant','Une formule adaptée aux plus jeunes, composée de plats simples, gourmands et équilibrés.'),(6,'Menu Festif','Une sélection de plats spécialement imaginés pour les fêtes de fin d\'année, mariages et grandes réceptions.');
/*!40000 ALTER TABLE `theme` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `gsm` varchar(20) NOT NULL,
  `adresse_postale` varchar(255) NOT NULL,
  `role_id` int NOT NULL,
  `is_active` tinyint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`),
  KEY `IDX_8D93D649D60322AC` (`role_id`),
  CONSTRAINT `FK_8D93D649D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin@vite-gourmand.fr','$2y$13$BaHUfYsXutnN1Nk0ck.gbeoNmoZa./hENtWkodXVNBWWfRMQKsgem','Administrateur','Alex','0600000000','123 rue de la Paix, 33000 Bordeaux',3,1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-17 20:09:37
