-- 1. Creazione e selezione del Database (Perfetto, lasciamo così)
CREATE DATABASE IF NOT EXISTS utenti;
USE utenti;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `Username` varchar(50) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `Cognome` varchar(50) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Admin` tinyint(1) NOT NULL DEFAULT '0',
  `avatar_3d` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`Username`); 

-- Svuota la tabella se c'erano dati vecchi
TRUNCATE TABLE `utenti`;

INSERT INTO `utenti` (`Username`, `Nome`, `Cognome`, `Email`, `Password`, `Admin`) VALUES
('test', 'Mario', 'Rossi','test@mail.com', 'dce2a1d10cad7aaad66071106262fa4c5f5d0f7a102a723422174d80a303f6f021048446f1c9a7de16bc6a8285d650f8c2ccf523ee0fe70ded7a55d331bc61b0', 1);


COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;