-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 30, 2025 at 10:44 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tusbinright`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `CheckEmailExists` (IN `p_email` VARCHAR(255))   BEGIN
    DECLARE email_count INT DEFAULT 0;
    SELECT COUNT(*) INTO email_count FROM users WHERE email = p_email;

    IF email_count > 0 THEN
        SELECT 1 AS status, 'Email exists' AS message;
    ELSE
        SELECT 0 AS status, 'Email does not exist' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateDisposalRule` (IN `p_item_type_id` INT, IN `p_location_id` INT, IN `p_bin_type_id` INT, IN `p_description` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to create disposal rule' AS message;
    END;

    START TRANSACTION;
        INSERT INTO DisposalRule (item_type_id, location_id, bin_type_id, description)
        VALUES (p_item_type_id, p_location_id, p_bin_type_id, p_description);
    COMMIT;

    SELECT 1 AS status, 'Disposal rule created successfully' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateUser` (IN `p_name` VARCHAR(100), IN `p_email` VARCHAR(255), IN `p_password_hash` VARCHAR(255), IN `p_user_type_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to create user' AS message;
    END;

    START TRANSACTION;
        INSERT INTO users (name, email, password_hash, user_type_id)
        VALUES (p_name, p_email, p_password_hash, p_user_type_id);
    COMMIT;

    SELECT 1 AS status, 'User created successfully' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateUserScan` (IN `p_user_id` INT, IN `p_item_type_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to create user scan' AS message;
    END;

    START TRANSACTION;
        INSERT INTO UserScan (user_id, item_type_id)
        VALUES (p_user_id, p_item_type_id);
    COMMIT;

    SELECT 1 AS status, 'User scan recorded successfully' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteDisposalRule` (IN `p_disposal_rule_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to delete disposal rule' AS message;
    END;

    START TRANSACTION;
        DELETE FROM DisposalRule WHERE id = p_disposal_rule_id;
    COMMIT;

    SELECT 1 AS status, 'Disposal rule deleted successfully' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteUser` (IN `p_user_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to delete user' AS message;
    END;

    START TRANSACTION;
        DELETE FROM users WHERE id = p_user_id;
    COMMIT;

    SELECT 1 AS status, 'User deleted successfully' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteUserScans` (IN `p_scan_ids` TEXT)   BEGIN
    DECLARE exit handler FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to delete user scans' AS message;
    END;

    START TRANSACTION;

    SET @sql = CONCAT('DELETE FROM UserScan WHERE id IN (', p_scan_ids, ')');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    COMMIT;

    SELECT 1 AS status, 'User scans deleted successfully' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllUsers` ()   BEGIN
    SELECT 
        u.id, 
        u.name AS user, 
        u.email, 
        u.user_type_id,
        ut.description AS user_type
    FROM users u
    INNER JOIN usertype ut ON u.user_type_id = ut.id
    ORDER BY u.id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetBinTypeById` (IN `p_bin_type_id` INT)   BEGIN
    SELECT 
        bt.id,
        bt.name AS bin_type,
        bt.description
    FROM BinType bt
    WHERE bt.id = p_bin_type_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetBinTypes` ()   BEGIN
    SELECT 
        bt.id,
        bt.name AS bin_type,
        bt.description
    FROM BinType bt
    ORDER BY bt.id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetDisposalRuleById` (IN `p_disposal_rule_id` INT)   BEGIN
    SELECT 
        dr.id,
        it.name AS item_type,
        l.name AS location,
        bt.name AS bin_type,
        dr.description
    FROM DisposalRule dr
    INNER JOIN ItemType it ON dr.item_type_id = it.id
    INNER JOIN Location l ON dr.location_id = l.id
    INNER JOIN BinType bt ON dr.bin_type_id = bt.id
    WHERE dr.id = p_disposal_rule_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetDisposalRules` ()   BEGIN
    SELECT 
        dr.id,
        it.name AS item_type,
        l.name AS location,
        bt.name AS bin_type,
        dr.description
    FROM DisposalRule dr
    INNER JOIN ItemType it ON dr.item_type_id = it.id
    INNER JOIN Location l ON dr.location_id = l.id
    INNER JOIN BinType bt ON dr.bin_type_id = bt.id
    ORDER BY dr.id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetDisposalRulesByItemAndLocationId` (IN `p_item_type_id` INT, IN `p_location_id` INT)   BEGIN
    SELECT 
        dr.id,
        it.name AS item_type,
        l.name AS location,
        bt.name AS bin_type,
        dr.description
    FROM disposalrule dr
    INNER JOIN itemtype it ON dr.item_type_id = it.id
    INNER JOIN location l ON dr.location_id = l.id
    INNER JOIN bintype bt ON dr.bin_type_id = bt.id
    WHERE dr.item_type_id = p_item_type_id
      AND dr.location_id = p_location_id
    ORDER BY dr.id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetItemTypeById` (IN `p_item_type_id` INT)   BEGIN
    SELECT 
        it.id,
        it.name AS item_type,
        it.description
    FROM ItemType it
    WHERE it.id = p_item_type_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetItemTypes` ()   BEGIN
    SELECT 
        it.id,
        it.name AS item_type,
        it.description
    FROM ItemType it
    ORDER BY it.id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetLocationById` (IN `p_location_id` INT)   BEGIN
    SELECT 
        l.id,
        l.name AS location
    FROM Location l
    WHERE l.id = p_location_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetLocations` ()   BEGIN
    SELECT 
        l.id,
        l.name AS location
    FROM Location l
    ORDER BY l.id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTotalScans` ()   BEGIN
    SELECT 
        COUNT(*) AS total_scans
    FROM userscan;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTotalUsers` ()   BEGIN
    SELECT 
        COUNT(*) AS total_users
    FROM users;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserById` (IN `p_user_id` INT)   BEGIN
    SELECT 
        u.id,
        u.name AS user,
        u.email,
        ut.description AS user_type
    FROM users u
    INNER JOIN usertype ut ON u.user_type_id = ut.id
    WHERE u.id = p_user_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserScans` (IN `p_user_id` INT, IN `p_start_date` DATETIME, IN `p_end_date` DATETIME, IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SET @sql = '
        SELECT 
            us.id,
            us.user_id,
            u.name AS user_name,
            it.name AS item_type,
            us.created_at
        FROM UserScan us
        INNER JOIN users u ON us.user_id = u.id
        INNER JOIN ItemType it ON us.item_type_id = it.id
        WHERE 1 = 1
    ';

    -- Filter by user if provided
    IF p_user_id IS NOT NULL THEN
        SET @sql = CONCAT(@sql, ' AND us.user_id = ', p_user_id);
    END IF;

    -- Filter by date range if both dates provided
    IF p_start_date IS NOT NULL AND p_end_date IS NOT NULL THEN
        SET @sql = CONCAT(@sql, 
            " AND us.created_at BETWEEN '", p_start_date, "' AND '", p_end_date, "'"
        );
    END IF;

    -- Add ordering and pagination
    SET @sql = CONCAT(@sql, 
        ' ORDER BY us.created_at DESC ',
        ' LIMIT ', p_limit, ' OFFSET ', p_offset
    );

    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateDisposalRule` (IN `p_disposal_rule_id` INT, IN `p_item_type_id` INT, IN `p_location_id` INT, IN `p_bin_type_id` INT, IN `p_description` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to update disposal rule' AS message;
    END;

    START TRANSACTION;
        UPDATE DisposalRule
        SET item_type_id = p_item_type_id,
            location_id = p_location_id,
            bin_type_id = p_bin_type_id,
            description = p_description
        WHERE id = p_disposal_rule_id;
    COMMIT;

    SELECT 1 AS status, 'Disposal rule updated successfully' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateUser` (IN `p_user_id` INT, IN `p_name` VARCHAR(100), IN `p_email` VARCHAR(255), IN `p_password_hash` VARCHAR(255), IN `p_user_type_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to update user' AS message;
    END;

    START TRANSACTION;
        UPDATE users
        SET name = p_name,
            email = p_email,
            password_hash = p_password_hash,
            user_type_id = p_user_type_id
        WHERE id = p_user_id;
    COMMIT;

    SELECT 1 AS status, 'User updated successfully' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UserLogin` (IN `p_email` VARCHAR(255), IN `p_password_hash` VARCHAR(255))   BEGIN
    SELECT 
        u.id,
        u.name,
        u.email,
        ut.description AS user_type
    FROM users u
    INNER JOIN usertype ut ON u.user_type_id = ut.id
    WHERE u.email = p_email
      AND u.password_hash = p_password_hash
    LIMIT 1;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `bintype`
--

CREATE TABLE `bintype` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bintype`
--

INSERT INTO `bintype` (`id`, `name`, `description`) VALUES
(1, 'Blue Bin', 'Paper & cardboard'),
(2, 'Yellow Bin', 'Plastic bottles, tubs, trays'),
(3, 'Black Bin', 'General Waste'),
(4, 'Brown Bin', 'Organic Waste');

-- --------------------------------------------------------

--
-- Table structure for table `disposalrule`
--

CREATE TABLE `disposalrule` (
  `id` int(11) NOT NULL,
  `item_type_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `bin_type_id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `itemtype`
--

CREATE TABLE `itemtype` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `itemtype`
--

INSERT INTO `itemtype` (`id`, `name`, `description`) VALUES
(1, 'Plastic', 'Plastic containers and bottles'),
(2, 'Can', 'Metal cans'),
(3, 'Glass', 'Glass jars and bottles'),
(4, 'Paper', 'Paper, cardboard, newspapers');

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`id`, `name`) VALUES
(1, 'Moylish Campus'),
(2, 'Home'),
(3, 'Work');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 1,
  `last_attempt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `locked_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `user_type_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `avatar`, `password_hash`, `user_type_id`, `created_at`, `updated_at`) VALUES
(1, 'Marius', 'marius.c49@Yahoo.com', NULL, '$2y$10$F0XEGrJxMYwgaFeitnzdj.ccQvmObRXF0sftg5yTTXi5RJ5zsMHje', 1, '2025-10-16 14:05:57', '2025-10-21 10:41:24'),
(2, 'alex', 'alex@gmail.ie', NULL, '$2y$10$W3/Mpff1VcrFqjLmboMZE.CBcjqGYgZbylpSH86duPNdOy6JNmCAO', 1, '2025-10-16 14:06:43', '2025-10-21 09:27:16'),
(3, 'lee', 'lee@gmail.com', NULL, '$2y$10$f6sr7.XCV54U1g9naPQpPe9eUvp.1zZWuumDZwtNawUsoSm1gIICu', 2, '2025-10-16 15:23:05', '2025-10-21 09:14:18'),
(4, 'lee', 'leej@gmail.com', NULL, '$2y$10$SoKQshB.PrlxHU09V7G6wuXExbpdtNmA7IemnZGFg.PEJ2rS.JGv.', 1, '2025-10-16 15:23:18', '2025-10-16 15:23:18'),
(5, 'test123', 'test123@gmail.com', NULL, '$2y$10$CNMluJwa8SsdbfcK3ZmRBuA18GYEDfFEpI7beXH4nbNLDuofWed.u', 1, '2025-10-16 15:36:21', '2025-10-16 15:36:21'),
(6, 'roberto', 'roberto@gmail.com', NULL, '$2y$10$mjbbMZe.xGvuTbwQfbyPSuYblahZDyjwx/8niWxod3/kWow2rv4Wu', 1, '2025-10-17 08:32:06', '2025-10-17 08:32:06'),
(7, 'yevhen', 'yevhen@gmail.com', NULL, '$2y$10$c9zinwh2xeJPU1W08qBMXOwujtHI9J5RtyKVrizTh1sMF0eKulGhq', 1, '2025-10-20 10:21:15', '2025-10-20 10:21:15'),
(8, 'marius', 'k00294842@student.tus.ie', NULL, '$2y$10$d8Vrkn7nNXjWNHZc6UcwZO6Y6qwgZwW2uk.uTF7aLvsL8DZOTqIyO', 2, '2025-10-20 10:42:11', '2025-10-20 11:05:24'),
(9, 'mahommed', 'mahommed@gmail.com', NULL, '$2y$10$luy4Po4NddKRBEsu2Y48J.q4rJMFvFUxh4jTTmQhjWA5QWJXfT5Ae', 1, '2025-10-20 12:00:16', '2025-10-20 12:00:16'),
(11, 'Mbappe', 'mbappe@gmail.com', NULL, '$2y$10$UHgA0iCCgv0RmmV3vEKQnOfOocmZ6aPN3rw5A8SM22BFO7warOsPK', 1, '2025-10-20 21:31:14', '2025-10-21 10:42:26');

-- --------------------------------------------------------

--
-- Table structure for table `userscan`
--

CREATE TABLE `userscan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_type_id` int(11) NOT NULL,
  `is_accurate` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userscan`
--

INSERT INTO `userscan` (`id`, `user_id`, `item_type_id`, `is_accurate`, `created_at`) VALUES
(1, 1, 1, NULL, '2025-10-25 08:00:00'),
(2, 1, 3, NULL, '2025-10-26 09:30:00'),
(3, 3, 2, NULL, '2025-10-27 10:15:00'),
(4, 5, 4, NULL, '2025-10-28 14:20:00'),
(5, 8, 1, NULL, '2025-10-29 08:45:00'),
(6, 8, 1, NULL, '2025-11-17 11:31:29'),
(7, 8, 2, NULL, '2025-11-30 21:15:07'),
(8, 8, 2, NULL, '2025-11-30 21:23:44');

-- --------------------------------------------------------

--
-- Table structure for table `usertype`
--

CREATE TABLE `usertype` (
  `id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usertype`
--

INSERT INTO `usertype` (`id`, `description`) VALUES
(1, 'Student'),
(2, 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bintype`
--
ALTER TABLE `bintype`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `disposalrule`
--
ALTER TABLE `disposalrule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_type_id` (`item_type_id`),
  ADD KEY `location_id` (`location_id`),
  ADD KEY `bin_type_id` (`bin_type_id`);

--
-- Indexes for table `itemtype`
--
ALTER TABLE `itemtype`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_ip` (`email`,`ip_address`),
  ADD KEY `locked_until` (`locked_until`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_type_id` (`user_type_id`);

--
-- Indexes for table `userscan`
--
ALTER TABLE `userscan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_type_id` (`item_type_id`),
  ADD KEY `idx_userscan_accuracy` (`is_accurate`);

--
-- Indexes for table `usertype`
--
ALTER TABLE `usertype`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bintype`
--
ALTER TABLE `bintype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `disposalrule`
--
ALTER TABLE `disposalrule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `itemtype`
--
ALTER TABLE `itemtype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `userscan`
--
ALTER TABLE `userscan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `usertype`
--
ALTER TABLE `usertype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `disposalrule`
--
ALTER TABLE `disposalrule`
  ADD CONSTRAINT `disposalrule_ibfk_1` FOREIGN KEY (`item_type_id`) REFERENCES `itemtype` (`id`),
  ADD CONSTRAINT `disposalrule_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`),
  ADD CONSTRAINT `disposalrule_ibfk_3` FOREIGN KEY (`bin_type_id`) REFERENCES `bintype` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`user_type_id`) REFERENCES `usertype` (`id`);

--
-- Constraints for table `userscan`
--
ALTER TABLE `userscan`
  ADD CONSTRAINT `userscan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `userscan_ibfk_2` FOREIGN KEY (`item_type_id`) REFERENCES `itemtype` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
