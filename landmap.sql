-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 14, 2025 at 08:57 PM
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
-- Database: `landmap`
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
-- Table structure for table `cms`
--

CREATE TABLE `cms` (
  `id` int(11) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `text` text DEFAULT NULL,
  `animation_text` text DEFAULT NULL,
  `background_color` varchar(7) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `land_services` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `font_family` varchar(50) DEFAULT NULL,
  `font_style` varchar(50) DEFAULT NULL,
  `font_size` varchar(10) DEFAULT NULL,
  `about_page` text DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `contact_location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cms`
--

INSERT INTO `cms` (`id`, `logo`, `text`, `animation_text`, `background_color`, `img`, `land_services`, `created_at`, `font_family`, `font_style`, `font_size`, `about_page`, `contact_email`, `contact_number`, `contact_location`) VALUES
(1, '67a9ba26a4838.png', 'Find your ideal land with our', 'LandMapsss', '#006D77', '67a9b9c5871ce.jpg', 'In LandMap, we make searching for land more straightforward by means of our interactive map feature: you can filter properties by size, price, and type through a few clicks, and it will instantly reveal key details regarding land dimensions, amenities, and pricing. Directions to each property are also indicated step by step on our map, making the planning of your visits easier. Whether you\'re looking to buy land for your home, business, or investment, our user-friendly mapping tool ensures a seamless and efficient property search experience.', '2025-02-10 07:40:52', 'Arial', 'normal', '60', 'Welcome to Land Property Estate, your trusted platform for finding and selling land properties. We connect buyers with sellers and agents to facilitate seamless land transactions. Our mission is to make land property dealings transparent, efficient and accessible to everyone across the Tanza, Cavite, Philippines.', 'company@example.com', '+63 936 7876', 'Makati, Philippines');

-- --------------------------------------------------------

--
-- Table structure for table `inquire`
--

CREATE TABLE `inquire` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `status` enum('pending','accepted','declined','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inquire`
--

INSERT INTO `inquire` (`id`, `user_id`, `property_id`, `status`, `created_at`) VALUES
(13, 64, 50, 'accepted', '2025-02-14 06:17:40');

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
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role_type` enum('user','agent') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `property_id`, `user_id`, `agent_id`, `message`, `created_at`, `role_type`) VALUES
(78, 50, 64, 56, 'dasdsa', '2025-02-14 18:02:40', 'user'),
(79, 50, 64, 56, 'dsada', '2025-02-14 18:02:44', 'user');

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
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `notification` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_seen` tinyint(1) NOT NULL DEFAULT 0,
  `role` enum('user','agent') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `agent_id`, `notification`, `created_at`, `is_seen`, `role`) VALUES
(6, 64, 56, 'You have a new message from an agent.', '2025-02-14 03:27:11', 1, 'agent'),
(11, 0, 56, 'Agent has added a new property: Ivan', '2025-02-14 04:56:03', 1, 'agent'),
(12, 0, 56, 'Kate has updated properties.', '2025-02-14 14:33:18', 1, 'agent'),
(13, 64, 56, 'You have a new message from an agent.', '2025-02-14 16:39:45', 1, 'agent'),
(14, 64, 56, 'You have a new message from a user.', '2025-02-14 18:02:40', 1, 'user'),
(15, 64, 56, 'You have a new message from a user.', '2025-02-14 18:02:44', 1, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `property_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `property_name` varchar(255) NOT NULL,
  `property_location` varchar(255) NOT NULL,
  `property_type` varchar(255) NOT NULL,
  `sale_or_lease` enum('sale','lease') NOT NULL,
  `land_area` int(11) NOT NULL,
  `lease_duration` varchar(50) DEFAULT NULL,
  `monthly_rent` decimal(15,2) DEFAULT NULL,
  `land_condition` varchar(255) DEFAULT NULL,
  `sale_price` decimal(15,2) DEFAULT NULL,
  `another_info` enum('cleanTitle','DisPromo','pagibig','fsbo') DEFAULT NULL,
  `property_description` text NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `coordinates` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived_at` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `restriction_end_date` datetime DEFAULT NULL,
  `is_archive` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`property_id`, `user_id`, `property_name`, `property_location`, `property_type`, `sale_or_lease`, `land_area`, `lease_duration`, `monthly_rent`, `land_condition`, `sale_price`, `another_info`, `property_description`, `latitude`, `longitude`, `coordinates`, `created_at`, `archived_at`, `status`, `updated_at`, `restriction_end_date`, `is_archive`) VALUES
(27, 56, 'Pwede tindahan', 'Capipisa, Tanza, Cavite', 'Commercial Lot', '', 324, '', 0.00, '', 100000.00, 'fsbo', 'ito ay malapit sa gas station', 0.00000000, 0.00000000, '[[120.86377958060768,14.370371972633336],[120.79806450926912,14.350715264518954]]', '2025-01-21 03:15:08', NULL, 'active', '2025-01-30 14:10:00', NULL, 0),
(28, 56, 'bakanting lote', 'Tres Cruses, Tanza, Cavite', 'Raw Land', 'sale', 1323, '', 1233.00, '', 0.00, '', 'First come', 0.00000000, 0.00000000, '[[120.8259249203615,14.337146892812669]]', '2025-01-21 03:25:44', NULL, 'active', '2025-01-31 10:34:17', NULL, 0),
(29, 67, 'bakanting lote', 'Tres Cruses, Tanza, Cavite', 'Residential Land', '', 1323, '', 4533.00, '', 0.00, '', 'sacXZCzx', 0.00000000, 0.00000000, '[[120.96395067903984,14.640256522527991]]', '2025-01-21 03:59:23', NULL, 'active', '2025-01-30 14:10:00', NULL, 0),
(30, 68, 'heavenly', 'Bagtas, Tanza, Cavite', 'Memorial Lot', '', 3354, '', 1000000.00, '', 0.00, '', 'fdvvswew', 0.00000000, 0.00000000, '[[120.91657213900146,14.414527304674607]]', '2025-01-21 04:05:47', NULL, 'active', '2025-01-30 14:10:00', NULL, 0),
(31, 68, 'sadsd', 'Capipisa, Tanza, Cavite', 'Memorial Lot', '', 21334, '', 0.00, '', 23423245.00, 'pagibig', 'tftfgjgkkhk', 0.00000000, 0.00000000, '[[120.85587468905067,14.389084672779916]]', '2025-01-21 04:26:07', NULL, 'active', '2025-01-30 14:10:00', NULL, 0),
(32, 67, 'Test2', 'Rosario, Cavite', 'Residential Land', '', 23244, '', 12334354.00, '', 0.00, '', 'sjkojlkcjd', 0.00000000, 0.00000000, '[]', '2025-01-21 06:46:22', NULL, 'active', '2025-01-30 14:10:00', NULL, 0),
(33, 56, 'farm', 'maragondon, cavite', 'Agricultural Farm', '', 2147483647, '', 0.00, '', -9999999999999.99, 'pagibig', 'libre lang po', 0.00000000, 0.00000000, '[]', '2025-01-21 07:21:23', NULL, 'active', '2025-01-30 14:10:00', NULL, 0),
(34, 56, 'Pwede higian at tulogan', 'Bagtas, Tanza, Cavite', 'Residential Land', 'lease', 77, 'short_term', 0.00, '', 8679.00, 'cleanTitle', '8687', 0.00000000, 0.00000000, '[[120.83438956571746,14.321578902068765]]', '2025-01-21 07:24:59', NULL, 'active', '2025-02-14 19:20:02', NULL, 0),
(35, 56, 'House and Lot', 'Bagtas, Tanza, Cavite', 'Raw Land', 'sale', 12423, '', 0.00, '', 23423.00, 'DisPromo', 'dsfjhijh hihikpjkapjo pito kljkjasdj kpjk ijsdofjokjskjdfjk hjsodkhfosokjf jsdojojf ijjsoajcoknga jhosfkjsj jhojsjof', 0.00000000, 0.00000000, '[]', '2025-01-28 16:57:24', NULL, 'active', '2025-02-10 16:27:45', NULL, 0),
(36, 68, 'Sunrise place Subdivision', 'Tres Cruses, Tanza, Cavite', 'houseandlot', 'sale', 2332, '', 2323.00, '', 0.00, '', 'pwedeng kang bumili ng bahay at lupa diro saamin ng linbre wala kang babaayataranaksfoiahfih ija okajdfoihj osiadjf oieaf ', 14.28410000, 120.88740000, '[]', '2025-01-30 08:36:05', NULL, 'archived', '2025-02-10 16:27:43', NULL, 0),
(37, 68, 'Pwede higian at tulogan', 'Capipisa, Tanza, Cavite', 'House and Lot', 'lease', 3223, 'short_term', 0.00, '', 23222.00, 'DisPromo', 'qeqwqewrrw wrewrwwe wefsdf ', 14.31250000, 120.92310000, '[]', '2025-01-30 08:44:04', NULL, 'archived', '2025-02-14 19:20:02', NULL, 0),
(38, 56, 'Pwede higian at tulogan', 'Tanza, Cavite', 'Residential Farm', 'sale', 32, '', 0.00, '', 3454345.00, 'pagibig', 'fdgfdgfdgfdg rsgfsdgfdg dfgfdgdfg dfgdf fdg', 14.27130000, 120.95120000, '[]', '2025-01-30 11:02:05', NULL, 'archived', '2025-02-10 16:27:38', '2025-06-02 15:38:34', 0),
(39, 56, 'House and Lot', 'Bagtas, Tanza, Cavite', 'Agricultural Farm', 'lease', 12123, 'short_term', 23423.00, '', 0.00, '', '243232422', 14.25670000, 120.87750000, '[]', '2025-01-30 18:03:45', NULL, 'active', '2025-02-14 19:20:02', NULL, 0),
(40, 70, 'Bagtas', 'Masikip, Cavite', 'House and Lot', 'sale', 2323, '', 123231.00, '', 0.00, '', 'Malapit sa inyo ito', 14.32980000, 120.96540000, '[]', '2025-01-30 18:24:16', NULL, 'active', '2025-02-10 16:27:34', NULL, 0),
(41, 56, 'Libngang ng Bayani', 'Sahud Ulan, Naic Cavite', 'Memorial Lot', 'sale', 2322, '', 0.00, '', 2344.00, 'DisPromo', 'ewrewewrw wefsdf  sdfsd sfsdfsdf fsdsfsdf sdf sdfsdt sxcvwsdgfx cx sdvx vwsfdvzxv esa dsssv sefcv sf xcv s', 0.00000000, 0.00000000, '[[120.84243701015373,14.393147810956933],[120.84253453398003,14.391570276017973],[120.84124721946654,14.391173528949452],[120.84075569914154,14.392251356826634],[120.8418187088543,14.393479375251943],[120.84211128033508,14.393498267790036]]', '2025-01-31 10:15:57', NULL, 'active', '2025-02-14 19:56:06', NULL, 1),
(42, 56, 'Pwede higian at tulogan', 'Capipisa, Tanza, Cavite', 'House and Lot', 'sale', 132, '', 0.00, '', 2133.00, 'DisPromo', 'wqewaf dafds  sdgfsg sdf sdf sdf sg es v sfd ', 0.00000000, 0.00000000, '[]', '2025-01-31 10:37:02', NULL, 'active', '2025-01-31 10:37:02', NULL, 0),
(43, 56, 'farm', 'Naic, Cavite ', 'House and Lot', 'lease', 2123, 'short_term', 23444.00, '', 0.00, '', 'For more inquireis message me for more details about', 0.00000000, 0.00000000, '[]', '2025-01-31 10:51:47', NULL, 'active', '2025-02-14 19:20:02', NULL, 0),
(44, 56, 'Ivan', 'Kawit, Cavite', 'House and Lot', 'sale', 40, '', 0.00, '', 40000.00, 'cleanTitle', 'Dasdasd', 14.28383250, 120.86687720, NULL, '2025-02-10 14:35:58', NULL, 'active', '2025-02-10 16:09:05', NULL, 0),
(45, 56, 'Ivan', 'Magallanes, Cavite', 'House and Lot', 'lease', 40, 'short_term', 40000.00, '', 0.00, '', 'Dasdsadsa', 14.27440921, 120.88512313, NULL, '2025-02-12 10:09:44', NULL, 'active', '2025-02-14 19:20:02', NULL, 0),
(46, 56, 'Ivanss', 'GMA, Cavite', 'House and Lot', 'sale', 39, '', 0.00, '', 400.00, 'cleanTitle', 'Dasdasda', 14.28006543, 120.87036025, NULL, '2025-02-12 10:11:57', NULL, 'active', '2025-02-14 19:47:12', NULL, 1),
(47, 56, 'Ivan', 'Bacoor, Cavite', 'House and Lot', 'lease', 40, 'short_term', 4.00, '', 0.00, '', 'Dasdsa', 14.22126085, 120.84976089, NULL, '2025-02-12 10:14:47', NULL, 'active', '2025-02-12 10:14:47', NULL, 0),
(48, 56, 'Ivan', 'Bacoor, Cavite', 'House and Lot', 'sale', 23, ' ', 0.00, '0', 23.00, 'cleanTitle', 'Dasdasdasds', 14.31886938, 120.90103354, NULL, '2025-02-12 16:11:03', NULL, 'active', '2025-02-14 19:47:08', NULL, 1),
(49, 56, 'Kate', 'Cavite City, Cavite', 'House and Lot', 'sale', 23, ' ', 0.00, 'resale', 23.00, 'DisPromo', 'Dasdasd', 14.31454479, 120.90584006, NULL, '2025-02-12 16:14:22', NULL, 'active', '2025-02-14 19:44:47', NULL, 1),
(50, 56, 'Kate', 'Bacoor, Cavite', 'Commercial Lot', 'sale', 40, ' ', 40000.00, 'resale', 4.00, 'cleanTitle', '??', 14.32518977, 120.89966025, NULL, '2025-02-14 04:56:03', NULL, 'active', '2025-02-14 19:44:17', NULL, 1);

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
(44, 44, '679cab43aba66.jpg', '2025-01-31 10:51:47'),
(45, 44, '679cab43ace56.jpg', '2025-01-31 10:51:47'),
(46, 44, '679cab43ada24.jpg', '2025-01-31 10:51:47'),
(47, 44, '67aa0ece9f9dd.png', '2025-02-10 14:35:58'),
(48, 45, '67ac7368aff77.png', '2025-02-12 10:09:44'),
(49, 46, '67ac73edcc3c0.png', '2025-02-12 10:11:57'),
(50, 47, '67ac7497f0d2e.png', '2025-02-12 10:14:47'),
(51, 48, '67acc8170fc65.png', '2025-02-12 16:11:03'),
(52, 49, '67acc8de7a46c.png', '2025-02-12 16:14:22'),
(53, 50, '67aecce33993b.png', '2025-02-14 04:56:03');

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
  `is_logged_in` tinyint(1) DEFAULT 0,
  `profile` varchar(255) NOT NULL DEFAULT 'profile.jpg',
  `mobile` varchar(15) NOT NULL,
  `primary_id_type` varchar(50) NOT NULL,
  `primary_id_number` varchar(50) NOT NULL,
  `primary_id_image` varchar(255) NOT NULL,
  `secondary_id_type` varchar(50) NOT NULL,
  `secondary_id_number` varchar(50) NOT NULL,
  `secondary_id_image` varchar(255) NOT NULL,
  `admin_verify` tinyint(1) NOT NULL DEFAULT 1,
  `position` varchar(50) DEFAULT NULL,
  `prc_file` varchar(255) DEFAULT NULL,
  `dshp_file` varchar(255) DEFAULT NULL,
  `prc_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `role_type`, `fname`, `lname`, `email`, `password`, `verification_code`, `is_verified`, `created_at`, `location`, `status`, `last_activity`, `is_logged_in`, `profile`, `mobile`, `primary_id_type`, `primary_id_number`, `primary_id_image`, `secondary_id_type`, `secondary_id_number`, `secondary_id_image`, `admin_verify`, `position`, `prc_file`, `dshp_file`, `prc_id`) VALUES
(53, 'admin', 'Admin', 'Account', 'ioproperty041@gmail.com', '$2y$10$ymwY7K3tYiskY6VUISX6Iufhu7p8ID3YlrwjK3fb15Wd0SAV6egIm', '', 1, '2025-01-13 10:48:43', '', 'offline', '2025-02-13 17:04:03', 1, 'profile.jpg', '', '', '', '', '', '', '', 1, NULL, NULL, NULL, NULL),
(56, 'agent', 'John Clifford', 'Olaco', 'olacojohnclifford@gmail.com', '$2y$10$o.DEc.3gGiQCoIYJC9Sv2uG5pYLHf6jxJh1VqeMtFekN1XnH0eP/S', '', 1, '2025-01-14 09:01:29', 'General Trias, Cavite', 'offline', '2025-02-14 18:57:00', 0, 'profile.jpg', '', '', '', '', '', '', '', 1, 'Division Manager', NULL, NULL, 123),
(58, 'user', 'jonthan', 'wick', 'jonathanwick89@gmail.com', '$2y$10$dA5TIFPn5eFZy4SKByS2fOrQlONCGXquPu5zpIr7AYp4FLo5.aRBS', '', 1, '2025-01-15 09:32:08', '', 'offline', '2025-02-13 17:04:03', 0, 'profile.jpg', '', '', '', '', '', '', '', 1, NULL, NULL, NULL, NULL),
(64, 'user', 'Joshua', 'palacio', 'Joshuapalacio270@gmail.com', '$2y$10$LiYRxUJFUKx.Q16DABZDi.rjj0jKjKaQU3wJJ9tuFahLyW5nzTY8y', '', 1, '2025-01-20 13:46:46', '', 'offline', '2025-02-14 09:45:20', 0, '1739512837_R.png', '', '', '', '', '', '', '', 1, NULL, NULL, NULL, NULL),
(65, 'user', 'Joshua', 'palacio', 'Joshuacocgamer3@gmail.com', '$2y$10$P98SkuvGcZJFUvl2HkQmhuE1u0ST8RlIv5K3UP4KC8uQWM3aYosW.', '\r\n', 1, '2025-01-20 15:52:32', '', 'offline', '2025-02-13 17:04:03', 0, 'profile.jpg', '', '', '', '', '', '', '', 1, NULL, NULL, NULL, NULL),
(66, 'user', 'haha', 'haha', 'bayanihan777@gmail.com', '$2y$10$FLGyijRvRPq/1C87890uSu7tR99BqA8CejEIwCCkLHwJJXNcTYgRe', '9cfb17db9818f075b13811ad1acdd1a0', 0, '2025-01-21 03:03:46', '', 'offline', '2025-02-13 17:04:03', 0, 'profile.jpg', '', '', '', '', '', '', '', 1, NULL, NULL, NULL, NULL),
(67, 'agent', 'Iron', 'Man', 'agent@gmail.com', '$2y$10$rw5xR5oArLMq/x5pwioiHevOJQuFmZPALOjoHoofMRSv/s8l9ju4u', '\r\n', 1, '2025-01-21 03:56:34', 'Rosario, Cavite', 'offline', '2025-02-13 17:04:03', 0, 'profile.jpg', '', '', '', '', '', '', '', 1, NULL, NULL, NULL, NULL),
(68, 'agent', 'Joshua', 'Palacio', 'joshuapalacio@gmail.com', '$2y$10$ogN5PW6SSY7jgSNk/snCwejiYR4bjQDt70CVBBHJ8s5twv98s4PK.', '', 1, '2025-01-21 04:02:25', 'Trece, Cavite', 'offline', '2025-02-13 17:04:03', 1, 'profile.jpg', '', '', '', '', '', '', '', 1, NULL, NULL, NULL, NULL),
(69, 'user', 'chrisa', 'turla', 'chrisaturla10@gmail.com', '$2y$10$hYcdDz./lM0zmW018ussGuxBc5MQ.rzUNgGd4BYxIE1x8k2CYZx/.', 'b70a86365882762ab1d77228626f9ad9', 0, '2025-01-21 07:02:18', '', 'offline', '2025-02-13 17:04:03', 0, 'profile.jpg', '', '', '', '', '', '', '', 1, NULL, NULL, NULL, NULL),
(70, 'agent', 'John Cedrick', 'plato', 'john@gmail.com', '$2y$10$SwlUrjT9gYry.AD3lCHeDeyQf/.2pSMfjmcOWWi2N2vjo0ydcx4Ty', '', 1, '2025-01-30 10:52:13', 'Tanza, Cavite', 'offline', '2025-02-13 17:04:03', 0, 'profile.jpg', '', '', '', '', '', '', '', 1, NULL, NULL, NULL, NULL);

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
-- Indexes for table `cms`
--
ALTER TABLE `cms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inquire`
--
ALTER TABLE `inquire`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `land_types`
--
ALTER TABLE `land_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `cms`
--
ALTER TABLE `cms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inquire`
--
ALTER TABLE `inquire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `land_types`
--
ALTER TABLE `land_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `property_images`
--
ALTER TABLE `property_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inquire`
--
ALTER TABLE `inquire`
  ADD CONSTRAINT `inquire_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `inquire_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`);

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
