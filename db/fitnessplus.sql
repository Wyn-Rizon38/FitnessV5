-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2025 at 01:53 AM
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
-- Database: `fitnessplus`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `user_id` int(11) NOT NULL,
  `fullname` varchar(20) DEFAULT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`user_id`, `fullname`, `username`, `password`) VALUES
(0, 'admin', 'admin', '$2y$10$TSSlz/biITlXAQAvEBQkse0IGnlhL7edkUQ4ic58wnN1OgHKieGTG');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `member_id`, `time_in`, `time_out`) VALUES
(26, 39, '2025-06-13 18:10:44', '2025-06-13 18:28:47'),
(27, 39, '2025-07-08 09:19:02', '2025-07-13 12:39:07'),
(30, 39, '2025-10-13 12:07:08', '2025-10-13 12:07:46'),
(31, 39, '2025-10-30 17:00:39', '2025-10-30 17:00:56'),
(32, 39, '2025-10-31 00:08:51', '2025-10-31 00:09:38');

-- --------------------------------------------------------

--
-- Table structure for table `class_members`
--

CREATE TABLE `class_members` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_schedule`
--

CREATE TABLE `class_schedule` (
  `id` int(11) NOT NULL,
  `coach_id` int(20) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `day_of_week` varchar(20) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `location` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coach`
--

CREATE TABLE `coach` (
  `id` int(20) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `birth_date` date NOT NULL,
  `specialization` varchar(20) DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `email` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coach`
--

INSERT INTO `coach` (`id`, `name`, `birth_date`, `specialization`, `contact_number`, `email`) VALUES
(61, 'Eloisa Maribao', '2004-04-12', 'Boxing', '065095215612', 'eloisa@gmail.com'),
(64, 'Juan', '2025-10-09', 'Cardio', '12321321', 'mjrizon38@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `user_id` int(11) NOT NULL,
  `fullname` varchar(20) DEFAULT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `dor` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `plan` varchar(100) DEFAULT NULL,
  `contact` varchar(10) DEFAULT NULL,
  `weight` float DEFAULT NULL,
  `height` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`user_id`, `fullname`, `username`, `password`, `gender`, `dor`, `expiration_date`, `plan`, `contact`, `weight`, `height`) VALUES
(39, 'Wyn Michael James', 'mjrizon8', '$2y$10$reNijk7ANCm8RUDOM7uONOLDNLhEVsHgH4ibZAqOV3J3OsRSMdpGe', 'Male', '2025-11-07', '2025-12-07', '1', '8545878545', 80, 170),
(44, 'Marlon Bernal', 'marlon', '$2y$10$2KsBWSPzk.83.dG5OblVceAZ9ytc5fkgpUEmgEH31J2PVA/Eu.W3y', 'Male', '2025-10-07', '2025-11-07', '1', '0976451', 200, 175),
(47, 'Eloisa Maribao', 'Eloisa', '$2y$10$OGs3NRCXkPuoE1h/LTfJhed0BrA.mpe8Xd3GMNEdyVqRTEXYLCYOq', 'Female', '2025-10-07', '2025-11-07', '1', '0456126026', 60, 160),
(53, 'toto', 'toto', '$2y$10$n4GvMqth2nH912cf2hCym.XqkrykhT1FvXAirXSuv9iqjNJms8zqW', 'Male', '2025-08-06', '2025-09-06', '1', '3123`', 0.05, 0.05),
(54, 'Abdulla', 'Abdullah', '$2y$10$DLVOmXtKITDqjg.X2Lz7beVxsSTsJ413YXns/F7TpFuFyrR36MbDu', 'Male', '2025-10-13', '2026-01-13', '3', '0976501620', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `monthlypayments`
--

CREATE TABLE `monthlypayments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan` varchar(20) NOT NULL,
  `amount` int(100) NOT NULL,
  `paid_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `monthlypayments`
--

INSERT INTO `monthlypayments` (`id`, `user_id`, `plan`, `amount`, `paid_date`) VALUES
(9, 39, '1', 900, '2025-06-16'),
(10, 39, '1', 900, '2025-05-16'),
(12, 39, '1', 5000, '2025-05-16'),
(13, 39, '1', 900, '2025-07-16'),
(14, 39, '1', 5000, '2025-05-16'),
(15, 39, '1', 1000, '2025-07-16'),
(18, 39, '1', 900, '2025-07-16'),
(19, 39, '7', 5000, '2025-07-16'),
(22, 44, '1', 900, '2025-10-07'),
(23, 47, '1', 900, '2025-10-07'),
(24, 39, '1', 900, '2025-11-07');

-- --------------------------------------------------------

--
-- Table structure for table `rates`
--

CREATE TABLE `rates` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `charge` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `rates`
--

INSERT INTO `rates` (`id`, `name`, `charge`) VALUES
(1, 'Boxing', '300'),
(2, 'Muay Thai', '350'),
(3, 'MMA', '350'),
(4, 'GYM Session', '80');

-- --------------------------------------------------------

--
-- Table structure for table `walkin_payments`
--

CREATE TABLE `walkin_payments` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `service` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `paid_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `walkin_payments`
--

INSERT INTO `walkin_payments` (`id`, `fullname`, `service`, `amount`, `paid_date`) VALUES
(1, 'Wyn Michael James Rizon', 'Boxing', 300, '2025-06-13'),
(2, 'Harry Denn', 'Boxing', 280, '2025-06-13'),
(3, 'Harry Denn', 'Boxing', 300, '2025-06-05'),
(4, 'Eloisa Maribao', 'Boxing', 250, '2025-07-16'),
(5, 'juan', 'Boxing', 300, '2025-09-19'),
(9, 'abdula', 'Muay Thai', 350, '2025-02-09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `class_members`
--
ALTER TABLE `class_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `class_schedule`
--
ALTER TABLE `class_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `coach_id` (`coach_id`);

--
-- Indexes for table `coach`
--
ALTER TABLE `coach`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `monthlypayments`
--
ALTER TABLE `monthlypayments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rates`
--
ALTER TABLE `rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `walkin_payments`
--
ALTER TABLE `walkin_payments`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `class_members`
--
ALTER TABLE `class_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `class_schedule`
--
ALTER TABLE `class_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `coach`
--
ALTER TABLE `coach`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `monthlypayments`
--
ALTER TABLE `monthlypayments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `rates`
--
ALTER TABLE `rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `walkin_payments`
--
ALTER TABLE `walkin_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `class_members`
--
ALTER TABLE `class_members`
  ADD CONSTRAINT `class_members_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `class_schedule` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_members_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `class_schedule`
--
ALTER TABLE `class_schedule`
  ADD CONSTRAINT `class_schedule_ibfk_1` FOREIGN KEY (`coach_id`) REFERENCES `coach` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `monthlypayments`
--
ALTER TABLE `monthlypayments`
  ADD CONSTRAINT `monthlypayments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
