-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2025 at 08:22 PM
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
-- Database: `db_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `email` varchar(50) NOT NULL,
  `address` text DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone` varchar(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`email`, `address`, `dob`, `phone`) VALUES
('garinarossmar9@gmail.com', 'UNIVERSITY MARS,', '2004-12-29', '09183034077');

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `bookingID` int(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `facility` varchar(30) NOT NULL,
  `building` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `roomNo` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `Id` int(11) NOT NULL,
  `facility` varchar(30) NOT NULL,
  `building` varchar(50) NOT NULL,
  `roomNo` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`Id`, `facility`, `building`, `roomNo`) VALUES
(15, 'Computer Laboratory', 'Science-Technology Building', '401'),
(16, 'Computer Laboratory', 'Old Building', '410'),
(17, 'Electric Laboratory', 'Science-Technology Building', '407'),
(18, 'Electric Laboratory', 'Old Building', '408'),
(19, 'Classroom', 'Law Building', '101'),
(20, 'Classroom', 'Science-Technology Building', '202');

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `bookingID` int(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `facility` varchar(30) NOT NULL,
  `building` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `roomNo` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`bookingID`, `email`, `facility`, `building`, `date`, `start_time`, `end_time`, `roomNo`) VALUES
(46, 'romenchester03@gmail.com', 'Computer Laboratory', 'Science-Technology Building', '2025-05-01', '13:20:00', '16:20:00', '401'),
(47, 'romenchester03@gmail.com', 'Classroom', 'Law Building', '2025-04-30', '01:31:00', '06:29:00', '101'),
(48, 'romenchester03@gmail.com', 'Computer Laboratory', 'Science-Technology Building', '2025-04-30', '06:34:00', '07:34:00', '401'),
(49, 'garinarossmar9@gmail.com', 'Computer Laboratory', 'Science-Technology Building', '2025-04-30', '13:37:00', '18:37:00', '401'),
(50, 'garinarossmar9@gmail.com', 'Computer Laboratory', 'Science-Technology Building', '2025-04-30', '17:39:00', '19:39:00', '401'),
(51, 'garinarossmar9@gmail.com', 'Computer Laboratory', 'Science-Technology Building', '2025-04-30', '04:10:00', '09:10:00', '401'),
(60, 'garinarossmar9@gmail.com', 'Computer Laboratory', 'Science-Technology Building', '2025-04-30', '13:42:00', '17:42:00', '401'),
(61, 'romenchester03@gmail.com', 'Electric Laboratory', 'Science-Technology Building', '2025-05-01', '23:29:00', '18:35:00', '407');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `Id` int(10) NOT NULL,
  `UserName` varchar(50) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `role` varchar(10) NOT NULL,
  `code` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`Id`, `UserName`, `Email`, `Password`, `role`, `code`) VALUES
(41, 'ches', 'romenchester03@gmail.com', '$2y$10$HC63BV0TcwwKU5.qFpweJeLPYm7pSQh9sKU6H7zfpmtjIZX2V6DLq', 'user', 0),
(42, 'rossmar', 'garinarossmar9@gmail.com', '$2y$10$Jsc2u44m4Y1Ya7SGEwh6mel.Vaksnak.abONTlmsM.0YUbqxEZGfy', 'admin', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`bookingID`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`bookingID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `bookingID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `bookingID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
