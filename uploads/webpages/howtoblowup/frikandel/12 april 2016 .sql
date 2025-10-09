-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 12 apr 2016 om 22:45
-- Serverversie: 5.5.47
-- PHP-Versie: 5.3.10-1ubuntu3.21

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dennisrenirie-nl`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblDRAuthorisatie`
--

CREATE TABLE IF NOT EXISTS `tblDRAuthorisatie` (
  `i_id` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `s_user` varchar(250) NOT NULL DEFAULT '',
  `s_password` varchar(250) NOT NULL DEFAULT '',
  `i_level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`i_id`),
  UNIQUE KEY `s_user` (`s_user`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Gegevens worden uitgevoerd voor tabel `tblDRAuthorisatie`
--

INSERT INTO `tblDRAuthorisatie` (`i_id`, `s_user`, `s_password`, `i_level`) VALUES
(1, 'dennis', '33c1fc42dbecd8479d0013b871a7c340', 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblDRAuthorisatieBlocked`
--

CREATE TABLE IF NOT EXISTS `tblDRAuthorisatieBlocked` (
  `i_id` int(3) unsigned NOT NULL DEFAULT '0',
  `s_ip` varchar(12) DEFAULT NULL,
  `s_user` varchar(255) DEFAULT NULL,
  `s_password` varchar(255) DEFAULT NULL,
  `i_datum` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`i_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblDRAuthorisatieLogs`
--

CREATE TABLE IF NOT EXISTS `tblDRAuthorisatieLogs` (
  `i_id` int(11) NOT NULL DEFAULT '0',
  `s_user` char(100) DEFAULT '0',
  `i_datum` int(11) NOT NULL DEFAULT '0',
  `s_ip` char(20) NOT NULL DEFAULT '',
  `s_host` char(255) NOT NULL DEFAULT '',
  `s_useragent` char(255) NOT NULL DEFAULT '',
  `s_page` char(255) DEFAULT NULL,
  `s_opmerkingen` char(255) DEFAULT NULL,
  PRIMARY KEY (`i_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Gegevens worden uitgevoerd voor tabel `tblDRAuthorisatieLogs`
--

INSERT INTO `tblDRAuthorisatieLogs` (`i_id`, `s_user`, `i_datum`, `s_ip`, `s_host`, `s_useragent`, `s_page`, `s_opmerkingen`) VALUES
(118, 'dennis', 1273314975, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaaldetail.php', ''),
(117, 'dennis', 1273314958, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(116, 'dennis', 1273314955, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalsave.php', ''),
(115, 'dennis', 1273314923, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaaldetail.php', ''),
(114, 'dennis', 1273314905, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(113, 'dennis', 1273314904, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalsave.php', ''),
(112, 'dennis', 1273314902, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/editpic.php', ''),
(111, 'dennis', 1273314893, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/editpic.php', ''),
(110, 'dennis', 1273314891, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaaldetail.php', ''),
(109, 'dennis', 1273314870, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(108, 'dennis', 1273314868, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalsave.php', ''),
(107, 'dennis', 1273314865, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/editpic.php', ''),
(106, 'dennis', 1273314858, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/editpic.php', ''),
(105, 'dennis', 1273314856, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaaldetail.php', ''),
(104, 'dennis', 1273314854, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(103, 'dennis', 1273314853, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalsave.php', ''),
(102, 'dennis', 1273314849, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/editpic.php', ''),
(101, 'dennis', 1273314807, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/editpic.php', ''),
(100, 'dennis', 1273314805, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaaldetail.php', ''),
(99, 'dennis', 1273314288, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(98, 'dennis', 1273314286, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalsave.php', ''),
(97, 'dennis', 1273314195, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaaldetail.php', ''),
(96, 'dennis', 1273314192, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(95, 'dennis', 1273314190, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalsave.php', ''),
(94, 'dennis', 1273314152, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaaldetail.php', ''),
(93, 'dennis', 1273314151, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(92, 'dennis', 1273312463, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/categorien_module/catindex.php', ''),
(91, 'dennis', 1273312462, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/categorien_module/catsave.php', ''),
(90, 'dennis', 1273312449, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/categorien_module/catdetail.php', ''),
(89, 'dennis', 1273312447, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/categorien_module/catindex.php', ''),
(88, 'dennis', 1273312445, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/categorien_module/catsave.php', ''),
(87, 'dennis', 1273312436, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/categorien_module/catdetail.php', ''),
(86, 'dennis', 1273312424, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/categorien_module/catindex.php', ''),
(85, 'dennis', 1273312423, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/categorien_module/catsave.php', ''),
(84, 'dennis', 1273312415, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/categorien_module/catdetail.php', ''),
(83, 'dennis', 1273312412, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/categorien_module/catindex.php', ''),
(82, 'dennis', 1273312411, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/categorien_module/catsave.php', ''),
(81, 'dennis', 1273312400, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/categorien_module/catdetail.php', ''),
(80, 'dennis', 1273312399, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/categorien_module/catindex.php', ''),
(79, 'dennis', 1273312397, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(78, 'dennis', 1273312394, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/home.php', ''),
(77, 'dennis', 1273312393, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/login.php', 'inloggen in cms'),
(76, '', 1273312389, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(75, 'dennis', 1273256466, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(74, 'dennis', 1273256464, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentsave.php', ''),
(73, 'dennis', 1273256459, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentdetail.php', ''),
(72, 'dennis', 1273256438, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(71, 'dennis', 1273256437, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentsave.php', ''),
(70, 'dennis', 1273256423, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentdetail.php', ''),
(69, 'dennis', 1273256373, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(68, 'dennis', 1273256371, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentsave.php', ''),
(67, 'dennis', 1273256350, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentdetail.php', ''),
(66, 'dennis', 1273255981, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(65, 'dennis', 1273255980, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentsave.php', ''),
(64, 'dennis', 1273255953, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentdetail.php', ''),
(63, 'dennis', 1273255775, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(62, 'dennis', 1273255774, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentsave.php', ''),
(61, 'dennis', 1273255756, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentdetail.php', ''),
(60, 'dennis', 1273255721, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(59, 'dennis', 1273255720, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentsave.php', ''),
(58, 'dennis', 1273255704, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentdetail.php', ''),
(57, 'dennis', 1273255450, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(56, 'dennis', 1273255449, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentsave.php', ''),
(55, 'dennis', 1273255445, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/editpic.php', ''),
(54, 'dennis', 1273255423, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/editpic.php', ''),
(53, 'dennis', 1273255422, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentdetail.php', ''),
(52, 'dennis', 1273255365, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(51, 'dennis', 1273255364, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentsave.php', ''),
(50, 'dennis', 1273255319, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentdetail.php', ''),
(49, 'dennis', 1273255245, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(48, 'dennis', 1273255244, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentsave.php', ''),
(47, 'dennis', 1273255230, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentdetail.php', ''),
(46, 'dennis', 1273255217, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(45, 'dennis', 1273255216, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentsave.php', ''),
(44, 'dennis', 1273255200, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentdetail.php', ''),
(43, 'dennis', 1273255196, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(42, 'dennis', 1273255190, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(41, 'dennis', 1273255150, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/2_nieuws_en_nieuwsbrief/nieuws_module/tempmailinglistindex.php', ''),
(40, 'dennis', 1273255149, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/2_nieuws_en_nieuwsbrief/nieuws_module/nieuwsbrief.php', ''),
(39, 'dennis', 1273255147, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/2_nieuws_en_nieuwsbrief/nieuws_module/tempmailinglistindex.php', ''),
(38, 'dennis', 1273255145, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/2_nieuws_en_nieuwsbrief/nieuws_module/nieuwsindex.php', ''),
(37, 'dennis', 1273255110, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/2_nieuws_en_nieuwsbrief/nieuws_module/nieuwsindex.php', ''),
(36, 'dennis', 1273254823, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(35, 'dennis', 1273254822, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentsave.php', ''),
(34, 'dennis', 1273254816, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentdetail.php', ''),
(33, 'dennis', 1273254601, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(32, 'dennis', 1273254600, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentsave.php', ''),
(31, 'dennis', 1273254503, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentdetail.php', ''),
(30, 'dennis', 1273254499, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(29, 'dennis', 1273254158, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(28, 'dennis', 1273254046, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/2_nieuws_en_nieuwsbrief/nieuws_module/mailinglistindex.php', ''),
(27, 'dennis', 1273253981, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(26, 'dennis', 1273253979, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/home.php', ''),
(25, 'dennis', 1273253979, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/login.php', 'inloggen in cms'),
(24, 'dennis', 1273253946, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(23, 'dennis', 1273253944, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(22, 'dennis', 1273253942, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(21, 'dennis', 1273253941, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/1_website/categorien_module/catindex.php', ''),
(20, 'dennis', 1273253939, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(19, 'dennis', 1273253938, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(18, 'dennis', 1273253937, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(17, 'dennis', 1273253934, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(16, 'dennis', 1273253932, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(15, 'dennis', 1273253887, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(14, 'dennis', 1273253886, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalsave.php', ''),
(13, 'dennis', 1273253822, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/1_website/materiaal_module/materiaaldetail.php', ''),
(12, 'dennis', 1273253819, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(11, 'dennis', 1273253701, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(10, 'dennis', 1273253700, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(9, 'dennis', 1273253699, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/1_website/categorien_module/catindex.php', ''),
(8, 'dennis', 1273253698, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(7, 'dennis', 1273253694, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/5_websitestatistieken/bezoeker_statistieken/statistiek-overig.php', ''),
(6, 'dennis', 1273253692, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/5_websitestatistieken/bezoeker_statistieken/statistiek-hits.php', ''),
(5, 'dennis', 1273253692, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/5_websitestatistieken/bezoeker_statistieken/statistiek-overig.php', ''),
(4, 'dennis', 1273253691, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/5_websitestatistieken/bezoeker_statistieken/statistiek-hits.php', ''),
(3, 'dennis', 1273253688, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/5_websitestatistieken/bezoeker_statistieken/statistiek-overig.php', ''),
(2, 'dennis', 1273253687, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/5_websitestatistieken/bezoeker_statistieken/statistiek-hits.php', ''),
(1, 'dennis', 1273253685, '84.31.62.75', '', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(119, 'dennis', 1273314990, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalsave.php', ''),
(120, 'dennis', 1273314991, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(121, '', 1273415464, '84.31.62.75', '', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/532.5 (KHTML, like Gecko) Chrome/4.1.249.1064 Safari/532.5', '/sitemanager/admin/login.php', 'verkeerd username/password dennis'),
(122, 'dennis', 1273415470, '84.31.62.75', '', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/532.5 (KHTML, like Gecko) Chrome/4.1.249.1064 Safari/532.5', '/sitemanager/admin/login.php', 'inloggen in cms'),
(123, 'dennis', 1273415470, '84.31.62.75', '', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/532.5 (KHTML, like Gecko) Chrome/4.1.249.1064 Safari/532.5', '/sitemanager/admin/home.php', ''),
(124, '', 1273415473, '84.31.62.75', '', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/532.5 (KHTML, like Gecko) Chrome/4.1.249.1064 Safari/532.5', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(125, 'dennis', 1273944948, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/login.php', 'inloggen in cms'),
(126, 'dennis', 1273944948, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/home.php', ''),
(127, 'dennis', 1273944950, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(128, 'dennis', 1273944954, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaaldetail.php', ''),
(129, 'dennis', 1273945256, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalsave.php', ''),
(130, 'dennis', 1273945258, '84.31.62.75', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; nl-nl) AppleWebKit/531.22.7 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7', '/sitemanager/admin/modules/1_website/materiaal_module/materiaalindex.php', ''),
(131, 'dennis', 1274295072, '84.31.62.75', '', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/532.5 (KHTML, like Gecko) Chrome/4.1.249.1064 Safari/532.5', '/sitemanager/admin/login.php', 'inloggen in cms'),
(132, 'dennis', 1274295073, '84.31.62.75', '', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/532.5 (KHTML, like Gecko) Chrome/4.1.249.1064 Safari/532.5', '/sitemanager/admin/home.php', ''),
(133, 'dennis', 1302376518, '84.27.164.216', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; nl-nl) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27', '/sitemanager/admin/login.php', 'inloggen in cms'),
(134, 'dennis', 1302376518, '84.27.164.216', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; nl-nl) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27', '/sitemanager/admin/home.php', ''),
(135, 'dennis', 1302376519, '84.27.164.216', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; nl-nl) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', ''),
(136, 'dennis', 1302376522, '84.27.164.216', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; nl-nl) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentdetail.php', ''),
(137, 'dennis', 1302376534, '84.27.164.216', '', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; nl-nl) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27', '/sitemanager/admin/modules/1_website/tekstenopwebsite_module/sitecontentindex.php', '');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblDRCategories`
--

CREATE TABLE IF NOT EXISTS `tblDRCategories` (
  `i_id` int(11) NOT NULL DEFAULT '0',
  `s_name` varchar(255) NOT NULL DEFAULT '',
  `i_parentnodeid` int(11) NOT NULL DEFAULT '0',
  `b_showonwebsite` int(11) NOT NULL DEFAULT '0',
  `i_order` int(11) NOT NULL DEFAULT '0',
  `i_level` int(11) NOT NULL DEFAULT '0' COMMENT 'for reading nodes top-down the list',
  `s_description` longtext,
  `s_photobig` varchar(255) DEFAULT NULL,
  `s_photosmall` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`i_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Gegevens worden uitgevoerd voor tabel `tblDRCategories`
--

INSERT INTO `tblDRCategories` (`i_id`, `s_name`, `i_parentnodeid`, `b_showonwebsite`, `i_order`, `i_level`, `s_description`, `s_photobig`, `s_photosmall`) VALUES
(1, 'Algemeen', 0, 1, 1, 0, '', '', ''),
(9, 'CMS v3', 7, 1, 9, 1, '', '', ''),
(8, 'JTrainer', 7, 1, 8, 1, '', '', ''),
(6, 'Overig', 0, 1, 6, 0, '', '', ''),
(7, 'Projecten', 0, 1, 7, 0, '', '', '');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblDRMailinglist`
--

CREATE TABLE IF NOT EXISTS `tblDRMailinglist` (
  `i_id` int(3) unsigned NOT NULL DEFAULT '0',
  `i_datum` int(3) unsigned DEFAULT '0',
  `s_emailadres` char(100) NOT NULL DEFAULT '0',
  PRIMARY KEY (`i_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblDRMailinglisttemp`
--

CREATE TABLE IF NOT EXISTS `tblDRMailinglisttemp` (
  `i_id` int(3) unsigned NOT NULL DEFAULT '0',
  `i_datum` int(3) unsigned DEFAULT '0',
  `s_emailadres` char(100) NOT NULL DEFAULT '0',
  PRIMARY KEY (`i_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblDRMaterial`
--

CREATE TABLE IF NOT EXISTS `tblDRMaterial` (
  `i_id` int(3) unsigned NOT NULL DEFAULT '0',
  `s_tekst` longtext CHARACTER SET latin1 NOT NULL,
  `i_datum` int(11) NOT NULL DEFAULT '0',
  `s_onderwerp` varchar(250) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `b_opwebsite` tinyint(10) unsigned NOT NULL DEFAULT '0',
  `i_volgorde` int(10) unsigned NOT NULL DEFAULT '0',
  `s_plaatjeurl` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `s_plaatjeurlklein` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `i_parentnodeid` int(11) NOT NULL,
  `i_aantalkeerbekeken` int(11) NOT NULL,
  `b_nieuw` tinyint(4) NOT NULL,
  `s_achtergrond` longtext COLLATE utf8_bin,
  PRIMARY KEY (`i_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden uitgevoerd voor tabel `tblDRMaterial`
--

INSERT INTO `tblDRMaterial` (`i_id`, `s_tekst`, `i_datum`, `s_onderwerp`, `b_opwebsite`, `i_volgorde`, `s_plaatjeurl`, `s_plaatjeurlklein`, `i_parentnodeid`, `i_aantalkeerbekeken`, `b_nieuw`, `s_achtergrond`) VALUES
(1, 'Bij deze een eerste bericht om mijn weblog te testen op deze site.<br>\r\nIk heb tevens een twitter account aangemaakt, het is dus ook mogelijk om me te volgen via twitter.', 1273251600, 'Eerste bericht', 1, 1, 'materiaal_groot_1(1).jpg', 'materiaal_klein_1(1).jpg', 1, 1, 1, ''),
(2, '<p>De oorsprong van CMS3 laat zich vinden in 2000, voor de plaatselijke computerwinkel ontwikkelde ik een website met een beheersysteem in PHP. Dit project is opgepakt door het bedrijf waar ik stage liep. De website is vanaf de grond af aan herschreven en was een volwaardig Content Management Systeem in ASP.<br>\r\n  Gezien PHP mij beter lag, heb ik voor mij prive een gebruiksvriendelijke variant hiervan ontwikkeld, CMS1 was geboren.<br>\r\n  Tijdens mijn afsturen in 2005 heb ik een applicatie framework in PHP met CRM toepassingen ontwikkeld, welke technologisch gezien zeer pienter in elkaar zat en een stap voorwaards was ten opzichte van CMS1<br>\r\n  De gehanteerde modulaire structuur was dermate goed doordacht dat ik dit idee heb toegepast op CMS1: dat werd CMS2.</p>\r\n<p>Vanaf dat moment heb ik besloten om een volledig nieuw CMS te ontwikkelen met de ideeen van mijn afstudeeropdracht, maar met de vernieuwde inzichten die de afstudeeropdracht had opgeleverd.<br>\r\n  Het bleef bij echter bij ideeen.<br>\r\n  Deze ideeen nemen in 2007 concrete vormen aan wanneer ik voor een detacheeropdracht met een zeer onlogisch CMS systeem te maken krijg.<br>\r\n  De toenemende frustratie zorgde voor eerste schetsen en een ontwerp op papier.</p>\r\n<p>Na het opleveren van websites, toevoegen van functionaliteit aan CMS2 en het ontdekken van mogelijke zwakke beveiligingsplekken (en daardoor het toevoegen van eisen en het uitwerken van ideeen) werd de eerste code in 2008 geschreven.<br>\r\n  Voortschrijdend inzicht heeft ertoe geleid dat CMS3 een applicatieframework moest worden welke ook ingezet kon worden als CMS.<br>\r\n  In 2010 is de standaard applicatie functiebibliotheek in grote lijnen voltooid en is de GUI ontworpen.</p>\r\n<p>Zoals u ziet is aan CMS3 een heel denkproces vooraf gegaan en is door ervaring gekristaliseerd tot een geavanceerd en makkelijk toepasbaar systeem.<br>\r\n  De grootte van het project heeft ertoe geleid dat het nog niet af is. Ik hoop echter wel ooit het punt te bereiken dat het toepasbaar wordt.</p>\r\n<p>Gezien het een hobbyproject is wordt het geproprammeerd in de weinig spaarzame uurtjes die mijn agenda biedt.<br>\r\n</p>', 1273312800, 'CMS3 - de oorsprong', 1, 2, 'materiaal_groot_2(1).jpg', 'materiaal_klein_2(1).jpg', 9, 2, 1, '');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblDRMaterialReactions`
--

CREATE TABLE IF NOT EXISTS `tblDRMaterialReactions` (
  `i_id` int(10) unsigned NOT NULL DEFAULT '0',
  `s_name` varchar(50) DEFAULT NULL,
  `s_website` varchar(100) DEFAULT NULL,
  `s_email` varchar(100) DEFAULT NULL,
  `s_ip` varchar(20) DEFAULT NULL,
  `s_reaction` longtext,
  `i_materialid` int(11) NOT NULL DEFAULT '0',
  `i_timestamp` int(11) NOT NULL,
  `b_updatereactionsviaemail` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`i_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblDRMaterialStats`
--

CREATE TABLE IF NOT EXISTS `tblDRMaterialStats` (
  `i_materialid` int(11) NOT NULL DEFAULT '0',
  `s_ip` varchar(25) NOT NULL DEFAULT '',
  `i_timestamp` int(11) DEFAULT NULL,
  PRIMARY KEY (`s_ip`,`i_materialid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `tblDRMaterialStats`
--

INSERT INTO `tblDRMaterialStats` (`i_materialid`, `s_ip`, `i_timestamp`) VALUES
(2, '84.31.62.75', 1273353508),
(2, '66.249.65.178', 1273558835),
(2, '213.10.82.34', 1276355253),
(1, '66.249.65.111', 1278903921),
(1, '66.249.65.110', 1279318446),
(1, '66.249.65.185', 1279565837);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblDRMaterialTags`
--

CREATE TABLE IF NOT EXISTS `tblDRMaterialTags` (
  `s_tag` varchar(255) NOT NULL DEFAULT '',
  `i_materialid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`i_materialid`,`s_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Gegevens worden uitgevoerd voor tabel `tblDRMaterialTags`
--

INSERT INTO `tblDRMaterialTags` (`s_tag`, `i_materialid`) VALUES
('', 1),
('', 2),
('applicatie\r', 2),
('ASP\r', 2),
('cms\r', 2),
('framework\r', 2),
('hbo\r', 2),
('informatica\r', 2),
('php\r', 2);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblDRNieuws`
--

CREATE TABLE IF NOT EXISTS `tblDRNieuws` (
  `i_id` int(3) unsigned NOT NULL DEFAULT '0',
  `s_tekst` longtext CHARACTER SET latin1 NOT NULL,
  `i_datum` int(11) NOT NULL DEFAULT '0',
  `s_onderwerp` varchar(250) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `b_opwebsite` tinyint(10) unsigned NOT NULL DEFAULT '0',
  `i_volgorde` int(10) unsigned NOT NULL DEFAULT '0',
  `s_plaatjeurl` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `s_plaatjeurlklein` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`i_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblDRNieuwsbrieven`
--

CREATE TABLE IF NOT EXISTS `tblDRNieuwsbrieven` (
  `i_id` int(11) NOT NULL DEFAULT '0',
  `i_datum` int(11) NOT NULL DEFAULT '0',
  `s_nieuwsbrief` longtext CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`i_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblDRSitecontent`
--

CREATE TABLE IF NOT EXISTS `tblDRSitecontent` (
  `i_id` int(3) unsigned NOT NULL DEFAULT '0',
  `s_naam` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `s_omschrijving` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `s_titel` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `s_tekst` longtext CHARACTER SET latin1 NOT NULL,
  `s_plaatjeurl` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  `s_plaatjeurlklein` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`i_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden uitgevoerd voor tabel `tblDRSitecontent`
--

INSERT INTO `tblDRSitecontent` (`i_id`, `s_naam`, `s_omschrijving`, `s_titel`, `s_tekst`, `s_plaatjeurl`, `s_plaatjeurlklein`) VALUES
(1, 'contact', '', 'Contact formulier', 'Middels het onderstaande contactformulier kunt u contact met mij opnemen via email', 'content_groot_1(1).jpg', 'content_klein_1(1).jpg'),
(4, 'over', '', 'Over mij', 'Volgt binnenkort ...', '', ''),
(6, 'disclaimer', '', 'Disclaimer', 'Deze website is met grote zorgvuldigheid samengesteld.<BR>\r\nDe teksten op deze website zijn meningen van een persoon en niet getoetst met de werkelijkheid.<BR>\r\nEr wordt zeer veel waarde gehecht aan auteursrechten. Te allen tijde zullen wij deze proberen na te leven.<BR>\r\nIndien er onverhoopt toch inbeuk gemaakt wordt op rechten van derden, maak dit kenbaar via het contactformulier op deze website.\r\n', '', ''),
(7, 'materiaal', '', 'Weblog', 'Hieronder kun je mijn logs gecategoriseerd terugvinden :', '', ''),
(8, 'abonnement', '', 'Abonnement op de crea-bea-blog-club', 'Met onregelmatige regelmaat (inspiratie laat zich niet sturen) verschijnt er nieuw materiaal op deze website.<BR>\r\nDoor een abonnement te nemen word je reklameloos op de hoogte gehouden en ontvang je de nieuwste schrijfsels, verhalen, uitspattingen etc. automatisch in je digitale brievenbus.<BR>\r\nLaat daarvoor hieronder je emailadres achter!<br>\r\n', '', ''),
(5, 'Nieuws', '', 'Nieuws', 'Nieuwsgierig ? Dan moet je hier zijn!', '', ''),
(17, 'rssfeeds', '', 'RSS feeds', 'RSS staat voor Really Simple Synication.<br>\r\nHet is een stroom informatie die op een gestructureerde wijze wordt aangeboden.<br>\r\nRSS feeds lezen kunt u doen met een RSS reader.<br>\r\nHet is ideaal voor nieuwsberichten of weblogs, gezien u (afhankelijk van het programma dat u gebruikt) een noficatie kunt krijgen wanneer nieuwe informatie beschikbaar is.<br>\r\n<br>\r\nInternetprogramma`s (browsers) zoals Internet Explorer, Firefox, Safari en Opera ondersteunen het lezen van RSS feeds. De notificatiefunctie van bovenstaande programma`s is echter zwaar sneu (behalve opera), vandaar dat ik een overzicht geef van andere RSS readers:<br>\r\n-<a href="http://www.mozilla-europe.org/nl/products/thunderbird/">Thunderbird</a> (AANRADER!!!)<br>\r\n-<a href="http://www.feedreader.com/">Feedreader</a><br>\r\n-<a href="http://www.newsgator.com/Individuals/FeedDemon/">Feed demon</a><br>\r\n-<a href="http://www.google.com/reader/">Google reader</a> (online, dus geen software installeren)<br>\r\n<h2>RSS Feeds op deze site</h2>\r\nDeze website kent een tweetal feeds, te weten:<br>\r\n<a href="http://www.dennisschraven.nl/materiaal-rss.xml">RSS feed weblog</a><br>', '', ''),
(10, 'aanmelden_nieuwsbrief', '', 'Aan/afmelden abonnement', 'Vul hieronder je emailadres in', '', ''),
(9, 'nieuwopsite', 'nieuw op de website (rechter kolom in site)', 'Nieuw op de site', 'Het laatst toegevoegde materiaal:', '', ''),
(11, 'materiaaldetail_dezealgelezen', '', 'Wellicht ook interessant', 'Misschien heeft u deze nog niet gelezen:', '', ''),
(12, 'materiaaldetail_achtergrond', '', 'Achtergrond info', '', '', ''),
(14, 'materiaaldetail_schrijftikfouten', '', 'Doorgeven tekstfouten', 'Graag tik, schrijf, taal en spel-fouten doorgeven via het onderstaande formulier, alvast bedankt.', '', ''),
(15, 'materiaaldetail_reageer', '', 'Reacties', 'U kunt reageren door het onderstaande formulier in te vullen.', '', ''),
(16, 'sitemap', '', 'Sitemap', 'Inhoudsopgave van deze website', '', ''),
(18, 'nieuwsbrief_header', '', 'berichtje van Dennis', 'Je ontvangt deze een email in verband met het abonnement dat je hebt op de website www.dennisschraven.nl', '', ''),
(19, 'nieuwsbrief_footer', '', '', '<br>\r\n<br>\r\n<br>\r\n<br>\r\n<b>\r\nTot de volgende mail.<br>\r\n<br>\r\nWil je je afmelden voor het abonnement (liever niet natuurlijk) kun je dit doen op de website : <a href="http://www.dennisschraven.nl/abonnement.html">www.dennisschraven.nl/abonnement.html</a>. <br>\r\nVul op deze pagina je emailadres in, selecteer AFMELDEN en klik op OPSLAAN. Je ontvangt dan in het vervolg geen emails meer.\r\n</b>', '', ''),
(20, 'home', '', 'Home', 'Welkom op mijn website.<br>\r\nVolg Dennis ook <a href="http://www.twitter.com/dennisrenirie" target="_blank">via Twitter</a>!', '', ''),
(23, 'twitter', '', 'Dennis Twittert', 'Je kunt mijn vorderingen en nieuws ook volgen via Twitter, hieronder de link naar mijn persoonlijke Twitter pagina\r\n<br>\r\n<a href="http://twitter.com/dennisrenirie" target="_blank">Link naar Twitter pagina van Dennis</a><br>\r\n<br>\r\n<br>', 'content_groot_23(1).jpg', 'content_klein_23(1).jpg'),
(21, 'speellijst', '', 'Speellijst', '<style type="text/css">\r\n<!--\r\n.style1 {\r\n	color: #CCCCCC\r\n}\r\n-->\r\n</style>\r\nHieronder zie je een lijst van optredens:<br>\r\n<table>\r\n<tbody><tr>\r\n    	<td>\r\n          <strong>datum</strong> </td>\r\n        <td>\r\n          <strong>optreden</strong> </td>\r\n        <td>\r\n          <strong>lokatie</strong> </td>\r\n	    <td><strong>hoedanigheid</strong></td>\r\n	</tr>\r\n	<tr>\r\n	  <td valign="top" bgcolor="#CCCCCC">18-12-2009<br>\r\n	    20:30u</td>\r\n	  <td valign="top" bgcolor="#CCCCCC">try out nieuw materiaal</td>\r\n	  <td valign="top" bgcolor="#CCCCCC">Huis van Puck<br> \r\n	    Parkstraat 34a, Arnhem</td>\r\n	  <td valign="top" bgcolor="#CCCCCC">theatermaker</td>\r\n  </tr>\r\n	<tr>\r\n      <td valign="top" bgcolor="#FFFFFF">19-12-2009<br>      </td>\r\n	  <td valign="top" bgcolor="#FFFFFF"><p>dans demonstratie<br>\r\n	    (besloten)<br>\r\n	  </p>\r\n	    </td>\r\n	  <td valign="top" bgcolor="#FFFFFF">Kolpinghuis Nijmegen</td>\r\n	  <td valign="top" bgcolor="#FFFFFF">danser</td>\r\n    </tr>\r\n	<tr>\r\n      <td valign="top" bgcolor="#CCCCCC">24-12-2009<br>\r\n        17:00u + 19:00u<br>\r\n        <br>\r\n        27-12-2009<br>\r\n        17:00u + 20:30u<br>\r\n        <br>\r\n        28-12-2009<br>\r\n        17:00u + 20:30u<br>\r\n        <br>\r\n        29-12-2009<br>\r\n        17:00u + 20:30u<br>\r\n        <br>\r\n        30-12-2009<br>\r\n        17:00u + 20:30u<br>\r\n      </td>\r\n	  <td valign="top" bgcolor="#CCCCCC">kerstvoorstelling<br>\r\n      (<a href="http://www.koningstheater.nl//docs/Akademie/Flyer_klein_a5.pdf">download flyer</a>)</td>\r\n	  <td valign="top" bgcolor="#CCCCCC">Koningstheater akademie<br>\r\n      Havensingel 25, Den Bosch</td>\r\n	  <td valign="top" bgcolor="#CCCCCC">theatermaker</td>\r\n    </tr>\r\n	<tr>\r\n      <td valign="top" bgcolor="#FFFFFF">30-01-2010</td>\r\n	  <td valign="top" bgcolor="#FFFFFF">try out: eerste wedstrijd seizoen</td>\r\n	  <td valign="top" bgcolor="#FFFFFF">Olympic Sporthal<br>\r\n      Nieuweweg 203, Wychen</td>\r\n	  <td valign="top" bgcolor="#FFFFFF">danser</td>\r\n    </tr>\r\n	<tr>\r\n      <td valign="top" bgcolor="#CCCCCC">14-02-2010</td>\r\n	  <td valign="top" bgcolor="#CCCCCC">wedstrijd<br><b><font class="Apple-style-span" color="#FF0000">WEGENS ZIEKTE AFGELAST</font></b></td>\r\n	  <td valign="top" bgcolor="#CCCCCC">Kerkdriel</td>\r\n	  <td valign="top" bgcolor="#CCCCCC">danser</td>\r\n    </tr>\r\n	<tr>\r\n      <td valign="top" bgcolor="#FFFFFF">20-02-2010 <br>\r\n      21-02-2010</td>\r\n	  <td valign="top" bgcolor="#FFFFFF">NTDS<br>(studenten danswedstrijd)</td>\r\n	  <td valign="top" bgcolor="#FFFFFF">Eindhoven</td>\r\n	  <td valign="top" bgcolor="#FFFFFF">jurylid</td>\r\n    </tr>\r\n	<tr>\r\n      <td valign="top" bgcolor="#CCCCCC">14-03-2010</td>\r\n	  <td valign="top" bgcolor="#CCCCCC">Wedstrijd <br><b><font class="Apple-style-span" color="#FF0000">WEGENS ZIEKTE AFGELAST</font></b></td>\r\n	  <td valign="top" bgcolor="#CCCCCC">Steenwijk</td>\r\n	  <td valign="top" bgcolor="#CCCCCC">danser</td>\r\n    </tr>\r\n	<tr>\r\n      <td valign="top" bgcolor="#FFFFFF">20-03-2010</td>\r\n	  <td valign="top" bgcolor="#FFFFFF">NK latin<br><font class="Apple-style-span" color="#FF0000"><b><br></b></font></td>\r\n	  <td valign="top" bgcolor="#FFFFFF">Almere</td>\r\n	  <td valign="top" bgcolor="#FFFFFF">danser</td>\r\n    </tr>\r\n	<tr>\r\n      <td valign="top" bgcolor="#CCCCCC">24-03-2010</td>\r\n	  <td valign="top" bgcolor="#CCCCCC">presentatie<br><font class="Apple-style-span" color="#FF0000"><b><span class="Apple-style-span" style="color: rgb(0, 0, 0); font-weight: normal; "><b><font class="Apple-style-span" color="#FF0000">WEGENS ZIEKTE AFGELAST</font></b></span></b></font></td>\r\n	  <td valign="top" bgcolor="#CCCCCC">Koningstheater akademie<br>\r\n      Havensingel 25, Den Bosch</td>\r\n	  <td valign="top" bgcolor="#CCCCCC">theatermaker</td>\r\n    </tr>\r\n	<tr>\r\n      <td valign="top" bgcolor="#FFFFFF">18-05-2010</td>\r\n	  <td valign="top" bgcolor="#FFFFFF">presentatie</td>\r\n	  <td valign="top" bgcolor="#FFFFFF">Koningstheater akademie<br>\r\n      Havensingel 25, Den Bosch</td>\r\n	  <td valign="top" bgcolor="#FFFFFF">theatermaker</td>\r\n    </tr>\r\n</tbody></table>\r\n', '', ''),
(22, 'videos', 'Video`s', 'Video`s', 'Een dansvideo uit 2008:<br>\r\n<br>\r\n<object width="425" height="344"><param name="movie" value="http://www.youtube.com/v/a_rGAsCUh4k&hl=nl_NL&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/a_rGAsCUh4k&hl=nl_NL&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="425" height="344"></embed></object><br>\r\n<br>\r\n', '', '');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tbltesttable`
--

CREATE TABLE IF NOT EXISTS `tbltesttable` (
  `i_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `dt_recordchanged` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dt_recordcreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `s_checksum` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`i_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='created:14-08-2014 20:02' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblUserGroup`
--

CREATE TABLE IF NOT EXISTS `tblUserGroup` (
  `i_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `dt_recordchanged` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dt_recordcreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `s_checksum` varchar(128) DEFAULT NULL,
  `s_name` varchar(50) NOT NULL,
  PRIMARY KEY (`i_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='created:14-08-2014 20:02' AUTO_INCREMENT=2 ;

--
-- Gegevens worden uitgevoerd voor tabel `tblUserGroup`
--

INSERT INTO `tblUserGroup` (`i_ID`, `dt_recordchanged`, `dt_recordcreated`, `s_checksum`, `s_name`) VALUES
(1, '0000-00-00 00:00:00', '2014-08-14 18:02:12', '6eee40baec54325261baaab3c0b6b75acd71487e', 'administrators');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tbl___TableVersions`
--

CREATE TABLE IF NOT EXISTS `tbl___TableVersions` (
  `i_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `s_checksum` varchar(128) DEFAULT NULL,
  `s_name` varchar(255) NOT NULL,
  `i_updateNumber` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`i_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='created:14-08-2014 20:02' AUTO_INCREMENT=28 ;

--
-- Gegevens worden uitgevoerd voor tabel `tbl___TableVersions`
--

INSERT INTO `tbl___TableVersions` (`i_ID`, `s_checksum`, `s_name`, `i_updateNumber`) VALUES
(1, '73c155ccd8d2a5bf0a078e82a29002dc94c3071a', 'tblCMS3_CMSACLRules', 0),
(2, '20d268164d57053b5a3e957d9fc0dd4df1824517', 'tblCMS3_CMSUsers', 0),
(3, '90c3f98557bff7ccf2ebdc8bb20b4d344d9f2175', 'tblCMS3_CMSUsersLoginAttempts', 0),
(4, '2aeff3a93b798365088044323bf05fee25c87473', 'tblCMS3_UserGroup', 0),
(5, '40f21b3b50f79e41be717e8375d89141cb819673', 'tblCMS3____TableVersions', 0),
(6, 'b2c10044ec38db53e70efc1433ee6a9a13b303c7', 'tblCMS3____Websites', 0),
(7, 'f504eba87734e621f1efb383660a53180852e3ff', 'tblCMS3_testtable', 0),
(8, '947c4d1b133f4c27c1cb2c3924606df4a4d1a243', 'tblCMSACLRules', 0),
(9, 'add22c6723fcabf19c2eb8838298a3ef39271f43', 'tblCMSUsers', 0),
(10, '7d2a13e6c187b14db69caceefe1227859ec34927', 'tblCMSUsersLoginAttempts', 0),
(11, '5f080c8729063a318bbdb7f6c7d08bae9dffc618', 'tblDRAuthorisatie', 0),
(12, '12ad01f95fd2d331357c8ee543f43e9c38a77204', 'tblDRAuthorisatieBlocked', 0),
(13, '8c5e09c17b6f81a73745c5b96336a87215d249f2', 'tblDRAuthorisatieLogs', 0),
(14, '8c467f0f4cba760e65b3646df329f868705afd2d', 'tblDRCategories', 0),
(15, '3504876131f754a9e46dd3f937968fe3054b3d92', 'tblDRMailinglist', 0),
(16, '4cc6d7dbebba8eb7f3bbf84c43464d3949976f9f', 'tblDRMailinglisttemp', 0),
(17, 'aeab3f4d88c9c35b828c55da8b5c15c8510051c4', 'tblDRMaterial', 0),
(18, '2628a2938ead5dc8019bee097f08bc3c027f8dfc', 'tblDRMaterialReactions', 0),
(19, '423d08525559a4387e9374fff346abbca33a98b2', 'tblDRMaterialStats', 0),
(20, '22d56fb887d1e33dbf49652336f6c72610890a46', 'tblDRMaterialTags', 0),
(21, '30ae70142ab6456d45ba7414cc9238620af274b9', 'tblDRNieuws', 0),
(22, 'c5f8d2b8c2ec6c32c975239919bfb1626bbad48b', 'tblDRNieuwsbrieven', 0),
(23, '0668900fb5c9ae7899d9a41d74527b14ef100fb7', 'tblDRSitecontent', 0),
(24, '35cdb564db73f024f05772021bcd8be284d1c589', 'tblUserGroup', 0),
(25, 'eeba1106fca8655a8c30118166419cebb4032887', 'tbl___TableVersions', 0),
(26, 'a6f56b0a4f14582e2a4ad3b619c34ecaf9f87b6a', 'tbl___Websites', 0),
(27, 'ebfbabf88b463641df3771dfd453428f1543dee8', 'tbltesttable', 3);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tbl___Websites`
--

CREATE TABLE IF NOT EXISTS `tbl___Websites` (
  `i_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `b_recordisdeleted` tinyint(1) unsigned NOT NULL,
  `s_checksum` varchar(128) DEFAULT NULL,
  `s_name` varchar(50) NOT NULL,
  PRIMARY KEY (`i_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='created:14-08-2014 20:02' AUTO_INCREMENT=3 ;

--
-- Gegevens worden uitgevoerd voor tabel `tbl___Websites`
--

INSERT INTO `tbl___Websites` (`i_ID`, `b_recordisdeleted`, `s_checksum`, `s_name`) VALUES
(1, 0, '649f0a6f875754279434cc84fd515c45d74872c4', 'cms.dennisrenirie.nl'),
(2, 0, '1cc58c62541b79ae1dde4bc23c175a6862b8a24c', 'www.dennisrenirie.nl');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `test`
--

CREATE TABLE IF NOT EXISTS `test` (
  `i_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `c_bedrag1` decimal(18,6) NOT NULL,
  `c_bedrag2` decimal(13,4) NOT NULL,
  `i_testcol` int(11) NOT NULL,
  PRIMARY KEY (`i_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='created:04-05-2014 13:34' AUTO_INCREMENT=29 ;

--
-- Gegevens worden uitgevoerd voor tabel `test`
--

INSERT INTO `test` (`i_id`, `c_bedrag1`, `c_bedrag2`, `i_testcol`) VALUES
(25, 0.250000, 0.7800, 1),
(26, 0.000000, 8.2350, 0),
(27, 0.000000, 123.4560, 0),
(28, 0.000000, 123.4560, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
