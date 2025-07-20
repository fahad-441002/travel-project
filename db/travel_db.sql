-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 20, 2025 at 12:31 PM
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
  `user_id` int DEFAULT NULL,
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
  `guest_id` int DEFAULT NULL,
  `agent_message` text COLLATE utf8mb4_unicode_ci,
  `source` enum('site','chatbot','agent') COLLATE utf8mb4_unicode_ci DEFAULT 'site',
  `channel` enum('book_now','talk_to_agent') COLLATE utf8mb4_unicode_ci DEFAULT 'book_now',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `destination_slug` (`destination_slug`),
  KEY `fk_guest` (`guest_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `destination_slug`, `destination_title`, `phone`, `travel_date`, `persons`, `amount`, `total_price`, `message`, `status`, `reason`, `created_at`, `guest_id`, `agent_message`, `source`, `channel`) VALUES
(1, 1, 'explore-paris', 'Explore Paris', '03247684739', '2025-07-09', 2, 1499.00, 2998.00, '123', 'Confirmed', NULL, '2025-07-09 20:36:56', NULL, NULL, 'site', 'book_now'),
(2, 1, 'explore-paris', 'Explore Paris', '123123', '2025-07-09', 1, 1499.00, 1499.00, '', 'Cancelled', '213213', '2025-07-09 20:37:53', NULL, NULL, 'site', 'book_now'),
(3, 1, 'explore-paris', 'Explore Paris', '123123', '2025-07-09', 1, 1499.00, 1499.00, '123123', 'Confirmed', NULL, '2025-07-09 20:40:15', NULL, NULL, 'site', 'book_now'),
(4, 2, 'explore-paris', 'Explore Paris', '03117912563', '2025-07-09', 1, 1499.00, 1499.00, '12323', 'Pending', NULL, '2025-07-09 21:31:00', NULL, NULL, 'site', 'book_now'),
(5, 1, 'discover-sydney', 'Discover Sydney', '03247684739', '2025-07-09', 5, 1399.00, 6995.00, '21213', 'Pending', NULL, '2025-07-09 21:47:16', NULL, NULL, 'site', 'book_now'),
(6, 1, 'discover-sydney', 'Discover Sydney', '03247684739', '2025-07-09', 5, 1399.00, 6995.00, '21213', 'Pending', NULL, '2025-07-09 21:51:42', NULL, NULL, 'site', 'book_now'),
(7, 1, 'discover-italy', 'Discover Italy', '03117912563', '2025-07-09', 1, 1499.00, 1499.00, '12313', 'Pending', NULL, '2025-07-09 21:54:31', NULL, NULL, 'site', 'book_now'),
(8, 1, 'explore-new-york-city', 'Explore New York City', '03117912563', '2025-07-09', 1, 1099.00, 1099.00, 'asds', 'Cancelled', 'asda', '2025-07-09 21:56:51', NULL, NULL, 'site', 'book_now'),
(9, 3, 'autralia', 'Autralia', '03247684739', '2025-07-10', 5, 2000.00, 10000.00, 'asdasd', 'Pending', NULL, '2025-07-10 15:30:53', NULL, NULL, 'site', 'book_now'),
(10, 4, 'discover-italy', 'Discover Italy', '03247684739', '2025-07-10', 10, 1499.00, 14990.00, '', 'Confirmed', NULL, '2025-07-10 15:34:36', NULL, NULL, 'site', 'book_now'),
(11, NULL, NULL, 'Actual Custom Destination', '', '2023-12-31', 3, 150.00, 450.00, NULL, 'Pending', NULL, '2025-07-20 11:21:59', 2, NULL, 'chatbot', 'book_now'),
(12, NULL, 'explore-paris', '', '', '2025-12-12', 5, 1499.00, 7495.00, NULL, 'Pending', NULL, '2025-07-20 11:23:44', 3, NULL, 'chatbot', 'book_now'),
(13, NULL, 'explore-new-york-city', '', '', '2025-12-12', 4, 1099.00, 4396.00, NULL, 'Pending', NULL, '2025-07-20 12:19:33', 9, NULL, 'chatbot', 'book_now'),
(14, NULL, 'explore-new-york-city', '', '', '2025-12-12', 4, 1099.00, 4396.00, NULL, 'Pending', NULL, '2025-07-20 12:20:27', 10, NULL, 'chatbot', 'book_now'),
(15, NULL, 'explore-new-york-city', '', '', '2025-12-12', 4, 1099.00, 4396.00, NULL, 'Pending', NULL, '2025-07-20 12:20:50', 11, NULL, 'chatbot', 'book_now'),
(16, NULL, 'explore-new-york-city', '', '', '2025-12-12', 4, 1099.00, 4396.00, NULL, 'Pending', NULL, '2025-07-20 12:22:44', 12, NULL, 'chatbot', 'book_now'),
(17, NULL, 'experience-tokyo', '', '', '2025-12-12', 5, 1299.00, 6495.00, NULL, 'Pending', NULL, '2025-07-20 12:23:54', 13, NULL, 'chatbot', 'book_now'),
(18, NULL, 'explore-new-york-city', '', '', '2025-12-12', 4, 1099.00, 4396.00, NULL, 'Pending', NULL, '2025-07-20 12:26:34', 14, NULL, 'chatbot', 'book_now');

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
-- Table structure for table `custom_bookings`
--

DROP TABLE IF EXISTS `custom_bookings`;
CREATE TABLE IF NOT EXISTS `custom_bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `guest_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_destination` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `travel_date` date DEFAULT NULL,
  `people` int DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `guest_id` (`guest_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `custom_bookings`
--

INSERT INTO `custom_bookings` (`id`, `user_id`, `guest_id`, `name`, `email`, `phone`, `custom_destination`, `travel_date`, `people`, `message`, `created_at`) VALUES
(1, NULL, 5, 'Fahad', '', '03247684739', 'full', '2025-12-22', 5, 'Ys new', '2025-07-20 11:46:28');

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
-- Table structure for table `destination_highlights`
--

DROP TABLE IF EXISTS `destination_highlights`;
CREATE TABLE IF NOT EXISTS `destination_highlights` (
  `id` int NOT NULL AUTO_INCREMENT,
  `destination_id` int NOT NULL,
  `video_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `video_description` text COLLATE utf8mb4_unicode_ci,
  `video_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `video_type` enum('youtube','mp4') COLLATE utf8mb4_unicode_ci DEFAULT 'youtube',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `destination_id` (`destination_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `destination_highlights`
--

INSERT INTO `destination_highlights` (`id`, `destination_id`, `video_title`, `video_description`, `video_url`, `video_type`, `created_at`) VALUES
(7, 8, 'Paris: City of Love', 'Walk the romantic streets of Paris, from the Eiffel Tower to quaint cafés and vibrant markets.', 'https://www.youtube.com/embed/Scxs7L0vhZ4', 'youtube', '2025-07-18 17:42:28'),
(8, 9, 'Romance & History in Italy', 'Explore Rome’s Colosseum, Venice’s canals, and Tuscany’s landscapes in this cultural Italian journey.', 'https://www.youtube.com/embed/9h2sC1DOO5I', 'youtube', '2025-07-18 17:43:04'),
(9, 10, 'NYC in Motion', 'Feel the energy of Times Square, Central Park, and Manhattan\'s skyline in this cinematic New York experience.', 'https://www.youtube.com/embed/vtpk6n2nH8A', 'youtube', '2025-07-18 17:43:32'),
(10, 11, 'Tokyo: Tradition Meets Technology', 'From cherry blossoms to neon-lit skyscrapers, Tokyo is a city where the past and future collide in harmony.', 'https://www.youtube.com/', 'youtube', '2025-07-18 17:44:09'),
(11, 12, 'Iconic Views of Sydney', 'Take in the Sydney Opera House, Harbour Bridge, and Bondi Beach in this scenic journey across Australia’s gem.', 'https://www.youtube.com/embed/nZgnZK2LrrY', 'youtube', '2025-07-18 17:44:34'),
(12, 13, 'Discover Futuristic Dubai', 'From Burj Khalifa to desert safaris, experience the luxury and innovation of Dubai in this unforgettable adventure.', 'https://www.youtube.com/embed/uwM2eJ44F5I', 'youtube', '2025-07-18 17:45:02');

-- --------------------------------------------------------

--
-- Table structure for table `guest_users`
--

DROP TABLE IF EXISTS `guest_users`;
CREATE TABLE IF NOT EXISTS `guest_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guest_users`
--

INSERT INTO `guest_users` (`id`, `name`, `email`, `phone`, `created_at`) VALUES
(2, 'Actual Name', 'actual.email@example.com', '', '2025-07-20 11:21:59'),
(3, 'Fahad', 'mfd84739@gmail.com', '', '2025-07-20 11:23:44'),
(4, 'Fahd', NULL, '03247684739', '2025-07-20 11:37:00'),
(5, 'Fahad', '', '03247684739', '2025-07-20 11:46:28'),
(6, 'Fhad', 'fahadshd11@gmail.com', NULL, '2025-07-20 12:17:36'),
(7, 'Fahad', 'fahadshd11@gmail.com', NULL, '2025-07-20 12:17:41'),
(8, 'Fahad', 'mfd84739@gmail.com', NULL, '2025-07-20 12:18:10'),
(9, 'Fahad', 'fahadshd11@gmail.com', '', '2025-07-20 12:19:33'),
(10, 'Fahad', 'fahadshd11@gmail.com', '', '2025-07-20 12:20:27'),
(11, 'Fahad', 'fahadshd11@gmail.com', '', '2025-07-20 12:20:50'),
(12, 'Fahad', 'fahadshd11@gmail.com', '', '2025-07-20 12:22:44'),
(13, 'fahad', 'mfd84739@gmail.com', '', '2025-07-20 12:23:54'),
(14, 'Fahad', 'fahadshd11@gmail.com', '', '2025-07-20 12:26:34');

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
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_guest` FOREIGN KEY (`guest_id`) REFERENCES `guest_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `custom_bookings`
--
ALTER TABLE `custom_bookings`
  ADD CONSTRAINT `custom_bookings_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `guest_users` (`id`),
  ADD CONSTRAINT `custom_bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `destination_highlights`
--
ALTER TABLE `destination_highlights`
  ADD CONSTRAINT `destination_highlights_ibfk_1` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
