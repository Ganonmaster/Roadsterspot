-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 26 Jul 2011 om 02:57
-- Serverversie: 5.5.8
-- PHP-Versie: 5.3.5

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
-- Tabelstructuur voor tabel `roadster`
--

CREATE TABLE IF NOT EXISTS `roadster` (
  `roadster_id` mediumint(255) NOT NULL AUTO_INCREMENT,
  `roadster_license_plate` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `roadster_owner_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `roadster_color` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `roadster_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `roadster_year` mediumint(255) DEFAULT NULL,
  `roadster_on_road` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`roadster_id`),
  UNIQUE KEY `roadster_license_plate` (`roadster_license_plate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Gegevens worden uitgevoerd voor tabel `roadster`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `spots`
--

CREATE TABLE IF NOT EXISTS `spots` (
  `spot_id` mediumint(255) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(255) NOT NULL,
  `roadster_id` mediumint(255) NOT NULL,
  `spot_coordinates` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `spot_location_readable` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `spot_date` int(255) NOT NULL,
  `spot_comments` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`spot_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Gegevens worden uitgevoerd voor tabel `spots`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` mediumint(255) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_email` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_password` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_admin` mediumint(255) NOT NULL,
  `user_approved` mediumint(255) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Gegevens worden uitgevoerd voor tabel `users`
--