-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 28, 2025 at 12:33 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

DROP TABLE IF EXISTS `complaints`;
CREATE TABLE IF NOT EXISTS `complaints` (
  `id` int NOT NULL AUTO_INCREMENT,
  `citizen_id` int NOT NULL,
  `department_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('registered','in_progress','solved','referred') COLLATE utf8mb4_general_ci DEFAULT 'registered',
  `priority` enum('top','medium','normal','low') COLLATE utf8mb4_general_ci DEFAULT 'normal',
  `officer_id` int DEFAULT NULL,
  `dept_head_id` int DEFAULT NULL,
  `referred_by` int DEFAULT NULL,
  `referred_at` timestamp NULL DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_general_ci,
  `response` text COLLATE utf8mb4_general_ci,
  `review_rating` int DEFAULT NULL,
  `review_feedback` text COLLATE utf8mb4_general_ci,
  `ai_summary_complaint` text COLLATE utf8mb4_general_ci,
  `ai_summary_response` text COLLATE utf8mb4_general_ci,
  `target_id` int DEFAULT NULL,
  `target_role` enum('officer','dept_head') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `target_id` int DEFAULT NULL,
  `target_role` enum('officer','dept_head') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `citizen_id` (`citizen_id`),
  KEY `department_id` (`department_id`),
  KEY `officer_id` (`officer_id`),
  KEY `dept_head_id` (`dept_head_id`),
  KEY `referred_by` (`referred_by`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `citizen_id`, `department_id`, `title`, `description`, `status`, `priority`, `officer_id`, `dept_head_id`, `referred_by`, `referred_at`, `remarks`, `response`, `review_rating`, `review_feedback`, `ai_summary_complaint`, `ai_summary_response`, `target_id`, `target_role`, `created_at`, `updated_at`) VALUES
(7, 8, 1, 'Complaint about Water', 'Poor water quality', 'in_progress', 'normal', 11, 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '', '2025-03-28 12:25:33', '2025-03-28 12:25:48');

-- --------------------------------------------------------

--
-- Table structure for table `complaint_activity`
--

DROP TABLE IF EXISTS `complaint_activity`;
CREATE TABLE IF NOT EXISTS `complaint_activity` (
  `id` int NOT NULL AUTO_INCREMENT,
  `complaint_id` int NOT NULL,
  `activity` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `activity_by` int NOT NULL,
  `activity_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `complaint_id` (`complaint_id`),
  KEY `activity_by` (`activity_by`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaint_activity`
--

INSERT INTO `complaint_activity` (`id`, `complaint_id`, `activity`, `activity_by`, `activity_time`) VALUES
(1, 7, 'Complaint Registered', 8, '2025-03-28 12:25:33'),
(2, 7, 'Assigned to Officer', 9, '2025-03-28 12:25:48');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'water', 'dams and irrigation', '2025-03-28 07:40:45'),
(2, 'electricity', 'PGVCL', '2025-03-28 12:19:50');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('citizen','officer','dept_head','admin') COLLATE utf8mb4_general_ci NOT NULL,
  `department_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `department_id`, `created_at`) VALUES
(7, 'admin', 'admin@gmail.com', '$2y$10$8wD.AgDx5ffYmlhLGc02ReFSCN4G9IylP/lnl8wJO4nnQJLvGIKeu', 'admin', NULL, '2025-03-28 11:53:26'),
(8, 'user1', 'user1@gmail.com', '$2y$10$ch.2h98n9lKVj6/1z2CLh.pCzrYJy0BS7mdXxlm4ZfQW55ok3IrvO', 'citizen', NULL, '2025-03-28 12:11:37'),
(9, 'WaterHead', 'WaterHead@gmail.com', '$2y$10$3kGwOE0T7z9/g1MzdkGMbe3M1TGn8rizczGT9zNM2WGjnvlWCgpn2', 'dept_head', 1, '2025-03-28 12:20:31'),
(10, 'ElectricityHead', 'ElectricityHead@gmail.com', '$2y$10$XIVGleRNTinx.pXS27YkyuQ69RuDL71Umy397VrLIPsgvePBMQmeu', 'dept_head', 2, '2025-03-28 12:21:06'),
(11, 'officer1', 'officer1@gmail.com', '$2y$10$qy2zmeGpB2GgCUsNhMSl8evaWzyOOM005kh0YCwOqfr4I7BvI5FlK', 'officer', 1, '2025-03-28 12:21:48'),
(12, 'officer2', 'officer2@gmail.com', '$2y$10$3pqlOUJh.bs19nLitqQxfu9h64EJfxh3bELnZfdxKxEslUlBjVk.i', 'officer', 2, '2025-03-28 12:22:15');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`citizen_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `complaints_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `complaints_ibfk_3` FOREIGN KEY (`officer_id`) ,
  ADD CONSTRAINT `complaints_ibfk_5` FOREIGN KEY (`referred_by`) REFERENCES `users` (`id`)REFER--
-- Constraints for table `complaint_activity`
--
ALTER TABLE `complaint_activity`
  ADD CONSTRAINT `complaint_activity_ibfk_1` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`),
  ADD CONSTRAINT `complaint_activity_ibfk_2` FOREIGN KEY (`activity_by`) REFERENCES `users` (`id`);ENCES `users` (`id`),
  ADD CONSTRAINT `complaints_ibfk_4` FOREIGN KEY (`dept_head_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `complaints_ibfk_5` FOREIGN KEY (`referred_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `complaint_activity`
--
ALTER TABLE `complaint_activity`
  ADD CONSTRAINT `complaint_activity_ibfk_1` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`),
  ADD CONSTRAINT `complaint_activity_ibfk_2` FOREIGN KEY (`activity_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE IF NOT EXISTS `feedback` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rating` int NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

