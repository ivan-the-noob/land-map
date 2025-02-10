-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 01, 2025 at 07:21 AM
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
-- Database: `u509581816_landmappp`
--

-- --------------------------------------------------------

--
-- Table structure for table `agents`
--

CREATE TABLE `agents` (
  `agent_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `location` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `status` enum('online','away','offline') DEFAULT 'offline',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `land_types`
--

CREATE TABLE `land_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `navigation_items`
--

CREATE TABLE `navigation_items` (
  `id` int(11) DEFAULT NULL,
  `home` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `agent` varchar(255) NOT NULL,
  `landing` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `property_id` int(11) NOT NULL,
  `property_name` varchar(255) NOT NULL,
  `property_location` varchar(255) NOT NULL,
  `property_type` varchar(255) NOT NULL,
  `sale_or_lease` enum('sale','lease') NOT NULL,
  `land_area` int(11) NOT NULL,
  `lease_duration` enum('shortTerm','longTerm') DEFAULT NULL,
  `monthly_rent` decimal(15,2) DEFAULT NULL,
  `land_condition` enum('resale','foreClose','pasalo') DEFAULT NULL,
  `sale_price` decimal(15,2) DEFAULT NULL,
  `another_info` enum('cleanTitle','DisPromo','pagibig','fsbo') DEFAULT NULL,
  `property_description` text NOT NULL,
  `coordinates` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `archived_at` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `restriction_end_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`property_id`, `property_name`, `property_location`, `property_type`, `sale_or_lease`, `land_area`, `lease_duration`, `monthly_rent`, `land_condition`, `sale_price`, `another_info`, `property_description`, `coordinates`, `created_at`, `user_id`, `archived_at`, `status`, `updated_at`, `restriction_end_date`) VALUES
(27, 'Pwede tindahan', 'Capipisa, Tanza, Cavite', 'Commercial Lot', '', 324, '', 0.00, '', 100000.00, 'fsbo', 'ito ay malapit sa gas station', '[[120.86377958060768,14.370371972633336],[120.79806450926912,14.350715264518954]]', '2025-01-21 03:15:08', 56, NULL, 'active', '2025-01-30 14:10:00', NULL),
(28, 'bakanting lote', 'Tres Cruses, Tanza, Cavite', 'Raw Land', 'sale', 1323, '', 1233.00, '', 0.00, '', 'First come', '[[120.8259249203615,14.337146892812669]]', '2025-01-21 03:25:44', 56, NULL, 'active', '2025-01-31 10:34:17', NULL),
(29, 'bakanting lote', 'Tres Cruses, Tanza, Cavite', 'Residential Land', '', 1323, '', 4533.00, '', 0.00, '', 'sacXZCzx', '[[120.96395067903984,14.640256522527991]]', '2025-01-21 03:59:23', 67, NULL, 'active', '2025-01-30 14:10:00', NULL),
(30, 'heavenly', 'Bagtas, Tanza, Cavite', 'Memorial Lot', '', 3354, '', 1000000.00, '', 0.00, '', 'fdvvswew', '[[120.91657213900146,14.414527304674607]]', '2025-01-21 04:05:47', 68, NULL, 'active', '2025-01-30 14:10:00', NULL),
(31, 'sadsd', 'Capipisa, Tanza, Cavite', 'Memorial Lot', '', 21334, '', 0.00, '', 23423245.00, 'pagibig', 'tftfgjgkkhk', '[[120.85587468905067,14.389084672779916]]', '2025-01-21 04:26:07', 68, NULL, 'active', '2025-01-30 14:10:00', NULL),
(32, 'Test2', 'Rosario, Cavite', 'Residential Land', '', 23244, '', 12334354.00, '', 0.00, '', 'sjkojlkcjd', '[]', '2025-01-21 06:46:22', 67, NULL, 'active', '2025-01-30 14:10:00', NULL),
(33, 'farm', 'maragondon, cavite', 'Agricultural Farm', '', 2147483647, '', 0.00, '', -9999999999999.99, 'pagibig', 'libre lang po', '[]', '2025-01-21 07:21:23', 56, NULL, 'active', '2025-01-30 14:10:00', NULL),
(34, 'Pwede higian at tulogan', 'Bagtas, Tanza, Cavite', 'Residential Land', '', 77, '', 0.00, '', 8679.00, 'cleanTitle', '8687', '[[120.83438956571746,14.321578902068765]]', '2025-01-21 07:24:59', 56, NULL, 'active', '2025-01-30 14:10:00', NULL),
(35, 'House and Lot', 'Bagtas, Tanza, Cavite', 'Raw Land', '', 12423, '', 0.00, '', 23423.00, 'DisPromo', 'dsfjhijh hihikpjkapjo pito kljkjasdj kpjk ijsdofjokjskjdfjk hjsodkhfosokjf jsdojojf ijjsoajcoknga jhosfkjsj jhojsjof', '[]', '2025-01-28 16:57:24', 56, NULL, 'active', '2025-01-30 14:10:00', NULL),
(36, 'Sunrise place Subdivision', 'Tres Cruses, Tanza, Cavite', 'houseandlot', '', 2332, '', 2323.00, '', 0.00, '', 'pwedeng kang bumili ng bahay at lupa diro saamin ng linbre wala kang babaayataranaksfoiahfih ija okajdfoihj osiadjf oieaf ', '[]', '2025-01-30 08:36:05', 68, NULL, 'archived', '2025-01-30 14:38:17', NULL),
(37, 'Pwede higian at tulogan', 'Capipisa, Tanza, Cavite', 'House and Lot', '', 3223, '', 0.00, '', 23222.00, 'DisPromo', 'qeqwqewrrw wrewrwwe wefsdf ', '[]', '2025-01-30 08:44:04', 68, NULL, 'archived', '2025-01-30 16:02:15', NULL),
(38, 'Pwede higian at tulogan', 'Tanza, Cavite', 'Residential Farm', '', 32, '', 0.00, '', 3454345.00, 'pagibig', 'fdgfdgfdgfdg rsgfsdgfdg dfgfdgdfg dfgdf fdg', '[]', '2025-01-30 11:02:05', 56, NULL, 'archived', '2025-01-30 14:39:52', '2025-06-02 15:38:34'),
(39, 'House and Lot', 'Bagtas, Tanza, Cavite', 'Agricultural Farm', '', 12123, '', 23423.00, '', 0.00, '', '243232422', '[]', '2025-01-30 18:03:45', 56, NULL, 'active', '2025-01-30 18:03:45', NULL),
(40, 'Bagtas', 'Masikip, Cavite', 'House and Lot', '', 2323, '', 123231.00, '', 0.00, '', 'Malapit sa inyo ito', '[]', '2025-01-30 18:24:16', 70, NULL, 'active', '2025-01-30 18:24:16', NULL),
(41, 'Libngang ng Bayani', 'Sahud Ulan, Naic Cavite', 'Memorial Lot', '', 2322, '', 0.00, '', 2344.00, 'DisPromo', 'ewrewewrw wefsdf  sdfsd sfsdfsdf fsdsfsdf sdf sdfsdt sxcvwsdgfx cx sdvx vwsfdvzxv esa dsssv sefcv sf xcv s', '[[120.84243701015373,14.393147810956933],[120.84253453398003,14.391570276017973],[120.84124721946654,14.391173528949452],[120.84075569914154,14.392251356826634],[120.8418187088543,14.393479375251943],[120.84211128033508,14.393498267790036]]', '2025-01-31 10:15:57', 56, NULL, 'active', '2025-01-31 10:15:57', NULL),
(42, 'Pwede higian at tulogan', 'Capipisa, Tanza, Cavite', 'House and Lot', 'sale', 132, '', 0.00, '', 2133.00, 'DisPromo', 'wqewaf dafds  sdgfsg sdf sdf sdf sg es v sfd ', '[]', '2025-01-31 10:37:02', 56, NULL, 'active', '2025-01-31 10:37:02', NULL),
(43, 'farm', 'Naic, Cavite ', 'House and Lot', 'lease', 2123, '', 23444.00, '', 0.00, '', 'For more inquireis message me for more details about', '[]', '2025-01-31 10:51:47', 56, NULL, 'active', '2025-01-31 10:51:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `property_images`
--

CREATE TABLE `property_images` (
  `image_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_images`
--

INSERT INTO `property_images` (`image_id`, `property_id`, `image_name`, `created_at`) VALUES
(18, 27, '678f113c2ce90.png', '2025-01-21 03:15:08'),
(19, 28, '678f13b885a67.png', '2025-01-21 03:25:44'),
(20, 29, '678f1b9ba6c8e.png', '2025-01-21 03:59:23'),
(21, 30, '678f1d1b746f8.png', '2025-01-21 04:05:47'),
(22, 31, '678f21df97c9e.png', '2025-01-21 04:26:07'),
(23, 32, '678f42bea0088.png', '2025-01-21 06:46:22'),
(24, 33, '678f4af30f7ed.png', '2025-01-21 07:21:23'),
(25, 34, '678f4bcb44b4b.png', '2025-01-21 07:24:59'),
(26, 35, '67990c746ff7e.jpg', '2025-01-28 16:57:24'),
(27, 36, '679b39f59aeec.jpg', '2025-01-30 08:36:05'),
(28, 37, '679b3bd46d9e8.jpg', '2025-01-30 08:44:04'),
(29, 38, '679b5c2db3147.png', '2025-01-30 11:02:05'),
(30, 39, '679bbf0140d50.png', '2025-01-30 18:03:45'),
(31, 40, '679bc3d09c6b9.jpg', '2025-01-30 18:24:16'),
(32, 40, '679bc3d09f7f1.jpg', '2025-01-30 18:24:16'),
(33, 41, '679ca2dd01d7e.jpg', '2025-01-31 10:15:57'),
(34, 41, '679ca2dd03013.jpg', '2025-01-31 10:15:57'),
(35, 41, '679ca2dd0442f.jpg', '2025-01-31 10:15:57'),
(36, 42, '679ca7ced8bd3.jpg', '2025-01-31 10:37:02'),
(37, 42, '679ca7cedaa16.jpg', '2025-01-31 10:37:02'),
(38, 42, '679ca7cedc142.jpg', '2025-01-31 10:37:02'),
(39, 42, '679ca7cedd4b2.jpg', '2025-01-31 10:37:02'),
(40, 42, '679ca7cedea1b.jpg', '2025-01-31 10:37:02'),
(41, 43, '679cab439ee82.png', '2025-01-31 10:51:47'),
(42, 43, '679cab43a217b.png', '2025-01-31 10:51:47'),
(43, 43, '679cab43a3740.png', '2025-01-31 10:51:47'),
(44, 43, '679cab43aba66.jpg', '2025-01-31 10:51:47'),
(45, 43, '679cab43ace56.jpg', '2025-01-31 10:51:47'),
(46, 43, '679cab43ada24.jpg', '2025-01-31 10:51:47');

-- --------------------------------------------------------

--
-- Table structure for table `property_messages`
--

CREATE TABLE `property_messages` (
  `message_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `property_views`
--

CREATE TABLE `property_views` (
  `view_id` int(100) DEFAULT NULL,
  `property_id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `property_id` int(11) DEFAULT NULL,
  `report_type` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `role_type` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verification_code` varchar(255) NOT NULL,
  `is_verified` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `location` varchar(255) NOT NULL,
  `status` enum('online','away','offline') DEFAULT 'offline',
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_logged_in` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `role_type`, `fname`, `lname`, `email`, `password`, `verification_code`, `is_verified`, `created_at`, `location`, `status`, `last_activity`, `is_logged_in`) VALUES
(53, 'admin', 'Admin', 'Account', 'ioproperty041@gmail.com', '$2y$10$ymwY7K3tYiskY6VUISX6Iufhu7p8ID3YlrwjK3fb15Wd0SAV6egIm', '', 1, '2025-01-13 10:48:43', '', 'offline', '2025-02-01 06:14:12', 1),
(56, 'agent', 'John Clifford', 'Olaco', 'olacojohnclifford@gmail.com', '$2y$10$o.DEc.3gGiQCoIYJC9Sv2uG5pYLHf6jxJh1VqeMtFekN1XnH0eP/S', '', 1, '2025-01-14 09:01:29', 'General Trias, Cavite', 'offline', '2025-01-30 17:53:29', 0),
(58, 'user', 'jonthan', 'wick', 'jonathanwick89@gmail.com', '$2y$10$dA5TIFPn5eFZy4SKByS2fOrQlONCGXquPu5zpIr7AYp4FLo5.aRBS', '', 1, '2025-01-15 09:32:08', '', 'offline', '2025-01-30 10:32:52', 0),
(64, 'user', 'Joshua', 'palacio', 'Joshuapalacio270@gmail.com', '$2y$10$r/H2Ndl4Xh2a5VQBa/stWe0cjmtXYADl.RabIL52/7wZH9cdeqRHu', '', 1, '2025-01-20 13:46:46', '', 'offline', '2025-01-31 09:25:32', 0),
(65, 'user', 'Joshua', 'palacio', 'Joshuacocgamer3@gmail.com', '$2y$10$P98SkuvGcZJFUvl2HkQmhuE1u0ST8RlIv5K3UP4KC8uQWM3aYosW.', '\r\n', 1, '2025-01-20 15:52:32', '', 'offline', '2025-01-30 10:32:52', 0),
(66, 'user', 'haha', 'haha', 'bayanihan777@gmail.com', '$2y$10$FLGyijRvRPq/1C87890uSu7tR99BqA8CejEIwCCkLHwJJXNcTYgRe', '9cfb17db9818f075b13811ad1acdd1a0', 0, '2025-01-21 03:03:46', '', 'offline', '2025-01-30 10:32:52', 0),
(67, 'agent', 'Iron', 'Man', 'agent@gmail.com', '$2y$10$rw5xR5oArLMq/x5pwioiHevOJQuFmZPALOjoHoofMRSv/s8l9ju4u', '\r\n', 1, '2025-01-21 03:56:34', 'Rosario, Cavite', 'offline', '2025-01-30 15:35:53', 0),
(68, 'agent', 'Joshua', 'Palacio', 'joshuapalacio@gmail.com', '$2y$10$ogN5PW6SSY7jgSNk/snCwejiYR4bjQDt70CVBBHJ8s5twv98s4PK.', '', 1, '2025-01-21 04:02:25', 'Trece, Cavite', 'offline', '2025-01-30 15:57:36', 1),
(69, 'user', 'chrisa', 'turla', 'chrisaturla10@gmail.com', '$2y$10$hYcdDz./lM0zmW018ussGuxBc5MQ.rzUNgGd4BYxIE1x8k2CYZx/.', 'b70a86365882762ab1d77228626f9ad9', 0, '2025-01-21 07:02:18', '', 'offline', '2025-01-30 10:32:52', 0),
(70, 'agent', 'John Cedrick', 'plato', 'john@gmail.com', '$2y$10$SwlUrjT9gYry.AD3lCHeDeyQf/.2pSMfjmcOWWi2N2vjo0ydcx4Ty', '', 1, '2025-01-30 10:52:13', 'Tanza, Cavite', 'offline', '2025-01-30 18:26:04', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_img`
--

CREATE TABLE `user_img` (
  `image_id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`agent_id`);

--
-- Indexes for table `land_types`
--
ALTER TABLE `land_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`property_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `property_images`
--
ALTER TABLE `property_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `property_messages`
--
ALTER TABLE `property_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agents`
--
ALTER TABLE `agents`
  MODIFY `agent_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `land_types`
--
ALTER TABLE `land_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `property_images`
--
ALTER TABLE `property_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `property_messages`
--
ALTER TABLE `property_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `property_images`
--
ALTER TABLE `property_images`
  ADD CONSTRAINT `property_images_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`) ON DELETE CASCADE;

--
-- Constraints for table `property_messages`
--
ALTER TABLE `property_messages`
  ADD CONSTRAINT `property_messages_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`),
  ADD CONSTRAINT `property_messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`),
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
