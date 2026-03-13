-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: golstalgia
-- ------------------------------------------------------
-- Server version	8.0.45

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
-- Table structure for table `clasificacion`
--

DROP TABLE IF EXISTS `clasificacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clasificacion` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `liga_id` int unsigned NOT NULL,
  `participante_id` int unsigned NOT NULL,
  `partidas_jugadas` smallint unsigned NOT NULL DEFAULT '0',
  `victorias` smallint unsigned NOT NULL DEFAULT '0',
  `derrotas` smallint unsigned NOT NULL DEFAULT '0',
  `puntos_favor` smallint unsigned NOT NULL DEFAULT '0',
  `puntos_contra` smallint unsigned NOT NULL DEFAULT '0',
  `puntos_clasificacion` smallint unsigned NOT NULL DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_clas_liga_usuario` (`liga_id`,`participante_id`),
  KEY `fk_clas_participante` (`participante_id`),
  CONSTRAINT `fk_clas_liga` FOREIGN KEY (`liga_id`) REFERENCES `ligas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_clas_participante` FOREIGN KEY (`participante_id`) REFERENCES `participantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clasificacion`
--

LOCK TABLES `clasificacion` WRITE;
/*!40000 ALTER TABLE `clasificacion` DISABLE KEYS */;
INSERT INTO `clasificacion` VALUES (41,1,1,1,1,0,1,0,3,'2026-03-13 18:34:54'),(42,1,2,1,0,1,0,1,0,'2026-03-13 18:34:54'),(43,1,3,1,1,0,2,1,3,'2026-03-13 18:34:54'),(44,1,4,1,0,1,1,2,0,'2026-03-13 18:34:54'),(45,1,5,1,1,0,1,0,3,'2026-03-13 18:34:54'),(46,1,6,1,0,1,0,1,0,'2026-03-13 18:34:54'),(47,1,7,1,0,1,0,4,0,'2026-03-13 18:34:54'),(48,1,8,1,1,0,4,0,3,'2026-03-13 18:34:54'),(49,1,9,1,0,0,1,1,1,'2026-03-13 18:34:54'),(50,1,10,1,0,0,1,1,1,'2026-03-13 18:34:54'),(51,1,11,1,0,1,0,1,0,'2026-03-13 18:34:54'),(52,1,12,1,1,0,1,0,3,'2026-03-13 18:34:54');
/*!40000 ALTER TABLE `clasificacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disponibilidad`
--

DROP TABLE IF EXISTS `disponibilidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disponibilidad` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `partida_id` int unsigned NOT NULL,
  `usuario_id` int unsigned NOT NULL,
  `franja_inicio` datetime NOT NULL,
  `franja_fin` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_disp_partida` (`partida_id`),
  KEY `fk_disp_usuario` (`usuario_id`),
  CONSTRAINT `fk_disp_partida` FOREIGN KEY (`partida_id`) REFERENCES `partidas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_disp_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disponibilidad`
--

