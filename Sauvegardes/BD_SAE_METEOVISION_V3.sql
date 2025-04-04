-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 28 mars 2025 à 11:21
-- Version du serveur : 8.3.0
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `meteo`
--

-- --------------------------------------------------------

--
-- Structure de la table `logs`
--

DROP TABLE IF EXISTS `logs`;
CREATE TABLE IF NOT EXISTS `logs` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `action` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `logs`
--

INSERT INTO `logs` (`log_id`, `utilisateur_id`, `action`, `description`, `timestamp`) VALUES
(52, 12, 'connexion', NULL, '2025-01-25 18:31:16'),
(53, 12, 'update', 'Modification des informations de l\'utilisateur', '2025-01-25 18:50:55'),
(54, 12, 'ajout_meteotheque', NULL, '2025-01-25 18:58:17'),
(56, 14, 'connexion', NULL, '2025-01-27 17:58:28'),
(57, 14, 'ajout_meteotheque', NULL, '2025-01-27 17:58:29'),
(58, 14, 'connexion', NULL, '2025-01-28 09:36:41'),
(59, 14, 'ajout_meteotheque', NULL, '2025-01-28 09:36:42'),
(60, 14, 'ajout_meteotheque', NULL, '2025-01-28 09:37:01'),
(61, 58, 'inscription', NULL, '2025-01-28 09:37:21'),
(62, 58, 'ajout_meteotheque', NULL, '2025-01-28 09:37:22'),
(63, 59, 'inscription', NULL, '2025-01-28 09:37:42'),
(64, 59, 'ajout_meteotheque', NULL, '2025-01-28 09:37:43'),
(65, 14, 'connexion', NULL, '2025-01-28 09:38:01'),
(66, 14, 'ajout_meteotheque', NULL, '2025-01-28 09:38:01'),
(67, 58, 'promotion', 'Utilisateur promu au rôle d\'administrateur', '2025-01-28 09:38:41'),
(68, 14, 'ajout_meteotheque', NULL, '2025-01-28 09:38:50'),
(69, 14, 'ajout_meteotheque', NULL, '2025-01-28 09:40:54'),
(70, 14, 'connexion', NULL, '2025-01-28 09:50:20'),
(71, 14, 'ajout_meteotheque', NULL, '2025-01-28 09:50:21'),
(72, 14, 'ajout_meteotheque', NULL, '2025-01-28 09:50:35'),
(73, 14, 'ajout_meteotheque', NULL, '2025-01-28 09:50:45'),
(74, 14, 'ajout_meteotheque', NULL, '2025-01-28 10:11:00'),
(75, 14, 'connexion', NULL, '2025-01-28 10:34:20'),
(76, 14, 'ajout_meteotheque', NULL, '2025-01-28 10:34:20'),
(77, 14, 'ajout_meteotheque', NULL, '2025-01-28 10:34:44'),
(78, 14, 'connexion', NULL, '2025-01-28 10:35:06'),
(79, 14, 'ajout_meteotheque', NULL, '2025-01-28 10:35:07'),
(80, 14, 'ajout_meteotheque', NULL, '2025-01-28 10:35:25'),
(81, 14, 'ajout_meteotheque', NULL, '2025-01-28 10:35:27'),
(82, 14, 'ajout_meteotheque', NULL, '2025-01-28 10:35:36'),
(83, 14, 'ajout_meteotheque', NULL, '2025-01-28 10:40:06'),
(84, 14, 'ajout_meteotheque', NULL, '2025-01-28 10:45:01'),
(85, 14, 'ajout_meteotheque', NULL, '2025-01-28 11:04:01'),
(86, 14, 'ajout_meteotheque', NULL, '2025-01-28 11:04:06'),
(87, 14, 'ajout_meteotheque', NULL, '2025-01-28 11:22:25'),
(88, 14, 'ajout_meteotheque', NULL, '2025-01-28 11:22:53'),
(89, 14, 'ajout_meteotheque', NULL, '2025-01-28 11:22:59'),
(90, 14, 'ajout_meteotheque', NULL, '2025-01-28 11:26:52'),
(91, 59, 'promotion', 'Utilisateur promu au rôle d\'administrateur', '2025-01-28 11:27:11'),
(92, 14, 'ajout_meteotheque', NULL, '2025-01-28 11:27:29'),
(93, 14, 'ajout_meteotheque', NULL, '2025-01-28 11:28:02'),
(94, 14, 'ajout_meteotheque', NULL, '2025-01-28 11:35:04'),
(95, 14, 'connexion', NULL, '2025-03-28 10:03:16'),
(96, 14, 'ajout_meteotheque', NULL, '2025-03-28 10:03:17'),
(97, 14, 'ajout_meteotheque', NULL, '2025-03-28 10:17:05'),
(98, 14, 'ajout_meteotheque', NULL, '2025-03-28 10:17:06'),
(99, 14, 'ajout_meteotheque', NULL, '2025-03-28 10:18:15'),
(100, 14, 'ajout_meteotheque', NULL, '2025-03-28 10:18:15'),
(101, 14, 'ajout_meteotheque', NULL, '2025-03-28 10:44:34');

