-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 11 juil. 2026 à 15:40
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `hospis`
--

-- --------------------------------------------------------

--
-- Structure de la table `absence`
--

DROP TABLE IF EXISTS `absence`;
CREATE TABLE IF NOT EXISTS `absence` (
  `id_absence` int NOT NULL AUTO_INCREMENT,
  `id_personel` int NOT NULL,
  `motif` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `date` datetime NOT NULL,
  `statut` enum('accepted','modified','cancel') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_absence`),
  KEY `Absence_id_personel_foreign` (`id_personel`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `absence`
--

INSERT INTO `absence` (`id_absence`, `id_personel`, `motif`, `date`, `statut`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'sleeping sickness', '2026-05-25 00:00:00', 'accepted', '2026-05-25 12:15:48', '2026-05-25 12:15:48', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `consultation`
--

DROP TABLE IF EXISTS `consultation`;
CREATE TABLE IF NOT EXISTS `consultation` (
  `id_consult` int NOT NULL AUTO_INCREMENT,
  `motif` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `symptoms` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `diagnois` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `observation` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `recommendation` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `id_medicalAct` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_consult`),
  KEY `consultation_id_medicalAct_foreign` (`id_medicalAct`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `consultation`
--

INSERT INTO `consultation` (`id_consult`, `motif`, `symptoms`, `diagnois`, `observation`, `recommendation`, `id_medicalAct`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'fever', 'headache,stomachache', 'malaria', 'dangerous case', 'drink water', 7, '2026-06-18 19:47:43', '2026-06-26 00:38:44', '2026-06-26 00:38:44'),
(2, 'fever', 'fever', 'fever', 'fever', 'feverrrrrrrrrr', 9, '2026-06-19 02:03:23', '2026-06-30 19:27:40', '2026-06-30 19:27:40'),
(3, 'fievre', 'elle parle trop,dera', 'folle', 'vraimenttttttttttttt', 'qu\'elle', 10, '2026-06-19 10:28:43', '2026-06-19 10:28:43', NULL),
(4, 'maux de tete', 'diarrahe', 'paludisme', 'fatigue', 'au revoir', 7, '2026-06-25 19:43:29', '2026-06-25 19:43:29', NULL),
(5, 'frefref', 'frvdvgfv', 'bfdbfg', 'redvfbgf', 'bsfvdbgf', 7, '2026-06-25 23:02:45', '2026-06-25 23:02:45', NULL),
(6, 'dfbxxfg', 'svfgb ', 'bgf gbgfb ', 'bgxfb', 'bgbbg', 7, '2026-06-25 23:48:10', '2026-06-25 23:48:10', NULL),
(7, 'jbbkjbk', 'kjb j n', 'gvhjbhj', 'hvdfcgh', 'kjbyughj', 7, '2026-06-25 23:51:58', '2026-06-26 00:39:05', '2026-06-26 00:39:05'),
(8, 'hbjjjjjjjj', 'hgghvvgg', 'ggggggggggggggggggg', 'hvgh hb', 'ggggv', 7, '2026-06-25 23:52:45', '2026-06-26 00:38:57', '2026-06-26 00:38:57'),
(9, 'wfvxfbxfgb', 'bgfxbfgx', 'bgfxg', 'bfgxg', 'bgfxgx', 7, '2026-06-25 23:54:42', '2026-06-26 00:38:51', '2026-06-26 00:38:51'),
(10, 'eeeeee', 'eeeee', 'eeeeee', 'eeeeeeee', 'ooooooo', 19, '2026-06-26 00:46:26', '2026-06-26 00:46:59', NULL),
(11, 'douleurs , maux de v', 'vomis,toussse', 'paludisnme plus tyho', 'verre', 'avoir beaucoup repos', 20, '2026-06-26 14:47:34', '2026-06-26 14:47:34', NULL),
(12, 'maux de tete', 'tous,vomissement', 'paludisme', 'terrible', 'dormir', 23, '2026-06-30 19:50:12', '2026-06-30 20:05:23', '2026-06-30 20:05:23'),
(13, 'maux de tete,mal de ', 'joue trop,', 'verre', 'c\'est terrible', 'boire beaucoup d\'eau', 25, '2026-07-01 13:01:55', '2026-07-01 13:01:55', NULL),
(14, 'palud', 'vomissement', 'desole', 'yeux vert', 'rienn', 7, '2026-07-09 17:53:12', '2026-07-09 17:53:12', NULL),
(15, 'paludd', 'vomissement', 'eeeeeeee', 'eeeeeeeeee', 'eeeeeeee', 7, '2026-07-11 05:39:24', '2026-07-11 05:39:24', NULL),
(16, 'paludd', 'vomissement', 'eeeeeeee', 'eeeeeeeeee', 'eeeeeeee', 7, '2026-07-11 05:40:12', '2026-07-11 05:40:12', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `function`
--

DROP TABLE IF EXISTS `function`;
CREATE TABLE IF NOT EXISTS `function` (
  `id_function` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('active','desactivated') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_function`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `function`
--

INSERT INTO `function` (`id_function`, `name`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 'nurse', 'catering for patient', 'active', '2026-06-10 09:06:20', '2026-06-10 09:06:20', NULL),
(3, 'Major', 'System Controller', 'active', '2026-06-09 12:32:02', '2026-06-09 12:32:02', NULL),
(5, 'doctor', 'consult and prescrib', 'active', '2026-06-10 09:06:48', '2026-06-10 09:06:48', NULL),
(6, 'headNurse', 'coordinate the nursi', 'active', '2026-06-10 09:07:15', '2026-06-10 09:07:15', NULL),
(7, 'pharmacist', 'sell products to pat', 'active', '2026-06-10 09:07:38', '2026-06-10 09:07:38', NULL),
(8, 'Midwife', 'neonatal care', 'active', '2026-06-10 14:51:16', '2026-06-10 14:51:16', NULL),
(9, 'Pharmacienne', 'Vendre les produit', 'active', '2026-07-01 13:16:53', '2026-07-01 13:16:53', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `group`
--

DROP TABLE IF EXISTS `group`;
CREATE TABLE IF NOT EXISTS `group` (
  `id_group` int NOT NULL AUTO_INCREMENT,
  `name_group` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `type_group` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `working_days` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `groupaffecter`
--

DROP TABLE IF EXISTS `groupaffecter`;
CREATE TABLE IF NOT EXISTS `groupaffecter` (
  `id_groupAff` int NOT NULL AUTO_INCREMENT,
  `id_group` int NOT NULL,
  `id_personel` int NOT NULL,
  `month` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `year` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_groupAff`),
  KEY `groupAffecter_id_personel_foreign` (`id_personel`),
  KEY `groupAffecter_id_group_foreign` (`id_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `medicalact`
--

DROP TABLE IF EXISTS `medicalact`;
CREATE TABLE IF NOT EXISTS `medicalact` (
  `id_medicalAct` int NOT NULL AUTO_INCREMENT,
  `type_act` enum('parametre','consultation') COLLATE utf8mb4_general_ci NOT NULL,
  `date_act` date NOT NULL,
  `status` enum('en_attente','attente_consultation','consulte') COLLATE utf8mb4_general_ci DEFAULT 'en_attente',
  `id_personel` int NOT NULL,
  `id_patient` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_medicalAct`),
  KEY `MedicalAct_id_personel_foreign` (`id_personel`),
  KEY `MedicalAct_id_patient_foreign` (`id_patient`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `medicalact`
--

INSERT INTO `medicalact` (`id_medicalAct`, `type_act`, `date_act`, `status`, `id_personel`, `id_patient`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'parametre', '2026-06-18', 'en_attente', 5, 1, '2026-06-18 02:32:51', '2026-06-18 02:32:51', NULL),
(9, 'parametre', '2026-06-19', 'en_attente', 5, 3, '2026-06-19 02:00:57', '2026-06-19 02:00:57', NULL),
(8, 'consultation', '2026-06-18', 'en_attente', 5, 1, '2026-06-18 19:54:40', '2026-06-18 19:54:40', NULL),
(7, 'parametre', '2026-06-18', 'attente_consultation', 5, 1, '2026-06-18 08:09:00', '2026-07-11 06:01:20', NULL),
(6, 'parametre', '2026-06-18', 'en_attente', 5, 1, '2026-06-18 08:09:00', '2026-06-18 08:09:00', NULL),
(10, 'parametre', '2026-06-19', 'en_attente', 5, 5, '2026-06-19 10:15:22', '2026-06-19 10:15:22', NULL),
(11, 'parametre', '2026-06-19', 'en_attente', 5, 5, '2026-06-19 12:40:19', '2026-06-19 12:40:19', NULL),
(12, 'parametre', '2026-06-19', 'en_attente', 5, 6, '2026-06-19 15:45:13', '2026-06-19 15:45:13', NULL),
(13, 'parametre', '2026-06-25', 'en_attente', 5, 1, '2026-06-25 09:27:04', '2026-06-25 09:27:04', NULL),
(14, 'parametre', '2026-06-25', 'en_attente', 5, 1, '2026-06-25 09:31:24', '2026-06-25 09:31:24', NULL),
(15, 'parametre', '2026-06-25', 'en_attente', 5, 1, '2026-06-25 09:57:17', '2026-06-25 09:57:17', NULL),
(16, 'parametre', '2026-06-25', 'en_attente', 5, 1, '2026-06-25 15:17:23', '2026-06-25 15:17:23', NULL),
(17, 'parametre', '2026-06-26', 'en_attente', 5, 6, '2026-06-26 00:41:17', '2026-06-26 00:41:17', NULL),
(18, 'parametre', '2026-06-26', 'en_attente', 5, 5, '2026-06-26 00:41:49', '2026-06-26 00:41:49', NULL),
(19, 'parametre', '2026-06-26', 'en_attente', 5, 5, '2026-06-26 00:44:30', '2026-06-26 00:44:30', NULL),
(20, 'parametre', '2026-06-26', 'en_attente', 5, 7, '2026-06-26 14:45:26', '2026-06-26 14:45:26', NULL),
(27, 'parametre', '2026-07-09', 'en_attente', 5, 1, '2026-07-09 17:00:45', '2026-07-09 17:00:45', NULL),
(26, 'consultation', '2026-07-07', 'en_attente', 5, 8, '2026-07-07 21:11:34', '2026-07-07 21:11:34', NULL),
(23, 'parametre', '2026-06-30', 'en_attente', 5, 7, '2026-06-30 19:49:05', '2026-06-30 19:49:05', NULL),
(25, 'parametre', '2026-07-01', 'en_attente', 5, 8, '2026-07-01 12:57:01', '2026-07-01 12:57:01', NULL),
(28, 'parametre', '2026-07-09', 'en_attente', 5, 1, '2026-07-09 17:00:46', '2026-07-09 17:00:46', NULL),
(29, 'parametre', '2026-07-09', 'en_attente', 5, 1, '2026-07-09 17:00:46', '2026-07-09 17:00:46', NULL),
(30, 'parametre', '2026-07-09', 'en_attente', 5, 1, '2026-07-09 17:02:40', '2026-07-09 17:02:40', NULL),
(31, 'parametre', '2026-07-09', 'en_attente', 5, 1, '2026-07-09 18:23:04', '2026-07-09 18:23:04', NULL),
(32, 'parametre', '2026-07-10', 'en_attente', 13, 9, '2026-07-10 11:43:33', '2026-07-10 11:43:33', NULL),
(33, 'parametre', '2026-07-11', 'en_attente', 5, 1, '2026-07-11 03:31:29', '2026-07-11 03:31:29', NULL),
(34, 'parametre', '2026-07-11', 'en_attente', 5, 1, '2026-07-11 06:01:18', '2026-07-11 06:01:18', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2026-05-10-143047', 'App\\Database\\Migrations\\User', 'default', 'App', 1779703259, 1),
(2, '2026-05-10-143601', 'App\\Database\\Migrations\\Patient', 'default', 'App', 1779703259, 1),
(3, '2026-05-10-145543', 'App\\Database\\Migrations\\MedicalAct', 'default', 'App', 1779703260, 1),
(4, '2026-05-10-154206', 'App\\Database\\Migrations\\VitalSigns', 'default', 'App', 1779703260, 1),
(5, '2026-05-10-155445', 'App\\Database\\Migrations\\Consultation', 'default', 'App', 1779703260, 1),
(6, '2026-05-10-160249', 'App\\Database\\Migrations\\Prescribtion', 'default', 'App', 1779703261, 1),
(7, '2026-05-10-161840', 'App\\Database\\Migrations\\Group', 'default', 'App', 1779703261, 1),
(8, '2026-05-10-164127', 'App\\Database\\Migrations\\GroupAffecter', 'default', 'App', 1779703262, 1),
(9, '2026-05-10-164550', 'App\\Database\\Migrations\\Absence', 'default', 'App', 1779703262, 1),
(10, '2026-05-12-202754', 'App\\Database\\Migrations\\Substitution', 'default', 'App', 1779703262, 1),
(11, '2026-05-14-144721', 'App\\Database\\Migrations\\functions', 'default', 'App', 1779703263, 1),
(12, '2026-05-14-144809', 'App\\Database\\Migrations\\Personel', 'default', 'App', 1779703264, 1),
(13, '2026-05-26-144521', 'App\\Database\\Migrations\\Stock', 'default', 'App', 1783013293, 2),
(14, '2026-05-26-144532', 'App\\Database\\Migrations\\Product', 'default', 'App', 1783013294, 2),
(15, '2026-05-26-144551', 'App\\Database\\Migrations\\Purchase', 'default', 'App', 1783013294, 2),
(16, '2026-05-26-144627', 'App\\Database\\Migrations\\StockMovement', 'default', 'App', 1783013294, 2),
(17, '2026-07-02-145326', 'App\\Database\\Migrations\\ProductLine', 'default', 'App', 1783013294, 2),
(18, '2026-07-02-150221', 'App\\Database\\Migrations\\PurchaseLine', 'default', 'App', 1783013394, 3),
(19, '2026-07-07-205736', 'App\\Database\\Migrations\\AddQuantityAndFixMovementDateToStockMovement', 'default', 'App', 1783457889, 4),
(20, '2026-07-08-085926', 'App\\Database\\Migrations\\PurchaseLine', 'default', 'App', 1783501347, 5),
(21, '2026-07-08-090019', 'App\\Database\\Migrations\\ProductLine', 'default', 'App', 1783501347, 5),
(22, '2026-07-10-115550', 'App\\Database\\Migrations\\AddStatusToMedicalAct', 'default', 'App', 1783684577, 6);

-- --------------------------------------------------------

--
-- Structure de la table `patient`
--

DROP TABLE IF EXISTS `patient`;
CREATE TABLE IF NOT EXISTS `patient` (
  `id_patient` int NOT NULL AUTO_INCREMENT,
  `emergency_number` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `id_user` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_patient`),
  KEY `patient_id_user_foreign` (`id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `patient`
--

INSERT INTO `patient` (`id_patient`, `emergency_number`, `id_user`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '677673994', 6, '2026-06-10 10:27:26', '2026-06-16 18:41:01', NULL),
(9, '677788767', 21, '2026-07-10 11:27:31', '2026-07-10 11:27:31', NULL),
(3, '111111111', 8, '2026-06-11 08:36:32', '2026-06-15 11:37:23', NULL),
(5, '670239313', 13, '2026-06-19 10:14:51', '2026-06-19 10:14:51', NULL),
(6, '123456789', 14, '2026-06-19 15:44:41', '2026-06-19 15:44:41', NULL),
(7, '677673245', 16, '2026-06-26 14:44:48', '2026-06-26 14:44:48', NULL),
(8, '654144138', 17, '2026-07-01 12:56:50', '2026-07-01 12:56:50', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `personel`
--

DROP TABLE IF EXISTS `personel`;
CREATE TABLE IF NOT EXISTS `personel` (
  `id_personel` int NOT NULL AUTO_INCREMENT,
  `staff_code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `id_user` int NOT NULL,
  `id_function` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_personel`),
  KEY `personel_id_user_foreign` (`id_user`),
  KEY `personel_id_function_foreign` (`id_function`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `personel`
--

INSERT INTO `personel` (`id_personel`, `staff_code`, `id_user`, `id_function`, `created_at`, `updated_at`, `deleted_at`) VALUES
(5, 'DOC008', 5, 3, '2026-06-09 12:34:32', '2026-06-09 12:34:32', NULL),
(4, 'DOC001', 4, 1, '2026-06-09 12:26:40', '2026-06-09 12:26:40', NULL),
(11, 'PH1234', 18, 9, '2026-07-01 13:18:09', '2026-07-01 13:18:09', NULL),
(14, 'DOC1234', 22, 5, '2026-07-10 12:26:53', '2026-07-10 12:26:53', NULL),
(12, 'DOC123', 19, 5, '2026-07-02 21:42:36', '2026-07-02 21:42:36', NULL),
(13, 'IN123', 20, 4, '2026-07-09 20:40:44', '2026-07-09 20:40:44', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `prescrib`
--

DROP TABLE IF EXISTS `prescrib`;
CREATE TABLE IF NOT EXISTS `prescrib` (
  `id_presc` int NOT NULL AUTO_INCREMENT,
  `date_presc` date NOT NULL,
  `instructions` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `id_consult` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_presc`),
  KEY `prescrib_id_consult_foreign` (`id_consult`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `prescrib`
--

INSERT INTO `prescrib` (`id_presc`, `date_presc`, `instructions`, `id_consult`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '2026-07-09', 'paracetamol 3fois pa', 3, '2026-07-09 19:14:35', '2026-07-09 19:14:35', NULL),
(2, '2026-07-09', 'amoxicilline a prend', 4, '2026-07-09 19:36:49', '2026-07-09 19:36:49', NULL),
(3, '2026-07-11', 'wwwwwwwwwwwwwwwwwwww', 15, '2026-07-11 05:39:47', '2026-07-11 05:39:47', NULL),
(4, '2026-07-11', 'paracetamol,2x pr jo', 16, '2026-07-11 05:41:37', '2026-07-11 05:41:37', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `id_product` int NOT NULL AUTO_INCREMENT,
  `name_product` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `minimum_quantity` int NOT NULL,
  `price` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_product`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `product`
--

INSERT INTO `product` (`id_product`, `name_product`, `description`, `minimum_quantity`, `price`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'paracetamol', 'ca soigne tous', 10, 100, '2026-07-02 21:11:38', '2026-07-02 21:11:38', NULL),
(3, 'dafalgan', 'efficace', 13, 300, '2026-07-02 21:32:02', '2026-07-02 21:32:02', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `productpres`
--

DROP TABLE IF EXISTS `productpres`;
CREATE TABLE IF NOT EXISTS `productpres` (
  `id_ProductPres` int NOT NULL AUTO_INCREMENT,
  `id_presc` int NOT NULL,
  `id_product` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_ProductPres`),
  KEY `ProductPres_id_presc_foreign` (`id_presc`),
  KEY `ProductPres_id_product_foreign` (`id_product`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `purchase`
--

DROP TABLE IF EXISTS `purchase`;
CREATE TABLE IF NOT EXISTS `purchase` (
  `id_purchase` int NOT NULL AUTO_INCREMENT,
  `final_amount` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_purchase`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `purchase`
--

INSERT INTO `purchase` (`id_purchase`, `final_amount`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 600, '2026-07-08 11:46:44', '2026-07-08 11:46:44', NULL),
(2, 600, '2026-07-08 11:51:11', '2026-07-08 11:51:11', NULL),
(3, 600, '2026-07-08 11:52:08', '2026-07-08 11:52:08', NULL),
(4, 600, '2026-07-08 11:53:57', '2026-07-08 11:53:57', NULL),
(5, 600, '2026-07-08 11:53:59', '2026-07-08 11:53:59', NULL),
(6, 600, '2026-07-08 11:54:08', '2026-07-08 11:54:08', NULL),
(7, 600, '2026-07-08 11:59:31', '2026-07-08 11:59:31', NULL),
(8, 600, '2026-07-08 11:59:33', '2026-07-08 11:59:33', NULL),
(9, 600, '2026-07-08 11:59:51', '2026-07-08 11:59:51', NULL),
(10, 600, '2026-07-08 12:02:38', '2026-07-08 12:02:38', NULL),
(11, 5000, '2026-07-08 13:15:34', '2026-07-08 13:15:34', NULL),
(12, 5000, '2026-07-08 13:16:55', '2026-07-08 13:16:55', NULL),
(13, 5000, '2026-07-08 13:17:47', '2026-07-08 13:17:47', NULL),
(14, 5000, '2026-07-08 13:19:24', '2026-07-08 13:19:24', NULL),
(15, 5000, '2026-07-08 13:21:28', '2026-07-08 13:21:28', NULL),
(16, 5000, '2026-07-08 13:30:10', '2026-07-08 13:30:10', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `purchasel`
--

DROP TABLE IF EXISTS `purchasel`;
CREATE TABLE IF NOT EXISTS `purchasel` (
  `id_purchaseL` int NOT NULL AUTO_INCREMENT,
  `id_product` int NOT NULL,
  `id_purchase` int NOT NULL,
  `id_stockMvt` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_purchaseL`),
  KEY `PurchaseL_id_purchase_foreign` (`id_purchase`),
  KEY `PurchaseL_id_stockMvt_foreign` (`id_stockMvt`),
  KEY `PurchaseL_id_product_foreign` (`id_product`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `purchasel`
--

INSERT INTO `purchasel` (`id_purchaseL`, `id_product`, `id_purchase`, `id_stockMvt`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 15, 14, '2026-07-08 13:21:28', '2026-07-08 13:21:28', NULL),
(2, 1, 16, 15, '2026-07-08 13:30:10', '2026-07-08 13:30:10', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `stock`
--

DROP TABLE IF EXISTS `stock`;
CREATE TABLE IF NOT EXISTS `stock` (
  `id_stock` int NOT NULL AUTO_INCREMENT,
  `name_stock` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `expiry_date` date NOT NULL,
  `quantity_available` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_stock`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `stock`
--

INSERT INTO `stock` (`id_stock`, `name_stock`, `expiry_date`, `quantity_available`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'amoxiclline stock', '0000-00-00', '10', '2026-07-06 12:38:38', '2026-07-06 12:38:38', NULL),
(3, 'paracetamol stock', '2026-12-01', '0', '2026-07-07 21:08:12', '2026-07-08 11:54:08', NULL),
(4, 'stock dafalgan', '2030-12-30', '11', '2026-07-07 22:28:40', '2026-07-07 22:28:40', NULL),
(5, 'paracetamol stock', '2027-12-12', '1', '2026-07-07 22:29:22', '2026-07-08 13:30:10', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `stockmovement`
--

DROP TABLE IF EXISTS `stockmovement`;
CREATE TABLE IF NOT EXISTS `stockmovement` (
  `id_stockMvt` int NOT NULL AUTO_INCREMENT,
  `movement_type` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` int DEFAULT NULL,
  `id_stock` int NOT NULL,
  `id_product` int NOT NULL,
  `movement_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_stockMvt`),
  KEY `StockMovement_id_stock_foreign` (`id_stock`),
  KEY `StockMovement_id_product_foreign` (`id_product`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `stockmovement`
--

INSERT INTO `stockmovement` (`id_stockMvt`, `movement_type`, `quantity`, `id_stock`, `id_product`, `movement_date`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'entree', 10, 0, 1, '2026-07-07', '2026-07-07 21:03:31', '2026-07-07 21:03:31', NULL),
(2, 'entree', 10, 3, 1, '2026-07-07', '2026-07-07 21:08:12', '2026-07-07 21:08:12', NULL),
(3, 'entree', 11, 4, 3, '2026-07-07', '2026-07-07 22:28:40', '2026-07-07 22:28:40', NULL),
(4, 'entree', 20, 5, 1, '2026-07-07', '2026-07-07 22:29:22', '2026-07-07 22:29:22', NULL),
(5, 'sortie', 3, 3, 1, '2026-07-08', '2026-07-08 11:52:08', '2026-07-08 11:52:08', NULL),
(6, 'sortie', 3, 3, 1, '2026-07-08', '2026-07-08 11:53:57', '2026-07-08 11:53:57', NULL),
(7, 'sortie', 3, 3, 1, '2026-07-08', '2026-07-08 11:53:59', '2026-07-08 11:53:59', NULL),
(8, 'sortie', 1, 3, 1, '2026-07-08', '2026-07-08 11:54:08', '2026-07-08 11:54:08', NULL),
(9, 'sortie', 3, 5, 1, '2026-07-08', '2026-07-08 11:59:31', '2026-07-08 11:59:31', NULL),
(10, 'sortie', 3, 5, 1, '2026-07-08', '2026-07-08 11:59:33', '2026-07-08 11:59:33', NULL),
(11, 'sortie', 3, 5, 1, '2026-07-08', '2026-07-08 11:59:51', '2026-07-08 11:59:51', NULL),
(12, 'sortie', 3, 5, 1, '2026-07-08', '2026-07-08 12:02:38', '2026-07-08 12:02:38', NULL),
(13, 'sortie', 2, 5, 1, '2026-07-08', '2026-07-08 13:19:25', '2026-07-08 13:19:25', NULL),
(14, 'sortie', 2, 5, 1, '2026-07-08', '2026-07-08 13:21:28', '2026-07-08 13:21:28', NULL),
(15, 'sortie', 3, 5, 1, '2026-07-08', '2026-07-08 13:30:10', '2026-07-08 13:30:10', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `substitution`
--

DROP TABLE IF EXISTS `substitution`;
CREATE TABLE IF NOT EXISTS `substitution` (
  `id_subs` int NOT NULL AUTO_INCREMENT,
  `id_absence` int NOT NULL,
  `id_personel` int NOT NULL,
  `date` datetime NOT NULL,
  `statut` enum('accepted','modified','cancel') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_subs`),
  KEY `substitution_id_absence_foreign` (`id_absence`),
  KEY `substitution_id_personel_foreign` (`id_personel`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `substitution`
--

INSERT INTO `substitution` (`id_subs`, `id_absence`, `id_personel`, `date`, `statut`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '2026-05-25 00:00:00', 'accepted', '2026-05-25 12:19:26', '2026-05-25 12:19:26', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `name_user` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `surname_user` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `quarter` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id_user`, `name_user`, `surname_user`, `quarter`, `email`, `password`, `telephone`, `created_at`, `updated_at`, `deleted_at`) VALUES
(6, 'Gyz', 'Berenice', 'eveche', 'gyz@gmail.com', '$2y$10$MWBDOMHc7DxXEhcNBa/9k.gJQOPAdQ5SO1ro/zbsLzh3JFro4AloW', '656165453', '2026-06-10 10:27:26', '2026-06-18 19:54:29', '2026-06-18 19:54:29'),
(5, 'Major', 'SongSang', 'Bafoussam', 'major@gmail.com', '$2y$10$uDRacMjqqebLSb1P.hKl8./1kIkV7SvIQqeKgYJhiM1lU3wHr5pnG', '697503163', '2026-06-09 12:34:32', '2026-06-09 12:34:32', NULL),
(4, 'Kamdem', 'Jean', 'Bafoussam', 'jean@csikp.com', '$2y$10$8cE7uBnGTJ0GaJwSWN65yua8OMXS29Po0rB8T8QzfnSl5lGwNQZ8C', '699000001', '2026-06-09 12:26:40', '2026-06-09 12:26:40', NULL),
(13, 'Ingrid', 'Alliance', 'bamendzi', 'ingrid@gmail.com', '$2y$10$yeYgrojeHAu9yXCrZWu32OoXJmjny7n6QzBhR4PFu0wQTtwrXV12i', '659247622', '2026-06-19 10:14:51', '2026-06-19 10:14:51', NULL),
(8, 'emma', 'tawo', 'tama', 'emma@gmail.com', '$2y$10$dSyAMWhxeasd2gNU2wEanunQmrMmRHlL56c3U154yqc9eu6yIj7xy', '693567896', '2026-06-11 08:36:32', '2026-06-15 11:37:23', NULL),
(15, 'cecilia', 'joana', 'totaal', 'cecilia@gmail.com', '$2y$10$7yNjEb/7LZpz75ib3dVmXeeyy6sB.4isyyCeb/a5E/jaIkC/4j/MG', '444444', '2026-06-19 15:47:36', '2026-07-02 21:41:32', '2026-07-02 21:41:32'),
(14, 'david', 'daniel', 'eveche', 'dave@gmail.com', '$2y$10$7p5oizvYFNfiB9vvS45Oku6lms41gU792AY8tuGvzK4qLo.ZZGzla', '677673994', '2026-06-19 15:44:41', '2026-06-19 15:44:41', NULL),
(20, 'matam', 'hortense', 'maetur', 'hor@gmail.com', '$2y$10$eDyHRdOulLoc2k17D9pleu5ofSL3vaolmdNDMi9bTe2TbYulA/bda', '622232425', '2026-07-09 20:40:44', '2026-07-09 20:40:44', NULL),
(16, 'ismael', 'bouba', 'tankou', 'ismael@gmail.com', '$2y$10$SOUTSa9.mC9Aq7yz1W6ODuwT3DFzhVi9n21IICtIjlgD91AAFUqLu', '656176564', '2026-06-26 14:44:48', '2026-06-26 14:44:48', NULL),
(17, 'Assim', 'Kimnel', 'tamdja', 'assim@gmail.com', '$2y$10$sjMQKy3VdoZuHMbzOJGoyeonpdCwOhwG3etwHK7DyaWHt0stOP8FW', '655273472', '2026-07-01 12:56:50', '2026-07-01 12:56:50', NULL),
(18, 'Makam', 'Lydie', 'tankou', 'lydie@gmail.com', '$2y$10$.oJdlDnH2hHZVobM1hnz0O.rXghRErZLOB7SeN16vR.U6dgC.TDN6', '656121314', '2026-07-01 13:18:09', '2026-07-01 13:18:09', NULL),
(19, 'jemima', 'tama', 'dcc', 'jems@gmail.com', '$2y$10$drODlwuov/fyE5iTyPRl3.toOEgKvc.2E80rEjVkso0xkxI4vEdbi', '111111111', '2026-07-02 21:42:36', '2026-07-02 21:42:36', NULL),
(21, 'effim', 'favor', 'makambou', 'fav@gmail.com', '$2y$10$rJADunEDxVOzr6jD9t5IxuPgvughqizNcOsEy.tnaGyPvuGmqOqJm', '656768645', '2026-07-10 11:27:31', '2026-07-10 11:27:31', NULL),
(22, 'nanat', 'george', 'stade', 'george@gmail.com', '$2y$10$kKeidZiAkxUGhCJFdgq42.iH.CFRLrPKZHqVsa.ahKhipcugdssCq', '693348727', '2026-07-10 12:26:53', '2026-07-10 12:26:53', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `vitalsigns`
--

DROP TABLE IF EXISTS `vitalsigns`;
CREATE TABLE IF NOT EXISTS `vitalsigns` (
  `id_vi` int NOT NULL AUTO_INCREMENT,
  `temperature` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `weight` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `blood_pressure` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `height` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `heart_beat` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `id_medicalAct` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_vi`),
  KEY `VitalSigns_id_medicalAct_foreign` (`id_medicalAct`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `vitalsigns`
--

INSERT INTO `vitalsigns` (`id_vi`, `temperature`, `weight`, `blood_pressure`, `height`, `heart_beat`, `id_medicalAct`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '37', '78', '140/90', '1.8', '120', 7, '2026-06-18 08:25:07', '2026-06-18 08:25:07', NULL),
(2, '3', '3', '3', '3', '3', 9, '2026-06-19 02:01:15', '2026-06-19 02:01:15', NULL),
(3, '29', '90', '140/90', '1.58', '120', 10, '2026-06-19 10:17:13', '2026-06-19 10:17:13', NULL),
(4, '111', '11', '111', '1.1111', '111', 19, '2026-06-26 00:44:44', '2026-06-26 00:44:44', NULL),
(5, '37', '45', '140/12', '1.64', '120', 20, '2026-06-26 14:46:13', '2026-06-26 14:46:13', NULL),
(6, '23', '12', '120/40', '1.7', '134', 23, '2026-06-30 19:49:25', '2026-06-30 19:49:25', NULL),
(7, '37', '78', '130/89', '1.8', '120', 25, '2026-07-01 12:58:59', '2026-07-01 12:58:59', NULL),
(8, '37', '23', '22', '12', '22', 30, '2026-07-09 17:13:44', '2026-07-09 17:13:44', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
