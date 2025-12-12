-- 1. AGGIUNTA FONDAMENTALE PER DOCKER:
CREATE DATABASE IF NOT EXISTS gallary;
USE gallary;

-- 2. IL TUO CODICE ORIGINALE (Confermo che è corretto):
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Struttura della tabella `gallary`
--

CREATE TABLE `gallary` (
  `title` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `path` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indici per le tabelle `gallary`
--
ALTER TABLE `gallary`
  ADD PRIMARY KEY (`path`);

COMMIT;

-- Tabella per i commenti
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_ref` varchar(255) NOT NULL, -- Si collega al 'path' del file GLB
  `username` varchar(50) NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Svuota la tabella se c'erano dati vecchi
TRUNCATE TABLE `gallary`;

INSERT INTO `gallary` (`title`, `category`, `path`) VALUES
-- MEDICINA (Virus e Organi)
('Corona Virus Covid-19', 'Medicina', 'corona_virus_covid-19.glb'),
('Cuore Umano', 'Medicina', 'heart.glb'),
('Teschio Umano', 'Medicina', 'exploding_skull.glb'),

-- CHIMICA (Strutture molecolari)
('Reticolo Cristallino Ghiaccio', 'Chimica', 'ice_lattice.glb'),
('Struttura Molecolare CO2', 'Chimica', 'co2.glb'),

-- INGEGNERIA (Motori e Auto)
('Blocco Motore V8', 'Ingegneria', 'disassembled_v8_engine_block.glb'),
('Iphone 12 Teadown', 'Ingegneria', 'iphone_12_teardown.glb'),
('Motore a Scoppio', 'Ingegneria', 'engine_work.glb'),
('Interno motore', 'Ingegneria', 'engine_exloded_view.glb'),
('Funzionamento Penna', 'Ingegneria', 'click_pen_mechanism.glb'),

COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;