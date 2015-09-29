-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2015 at 06:05 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `tdebt`
--

CREATE TABLE `tdebt` (
  `debt_id` varchar(20) NOT NULL,
  `debt_date` date NOT NULL,
  `supplier_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `debt_description` text NOT NULL,
  `debt_nominal` int(11) NOT NULL,
  `debt_imageblob` blob NOT NULL,
  `debt_status` varchar(10) NOT NULL,
  `debt_deletedate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tdebtpayment`
--

CREATE TABLE `tdebtpayment` (
  `debtpayment_id` varchar(20) NOT NULL,
  `debtpayment_date` date NOT NULL,
  `debt_id` varchar(20) NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `debtpayment_description` text NOT NULL,
  `debtpayment_nominal` int(11) NOT NULL,
  `debtpayment_status` varchar(10) NOT NULL,
  `debtpayment_deletedate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tdebt`
--
ALTER TABLE `tdebt`
  ADD PRIMARY KEY (`debt_id`);

--
-- Indexes for table `tdebtpayment`
--
ALTER TABLE `tdebtpayment`
  ADD PRIMARY KEY (`debtpayment_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
