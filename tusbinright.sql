-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2025 at 01:22 PM
-- Server version: 8.0.42
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

    SELECT COUNT(*) INTO email_count
    FROM users
    WHERE email = p_email;

    IF email_count > 0 THEN
        SELECT 1 AS status, 'Email exists' AS message;
    ELSE
        SELECT 0 AS status, 'Email does not exist' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateBinStep` (IN `p_description` VARCHAR(255), IN `p_bin_type_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to create bin step' AS message;
    END;

    START TRANSACTION;
        INSERT INTO BinStep (description, bin_type_id)
        VALUES (p_description, p_bin_type_id);
    COMMIT;

    SELECT 1 AS status, 'Bin step created successfully' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateBinType` (IN `p_name` VARCHAR(100), IN `p_description` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to create bin type' AS message;
    END;

    START TRANSACTION;
        INSERT INTO BinType (name, description)
        VALUES (p_name, p_description);
    COMMIT;

    SELECT 1 AS status, 'Bin type created successfully' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateDisposalRule` (IN `p_item_id` INT, IN `p_location_id` INT, IN `p_bin_type_id` INT, IN `p_description` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to create disposal rule' AS message;
    END;

    START TRANSACTION;
        INSERT INTO DisposalRule (item_id, location_id, bin_type_id, description)
        VALUES (p_item_id, p_location_id, p_bin_type_id, p_description);
    COMMIT;

    SELECT 1 AS status, 'Disposal rule created successfully' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateItem` (IN `p_name` VARCHAR(100), IN `p_description` VARCHAR(255), IN `p_bar_code` VARCHAR(255), IN `p_qr_code` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to create item' AS message;
    END;

    START TRANSACTION;
        INSERT INTO Item (name, description, bar_code, qr_code)
        VALUES (p_name, p_description, p_bar_code, p_qr_code);
    COMMIT;

    SELECT 1 AS status, 'Item created successfully' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateLocation` (IN `p_name` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to create location' AS message;
    END;

    START TRANSACTION;
        INSERT INTO Location (name)
        VALUES (p_name);
    COMMIT;

    SELECT 1 AS status, 'Location created successfully' AS message;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteBinStep` (IN `p_bin_step_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to delete bin step' AS message;
    END;

    START TRANSACTION;
        DELETE FROM BinStep WHERE id = p_bin_step_id;
    COMMIT;

    SELECT 1 AS status, 'Bin step deleted successfully' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteBinType` (IN `p_bin_type_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to delete bin type' AS message;
    END;

    START TRANSACTION;
        DELETE FROM BinType WHERE id = p_bin_type_id;
    COMMIT;

    SELECT 1 AS status, 'Bin type deleted successfully' AS message;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteItem` (IN `p_item_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to delete item' AS message;
    END;

    START TRANSACTION;
        DELETE FROM Item WHERE id = p_item_id;
    COMMIT;

    SELECT 1 AS status, 'Item deleted successfully' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteLocation` (IN `p_location_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to delete location' AS message;
    END;

    START TRANSACTION;
        DELETE FROM Location WHERE id = p_location_id;
    COMMIT;

    SELECT 1 AS status, 'Location deleted successfully' AS message;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetBinStepById` (IN `p_bin_step_id` INT)   BEGIN
    SELECT 
        bs.id,
        bs.description AS step_description,
        bt.name AS bin_type
    FROM BinStep bs
    INNER JOIN BinType bt ON bs.bin_type_id = bt.id
    WHERE bs.id = p_bin_step_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetBinSteps` ()   BEGIN
    SELECT 
        bs.id,
        bs.description AS step_description,
        bt.name AS bin_type
    FROM BinStep bs
    INNER JOIN BinType bt ON bs.bin_type_id = bt.id
    ORDER BY bs.id;
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
        i.name AS item,
        l.name AS location,
        bt.name AS bin_type,
        dr.description
    FROM DisposalRule dr
    INNER JOIN Item i ON dr.item_id = i.id
    INNER JOIN Location l ON dr.location_id = l.id
    INNER JOIN BinType bt ON dr.bin_type_id = bt.id
    WHERE dr.id = p_disposal_rule_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetDisposalRules` ()   BEGIN
    SELECT 
        dr.id,
        i.name AS item,
        l.name AS location,
        bt.name AS bin_type,
        dr.description
    FROM DisposalRule dr
    INNER JOIN Item i ON dr.item_id = i.id
    INNER JOIN Location l ON dr.location_id = l.id
    INNER JOIN BinType bt ON dr.bin_type_id = bt.id
    ORDER BY dr.id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetItemById` (IN `p_item_id` INT)   BEGIN
    SELECT 
        i.id,
        i.name AS item,
        i.description,
        i.bar_code,
        i.qr_code
    FROM Item i
    WHERE i.id = p_item_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetItems` ()   BEGIN
    SELECT 
        i.id,
        i.name AS item,
        i.description,
        i.bar_code,
        i.qr_code
    FROM Item i
    ORDER BY i.id;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateBinStep` (IN `p_bin_step_id` INT, IN `p_description` VARCHAR(255), IN `p_bin_type_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to update bin step' AS message;
    END;

    START TRANSACTION;
        UPDATE BinStep
        SET description = p_description, bin_type_id = p_bin_type_id
        WHERE id = p_bin_step_id;
    COMMIT;

    SELECT 1 AS status, 'Bin step updated successfully' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateBinType` (IN `p_bin_type_id` INT, IN `p_name` VARCHAR(100), IN `p_description` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to update bin type' AS message;
    END;

    START TRANSACTION;
        UPDATE BinType
        SET name = p_name, description = p_description
        WHERE id = p_bin_type_id;
    COMMIT;

    SELECT 1 AS status, 'Bin type updated successfully' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateDisposalRule` (IN `p_disposal_rule_id` INT, IN `p_item_id` INT, IN `p_location_id` INT, IN `p_bin_type_id` INT, IN `p_description` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to update disposal rule' AS message;
    END;

    START TRANSACTION;
        UPDATE DisposalRule
        SET item_id = p_item_id,
            location_id = p_location_id,
            bin_type_id = p_bin_type_id,
            description = p_description
        WHERE id = p_disposal_rule_id;
    COMMIT;

    SELECT 1 AS status, 'Disposal rule updated successfully' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateItem` (IN `p_item_id` INT, IN `p_name` VARCHAR(100), IN `p_description` VARCHAR(255), IN `p_bar_code` VARCHAR(255), IN `p_qr_code` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to update item' AS message;
    END;

    START TRANSACTION;
        UPDATE Item
        SET name = p_name,
            description = p_description,
            bar_code = p_bar_code,
            qr_code = p_qr_code
        WHERE id = p_item_id;
    COMMIT;

    SELECT 1 AS status, 'Item updated successfully' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateLocation` (IN `p_location_id` INT, IN `p_name` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to update location' AS message;
    END;

    START TRANSACTION;
        UPDATE Location
        SET name = p_name
        WHERE id = p_location_id;
    COMMIT;

    SELECT 1 AS status, 'Location updated successfully' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateUser` (IN `p_user_id` INT, IN `p_name` VARCHAR(100), IN `p_email` VARCHAR(255), IN `p_password_hash` VARCHAR(255), IN `p_user_type_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 0 AS status, 'Failed to update user' AS message;
    END;

    START TRANSACTION;
        UPDATE users 
        SET 
            name = p_name,
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
-- Table structure for table `binstep`
--

CREATE TABLE `binstep` (
  `id` int NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `bin_type_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `binstep`
--

INSERT INTO `binstep` (`id`, `description`, `bin_type_id`) VALUES
(4, 'Cut boxes', 1);

-- --------------------------------------------------------

--
-- Table structure for table `bintype`
--

CREATE TABLE `bintype` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
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
  `id` int NOT NULL,
  `item_id` int NOT NULL,
  `location_id` int NOT NULL,
  `bin_type_id` int NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bar_code` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `qr_code` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `user_type_id` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `user_type_id`, `created_at`, `updated_at`) VALUES
(1, 'Marius', 'marius.c49@Yahoo.com', '$2y$10$F0XEGrJxMYwgaFeitnzdj.ccQvmObRXF0sftg5yTTXi5RJ5zsMHje', 1, '2025-10-16 14:05:57', '2025-10-21 10:41:24'),
(2, 'alex', 'alex@gmail.ie', '$2y$10$W3/Mpff1VcrFqjLmboMZE.CBcjqGYgZbylpSH86duPNdOy6JNmCAO', 1, '2025-10-16 14:06:43', '2025-10-21 09:27:16'),
(3, 'lee', 'lee@gmail.com', '$2y$10$f6sr7.XCV54U1g9naPQpPe9eUvp.1zZWuumDZwtNawUsoSm1gIICu', 2, '2025-10-16 15:23:05', '2025-10-21 09:14:18'),
(4, 'lee', 'leej@gmail.com', '$2y$10$SoKQshB.PrlxHU09V7G6wuXExbpdtNmA7IemnZGFg.PEJ2rS.JGv.', 1, '2025-10-16 15:23:18', '2025-10-16 15:23:18'),
(5, 'test123', 'test123@gmail.com', '$2y$10$CNMluJwa8SsdbfcK3ZmRBuA18GYEDfFEpI7beXH4nbNLDuofWed.u', 1, '2025-10-16 15:36:21', '2025-10-16 15:36:21'),
(6, 'roberto', 'roberto@gmail.com', '$2y$10$mjbbMZe.xGvuTbwQfbyPSuYblahZDyjwx/8niWxod3/kWow2rv4Wu', 1, '2025-10-17 08:32:06', '2025-10-17 08:32:06'),
(7, 'yevhen', 'yevhen@gmail.com', '$2y$10$c9zinwh2xeJPU1W08qBMXOwujtHI9J5RtyKVrizTh1sMF0eKulGhq', 1, '2025-10-20 10:21:15', '2025-10-20 10:21:15'),
(8, 'marius', 'k00294842@student.tus.ie', '$2y$10$d8Vrkn7nNXjWNHZc6UcwZO6Y6qwgZwW2uk.uTF7aLvsL8DZOTqIyO', 2, '2025-10-20 10:42:11', '2025-10-20 11:05:24'),
(9, 'mahommed', 'mahommed@gmail.com', '$2y$10$luy4Po4NddKRBEsu2Y48J.q4rJMFvFUxh4jTTmQhjWA5QWJXfT5Ae', 1, '2025-10-20 12:00:16', '2025-10-20 12:00:16'),
(11, 'Mbappe', 'mbappe@gmail.com', '$2y$10$UHgA0iCCgv0RmmV3vEKQnOfOocmZ6aPN3rw5A8SM22BFO7warOsPK', 1, '2025-10-20 21:31:14', '2025-10-21 10:42:26');

-- --------------------------------------------------------

--
-- Table structure for table `usertype`
--

CREATE TABLE `usertype` (
  `id` int NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
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
-- Indexes for table `binstep`
--
ALTER TABLE `binstep`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bin_type_id` (`bin_type_id`);

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
  ADD KEY `item_id` (`item_id`),
  ADD KEY `location_id` (`location_id`),
  ADD KEY `bin_type_id` (`bin_type_id`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bar_code` (`bar_code`),
  ADD UNIQUE KEY `qr_code` (`qr_code`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_type_id` (`user_type_id`);

--
-- Indexes for table `usertype`
--
ALTER TABLE `usertype`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `binstep`
--
ALTER TABLE `binstep`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bintype`
--
ALTER TABLE `bintype`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `disposalrule`
--
ALTER TABLE `disposalrule`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `usertype`
--
ALTER TABLE `usertype`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `binstep`
--
ALTER TABLE `binstep`
  ADD CONSTRAINT `binstep_ibfk_1` FOREIGN KEY (`bin_type_id`) REFERENCES `bintype` (`id`);

--
-- Constraints for table `disposalrule`
--
ALTER TABLE `disposalrule`
  ADD CONSTRAINT `disposalrule_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`),
  ADD CONSTRAINT `disposalrule_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`),
  ADD CONSTRAINT `disposalrule_ibfk_3` FOREIGN KEY (`bin_type_id`) REFERENCES `bintype` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`user_type_id`) REFERENCES `usertype` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
