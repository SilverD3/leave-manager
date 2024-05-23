-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 23 mai 2024 à 04:43
-- Version du serveur : 8.0.35
-- Version de PHP : 8.1.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `leave_manager`
--

-- --------------------------------------------------------

--
-- Structure de la table `company`
--

CREATE TABLE `company` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `director_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tel1` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tel2` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `about` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT NULL,
  `modified_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `configs`
--

CREATE TABLE `configs` (
  `id` int NOT NULL,
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `default_value` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `value` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int DEFAULT NULL,
  `value_type` enum('bool','string') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `configs`
--

INSERT INTO `configs` (`id`, `code`, `description`, `default_value`, `value`, `modified`, `modified_by`, `value_type`) VALUES
(1, 'LM_LEAVE_NB_DAYS', 'Nombre de jours de congés par an pour chaque employé', '18', '18', '2022-09-08 10:38:58', 1, 'string'),
(2, 'LM_MULTIPLE_LEAVES_IN_YEAR', 'Permettre plusieurs prises de congés dans l\'année', 'NON', 'NON', '2022-09-08 10:38:58', 1, 'bool'),
(3, 'LM_WORK_BEGIN_AT', 'Heure de début du travail', '08:00', '08:00', '2022-09-08 10:38:58', 1, 'string'),
(4, 'LM_WORK_END_AT', 'Heure de fin du travail', '18:00', '18:00', '2022-09-08 10:38:58', 1, 'string'),
(5, 'LM_HOURLY_RATE', 'Nombre de d\'heures de travail par semaine', '40', '40', '2022-09-13 10:08:39', 1, 'string'),
(6, 'LM_HOLIDAYS', 'Les jours fériés (non ouvrables) dans l\'année, séparée par une virgule. Format: année/mois/jour. Pour les jours concernant tous les ans, mettre une étoile (*) à la place de l’année. Exemple: */12/25, 1999/02/08', '*/12/25,*/01/01,*/02/11,*/05/20', '*/12/25,*/01/01,*/02/11,*/05/20,*/05/01', '2022-09-08 10:46:38', 1, 'string'),
(7, 'LM_WORKING_DAYS', 'Les jours ouvrables de l\'entreprise séparés par des virgules.', 'lundi,mardi,mercredi,jeudi,vendredi', 'lundi,mardi,mercredi,jeudi,vendredi', '2022-09-08 10:38:58', 1, 'string'),
(8, 'LM_DAILY_BREAKS', 'Les périodes de la journée pendant lesquelles une pause est observée. Séparer ces périodes par des virgules. Exemple: 11:15-12:00,14:45-15:30', '12:00-14:00', '12:00-14:00', '2022-09-17 12:24:03', 1, 'string'),
(9, 'LM_PERMISSION_REDUCE_LEAVE', 'Définit si le temps utilisé dans les permissions doit être prélevé sur la durée de congé allouée à un employé.', 'OUI', 'OUI', '2022-09-22 15:47:08', 1, 'bool'),
(10, 'LM_SAME_TIME_NB_LEAVE', 'Définit le nombre maximal d’employés en congé en même temps. La valeur 0 signifie pas de limite.', '0', '2', '2022-09-22 15:53:08', 1, 'string'),
(11, 'LM_NEXT_PERMISSION_DELAY', 'Période inter-permission exprimée en jours. La valeur 0 signifie pas de délai.', '0', '14', '2022-09-13 11:29:25', 1, 'string'),
(12, 'LM_OVERRIDE_LEAVE_NB_DAYS', 'Permettre le surpassement du nombre de jours de congé, i.e. qu\'un employé prenne plus de jours de congé que le nombre de jours défini', 'NON', 'NON', '2022-09-19 08:51:44', 1, 'bool');

-- --------------------------------------------------------

--
-- Structure de la table `contracts`
--

CREATE TABLE `contracts` (
  `id` int NOT NULL,
  `employee_id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contract_type_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `job_object` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `job_description` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `job_salary` decimal(13,4) DEFAULT NULL,
  `hourly_rate` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pdf` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT NULL,
  `status` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `etat` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `contracts`
--

INSERT INTO `contracts` (`id`, `employee_id`, `title`, `contract_type_id`, `start_date`, `end_date`, `job_object`, `job_description`, `job_salary`, `hourly_rate`, `pdf`, `created`, `modified`, `status`, `etat`) VALUES
(1, 3, 'CDI de Yollo', 4, '2024-06-01', '2024-11-30', 'Designer UI/UX', NULL, '250000.0000', '40 heures/semaine', NULL, '2024-05-21 05:18:56', NULL, 'pending', 1);

-- --------------------------------------------------------

--
-- Structure de la table `contract_models`
--

CREATE TABLE `contract_models` (
  `id` int NOT NULL,
  `contract_type_id` int NOT NULL,
  `name` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT NULL,
  `etat` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `contract_models`
--

INSERT INTO `contract_models` (`id`, `contract_type_id`, `name`, `content`, `is_current`, `status`, `created`, `modified`, `etat`) VALUES
(4, 3, 'Modele CDI 1', '<p style=\"line-height: 100%; margin-right: -0cm; margin-bottom: 0cm; border-top: none; border-bottom: 1px solid #000000; border-left: none; border-right: none; padding: 0cm 0cm 0.04cm 0cm;\" align=\"center\"><span style=\"color: rgb(0, 32, 96); font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><strong>Contrat &agrave; dur&eacute;e ind&eacute;termin&eacute;e (CDI) avec p&eacute;riode d\'essai</strong></span></span></p>\r\n<p style=\"line-height: 100%; margin-right: -0cm; margin-bottom: 0cm;\" align=\"center\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\"><em>(sur papier &agrave; en-t&ecirc;te de l&rsquo;entreprise)</em></span></span></p>\r\n<p style=\"line-height: 100%; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">Entre les soussign&eacute;s :</span></span></p>\r\n<p style=\"line-height: 100%; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">- la soci&eacute;t&eacute; $_company_name</span></span></span></p>\r\n<p style=\"line-height: 100%; margin-left: 0.8cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">Adresse $_company_address<br></span></span></p>\r\n<p style=\"line-height: 100%; margin-left: 0.8cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">Immatriculation au RCS ......</span></span></p>\r\n<p style=\"line-height: 100%; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">Repr&eacute;sent&eacute;e par M. $_employer_name </span></span><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">agissant en qualit&eacute; de ......</span></span></p>\r\n<p style=\"line-height: 100%; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">d&rsquo;une part,</span></span></p>\r\n<p style=\"line-height: 100%; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">et :</span></span></p>\r\n<p style=\"line-height: 100%; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">- M.&nbsp;......</span></span></p>\r\n<p style=\"line-height: 100%; margin-left: 0.8cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">demeurant &agrave; ......</span></span></p>\r\n<p style=\"line-height: 100%; margin-left: 0.8cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">n&deg; de S&eacute;curit&eacute; sociale&nbsp;: &hellip;&hellip;</span></span></p>\r\n<p style=\"line-height: 100%; margin-left: 0.8cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">de nationalit&eacute; ......</span></span></p>\r\n<p style=\"line-height: 100%; margin-left: 0.8cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">libre de tout engagement,</span></span></p>\r\n<p style=\"line-height: 100%; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">d\'autre part.</span></span></p>\r\n<p style=\"line-height: 100%; margin-bottom: 0cm; text-decoration: none;\" align=\"center\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\"><strong>Il a &eacute;t&eacute; convenu ce qui suit :</strong></span></span></p>\r\n<p style=\"line-height: 100%; margin-right: -0cm; margin-bottom: 0cm;\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\"><u><strong>Article 1 &ndash; Engagement</strong></u></span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">Sous r&eacute;serve des r&eacute;sultats de la visite m&eacute;dicale d&rsquo;embauche d&eacute;cidant de l&rsquo;aptitude de M.&nbsp;&hellip;&hellip; au poste propos&eacute;, M.&nbsp;...... est engag&eacute; par la soci&eacute;t&eacute; ...... en qualit&eacute; de ...... </span><span style=\"font-size: small;\"><em>(qualification ou titre)</em></span><span style=\"font-size: small;\">.</span></span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">Ce contrat prend effet &agrave; compter du ...... </span><span style=\"font-size: small;\"><em>(date)</em></span><span style=\"font-size: small;\"> &agrave; ...... </span><span style=\"font-size: small;\"><em>(heures)</em></span><span style=\"font-size: small;\">.</span></span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">La d&eacute;claration pr&eacute;alable &agrave; l&rsquo;embauche de M.&nbsp;...... a &eacute;t&eacute; remise &agrave; l&rsquo;URSSAF de &hellip;&hellip; </span><span style=\"font-size: small;\"><em>(pr&eacute;ciser le nom de la ville).&nbsp;</em></span></span></span></p>\r\n<p style=\"line-height: 100%; margin-right: -0cm; margin-bottom: 0cm;\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\"><u><strong>Article 2 - Convention collective</strong></u></span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">En application de la Convention collective nationale de ...... </span><span style=\"font-size: small;\"><em>(et, le cas &eacute;ch&eacute;ant, de la convention collective d&eacute;partementale et ou r&eacute;gionale)</em></span><span style=\"font-size: small;\">, M.&nbsp;...... rel&egrave;vera du coefficient ......, position &hellip;&hellip; </span><span style=\"font-size: small;\"><em>(&agrave; pr&eacute;ciser)</em></span><span style=\"font-size: small;\">, niveau &hellip;&hellip; </span><span style=\"font-size: small;\"><em>(&agrave; pr&eacute;ciser).</em></span></span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">L&rsquo;ensemble des dispositions de la convention sus-indiqu&eacute;e s&rsquo;applique au pr&eacute;sent contrat et ceci tant que ces derni&egrave;res resteront opposables de droit &agrave; l&rsquo;entreprise. </span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">Un exemplaire de la pr&eacute;sente convention collective est &agrave; la disposition de M.&nbsp;...... au sein de l&rsquo;&eacute;tablissement </span><span style=\"font-size: small;\"><em>(pr&eacute;ciser le service ou le bureau)</em></span><span style=\"font-size: small;\">.</span></span></span></p>\r\n<p style=\"line-height: 100%; margin-right: -0cm; margin-bottom: 0cm;\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\"><u><strong>Article 3 - P&eacute;riode d&rsquo;essai </strong></u></span></span></p>\r\n<p class=\"western\" style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\">Le pr&eacute;sent contrat est conclu pour une dur&eacute;e ind&eacute;termin&eacute;e.</span></p>\r\n<p class=\"western\" style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\">Il ne deviendra d&eacute;finitif qu&rsquo;&agrave; l&rsquo;expiration d&rsquo;une p&eacute;riode d&rsquo;essai de ......&nbsp;<em>(jours ou moi</em>s).</span></p>\r\n<p class=\"western\" style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\">Il est express&eacute;ment convenu que la p&eacute;riode d&rsquo;essai s&rsquo;entend d&rsquo;un travail effectif.</span></p>\r\n<p class=\"western\" style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\">Si pendant l&rsquo;ex&eacute;cution de ladite p&eacute;riode d&rsquo;essai, le contrat de travail de M.&nbsp;...... devait &ecirc;tre suspendu pour quelque motif que ce soit, cette p&eacute;riode d&rsquo;essai serait prolong&eacute;e d&rsquo;une dur&eacute;e identique &agrave; la p&eacute;riode de suspension.</span></p>\r\n<p class=\"western\" style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\">Jusqu&rsquo;&agrave; cette date, il sera possible &agrave; M.&nbsp;......, comme &agrave; l&rsquo;entreprise, de rompre le contrat de travail sans indemnit&eacute;.</span></p>\r\n<p class=\"western\" style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><em>(Un d&eacute;lai de pr&eacute;venance devra alors &ecirc;tre respect&eacute; par les parties [C.&nbsp;trav., art.&nbsp;L.&nbsp;1221-25 et L.&nbsp;1221-26]).</em></span></p>\r\n<p class=\"western\" style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><em>Si le renouvellement est pr&eacute;vu par la convention collective&nbsp;:</em></span></p>\r\n<p class=\"western\" style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0.21cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\">Conform&eacute;ment aux dispositions de la convention collective, cet essai pourra &ecirc;tre renouvel&eacute; dans les conditions suivantes : ......</span></p>\r\n<p style=\"line-height: 100%; margin-right: -0cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\"><u><strong>Article 4 - Fonctions</strong></u></span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">M.&nbsp;...... en sa qualit&eacute; de &hellip;&hellip; </span><span style=\"font-size: small;\"><em>(poste occup&eacute;) </em></span><span style=\"font-size: small;\">sera plus particuli&egrave;rement charg&eacute; de ...... </span><span style=\"font-size: small;\"><em>(pr&eacute;ciser).</em></span></span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">Cette liste de t&acirc;ches est non exhaustive et pourra &ecirc;tre compl&eacute;t&eacute;e en fonction des besoins de l&rsquo;entreprise.&nbsp;</span></span></p>\r\n<p style=\"line-height: 100%; margin-right: -0cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\"><u><strong>Article 5 - Lieu de travail </strong></u></span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">M.&nbsp;...... exercera ses fonctions sur le site de &hellip;&hellip; </span><span style=\"font-size: small;\"><em>(adresse de l&rsquo;&eacute;tablissement ou de l&rsquo;entreprise)</em></span><span style=\"font-size: small;\">. En fonction des n&eacute;cessit&eacute;s de service, le lieu de travail de M. ...... pourra &ecirc;tre modifi&eacute; de mani&egrave;re temporaire ou d&eacute;finitive &agrave; l&rsquo;int&eacute;rieur du secteur g&eacute;ographique d&rsquo;implantation de la soci&eacute;t&eacute;.&nbsp;</span></span></span></p>\r\n<p style=\"line-height: 100%; margin-right: -0cm; margin-bottom: 0cm;\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\"><u><strong>Article 6 - Horaire de travail</strong></u></span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">M. ...... est assujetti &agrave; l&rsquo;horaire de travail de l&rsquo;&eacute;tablissement, soit un horaire de ...... et une dur&eacute;e hebdomadaire de ...... heures.</span></span></p>\r\n<p style=\"line-height: 100%; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\">Variante</span></span></p>\r\n<center>\r\n<table width=\"605\" cellspacing=\"0\" cellpadding=\"7\"><colgroup><col width=\"589\"> </colgroup>\r\n<tbody>\r\n<tr>\r\n<td style=\"border: 1px solid #000000; padding: 0cm 0.19cm;\" valign=\"top\" width=\"589\">\r\n<p style=\"margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">Dans le cadre du pr&eacute;sent contrat, M. &hellip;&hellip; b&eacute;n&eacute;ficie d&rsquo;un horaire individualis&eacute; selon les modalit&eacute;s suivantes ...... </span><span style=\"font-size: small;\"><em>(pr&eacute;ciser)</em></span></span></span></p>\r\n<p style=\"margin-bottom: 0cm;\" align=\"justify\">&nbsp;</p>\r\n<p align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">La dur&eacute;e hebdomadaire du travail sera de ......&nbsp;h ......</span></span></p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</center>\r\n<p class=\"western\" style=\"line-height: 100%; margin-top: 0.18cm; margin-bottom: 0.18cm;\">&nbsp;</p>\r\n<p style=\"line-height: 100%; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">M.&nbsp;...... pourra &ecirc;tre amen&eacute; &agrave; effectuer des heures suppl&eacute;mentaires &agrave; la demande de <br>la Direction qui seront r&eacute;mun&eacute;r&eacute;es conform&eacute;ment aux dispositions l&eacute;gales et conventionnelles en vigueur.</span></span></span></p>\r\n<p style=\"line-height: 100%; margin-right: -0cm; margin-bottom: 0cm;\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\"><u><strong>Article 7 - R&eacute;mun&eacute;ration</strong></u></span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">La r&eacute;mun&eacute;ration mensuelle brute sera de ......&nbsp;euros pour un horaire mensualis&eacute; de ...... heures </span><span style=\"font-size: small;\"><em>(v&eacute;rifier l&rsquo;ad&eacute;quation entre le coefficient hi&eacute;rarchique et les minima conventionnels).</em></span></span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">Pour toute heure effectu&eacute;e au-del&agrave; de ......, une majoration sera accord&eacute;e et calcul&eacute;e conform&eacute;ment aux dispositions l&eacute;gales et conventionnelles en vigueur.</span></span></p>\r\n<p style=\"line-height: 100%; margin-right: -0cm; margin-bottom: 0cm;\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\"><u><strong>Article 8 &ndash; Discipline et S&eacute;curit&eacute;</strong></u></span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">M.&nbsp;...... reconna&icirc;t avoir pris connaissance du r&egrave;glement int&eacute;rieur en vigueur dans l&rsquo;&eacute;tablissement. Tout manquement au pr&eacute;sent r&egrave;glement pourrait donner lieu &agrave; des poursuites disciplinaires et &agrave; un &eacute;ventuel licenciement pour faute.&nbsp;</span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">M.&nbsp;...... exercera ses fonctions sous l&rsquo;autorit&eacute; et dans le cadre des instructions donn&eacute;es par M.&nbsp;...... ou de toute personne habilit&eacute;e &agrave; cet effet.</span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">M.&nbsp;</span><span style=\"font-size: small;\"><em>...... </em></span><span style=\"font-size: small;\">s&rsquo;engage &agrave; observer toutes les instructions et consignes particuli&egrave;res de travail qui lui seront donn&eacute;es et &agrave; respecter une stricte obligation de discr&eacute;tion sur tout ce qui concerne l&rsquo;activit&eacute; de l&rsquo;entreprise.&nbsp;</span></span></span></p>\r\n<p style=\"line-height: 100%; margin-right: -0cm; margin-bottom: 0cm;\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\"><u><strong>Article 9 - Cong&eacute;s pay&eacute;s</strong></u></span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">M.&nbsp;...... b&eacute;n&eacute;ficiera des droits &agrave; cong&eacute;s pay&eacute;s conform&eacute;ment aux dispositions l&eacute;gales </span><span style=\"font-size: small;\"><em>(ou conventionnelles) </em></span><span style=\"font-size: small;\">en vigueur.</span></span></span></p>\r\n<p style=\"line-height: 100%; margin-right: -0cm; margin-bottom: 0cm;\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\"><u><strong>Article 10 - Avantages sociaux</strong></u></span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">M.&nbsp;......, relevant de la cat&eacute;gorie professionnelle des &hellip;&hellip; </span><span style=\"font-size: small;\"><em>(pr&eacute;ciser employ&eacute;s, agents de ma&icirc;trise, cadres), </em></span><span style=\"font-size: small;\">sera affili&eacute; d&egrave;s son entr&eacute;e au sein de la soci&eacute;t&eacute; &agrave;&nbsp;:</span></span></span></p>\r\n<p style=\"line-height: 100%; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">- ...... </span><span style=\"font-size: small;\"><em>(organisme de retraite)</em></span><span style=\"font-size: small;\">&nbsp;; </span></span></span></p>\r\n<p style=\"line-height: 100%; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">- ...... </span><span style=\"font-size: small;\"><em>(organisme de pr&eacute;voyance)</em></span><span style=\"font-size: small;\">. </span></span></span></p>\r\n<p style=\"line-height: 100%; margin-right: -0cm; margin-bottom: 0cm;\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\"><u><strong>Article 11 - Rupture du contrat (hors essai)</strong></u></span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">Le pr&eacute;sent contrat pourra &ecirc;tre rompu&nbsp;:</span></span></p>\r\n<p style=\"line-height: 100%; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">- &agrave; l&rsquo;initiative du salari&eacute;&nbsp;; </span></span></span></p>\r\n<p style=\"line-height: 100%; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">- &agrave; l&rsquo;initiative de l\'employeur. </span></span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">Dans l&rsquo;un ou l&rsquo;autre cas, un pr&eacute;avis devra &ecirc;tre respect&eacute; conform&eacute;ment aux dispositions l&eacute;gales et conventionnelles en vigueur.</span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">La rupture du contrat par l&rsquo;employeur, justifi&eacute;e par une cause r&eacute;elle et s&eacute;rieuse, entra&icirc;nera le versement d&rsquo;une indemnit&eacute; de licenciement si le salari&eacute; a au moins 1 an d&rsquo;anciennet&eacute;. Cette &eacute;ventuelle rupture entra&icirc;nera le versement d&rsquo;une indemnit&eacute; de licenciement calcul&eacute;e en fonction du bar&egrave;me le plus avantageux r&eacute;sultant soit de la loi soit de la convention collective.</span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">Cette indemnit&eacute; n&rsquo;est pas due en cas de faute grave ou lourde ou en cas de force majeure.</span></span></p>\r\n<p style=\"line-height: 100%; margin-right: -0cm; margin-bottom: 0cm;\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\"><u><strong>Article 12 &ndash; Obligations professionnelles</strong></u></span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">M.&nbsp;...... s&rsquo;engage &agrave; informer la soci&eacute;t&eacute; de tout changement le concernant, notamment en cas de changement de domicile. La nouvelle adresse sera transmise d&egrave;s que possible au bureau du personnel.</span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: small;\">M.&nbsp;...... s&rsquo;engage &agrave; d&eacute;clarer tout accident du travail survenu sur les lieux du travail ou tout accident survenu sur le trajet dans les 48&nbsp;heures &agrave; l&rsquo;autorit&eacute; hi&eacute;rarchique.</span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">M.&nbsp;&hellip;&hellip;&nbsp;s&rsquo;engage &agrave; informer sans d&eacute;lai la soci&eacute;t&eacute; de toute absence et de justifier des raisons de celle-ci dans les 48 heures par tout justificatif utile (certificat m&eacute;dical&nbsp;le cas &eacute;ch&eacute;ant).&nbsp;</span></span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">M.&nbsp;&hellip;&hellip; s&rsquo;engage &agrave; conserver une discr&eacute;tion absolue sur tous les fichiers et documents internes &agrave; la soci&eacute;t&eacute; pendant toute la dur&eacute;e du pr&eacute;sent contrat et apr&egrave;s la rupture de celui-ci quelle que soit la cause. </span></span></span></p>\r\n<p style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">Fait en double exemplaire &agrave; &hellip;&hellip;, le ......</span></span></span></p>\r\n<p style=\"line-height: 100%; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\"><em>(Signature des parties pr&eacute;c&eacute;d&eacute;e de la mention &laquo; lu et approuv&eacute; &raquo;)</em></span></span></span></p>\r\n<p style=\"line-height: 100%; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: helvetica, arial, sans-serif;\"><span style=\"font-size: medium;\"><span style=\"font-size: small;\">Signature du salari&eacute; Signature de l\'employeur</span></span></span></p>', 1, 'active', '2022-08-25 17:35:02', '2022-08-26 17:20:00', 1),
(5, 3, 'Modèle CDI 2', '<p>CDI mod&egrave;le 2</p>', 0, 'active', '2022-08-26 08:12:59', '2022-08-26 17:31:07', 1),
(6, 4, 'Modele CDD 1', '&lt;p&gt;CDD&lt;/p&gt;', 0, 'deleted', '2022-08-26 08:13:33', '2022-08-26 08:15:02', 0),
(7, 4, 'Modèle principal CDD', '<h1 class=\"western\" style=\"line-height: 1.5; border: 6.75pt double rgb(0, 0, 0); padding: 0.04cm; margin: 0.21cm 0.6cm;\" align=\"center\"><span style=\"font-size: 18pt; font-family: \'times new roman\', times, serif;\"><strong>CONTRAT DE TRAVAIL A DUR&Eacute;E D&Eacute;TERMIN&Eacute;E </strong></span></h1>\r\n<p>&nbsp;</p>\r\n<p style=\"line-height: 2;\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\"><strong>ENTRE LES SOUSSIGN&Eacute;S :</strong></span></p>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\">L&rsquo;entreprise : $_company_name</span></p>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\">dont le si&egrave;ge social est situ&eacute; &agrave; $_company_address<br></span></p>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\">repr&eacute;sent&eacute;e par Monsieur $_employer_name<br></span></p>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\">agissant en qualit&eacute; de Directeur G&eacute;n&eacute;rale </span></p>\r\n<p style=\"line-height: 2;\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\"><strong>D\'UNE PART, </strong><strong>ET</strong></span></p>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-bottom: 0cm;\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\"><strong>Monsieur $_candidate_name</strong></span></p>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-bottom: 0cm;\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\">demeurant &agrave; $_candidate_address<br></span></p>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-bottom: 0cm;\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\">n&eacute;(e) le : $_candidate_birth &agrave; $_candidate_baddress<br></span></p>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-bottom: 0cm;\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\">de nationalit&eacute; : Camerounaise</span></p>\r\n<p style=\"line-height: 2;\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\"><strong>D\'AUTRE PART,</strong></span></p>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-bottom: 0cm;\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\"><strong>IL A &Eacute;T&Eacute; CONVENU CE QUI SUIT :</strong></span></p>\r\n<h2 style=\"line-height: 2;\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\"><strong>ARTICLE I : MOTIF </strong></span></h2>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\">Monsieur $_candidate_name est engag&eacute; en vue de $_job_description. </span></p>\r\n<h2 class=\"western\" style=\"line-height: 1.5; margin-bottom: 0cm;\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\"><strong>ARTICLE II : EMPLOI OCCUPE <br></strong></span></h2>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: \'times new roman\', times, serif; font-size: 12pt;\">Monsieur $_candidate_name est employ&eacute;(e) en qualit&eacute; de $_job_object suivant le coefficient hi&eacute;rarchique Niveau III Echelon A pr&eacute;vu par la convention collective nationale de la Boucherie, Boucherie-charcuterie, Boucherie Hippophagique, Triperie, Commerce de Volailles et Gibiers.</span></p>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\">Il aura pour mission la transformation des carcasses de leur &eacute;tat initial de gros morceaux de coupe jusqu&rsquo; &agrave; leur pr&eacute;sentation en morceaux de d&eacute;tail. </span></p>\r\n<h2 class=\"western\" style=\"line-height: 2; margin-bottom: 0cm;\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\"><strong>ARTICLE III : DUR&Eacute;E </strong></span></h2>\r\n<p class=\"western\" style=\"line-height: 100%; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\">Le pr&eacute;sent contrat prend effet le $_job_start_date &nbsp;&agrave; 08:00 heures et prendra fin de plein droit et sans formalit&eacute; le $_job_end_date.</span></p>\r\n<h2 class=\"western\" style=\"line-height: 2; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\"><strong>ARTICLE IV : LIEU DE TRAVAIL</strong></span></h2>\r\n<p class=\"western\" style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\">Le lieu de travail est situ&eacute; &agrave; $_company_address<br></span></p>\r\n<h2 class=\"western\" style=\"line-height: 2; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\"><strong>ARTICLE V : DUR&Eacute;E DU TRAVAIL<br></strong></span></h2>\r\n<p class=\"western\" style=\"line-height: 100%; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\">Les horaires seront les suivants :</span></p>\r\n<p class=\"western\" style=\"line-height: 100%; margin-left: 0.5cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\">- De lundi a jeudi : de 8 H &agrave; 12 H, de 14 H &agrave; 18 H </span></p>\r\n<p class=\"western\" style=\"line-height: 100%; margin-left: 0.5cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\">- Le samedi de 8 H &agrave; 12 H<br></span></p>\r\n<h2 class=\"western\" style=\"line-height: 2; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\"><strong>ARTICLE VI : REMUNERATION<br></strong></span></h2>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: \'times new roman\', times, serif; font-size: 12pt;\">En contrepartie de ses fonctions, Monsieur $_candidate_name<em> </em>percevra un salaire horaire de $_job_salary XAF, pour l&rsquo;horaire moyen de $_hourly_rate pratiqu&eacute; dans l&rsquo;entreprise. Elle lui sera vers&eacute;e &agrave; la fin de chaque mois civil.</span></p>\r\n<h2 class=\"western\" style=\"line-height: 2; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\"><strong>ARTICLE VII : INDEMNIT&Eacute; DE FIN DE CONTRAT </strong></span></h2>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\">A la cessation de ses fonctions dans l&rsquo;entreprise, Monsieur $_candidate_name percevra une indemnit&eacute; de fin de contrat aux conditions et taux fix&eacute;s par le code du travail.</span></p>\r\n<h2 class=\"western\" style=\"line-height: 2; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\"><strong>ARTICLE VIII : RETRAITE COMPL&Eacute;MENTAIRE ET PR&Eacute;VOYANCE</strong></span></h2>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\">Monsieur $_candidate_name sera affili&eacute; aupr&egrave;s des caisses de retraite et de pr&eacute;voyance suivantes :</span></p>\r\n<p><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\">CNPS Dschang, B.P 1234</span></p>\r\n<h2 class=\"western\" style=\"line-height: 2; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\"><strong>ARTICLE IX : CONVENTION COLLECTIVE </strong></span></h2>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\">Pour toutes les dispositions non pr&eacute;vues par les pr&eacute;sentes, les parties d&eacute;clarent se r&eacute;f&eacute;rer &agrave; la convention collective nationale de la Boucherie, Boucherie-Charcuterie, Boucherie Hippophagique, Triperie, Commerce de Volailles et Gibiers applicable &agrave; l\'entreprise </span></p>\r\n<h2 class=\"western\" style=\"line-height: 2; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\"><strong>ARTICLE X : RUPTURE ANTICIP&Eacute;E POUR FAUTE GRAVE OU FORCE MAJEURE</strong></span></h2>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-top: 0.21cm; margin-bottom: 0cm;\" align=\"justify\"><span style=\"font-family: \'times new roman\', times, serif; font-size: 12pt;\">Chacune des parties se r&eacute;serve mutuellement le droit de mettre fin au contrat imm&eacute;diatement en cas de faute grave de l&rsquo;autre partie<em> </em>ou de force majeure<em>.</em></span></p>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-bottom: 0cm;\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\">Fait en double exemplaire,</span></p>\r\n<p class=\"western\" style=\"line-height: 2; margin-bottom: 0cm;\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\"><strong>A Dschang</strong></span></p>\r\n<p class=\"western\" style=\"line-height: 2; margin-bottom: 0cm;\"><span style=\"font-size: 12pt; font-family: \'times new roman\', times, serif;\"><strong>Le $_generated_date</strong><br></span></p>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-bottom: 0cm;\">&nbsp;</p>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-bottom: 0cm;\">&nbsp;</p>\r\n<p class=\"western\" style=\"line-height: 1.5; margin-bottom: 0cm;\">&nbsp;</p>', 1, 'active', '2022-09-06 15:06:30', '2022-09-07 11:52:30', 1);

-- --------------------------------------------------------

--
-- Structure de la table `contract_types`
--

CREATE TABLE `contract_types` (
  `id` int NOT NULL,
  `name` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `etat` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `contract_types`
--

INSERT INTO `contract_types` (`id`, `name`, `description`, `created`, `etat`) VALUES
(3, 'CDI', 'Contrat &agrave; dur&eacute;e ind&eacute;termin&eacute;e', '2022-08-16 16:57:26', 1),
(4, 'CDD', 'Contrat &agrave; dur&eacute;e d&eacute;termin&eacute;e', '2022-08-16 18:03:33', 1);

-- --------------------------------------------------------

--
-- Structure de la table `employees`
--

CREATE TABLE `employees` (
  `id` int NOT NULL,
  `first_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pwd` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role_id` int NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token_exp_date` datetime DEFAULT NULL,
  `status` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `etat` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `employees`
--

INSERT INTO `employees` (`id`, `first_name`, `last_name`, `email`, `username`, `pwd`, `role_id`, `created`, `modified`, `token`, `token_exp_date`, `status`, `etat`) VALUES
(1, 'Admin', 'admin', 'admin@gmail.com', 'admin', '4ceb415ca0376305106660973f6f9cf550126cb0fc2a9fdcff1b30b4e6e27383 ', 1, '2022-08-05 16:19:18', NULL, NULL, NULL, 'pending', 1),
(3, 'Adesanya', 'Yollo', 'adesanya.yollo@gmail.com', 'adesanya', '4ceb415ca0376305106660973f6f9cf550126cb0fc2a9fdcff1b30b4e6e27383', 2, '2024-05-07 12:54:59', NULL, NULL, NULL, 'active', 1);

-- --------------------------------------------------------

--
-- Structure de la table `internships`
--

CREATE TABLE `internships` (
  `id` int NOT NULL,
  `internship_type_id` int NOT NULL,
  `supervisor` int DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `sex` enum('M','F') NOT NULL,
  `birthdate` date NOT NULL,
  `school_name` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `report` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `status` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT NULL,
  `etat` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `internship_documents`
--

CREATE TABLE `internship_documents` (
  `id` int NOT NULL,
  `internship_id` int NOT NULL,
  `internship_document_type_id` int NOT NULL,
  `document` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `etat` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `internship_document_types`
--

CREATE TABLE `internship_document_types` (
  `id` int NOT NULL,
  `code` varchar(100) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `is_multiple` tinyint(1) NOT NULL,
  `is_required` tinyint(1) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `etat` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `internship_types`
--

CREATE TABLE `internship_types` (
  `id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `etat` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `leaves`
--

CREATE TABLE `leaves` (
  `id` int NOT NULL,
  `employee_id` int NOT NULL,
  `year` int NOT NULL,
  `days` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT NULL,
  `note` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `etat` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `permissions`
--

CREATE TABLE `permissions` (
  `id` int NOT NULL,
  `employee_id` int NOT NULL,
  `reason` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `etat` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `permission_requests`
--

CREATE TABLE `permission_requests` (
  `id` int NOT NULL,
  `employee_id` int NOT NULL,
  `reason` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `reduce` tinyint(1) NOT NULL,
  `status` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT NULL,
  `etat` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `permission_requests`
--

INSERT INTO `permission_requests` (`id`, `employee_id`, `reason`, `description`, `start_date`, `end_date`, `reduce`, `status`, `created`, `modified`, `etat`) VALUES
(1, 3, 'Celebrating my weddings', '<p>Monsieur le directeur,</p>\r\n<p>Je vous prie par la pr&eacute;sente de solliciter un cong&eacute; pour raison m&eacute;dicale du [date de d&eacute;but du cong&eacute;] au [date de fin du cong&eacute;], soit [nombre] jours ouvrables. J\'ai joint &agrave; ce courriel un justificatif m&eacute;dical attestant de mon incapacit&eacute; de travail. J\'ai inform&eacute; [nom du coll&egrave;gue] de mon absence et lui ai transmis les t&acirc;ches urgentes &agrave; effectuer pendant mon absence. Je serai joignable par [t&eacute;l&eacute;phone/email] en cas d\'urgence absolue. Je vous remercie de votre compr&eacute;hension et vous prie d\'agr&eacute;er, Madame/Monsieur [Nom du sup&eacute;rieur hi&eacute;rarchique], l\'expression de mes salutations distingu&eacute;es.</p>\r\n<p>Cordialement,</p>\r\n<p>Adesanya Yollo</p>', '2024-06-21 08:00:00', '2024-06-24 18:00:00', 0, 'pending', '2024-05-07 14:00:07', NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `code`, `name`) VALUES
(1, 'ADM', 'Admin'),
(2, 'EMP', 'Employee'),
(3, 'INT', 'Intern');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `configs`
--
ALTER TABLE `configs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_employee_id_contracts` (`employee_id`),
  ADD KEY `fk_contract_type_id_contracts` (`contract_type_id`);

--
-- Index pour la table `contract_models`
--
ALTER TABLE `contract_models`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_contract_type_id_contract_models` (`contract_type_id`);

--
-- Index pour la table `contract_types`
--
ALTER TABLE `contract_types`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_role_id_employees` (`role_id`);

--
-- Index pour la table `internships`
--
ALTER TABLE `internships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id_internships` (`user_id`),
  ADD KEY `fk_user_id_supervisor_internships` (`supervisor`),
  ADD KEY `fk_internship_type_id_internships` (`internship_type_id`);

--
-- Index pour la table `internship_documents`
--
ALTER TABLE `internship_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_internship_id_internship_documents` (`internship_id`),
  ADD KEY `fk_internship_document_type_id_internship_documents` (`internship_document_type_id`);

--
-- Index pour la table `internship_document_types`
--
ALTER TABLE `internship_document_types`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `internship_types`
--
ALTER TABLE `internship_types`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_employee_id_leaves` (`employee_id`);

--
-- Index pour la table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_remployee_id_permissions` (`employee_id`);

--
-- Index pour la table `permission_requests`
--
ALTER TABLE `permission_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_employee_id_permission_requests` (`employee_id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `company`
--
ALTER TABLE `company`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `configs`
--
ALTER TABLE `configs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `contract_models`
--
ALTER TABLE `contract_models`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `contract_types`
--
ALTER TABLE `contract_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `internships`
--
ALTER TABLE `internships`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `internship_documents`
--
ALTER TABLE `internship_documents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `internship_document_types`
--
ALTER TABLE `internship_document_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `internship_types`
--
ALTER TABLE `internship_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `permission_requests`
--
ALTER TABLE `permission_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `contracts`
--
ALTER TABLE `contracts`
  ADD CONSTRAINT `fk_contract_type_id_contracts` FOREIGN KEY (`contract_type_id`) REFERENCES `contract_types` (`id`),
  ADD CONSTRAINT `fk_employee_id_contracts` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Contraintes pour la table `contract_models`
--
ALTER TABLE `contract_models`
  ADD CONSTRAINT `fk_contract_type_id_contract_models` FOREIGN KEY (`contract_type_id`) REFERENCES `contract_types` (`id`);

--
-- Contraintes pour la table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_role_id_employees` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Contraintes pour la table `internships`
--
ALTER TABLE `internships`
  ADD CONSTRAINT `fk_internship_type_id_internships` FOREIGN KEY (`internship_type_id`) REFERENCES `internship_types` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_user_id_internships` FOREIGN KEY (`user_id`) REFERENCES `employees` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_user_id_supervisor_internships` FOREIGN KEY (`supervisor`) REFERENCES `employees` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `internship_documents`
--
ALTER TABLE `internship_documents`
  ADD CONSTRAINT `fk_internship_document_type_id_internship_documents` FOREIGN KEY (`internship_document_type_id`) REFERENCES `internship_document_types` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_internship_id_internship_documents` FOREIGN KEY (`internship_id`) REFERENCES `internships` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `fk_employee_id_leaves` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Contraintes pour la table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `fk_remployee_id_permissions` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Contraintes pour la table `permission_requests`
--
ALTER TABLE `permission_requests`
  ADD CONSTRAINT `fk_employee_id_permission_requests` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;