-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2015 at 08:28 PM
-- Server version: 5.6.24
-- PHP Version: 5.6.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `riceshop`
--
CREATE DATABASE IF NOT EXISTS `riceshop` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `riceshop`;

-- --------------------------------------------------------

--
-- Table structure for table `tlevel_access`
--

CREATE TABLE IF NOT EXISTS `tlevel_access` (
  `level_id` varchar(10) NOT NULL,
  `module_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `tlevel_access`
--

TRUNCATE TABLE `tlevel_access`;
--
-- Dumping data for table `tlevel_access`
--

INSERT INTO `tlevel_access` (`level_id`, `module_id`) VALUES
('LEV0000', 'MOD0000'),
('LEV0000', 'MOD0001');

-- --------------------------------------------------------

--
-- Table structure for table `tlog`
--

CREATE TABLE IF NOT EXISTS `tlog` (
  `log_id` int(100) NOT NULL,
  `log_name` varchar(30) NOT NULL,
  `log_reference` varchar(20) NOT NULL,
  `log_action` varchar(20) NOT NULL,
  `log_date` datetime NOT NULL,
  `user_id` varchar(15) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `tlog`
--

--
-- Dumping data for table `tlog`
--

-- --------------------------------------------------------

--
-- Table structure for table `tmodule`
--

CREATE TABLE IF NOT EXISTS `tmodule` (
  `module_id` varchar(10) NOT NULL,
  `module_name` varchar(30) NOT NULL,
  `module_category` varchar(30) NOT NULL,
  `module_description` varchar(500) DEFAULT NULL,
  `module_pageurl` varchar(100) NOT NULL,
  `module_issub` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `tmodule`
--

TRUNCATE TABLE `tmodule`;
--
-- Dumping data for table `tmodule`
--

INSERT INTO `tmodule` (`module_id`, `module_name`, `module_category`, `module_description`, `module_pageurl`, `module_issub`) VALUES
('MOD0000', 'Module', 'Utility', 'Untuk Mengatur Modul-modul Yang Terdapat Pada Aplikasi Ini', 'module.php', 0),
('MOD0001', 'Dashboard', 'Utama', 'Menampilkan Halaman Utama Setelah Login', 'index.php', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tuser`
--

CREATE TABLE IF NOT EXISTS `tuser` (
  `user_id` varchar(15) NOT NULL,
  `user_name` varchar(30) NOT NULL,
  `user_password` varchar(100) NOT NULL,
  `user_completename` varchar(50) NOT NULL,
  `level_id` varchar(10) NOT NULL,
  `user_status` varchar(10) NOT NULL,
  `module_id` varchar(10) DEFAULT NULL,
  `user_accessdate` datetime DEFAULT NULL,
  `user_deletedate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `tuser`
--

TRUNCATE TABLE `tuser`;
--
-- Dumping data for table `tuser`
--

INSERT INTO `tuser` (`user_id`, `user_name`, `user_password`, `user_completename`, `level_id`, `user_status`, `module_id`, `user_accessdate`, `user_deletedate`) VALUES
('USER0000', 'CREATOR', '31c177061c7de43a7a9d01269fb1b4d8caf4cea2', 'Creator', 'LEV0000', 'Online', 'MOD0001', '2015-08-12 01:25:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tuser_level`
--

CREATE TABLE IF NOT EXISTS `tuser_level` (
  `level_id` varchar(10) NOT NULL,
  `level_name` varchar(30) NOT NULL,
  `level_description` varchar(500) DEFAULT NULL,
  `level_deletedate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `tuser_level`
--

TRUNCATE TABLE `tuser_level`;
--
-- Dumping data for table `tuser_level`
--

INSERT INTO `tuser_level` (`level_id`, `level_name`, `level_description`, `level_deletedate`) VALUES
('LEV0000', 'Creator', 'Level pembuat website, dapat mengakses semua module', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tlog`
--
ALTER TABLE `tlog`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `tmodule`
--
ALTER TABLE `tmodule`
  ADD PRIMARY KEY (`module_id`);

--
-- Indexes for table `tuser`
--
ALTER TABLE `tuser`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `tuser_level`
--
ALTER TABLE `tuser_level`
  ADD PRIMARY KEY (`level_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tlog`
--
ALTER TABLE `tlog`
  MODIFY `log_id` int(100) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=33;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