LOCK TABLES `disponibilidad` WRITE;
/*!40000 ALTER TABLE `disponibilidad` DISABLE KEYS */;
/*!40000 ALTER TABLE `disponibilidad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estadisticas`
--

DROP TABLE IF EXISTS `estadisticas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estadisticas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `partida_id` int unsigned NOT NULL,
  `usuario_id` int unsigned NOT NULL,
  `tema` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `preguntas_total` tinyint unsigned NOT NULL DEFAULT '0',
  `preguntas_acertadas` tinyint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_est_partida` (`partida_id`),
  KEY `fk_est_usuario` (`usuario_id`),
  CONSTRAINT `fk_est_partida` FOREIGN KEY (`partida_id`) REFERENCES `partidas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_est_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estadisticas`
--

LOCK TABLES `estadisticas` WRITE;
/*!40000 ALTER TABLE `estadisticas` DISABLE KEYS */;
/*!40000 ALTER TABLE `estadisticas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jornadas`
--

DROP TABLE IF EXISTS `jornadas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jornadas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `liga_id` int unsigned NOT NULL,
  `numero` int unsigned NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_jornada_liga` (`liga_id`,`numero`),
  CONSTRAINT `fk_jornada_liga` FOREIGN KEY (`liga_id`) REFERENCES `ligas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jornadas`
--

LOCK TABLES `jornadas` WRITE;
/*!40000 ALTER TABLE `jornadas` DISABLE KEYS */;
INSERT INTO `jornadas` VALUES (36,1,1,'2026-03-13','2026-03-13',1,'2026-03-13 17:57:34'),(37,1,2,'2026-03-13','2026-03-13',1,'2026-03-13 17:57:34'),(38,1,3,'2026-03-13','2026-03-13',1,'2026-03-13 17:57:34'),(39,1,4,'2026-03-13','2026-03-13',1,'2026-03-13 17:57:34'),(40,1,5,'2026-03-13','2026-03-13',1,'2026-03-13 17:57:34'),(41,1,6,'2026-03-13','2026-03-13',1,'2026-03-13 17:57:34'),(42,1,7,'2026-03-13','2026-03-13',1,'2026-03-13 17:57:34'),(43,1,8,'2026-03-13','2026-03-13',1,'2026-03-13 17:57:34'),(44,1,9,'2026-03-13','2026-03-13',1,'2026-03-13 17:57:34'),(45,1,10,'2026-03-13','2026-03-13',1,'2026-03-13 17:57:34'),(46,1,11,'2026-03-13','2026-03-13',1,'2026-03-13 17:57:34');
/*!40000 ALTER TABLE `jornadas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liga_usuarios`
--

DROP TABLE IF EXISTS `liga_usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `liga_usuarios` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `liga_id` int unsigned NOT NULL,
  `usuario_id` int unsigned NOT NULL,
  `inscrito_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_liga_usuario` (`liga_id`,`usuario_id`),
  KEY `fk_lu_usuario` (`usuario_id`),
  CONSTRAINT `fk_lu_liga` FOREIGN KEY (`liga_id`) REFERENCES `ligas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_lu_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `liga_usuarios`
--

LOCK TABLES `liga_usuarios` WRITE;
/*!40000 ALTER TABLE `liga_usuarios` DISABLE KEYS */;
/*!40000 ALTER TABLE `liga_usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ligas`
--

DROP TABLE IF EXISTS `ligas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ligas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `temporada` int unsigned NOT NULL DEFAULT '1',
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ligas`
--

LOCK TABLES `ligas` WRITE;
/*!40000 ALTER TABLE `ligas` DISABLE KEYS */;
INSERT INTO `ligas` VALUES (1,'Liga Golstalgia',1,'Primera temporada de la liga de trivial para patreons de Golstalgia',1,'2026-03-01',NULL,'2026-03-13 14:54:29','2026-03-13 14:54:29');
/*!40000 ALTER TABLE `ligas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `participantes`
--

DROP TABLE IF EXISTS `participantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `participantes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `usuario_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `participantes`
--

LOCK TABLES `participantes` WRITE;
/*!40000 ALTER TABLE `participantes` DISABLE KEYS */;
INSERT INTO `participantes` VALUES (1,'Fabián',NULL,'2026-03-13 18:13:36','2026-03-13 18:13:36'),(2,'Óscar',NULL,'2026-03-13 18:13:36','2026-03-13 18:13:36'),(3,'Gerard',NULL,'2026-03-13 18:13:36','2026-03-13 18:13:36'),(4,'Jordi',NULL,'2026-03-13 18:13:36','2026-03-13 18:13:36'),(5,'Ferri',NULL,'2026-03-13 18:13:36','2026-03-13 18:13:36'),(6,'Toni',NULL,'2026-03-13 18:13:37','2026-03-13 18:13:37'),(7,'Jorge',NULL,'2026-03-13 18:13:37','2026-03-13 18:13:37'),(8,'Carles',NULL,'2026-03-13 18:13:37','2026-03-13 18:13:37'),(9,'Juanjo',NULL,'2026-03-13 18:13:37','2026-03-13 18:13:37'),(10,'Gustavo',NULL,'2026-03-13 18:13:37','2026-03-13 18:13:37'),(11,'Javi',NULL,'2026-03-13 18:13:37','2026-03-13 18:13:37'),(12,'Zerf',4,'2026-03-13 18:13:37','2026-03-13 18:38:32');
/*!40000 ALTER TABLE `participantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `participantes_ligas`
--

DROP TABLE IF EXISTS `participantes_ligas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `participantes_ligas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `participante_id` int unsigned NOT NULL,
  `liga_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `participante_id` (`participante_id`),
  KEY `liga_id` (`liga_id`),
  CONSTRAINT `participantes_ligas_ibfk_1` FOREIGN KEY (`participante_id`) REFERENCES `participantes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `participantes_ligas_ibfk_2` FOREIGN KEY (`liga_id`) REFERENCES `ligas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `participantes_ligas`
--

LOCK TABLES `participantes_ligas` WRITE;
/*!40000 ALTER TABLE `participantes_ligas` DISABLE KEYS */;
INSERT INTO `participantes_ligas` VALUES (1,1,1,'2026-03-13 18:13:37'),(2,2,1,'2026-03-13 18:13:37'),(3,3,1,'2026-03-13 18:13:37'),(4,4,1,'2026-03-13 18:13:37'),(5,5,1,'2026-03-13 18:13:37'),(6,6,1,'2026-03-13 18:13:37'),(7,7,1,'2026-03-13 18:13:37'),(8,8,1,'2026-03-13 18:13:37'),(9,9,1,'2026-03-13 18:13:37'),(10,10,1,'2026-03-13 18:13:37'),(11,11,1,'2026-03-13 18:13:37'),(12,12,1,'2026-03-13 18:13:37');
/*!40000 ALTER TABLE `participantes_ligas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `partidas`
--

DROP TABLE IF EXISTS `partidas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `partidas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `jornada_id` int unsigned NOT NULL,
  `local_id` int unsigned NOT NULL,
  `visitante_id` int unsigned NOT NULL,
  `estado` enum('pendiente','acordada','jugada','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `fecha_acordada` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_partida_jornada` (`jornada_id`),
  KEY `fk_partida_local` (`local_id`),
  KEY `fk_partida_visitante` (`visitante_id`),
  CONSTRAINT `fk_partida_jornada` FOREIGN KEY (`jornada_id`) REFERENCES `jornadas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_partida_local` FOREIGN KEY (`local_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `fk_partida_visitante` FOREIGN KEY (`visitante_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=271 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `partidas`
--

LOCK TABLES `partidas` WRITE;
/*!40000 ALTER TABLE `partidas` DISABLE KEYS */;
INSERT INTO `partidas` VALUES (205,36,1,2,'jugada',NULL,'2026-03-13 17:57:34','2026-03-13 18:07:26'),(206,36,3,4,'jugada',NULL,'2026-03-13 17:57:34','2026-03-13 18:07:26'),(207,36,5,6,'jugada',NULL,'2026-03-13 17:57:34','2026-03-13 18:07:26'),(208,36,7,8,'jugada',NULL,'2026-03-13 17:57:34','2026-03-13 18:07:26'),(209,36,9,10,'jugada',NULL,'2026-03-13 17:57:34','2026-03-13 18:07:26'),(210,36,11,12,'jugada',NULL,'2026-03-13 17:57:34','2026-03-13 18:07:26'),(211,37,8,1,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(212,37,12,5,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(213,37,10,11,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(214,37,4,9,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(215,37,2,3,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(216,37,6,7,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(217,38,3,1,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(218,38,5,10,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(219,38,7,12,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(220,38,9,2,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(221,38,11,4,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(222,38,6,8,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(223,39,8,3,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(224,39,1,9,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(225,39,12,6,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(226,39,10,7,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(227,39,4,5,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(228,39,2,11,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(229,40,12,8,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(230,40,5,2,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(231,40,7,4,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(232,40,9,3,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(233,40,11,1,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(234,40,6,10,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(235,41,8,9,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(236,41,1,5,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(237,41,3,11,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(238,41,10,12,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(239,41,4,6,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(240,41,2,7,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(241,42,12,4,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(242,42,5,3,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(243,42,10,8,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(244,42,7,1,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(245,42,11,9,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(246,42,6,2,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(247,43,8,11,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(248,43,1,6,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(249,43,3,7,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(250,43,4,10,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(251,43,9,5,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(252,43,2,12,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(253,44,12,1,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(254,44,5,11,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(255,44,10,2,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(256,44,4,8,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(257,44,7,9,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(258,44,6,3,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(259,45,1,10,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(260,45,3,12,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(261,45,5,8,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(262,45,9,6,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(263,45,2,4,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(264,45,11,7,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(265,46,8,2,'pendiente',NULL,'2026-03-13 17:57:34','2026-03-13 17:57:34'),(266,46,12,9,'pendiente',NULL,'2026-03-13 17:57:35','2026-03-13 17:57:35'),(267,46,10,3,'pendiente',NULL,'2026-03-13 17:57:35','2026-03-13 17:57:35'),(268,46,4,1,'pendiente',NULL,'2026-03-13 17:57:35','2026-03-13 17:57:35'),(269,46,7,5,'pendiente',NULL,'2026-03-13 17:57:35','2026-03-13 17:57:35'),(270,46,6,11,'pendiente',NULL,'2026-03-13 17:57:35','2026-03-13 17:57:35');
/*!40000 ALTER TABLE `partidas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resultados`
--

DROP TABLE IF EXISTS `resultados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resultados` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `partida_id` int unsigned NOT NULL,
  `ganador_id` int unsigned DEFAULT NULL,
  `puntos_local` tinyint unsigned NOT NULL DEFAULT '0',
  `puntos_visitante` tinyint unsigned NOT NULL DEFAULT '0',
  `notas` text COLLATE utf8mb4_unicode_ci,
  `registrado_por` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `partida_id` (`partida_id`),
  KEY `fk_res_ganador` (`ganador_id`),
  KEY `fk_res_registrado` (`registrado_por`),
  CONSTRAINT `fk_res_ganador` FOREIGN KEY (`ganador_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `fk_res_partida` FOREIGN KEY (`partida_id`) REFERENCES `partidas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_res_registrado` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resultados`
--

LOCK TABLES `resultados` WRITE;
/*!40000 ALTER TABLE `resultados` DISABLE KEYS */;
INSERT INTO `resultados` VALUES (7,205,1,1,0,NULL,1,'2026-03-13 18:07:26','2026-03-13 18:07:26'),(8,206,3,2,1,NULL,1,'2026-03-13 18:07:26','2026-03-13 18:07:26'),(9,207,5,1,0,NULL,1,'2026-03-13 18:07:26','2026-03-13 18:07:26'),(10,208,8,0,4,NULL,1,'2026-03-13 18:07:26','2026-03-13 18:07:26'),(11,209,NULL,1,1,NULL,1,'2026-03-13 18:07:26','2026-03-13 18:07:26'),(12,210,12,0,1,NULL,1,'2026-03-13 18:07:26','2026-03-13 18:07:26');
/*!40000 ALTER TABLE `resultados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nivel` enum('participante','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'participante',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `google_id` (`google_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Josep','josep@golstalgia.com','$2y$10$pD6GTFPY8xgbMH.hRHE9nexsyPldY8SCU1vN8t27V9e.o8CD0mLfy','admin',NULL,1,NULL,NULL,'2026-03-13 18:14:02','2026-03-13 18:14:02'),(2,'Sagra','sagra@golstalgia.com','$2y$10$pD6GTFPY8xgbMH.hRHE9nexsyPldY8SCU1vN8t27V9e.o8CD0mLfy','admin',NULL,1,NULL,NULL,'2026-03-13 18:14:02','2026-03-13 18:14:02'),(3,'Zerf','zerf@golstalgia.com','$2y$10$pD6GTFPY8xgbMH.hRHE9nexsyPldY8SCU1vN8t27V9e.o8CD0mLfy','admin',NULL,1,NULL,NULL,'2026-03-13 18:14:02','2026-03-13 18:14:02'),(4,'Zerf Zerfiuno','zerfiunozerf@gmail.com',NULL,'participante',NULL,1,NULL,'113465819914228559269','2026-03-13 18:37:11','2026-03-13 18:37:11');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-13 19:36:43
