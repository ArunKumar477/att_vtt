-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 21, 2021 at 02:10 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `samsung_care`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_users`
--

CREATE TABLE `app_users` (
  `id` int(11) NOT NULL,
  `fos_name` varchar(30) NOT NULL,
  `user_name` varchar(10) NOT NULL,
  `user_password` varchar(20) NOT NULL,
  `otp` varchar(10) NOT NULL,
  `popup_otp` varchar(10) NOT NULL,
  `version` varchar(5) NOT NULL DEFAULT '0',
  `email` varchar(60) NOT NULL,
  `Latitude` double NOT NULL,
  `Longitude` double NOT NULL,
  `Address` varchar(200) NOT NULL,
  `user_category` int(11) NOT NULL DEFAULT 1,
  `app_admin` int(11) NOT NULL,
  `rights` int(11) NOT NULL,
  `un_fos` int(11) NOT NULL DEFAULT 0,
  `Active` int(11) NOT NULL,
  `site_user` int(11) NOT NULL DEFAULT 0,
  `site_admin` int(11) NOT NULL,
  `Login_date` date NOT NULL,
  `Login_time` time NOT NULL,
  `Edit_status` int(11) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `app_users`
--

INSERT INTO `app_users` (`id`, `fos_name`, `user_name`, `user_password`, `otp`, `popup_otp`, `version`, `email`, `Latitude`, `Longitude`, `Address`, `user_category`, `app_admin`, `rights`, `un_fos`, `Active`, `site_user`, `site_admin`, `Login_date`, `Login_time`, `Edit_status`, `created`, `modified`) VALUES
(1, 'Arun', '9003585477', '7174', '3453', '', '2.8.3', 'arunannamalai477@gmail.com', 0, 0, '', 1, 1, 2, 0, 1, 1, 1, '2021-02-21', '16:49:20', 1, '2017-05-23 12:13:57', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_log`
--

CREATE TABLE `attendance_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `long` double DEFAULT NULL,
  `status` varchar(11) NOT NULL DEFAULT 'A',
  `update_date` datetime DEFAULT NULL,
  `create_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `attendance_log`
--

INSERT INTO `attendance_log` (`id`, `user_id`, `shop_id`, `start_time`, `end_time`, `lat`, `long`, `status`, `update_date`, `create_date`) VALUES
(1, 1, 0, '2021-02-21 17:23:34', '2021-02-21 17:39:30', 13.082680199999999, 80.2707184, 'A', NULL, '2021-02-21 17:23:34');

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE `shops` (
  `id` int(11) NOT NULL,
  `User_id` varchar(10) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Partner_name` varchar(50) NOT NULL,
  `Address_one` varchar(150) DEFAULT NULL,
  `Address_two` varchar(150) DEFAULT NULL,
  `Area` varchar(30) NOT NULL,
  `City` varchar(30) DEFAULT NULL,
  `State` varchar(30) DEFAULT NULL,
  `Pincode` int(6) DEFAULT NULL,
  `Latitude` double DEFAULT NULL,
  `Longitude` double DEFAULT NULL,
  `Deleted` int(2) NOT NULL DEFAULT 0,
  `Image` varchar(250) DEFAULT NULL,
  `fos` varchar(50) NOT NULL,
  `primary_mobile` varchar(10) NOT NULL,
  `secondary_mobile` varchar(10) NOT NULL,
  `primary_email` varchar(50) NOT NULL,
  `secondary_email` varchar(50) NOT NULL,
  `shop_PMobile` varchar(10) NOT NULL,
  `shop_SMobile` varchar(10) NOT NULL,
  `Staff_name` varchar(35) NOT NULL,
  `Staff_mobile` varchar(10) NOT NULL,
  `Staff_email` varchar(40) NOT NULL,
  `credit_period` int(11) NOT NULL DEFAULT 7,
  `credit_value` int(11) NOT NULL DEFAULT 10000,
  `beat_id` int(11) NOT NULL,
  `target` int(11) NOT NULL,
  `target_b` int(11) NOT NULL,
  `addedShop_date` datetime NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `shops`
--

INSERT INTO `shops` (`id`, `User_id`, `Name`, `Partner_name`, `Address_one`, `Address_two`, `Area`, `City`, `State`, `Pincode`, `Latitude`, `Longitude`, `Deleted`, `Image`, `fos`, `primary_mobile`, `secondary_mobile`, `primary_email`, `secondary_email`, `shop_PMobile`, `shop_SMobile`, `Staff_name`, `Staff_mobile`, `Staff_email`, `credit_period`, `credit_value`, `beat_id`, `target`, `target_b`, `addedShop_date`, `created`) VALUES
(1, '1', 'VEETEE TRADING PVT LTD', 'Venkat', 'Radhakrishan street,shankaran avenue ', 'Velachery chennai_600042', 'Velachery', 'Chennai', 'TamilNadu', 600042, 12.9909443, 80.2183553, 0, NULL, '1', '9841021333', '', '', '', '', '', '', '', '', 7, 10000, 0, 0, 0, '2021-02-19 16:41:06', '2021-02-19 11:11:06'),
(3, '1', 'Arun mobils', 'Arun', 'Radhakrishan street,shankaran avenue ', 'Velachery chennai_600042', 'elavanasur', 'ulunduroet', 'TamilNadu', 607202, 11.127122499999999, 78.6568942, 0, NULL, '1', '9003585477', '', '', '', '', '', '', '', '', 7, 10000, 0, 0, 0, '2021-02-19 16:41:06', '2021-02-20 16:12:08'),
(4, '1', 'Annamalai mobils', 'Annamalai', 'Radhakrishan street,shankaran avenue ', 'Velachery chennai_600042', 'elavanasur', 'ulunduroet', 'TamilNadu', 607202, 13.082680199999999, 80.2707184, 0, NULL, '1', '9003585477', '', '', '', '', '', '', '', '', 7, 10000, 0, 0, 0, '2021-02-19 16:41:06', '2021-02-21 07:40:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_users`
--
ALTER TABLE `app_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance_log`
--
ALTER TABLE `attendance_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Name` (`Name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_users`
--
ALTER TABLE `app_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `attendance_log`
--
ALTER TABLE `attendance_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `shops`
--
ALTER TABLE `shops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
