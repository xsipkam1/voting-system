-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+jammy2
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: localhost:3306
-- Čas generovania: Po 13.Máj 2024, 18:13
-- Verzia serveru: 8.0.36-0ubuntu0.22.04.1
-- Verzia PHP: 8.3.3-1+ubuntu22.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáza: `vote_system`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `answers`
--

CREATE TABLE `answers` (
  `id` int NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `votes` int NOT NULL,
  `question_fk` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `answers`
--

INSERT INTO `answers` (`id`, `description`, `votes`, `question_fk`) VALUES
(67, 'Jupiter', 0, 39),
(68, 'Venusa', 1, 39),
(69, 'Slnko', 1, 39),
(70, 'Merkur', 0, 39),
(75, 'lol', 0, 42),
(76, 'ahoj', 1, 42),
(77, 'ahoj', 1, 41);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `questions`
--

CREATE TABLE `questions` (
  `id` int NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `date_created` date NOT NULL,
  `date_closed` date DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  `note_closed` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `code` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `user_fk` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `questions`
--

INSERT INTO `questions` (`id`, `description`, `type`, `subject`, `date_created`, `date_closed`, `active`, `note_closed`, `code`, `user_fk`) VALUES
(39, 'Aka je najhorucejsia planeta slnecnej sustavy?', 'list', 'vesmir', '2024-05-13', NULL, 0, NULL, 'qBiAv', 18),
(41, 'test exportu', 'text', 'test', '2024-05-13', NULL, 1, NULL, 'dqQbX', 18),
(42, 'test list', 'list', 'test', '2024-05-13', NULL, 1, NULL, 'NEXrQ', 18);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `login` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(1) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `role`) VALUES
(18, 'u1', '$argon2id$v=19$m=65536,t=4,p=1$V21XNi5ZdUg0emVCNFlXMw$+IF5uxtc68bsly/1EglW2lnkdGqOjMi9jWk88zAD2Sk', 'A'),
(19, 'u2', '$argon2id$v=19$m=65536,t=4,p=1$emhvQUhqTnRVTUtuQzhaTw$PLQw7fXA1EDKQEhGcYBmpc6bsoNUwgOySo6y/Qd9WmQ', 'U');

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_fk` (`question_fk`);

--
-- Indexy pre tabuľku `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `user_fk` (`user_fk`);

--
-- Indexy pre tabuľku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT pre tabuľku `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT pre tabuľku `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_fk`) REFERENCES `questions` (`id`);

--
-- Obmedzenie pre tabuľku `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`user_fk`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
