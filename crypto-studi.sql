-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 26 oct. 2021 à 20:41
-- Version du serveur : 10.4.21-MariaDB
-- Version de PHP : 7.4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `crypto-studi`
--

-- --------------------------------------------------------

--
-- Structure de la table `api`
--

CREATE TABLE `api` (
  `id` int(11) NOT NULL,
  `api` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `api`
--

INSERT INTO `api` (`id`, `api`) VALUES
(1, '70e6aa97-cd54-4cea-ab05-fb3abdd13214');

-- --------------------------------------------------------

--
-- Structure de la table `cryptocurrency`
--

CREATE TABLE `cryptocurrency` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` double NOT NULL,
  `price` double NOT NULL,
  `total_price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cryptocurrency`
--

INSERT INTO `cryptocurrency` (`id`, `name`, `quantity`, `price`, `total_price`) VALUES
(26, 'BOSON', 4392, 1.3179, 5788.2168),
(33, 'POLK', 228, 0.6553, 149.4084),
(34, 'MTV', 155569, 0.0045, 700.0605),
(36, 'ETH', 1, 1500, 1500),
(37, 'ADA', 10, 10, 100),
(38, 'BTC', 0.05, 55000, 2750);

-- --------------------------------------------------------

--
-- Structure de la table `crypto_liste`
--

CREATE TABLE `crypto_liste` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `crypto_liste`
--

INSERT INTO `crypto_liste` (`id`, `name`, `symbol`) VALUES
(1, 'Bitcoin', 'BTC'),
(2, 'Cardano', 'ADA'),
(4, 'Ethereum', 'ETH'),
(5, 'Polkamarkets', 'POLK'),
(6, 'Boson Protocol', 'BOSON'),
(7, 'MultiVAC', 'MTV'),
(8, 'Ripple', 'XRP');

-- --------------------------------------------------------

--
-- Structure de la table `migration_versions`
--

CREATE TABLE `migration_versions` (
  `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migration_versions`
--

INSERT INTO `migration_versions` (`version`, `executed_at`) VALUES
('20210908101008', '2021-09-08 10:10:20'),
('20210908111241', '2021-09-08 11:38:59'),
('20210908114858', '2021-09-08 11:49:02'),
('20210910124331', '2021-09-10 12:43:42'),
('20210913110835', '2021-09-13 11:08:46'),
('20210914143802', '2021-09-14 14:38:07'),
('20210917110825', '2021-09-17 11:08:37'),
('20210917111349', '2021-09-17 11:13:51'),
('20210921152018', '2021-09-21 15:20:25');

-- --------------------------------------------------------

--
-- Structure de la table `sauvegarde_journaliere`
--

CREATE TABLE `sauvegarde_journaliere` (
  `id` int(11) NOT NULL,
  `date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valorisation_totale` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sauvegarde_journaliere`
--

INSERT INTO `sauvegarde_journaliere` (`id`, `date`, `valorisation_totale`) VALUES
(23, '2021-10-10', 1000),
(24, '2021-10-20', 2000),
(25, '2021-10-24', 1500),
(26, '2021-10-26', 3496);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `api`
--
ALTER TABLE `api`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `cryptocurrency`
--
ALTER TABLE `cryptocurrency`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `crypto_liste`
--
ALTER TABLE `crypto_liste`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `migration_versions`
--
ALTER TABLE `migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `sauvegarde_journaliere`
--
ALTER TABLE `sauvegarde_journaliere`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `api`
--
ALTER TABLE `api`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `cryptocurrency`
--
ALTER TABLE `cryptocurrency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pour la table `crypto_liste`
--
ALTER TABLE `crypto_liste`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `sauvegarde_journaliere`
--
ALTER TABLE `sauvegarde_journaliere`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
