-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 27, 2011 at 01:59 AM
-- Server version: 5.1.44
-- PHP Version: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `roadsterspot`
--

-- --------------------------------------------------------

--
-- Table structure for table `roadster`
--

CREATE TABLE `roadster` (
  `roadster_id` mediumint(255) NOT NULL AUTO_INCREMENT,
  `roadster_license_plate` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `roadster_info` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`roadster_id`),
  UNIQUE KEY `roadster_license_plate` (`roadster_license_plate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `roadster`
--


-- --------------------------------------------------------

--
-- Table structure for table `spots`
--

CREATE TABLE `spots` (
  `spot_id` mediumint(255) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(255) NOT NULL,
  `roadster_id` mediumint(255) NOT NULL,
  `spot_coordinates` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`spot_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `spots`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` mediumint(255) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_email` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_password` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_admin` mediumint(255) NOT NULL,
  `user_approved` mediumint(255) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `users`
--

