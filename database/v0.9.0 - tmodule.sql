-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2015 at 06:16 PM
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
-- Table structure for table `tmodule`
--

DROP TABLE IF EXISTS `tmodule`;
CREATE TABLE IF NOT EXISTS `tmodule` (
  `module_id` varchar(10) NOT NULL,
  `module_name` varchar(30) NOT NULL,
  `module_category` varchar(30) NOT NULL,
  `module_description` varchar(500) DEFAULT NULL,
  `module_pageurl` varchar(100) NOT NULL,
  `module_issub` tinyint(1) NOT NULL DEFAULT '0',
  `module_hascrud` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `tmodule`
--

TRUNCATE TABLE `tmodule`;
--
-- Dumping data for table `tmodule`
--

INSERT INTO `tmodule` (`module_id`, `module_name`, `module_category`, `module_description`, `module_pageurl`, `module_issub`, `module_hascrud`) VALUES
('MOD0000', 'Module', 'Utility', 'Untuk Mengatur Modul-modul Yang Terdapat Pada Aplikasi Ini', 'module.php', 0, 1),
('MOD0001', 'Dashboard', 'Utama', 'Menampilkan Halaman Utama Setelah Login', 'index.php', 0, 0),
('MOD0002', 'Profile', 'Lain-lain', 'Melihat Profile Dan Kegiatan User. Juga Untuk Mengubah Password User.', 'profile.php', 1, 0),
('MOD0003', 'User', 'Utility', 'Mengatur Data Pengguna Aplikasi Ini.', 'user.php', 0, 1),
('MOD0004', 'Level User', 'Utility', 'Mengatur Hak Akses User Terhadap Suatu Halaman.', 'user_level.php', 0, 1),
('MOD0005', 'Supplier', 'Master', 'Mengatur Data Supplier', 'supplier.php', 0, 1),
('MOD0006', 'Utang', 'Transaksi', 'Transaksi Utang Dari Supplier', 'debt.php', 0, 1),
('MOD0007', 'Transaksi Utang', 'Batal Posting', 'Untuk Membatalkan Transaksi Utang Yang Pernah Di Tambahkan Sebelumnya', 'cancel_debt.php', 0, 0),
('MOD0008', 'Pembayaran Utang', 'Transaksi', 'Transaksi Pembayaran Utang', 'debt_payment.php', 0, 1),
('MOD0009', 'Transaksi Pembayaran Utang', 'Batal Posting', 'Untuk Membatalkan Transaksi Pembayaran Utang Yang Pernah Di Tambahkan Sebelumnya', 'cancel_debtpayment.php', 0, 0),
('MOD0010', 'Print Transaksi Utang', 'Print', 'Untuk Mencetak / Print Transaksi Utang.', 'print_debt.php', 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tmodule`
--
ALTER TABLE `tmodule`
  ADD PRIMARY KEY (`module_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
