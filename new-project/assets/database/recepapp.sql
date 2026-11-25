-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: recepapp
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
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Postres'),(2,'Ensaladas'),(3,'Sopas'),(4,'Platos Principales'),(5,'Desayunos'),(6,'Bebidas');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comentarios`
--

DROP TABLE IF EXISTS `comentarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `receta_id` int(11) DEFAULT NULL,
  `comentario` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `receta_id` (`receta_id`),
  CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`receta_id`) REFERENCES `recetas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comentarios`
--

LOCK TABLES `comentarios` WRITE;
/*!40000 ALTER TABLE `comentarios` DISABLE KEYS */;
INSERT INTO `comentarios` VALUES (4,2,1,'Delicioso y muy facil de preparar, me encanto esta receta','2025-10-27 02:09:18'),(6,9,1,'Un tip es que tambien lo pueden hacer en la estufa y queda una igual de delicioso.','2025-10-27 02:14:49'),(7,10,33,'Super Deliciosa me quedo esta receta, muy recomendada.','2025-10-27 02:15:57'),(8,10,1,'Sii!! lo hice en la estufa y quedo super bien.','2025-10-27 02:16:30'),(16,9,33,'Buena receta','2025-11-18 15:44:01'),(17,2,38,'delicioso','2025-11-18 15:51:58');
/*!40000 ALTER TABLE `comentarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favoritos`
--

DROP TABLE IF EXISTS `favoritos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `favoritos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `receta_id` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_favorito` (`usuario_id`,`receta_id`),
  KEY `receta_id` (`receta_id`),
  CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `favoritos_ibfk_2` FOREIGN KEY (`receta_id`) REFERENCES `recetas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favoritos`
--