-- --------------------------------------------------------

--
-- Structure de la table `meteotheques`
--

DROP TABLE IF EXISTS `meteotheques`;
CREATE TABLE IF NOT EXISTS `meteotheques` (
  `meteo_id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `nom_collection` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`meteo_id`),
  KEY `meteotheques_ibfk_1` (`utilisateur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `meteotheques`
--

INSERT INTO `meteotheques` (`meteo_id`, `utilisateur_id`, `nom_collection`, `description`, `date_creation`) VALUES
(50, 46, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 7.7°C, Humidité 78%', '2025-01-25 16:44:25'),
(51, 46, 'Hauts-de-France', 'Carte Interactive => Région Hauts-de-France. Détails: LILLE-LESQUIN: Temp 8.1°C, Humidité 82%', '2025-01-25 16:44:31'),
(52, 46, 'Grand Est', 'Carte Interactive => Région Grand Est. Détails: TROYES-BARBEREY: Temp 10.4°C, Humidité 78%; STRASBOURG-ENTZHEIM: Temp 5.6°C, Humidité 76%; BALE-MULHOUSE: Temp 3.7°C, Humidité 82%; REIMS-PRUNAY: Temp 10.6°C, Humidité 80%; NANCY-OCHEY: Temp 9°C, Humidité 62%', '2025-01-25 16:44:35'),
(53, 46, 'Bretagne', 'Carte Interactive => Région Bretagne. Détails: PLOUMANAC\'H: Temp 11.3°C, Humidité 95%; BELLE ILE-LE TALUT: Temp 12°C, Humidité --%; BREST-GUIPAVAS: Temp 9.3°C, Humidité 96%; RENNES-ST JACQUES: Temp 8.4°C, Humidité 99%', '2025-01-25 16:44:37'),
(54, 12, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 7.7°C, Humidité 78%', '2025-01-25 16:44:46'),
(55, 12, 'Grand Est', 'Carte Thermique => Région Grand Est. Détails: Température Moyenne: 6.1°C', '2025-01-25 16:48:13'),
(56, 12, 'Auvergne-Rhône-Alpes', 'Carte Thermique => Région Auvergne-Rhône-Alpes. Détails: Température Moyenne: 2.8°C', '2025-01-25 16:48:16'),
(57, 12, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 7.7°C, Humidité 78%', '2025-01-25 16:52:55'),
(58, 12, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 7.7°C, Humidité 78%', '2025-01-25 17:16:50'),
(59, 12, 'Guyane', 'Carte Interactive => Région Guyane. Détails: MARIPASOULA: Temp 26.3°C, Humidité 88%; SAINT LAURENT: Temp 28°C, Humidité 89%; SAINT GEORGES: Temp 24.2°C, Humidité 96%; CAYENNE-MATOURY: Temp 24.5°C, Humidité 94%', '2025-01-25 17:16:58'),
(60, 12, 'Guyane', 'Carte Interactive => Région Guyane. Détails: MARIPASOULA: Temp 26.3°C, Humidité 88%; SAINT LAURENT: Temp 28°C, Humidité 89%; SAINT GEORGES: Temp 24.2°C, Humidité 96%; CAYENNE-MATOURY: Temp 24.5°C, Humidité 94%', '2025-01-25 17:16:59'),
(61, 12, 'Centre-Val de Loire', 'Carte Thermique => Région Centre-Val de Loire. Détails: Température Moyenne: 4.4°C', '2025-01-25 17:17:12'),
(62, 12, 'Occitanie', 'Carte Thermique => Région Occitanie. Détails: Température Moyenne: 10.2°C', '2025-01-25 17:17:14'),
(63, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 7.7°C, Humidité 78%', '2025-01-25 17:17:54'),
(64, 14, 'Grand Est', 'Carte Thermique => Région Grand Est. Détails: Température Moyenne: 6.1°C', '2025-01-25 17:23:15'),
(65, 14, 'Corse', 'Carte Thermique => Région Corse. Détails: Température Moyenne: 14.2°C', '2025-01-25 17:23:17'),
(73, 12, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 7.7°C, Humidité 78%', '2025-01-25 17:31:17'),
(74, 12, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 7.7°C, Humidité 78%', '2025-01-25 17:49:52'),
(75, 12, 'Nouvelle-Aquitaine', 'Carte Interactive => Région Nouvelle-Aquitaine. Détails: LIMOGES-BELLEGARDE: Temp 5.5°C, Humidité 80%; MONT-DE-MARSAN: Temp 13.4°C, Humidité 65%; PTE DE CHASSIRON: Temp 10.1°C, Humidité 83%; BORDEAUX-MERIGNAC: Temp 6.3°C, Humidité 89%; POITIERS-BIARD: Temp 11.8°C, Humidité 71%', '2025-01-25 17:49:57'),
(76, 12, 'Bourgogne-Franche-Comté', 'Carte Interactive => Région Bourgogne-Franche-Comté. Détails: DIJON-LONGVIC: Temp 7.4°C, Humidité 71%', '2025-01-25 17:49:59'),
(77, 12, 'Bretagne', 'Carte Thermique => Région Bretagne. Détails: Température Moyenne: 10.0°C', '2025-01-25 17:58:17'),
(78, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ', '2025-01-27 16:58:29'),
(79, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 08:36:42'),
(80, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 08:37:01'),
(81, 58, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 08:37:22'),
(82, 59, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 08:37:43'),
(83, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 08:38:01'),
(84, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 08:38:50'),
(85, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 08:40:54'),
(86, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 08:50:21'),
(87, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 08:50:35'),
(88, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 08:50:45'),
(89, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 09:11:00'),
(90, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 09:34:20'),
(91, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 09:34:44'),
(92, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 09:35:07'),
(93, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 09:35:25'),
(94, 14, 'Normandie', 'Carte Interactive => Région Normandie. Détails: PTE DE LA HAGUE: Temp 8.8°C, Humidité 82%; CAEN-CARPIQUET: Temp 9.3°C, Humidité 72%; ROUEN-BOOS: Temp 8.9°C, Humidité 72%; ALENCON: Temp 7.4°C, Humidité 79%', '2025-01-28 09:35:27'),
(95, 14, 'Bretagne', 'Carte Interactive => Région Bretagne. Détails: PLOUMANAC\'H: Temp 9°C, Humidité 78%; BREST-GUIPAVAS: Temp 8.1°C, Humidité 81%; BELLE ILE-LE TALUT: Temp 11.1°C, Humidité --%', '2025-01-28 09:35:36'),
(96, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 09:40:06'),
(97, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 09:45:01'),
(98, 14, 'Bretagne', 'Carte Interactive => Région Bretagne. Détails: PLOUMANAC\'H: Temp 9°C, Humidité 78%; BREST-GUIPAVAS: Temp 8.1°C, Humidité 81%; BELLE ILE-LE TALUT: Temp 11.1°C, Humidité --%', '2025-01-28 10:04:01'),
(99, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 10:04:06'),
(100, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 10:22:25'),
(101, 14, 'Bretagne', 'Carte Interactive => Région Bretagne. Détails: PLOUMANAC\'H: Temp 9°C, Humidité 78%; BREST-GUIPAVAS: Temp 8.1°C, Humidité 81%; BELLE ILE-LE TALUT: Temp 11.1°C, Humidité --%', '2025-01-28 10:22:53'),
(102, 14, 'Guyane', 'Carte Interactive => Région Guyane. Détails: SAINT GEORGES: Temp 24°C, Humidité 98%; SAINT LAURENT: Temp 23.2°C, Humidité 97%', '2025-01-28 10:22:59'),
(103, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 10:26:52'),
(104, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 10:27:29'),
(105, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 10:28:02'),
(106, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 8.1°C, Humidité 77%', '2025-01-28 10:35:04'),
(107, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 14.3°C, Humidité 46%', '2025-03-28 09:03:17'),
(108, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 14.3°C, Humidité 46%', '2025-03-28 09:17:05'),
(109, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 14.3°C, Humidité 46%', '2025-03-28 09:17:06'),
(110, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 14.3°C, Humidité 46%', '2025-03-28 09:18:15'),
(111, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 14.3°C, Humidité 46%', '2025-03-28 09:18:15'),
(112, 14, 'Île-de-France', 'Carte Interactive => Région Île-de-France. Détails: ORLY: Temp 14.3°C, Humidité 46%', '2025-03-28 09:44:34');

-- --------------------------------------------------------

--
-- Structure de la table `stations`
--

DROP TABLE IF EXISTS `stations`;
CREATE TABLE IF NOT EXISTS `stations` (
  `station_id` int NOT NULL AUTO_INCREMENT,
  `numero` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `latitude` decimal(10,6) NOT NULL,
  `longitude` decimal(10,6) NOT NULL,
  `altitude` int NOT NULL,
  `region` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `departement` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ville` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `code_geo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`station_id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `stations`
--

INSERT INTO `stations` (`station_id`, `numero`, `latitude`, `longitude`, `altitude`, `region`, `departement`, `ville`, `code_geo`) VALUES
(1, '78925', 14.595333, -60.995667, 3, 'Martinique', 'Martinique', 'Le Lamentin', '97213'),
(2, '07110', 48.444167, -4.412000, 94, 'Bretagne', 'Finistère', 'Guipavas', '29075'),
(3, '07481', 45.726500, 5.077833, 235, 'Auvergne-Rhône-Alpes', 'Rhône', 'Colombier-Saugnieu', '69299'),
(4, '07661', 43.079333, 5.940833, 115, 'Provence-Alpes-Côte d\'Azur', 'Var', 'Saint-Mandrier-sur-Mer', '83153'),
(5, '07761', 41.918000, 8.792667, 5, 'Corse', 'Corse-du-Sud', 'Ajaccio', '2a004'),
(6, '07130', 48.068833, -1.734000, 36, 'Bretagne', 'Ille-et-Vilaine', 'Saint-Jacques-de-la-Lande', '35281'),
(7, '07591', 44.565667, 6.502333, 871, 'Provence-Alpes-Côte d\'Azur', 'Hautes-Alpes', 'Embrun', '05046'),
(8, '61980', -20.892500, 55.528667, 8, 'La Réunion', 'La Réunion', 'Sainte-Marie', '97418'),
(9, '61976', -15.887667, 54.520667, 7, 'Inconnu', 'Inconnu', 'Inconnu', 'Inconnu'),
(10, '07280', 47.267833, 5.088333, 219, 'Bourgogne-Franche-Comté', 'Côte-d\'Or', 'Ouges', '21473'),
(11, '07149', 48.716833, 2.384333, 89, 'Île-de-France', 'Essonne', 'Athis-Mons', '91027'),
(12, '07222', 47.150000, -1.608833, 26, 'Pays de la Loire', 'Loire-Atlantique', 'Saint-Aignan-Grandlieu', '44150'),
(13, '07607', 43.909833, -0.500167, 59, 'Nouvelle-Aquitaine', 'Landes', 'Mont-de-Marsan', '40192'),
(14, '07790', 42.540667, 9.485167, 10, 'Corse', 'Haute-Corse', 'Lucciana', '2b148'),
(15, '07747', 42.737167, 2.872833, 42, 'Occitanie', 'Pyrénées-Orientales', 'Perpignan', '66136'),
(16, '07577', 44.581167, 4.733000, 73, 'Auvergne-Rhône-Alpes', 'Drôme', 'Montélimar', '26198'),
(17, '89642', -66.663167, 140.001000, 43, 'Inconnu', 'Inconnu', 'Inconnu', 'Inconnu'),
(18, '07005', 50.136000, 1.834000, 69, 'Hauts-de-France', 'Somme', 'Abbeville', '80001'),
(19, '81408', 3.890667, -51.804667, 6, 'Guyane', 'Guyane', 'Saint-Georges', '97308'),
(20, '61972', -22.344167, 40.340667, 6, 'Inconnu', 'Inconnu', 'Inconnu', 'Inconnu'),
(21, '61996', -37.795167, 77.569167, 27, 'Inconnu', 'Inconnu', 'Inconnu', 'Inconnu'),
(22, '07434', 45.861167, 1.175000, 402, 'Nouvelle-Aquitaine', 'Haute-Vienne', 'Limoges', '87085'),
(23, '07015', 50.570000, 3.097500, 47, 'Hauts-de-France', 'Nord', 'Fretin', '59256'),
(24, '07299', 47.614333, 7.510000, 263, 'Grand Est', 'Haut-Rhin', 'Blotzheim', '68042'),
(25, '07181', 48.581000, 5.959833, 336, 'Grand Est', 'Meurthe-et-Moselle', 'Thuilley-aux-Groseilles', '54523'),
(26, '61997', -46.432500, 51.856667, 146, 'Inconnu', 'Inconnu', 'Inconnu', 'Inconnu'),
(27, '07558', 44.118500, 3.019500, 712, 'Occitanie', 'Aveyron', 'Millau', '12145'),
(28, '07630', 43.621000, 1.378833, 151, 'Occitanie', 'Haute-Garonne', 'Blagnac', '31069'),
(29, '07335', 46.593833, 0.314333, 123, 'Nouvelle-Aquitaine', 'Vienne', 'Poitiers', '86194'),
(30, '78897', 16.264000, -61.516333, 11, 'Guadeloupe', 'Guadeloupe', 'Les Abymes', '97101'),
(31, '07037', 49.383000, 1.181667, 151, 'Normandie', 'Seine-Maritime', 'Boos', '76116'),
(32, '07020', 49.725167, -1.939833, 6, 'Normandie', 'Manche', 'La Hague', '50041'),
(33, '61998', -49.352333, 70.243333, 29, 'Inconnu', 'Inconnu', 'Inconnu', 'Inconnu'),
(34, '07190', 48.549500, 7.640333, 150, 'Grand Est', 'Bas-Rhin', 'Holtzheim', '67212'),
(35, '07627', 43.005333, 1.106833, 414, 'Occitanie', 'Ariège', 'Lorp-Sentaraille', '09289'),
(36, '07027', 49.180000, -0.456167, 67, 'Normandie', 'Calvados', 'Carpiquet', '14137'),
(37, '07643', 43.577000, 3.963167, 2, 'Occitanie', 'Hérault', 'Mauguio', '34154'),
(38, '78922', 14.774500, -60.875333, 26, 'Martinique', 'Martinique', 'La Trinité', '97230'),
(39, '81405', 4.822333, -52.365333, 4, 'Guyane', 'Guyane', 'Matoury', '97307'),
(40, '78890', 16.335000, -61.004000, 27, 'Guadeloupe', 'Guadeloupe', 'La Désirade', '97110'),
(41, '07168', 48.324667, 4.020000, 112, 'Grand Est', 'Aube', 'Barberey-Saint-Sulpice', '10030'),
(42, '07510', 44.830667, -0.691333, 47, 'Nouvelle-Aquitaine', 'Gironde', 'Mérignac', '33281'),
(43, '07650', 43.437667, 5.216000, 9, 'Provence-Alpes-Côte d\'Azur', 'Bouches-du-Rhône', 'Marignane', '13054'),
(44, '07690', 43.648833, 7.209000, 2, 'Provence-Alpes-Côte d\'Azur', 'Alpes-Maritimes', 'Nice', '06088'),
(45, '07240', 47.444500, 0.727333, 108, 'Centre-Val de Loire', 'Indre-et-Loire', 'Parçay-Meslay', '37179'),
(46, '07255', 47.059167, 2.359833, 161, 'Centre-Val de Loire', 'Cher', 'Bourges', '18033'),
(47, '07139', 48.445500, 0.110167, 143, 'Normandie', 'Orne', 'Cerisé', '61077'),
(48, '81415', 3.640167, -54.028333, 106, 'Guyane', 'Guyane', 'Maripasoula', '97353'),
(49, '07117', 48.825833, -3.473167, 55, 'Bretagne', 'Côtes-d\'Armor', 'Perros-Guirec', '22168'),
(50, '61968', -11.582667, 47.289667, 3, 'Inconnu', 'Inconnu', 'Inconnu', 'Inconnu'),
(51, '07207', 47.294333, -3.218333, 34, 'Bretagne', 'Morbihan', 'Bangor', '56009'),
(52, '78894', 17.901500, -62.852167, 44, 'Inconnu', 'Inconnu', 'Inconnu', 'Inconnu'),
(53, '07314', 46.046833, -1.411500, 11, 'Nouvelle-Aquitaine', 'Charente-Maritime', 'Saint-Denis-d\'Oléron', '17323'),
(54, '67005', -12.805500, 45.282833, 7, 'Mayotte', 'Mayotte', 'Pamandzi', '97615'),
(55, '07072', 49.209667, 4.155333, 95, 'Grand Est', 'Marne', 'Prunay', '51449'),
(56, '07471', 45.074500, 3.764000, 833, 'Auvergne-Rhône-Alpes', 'Haute-Loire', 'Chaspuzac', '43062'),
(57, '07460', 45.786833, 3.149333, 331, 'Auvergne-Rhône-Alpes', 'Puy-de-Dôme', 'Clermont-Ferrand', '63113'),
(58, '07535', 44.745000, 1.396667, 260, 'Occitanie', 'Lot', 'Gourdon', '46127'),
(59, '81401', 5.485500, -54.031667, 5, 'Guyane', 'Guyane', 'Saint-Laurent-du-Maroni', '97311'),
(60, '71805', 46.766333, -56.179167, 21, 'Inconnu', 'Inconnu', 'Inconnu', 'Inconnu'),
(61, '61970', -17.054667, 42.712000, 9, 'Inconnu', 'Inconnu', 'Inconnu', 'Inconnu');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `utilisateur_id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `prenom` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mot_de_passe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `role` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `etat_compte` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`utilisateur_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `email_2` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`utilisateur_id`, `nom`, `prenom`, `email`, `mot_de_passe`, `date_creation`, `role`, `etat_compte`) VALUES
(12, 'ALVES MIRANDA', 'Hugo', 'fr.hugoalves@gmail.com', '$2y$10$4VdhrkV3pUsazZf9zBBfw.iwMWn30S5hO2/rXmI9jxkGaQJiFn36u', '2025-01-13 15:14:26', 'admin', 'en_attente'),
(14, 'RAYAN', 'Rayan', 'rayan@upec.fr', '$2y$10$5hqlMQFJ1vBWPa5bFkL9XO9u.LW39k3SYSJ8.aqO93MnEEHJNsrsO', '2025-01-18 18:36:19', 'admin', 'en_attente'),
(46, 'LECLERC', 'Charles', 'charles.lcrc@16', '$2y$10$2TrksAiiymp8NoDZ7wDxBuiaPubVMRxj6rZf0hLfB0N6Lb0IW7PzK', '2025-01-24 08:49:00', 'admin', 'en_attente'),
(52, 'LAUDA', 'Niki', 'niki.lauda@4wc.aus', '$2y$10$kmazn9dhI8tCShZ/jlZA6OEfztgYFKG1TX/94dS75Ael4imku9lAO', '2025-01-25 17:25:41', 'admin', 'en_attente'),
(58, 'daabak ', 'ilyes', 'dabak@gmail.com', '$2y$10$fUf1K0FWdtcxIMuN1JFX9u4iKycNAOTxz3Jv.zmKr4hlXSq6wm8V.', '2025-01-28 08:37:21', 'admin', 'en_attente'),
(59, 'mohamed', 'ridwan', 'ridwan@upec.fr', '$2y$10$Z.PhzA27FCk330FFe2FxmO8V6Qk25Kn0NX3WZM9Of7OCOneI4046q', '2025-01-28 08:37:42', 'admin', 'en_attente');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`utilisateur_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `meteotheques`
--
ALTER TABLE `meteotheques`
  ADD CONSTRAINT `meteotheques_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`utilisateur_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
