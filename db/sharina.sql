-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2017 at 10:08 AM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sharina`
--
CREATE DATABASE IF NOT EXISTS `sharina` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `sharo`;

-- --------------------------------------------------------

--
-- Table structure for table `contributions`
--

CREATE TABLE `contributions` (
  `user_ID` int(10) NOT NULL,
  `amount` decimal(40,2) NOT NULL,
  `date` text NOT NULL,
  `event` varchar(200) NOT NULL,
  `transactioncode` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lending`
--

CREATE TABLE `lending` (
  `ID` int(10) NOT NULL,
  `user_ID` int(10) NOT NULL,
  `amount_requested` decimal(10,2) NOT NULL,
  `duration` int(5) NOT NULL,
  `payable` decimal(10,2) NOT NULL,
  `installment` decimal(10,2) NOT NULL,
  `date` text NOT NULL,
  `reason` text NOT NULL,
  `paid` int(1) NOT NULL DEFAULT '0',
  `remaining` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `moneypool`
--

CREATE TABLE `moneypool` (
  `ID` int(10) NOT NULL,
  `user_ID` int(10) DEFAULT NULL,
  `totalamount` decimal(15,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Table structure for table `repays`
--

CREATE TABLE `repays` (
  `user_ID` int(10) NOT NULL,
  `loanID` int(10) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` text NOT NULL,
  `remaining` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(10) NOT NULL,
  `firstname` varchar(20) NOT NULL,
  `lastname` varchar(20) NOT NULL,
  `username` varchar(20) NOT NULL,
  `userLevel` varchar(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nationalID` int(8) NOT NULL,
  `password` varchar(355) NOT NULL,
  `phone` int(11) NOT NULL,
  `reg_date` text NOT NULL,
  `reg_fee` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `firstname`, `lastname`, `username`, `userLevel`, `email`, `nationalID`, `password`, `phone`, `reg_date`, `reg_fee`) VALUES
(1, 'Admin', 'Admin', 'Admin', 'admin', 'admin@sharina.local', 0, '$2y$11$T/SYagL6XWakdcx1AuIJXeTX..WYYsvCjaYXf0BN3FgJULd1VAcTe', 700022757, 'Wed, May 05 2017 @ 09:35:45', 200);

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `user_ID` int(10) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` text NOT NULL,
  `reason` varchar(200) NOT NULL,
  `transactioncode` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contributions`
--
ALTER TABLE `contributions`
  ADD KEY `contributions_ibfk_1` (`user_ID`);

--
-- Indexes for table `lending`
--
ALTER TABLE `lending`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_user_ID` (`user_ID`);

--
-- Indexes for table `moneypool`
--
ALTER TABLE `moneypool`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `user_ID` (`user_ID`);

--
-- Indexes for table `repays`
--
ALTER TABLE `repays`
  ADD KEY `repays_USER_ID` (`user_ID`),
  ADD KEY `FK_loam_ID` (`loanID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `nationalID` (`nationalID`),
  ADD KEY `ID` (`ID`),
  ADD KEY `userLever` (`userLevel`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD KEY `user_ID` (`user_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lending`
--
ALTER TABLE `lending`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `moneypool`
--
ALTER TABLE `moneypool`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `contributions`
--
ALTER TABLE `contributions`
  ADD CONSTRAINT `contributions_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lending`
--
ALTER TABLE `lending`
  ADD CONSTRAINT `lending_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `moneypool`
--
ALTER TABLE `moneypool`
  ADD CONSTRAINT `moneypool_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `repays`
--
ALTER TABLE `repays`
  ADD CONSTRAINT `repays_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `repays_ibfk_2` FOREIGN KEY (`loanID`) REFERENCES `lending` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD CONSTRAINT `withdrawals_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
