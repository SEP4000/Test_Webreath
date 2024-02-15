-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 15 fév. 2024 à 16:48
-- Version du serveur : 5.7.33
-- Version de PHP : 8.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `webreath`
--

-- --------------------------------------------------------

--
-- Structure de la table `donnees`
--

CREATE TABLE `donnees` (
  `donnee_id` int(11) NOT NULL,
  `module_id` int(11) DEFAULT NULL,
  `valeur_mesuree` decimal(10,2) DEFAULT NULL,
  `date_mesure` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `donnees`
--

INSERT INTO `donnees` (`donnee_id`, `module_id`, `valeur_mesuree`, `date_mesure`) VALUES
(1, 1, '9.00', '2024-02-10 08:00:00'),
(2, 2, '51.30', '2024-02-10 08:00:00'),
(3, 3, '2600.00', '2024-02-10 08:00:00'),
(10, 1, '12.00', '2024-02-14 08:00:00'),
(12, 2, '46.00', '2024-02-15 08:00:00'),
(13, 3, '2478.00', '2024-02-12 08:00:00'),
(16, 1, '8.00', '2024-02-07 08:00:00'),
(17, 1, '4.00', '2024-02-05 08:00:00'),
(18, 1, '14.00', '2024-02-12 08:00:00'),
(19, 1, '17.00', '2024-02-11 08:00:00'),
(20, 1, '13.00', '2024-02-09 08:00:00'),
(21, 1, '11.00', '2024-02-08 08:00:00'),
(22, 2, '38.90', '2024-02-11 08:00:00'),
(23, 2, '53.70', '2024-02-12 08:00:00'),
(24, 2, '60.10', '2024-02-13 08:00:00'),
(25, 2, '64.00', '2024-02-09 08:00:00'),
(26, 2, '43.40', '2024-02-08 08:00:00'),
(27, 2, '42.90', '2024-02-07 08:00:00'),
(28, 2, '55.20', '2024-02-06 08:00:00'),
(29, 2, '61.10', '2024-02-05 08:00:00'),
(30, 2, '49.40', '2024-02-04 08:00:00'),
(31, 2, '43.00', '2024-02-03 08:00:00'),
(32, 3, '3087.00', '2024-02-11 08:00:00'),
(33, 3, '2358.00', '2024-02-13 08:00:00'),
(34, 3, '2965.00', '2024-02-09 08:00:00'),
(35, 3, '2843.00', '2024-02-08 08:00:00'),
(36, 3, '2556.00', '2024-02-07 08:00:00'),
(37, 3, '2673.00', '2024-02-06 08:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `etat_module`
--

CREATE TABLE `etat_module` (
  `etat_id` int(11) NOT NULL,
  `module_id` int(11) DEFAULT NULL,
  `etat_fonctionnement` varchar(50) DEFAULT NULL,
  `duree_fonctionnement` time DEFAULT NULL,
  `nombre_donnees_envoyees` int(11) DEFAULT NULL,
  `date_derniere_mise_a_jour` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `etat_module`
--

INSERT INTO `etat_module` (`etat_id`, `module_id`, `etat_fonctionnement`, `duree_fonctionnement`, `nombre_donnees_envoyees`, `date_derniere_mise_a_jour`) VALUES
(1, 1, NULL, '08:00:00', 10, '2024-02-10 08:00:00'),
(2, 2, NULL, '08:15:00', 12, '2024-02-10 08:15:00'),
(3, 3, NULL, '08:30:00', 8, '2024-02-10 08:30:00');

-- --------------------------------------------------------

--
-- Structure de la table `modules`
--

CREATE TABLE `modules` (
  `module_id` int(11) NOT NULL,
  `nom_module` varchar(255) DEFAULT NULL,
  `description` text,
  `date_installation` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `modules`
--

INSERT INTO `modules` (`module_id`, `nom_module`, `description`, `date_installation`) VALUES
(1, 'Module de la température', 'Température de Lille en moyenne de la journée', '2024-01-01'),
(2, 'Module de la vitesse de voiture\r\n', 'Vitesse de la voiture en moyenne de la journée ', '2024-01-05'),
(3, 'Module des calories', 'Nombre de calories manger de la journée', '2024-01-10');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `donnees`
--
ALTER TABLE `donnees`
  ADD PRIMARY KEY (`donnee_id`),
  ADD KEY `module_id` (`module_id`);

--
-- Index pour la table `etat_module`
--
ALTER TABLE `etat_module`
  ADD PRIMARY KEY (`etat_id`),
  ADD KEY `module_id` (`module_id`);

--
-- Index pour la table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`module_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `donnees`
--
ALTER TABLE `donnees`
  MODIFY `donnee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT pour la table `etat_module`
--
ALTER TABLE `etat_module`
  MODIFY `etat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `modules`
--
ALTER TABLE `modules`
  MODIFY `module_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `donnees`
--
ALTER TABLE `donnees`
  ADD CONSTRAINT `donnees_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `modules` (`module_id`);

--
-- Contraintes pour la table `etat_module`
--
ALTER TABLE `etat_module`
  ADD CONSTRAINT `etat_module_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `modules` (`module_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
