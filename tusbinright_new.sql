-- --------------------------------------------------------
-- Database: tusbinright
-- --------------------------------------------------------
DROP DATABASE IF EXISTS tusbinright;
CREATE DATABASE tusbinright CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE tusbinright;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Tables
-- --------------------------------------------------------

-- UserType
CREATE TABLE usertype (
  id INT NOT NULL AUTO_INCREMENT,
  description VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO usertype (id, description) VALUES
(1, 'Student'),
(2, 'Admin');

-- Users
CREATE TABLE users (
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  user_type_id INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY user_type_id (user_type_id),
  CONSTRAINT users_ibfk_1 FOREIGN KEY (user_type_id) REFERENCES usertype (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (id, name, email, password_hash, user_type_id, created_at, updated_at) VALUES
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

-- BinType
CREATE TABLE bintype (
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  description VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO bintype (id, name, description) VALUES
(1, 'Blue Bin', 'Paper & cardboard'),
(2, 'Yellow Bin', 'Plastic bottles, tubs, trays'),
(3, 'Black Bin', 'General Waste'),
(4, 'Brown Bin', 'Organic Waste');

-- Location
CREATE TABLE location (
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO location (id, name) VALUES
(1, 'Moylish Campus'),
(2, 'Home'),
(3, 'Work');

-- ItemType
CREATE TABLE itemtype (
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  description VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO itemtype (id, name, description) VALUES
(1, 'Plastic', 'Plastic containers and bottles'),
(2, 'Can', 'Metal cans'),
(3, 'Glass', 'Glass jars and bottles'),
(4, 'Paper', 'Paper, cardboard, newspapers');

-- DisposalRule
CREATE TABLE disposalrule (
  id INT NOT NULL AUTO_INCREMENT,
  item_type_id INT NOT NULL,
  location_id INT NOT NULL,
  bin_type_id INT NOT NULL,
  description VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY item_type_id (item_type_id),
  KEY location_id (location_id),
  KEY bin_type_id (bin_type_id),
  CONSTRAINT disposalrule_ibfk_1 FOREIGN KEY (item_type_id) REFERENCES itemtype (id),
  CONSTRAINT disposalrule_ibfk_2 FOREIGN KEY (location_id) REFERENCES location (id),
  CONSTRAINT disposalrule_ibfk_3 FOREIGN KEY (bin_type_id) REFERENCES bintype (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- UserScan
CREATE TABLE userscan (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  item_type_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY user_id (user_id),
  KEY item_type_id (item_type_id),
  CONSTRAINT userscan_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id),
  CONSTRAINT userscan_ibfk_2 FOREIGN KEY (item_type_id) REFERENCES itemtype (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO userscan (user_id, item_type_id, created_at) VALUES
(1, 1, '2025-10-25 08:00:00'),
(1, 3, '2025-10-26 09:30:00'),
(3, 2, '2025-10-27 10:15:00'),
(5, 4, '2025-10-28 14:20:00'),
(8, 1, '2025-10-29 08:45:00');

COMMIT;

-- --------------------------------------------------------
-- Stored Procedures
-- --------------------------------------------------------

DELIMITER $$

CREATE PROCEDURE CheckEmailExists (IN p_email VARCHAR(255))
BEGIN
    DECLARE email_count INT DEFAULT 0;
    SELECT COUNT(*) INTO email_count FROM users WHERE email = p_email;

    IF email_count > 0 THEN
        SELECT 1 AS status, 'Email exists' AS message;
    ELSE
        SELECT 0 AS status, 'Email does not exist' AS message;
    END IF;
END$$


CREATE PROCEDURE CreateUser (
    IN p_name VARCHAR(100),
    IN p_email VARCHAR(255),
    IN p_password_hash VARCHAR(255),
    IN p_user_type_id INT
)
BEGIN
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


CREATE PROCEDURE DeleteUser (IN p_user_id INT)
BEGIN
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


CREATE PROCEDURE GetAllUsers ()
BEGIN
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


CREATE PROCEDURE GetUserById (IN p_user_id INT)
BEGIN
    SELECT 
        u.id,
        u.name AS user,
        u.email,
        ut.description AS user_type
    FROM users u
    INNER JOIN usertype ut ON u.user_type_id = ut.id
    WHERE u.id = p_user_id;
END$$


CREATE PROCEDURE UpdateUser (
    IN p_user_id INT,
    IN p_name VARCHAR(100),
    IN p_email VARCHAR(255),
    IN p_password_hash VARCHAR(255),
    IN p_user_type_id INT
)
BEGIN
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


CREATE PROCEDURE UserLogin (IN p_email VARCHAR(255), IN p_password_hash VARCHAR(255))
BEGIN
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


CREATE PROCEDURE GetTotalUsers()
BEGIN
    SELECT 
        COUNT(*) AS total_users
    FROM users;
END$$


CREATE PROCEDURE GetBinTypeById (IN p_bin_type_id INT)
BEGIN
    SELECT 
        bt.id,
        bt.name AS bin_type,
        bt.description
    FROM BinType bt
    WHERE bt.id = p_bin_type_id;
END$$


CREATE PROCEDURE GetBinTypes ()
BEGIN
    SELECT 
        bt.id,
        bt.name AS bin_type,
        bt.description
    FROM BinType bt
    ORDER BY bt.id;
END$$


CREATE PROCEDURE GetLocationById (IN p_location_id INT)
BEGIN
    SELECT 
        l.id,
        l.name AS location
    FROM Location l
    WHERE l.id = p_location_id;
END$$


CREATE PROCEDURE GetLocations ()
BEGIN
    SELECT 
        l.id,
        l.name AS location
    FROM Location l
    ORDER BY l.id;
END$$


CREATE PROCEDURE CreateDisposalRule (
    IN p_item_type_id INT,
    IN p_location_id INT,
    IN p_bin_type_id INT,
    IN p_description VARCHAR(255)
)
BEGIN
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


CREATE PROCEDURE GetDisposalRules ()
BEGIN
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


CREATE PROCEDURE GetDisposalRuleById (IN p_disposal_rule_id INT)
BEGIN
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


CREATE PROCEDURE GetDisposalRulesByItemAndLocationId(
    IN p_item_type_id INT,
    IN p_location_id INT
)
BEGIN
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


CREATE PROCEDURE UpdateDisposalRule (
    IN p_disposal_rule_id INT,
    IN p_item_type_id INT,
    IN p_location_id INT,
    IN p_bin_type_id INT,
    IN p_description VARCHAR(255)
)
BEGIN
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


CREATE PROCEDURE DeleteDisposalRule (IN p_disposal_rule_id INT)
BEGIN
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


CREATE PROCEDURE CreateUserScan (IN p_user_id INT, IN p_item_type_id INT)
BEGIN
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


CREATE PROCEDURE GetUserScans (
    IN p_user_id INT,
    IN p_start_date DATETIME,
    IN p_end_date DATETIME,
    IN p_limit INT,
    IN p_offset INT
)
BEGIN
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


CREATE PROCEDURE DeleteUserScans(IN p_scan_ids TEXT)
BEGIN
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


CREATE PROCEDURE GetTotalScans()
BEGIN
    SELECT 
        COUNT(*) AS total_scans
    FROM userscan;
END$$


CREATE PROCEDURE GetItemTypes ()
BEGIN
    SELECT 
        it.id,
        it.name AS item_type,
        it.description
    FROM ItemType it
    ORDER BY it.id;
END$$


CREATE PROCEDURE GetItemTypeById (IN p_item_type_id INT)
BEGIN
    SELECT 
        it.id,
        it.name AS item_type,
        it.description
    FROM ItemType it
    WHERE it.id = p_item_type_id;
END$$


DELIMITER ;
