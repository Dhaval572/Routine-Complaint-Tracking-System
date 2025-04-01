-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2025 at 08:08 AM
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
-- Database: `cms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `citizen_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('registered','in_progress','solved','referred') DEFAULT 'registered',
  `priority` enum('top','medium','normal','low') DEFAULT 'normal',
  `officer_id` int(11) DEFAULT NULL,
  `dept_head_id` int(11) DEFAULT NULL,
  `referred_by` int(11) DEFAULT NULL,
  `referred_at` timestamp NULL DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `response` text DEFAULT NULL,
  `review_rating` int(11) DEFAULT NULL,
  `review_feedback` text DEFAULT NULL,
  `ai_summary_complaint` text DEFAULT NULL,
  `ai_summary_response` text DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `target_role` enum('officer','dept_head') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `citizen_id`, `department_id`, `title`, `description`, `status`, `priority`, `officer_id`, `dept_head_id`, `referred_by`, `referred_at`, `remarks`, `response`, `review_rating`, `review_feedback`, `ai_summary_complaint`, `ai_summary_response`, `target_id`, `target_role`, `created_at`, `updated_at`) VALUES
(7, 8, 1, 'Complaint about Water', 'Poor water quality', 'solved', 'normal', 11, 9, 13, '2025-03-29 05:30:34', 'sorry ', 'tanker sent', NULL, NULL, NULL, NULL, 0, '', '2025-03-28 12:25:33', '2025-03-29 15:56:02'),
(8, 8, 1, 'corruption', 'officer1 asked bribe ', 'solved', 'normal', NULL, 9, NULL, NULL, 'your money will be returned', 'he is now suspended', NULL, NULL, NULL, NULL, 11, 'officer', '2025-03-29 05:50:09', '2025-03-29 06:01:28'),
(9, 8, 1, 'corruption', 'bribe', 'referred', 'normal', NULL, 9, 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 11, 'officer', '2025-03-29 15:43:42', '2025-03-29 15:57:17'),
(10, 8, 2, 'electricity not coming', 'due to flood ', 'solved', 'normal', 11, 10, 12, '2025-03-29 16:01:38', 'done', 'restored', NULL, NULL, NULL, NULL, 0, '', '2025-03-29 15:47:11', '2025-03-29 16:04:40'),
(11, 8, 2, 'corruption', 'bribe for services', 'registered', 'normal', NULL, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, 'officer', '2025-03-30 06:00:41', '2025-03-30 06:00:41'),
(12, 8, 2, 'corruption', 'bribe for services', 'registered', 'normal', NULL, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, 'officer', '2025-03-30 06:00:41', '2025-03-30 06:00:41');

-- --------------------------------------------------------

--
-- Table structure for table `complaint_activity`
--

CREATE TABLE `complaint_activity` (
  `id` int(11) NOT NULL,
  `complaint_id` int(11) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `activity_by` int(11) NOT NULL,
  `activity_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaint_activity`
--

INSERT INTO `complaint_activity` (`id`, `complaint_id`, `activity`, `activity_by`, `activity_time`) VALUES
(1, 7, 'Complaint Registered', 8, '2025-03-28 12:25:33'),
(2, 7, 'Assigned to Officer', 9, '2025-03-28 12:25:48'),
(3, 7, 'Complaint Referred to Officer', 11, '2025-03-29 05:24:06'),
(4, 7, 'Complaint Referred to Officer', 13, '2025-03-29 05:30:35'),
(5, 8, 'Complaint Registered', 8, '2025-03-29 05:50:09'),
(6, 8, 'Complaint Solved by Dept Head', 9, '2025-03-29 06:01:28'),
(7, 9, 'Complaint Registered', 8, '2025-03-29 15:43:43'),
(8, 10, 'Complaint Registered', 8, '2025-03-29 15:47:11'),
(9, 7, 'Complaint Solved by Officer', 11, '2025-03-29 15:56:02'),
(10, 9, 'Referred to Dept Head (ID: 10)', 9, '2025-03-29 15:57:17'),
(11, 10, 'Assigned to Officer', 10, '2025-03-29 15:58:21'),
(12, 10, 'Complaint Referred to Officer', 12, '2025-03-29 16:01:38'),
(13, 10, 'Complaint Solved by Officer', 11, '2025-03-29 16:04:40'),
(14, 11, 'Complaint Registered', 8, '2025-03-30 06:00:41'),
(15, 12, 'Complaint Registered', 8, '2025-03-30 06:00:41');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'water', 'dams and irrigation', '2025-03-28 07:40:45'),
(2, 'electricity', 'PGVCL', '2025-03-28 12:19:50');

