-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 11, 2026 at 01:20 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `biadms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `barangays`
--

CREATE TABLE `barangays` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `barangays`
--

INSERT INTO `barangays` (`id`, `name`, `logo`, `created_at`, `updated_at`) VALUES
(1, 'Barangay 1', 'uploads/barangay_logos/barangay_696b490ec129c9.05037582.jpeg', '2026-01-15 13:56:34', '2026-01-17 08:32:14'),
(2, 'Melinda Downs1234', NULL, '2026-01-17 06:45:48', '2026-01-17 06:45:48'),
(3, 'qweqwe', NULL, '2026-01-17 06:46:36', '2026-01-17 06:46:36'),
(5, 'qweqweqweqwe', NULL, '2026-01-17 06:47:30', '2026-01-17 06:47:30'),
(7, 'qweq', NULL, '2026-01-17 07:26:21', '2026-01-17 07:26:21'),
(10, 'qweqweqweqwtrqwtqweqwe', 'uploads/barangay_logos/barangay_696b49e6c639a5.57036848.jpeg', '2026-01-17 08:35:50', '2026-01-17 08:35:50'),
(11, 'asdq2313123123', 'uploads/barangay_logos/barangay_696b4a5cf413f0.11699078.jpeg', '2026-01-17 08:37:49', '2026-01-17 08:37:49');

-- --------------------------------------------------------

--
-- Table structure for table `family_members`
--

CREATE TABLE `family_members` (
  `id` int UNSIGNED NOT NULL,
  `resident_id` int UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `birth_date` date NOT NULL,
  `relationship` enum('spouse','child','parent','sibling','grandparent','relative','other') NOT NULL,
  `civil_status` enum('single','married','widowed','separated') DEFAULT 'single',
  `occupation` varchar(150) DEFAULT NULL,
  `special_status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `family_members`
--

INSERT INTO `family_members` (`id`, `resident_id`, `first_name`, `middle_name`, `last_name`, `gender`, `birth_date`, `relationship`, `civil_status`, `occupation`, `special_status`, `created_at`) VALUES
(3, 2, 'Vielka', 'Karina Gregory', 'Acosta', 'male', '1970-05-29', 'child', 'married', 'Excepturi debitis no', 'pwd', '2026-02-11 07:08:10'),
(4, 2, 'Brielle', 'Breanna Franks', 'Langley', 'male', '2011-08-19', 'spouse', 'widowed', 'Porro consequuntur q', 'pwd', '2026-02-11 07:08:10'),
(5, 4, 'Alea', 'Neil Hinton', 'Turner', 'female', '1995-03-17', 'sibling', 'married', 'Cupiditate magnam ad', 'None', '2026-02-11 07:53:13'),
(6, 4, 'Kendall', 'Regan Black', 'Wilcox', 'female', '2022-07-10', 'child', 'married', 'Saepe distinctio Bl', 'senior_citizen', '2026-02-11 07:53:13');

-- --------------------------------------------------------

--
-- Table structure for table `residents`
--

CREATE TABLE `residents` (
  `id` int UNSIGNED NOT NULL,
  `household_no` varchar(50) NOT NULL,
  `barangay_id` int UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `birth_date` date NOT NULL,
  `civil_status` enum('single','married','widowed','separated') NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` text NOT NULL,
  `occupation` varchar(150) DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `special_status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`id`, `household_no`, `barangay_id`, `first_name`, `middle_name`, `last_name`, `gender`, `birth_date`, `civil_status`, `contact_number`, `address`, `occupation`, `created_by`, `created_at`, `updated_at`, `special_status`) VALUES
(2, 'Quas officia ipsam c', 3, 'Cassady', 'Kyla Norris', 'Miller', 'female', '1994-04-17', 'widowed', '445', 'Deserunt commodo cum', 'Ut omnis dolores dol', NULL, '2026-02-11 07:08:10', '2026-02-11 07:08:10', '4ps_beneficiary'),
(4, 'Repudiandae praesent', 3, 'Bernard', 'Joshua Harrison', 'Peck', 'female', '2004-11-18', 'single', '422', 'Rem elit quis et de', 'Exercitation alias c', NULL, '2026-02-11 07:53:12', '2026-02-11 07:53:12', 'PWD');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `role` enum('admin','barangay') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'barangay',
  `barangay_id` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `image`, `role`, `barangay_id`, `created_at`, `updated_at`) VALUES
(1, 'administrator', 'administrator@app.com', '$2y$10$m2ITl/1fsvj2r6medvc4gOE/RKtNuyzIGt.lZPbfh8BcyEJs6SeSa', NULL, 'admin', NULL, '2026-01-12 00:03:22', '2026-01-12 00:06:14'),
(2, 'barangay admin', 'baarangay_admin@app.com', '$2y$10$m2ITl/1fsvj2r6medvc4gOE/RKtNuyzIGt.lZPbfh8BcyEJs6SeSa', NULL, 'barangay', 1, '2026-01-25 13:11:04', '2026-01-25 13:28:01'),
(3, 'barangay admin 2', 'barangay_admin2@app.com', '$2y$10$m2ITl/1fsvj2r6medvc4gOE/RKtNuyzIGt.lZPbfh8BcyEJs6SeSa', NULL, 'barangay', 2, '2026-01-25 14:38:04', '2026-01-25 14:38:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barangays`
--
ALTER TABLE `barangays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `family_members`
--
ALTER TABLE `family_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_family_resident` (`resident_id`);

--
-- Indexes for table `residents`
--
ALTER TABLE `residents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `household_no` (`household_no`),
  ADD KEY `fk_resident_barangay` (`barangay_id`),
  ADD KEY `fk_resident_user` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_barangay_id` (`barangay_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barangays`
--
ALTER TABLE `barangays`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `family_members`
--
ALTER TABLE `family_members`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `family_members`
--
ALTER TABLE `family_members`
  ADD CONSTRAINT `fk_family_resident` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `residents`
--
ALTER TABLE `residents`
  ADD CONSTRAINT `fk_resident_barangay` FOREIGN KEY (`barangay_id`) REFERENCES `barangays` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_resident_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_barangay` FOREIGN KEY (`barangay_id`) REFERENCES `barangays` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
