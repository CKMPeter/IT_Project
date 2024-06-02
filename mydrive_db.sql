SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `folders` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `parent` int(11) NOT NULL DEFAULT 0,
  `trash` tinyint(1) NOT NULL DEFAULT 0,
  `share_mode` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `folders` (`id`, `name`, `date_created`, `user_id`, `parent`, `trash`, `share_mode`) VALUES
(1, 'my folder', '2023-03-21 15:11:37', 1, 0, 0, 0),
(3, 'folder in folder', '2023-03-22 17:18:32', 1, 0, 0, 0),
(5, 'folder in folder3', '2023-03-22 17:20:29', 1, 1, 0, 0),
(6, 'folder in folder4', '2023-03-22 17:20:57', 1, 5, 0, 0),
(7, 'My folder', '2023-04-21 09:23:30', 4, 0, 0, 0);

CREATE TABLE `mydrive` (
  `id` int(11) NOT NULL,
  `file_name` varchar(100) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_path` varchar(1024) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL DEFAULT current_timestamp(),
  `trash` tinyint(1) NOT NULL DEFAULT 0,
  `favorite` tinyint(1) NOT NULL DEFAULT 0,
  `folder_id` int(11) NOT NULL DEFAULT 0,
  `share_mode` tinyint(1) NOT NULL DEFAULT 0,
  `slug` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `shared_to` (
  `id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`id`, `username`, `email`, `password`, `date_created`, `date_updated`) VALUES
(1, 'Eathorne', 'email@email.com', '$2y$10$6g7OFcX.5ovhQtdCZcPgveivMtzoSxxyd/Xz3PDRF2DftpLm6KZZe', '2023-03-18 13:22:09', '2023-03-18 13:22:09'),
(2, 'Mary', 'mary@email.com', '$2y$10$3TdpcVRC.4cdziOthPw7.OS17LI14RWf6m1kFTpcaJVREsruFv1Jm', '2023-03-18 13:23:59', '2023-03-18 13:23:59'),
(3, 'John', 'john@email.com', '$2y$10$OX2q0qBMKaN7cqHCXXeUnuf3u4HYRqO5DFLZKxPCgJQLdVRJsugai', '2023-04-21 09:15:02', '2023-04-21 09:15:02'),
(4, 'Peter', 'peter@email.com', '$2y$10$YKx1jyz2xsjSlpA9P2Bs4.qTC4QRvhphOO6J9GPS0Nxit/oqfkVSq', '2023-04-21 09:22:34', '2023-04-21 09:22:34');

--Adding Index
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `trash` (`trash`),
  ADD KEY `share_mode` (`share_mode`);

ALTER TABLE `mydrive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `file_name` (`file_name`),
  ADD KEY `trash` (`trash`),
  ADD KEY `favorite` (`favorite`),
  ADD KEY `folder_id` (`folder_id`),
  ADD KEY `slug` (`slug`);

ALTER TABLE `shared_to`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_id` (`file_id`),
  ADD KEY `disabled` (`disabled`),
  ADD KEY `email` (`email`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

ALTER TABLE `folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `mydrive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

ALTER TABLE `shared_to`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

CREATE TRIGGER optimizeFileName_trigger
BEFORE INSERT ON mydrive
FOR EACH ROW
BEGIN
    -- Shorten the file name to 10 characters and concatenate with the current date
    SET NEW.file_name = CONCAT(LEFT(NEW.file_name, 10), ' ', DATE_FORMAT(NOW(), '%Y-%m-%d'));
END 
COMMIT;
