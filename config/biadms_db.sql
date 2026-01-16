-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 16, 2026 at 04:47 AM
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
(1, 'Barangay 1', NULL, '2026-01-15 13:56:34', '2026-01-15 13:56:34');

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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `family_members`
--

INSERT INTO `family_members` (`id`, `resident_id`, `first_name`, `middle_name`, `last_name`, `gender`, `birth_date`, `relationship`, `civil_status`, `occupation`, `created_at`) VALUES
(1, 29, 'Rafael', 'Brett Hoffman', 'Osborne', 'female', '1981-05-01', 'grandparent', 'widowed', 'Ipsum ut qui rem do', '2026-01-16 02:26:04'),
(2, 30, 'Audrey', 'Basia Dunn', 'Talley', 'male', '1984-10-08', 'other', 'married', 'Ipsum in incididunt', '2026-01-16 03:31:32');

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
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`id`, `household_no`, `barangay_id`, `first_name`, `middle_name`, `last_name`, `gender`, `birth_date`, `civil_status`, `contact_number`, `address`, `occupation`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Voluptates nemo quae', 1, 'Holmes', 'Baker Huber', 'Hill', 'female', '1999-10-13', 'widowed', '419', 'Qui id deserunt eaqu', 'Consectetur voluptat', NULL, '2026-01-15 14:26:14', '2026-01-15 14:26:14'),
(2, 'Eaque ut alias tenet', 1, 'Jack', 'Steven Chandler', 'Rogers', 'male', '1981-04-03', 'widowed', '996', 'Aut ut nisi ex tempo', 'Ea qui ipsum volupta', NULL, '2026-01-15 14:42:22', '2026-01-15 14:42:22'),
(29, 'Sed ad soluta id co', 1, 'Jana', 'Signe Gallegos', 'Wall', 'female', '2019-05-18', 'single', '32', 'Et eaque ea est moll', 'Soluta vel adipisci', NULL, '2026-01-16 02:26:04', '2026-01-16 02:26:04'),
(30, 'Praesentium non dolo', 1, 'Meghan', 'Honorato Goodwin', 'Gillespie', 'male', '2025-10-29', 'widowed', '426', 'Blanditiis amet dui', 'Laudantium voluptat', NULL, '2026-01-16 03:31:32', '2026-01-16 03:31:32');

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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `image`, `role`, `created_at`, `updated_at`) VALUES
(1, 'administrator', 'administrator@app.com', '$2y$10$m2ITl/1fsvj2r6medvc4gOE/RKtNuyzIGt.lZPbfh8BcyEJs6SeSa', NULL, 'admin', '2026-01-12 00:03:22', '2026-01-12 00:06:14');

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
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barangays`
--
ALTER TABLE `barangays`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `family_members`
--
ALTER TABLE `family_members`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
