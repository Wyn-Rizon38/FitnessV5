-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2025 at 02:36 AM
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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
