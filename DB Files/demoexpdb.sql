-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 01, 2025 at 03:42 PM
-- Server version: 5.7.40
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `demoexpdb`
--
CREATE DATABASE IF NOT EXISTS `demoexpdb` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `demoexpdb`;

-- --------------------------------------------------------

--
-- Table structure for table `biz_expense_types`
--

DROP TABLE IF EXISTS `biz_expense_types`;
CREATE TABLE IF NOT EXISTS `biz_expense_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `biz_expense_types`
--

INSERT INTO `biz_expense_types` (`id`, `type_name`) VALUES
(1, 'Fixed'),
(2, 'Variable'),
(3, 'Operating'),
(4, 'Non-Operating'),
(5, 'Capital'),
(6, 'Revenue'),
(7, 'Tax-Deductible'),
(8, 'Non-Deductible');

-- --------------------------------------------------------

--
-- Table structure for table `business_types`
--

DROP TABLE IF EXISTS `business_types`;
CREATE TABLE IF NOT EXISTS `business_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `business_types`
--

INSERT INTO `business_types` (`id`, `name`) VALUES
(1, 'Sales'),
(2, 'Marketing'),
(3, 'Operations'),
(4, 'IT'),
(5, 'HR'),
(6, 'Finance'),
(7, 'Legal'),
(8, 'Research'),
(9, 'Development'),
(10, 'Customer Service');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expenseCode` varchar(20) NOT NULL,
  `userCode` varchar(20) NOT NULL,
  `compCode` varchar(20) NOT NULL,
  `expSubChannel` varchar(50) NOT NULL,
  `expcost` decimal(10,2) NOT NULL,
  `expdescrptn` text,
  `exdate` date NOT NULL,
  `biztypeexp` varchar(50) NOT NULL,
  `addedby` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `expenseCode`, `userCode`, `compCode`, `expSubChannel`, `expcost`, `expdescrptn`, `exdate`, `biztypeexp`, `addedby`, `created_at`) VALUES
(1, '234555', '555555', '6666666', 'Administrative', '3000.00', 'Expense Description', '2025-05-01', 'Capital', 'ffffrrrr', '2025-05-01 14:47:36');

-- --------------------------------------------------------

--
-- Table structure for table `expense_sub_channels`
--

DROP TABLE IF EXISTS `expense_sub_channels`;
CREATE TABLE IF NOT EXISTS `expense_sub_channels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `expense_sub_channels`
--

INSERT INTO `expense_sub_channels` (`id`, `name`) VALUES
(1, 'Travel'),
(2, 'Meals'),
(3, 'Office Supplies'),
(4, 'Equipment'),
(5, 'Utilities'),
(6, 'Rent'),
(7, 'Insurance'),
(8, 'Marketing'),
(9, 'Training'),
(10, 'Miscellaneous');

-- --------------------------------------------------------

--
-- Table structure for table `exp_sub_channels`
--

DROP TABLE IF EXISTS `exp_sub_channels`;
CREATE TABLE IF NOT EXISTS `exp_sub_channels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exp_sub_channels`
--

INSERT INTO `exp_sub_channels` (`id`, `channel_name`) VALUES
(1, 'Marketing'),
(2, 'Sales'),
(3, 'Operations'),
(4, 'Administrative'),
(5, 'IT'),
(6, 'Travel'),
(7, 'Meals'),
(8, 'Office Supplies');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