-- --------------------------------------------------------

--
-- Table structure for table `signatures`
--

CREATE TABLE `signatures` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `signature_filename` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `signatures`
--

INSERT INTO `signatures` (`id`, `user_id`, `signature_filename`, `uploaded_at`) VALUES
(2, 11, '1743314281_emblem.PNG', '2025-03-30 05:58:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('citizen','officer','dept_head','admin') NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `department_id`, `created_at`) VALUES
(7, 'admin', 'admin@gmail.com', '$2y$10$8wD.AgDx5ffYmlhLGc02ReFSCN4G9IylP/lnl8wJO4nnQJLvGIKeu', 'admin', NULL, '2025-03-28 11:53:26'),
(8, 'user1', 'user1@gmail.com', '$2y$10$ch.2h98n9lKVj6/1z2CLh.pCzrYJy0BS7mdXxlm4ZfQW55ok3IrvO', 'citizen', NULL, '2025-03-28 12:11:37'),
(9, 'WaterHead', 'WaterHead@gmail.com', '$2y$10$3kGwOE0T7z9/g1MzdkGMbe3M1TGn8rizczGT9zNM2WGjnvlWCgpn2', 'dept_head', 1, '2025-03-28 12:20:31'),
(10, 'ElectricityHead', 'ElectricityHead@gmail.com', '$2y$10$XIVGleRNTinx.pXS27YkyuQ69RuDL71Umy397VrLIPsgvePBMQmeu', 'dept_head', 2, '2025-03-28 12:21:06'),
(11, 'officer1', 'officer1@gmail.com', '$2y$10$qy2zmeGpB2GgCUsNhMSl8evaWzyOOM005kh0YCwOqfr4I7BvI5FlK', 'officer', 1, '2025-03-28 12:21:48'),
(12, 'officer2', 'officer2@gmail.com', '$2y$10$3pqlOUJh.bs19nLitqQxfu9h64EJfxh3bELnZfdxKxEslUlBjVk.i', 'officer', 2, '2025-03-28 12:22:15'),
(13, 'manthan', 'officer3@gmail.com', '$2y$10$L7JUMfbvR1s1bh3FJm1OY.w6j9g9AyxixBpHTwMq7i47IRAKYf3tu', 'officer', 1, '2025-03-29 05:23:40'),
(20, 'het', 'het@gmail.com', '$2y$10$YuS9mC7shZFVyFPsOqDWOeZ/Ay1HAm1cD9SLRwZRc16Tjf3IIx52K', 'dept_head', 1, '2025-03-30 05:58:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `citizen_id` (`citizen_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `officer_id` (`officer_id`),
  ADD KEY `dept_head_id` (`dept_head_id`),
  ADD KEY `referred_by` (`referred_by`);

--
-- Indexes for table `complaint_activity`
--
ALTER TABLE `complaint_activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complaint_id` (`complaint_id`),
  ADD KEY `activity_by` (`activity_by`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `signatures`
--
ALTER TABLE `signatures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `department_id` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `complaint_activity`
--
ALTER TABLE `complaint_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `signatures`
--
ALTER TABLE `signatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`citizen_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `complaints_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `complaints_ibfk_3` FOREIGN KEY (`officer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `complaints_ibfk_4` FOREIGN KEY (`dept_head_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `complaints_ibfk_5` FOREIGN KEY (`referred_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `complaint_activity`
--
ALTER TABLE `complaint_activity`
  ADD CONSTRAINT `complaint_activity_ibfk_1` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`),
  ADD CONSTRAINT `complaint_activity_ibfk_2` FOREIGN KEY (`activity_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `signatures`
--
ALTER TABLE `signatures`
  ADD CONSTRAINT `signatures_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;
COMMIT;


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
