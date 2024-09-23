CREATE DATABASE  IF NOT EXISTS `freducacional`;
USE `freducacional`;
-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: localhost    Database: freducacional
-- ------------------------------------------------------
-- Server version	9.0.0

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
-- Table structure for table `afastados`
--

DROP TABLE IF EXISTS `afastados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `afastados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDInativo` int NOT NULL,
  `Justificativa` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `INISuspensao` date DEFAULT NULL,
  `TERSuspensao` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `afastados`
--

LOCK TABLES `afastados` WRITE;
/*!40000 ALTER TABLE `afastados` DISABLE KEYS */;
/*!40000 ALTER TABLE `afastados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alocacoes`
--

DROP TABLE IF EXISTS `alocacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alocacoes` (
  `IDEscola` int NOT NULL,
  `IDProfissional` int NOT NULL,
  `INITurno` time NOT NULL,
  `TERTurno` time NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `TPProfissional` varchar(4) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alocacoes`
--

LOCK TABLES `alocacoes` WRITE;
/*!40000 ALTER TABLE `alocacoes` DISABLE KEYS */;
INSERT INTO `alocacoes` VALUES (3,1,'13:00:00','17:00:00','2024-07-28','2024-07-28','PROF'),(1,1,'07:00:00','11:00:00','2024-07-28','2024-07-28','PROF'),(1,2,'13:00:00','17:00:00','2024-07-31','2024-07-31','PROF');
/*!40000 ALTER TABLE `alocacoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alocacoes_disciplinas`
--

DROP TABLE IF EXISTS `alocacoes_disciplinas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alocacoes_disciplinas` (
  `IDEscola` int NOT NULL,
  `IDDisciplina` int NOT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alocacoes_disciplinas`
--

LOCK TABLES `alocacoes_disciplinas` WRITE;
/*!40000 ALTER TABLE `alocacoes_disciplinas` DISABLE KEYS */;
INSERT INTO `alocacoes_disciplinas` VALUES (1,1,'2024-07-28','2024-07-28'),(3,1,'2024-07-28','2024-07-28'),(1,2,'2024-07-28','2024-07-28'),(2,2,'2024-07-28','2024-07-28'),(3,2,'2024-07-28','2024-07-28');
/*!40000 ALTER TABLE `alocacoes_disciplinas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alteracoes_situacao`
--

DROP TABLE IF EXISTS `alteracoes_situacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alteracoes_situacao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDAluno` int NOT NULL,
  `STAluno` int NOT NULL,
  `Justificativa` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alteracoes_situacao`
--

LOCK TABLES `alteracoes_situacao` WRITE;
/*!40000 ALTER TABLE `alteracoes_situacao` DISABLE KEYS */;
INSERT INTO `alteracoes_situacao` VALUES (1,1,0,'fdgdfgg','2024-07-29','2024-07-29');
/*!40000 ALTER TABLE `alteracoes_situacao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alunos`
--

DROP TABLE IF EXISTS `alunos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alunos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDMatricula` int NOT NULL,
  `STAluno` int DEFAULT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `IDTurma` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alunos`
--

LOCK TABLES `alunos` WRITE;
/*!40000 ALTER TABLE `alunos` DISABLE KEYS */;
INSERT INTO `alunos` VALUES (1,1,0,'2024-07-28','2024-07-29',1),(2,2,0,'2024-07-28','2024-07-28',1);
/*!40000 ALTER TABLE `alunos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `atividades`
--

DROP TABLE IF EXISTS `atividades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `atividades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDAula` int NOT NULL,
  `DTEntrega` datetime NOT NULL,
  `TPConteudo` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `DSAtividade` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `Pontuacao` float NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atividades`
--

LOCK TABLES `atividades` WRITE;
/*!40000 ALTER TABLE `atividades` DISABLE KEYS */;
INSERT INTO `atividades` VALUES (1,2,'2024-07-31 20:16:00','Atividade 1 Matematica','At1 mat',10,'2024-07-31','2024-07-31'),(2,3,'2024-07-31 20:41:00','At1 Port','port at1',10,'2024-07-31','2024-07-31'),(3,3,'2024-07-31 23:19:00','At3 pt','pt at3 atividade 3 portugues',10,'2024-08-01','2024-08-01');
/*!40000 ALTER TABLE `atividades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `atividades_atribuicoes`
--

DROP TABLE IF EXISTS `atividades_atribuicoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `atividades_atribuicoes` (
  `IDAluno` int NOT NULL,
  `DTEntrega` datetime DEFAULT NULL,
  `Realizado` int DEFAULT NULL,
  `Feedback` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `Nota` float DEFAULT NULL,
  `IDAtividade` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atividades_atribuicoes`
--

LOCK TABLES `atividades_atribuicoes` WRITE;
/*!40000 ALTER TABLE `atividades_atribuicoes` DISABLE KEYS */;
INSERT INTO `atividades_atribuicoes` VALUES (1,NULL,NULL,NULL,'2024-07-30','2024-07-30',NULL,1),(2,NULL,NULL,NULL,'2024-07-30','2024-07-30',NULL,1),(1,NULL,NULL,NULL,'2024-07-30','2024-07-30',NULL,2),(1,NULL,NULL,NULL,'2024-07-30','2024-07-30',NULL,3),(2,NULL,NULL,NULL,'2024-07-30','2024-07-30',NULL,3),(1,NULL,NULL,NULL,'2024-07-30','2024-07-30',NULL,4),(2,NULL,NULL,NULL,'2024-07-30','2024-07-30',NULL,4),(1,NULL,NULL,NULL,'2024-07-31','2024-07-31',NULL,5),(1,NULL,NULL,NULL,'2024-07-31','2024-07-31',NULL,6),(2,NULL,NULL,NULL,'2024-07-31','2024-07-31',NULL,6),(1,NULL,NULL,NULL,'2024-07-31','2024-07-31',NULL,1),(2,NULL,NULL,NULL,'2024-07-31','2024-07-31',NULL,1),(1,NULL,NULL,NULL,'2024-07-31','2024-07-31',NULL,2),(2,NULL,NULL,NULL,'2024-07-31','2024-07-31',NULL,2),(1,NULL,NULL,NULL,'2024-08-01','2024-08-01',NULL,3),(2,NULL,NULL,NULL,'2024-08-01','2024-08-01',NULL,3);
/*!40000 ALTER TABLE `atividades_atribuicoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `atividades_atribuicoes_ead`
--

DROP TABLE IF EXISTS `atividades_atribuicoes_ead`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `atividades_atribuicoes_ead` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDAluno` int NOT NULL,
  `DTEntrega` date NOT NULL,
  `Realizado` int NOT NULL,
  `Feedback` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atividades_atribuicoes_ead`
--

LOCK TABLES `atividades_atribuicoes_ead` WRITE;
/*!40000 ALTER TABLE `atividades_atribuicoes_ead` DISABLE KEYS */;
/*!40000 ALTER TABLE `atividades_atribuicoes_ead` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `atividades_ead`
--

DROP TABLE IF EXISTS `atividades_ead`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `atividades_ead` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDTurma` int NOT NULL,
  `IDDisciplina` int NOT NULL,
  `DTAvaliacao` date NOT NULL,
  `TPConteudo` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `DSAtividade` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `Pontuacao` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atividades_ead`
--

LOCK TABLES `atividades_ead` WRITE;
/*!40000 ALTER TABLE `atividades_ead` DISABLE KEYS */;
/*!40000 ALTER TABLE `atividades_ead` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aulas`
--

DROP TABLE IF EXISTS `aulas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aulas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDTurma` int NOT NULL,
  `DSConteudo` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Estagio` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `DSAula` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL,
  `IDProfessor` int NOT NULL,
  `IDDisciplina` int NOT NULL,
  `INIAula` time NOT NULL,
  `TERAula` time NOT NULL,
  `STAula` int DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aulas`
--

LOCK TABLES `aulas` WRITE;
/*!40000 ALTER TABLE `aulas` DISABLE KEYS */;
INSERT INTO `aulas` VALUES (2,1,'fsdfsf','3º BIM','Aula de Matematica','2024-07-31','2024-07-31',1,1,'07:00:00','07:50:00',1),(3,1,'fsdfdsf','3º BIM','Aula de Portugues','2024-07-31','2024-07-31',2,2,'07:50:00','08:40:00',1),(4,1,'fsdfdsf','3º BIM','dfsdfdsfsdf','2024-08-01','2024-08-01',2,2,'23:02:00','01:02:00',1);
/*!40000 ALTER TABLE `aulas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aulas_ead`
--

DROP TABLE IF EXISTS `aulas_ead`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aulas_ead` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDTurma` int NOT NULL,
  `Descricao_da_Aula` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aulas_ead`
--

LOCK TABLES `aulas_ead` WRITE;
/*!40000 ALTER TABLE `aulas_ead` DISABLE KEYS */;
/*!40000 ALTER TABLE `aulas_ead` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auxiliares`
--

DROP TABLE IF EXISTS `auxiliares`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auxiliares` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDEscola` int NOT NULL,
  `Nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Nascimento` date NOT NULL,
  `Admissao` date NOT NULL,
  `Email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Celular` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `TerminoContrato` date DEFAULT NULL,
  `CEP` varchar(8) COLLATE utf8mb4_general_ci NOT NULL,
  `Rua` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `UF` varchar(2) COLLATE utf8mb4_general_ci NOT NULL,
  `Cidade` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Bairro` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Numero` int DEFAULT NULL,
  `Ativo` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auxiliares`
--

LOCK TABLES `auxiliares` WRITE;
/*!40000 ALTER TABLE `auxiliares` DISABLE KEYS */;
/*!40000 ALTER TABLE `auxiliares` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('diretor@gmail.com|127.0.0.1','i:1;',1722564377),('diretor@gmail.com|127.0.0.1:timer','i:1722564377;',1722564377),('professor@gmail.com|127.0.0.1','i:1;',1722290671),('professor@gmail.com|127.0.0.1:timer','i:1722290671;',1722290671);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendario`
--

DROP TABLE IF EXISTS `calendario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calendario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDOrg` int NOT NULL,
  `INIAno` date NOT NULL,
  `TERAno` date NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calendario`
--

LOCK TABLES `calendario` WRITE;
/*!40000 ALTER TABLE `calendario` DISABLE KEYS */;
INSERT INTO `calendario` VALUES (1,1,'2024-02-01','2024-12-01','2024-07-29','2024-07-29');
/*!40000 ALTER TABLE `calendario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cardapio`
--

DROP TABLE IF EXISTS `cardapio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cardapio` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDEscola` int NOT NULL,
  `Dia` date NOT NULL,
  `Turno` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `Descricao` text COLLATE utf8mb4_general_ci NOT NULL,
  `Foto` text COLLATE utf8mb4_general_ci NOT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cardapio`
--

LOCK TABLES `cardapio` WRITE;
/*!40000 ALTER TABLE `cardapio` DISABLE KEYS */;
INSERT INTO `cardapio` VALUES (1,0,'2024-07-29','Manhã','gfdgdgfgdfg','','2024-07-29','2024-07-29');
/*!40000 ALTER TABLE `cardapio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comentarios_planual`
--

DROP TABLE IF EXISTS `comentarios_planual`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comentarios_planual` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDPlanejamentoAnual` int NOT NULL,
  `IDPedagogo` int NOT NULL,
  `Feedback` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comentarios_planual`
--

LOCK TABLES `comentarios_planual` WRITE;
/*!40000 ALTER TABLE `comentarios_planual` DISABLE KEYS */;
/*!40000 ALTER TABLE `comentarios_planual` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comentarios_plsemanal`
--

DROP TABLE IF EXISTS `comentarios_plsemanal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comentarios_plsemanal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDPlanejamentoSemanal` int NOT NULL,
  `IDPedagogo` int NOT NULL,
  `Feedback` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comentarios_plsemanal`
--

LOCK TABLES `comentarios_plsemanal` WRITE;
/*!40000 ALTER TABLE `comentarios_plsemanal` DISABLE KEYS */;
/*!40000 ALTER TABLE `comentarios_plsemanal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `diretores`
--

DROP TABLE IF EXISTS `diretores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `diretores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Nascimento` date NOT NULL,
  `Admissao` date NOT NULL,
  `Email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Celular` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `TerminoContrato` date DEFAULT NULL,
  `CEP` varchar(8) COLLATE utf8mb4_general_ci NOT NULL,
  `Rua` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `UF` varchar(2) COLLATE utf8mb4_general_ci NOT NULL,
  `Cidade` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Bairro` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Numero` int DEFAULT NULL,
  `IDEscola` int NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diretores`
--

LOCK TABLES `diretores` WRITE;
/*!40000 ALTER TABLE `diretores` DISABLE KEYS */;
INSERT INTO `diretores` VALUES (1,'Diretor 1','1975-08-15','2024-07-28','diretor1@gmail.com','23432423422','2024-11-28','35160208','Avenida Vinte e Seis de Outubro','MG','Ipatinga','Bela Vista',2014,1,'2024-07-28','2024-07-28');
/*!40000 ALTER TABLE `diretores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disciplinas`
--

DROP TABLE IF EXISTS `disciplinas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disciplinas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `NMDisciplina` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `Obrigatoria` varchar(3) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disciplinas`
--

LOCK TABLES `disciplinas` WRITE;
/*!40000 ALTER TABLE `disciplinas` DISABLE KEYS */;
INSERT INTO `disciplinas` VALUES (1,'Matemática','Sim','2024-07-28','2024-07-28'),(2,'Lingua Portuguesa','Sim','2024-07-28','2024-07-28');
/*!40000 ALTER TABLE `disciplinas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dissertativas_ead`
--

DROP TABLE IF EXISTS `dissertativas_ead`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dissertativas_ead` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDAtividade` int NOT NULL,
  `Enunciado` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Resposta` text COLLATE utf8mb4_general_ci,
  `Feedback` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Resultado` int NOT NULL,
  `Total` float NOT NULL,
  `Nota` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dissertativas_ead`
--

LOCK TABLES `dissertativas_ead` WRITE;
/*!40000 ALTER TABLE `dissertativas_ead` DISABLE KEYS */;
/*!40000 ALTER TABLE `dissertativas_ead` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `escolas`
--

DROP TABLE IF EXISTS `escolas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `escolas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDOrg` int NOT NULL,
  `Nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `CEP` varchar(8) COLLATE utf8mb4_general_ci NOT NULL,
  `Rua` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Bairro` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Cidade` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Numero` int NOT NULL,
  `UF` varchar(2) COLLATE utf8mb4_general_ci NOT NULL,
  `Telefone` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `Email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `QTVagas` int NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `escolas`
--

LOCK TABLES `escolas` WRITE;
/*!40000 ALTER TABLE `escolas` DISABLE KEYS */;
INSERT INTO `escolas` VALUES (1,1,'Escola Municipal Levindo Mariano','35162282','Rua Mariano Félix','Bom Jardim','Ipatinga',570,'MG','3138298388','levindomariano@pmi.com.br',597,'2024-07-28 20:38:56','2024-07-28 20:38:56'),(2,1,'Escola Municipal Gente Inocente','35160279','Rua Vanádio','Imbaúbas','Ipatinga',27,'MG','3138298358','genteinocente@pmi.mg.gov.br',499,'2024-07-28 20:40:10','2024-07-28 20:40:10'),(3,1,'Escola Municipal Carlos Drumond de Andrade','35162155','Rua Artur Azevedo','Ideal','Ipatinga',118,'MG','3138298380','carlosdrumong@mg.gov.br',799,'2024-07-28 20:44:26','2024-07-28 20:44:26');
/*!40000 ALTER TABLE `escolas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estoque`
--

DROP TABLE IF EXISTS `estoque`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estoque` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDEscola` int NOT NULL,
  `Quantidade` float NOT NULL,
  `TPUnidade` varchar(2) COLLATE utf8mb4_general_ci NOT NULL,
  `Vencimento` date DEFAULT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `Item` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estoque`
--

LOCK TABLES `estoque` WRITE;
/*!40000 ALTER TABLE `estoque` DISABLE KEYS */;
INSERT INTO `estoque` VALUES (1,1,-31,'UN','2024-08-09','2024-07-29','2024-07-29','gsg'),(2,1,234,'UN','2024-07-30','2024-07-29','2024-07-29','gfgdfg'),(3,1,3244,'UN','2024-07-26','2024-07-29','2024-07-29','sdfsdf');
/*!40000 ALTER TABLE `estoque` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estoque_movimentacao`
--

DROP TABLE IF EXISTS `estoque_movimentacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estoque_movimentacao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDEstoque` int NOT NULL,
  `TPMovimentacao` int NOT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL,
  `Quantidade` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estoque_movimentacao`
--

LOCK TABLES `estoque_movimentacao` WRITE;
/*!40000 ALTER TABLE `estoque_movimentacao` DISABLE KEYS */;
INSERT INTO `estoque_movimentacao` VALUES (1,1,0,'2024-07-29','2024-07-29',35);
/*!40000 ALTER TABLE `estoque_movimentacao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eventos`
--

DROP TABLE IF EXISTS `eventos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eventos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `DSEvento` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `IDEscola` int NOT NULL,
  `Data` date NOT NULL,
  `Inicio` time NOT NULL,
  `Termino` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventos`
--

LOCK TABLES `eventos` WRITE;
/*!40000 ALTER TABLE `eventos` DISABLE KEYS */;
INSERT INTO `eventos` VALUES (1,'fgdfgdgdgf','2024-07-29','2024-07-29',1,'2024-07-29','00:31:00','04:31:00');
/*!40000 ALTER TABLE `eventos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faltas_justificadas`
--

DROP TABLE IF EXISTS `faltas_justificadas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faltas_justificadas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDPessoa` int NOT NULL,
  `Justificativa` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `DTFalta` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faltas_justificadas`
--

LOCK TABLES `faltas_justificadas` WRITE;
/*!40000 ALTER TABLE `faltas_justificadas` DISABLE KEYS */;
/*!40000 ALTER TABLE `faltas_justificadas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faltas_justificadas_profissional`
--

DROP TABLE IF EXISTS `faltas_justificadas_profissional`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faltas_justificadas_profissional` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDPessoa` int NOT NULL,
  `Justificativa` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `DTFalta` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faltas_justificadas_profissional`
--

LOCK TABLES `faltas_justificadas_profissional` WRITE;
/*!40000 ALTER TABLE `faltas_justificadas_profissional` DISABLE KEYS */;
/*!40000 ALTER TABLE `faltas_justificadas_profissional` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedback_transferencias`
--

DROP TABLE IF EXISTS `feedback_transferencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feedback_transferencias` (
  `id` int NOT NULL,
  `Feedback` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `IDTransferencia` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feedback_transferencias`
--

LOCK TABLES `feedback_transferencias` WRITE;
/*!40000 ALTER TABLE `feedback_transferencias` DISABLE KEYS */;
/*!40000 ALTER TABLE `feedback_transferencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ferias_alunos`
--

DROP TABLE IF EXISTS `ferias_alunos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ferias_alunos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `DTInicio` date NOT NULL,
  `DTTermino` date NOT NULL,
  `IDEscola` int NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ferias_alunos`
--

LOCK TABLES `ferias_alunos` WRITE;
/*!40000 ALTER TABLE `ferias_alunos` DISABLE KEYS */;
INSERT INTO `ferias_alunos` VALUES (1,'2024-07-17','2024-07-26',1,'2024-07-29','2024-07-29');
/*!40000 ALTER TABLE `ferias_alunos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ferias_profissionais`
--

DROP TABLE IF EXISTS `ferias_profissionais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ferias_profissionais` (
  `id` int NOT NULL AUTO_INCREMENT,
  `DTInicio` date NOT NULL,
  `DTTermino` date NOT NULL,
  `IDEscola` int NOT NULL,
  `IDProfissional` int NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ferias_profissionais`
--

LOCK TABLES `ferias_profissionais` WRITE;
/*!40000 ALTER TABLE `ferias_profissionais` DISABLE KEYS */;
INSERT INTO `ferias_profissionais` VALUES (1,'2024-07-24','2024-07-29',1,1,'2024-07-29','2024-07-29');
/*!40000 ALTER TABLE `ferias_profissionais` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `frequencia`
--

DROP TABLE IF EXISTS `frequencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `frequencia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDAluno` int NOT NULL,
  `IDAula` int DEFAULT NULL,
  `Presenca` int DEFAULT '0',
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `frequencia`
--

LOCK TABLES `frequencia` WRITE;
/*!40000 ALTER TABLE `frequencia` DISABLE KEYS */;
INSERT INTO `frequencia` VALUES (1,1,1,0,'2024-07-31','2024-07-31'),(2,2,1,0,'2024-07-31','2024-07-31'),(3,1,3,0,'2024-07-31','2024-07-31'),(4,2,3,0,'2024-07-31','2024-07-31'),(5,1,2,0,'2024-07-31','2024-07-31'),(6,2,2,0,'2024-07-31','2024-07-31'),(7,1,4,0,'2024-08-01','2024-08-01');
/*!40000 ALTER TABLE `frequencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `justificativa_alteracoes`
--

DROP TABLE IF EXISTS `justificativa_alteracoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `justificativa_alteracoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDAluno` int NOT NULL,
  `Justificativa` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `justificativa_alteracoes`
--

LOCK TABLES `justificativa_alteracoes` WRITE;
/*!40000 ALTER TABLE `justificativa_alteracoes` DISABLE KEYS */;
/*!40000 ALTER TABLE `justificativa_alteracoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matriculas`
--

DROP TABLE IF EXISTS `matriculas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matriculas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `AnexoRG` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `CResidencia` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `Historico` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `Nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `CPF` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `RG` varchar(9) COLLATE utf8mb4_general_ci NOT NULL,
  `CEP` varchar(8) COLLATE utf8mb4_general_ci NOT NULL,
  `Rua` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Celular` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `UF` varchar(2) COLLATE utf8mb4_general_ci NOT NULL,
  `Cidade` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `BolsaFamilia` int NOT NULL,
  `Alergia` int NOT NULL,
  `Transporte` int NOT NULL,
  `NEE` int NOT NULL,
  `AMedico` int NOT NULL,
  `APsicologico` int NOT NULL,
  `Aprovado` int NOT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL,
  `Nascimento` date NOT NULL,
  `Foto` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `Bairro` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `Numero` int NOT NULL,
  `CDPasta` bigint NOT NULL,
  `RGPaisAnexo` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matriculas`
--

LOCK TABLES `matriculas` WRITE;
/*!40000 ALTER TABLE `matriculas` DISABLE KEYS */;
INSERT INTO `matriculas` VALUES (1,'Proposta.pdf','Planilha-de-Custos CRT-ES.pdf','PLANEJAMENTO HISTÓRIA 6 ANO.pdf','Aluna 1','42342342424','23424344','35160208','Avenida Vinte e Seis de Outubro','fsfsd@gmail.com','23423434244','MG','Ipatinga',1,1,1,1,1,1,1,'2024-07-28','2024-07-28','2024-07-28','aluna-na-biblioteca.png','Bela Vista',2424,42562821820,'termo.bnc.pdf'),(2,'termo.bnc (2).pdf','Propospa Cosmópolis (1).pdf','Proposta.pdf','Aluno 2','35345345344','44354353','35160209','Rua Virginópolis','gdfg@gmail.com','23432432442','MG','Ipatinga',1,0,1,1,0,1,1,'2024-07-28','2024-07-28','2024-07-22','EF1.png','Bela Vista',44,19241598974,'Formalização da Prorrogação da Assinatura Locaweb.pdf');
/*!40000 ALTER TABLE `matriculas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `motoristas`
--

DROP TABLE IF EXISTS `motoristas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `motoristas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDOrganizacao` int NOT NULL,
  `Nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Nascimento` date NOT NULL,
  `Admissao` date NOT NULL,
  `Email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Celular` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `TerminoContrato` date DEFAULT NULL,
  `CEP` varchar(8) COLLATE utf8mb4_general_ci NOT NULL,
  `Rua` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `UF` varchar(2) COLLATE utf8mb4_general_ci NOT NULL,
  `Cidade` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Bairro` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Numero` int DEFAULT NULL,
  `Ativo` int NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `motoristas`
--

LOCK TABLES `motoristas` WRITE;
/*!40000 ALTER TABLE `motoristas` DISABLE KEYS */;
INSERT INTO `motoristas` VALUES (1,1,'motora','2001-03-23','2024-07-29','fsdfsdf@gmail.com','2342343242','2024-08-01','35160208','Avenida Vinte e Seis de Outubro','MG','Ipatinga','Bela Vista',2014,0,'2024-07-29','2024-07-29');
/*!40000 ALTER TABLE `motoristas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notas`
--

DROP TABLE IF EXISTS `notas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notas` (
  `IDAluno` int NOT NULL,
  `IDAtividade` int NOT NULL,
  `Nota` float NOT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notas`
--

LOCK TABLES `notas` WRITE;
/*!40000 ALTER TABLE `notas` DISABLE KEYS */;
INSERT INTO `notas` VALUES (1,1,10,'2024-08-01','2024-08-01'),(2,1,10,'2024-08-01','2024-08-01'),(1,2,10,'2024-08-01','2024-08-01'),(2,2,10,'2024-08-01','2024-08-01'),(1,3,5,'2024-08-01','2024-08-01');
/*!40000 ALTER TABLE `notas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `objetivas_ead`
--

DROP TABLE IF EXISTS `objetivas_ead`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `objetivas_ead` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDAtividade` int NOT NULL,
  `Enunciado` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Opcoes` text COLLATE utf8mb4_general_ci NOT NULL,
  `Correta` varchar(1) COLLATE utf8mb4_general_ci NOT NULL,
  `Resposta` varchar(1) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Feedback` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Total` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `objetivas_ead`
--

LOCK TABLES `objetivas_ead` WRITE;
/*!40000 ALTER TABLE `objetivas_ead` DISABLE KEYS */;
/*!40000 ALTER TABLE `objetivas_ead` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ocorrencias`
--

DROP TABLE IF EXISTS `ocorrencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ocorrencias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDAluno` int NOT NULL,
  `IDProfessor` int NOT NULL,
  `DTOcorrencia` datetime NOT NULL,
  `DSOcorrido` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ocorrencias`
--

LOCK TABLES `ocorrencias` WRITE;
/*!40000 ALTER TABLE `ocorrencias` DISABLE KEYS */;
/*!40000 ALTER TABLE `ocorrencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organizacoes`
--

DROP TABLE IF EXISTS `organizacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `organizacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Organizacao` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `Email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Endereco` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `UF` varchar(2) COLLATE utf8mb4_general_ci NOT NULL,
  `Cidade` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organizacoes`
--

LOCK TABLES `organizacoes` WRITE;
/*!40000 ALTER TABLE `organizacoes` DISABLE KEYS */;
INSERT INTO `organizacoes` VALUES (1,'Prefeitura Municipal de Ipatinga - PMI','educacional@pmi.mg.gov.br','{\"Rua\":\"Avenida Carlos Chagas\",\"Cidade\":\"Ipatinga\",\"Bairro\":\"Cidade Nobre\",\"UF\":\"MG\",\"Numero\":\"825\",\"CEP\":\"35162-359\"}','MG','Ipatinga','2024-07-28 20:32:28','2024-07-28 20:32:28');
/*!40000 ALTER TABLE `organizacoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paradas`
--

DROP TABLE IF EXISTS `paradas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paradas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDRota` int NOT NULL,
  `Nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Hora` time NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paradas`
--

LOCK TABLES `paradas` WRITE;
/*!40000 ALTER TABLE `paradas` DISABLE KEYS */;
INSERT INTO `paradas` VALUES (1,1,'Ponto A2','06:30:00','2024-07-29','2024-07-29');
/*!40000 ALTER TABLE `paradas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paralizacoes`
--

DROP TABLE IF EXISTS `paralizacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paralizacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `DSMotivo` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `IDEscola` int NOT NULL,
  `DTInicio` date NOT NULL,
  `DTTermino` date NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paralizacoes`
--

LOCK TABLES `paralizacoes` WRITE;
/*!40000 ALTER TABLE `paralizacoes` DISABLE KEYS */;
INSERT INTO `paralizacoes` VALUES (1,'fgdfgdgdf',1,'2024-07-29','2024-07-31','2024-07-29','2024-07-29');
/*!40000 ALTER TABLE `paralizacoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedagogos`
--

DROP TABLE IF EXISTS `pedagogos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedagogos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Nascimento` date NOT NULL,
  `Admissao` date NOT NULL,
  `Email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Celular` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `TerminoContrato` date DEFAULT NULL,
  `CEP` varchar(8) COLLATE utf8mb4_general_ci NOT NULL,
  `Rua` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `UF` varchar(2) COLLATE utf8mb4_general_ci NOT NULL,
  `Cidade` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Bairro` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Numero` int DEFAULT NULL,
  `Ativo` int NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedagogos`
--

LOCK TABLES `pedagogos` WRITE;
/*!40000 ALTER TABLE `pedagogos` DISABLE KEYS */;
/*!40000 ALTER TABLE `pedagogos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `planejamentoanual`
--

DROP TABLE IF EXISTS `planejamentoanual`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `planejamentoanual` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDProfessor` int NOT NULL,
  `IDDisciplina` int NOT NULL,
  `IDTurma` int NOT NULL,
  `NMPlanejamento` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `PLConteudos` text COLLATE utf8mb4_general_ci NOT NULL,
  `Aprovado` int NOT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `planejamentoanual`
--

LOCK TABLES `planejamentoanual` WRITE;
/*!40000 ALTER TABLE `planejamentoanual` DISABLE KEYS */;
INSERT INTO `planejamentoanual` VALUES (1,1,1,1,'Matemática','\"{\\\"primeiroBimestre\\\":[{\\\"Conteudo\\\":\\\"m1\\\",\\\"Inicio\\\":\\\"01\\/02\\/2024\\\",\\\"Termino\\\":\\\"01\\/04\\/2024\\\",\\\"Conteudos\\\":[\\\"sdfsf\\\"]}],\\\"segundoBimestre\\\":[{\\\"Conteudo\\\":\\\"m2\\\",\\\"Inicio\\\":\\\"01\\/04\\/2024\\\",\\\"Termino\\\":\\\"01\\/07\\/2024\\\",\\\"Conteudos\\\":[\\\"fsdfdsf\\\"]}],\\\"terceiroBimestre\\\":[{\\\"Conteudo\\\":\\\"m3\\\",\\\"Inicio\\\":\\\"01\\/07\\/2024\\\",\\\"Termino\\\":\\\"01\\/09\\/2024\\\",\\\"Conteudos\\\":[\\\"fsdfsf\\\",\\\"fsddsf\\\"]}],\\\"quartoBimestre\\\":[{\\\"Conteudo\\\":\\\"m4\\\",\\\"Inicio\\\":\\\"01\\/09\\/2024\\\",\\\"Termino\\\":\\\"01\\/12\\/2024\\\",\\\"Conteudos\\\":[\\\"fdsfdsf\\\",\\\"fsdff\\\"]}]}\"',0,'2024-07-31','2024-07-31'),(2,2,2,1,'Planejamento Portugues','\"{\\\"primeiroBimestre\\\":[{\\\"Conteudo\\\":\\\"p1\\\",\\\"Inicio\\\":\\\"01\\/02\\/2024\\\",\\\"Termino\\\":\\\"01\\/04\\/2024\\\",\\\"Conteudos\\\":[\\\"fdsf\\\"]}],\\\"segundoBimestre\\\":[{\\\"Conteudo\\\":\\\"p2\\\",\\\"Inicio\\\":\\\"01\\/04\\/2024\\\",\\\"Termino\\\":\\\"01\\/06\\/2024\\\",\\\"Conteudos\\\":[\\\"sdfdsf\\\"]}],\\\"terceiroBimestre\\\":[{\\\"Conteudo\\\":\\\"p3\\\",\\\"Inicio\\\":\\\"01\\/06\\/2024\\\",\\\"Termino\\\":\\\"01\\/08\\/2024\\\",\\\"Conteudos\\\":[\\\"fsdfdsf\\\"]}],\\\"quartoBimestre\\\":[{\\\"Conteudo\\\":\\\"p4\\\",\\\"Inicio\\\":\\\"01\\/08\\/2024\\\",\\\"Termino\\\":\\\"01\\/10\\/2024\\\",\\\"Conteudos\\\":[\\\"fsdfdsf\\\"]}]}\"',0,'2024-07-31','2024-07-31');
/*!40000 ALTER TABLE `planejamentoanual` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `planejamentosemanal`
--

DROP TABLE IF EXISTS `planejamentosemanal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `planejamentosemanal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDPlanejamentoAnual` int NOT NULL,
  `PLConteudos` text COLLATE utf8mb4_general_ci NOT NULL,
  `INISemana` date NOT NULL,
  `TERSemana` date NOT NULL,
  `Aprovado` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `planejamentosemanal`
--

LOCK TABLES `planejamentosemanal` WRITE;
/*!40000 ALTER TABLE `planejamentosemanal` DISABLE KEYS */;
/*!40000 ALTER TABLE `planejamentosemanal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presenca`
--

DROP TABLE IF EXISTS `presenca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presenca` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDAula` int NOT NULL,
  `IDEscola` int NOT NULL,
  `IDTurma` int NOT NULL,
  `IDProfessor` int NOT NULL,
  `IDAluno` int NOT NULL,
  `Status` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presenca`
--

LOCK TABLES `presenca` WRITE;
/*!40000 ALTER TABLE `presenca` DISABLE KEYS */;
/*!40000 ALTER TABLE `presenca` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `professores`
--

DROP TABLE IF EXISTS `professores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `professores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Nascimento` date NOT NULL,
  `Admissao` date NOT NULL,
  `Email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Celular` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `TerminoContrato` date DEFAULT NULL,
  `CEP` varchar(8) COLLATE utf8mb4_general_ci NOT NULL,
  `Rua` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `UF` varchar(2) COLLATE utf8mb4_general_ci NOT NULL,
  `Cidade` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Bairro` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Numero` int DEFAULT NULL,
  `Ativo` int NOT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `professores`
--

LOCK TABLES `professores` WRITE;
/*!40000 ALTER TABLE `professores` DISABLE KEYS */;
INSERT INTO `professores` VALUES (1,'Professor 1','1980-10-05','2024-07-28','professor1@gmail.com','31933244223','2024-10-16','35160208','Avenida Vinte e Seis de Outubro','MG','Ipatinga','Bela Vista',2014,0,'2024-07-28','2024-07-28'),(2,'Professor 2','2024-08-01','2024-07-31','professor2@gmail.com','31983086235','2024-08-02','35160208','Avenida Vinte e Seis de Outubro','MG','Ipatinga','Bela Vista',2014,0,'2024-07-31','2024-07-31');
/*!40000 ALTER TABLE `professores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `renovacoes`
--

DROP TABLE IF EXISTS `renovacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `renovacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDAluno` int NOT NULL,
  `Aprovado` int NOT NULL,
  `ANO` year NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `Vencimento` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `renovacoes`
--

LOCK TABLES `renovacoes` WRITE;
/*!40000 ALTER TABLE `renovacoes` DISABLE KEYS */;
INSERT INTO `renovacoes` VALUES (1,1,1,2024,'2024-07-28','2024-07-28','2025-01-01'),(2,2,1,2024,'2024-07-28','2024-07-28','2024-10-25');
/*!40000 ALTER TABLE `renovacoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `responsavel`
--

DROP TABLE IF EXISTS `responsavel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `responsavel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDAluno` int NOT NULL,
  `RGPaisAnexo` text COLLATE utf8mb4_general_ci NOT NULL,
  `RGPais` varchar(9) COLLATE utf8mb4_general_ci NOT NULL,
  `NMResponsavel` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `EmailResponsavel` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `CLResponsavel` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `CPFResponsavel` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `responsavel`
--

LOCK TABLES `responsavel` WRITE;
/*!40000 ALTER TABLE `responsavel` DISABLE KEYS */;
INSERT INTO `responsavel` VALUES (1,1,'C:\\Users\\frtec\\AppData\\Local\\Temp\\phpEA27.tmp','23443543','Pai 1','fgfdgdf@gmail.com','23434424243','43534534343','2024-07-28','2024-07-28'),(2,2,'C:\\Users\\frtec\\AppData\\Local\\Temp\\php1A79.tmp','23432424','sdfsfsd@gmail.com','sdfds@gmail.com','23423244242','23424232342','2024-07-28','2024-07-28');
/*!40000 ALTER TABLE `responsavel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reunioes`
--

DROP TABLE IF EXISTS `reunioes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reunioes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDEscola` int NOT NULL,
  `IDTurma` int NOT NULL,
  `Data` date NOT NULL,
  `Inicio` time NOT NULL,
  `Termino` time NOT NULL,
  `DSReunião` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reunioes`
--

LOCK TABLES `reunioes` WRITE;
/*!40000 ALTER TABLE `reunioes` DISABLE KEYS */;
/*!40000 ALTER TABLE `reunioes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rodagem`
--

DROP TABLE IF EXISTS `rodagem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rodagem` (
  `id` int NOT NULL AUTO_INCREMENT,
  `KMInicial` float NOT NULL,
  `KMFinal` float NOT NULL,
  `IDVeiculo` int NOT NULL,
  `IDRota` int NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rodagem`
--

LOCK TABLES `rodagem` WRITE;
/*!40000 ALTER TABLE `rodagem` DISABLE KEYS */;
INSERT INTO `rodagem` VALUES (1,60700,60770,1,1,'2024-07-29','2024-07-29');
/*!40000 ALTER TABLE `rodagem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rotas`
--

DROP TABLE IF EXISTS `rotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rotas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDVeiculo` int NOT NULL,
  `IDMotorista` int NOT NULL,
  `Descricao` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Distancia` float NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `Turno` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `Partida` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Chegada` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `HoraPartida` time NOT NULL,
  `HoraChegada` time NOT NULL,
  `DiasJSON` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rotas`
--

LOCK TABLES `rotas` WRITE;
/*!40000 ALTER TABLE `rotas` DISABLE KEYS */;
INSERT INTO `rotas` VALUES (1,0,1,'Partida do Busão',70,'2024-07-29','2024-07-29','Manhã','Ponto A','Ponto B','06:00:00','08:00:00','[\"Segunda\",\"Terça\",\"Quarta\",\"Quinta\",\"Sexta\"]');
/*!40000 ALTER TABLE `rotas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sabados_letivos`
--

DROP TABLE IF EXISTS `sabados_letivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sabados_letivos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDEscola` int NOT NULL,
  `Data` date NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sabados_letivos`
--

LOCK TABLES `sabados_letivos` WRITE;
/*!40000 ALTER TABLE `sabados_letivos` DISABLE KEYS */;
INSERT INTO `sabados_letivos` VALUES (1,1,'2024-08-03','2024-07-29','2024-07-29');
/*!40000 ALTER TABLE `sabados_letivos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('VjfXN0m5DaCwURgp0bNtHPTErg2G6BKjJnS5jPPx',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoieVVQNTFtaThnSkFQM1c1aEZzWjlzZ1FOd2IyejYzSDFJaDlNenRaOSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM5OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvaW5kZXgucGhwL0VzY29sYXMiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=',1722574950);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suspensos`
--

DROP TABLE IF EXISTS `suspensos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suspensos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDInativo` int NOT NULL,
  `Justificativa` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `INISuspensao` date DEFAULT NULL,
  `TERSuspensao` date DEFAULT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suspensos`
--

LOCK TABLES `suspensos` WRITE;
/*!40000 ALTER TABLE `suspensos` DISABLE KEYS */;
/*!40000 ALTER TABLE `suspensos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `terceirizadas`
--

DROP TABLE IF EXISTS `terceirizadas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `terceirizadas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDOrg` int NOT NULL,
  `Nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `CEP` varchar(8) COLLATE utf8mb4_general_ci NOT NULL,
  `Rua` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Bairro` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Cidade` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Numero` int NOT NULL,
  `UF` varchar(2) COLLATE utf8mb4_general_ci NOT NULL,
  `Telefone` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `Email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `CNPJ` varchar(14) COLLATE utf8mb4_general_ci NOT NULL,
  `Ramo` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `TerminoContrato` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `terceirizadas`
--

LOCK TABLES `terceirizadas` WRITE;
/*!40000 ALTER TABLE `terceirizadas` DISABLE KEYS */;
INSERT INTO `terceirizadas` VALUES (1,1,'motora terceirizado','35160-20','Avenida Vinte e Seis de Outubro','Bela Vista','Ipatinga',2014,'MG','(34) 5 3553','sdfs@gmail.com','34.232.4324/24','Transportes','2024-07-29','2024-07-29','2024-07-29');
/*!40000 ALTER TABLE `terceirizadas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transferencias`
--

DROP TABLE IF EXISTS `transferencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transferencias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDAluno` int NOT NULL,
  `Aprovado` int NOT NULL DEFAULT '0',
  `IDEscolaDestino` int NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `Justificativa` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `IDEscolaOrigem` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transferencias`
--

LOCK TABLES `transferencias` WRITE;
/*!40000 ALTER TABLE `transferencias` DISABLE KEYS */;
/*!40000 ALTER TABLE `transferencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `turmas`
--

DROP TABLE IF EXISTS `turmas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `turmas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDEscola` int NOT NULL,
  `Serie` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `Nome` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `INITurma` time NOT NULL,
  `TERTurma` time NOT NULL,
  `Periodo` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `NotaPeriodo` float DEFAULT NULL,
  `MediaPeriodo` float DEFAULT NULL,
  `TotalAno` float DEFAULT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL,
  `QTRepetencia` int NOT NULL,
  `IDPlanejamento` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `turmas`
--

LOCK TABLES `turmas` WRITE;
/*!40000 ALTER TABLE `turmas` DISABLE KEYS */;
INSERT INTO `turmas` VALUES (1,1,'1º Ano E.FUNDAMENTAL','Sala 1','13:00:00','17:25:00','Bimestral',30,15,100,'2024-07-31','2024-07-28',4,2),(2,1,'2º Ano E.FUNDAMENTAL','Sala 2','13:00:00','17:25:00','Bimestral',30,60,100,'2024-07-28','2024-07-28',4,0);
/*!40000 ALTER TABLE `turmas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `turnos`
--

DROP TABLE IF EXISTS `turnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `turnos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDProfessor` int NOT NULL,
  `IDDisciplina` int NOT NULL,
  `IDTurma` int NOT NULL,
  `INITur` time NOT NULL,
  `TERTur` time NOT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL,
  `DiaSemana` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `turnos`
--

LOCK TABLES `turnos` WRITE;
/*!40000 ALTER TABLE `turnos` DISABLE KEYS */;
INSERT INTO `turnos` VALUES (1,1,1,1,'07:01:00','07:50:00','2024-07-29','2024-07-28','Segunda'),(2,2,2,1,'13:00:00','13:50:00','2024-07-31','2024-07-31','Segunda');
/*!40000 ALTER TABLE `turnos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `id_org` int NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permissoes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tipo` int NOT NULL,
  `IDProfissional` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Max Henrique','maxhenrique308@gmail.com',NULL,0,'$2y$12$1du1SEUwJqUORRZVBo9jS.jmDaV8MtEecWKY3k4sZ/B0Oq9XwEMEu',NULL,NULL,'2024-07-28 23:28:16','2024-07-28 23:28:16',0,NULL),(2,'Secretario de Educação da Ipatinga','secretario@gmail.com',NULL,1,'$2y$12$1du1SEUwJqUORRZVBo9jS.jmDaV8MtEecWKY3k4sZ/B0Oq9XwEMEu',NULL,NULL,'2024-07-28 23:34:01','2024-07-28 23:34:01',2,NULL),(3,'Professor 1','professor1@gmail.com',NULL,1,'$2y$12$1du1SEUwJqUORRZVBo9jS.jmDaV8MtEecWKY3k4sZ/B0Oq9XwEMEu',NULL,NULL,'2024-07-29 01:44:36','2024-07-29 01:44:36',6,1),(4,'Diretor 1','diretor1@gmail.com',NULL,1,'$2y$12$1du1SEUwJqUORRZVBo9jS.jmDaV8MtEecWKY3k4sZ/B0Oq9XwEMEu',NULL,NULL,'2024-07-29 01:46:32','2024-07-29 01:46:32',4,1),(5,'Professor 2','professor2@gmail.com',NULL,1,'$2y$12$1du1SEUwJqUORRZVBo9jS.jmDaV8MtEecWKY3k4sZ/B0Oq9XwEMEu',NULL,NULL,'2024-07-31 20:04:33','2024-07-31 20:04:33',6,2);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `veiculos`
--

DROP TABLE IF EXISTS `veiculos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `veiculos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `IDOrganizacao` int NOT NULL,
  `Nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Marca` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Placa` varchar(7) COLLATE utf8mb4_general_ci NOT NULL,
  `Cor` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `KMAquisicao` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `veiculos`
--

LOCK TABLES `veiculos` WRITE;
/*!40000 ALTER TABLE `veiculos` DISABLE KEYS */;
INSERT INTO `veiculos` VALUES (1,1,'Marcopolo f1','Marcopolo','1159','Preto','2024-07-29','2024-07-29',60700);
/*!40000 ALTER TABLE `veiculos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'freducacional'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-08-02  2:05:21