LOCK TABLES `favoritos` WRITE;
/*!40000 ALTER TABLE `favoritos` DISABLE KEYS */;
INSERT INTO `favoritos` VALUES (3,2,2,'2025-10-21 07:15:03'),(5,2,1,'2025-10-21 12:48:50'),(6,1,1,'2025-10-21 14:16:57'),(11,9,33,'2025-10-27 22:40:06'),(15,2,38,'2025-10-28 01:36:19'),(17,2,39,'2025-11-18 16:36:12'),(18,17,38,'2026-09-14 00:51:21');
/*!40000 ALTER TABLE `favoritos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recetas`
--

DROP TABLE IF EXISTS `recetas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recetas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ingredientes` text NOT NULL,
  `instrucciones` text NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `categoria_id` (`categoria_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `recetas_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  CONSTRAINT `recetas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recetas`
--

LOCK TABLES `recetas` WRITE;
/*!40000 ALTER TABLE `recetas` DISABLE KEYS */;
INSERT INTO `recetas` VALUES (1,'Pastel de Chocolate','Delicioso y suave pastel con cobertura de cacao','2 tazas de harina\n1 taza de azúcar\n1/2 taza de cacao en polvo\n2 huevos\n1 taza de leche\n1/2 taza de mantequilla\n1 cucharadita de vainilla','1. Precalentar el horno a 180°C\n2. Mezclar ingredientes secos\n3. Agregar ingredientes húmedos\n4. Hornear por 30 minutos\n5. Dejar enfriar y decorar','pastel de chocolate.jpg',1,1,'2025-10-21 04:01:50'),(2,'Ensalada César','Ensalada fresca con aderezo césar y crotones','1 lechuga romana\n200g de pollo a la parrilla\n1 taza de crotones\n1/2 taza de queso parmesano\nAderezo césar al gusto','1. Lavar y cortar la lechuga\n2. Cocinar el pollo y cortar en tiras\n3. Mezclar todos los ingredientes\n4. Agregar aderezo y servir','ensaladas.jpg',2,1,'2025-10-21 04:01:50'),(3,'Sopa de Tomate','Sopa cremosa de tomate perfecta para días fríos','1 kg de tomates maduros\n1 cebolla picada\n2 dientes de ajo\n2 tazas de caldo de verduras\n1/2 taza de crema\nSal y pimienta al gusto','1. Sofreír cebolla y ajo\n2. Agregar tomates picados\n3. Cocinar por 20 minutos\n4. Licuar y agregar crema\n5. Sazonar al gusto','sopa de verduras.jpg',3,1,'2025-10-21 04:01:50'),(4,'Pancakes Esponjosos','Pancakes suaves y esponjosos perfectos para el desayuno','1 taza de harina\n2 cucharadas de azúcar\n2 cucharaditas de polvo de hornear\n1/2 cucharadita de sal\n1 huevo\n1 taza de leche\n2 cucharadas de mantequilla derretida','1. Mezclar ingredientes secos\n2. Agregar ingredientes húmedos\n3. Cocinar en sartén caliente\n4. Servir con miel o frutas','brownies-237776_1280.jpg',5,1,'2025-10-21 04:01:50'),(5,'Smoothie de Frutos Rojos','Batido refrescante y nutritivo lleno de antioxidantes','1 taza de frutos rojos mezclados\n1 plátano\n1 taza de yogurt griego\n1/2 taza de leche de almendras\n1 cucharada de miel','1. Lavar las frutas\n2. Licuar todos los ingredientes\n3. Servir inmediatamente','ensalada de frutas.jpg',6,1,'2025-10-21 04:01:50'),(6,'Pasta Alfredo','Pasta cremosa con salsa alfredo casera','200g de pasta fettuccine\n1 taza de crema para batir\n1/2 taza de mantequilla\n1 taza de queso parmesano rallado\nSal y pimienta al gusto\nPerejil fresco picado','1. Cocinar la pasta al dente\n2. Preparar la salsa alfredo\n3. Mezclar pasta con salsa\n4. Espolvorear con perejil','pasta carbonara.jpg',4,1,'2025-10-21 04:01:50'),(33,'Pasta con Pollo y Champiñones','Una receta deliciosa y fácil de preparar. Una pasta cremosa con pollo y champiñones.','250 g de pasta (tipo fettuccine o penne)\r\n\r\n1 pechuga de pollo en trozos\r\n\r\n1 taza de champiñones rebanados\r\n\r\n1 taza de crema de leche\r\n\r\n1/2 taza de queso parmesano rallado\r\n\r\n2 cucharadas de mantequilla\r\n\r\n1 diente de ajo picado\r\n\r\nSal y pimienta al gusto\r\n\r\nPerejil fresco picado (opcional)','Cocinar la pasta en agua con sal siguiendo las instrucciones del empaque. Escurrir y reservar.\r\n\r\nDerretir la mantequilla en una sartén y sofreír el ajo hasta que esté dorado.\r\n\r\nAgregar el pollo y cocinar hasta que esté bien dorado.\r\n\r\nAñadir los champiñones y cocinar por 3-4 minutos más.\r\n\r\nIncorporar la crema de leche y el queso parmesano. Mezclar hasta obtener una salsa cremosa.\r\n\r\nAgregar la pasta cocida a la sartén y revolver para que se impregne bien de la salsa.\r\n\r\nServir caliente y decorar con perejil fresco si lo deseas.','receta_68fe8eb67cbe5.jpg',4,9,'2025-10-27 02:12:22'),(38,'Smoothie de Fresa y Banano','Un delicioso smoothie de fresa y banano, cremoso, refrescante y lleno de vitaminas. Ideal para el desayuno o como una bebida energética natural.','1 taza de fresas frescas (lavadas y sin hojas)\r\n\r\n1 banano maduro\r\n\r\n1 taza de leche (puede ser entera, deslactosada o vegetal)\r\n\r\n½ taza de yogur natural o de fresa\r\n\r\n1 cucharada de miel o azúcar (opcional, al gusto)\r\n\r\n3 a 4 cubos de hielo','Preparar los ingredientes: Lava bien las fresas y corta el banano en rodajas.\r\n\r\nAgregar al vaso de la licuadora: Coloca las fresas, el banano, la leche, el yogur y la miel.\r\n\r\nAñadir el hielo: Incorpora los cubos de hielo para obtener una textura más fría y espesa.\r\n\r\nLicuar: Mezcla todo durante 1-2 minutos hasta que quede una textura suave y sin grumos.\r\n\r\nServir: Vierte en un vaso grande o copa y decora con una rodaja de fresa o banano.','receta_6900178784f57.jpg',6,10,'2025-10-28 01:08:23'),(39,'Cupcakes de vainilla Decorables','Estos deliciosos cupcakes de vainilla son suaves, esponjosos y con un sabor clásico que encanta a todos.','1 ½ tazas de harina de trigo\r\n\r\n1 taza de azúcar\r\n\r\n½ taza de mantequilla (a temperatura ambiente)\r\n\r\n2 huevos grandes\r\n\r\n½ taza de leche\r\n\r\n2 cucharaditas de esencia de vainilla\r\n\r\n2 cucharaditas de polvo de hornear\r\n\r\n1 pizca de sal','Precalentar el horno: Calienta el horno a 180 °C (350 °F) y prepara una bandeja con moldes para cupcakes.\r\n\r\nMezclar ingredientes secos: En un recipiente, tamiza la harina, el polvo de hornear y la sal.\r\n\r\nBatir la mantequilla y el azúcar: En otro bol, bate la mantequilla con el azúcar hasta obtener una mezcla cremosa y suave.\r\n\r\nAgregar los huevos y la vainilla: Incorpora los huevos uno a uno y la esencia de vainilla, mezclando bien.\r\n\r\nCombinar: Añade poco a poco la mezcla seca, alternando con la leche, hasta obtener una masa homogénea.\r\n\r\nLlenar los moldes: Vierte la mezcla en los moldes, llenando solo 2/3 de su capacidad.\r\n\r\nHornear: Cocina durante 18–20 minutos o hasta que al insertar un palillo, este salga limpio.\r\n\r\nEnfriar y decorar: Deja enfriar completamente antes de decorarlos con crema, chispas o el topping de tu preferencia.','receta_690017f299929.jpg',1,2,'2025-10-28 01:10:10');
/*!40000 ALTER TABLE `recetas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Chef Principal','chef@recepapp.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2025-10-21 04:01:50'),(2,'Elizabeth Pinto','elizabeth@gmail.com','$2y$10$ax6WSLhzGnEz8fWENTyoyulCv2su9Geyop1fCQC3mlSTN7D6Najn2','2025-10-21 04:19:21'),(9,'Fernanda Collazos','Fernanda@gmail.com','$2y$10$UDjhvEiPrMFVzFsv.xOdpe98Jf8jRnohukR/W8Ot7NLkwAu1m2ejm','2025-10-27 02:10:18'),(10,'Yudy Fuertes','Yudy@gmail.com','$2y$10$K2D8lqWqCV.YWRd6ceNTve6oaXUtrXXzTBZZThqav4L7xIMfRkND.','2025-10-27 02:15:17'),(11,'Isabela Pinto','Isabela@gmail.com','$2y$10$fz34QN0KeijIoqpo4lEjZepZPtyh8HK2HMKniPt3F9bRoWcgUT8x.','2025-10-27 02:20:12'),(12,'Angélica Pinto','ange.pinto22@gmail.com','$2y$10$2RUrTSViux4JM4jqqdkRhOr0ARHQO59RKMFxkr8okvxeu6vOykbwa','2025-10-27 16:44:26'),(13,'Juan','Juan@gmail.com','$2y$10$CWxlW28C3fPxj4g4Ll.crO7GgnFmzPujGOaQu/u.BkBALC8zCLGHC','2025-10-27 20:25:03'),(15,'a...................................................................................................','sQ@gmail.com','$2y$10$hw56601cVjSSA8iV3wYdeun6c0iSsmv9fUKBRuYK3wg4IoyUsc2Na','2025-11-18 16:36:57'),(16,'javier guaca','javier@gmail.com','$2y$10$K15CW/8aiZNchj5GEDh.6.VYUsxMkmNIbzQAm02zp.Tp8y988Do9G','2026-02-19 19:50:13'),(17,'sebastian','sebastian@gmail.com','$2y$10$0WqkfoakRfX2l2k/nBGGB.84oZgGCS3UU.zTH7SENoWkomNV1AJRi','2026-09-14 00:50:59'),(18,'carlos','carlos@gmail.com','$2y$10$34c2jKV6CCkRWF6SZPvQAOsBexGeRSm9pwzbIqphkUQIFvt2sNvgW','2026-11-25 04:14:34');
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

-- Dump completed on 2026-11-24 23:34:46
