-- phpMyAdmin SQL Dump
-- version 4.9.7deb1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 17 fév. 2022 à 13:45
-- Version du serveur :  8.0.27-0ubuntu0.21.04.1
-- Version de PHP : 7.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `legalpilot_db`
--
CREATE DATABASE IF NOT EXISTS `legalpilot_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `legalpilot_db`;

-- --------------------------------------------------------

--
-- Structure de la table `app_character`
--

CREATE TABLE `app_character` (
  `id` int NOT NULL,
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` int NOT NULL,
  `experience` int DEFAULT NULL,
  `health` int NOT NULL,
  `strong` int NOT NULL,
  `speed` int NOT NULL,
  `guard` int NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `victory` int DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `fight` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `app_character`
--

INSERT INTO `app_character` (`id`, `name`, `level`, `experience`, `health`, `strong`, `speed`, `guard`, `image`, `victory`, `modified_at`, `fight`) VALUES
(8, 'Vivaldi', 1, NULL, 20, 4, 4, 4, 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ftse1.mm.bing.net%2Fth%3Fid%3DOIP.DliVpgZqS3JfrcP6CMy0WwHaEK%26pid%3DApi&f=1', 0, NULL, 0),
(9, 'Beethoven', 1, NULL, 20, 4, 4, 4, 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ftse3.mm.bing.net%2Fth%3Fid%3DOIP.3SVH1dP-C1zrNkO2qOcbPAHaHa%26pid%3DApi&f=1', 0, NULL, 0),
(10, 'Bach', 1, NULL, 20, 4, 4, 4, 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ftse4.mm.bing.net%2Fth%3Fid%3DOIP.ZeVhW4M9H6Wyl1o543sAOQHaEK%26pid%3DApi&f=1', 0, NULL, 0),
(11, 'Mozart', 1, NULL, 20, 4, 4, 4, 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ftse3.mm.bing.net%2Fth%3Fid%3DOIP.BIUDEC50rhJPngFg-R1uiQHaHa%26pid%3DApi&f=1', 0, NULL, 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `app_character`
--
ALTER TABLE `app_character`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `app_character`
--
ALTER TABLE `app_character`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
