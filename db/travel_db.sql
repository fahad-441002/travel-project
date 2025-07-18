-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 10, 2025 at 03:59 PM
-- Server version: 9.1.0
-- PHP Version: 8.2.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `travel_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `destination_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destination_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `travel_date` date NOT NULL,
  `persons` int NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` enum('Pending','Confirmed','Cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `destination_slug` (`destination_slug`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `destination_slug`, `destination_title`, `phone`, `travel_date`, `persons`, `amount`, `total_price`, `message`, `status`, `reason`, `created_at`) VALUES
(1, 1, 'explore-paris', 'Explore Paris', '03247684739', '2025-07-09', 2, 1499.00, 2998.00, '123', 'Confirmed', NULL, '2025-07-09 20:36:56'),
(2, 1, 'explore-paris', 'Explore Paris', '123123', '2025-07-09', 1, 1499.00, 1499.00, '', 'Cancelled', '213213', '2025-07-09 20:37:53'),
(3, 1, 'explore-paris', 'Explore Paris', '123123', '2025-07-09', 1, 1499.00, 1499.00, '123123', 'Confirmed', NULL, '2025-07-09 20:40:15'),
(4, 2, 'explore-paris', 'Explore Paris', '03117912563', '2025-07-09', 1, 1499.00, 1499.00, '12323', 'Pending', NULL, '2025-07-09 21:31:00'),
(5, 1, 'discover-sydney', 'Discover Sydney', '03247684739', '2025-07-09', 5, 1399.00, 6995.00, '21213', 'Pending', NULL, '2025-07-09 21:47:16'),
(6, 1, 'discover-sydney', 'Discover Sydney', '03247684739', '2025-07-09', 5, 1399.00, 6995.00, '21213', 'Pending', NULL, '2025-07-09 21:51:42'),
(7, 1, 'discover-italy', 'Discover Italy', '03117912563', '2025-07-09', 1, 1499.00, 1499.00, '12313', 'Pending', NULL, '2025-07-09 21:54:31'),
(8, 1, 'explore-new-york-city', 'Explore New York City', '03117912563', '2025-07-09', 1, 1099.00, 1099.00, 'asds', 'Cancelled', 'asda', '2025-07-09 21:56:51'),
(9, 3, 'autralia', 'Autralia', '03247684739', '2025-07-10', 5, 2000.00, 10000.00, 'asdasd', 'Pending', NULL, '2025-07-10 15:30:53'),
(10, 4, 'discover-italy', 'Discover Italy', '03247684739', '2025-07-10', 10, 1499.00, 14990.00, '', 'Confirmed', NULL, '2025-07-10 15:34:36');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(1, 'New User', 'fahaduser@gmail.com', 'asdasd', '2025-07-09 22:13:38'),
(2, 'New User', 'fahaduser@gmail.com', 'asdasd', '2025-07-09 22:14:47');

-- --------------------------------------------------------

--
-- Table structure for table `destinations`
--

DROP TABLE IF EXISTS `destinations`;
CREATE TABLE IF NOT EXISTS `destinations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `second_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `second_description` text COLLATE utf8mb4_unicode_ci,
  `features` blob NOT NULL,
  `banner_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `background_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `slug_2` (`slug`),
  UNIQUE KEY `slug_3` (`slug`),
  UNIQUE KEY `slug_4` (`slug`),
  UNIQUE KEY `slug_5` (`slug`),
  UNIQUE KEY `slug_6` (`slug`),
  UNIQUE KEY `slug_7` (`slug`),
  UNIQUE KEY `slug_8` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `destinations`
--

INSERT INTO `destinations` (`id`, `slug`, `title`, `description`, `second_title`, `second_description`, `features`, `banner_image`, `background_image`, `duration`, `price`, `created_at`) VALUES
(8, 'explore-paris', 'Explore Paris', 'The City of Lights Awaits You', 'Paris Tour Package', 'Discover the magic of Paris with our exclusive 5-day tour package. Visit the Eiffel Tower, Louvre Museum, Seine River, and more!', 0x3c703ef09f9793efb88f204475726174696f6e3a20352044617973202f2034204e69676874733c2f703e3c703ef09f8fa820486f74656c205374617920496e636c756465643c2f703e3c703ef09f9a97204461696c79205369676874736565696e673c2f703e3c703ef09f8dbdefb88f20427265616b666173742026616d703b2044696e6e65723c2f703e3c703ef09f91a8e2808df09f92bc20546f757220477569646520496e636c756465643c2f703e, NULL, '/assets/images/1752089349_paris.jpg', 5, 1499.00, '2025-07-09 19:29:09'),
(9, 'discover-italy', 'Discover Italy', 'Experience the Beauty of Rome, Venice, and Florence', 'Italy Tour Package', 'Join us on an unforgettable 7-day journey through Italy. Explore iconic cities, taste authentic cuisine, and immerse yourself in rich history and culture.', 0x3c703ef09f9793efb88f204475726174696f6e3a20372044617973202f2036204e69676874733c2f703e3c703ef09f8fa820342d5374617220486f74656c204163636f6d6d6f646174696f6e3c2f703e3c703ef09f9a8420547261696e2054726176656c204265747765656e204369746965733c2f703e3c703ef09f8d9d20547261646974696f6e616c204974616c69616e204d65616c733c2f703e3c703ef09f8ea7204d756c74696c696e6775616c20546f75722047756964653c2f703e, NULL, '/assets/images/1752096877_italy.jpg', 7, 1499.00, '2025-07-09 21:34:37'),
(10, 'explore-new-york-city', 'Explore New York City', 'The City That Never Sleeps', 'New York City Tour Package', 'Experience the vibrant energy of NYC with our 4-day tour. See Times Square, the Statue of Liberty, Central Park, and more!', 0x3c703ef09f9793efb88f204475726174696f6e3a20342044617973202f2033204e69676874733c2f703e3c703ef09f8fa820486f74656c205374617920696e204d616e68617474616e3c2f703e3c703ef09f97bd20537461747565206f66204c696265727479204372756973653c2f703e3c703ef09f9a8d20486f702d4f6e20486f702d4f6666204369747920546f75723c2f703e3c703ef09f8e9fefb88f20456e747279205469636b65747320496e636c756465643c2f703e, NULL, '/assets/images/1752096940_new_york.jpg', 4, 1099.00, '2025-07-09 21:35:40'),
(11, 'experience-tokyo', 'Experience Tokyo', 'Modern Marvels Meet Ancient Traditions', 'Tokyo Tour Package', 'Dive into the dynamic culture of Japan’s capital. From neon-lit streets to tranquil temples, explore Tokyo like never before.', 0x3c703ef09f9793efb88f204475726174696f6e3a20352044617973202f2034204e69676874733c2f703e3c703ef09f8fa820486f74656c20696e205368696e6a756b752044697374726963743c2f703e3c703ef09f8e8c2047756964656420546f7572206f66204173616b7573612026616d703b20536869627579613c2f703e3c703ef09f8da320547261646974696f6e616c204a6170616e657365204d65616c733c2f703e3c703ef09f9b8defb88f2053686f7070696e6720546f757220696e20486172616a756b753c2f703e, NULL, '/assets/images/1752097105_tokyo.jpg', 5, 1299.00, '2025-07-09 21:36:30'),
(12, 'discover-sydney', 'Discover Sydney', 'Harbor City Adventures Await', 'Sydney Tour Package', 'Explore the iconic Sydney Opera House, Bondi Beach, and the scenic Blue Mountains in this exciting 5-day trip to Australia’s coastal gem.', 0x3c703ef09f9793efb88f204475726174696f6e3a20352044617973202f2034204e69676874733c2f703e3c703ef09f8fa820486f74656c2053746179204e656172204461726c696e6720486172626f75723c2f703e3c703ef09f9aa4205379646e657920486172626f7572204372756973653c2f703e3c703ef09f8f96efb88f20426f6e64692042656163682044617920546f75723c2f703e3c703ee29bb0efb88f20426c7565204d6f756e7461696e7320457863757273696f6e3c2f703e, NULL, '/assets/images/1752097044_sydney.jpg', 5, 1399.00, '2025-07-09 21:37:24'),
(13, 'explore-dubai', 'Explore Dubai', 'Luxury, Desert, and Skyscrapers Await', 'Dubai Tour Package', 'Discover the glamour of Dubai with this 4-day tour, featuring desert safaris, luxury shopping, and architectural wonders.', 0x3c703ef09f9793efb88f204475726174696f6e3a20342044617973202f2033204e69676874733c2f703e3c703ef09f8fa820352d5374617220486f74656c2053746179206f6e20536865696b68205a6179656420526f61643c2f703e3c703ef09f90aa20446573657274205361666172692077697468204242512044696e6e65723c2f703e3c703ef09f8f99efb88f204275726a204b68616c6966612026616d703b204475626169204d616c6c20546f75723c2f703e3c703ef09f9b8defb88f20476f6c6420536f756b2026616d703b204d6172696e61204372756973653c2f703e, NULL, '/assets/images/1752097093_dubai.jpg', 4, 1199.00, '2025-07-09 21:38:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','suspended') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `status`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$JtiEVVDwNZyzVvrZFTxuY.3CiaUMlbt3UDqwEb4IdOnSYajAifjIC', 'admin', '2025-07-06 10:02:12', 'active'),
(2, 'New User', 'fahaduser@gmail.com', '$2y$10$XGD.hrKO7H24ACaSYEERTer/UE1Bptihq2h.aQ713EkhSwf7oJLiW', 'user', '2025-07-09 21:24:17', 'active'),
(3, 'New User', 'newfahad@gmail.com', '$2y$10$xiETEJOKsidMd/djIXR2zeNXxKvUFCTXT6DbMJjl89ZbhqYxrTquO', 'user', '2025-07-10 15:24:30', 'active'),
(4, 'Hassan', 'hassanaltaf468348@gmail.com', '$2y$10$KusarT.UEmqxBqhSyqIfOOotLofjbW0lKyuICIDJU.2H0W671HPmu', 'user', '2025-07-10 15:33:55', 'active');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
