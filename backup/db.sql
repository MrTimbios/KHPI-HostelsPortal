-- phpMyAdmin SQL Dump
-- version 4.9.4
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Ноя 14 2020 г., 01:43
-- Версия сервера: 5.7.31-cll-lve
-- Версия PHP: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `v43960_espo`
--

-- --------------------------------------------------------

--
-- Структура таблицы `dle_admin_logs`
--

CREATE TABLE `dle_admin_logs` (
  `id` int(11) NOT NULL,
  `name` varchar(40) NOT NULL DEFAULT '',
  `date` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `ip` varchar(46) NOT NULL DEFAULT '',
  `action` int(11) NOT NULL DEFAULT '0',
  `extras` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `dle_admin_logs`
--

INSERT INTO `dle_admin_logs` (`id`, `name`, `date`, `ip`, `action`, `extras`) VALUES
(1, 'ADMIN', 1589592673, '193.110.169.218', 82, ''),
(2, 'ADMIN', 1589592825, '193.110.169.218', 48, ''),
(3, 'ADMIN', 1589711972, '193.110.169.218', 59, 'page'),
(4, 'ADMIN', 1589714688, '193.110.169.218', 60, 'page'),
(5, 'ADMIN', 1589716717, '193.110.169.218', 48, ''),
(6, 'ADMIN', 1589716763, '193.110.169.218', 48, ''),
(7, 'ADMIN', 1589716820, '193.110.169.218', 48, ''),
(8, 'ADMIN', 1589839598, '193.110.169.218', 59, 'page1'),
(9, 'ADMIN', 1589839701, '193.110.169.218', 40, ''),
(10, 'ADMIN', 1589839855, '193.110.169.218', 13, '8'),
(11, 'ADMIN', 1589839862, '193.110.169.218', 11, ''),
(12, 'ADMIN', 1589839878, '193.110.169.218', 13, '7'),
(13, 'ADMIN', 1589839884, '193.110.169.218', 13, '6'),
(14, 'ADMIN', 1589839894, '193.110.169.218', 13, '5'),
(15, 'ADMIN', 1589839900, '193.110.169.218', 13, '4'),
(16, 'ADMIN', 1589840036, '193.110.169.218', 13, '3'),
(17, 'ADMIN', 1589840043, '193.110.169.218', 13, '2'),
(18, 'ADMIN', 1589841574, '193.110.169.218', 59, 'hostels'),
(19, 'ADMIN', 1589841834, '193.110.169.218', 60, 'hostels'),
(20, 'ADMIN', 1589841984, '193.110.169.218', 60, 'hostels'),
(21, 'ADMIN', 1589842069, '193.110.169.218', 60, 'hostels'),
(22, 'ADMIN', 1589842271, '193.110.169.218', 60, 'hostels'),
(23, 'ADMIN', 1589842310, '193.110.169.218', 60, 'hostels'),
(24, 'ADMIN', 1589842527, '193.110.169.218', 60, 'hostels'),
(25, 'ADMIN', 1589842583, '193.110.169.218', 60, 'hostels'),
(26, 'ADMIN', 1589842622, '193.110.169.218', 60, 'hostels'),
(27, 'ADMIN', 1589842771, '193.110.169.218', 60, 'hostels'),
(28, 'ADMIN', 1589842813, '193.110.169.218', 60, 'hostels'),
(29, 'ADMIN', 1589842918, '193.110.169.218', 60, 'hostels'),
(30, 'ADMIN', 1589842987, '193.110.169.218', 60, 'hostels'),
(31, 'ADMIN', 1589843277, '193.110.169.218', 60, 'hostels'),
(32, 'ADMIN', 1589843355, '193.110.169.218', 60, 'hostels'),
(33, 'ADMIN', 1589843457, '193.110.169.218', 60, 'hostels'),
(34, 'ADMIN', 1589843734, '193.110.169.218', 40, ''),
(35, 'ADMIN', 1589844427, '193.110.169.218', 36, '1589844444_dsc_3991-1.jpg'),
(36, 'ADMIN', 1589844988, '193.110.169.218', 48, ''),
(37, 'ADMIN', 1589845005, '193.110.169.218', 59, 'hostels-1'),
(38, 'ADMIN', 1589845032, '193.110.169.218', 36, '1589845050_hostel-1.jpg'),
(39, 'ADMIN', 1589845130, '193.110.169.218', 48, ''),
(40, 'ADMIN', 1589845168, '193.110.169.218', 36, '1589845240_hostel-1.jpg'),
(41, 'ADMIN', 1589845275, '193.110.169.218', 60, 'hostels-1'),
(42, 'ADMIN', 1589845342, '193.110.169.218', 60, 'hostels-1'),
(43, 'ADMIN', 1589845406, '193.110.169.218', 60, 'hostels-1'),
(44, 'ADMIN', 1589845608, '193.110.169.218', 59, 'hostels-1-map'),
(45, 'ADMIN', 1589845708, '193.110.169.218', 60, 'hostels-1-map'),
(46, 'ADMIN', 1589845954, '193.110.169.218', 60, 'hostels'),
(47, 'ADMIN', 1589846031, '193.110.169.218', 14, 'О скрипте'),
(48, 'ADMIN', 1589846440, '193.110.169.218', 39, ''),
(49, 'ADMIN', 1589846452, '193.110.169.218', 39, ''),
(50, 'ADMIN', 1589846581, '193.110.169.218', 59, 'rent'),
(51, 'ADMIN', 1589846614, '193.110.169.218', 60, 'rent'),
(52, 'ADMIN', 1589846658, '193.110.169.218', 60, 'rent'),
(53, 'ADMIN', 1589846907, '193.110.169.218', 60, 'rent'),
(54, 'ADMIN', 1589847574, '193.110.169.218', 60, 'rent'),
(55, 'ADMIN', 1589849019, '193.110.169.218', 60, 'rent'),
(56, 'ADMIN', 1589849148, '193.110.169.218', 60, 'rent'),
(57, 'ADMIN', 1589849243, '193.110.169.218', 60, 'rent'),
(58, 'ADMIN', 1589922184, '193.110.169.218', 64, 'ADMIN'),
(59, 'ADMIN', 1589924297, '193.110.169.218', 82, ''),
(60, 'ADMIN', 1589924531, '193.110.169.218', 89, ''),
(61, 'ADMIN', 1589925946, '193.110.169.218', 82, ''),
(62, 'ADMIN', 1589930117, '193.110.169.218', 26, 'Добро пожаловать'),
(63, 'ADMIN', 1589930117, '193.110.169.218', 26, 'Приобретение и оплата скрипта'),
(64, 'ADMIN', 1589930117, '193.110.169.218', 26, 'Осуществление технической поддержки скрипта'),
(65, 'ADMIN', 1589930575, '193.110.169.218', 1, '17 января 2020г. в общежитии «Гигант» НТУ «ХПИ» (I и II секции) прошли противопожарные учения.'),
(66, 'ADMIN', 1589930635, '193.110.169.218', 25, '17 января 2020г. в общежитии «Гигант» НТУ «ХПИ» (I и II секции) прошли противопожарные учения.'),
(67, 'ADMIN', 1589930850, '193.110.169.218', 36, '1589930870_1589930836217.png'),
(68, 'ADMIN', 1589930888, '193.110.169.218', 1, '⚡️Перемога в суді: ХПІ має знизити ціну за проживання в гуртожитках під час літніх канікул'),
(69, 'ADMIN', 1589930974, '193.110.169.218', 14, 'Новости студгородка'),
(70, 'ADMIN', 1590416655, '193.110.169.198', 59, 'settlement'),
(71, 'ADMIN', 1590416874, '193.110.169.198', 59, 'abiturientu'),
(72, 'ADMIN', 1590419677, '193.110.169.198', 60, 'settlement'),
(73, 'ADMIN', 1590419755, '193.110.169.198', 60, 'settlement'),
(74, 'ADMIN', 1590419852, '193.110.169.198', 60, 'settlement'),
(75, 'ADMIN', 1590419890, '193.110.169.198', 60, 'settlement'),
(76, 'ADMIN', 1590420043, '193.110.169.198', 60, 'settlement'),
(77, 'ADMIN', 1590420158, '193.110.169.198', 60, 'settlement'),
(78, 'ADMIN', 1590420575, '193.110.169.198', 60, 'abiturientu'),
(79, 'ADMIN', 1590422853, '193.110.169.198', 60, 'abiturientu'),
(80, 'ADMIN', 1590422889, '193.110.169.198', 60, 'abiturientu'),
(81, 'ADMIN', 1590427615, '193.110.169.198', 59, 'hostels-2'),
(82, 'ADMIN', 1590427634, '193.110.169.198', 60, 'hostels-2'),
(83, 'ADMIN', 1590427659, '193.110.169.198', 59, 'hostels-3'),
(84, 'ADMIN', 1590429536, '193.110.169.198', 36, '1590429604_p1240411-1024x545.jpg'),
(85, 'ADMIN', 1590429632, '193.110.169.198', 36, '1590429647_p1240445.jpg'),
(86, 'ADMIN', 1590429671, '193.110.169.198', 59, 'firstaid'),
(87, 'ADMIN', 1590429820, '193.110.169.198', 60, 'firstaid'),
(88, 'ADMIN', 1590429859, '193.110.169.198', 60, 'firstaid'),
(89, 'ADMIN', 1590430115, '193.110.169.198', 60, 'firstaid'),
(90, 'ADMIN', 1590430235, '193.110.169.198', 60, 'firstaid'),
(91, 'ADMIN', 1590430330, '193.110.169.198', 60, 'firstaid'),
(92, 'ADMIN', 1590430848, '193.110.169.198', 60, 'abiturientu'),
(93, 'ADMIN', 1590431069, '193.110.169.198', 60, 'abiturientu'),
(94, 'ADMIN', 1590438648, '193.110.169.198', 59, 'subsidies'),
(95, 'ADMIN', 1590438876, '193.110.169.198', 60, 'abiturientu'),
(96, 'ADMIN', 1590438895, '193.110.169.198', 60, 'abiturientu'),
(97, 'ADMIN', 1590439224, '193.110.169.198', 36, '1590439309_obshezhitie_02.jpg'),
(98, 'ADMIN', 1590439692, '193.110.169.198', 60, 'hostels-2'),
(99, 'ADMIN', 1590439905, '193.110.169.198', 59, 'hostels-2-map'),
(100, 'ADMIN', 1590449937, '193.110.169.198', 59, 'foreigners'),
(101, 'ADMIN', 1590453343, '193.110.169.198', 60, 'settlement'),
(102, 'ADMIN', 1590453408, '193.110.169.198', 60, 'settlement'),
(103, 'ADMIN', 1590453469, '193.110.169.198', 60, 'settlement'),
(104, 'ADMIN', 1590453529, '193.110.169.198', 60, 'settlement'),
(105, 'ADMIN', 1590453565, '193.110.169.198', 60, 'settlement'),
(106, 'ADMIN', 1590453896, '193.110.169.198', 60, 'hostels'),
(107, 'ADMIN', 1591779667, '193.110.169.150', 82, ''),
(108, 'ADMIN', 1591843934, '193.110.169.150', 60, 'abiturientu'),
(109, 'ADMIN', 1591844304, '193.110.169.150', 59, 'hostels-4'),
(110, 'ADMIN', 1591844769, '193.110.169.150', 36, '1591844851_xxxl.jpg'),
(111, 'ADMIN', 1591844789, '193.110.169.150', 60, 'hostels-3'),
(112, 'ADMIN', 1591851574, '193.110.169.150', 36, '1591851669_5.jpg'),
(113, 'ADMIN', 1591851585, '193.110.169.150', 59, 'hostels-5'),
(114, 'ADMIN', 1591851856, '193.110.169.150', 36, '1591851936_dsc_1975.jpg'),
(115, 'ADMIN', 1591851874, '193.110.169.150', 59, 'hostels-6'),
(116, 'ADMIN', 1592334472, '159.224.76.61', 82, ''),
(117, 'ADMIN', 1592335024, '159.224.76.61', 60, 'abiturientu'),
(118, 'ADMIN', 1592335082, '159.224.76.61', 60, 'abiturientu'),
(119, 'ADMIN', 1592335204, '159.224.76.61', 60, 'abiturientu'),
(120, 'ADMIN', 1593244943, '193.110.169.185', 82, ''),
(121, 'ADMIN', 1593246779, '193.110.169.185', 36, '1593246787_img_7084.jpg'),
(122, 'ADMIN', 1593246878, '193.110.169.185', 59, 'hostels-7'),
(123, 'ADMIN', 1593247983, '193.110.169.185', 36, '1593248082_snimok.png'),
(124, 'ADMIN', 1593248414, '193.110.169.185', 59, 'hostels-8'),
(125, 'ADMIN', 1593250336, '193.110.169.185', 36, '1593250384_foto_17828.jpg'),
(126, 'ADMIN', 1593250371, '193.110.169.185', 36, '1593250430_x_22c8a434.jpg'),
(127, 'ADMIN', 1593250452, '193.110.169.185', 59, 'hostels-9'),
(128, 'ADMIN', 1593250742, '193.110.169.185', 36, '1593250780_snimo1k.png'),
(129, 'ADMIN', 1593250817, '193.110.169.185', 59, 'hostels-10'),
(130, 'ADMIN', 1593250829, '193.110.169.185', 60, 'hostels-9'),
(131, 'ADMIN', 1593251164, '193.110.169.185', 36, '1593251227_dsc_2685-1024x685.jpg'),
(132, 'ADMIN', 1593251330, '193.110.169.185', 59, 'hostels-11'),
(133, 'ADMIN', 1593251871, '193.110.169.185', 36, '1593251874_img_0028-12.jpg'),
(134, 'ADMIN', 1593251918, '193.110.169.185', 59, 'hostels-12'),
(135, 'ADMIN', 1593255422, '193.110.169.185', 36, '1593255433_2017-10-17.jpg'),
(136, 'ADMIN', 1593255473, '193.110.169.185', 59, 'hostels-13'),
(137, 'ADMIN', 1593255910, '193.110.169.185', 60, 'hostels-7'),
(138, 'ADMIN', 1593258424, '193.110.169.185', 36, '1593258477_img_0267-1024x683.jpg'),
(139, 'ADMIN', 1593258486, '193.110.169.185', 59, 'hostels-14'),
(140, 'ADMIN', 1593259536, '193.110.169.185', 36, '1593259588_sn2imok.png'),
(141, 'ADMIN', 1593259584, '193.110.169.185', 59, 'hostels-15'),
(142, 'ADMIN', 1593259996, '193.110.169.185', 36, '1593260062_93_big.jpg'),
(143, 'ADMIN', 1593260030, '193.110.169.185', 60, 'hostels-4'),
(144, 'ADMIN', 1593293993, '193.110.169.185', 36, '1593294021_karta-sajta.png'),
(145, 'ADMIN', 1593294035, '193.110.169.185', 59, 'about'),
(146, 'ADMIN', 1593294133, '193.110.169.185', 60, 'about'),
(147, 'ADMIN', 1593380921, '159.224.76.61', 82, ''),
(148, 'ADMIN', 1593380955, '159.224.76.61', 36, '1593381028_karta-sajta.png'),
(149, 'ADMIN', 1593381191, '159.224.76.61', 60, 'about'),
(150, 'ADMIN', 1593435495, '159.224.76.61', 59, 'hostels-3-map'),
(151, 'ADMIN', 1593435533, '159.224.76.61', 60, 'hostels-3-map'),
(152, 'ADMIN', 1593435574, '159.224.76.61', 60, 'hostels-3-map'),
(153, 'ADMIN', 1593435592, '159.224.76.61', 60, 'hostels-2-map'),
(154, 'ADMIN', 1593435785, '159.224.76.61', 59, 'hostels-4-map'),
(155, 'ADMIN', 1593435951, '159.224.76.61', 59, 'hostels-5-map'),
(156, 'ADMIN', 1593436194, '159.224.76.61', 59, 'hostels-6-map'),
(157, 'ADMIN', 1593436481, '159.224.76.61', 59, 'hostels-7-map'),
(158, 'ADMIN', 1593436854, '159.224.76.61', 59, 'hostels-8-map'),
(159, 'ADMIN', 1593437100, '159.224.76.61', 59, 'hostels-10-map'),
(160, 'ADMIN', 1593437223, '159.224.76.61', 59, 'hostels-9-map'),
(161, 'ADMIN', 1593437402, '159.224.76.61', 59, 'hostels-11-map'),
(162, 'ADMIN', 1593437642, '159.224.76.61', 59, 'hostels-12-map'),
(163, 'ADMIN', 1593437875, '159.224.76.61', 59, 'hostels-13-map'),
(164, 'ADMIN', 1593438115, '159.224.76.61', 59, 'hostels-14-map'),
(165, 'ADMIN', 1593438235, '159.224.76.61', 59, 'hostels-15-map'),
(166, 'ADMIN', 1593438292, '159.224.76.61', 60, 'hostels'),
(167, 'ADMIN', 1593438331, '159.224.76.61', 60, 'foreigners'),
(168, 'ADMIN', 1593438384, '159.224.76.61', 60, 'hostels-3'),
(169, 'ADMIN', 1593438420, '159.224.76.61', 60, 'hostels-2'),
(170, 'ADMIN', 1593438610, '159.224.76.61', 60, 'hostels-1'),
(171, 'ADMIN', 1594308869, '193.110.169.137', 82, ''),
(172, 'ADMIN', 1594309319, '193.110.169.137', 60, 'hostels'),
(173, 'ADMIN', 1594309331, '193.110.169.137', 60, 'hostels-9'),
(174, 'ADMIN', 1594309468, '193.110.169.137', 60, 'hostels'),
(175, 'ADMIN', 1594309485, '193.110.169.137', 60, 'hostels-15'),
(176, 'ADMIN', 1595702133, '193.110.169.240', 86, 'https://espo.co.ua/subsidies.html'),
(177, 'ADMIN', 1595702155, '193.110.169.240', 60, 'subsidies'),
(178, 'ADMIN', 1595806429, '193.110.169.240', 59, 'polozhennjam-pro-studentski-gurtozhitki-ntu-hpi'),
(179, 'ADMIN', 1595806566, '193.110.169.240', 60, 'pologennja'),
(180, 'ADMIN', 1595806691, '193.110.169.240', 60, 'pologennja'),
(181, 'ADMIN', 1595806955, '193.110.169.240', 60, 'pologennja'),
(182, 'ADMIN', 1595807087, '193.110.169.240', 60, 'settlement'),
(183, 'ADMIN', 1595807197, '193.110.169.240', 60, 'pologennja'),
(184, 'ADMIN', 1595807231, '193.110.169.240', 60, 'settlement'),
(185, 'ADMIN', 1595807265, '193.110.169.240', 60, 'settlement'),
(186, 'ADMIN', 1595807312, '193.110.169.240', 60, 'settlement'),
(187, 'ADMIN', 1595862210, '193.110.169.216', 60, 'settlement'),
(188, 'ADMIN', 1595862274, '193.110.169.216', 60, 'pologennja'),
(189, 'ADMIN', 1598922471, '159.192.221.64', 89, ''),
(190, 'ADMIN', 1599005093, '125.25.76.209', 89, ''),
(191, 'ADMIN', 1599225882, '157.230.50.42', 89, ''),
(192, 'ADMIN', 1599342419, '161.35.109.71', 89, ''),
(193, 'ADMIN', 1599379627, '113.180.209.100', 89, ''),
(194, 'ADMIN', 1599408899, '18.141.199.207', 89, ''),
(195, 'ADMIN', 1599414834, '36.229.4.53', 89, ''),
(196, 'ADMIN', 1599433538, '177.190.196.168', 89, ''),
(197, 'ADMIN', 1599438526, '77.69.155.70', 89, ''),
(198, 'ADMIN', 1599444565, '14.245.9.49', 89, ''),
(199, 'ADMIN', 1599446704, '118.137.75.4', 89, ''),
(200, 'ADMIN', 1599447634, '36.82.90.149', 89, ''),
(201, 'ADMIN', 1599448315, '116.111.99.189', 89, ''),
(202, 'ADMIN', 1599448388, '124.40.251.218', 89, ''),
(203, 'ADMIN', 1599448508, '123.21.69.187', 89, ''),
(204, 'ADMIN', 1599448580, '130.105.181.43', 89, ''),
(205, 'ADMIN', 1599593515, '77.121.95.25', 89, ''),
(206, 'ADMIN', 1599659922, '195.191.138.113', 89, ''),
(207, 'ADMIN', 1599710276, '223.206.226.115', 89, ''),
(208, 'ADMIN', 1599818961, '14.207.152.169', 89, ''),
(209, 'ADMIN', 1599903943, '202.165.244.157', 89, ''),
(210, 'ADMIN', 1600026092, '180.190.174.143', 89, ''),
(211, 'ADMIN', 1600067204, '43.228.73.50', 89, ''),
(212, 'ADMIN', 1600091541, '190.232.148.64', 89, ''),
(213, 'ADMIN', 1600132810, '124.105.34.123', 89, ''),
(214, 'ADMIN', 1600140058, '165.22.65.6', 89, ''),
(215, 'ADMIN', 1600175264, '93.189.216.24', 89, ''),
(216, 'ADMIN', 1600201999, '137.59.229.147', 89, ''),
(217, 'ADMIN', 1600292687, '151.241.13.239', 89, ''),
(218, 'ADMIN', 1600388392, '186.211.103.237', 89, ''),
(219, 'ADMIN', 1600458054, '45.235.181.73', 89, ''),
(220, 'ADMIN', 1600488589, '43.251.216.138', 89, ''),
(221, 'ADMIN', 1600593931, '210.245.32.147', 89, ''),
(222, 'ADMIN', 1600668228, '183.88.50.121', 89, ''),
(223, 'ADMIN', 1600830868, '14.237.187.153', 89, ''),
(224, 'ADMIN', 1600892530, '113.185.79.143', 89, ''),
(225, 'ADMIN', 1600950129, '14.169.180.85', 89, ''),
(226, 'ADMIN', 1601063778, '31.133.81.157', 82, ''),
(227, 'ADMIN', 1601063828, '31.133.81.157', 14, 'Новини студмістечка'),
(228, 'ADMIN', 1601179566, '85.172.81.21', 89, ''),
(229, 'ADMIN', 1601214326, '185.133.226.120', 89, ''),
(230, 'ADMIN', 1601324871, '63.245.119.130', 89, ''),
(231, 'ADMIN', 1601404683, '36.91.166.98', 89, ''),
(232, 'ADMIN', 1601754523, '177.103.14.167', 89, ''),
(233, 'ADMIN', 1601999172, '182.52.167.7', 89, ''),
(234, 'ADMIN', 1602028497, '41.59.193.176', 89, ''),
(235, 'ADMIN', 1602079048, '46.36.132.23', 89, ''),
(236, 'ADMIN', 1602086524, '34.125.140.42', 89, ''),
(237, 'ADMIN', 1602137096, '171.231.165.147', 89, ''),
(238, 'ADMIN', 1602169715, '102.101.174.175', 89, ''),
(239, 'ADMIN', 1602188876, '14.178.194.147', 89, ''),
(240, 'ADMIN', 1602232236, '121.200.5.127', 89, ''),
(241, 'ADMIN', 1602256539, '188.235.145.47', 89, ''),
(242, 'ADMIN', 1602292560, '14.239.113.189', 89, ''),
(243, 'ADMIN', 1602312515, '190.237.243.139', 89, ''),
(244, 'ADMIN', 1602379937, '193.188.121.131', 89, ''),
(245, 'ADMIN', 1602442549, '187.49.237.82', 89, ''),
(246, 'ADMIN', 1602466582, '5.45.135.236', 89, ''),
(247, 'ADMIN', 1602479326, '36.91.112.31', 89, ''),
(248, 'ADMIN', 1602555127, '103.92.225.36', 89, ''),
(249, 'ADMIN', 1602580200, '104.248.230.202', 89, ''),
(250, 'ADMIN', 1602602347, '202.80.216.252', 89, ''),
(251, 'ADMIN', 1603069299, '36.88.9.36', 89, ''),
(252, 'ADMIN', 1603096063, '14.241.133.0', 89, ''),
(253, 'ADMIN', 1603128590, '27.55.80.251', 89, ''),
(254, 'ADMIN', 1603149946, '197.218.86.211', 89, ''),
(255, 'ADMIN', 1603247583, '110.169.137.6', 89, ''),
(256, 'ADMIN', 1603268171, '31.214.16.201', 89, ''),
(257, 'ADMIN', 1603313404, '212.36.194.90', 89, ''),
(258, 'ADMIN', 1603365860, '14.252.250.122', 89, ''),
(259, 'ADMIN', 1603397844, '27.71.98.172', 89, ''),
(260, 'ADMIN', 1603424130, '177.208.26.49', 89, ''),
(261, 'ADMIN', 1603462700, '175.158.38.169', 89, ''),
(262, 'ADMIN', 1603493818, '81.108.167.166', 89, ''),
(263, 'ADMIN', 1603526762, '14.247.189.102', 89, ''),
(264, 'ADMIN', 1603543909, '14.251.231.255', 89, ''),
(265, 'ADMIN', 1603561395, '105.66.133.204', 89, ''),
(266, 'ADMIN', 1603595128, '116.58.230.242', 89, ''),
(267, 'ADMIN', 1603626293, '49.145.207.39', 89, ''),
(268, 'ADMIN', 1603649889, '60.49.84.213', 89, ''),
(269, 'ADMIN', 1603663351, '110.78.165.32', 89, ''),
(270, 'ADMIN', 1603693782, '201.213.169.203', 89, ''),
(271, 'ADMIN', 1603742050, '181.118.101.217', 89, ''),
(272, 'ADMIN', 1603776584, '70.171.71.179', 89, ''),
(273, 'ADMIN', 1603787283, '183.89.9.211', 89, ''),
(274, 'ADMIN', 1603827627, '78.177.72.10', 89, ''),
(275, 'ADMIN', 1603838587, '190.106.213.65', 89, ''),
(276, 'ADMIN', 1603880821, '31.133.81.157', 86, 'Direct DLE Adminpanel'),
(277, 'ADMIN', 1603886795, '115.72.4.109', 89, ''),
(278, 'ADMIN', 1603937383, '118.69.134.213', 89, ''),
(279, 'ADMIN', 1603947882, '158.140.170.99', 89, ''),
(280, 'ADMIN', 1603989562, '176.88.80.137', 89, ''),
(281, 'ADMIN', 1604024116, '77.28.161.83', 89, ''),
(282, 'ADMIN', 1604031080, '101.53.45.146', 89, ''),
(283, 'ADMIN', 1604058223, '125.160.149.123', 89, ''),
(284, 'ADMIN', 1604095730, '115.135.148.161', 89, ''),
(285, 'ADMIN', 1604114864, '113.174.54.27', 89, ''),
(286, 'ADMIN', 1604169858, '109.186.200.84', 89, ''),
(287, 'ADMIN', 1604214582, '202.187.69.215', 89, ''),
(288, 'ADMIN', 1604245583, '39.40.3.34', 89, ''),
(289, 'ADMIN', 1604263902, '84.24.120.215', 89, ''),
(290, 'ADMIN', 1604288389, '101.108.102.53', 89, ''),
(291, 'ADMIN', 1604338366, '1.10.183.73', 89, ''),
(292, 'ADMIN', 1604393580, '1.22.101.176', 89, ''),
(293, 'ADMIN', 1604414922, '101.50.107.177', 89, ''),
(294, 'ADMIN', 1604456438, '191.98.180.122', 89, ''),
(295, 'ADMIN', 1604490486, '49.207.128.110', 89, ''),
(296, 'ADMIN', 1604527160, '112.135.231.101', 89, ''),
(297, 'ADMIN', 1604554070, '103.84.202.247', 89, ''),
(298, 'ADMIN', 1604590607, '171.7.247.116', 89, ''),
(299, 'ADMIN', 1604605554, '60.54.89.223', 89, ''),
(300, 'ADMIN', 1604643396, '223.205.251.17', 89, ''),
(301, 'ADMIN', 1604682910, '123.27.152.143', 89, ''),
(302, 'ADMIN', 1604690137, '45.160.111.90', 89, ''),
(303, 'ADMIN', 1604774227, '89.131.22.31', 89, ''),
(304, 'ADMIN', 1604804634, '188.69.209.107', 89, ''),
(305, 'ADMIN', 1604848214, '118.71.185.216', 89, ''),
(306, 'ADMIN', 1604871325, '78.189.138.142', 89, ''),
(307, 'ADMIN', 1604898805, '178.89.60.240', 89, ''),
(308, 'ADMIN', 1604932967, '143.0.84.86', 89, ''),
(309, 'ADMIN', 1604954840, '177.19.64.129', 89, ''),
(310, 'ADMIN', 1604981999, '27.123.222.230', 89, ''),
(311, 'ADMIN', 1605010356, '152.57.62.177', 89, ''),
(312, 'ADMIN', 1605026801, '45.135.187.69', 89, ''),
(313, 'ADMIN', 1605078217, '117.4.107.76', 89, ''),
(314, 'ADMIN', 1605093108, '154.125.198.122', 89, ''),
(315, 'ADMIN', 1605139973, '49.145.200.245', 89, ''),
(316, 'ADMIN', 1605160929, '36.69.4.235', 89, ''),
(317, 'ADMIN', 1605181158, '103.146.151.21', 89, ''),
(318, 'ADMIN', 1605223611, '14.250.175.244', 89, ''),
(319, 'ADMIN', 1605244298, '115.87.199.153', 89, ''),
(320, 'ADMIN', 1605279992, '95.7.3.195', 89, ''),
(321, 'ADMIN', 1605297010, '179.191.239.251', 89, ''),
(322, 'ADMIN', 1605307258, '31.133.81.157', 86, 'Direct DLE Adminpanel'),
(323, 'ADMIN', 1605307373, '31.133.81.157', 64, 'ADMIN'),
(324, 'ADMIN', 1605307378, '31.133.81.157', 90, '');

-- --------------------------------------------------------

--
-- Структура таблицы `dle_admin_sections`
--

CREATE TABLE `dle_admin_sections` (
  `id` mediumint(8) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `descr` varchar(255) NOT NULL DEFAULT '',
  `icon` varchar(255) NOT NULL DEFAULT '',
  `allow_groups` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_banned`
--

CREATE TABLE `dle_banned` (
  `id` smallint(5) NOT NULL,
  `users_id` int(11) NOT NULL DEFAULT '0',
  `descr` text NOT NULL,
  `date` varchar(15) NOT NULL DEFAULT '',
  `days` smallint(4) NOT NULL DEFAULT '0',
  `ip` varchar(46) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_banners`
--

CREATE TABLE `dle_banners` (
  `id` smallint(5) NOT NULL,
  `banner_tag` varchar(40) NOT NULL DEFAULT '',
  `descr` varchar(200) NOT NULL DEFAULT '',
  `code` text NOT NULL,
  `approve` tinyint(1) NOT NULL DEFAULT '0',
  `short_place` tinyint(1) NOT NULL DEFAULT '0',
  `bstick` tinyint(1) NOT NULL DEFAULT '0',
  `main` tinyint(1) NOT NULL DEFAULT '0',
  `category` varchar(255) NOT NULL DEFAULT '',
  `grouplevel` varchar(100) NOT NULL DEFAULT 'all',
  `start` varchar(15) NOT NULL DEFAULT '',
  `end` varchar(15) NOT NULL DEFAULT '',
  `fpage` tinyint(1) NOT NULL DEFAULT '0',
  `innews` tinyint(1) NOT NULL DEFAULT '0',
  `devicelevel` varchar(10) NOT NULL DEFAULT '',
  `allow_views` tinyint(1) NOT NULL DEFAULT '0',
  `max_views` int(11) NOT NULL DEFAULT '0',
  `allow_counts` tinyint(1) NOT NULL DEFAULT '0',
  `max_counts` int(11) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0',
  `clicks` int(11) NOT NULL DEFAULT '0',
  `rubric` mediumint(8) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `dle_banners`
--

INSERT INTO `dle_banners` (`id`, `banner_tag`, `descr`, `code`, `approve`, `short_place`, `bstick`, `main`, `category`, `grouplevel`, `start`, `end`, `fpage`, `innews`, `devicelevel`, `allow_views`, `max_views`, `allow_counts`, `max_counts`, `views`, `clicks`, `rubric`) VALUES
(1, 'header', 'Верхний баннер', '<div style=\"text-align:center;\"><a href=\"https://dle-news.ru/\" target=\"_blank\"><img src=\"https://espo.co.ua/templates/Default/images/_banner_.gif\" style=\"border: none;\" alt=\"\" /></a></div>', 1, 0, 0, 0, '0', 'all', '', '', 0, 0, '', 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `dle_banners_logs`
--

CREATE TABLE `dle_banners_logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `bid` int(11) NOT NULL DEFAULT '0',
  `click` tinyint(1) NOT NULL DEFAULT '0',
  `ip` varchar(46) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_banners_rubrics`
--

CREATE TABLE `dle_banners_rubrics` (
  `id` mediumint(8) NOT NULL,
  `parentid` mediumint(8) NOT NULL DEFAULT '0',
  `title` varchar(70) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_category`
--

CREATE TABLE `dle_category` (
  `id` mediumint(8) NOT NULL,
  `parentid` mediumint(8) NOT NULL DEFAULT '0',
  `posi` mediumint(8) NOT NULL DEFAULT '1',
  `name` varchar(50) NOT NULL DEFAULT '',
  `alt_name` varchar(50) NOT NULL DEFAULT '',
  `icon` varchar(200) NOT NULL DEFAULT '',
  `skin` varchar(50) NOT NULL DEFAULT '',
  `descr` varchar(300) NOT NULL DEFAULT '',
  `keywords` text NOT NULL,
  `news_sort` varchar(10) NOT NULL DEFAULT '',
  `news_msort` varchar(4) NOT NULL DEFAULT '',
  `news_number` smallint(5) NOT NULL DEFAULT '0',
  `short_tpl` varchar(40) NOT NULL DEFAULT '',
  `full_tpl` varchar(40) NOT NULL DEFAULT '',
  `metatitle` varchar(255) NOT NULL DEFAULT '',
  `show_sub` tinyint(1) NOT NULL DEFAULT '0',
  `allow_rss` tinyint(1) NOT NULL DEFAULT '1',
  `fulldescr` text NOT NULL,
  `disable_search` tinyint(1) NOT NULL DEFAULT '0',
  `disable_main` tinyint(1) NOT NULL DEFAULT '0',
  `disable_rating` tinyint(1) NOT NULL DEFAULT '0',
  `disable_comments` tinyint(1) NOT NULL DEFAULT '0',
  `enable_dzen` tinyint(1) NOT NULL DEFAULT '1',
  `enable_turbo` tinyint(1) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `dle_category`
--

INSERT INTO `dle_category` (`id`, `parentid`, `posi`, `name`, `alt_name`, `icon`, `skin`, `descr`, `keywords`, `news_sort`, `news_msort`, `news_number`, `short_tpl`, `full_tpl`, `metatitle`, `show_sub`, `allow_rss`, `fulldescr`, `disable_search`, `disable_main`, `disable_rating`, `disable_comments`, `enable_dzen`, `enable_turbo`, `active`) VALUES
(1, 0, 1, 'Новини студмістечка', 'hostels-news', '', '', '', '', '', '', 0, '', '', '', 0, 1, '', 0, 0, 0, 0, 1, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `dle_comments`
--

CREATE TABLE `dle_comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `post_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `autor` varchar(40) NOT NULL DEFAULT '',
  `email` varchar(40) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `ip` varchar(46) NOT NULL DEFAULT '',
  `is_register` tinyint(1) NOT NULL DEFAULT '0',
  `approve` tinyint(1) NOT NULL DEFAULT '1',
  `rating` int(11) NOT NULL DEFAULT '0',
  `vote_num` int(11) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `dle_comments`
--

INSERT INTO `dle_comments` (`id`, `post_id`, `user_id`, `date`, `autor`, `email`, `text`, `ip`, `is_register`, `approve`, `rating`, `vote_num`, `parent`) VALUES
(1, 5, 0, '2020-07-25 21:14:30', 'Андрей', 'andreifomenko@yandex.ru', 'Они ничего не вернут..', '193.110.169.240', 0, 1, 0, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `dle_comments_files`
--

CREATE TABLE `dle_comments_files` (
  `id` int(10) NOT NULL,
  `c_id` int(10) NOT NULL DEFAULT '0',
  `author` varchar(40) NOT NULL DEFAULT '',
  `date` varchar(15) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_comment_rating_log`
--

CREATE TABLE `dle_comment_rating_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `c_id` int(11) NOT NULL DEFAULT '0',
  `member` varchar(40) NOT NULL DEFAULT '',
  `ip` varchar(46) NOT NULL DEFAULT '',
  `rating` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_complaint`
--

CREATE TABLE `dle_complaint` (
  `id` int(11) UNSIGNED NOT NULL,
  `p_id` int(11) NOT NULL DEFAULT '0',
  `c_id` int(11) NOT NULL DEFAULT '0',
  `n_id` int(11) NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `from` varchar(40) NOT NULL DEFAULT '',
  `to` varchar(255) NOT NULL DEFAULT '',
  `date` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `email` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_email`
--

CREATE TABLE `dle_email` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(10) NOT NULL DEFAULT '',
  `template` text NOT NULL,
  `use_html` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `dle_email`
--

INSERT INTO `dle_email` (`id`, `name`, `template`, `use_html`) VALUES
(1, 'reg_mail', '{%username%},\r\n\r\nЭто письмо отправлено с сайта https://espo.co.ua/\r\n\r\nВы получили это письмо, так как этот e-mail адрес был использован при регистрации на сайте. Если Вы не регистрировались на этом сайте, просто проигнорируйте это письмо и удалите его. Вы больше не получите такого письма.\r\n\r\n------------------------------------------------\r\nВаш логин и пароль на сайте:\r\n------------------------------------------------\r\n\r\nЛогин: {%username%}\r\nПароль: {%password%}\r\n\r\n------------------------------------------------\r\nИнструкция по активации\r\n------------------------------------------------\r\n\r\nБлагодарим Вас за регистрацию.\r\nМы требуем от Вас подтверждения Вашей регистрации, для проверки того, что введённый Вами e-mail адрес - реальный. Это требуется для защиты от нежелательных злоупотреблений и спама.\r\n\r\nДля активации Вашего аккаунта, зайдите по следующей ссылке:\r\n\r\n{%validationlink%}\r\n\r\nЕсли и при этих действиях ничего не получилось, возможно Ваш аккаунт удалён. В этом случае, обратитесь к Администратору, для разрешения проблемы.\r\n\r\nС уважением,\r\n\r\nАдминистрация https://espo.co.ua/.', 0),
(2, 'feed_mail', '{%username_to%},\r\n\r\nДанное письмо вам отправил {%username_from%} с сайта https://espo.co.ua/\r\n\r\n------------------------------------------------\r\nТекст сообщения\r\n------------------------------------------------\r\n\r\n{%text%}\r\n\r\nIP адрес отправителя: {%ip%}\r\nГруппа: {%group%}\r\n\r\n------------------------------------------------\r\nПомните, что администрация сайта не несет ответственности за содержание данного письма\r\n\r\nС уважением,\r\n\r\nАдминистрация https://espo.co.ua/', 0),
(3, 'lost_mail', 'Уважаемый {%username%},\r\n\r\nВы сделали запрос на получение забытого пароля на сайте https://espo.co.ua/ Однако в целях безопасности все пароли хранятся в зашифрованном виде, поэтому мы не можем сообщить вам ваш старый пароль, поэтому если вы хотите сгенерировать новый пароль, зайдите по следующей ссылке: \r\n\r\n{%lostlink%}\r\n\r\nЕсли вы не делали запроса для получения пароля, то просто удалите данное письмо, ваш пароль храниться в надежном месте, и недоступен посторонним лицам.\r\n\r\nIP адрес отправителя: {%ip%}\r\n\r\nС уважением,\r\n\r\nАдминистрация https://espo.co.ua/', 0),
(4, 'new_news', 'Уважаемый администратор,\r\n\r\nуведомляем вас о том, что на сайт  https://espo.co.ua/ была добавлена новость, которая в данный момент ожидает модерации.\r\n\r\n------------------------------------------------\r\nКраткая информация о новости\r\n------------------------------------------------\r\n\r\nАвтор: {%username%}\r\nЗаголовок новости: {%title%}\r\nКатегория: {%category%}\r\nДата добавления: {%date%}\r\n\r\nС уважением,\r\n\r\nАдминистрация https://espo.co.ua/', 0),
(5, 'comments', 'Уважаемый {%username_to%},\r\n\r\nуведомляем вас о том, что на сайт  https://espo.co.ua/ был добавлен комментарий к новости, на которую вы были подписаны.\r\n\r\n------------------------------------------------\r\nКраткая информация о комментарии\r\n------------------------------------------------\r\n\r\nАвтор: {%username%}\r\nДата добавления: {%date%}\r\nСсылка на новость: {%link%}\r\n\r\n------------------------------------------------\r\nТекст комментария\r\n------------------------------------------------\r\n\r\n{%text%}\r\n\r\n------------------------------------------------\r\n\r\nЕсли вы не хотите больше получать уведомлений о новых комментариях к данной новости, то проследуйте по данной ссылке: {%unsubscribe%}\r\n\r\nС уважением,\r\n\r\nАдминистрация https://espo.co.ua/', 0),
(6, 'pm', 'Уважаемый {%username%},\r\n\r\nуведомляем вас о том, что на сайте  https://espo.co.ua/ вам было отправлено персональное сообщение.\r\n\r\n------------------------------------------------\r\nКраткая информация о сообщении\r\n------------------------------------------------\r\n\r\nОтправитель: {%fromusername%}\r\nДата  получения: {%date%}\r\nЗаголовок: {%title%}\r\n\r\n------------------------------------------------\r\nТекст сообщения\r\n------------------------------------------------\r\n\r\n{%text%}\r\n\r\nС уважением,\r\n\r\nАдминистрация https://espo.co.ua/', 0),
(7, 'wait_mail', 'Уважаемый {%username%},\r\n\r\nВы сделали запрос на обьединение  вашего аккаунта на сайте https://espo.co.ua/ с аккаунтом в социальной сети {%network%}.  Однако в целях безопасности вам необходимо подтвердить данное действие по следующей ссылке: \r\n\r\n------------------------------------------------\r\n{%link%}\r\n------------------------------------------------\r\n\r\nЕсли вы не делали данного запроса, то просто удалите это письмо, данные вашего аккаунта хранятся в надежном месте, и недоступны посторонним лицам.\r\n\r\nIP адрес отправителя: {%ip%}\r\n\r\nС уважением,\r\n\r\nАдминистрация https://espo.co.ua/', 0),
(8, 'newsletter', '<html>\r\n<head>\r\n<title>{%title%}</title>\r\n<meta content=\"text/html; charset={%charset%}\" http-equiv=Content-Type>\r\n<style type=\"text/css\">\r\nhtml,body{\r\n    font-family: Verdana;\r\n    word-spacing: 0.1em;\r\n    letter-spacing: 0;\r\n    line-height: 1.5em;\r\n    font-size: 11px;\r\n}\r\n\r\np {\r\n	margin:0px;\r\n	padding: 0px;\r\n}\r\n\r\na:active,\r\na:visited,\r\na:link {\r\n	color: #4b719e;\r\n	text-decoration:none;\r\n}\r\n\r\na:hover {\r\n	color: #4b719e;\r\n	text-decoration: underline;\r\n}\r\n</style>\r\n</head>\r\n<body>\r\n{%content%}\r\n</body>\r\n</html>', 0),
(9, 'twofactor', '{%username%},\r\n\r\nЭто письмо отправлено с сайта https://espo.co.ua/\r\n\r\nВы получили это письмо, так как для вашего аккаунта включена двухфакторная авторизация. Для авторизации на сайте вам необходимо ввести полученный вами пин-код.\r\n\r\n------------------------------------------------\r\nПин-код:\r\n------------------------------------------------\r\n\r\n{%pin%}\r\n\r\n------------------------------------------------\r\nЕсли Вы не авторизовывались на нашем сайте, то ваш пароль известен посторонним лицам. Вам нужно незамедлительно зайти на сайт под своим логином и паролем, и в своем профиле изменить свой пароль.\r\n\r\nIP пользователя который ввел пароль: {%ip%}\r\n\r\nС уважением,\r\n\r\nАдминистрация https://espo.co.ua/', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `dle_files`
--

CREATE TABLE `dle_files` (
  `id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(250) NOT NULL DEFAULT '',
  `onserver` varchar(250) NOT NULL DEFAULT '',
  `author` varchar(40) NOT NULL DEFAULT '',
  `date` varchar(15) NOT NULL DEFAULT '',
  `dcount` int(11) NOT NULL DEFAULT '0',
  `size` bigint(20) NOT NULL DEFAULT '0',
  `checksum` char(32) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_flood`
--

CREATE TABLE `dle_flood` (
  `f_id` int(11) UNSIGNED NOT NULL,
  `ip` varchar(46) NOT NULL DEFAULT '',
  `id` varchar(20) NOT NULL DEFAULT '',
  `flag` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_ignore_list`
--

CREATE TABLE `dle_ignore_list` (
  `id` int(10) UNSIGNED NOT NULL,
  `user` int(11) NOT NULL DEFAULT '0',
  `user_from` varchar(40) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_images`
--

CREATE TABLE `dle_images` (
  `id` int(10) UNSIGNED NOT NULL,
  `images` text NOT NULL,
  `news_id` int(10) NOT NULL DEFAULT '0',
  `author` varchar(40) NOT NULL DEFAULT '',
  `date` varchar(15) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `dle_images`
--

INSERT INTO `dle_images` (`id`, `images`, `news_id`, `author`, `date`) VALUES
(1, '2020-05/1589930870_1589930836217.png', 5, 'ADMIN', '1589930850');

-- --------------------------------------------------------

--
-- Структура таблицы `dle_links`
--

CREATE TABLE `dle_links` (
  `id` int(11) UNSIGNED NOT NULL,
  `word` varchar(255) NOT NULL DEFAULT '',
  `link` varchar(255) NOT NULL DEFAULT '',
  `only_one` tinyint(1) NOT NULL DEFAULT '0',
  `replacearea` tinyint(1) NOT NULL DEFAULT '1',
  `rcount` tinyint(3) NOT NULL DEFAULT '0',
  `targetblank` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_login_log`
--

CREATE TABLE `dle_login_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip` varchar(46) NOT NULL DEFAULT '',
  `count` smallint(6) NOT NULL DEFAULT '0',
  `date` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `dle_login_log`
--

INSERT INTO `dle_login_log` (`id`, `ip`, `count`, `date`) VALUES
(1, '31.133.81.157', 1, 1605307378);

-- --------------------------------------------------------

--
-- Структура таблицы `dle_logs`
--

CREATE TABLE `dle_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `news_id` int(10) NOT NULL DEFAULT '0',
  `member` varchar(40) NOT NULL DEFAULT '',
  `ip` varchar(46) NOT NULL DEFAULT '',
  `rating` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `dle_logs`
--

INSERT INTO `dle_logs` (`id`, `news_id`, `member`, `ip`, `rating`) VALUES
(1, 5, 'noname', '193.110.169.150', 5);

-- --------------------------------------------------------

--
-- Структура таблицы `dle_lostdb`
--

CREATE TABLE `dle_lostdb` (
  `id` mediumint(8) NOT NULL,
  `lostname` mediumint(8) NOT NULL DEFAULT '0',
  `lostid` varchar(40) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_mail_log`
--

CREATE TABLE `dle_mail_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `mail` varchar(50) NOT NULL DEFAULT '',
  `hash` varchar(40) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_metatags`
--

CREATE TABLE `dle_metatags` (
  `id` int(11) UNSIGNED NOT NULL,
  `url` varchar(250) NOT NULL DEFAULT '',
  `title` varchar(200) NOT NULL DEFAULT '',
  `description` varchar(300) NOT NULL DEFAULT '',
  `keywords` text NOT NULL,
  `page_title` varchar(255) NOT NULL DEFAULT '',
  `page_description` text NOT NULL,
  `robots` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_notice`
--

CREATE TABLE `dle_notice` (
  `id` mediumint(8) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `notice` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_plugins`
--

CREATE TABLE `dle_plugins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `icon` varchar(255) NOT NULL DEFAULT '',
  `version` varchar(10) NOT NULL DEFAULT '',
  `dleversion` varchar(10) NOT NULL DEFAULT '',
  `versioncompare` char(2) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `mysqlinstall` text NOT NULL,
  `mysqlupgrade` text NOT NULL,
  `mysqlenable` text NOT NULL,
  `mysqldisable` text NOT NULL,
  `mysqldelete` text NOT NULL,
  `filedelete` tinyint(1) NOT NULL DEFAULT '0',
  `filelist` text NOT NULL,
  `upgradeurl` varchar(255) NOT NULL DEFAULT '',
  `needplugin` varchar(100) NOT NULL DEFAULT '',
  `phpinstall` text NOT NULL,
  `phpupgrade` text NOT NULL,
  `phpenable` text NOT NULL,
  `phpdisable` text NOT NULL,
  `phpdelete` text NOT NULL,
  `notice` text NOT NULL,
  `mnotice` tinyint(1) NOT NULL DEFAULT '0',
  `posi` mediumint(8) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_plugins_files`
--

CREATE TABLE `dle_plugins_files` (
  `id` int(11) NOT NULL,
  `plugin_id` int(11) NOT NULL DEFAULT '0',
  `file` varchar(255) NOT NULL DEFAULT '',
  `action` varchar(10) NOT NULL DEFAULT '',
  `searchcode` text NOT NULL,
  `replacecode` mediumtext NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `searchcount` smallint(6) NOT NULL DEFAULT '0',
  `replacecount` smallint(6) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_plugins_logs`
--

CREATE TABLE `dle_plugins_logs` (
  `id` int(11) NOT NULL,
  `plugin_id` int(11) NOT NULL DEFAULT '0',
  `area` text NOT NULL,
  `error` text NOT NULL,
  `type` varchar(10) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_pm`
--

CREATE TABLE `dle_pm` (
  `id` int(11) UNSIGNED NOT NULL,
  `subj` varchar(255) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `user` mediumint(8) NOT NULL DEFAULT '0',
  `user_from` varchar(40) NOT NULL DEFAULT '',
  `date` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `pm_read` tinyint(1) NOT NULL DEFAULT '0',
  `folder` varchar(10) NOT NULL DEFAULT '',
  `reply` tinyint(1) NOT NULL DEFAULT '0',
  `sendid` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_poll`
--

CREATE TABLE `dle_poll` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `news_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `title` varchar(200) NOT NULL DEFAULT '',
  `frage` varchar(200) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `votes` mediumint(8) NOT NULL DEFAULT '0',
  `multiple` tinyint(1) NOT NULL DEFAULT '0',
  `answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_poll_log`
--

CREATE TABLE `dle_poll_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `news_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `member` varchar(40) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_post`
--

CREATE TABLE `dle_post` (
  `id` int(11) NOT NULL,
  `autor` varchar(40) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `short_story` mediumtext NOT NULL,
  `full_story` mediumtext NOT NULL,
  `xfields` mediumtext NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `descr` varchar(300) NOT NULL DEFAULT '',
  `keywords` text NOT NULL,
  `category` varchar(190) NOT NULL DEFAULT '0',
  `alt_name` varchar(190) NOT NULL DEFAULT '',
  `comm_num` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `allow_comm` tinyint(1) NOT NULL DEFAULT '1',
  `allow_main` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `approve` tinyint(1) NOT NULL DEFAULT '0',
  `fixed` tinyint(1) NOT NULL DEFAULT '0',
  `allow_br` tinyint(1) NOT NULL DEFAULT '1',
  `symbol` varchar(3) NOT NULL DEFAULT '',
  `tags` varchar(255) NOT NULL DEFAULT '',
  `metatitle` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `dle_post`
--

INSERT INTO `dle_post` (`id`, `autor`, `date`, `short_story`, `full_story`, `xfields`, `title`, `descr`, `keywords`, `category`, `alt_name`, `comm_num`, `allow_comm`, `allow_main`, `approve`, `fixed`, `allow_br`, `symbol`, `tags`, `metatitle`) VALUES
(4, 'ADMIN', '2020-05-20 02:22:55', '<img src=\\\"https://www.kpi.kharkov.ua/rus/wp-content/uploads/sites/3/2020/01/SHA_1037-1024x683.jpg\\\" class=\\\"fr-fic fr-dib\\\" alt=\\\"\\\"><b>17 января 2020г. в общежитии «Гигант» НТУ «Харьковский политехнический институт» (</b><b>I</b> <b>и </b><b>II</b><b> секции) прошли противопожарные учения. Цель — отработка тактики тушения пожаров, проведения аварийно-спасательных работ в месте возникновения пожара, а также отработка действий администрации вуза по эвакуации людей с места чрезвычайной ситуации. В учениях от ГСЧС Харьковской области было задействовано 2 пожарных автоцистерны, 1 пожарная автолестница и 13 человек личного состава. Из общежития эвакуировали около 100 студентов.</b><br> «Это были общие учения, как сотрудников Государственной службы чрезвычайных ситуаций, так и работников общежития, руководства ХПИ — отрабатывали совместные действия по эвакуации людей и тушению условного пожара. Главное было провести своевременную эвакуацию всех жильцов, посмотреть на работу вахтеров, руководства общежития. По легенде, возгорание происходило в комнате на 3-м этаже. Отрезанный от эвакуационных выходов, в дыму остался один человек, которого мы спасали.  Также проводилась эвакуация и проверка всех комнат, которые находятся рядом с очагом пожара», — сообщил начальник пожарно-спасательной части № 9 г. Харькова Дмитрий Завирюха.<br><br>', '<img src=\\\"https://www.kpi.kharkov.ua/rus/wp-content/uploads/sites/3/2020/01/SHA_1037-1024x683.jpg\\\" class=\\\"fr-fic fr-dii\\\" alt=\\\"\\\"><br><br><b>17 января 2020г. в общежитии «Гигант» НТУ «Харьковский политехнический институт» (</b><b>I</b> <b>и </b><b>II</b><b> секции) прошли противопожарные учения. Цель — отработка тактики тушения пожаров, проведения аварийно-спасательных работ в месте возникновения пожара, а также отработка действий администрации вуза по эвакуации людей с места чрезвычайной ситуации. В учениях от ГСЧС Харьковской области было задействовано 2 пожарных автоцистерны, 1 пожарная автолестница и 13 человек личного состава. Из общежития эвакуировали около 100 студентов.</b><br> «Это были общие учения, как сотрудников Государственной службы чрезвычайных ситуаций, так и работников общежития, руководства ХПИ — отрабатывали совместные действия по эвакуации людей и тушению условного пожара. Главное было провести своевременную эвакуацию всех жильцов, посмотреть на работу вахтеров, руководства общежития. По легенде, возгорание происходило в комнате на 3-м этаже. Отрезанный от эвакуационных выходов, в дыму остался один человек, которого мы спасали.  Также проводилась эвакуация и проверка всех комнат, которые находятся рядом с очагом пожара», — сообщил начальник пожарно-спасательной части № 9 г. Харькова Дмитрий Завирюха.<br><br>По словам проректора ХПИ Магомедэмина Гасанова, учения проводились в том числе для того, чтобы студенты, которые живут в общежитии, при возникновении пожара знали, что им делать в такой ситуации, как поступать, чтобы спасти свою жизнь. «Жизнь человека — это самое важное на сегодняшний день. Сегодня мы имитировали возникновение пожара в общежитии «Гигант», позвонили в дежурную часть. Пожарные службы включили сигнал оповещения о том, что в «Гиганте» пожар. Общежитие расположено в центре города (ул. Пушкинская, 79/1), поэтому это важный объект, где проживают много наших студентов. Мы студентов не предупреждали, для того чтобы посмотреть, как это сработает. Все произошло в реальном времени, и студенты выбегали на улицу. По итогу, пожар потушен, все эвакуированы, все получилось нормально», — рассказал Магомедэмин Гасанов. Он также отметил, что такие учение будут проходить и в дальнейшем. На очереди — одно из общежитий ХПИ на Алексеевке. Отметим, что после трагедии в Одесском колледже во всех городах Украины усиливают противопожарную безопасность и проводят специальные учения.<br>Напомним, в мае 2019 года противопожарные учения проводились на территории кампуса НТУ «ХПИ», в высотном учебном корпусе У1.<br><br><img src=\\\"https://www.kpi.kharkov.ua/rus/wp-content/uploads/sites/3/2020/01/SHA_1067-590x394.jpg\\\" class=\\\"fr-fic fr-dii\\\" alt=\\\"\\\"><img src=\\\"https://www.kpi.kharkov.ua/rus/wp-content/uploads/sites/3/2020/01/SHA_1063-292x195.jpg\\\" class=\\\"fr-fic fr-dii\\\" alt=\\\"\\\"><img src=\\\"https://www.kpi.kharkov.ua/rus/wp-content/uploads/sites/3/2020/01/SHA_1078-507x759.jpg\\\" class=\\\"fr-fic fr-dii\\\" alt=\\\"\\\">', '', '17 января 2020г. в общежитии «Гигант» НТУ «ХПИ» (I и II секции) прошли противопожарные учения.', '17 января 2020г. в общежитии «Гигант» НТУ «Харьковский политехнический институт» (I и II секции) прошли противопожарные учения. Цель — отработка тактики тушения пожаров, проведения аварийно-спасательных работ в месте возникновения пожара, а также отработка действий администрации вуза по эвакуации', 'учения, пожара, общежития, руководства, студентов, эвакуации, людей, отработка, общежитии, человек, противопожарные, чтобы, также, посмотреть, ситуации, службы, которые, «Гигант», эвакуационных, Отрезанный', '1', '17-janvarja-2020g-v-obschezhitii-gigant-ntu-hpi-i-i-ii-sekcii-proshli-protivopozharnye-uchenija', 0, 1, 1, 1, 0, 0, '', '', ''),
(5, 'ADMIN', '2020-05-20 02:28:09', '<a href=\\\"https://espo.co.ua/uploads/posts/2020-05/1589930870_1589930836217.png\\\"><img src=\\\"https://espo.co.ua/uploads/posts/2020-05/medium/1589930870_1589930836217.png\\\" class=\\\"fr-fic fr-dib\\\" alt=\\\"\\\"></a>⚡️Перемога в суді: ХПІ має знизити ціну за проживання в гуртожитках під час літніх канікул<br><br>Щойно Другий апеляційний суд підтвердив законність рішень Держпродспоживслужби, яка встановила, що Політех завищив ціни для студентів та зобов\\\'язала знизити їх, адже відповідно до наказу Міносвіти, ціна на проживання для студентів не може перевищувати 40% від стипендії, тобто 520 ₴.<br><br>У вересні минулого року ХПІ подав до суду, щоб скасувати ці рішення, і в січні виграв суд (https://t.me/c/1324999204/614). Проте Держспоживслужба не погодилася з рішенням, і сьогодні суд апеляційної інстанції підтримав її позицію.<br><br>', '<a class=\\\"highslide\\\" href=\\\"https://espo.co.ua/uploads/posts/2020-05/1589930870_1589930836217.png\\\"><img src=\\\"https://espo.co.ua/uploads/posts/2020-05/medium/1589930870_1589930836217.png\\\" class=\\\"fr-fic fr-dib\\\" alt=\\\"\\\"></a>⚡️Перемога в суді: ХПІ має знизити ціну за проживання в гуртожитках під час літніх канікул<br><br>Щойно Другий апеляційний суд підтвердив законність рішень Держпродспоживслужби, яка встановила, що Політех завищив ціни для студентів та зобов\\\'язала знизити їх, адже відповідно до наказу Міносвіти, ціна на проживання для студентів не може перевищувати 40% від стипендії, тобто 520 ₴.<br><br>У вересні минулого року ХПІ подав до суду, щоб скасувати ці рішення, і в січні виграв суд (https://t.me/c/1324999204/614). Проте Держспоживслужба не погодилася з рішенням, і сьогодні суд апеляційної інстанції підтримав її позицію.<br><br>Ми вже неодноразово писали (https://t.me/c/1324999204/489) про цю справу та закликали (https://t.me/c/1324999204/506) приходити на суди.<br><br>Суддя апеляційного суду намагалася дізнатися в адвокатки ХПІ, чому протягом двох місяців на рік студенти стають для університету «сторонніми особами» та мають платити за проживання більше, ніж дозволяє Міністерство освіти. Чіткої відповіді почути не вдалося.<br><br>Студентів ХПІ в суді представляв учасник KHPI Rights Alliance (https://t.me/joinchat/AAAAAFQxeGwEXwkK3jXr6Q) Денис Волоха (http://t.me/volokha) (на фото), якому Політех тепер має повернути 657 гривень, які він переплатив за проживання в гуртожитку минулого літа.<br><br>Ви теж жили в гуртожитку протягом липня-серпня та заплатили більш як 520 гривень за місяць? Напишіть @v_means_vendettat і ми допоможемо вам подати скаргу, щоб повернути переплату.', '', '⚡️Перемога в суді: ХПІ має знизити ціну за проживання в гуртожитках під час літніх канікул', '⚡️Перемога в суді: ХПІ має знизити ціну за проживання в гуртожитках під час літніх канікул Щойно Другий апеляційний суд підтвердив законність рішень Держпродспоживслужби, яка встановила, що Політех завищив ціни для студентів та зобов\'язала знизити їх, адже відповідно до наказу Міносвіти, ціна на', 'проживання, https, знизити, 1324999204, студентів, Політех, минулого, ⚡️Перемога, скасувати, рішення, січні, виграв, Проте, погодилася, сьогодні, рішенням, апеляційної, інстанції, підтримав, позицію', '1', 'peremoga-v-sudi-hpi-maye-zniziti-cinu-za-prozhivannja-v-gurtozhitkah-pid-chas-litnih-kanikul', 1, 1, 1, 1, 0, 0, '', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `dle_post_extras`
--

CREATE TABLE `dle_post_extras` (
  `eid` int(11) NOT NULL,
  `news_id` int(11) NOT NULL DEFAULT '0',
  `news_read` int(11) NOT NULL DEFAULT '0',
  `allow_rate` tinyint(1) NOT NULL DEFAULT '1',
  `rating` int(11) NOT NULL DEFAULT '0',
  `vote_num` int(11) NOT NULL DEFAULT '0',
  `votes` tinyint(1) NOT NULL DEFAULT '0',
  `view_edit` tinyint(1) NOT NULL DEFAULT '0',
  `disable_index` tinyint(1) NOT NULL DEFAULT '0',
  `related_ids` varchar(255) NOT NULL DEFAULT '',
  `access` varchar(150) NOT NULL DEFAULT '',
  `editdate` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `editor` varchar(40) NOT NULL DEFAULT '',
  `reason` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `disable_search` tinyint(1) NOT NULL DEFAULT '0',
  `need_pass` tinyint(1) NOT NULL DEFAULT '0',
  `allow_rss` tinyint(1) NOT NULL DEFAULT '1',
  `allow_rss_turbo` tinyint(1) NOT NULL DEFAULT '1',
  `allow_rss_dzen` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `dle_post_extras`
--

INSERT INTO `dle_post_extras` (`eid`, `news_id`, `news_read`, `allow_rate`, `rating`, `vote_num`, `votes`, `view_edit`, `disable_index`, `related_ids`, `access`, `editdate`, `editor`, `reason`, `user_id`, `disable_search`, `need_pass`, `allow_rss`, `allow_rss_turbo`, `allow_rss_dzen`) VALUES
(4, 4, 15, 1, 0, 0, 0, 0, 0, '', '', 1589930635, 'ADMIN', '', 1, 0, 0, 1, 1, 1),
(5, 5, 44, 1, 5, 1, 0, 0, 0, '4', '', 0, '', '', 1, 0, 0, 1, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `dle_post_extras_cats`
--

CREATE TABLE `dle_post_extras_cats` (
  `id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL DEFAULT '0',
  `cat_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `dle_post_extras_cats`
--

INSERT INTO `dle_post_extras_cats` (`id`, `news_id`, `cat_id`) VALUES
(4, 4, 1),
(5, 5, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `dle_post_log`
--

CREATE TABLE `dle_post_log` (
  `id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL DEFAULT '0',
  `expires` varchar(15) NOT NULL DEFAULT '',
  `action` tinyint(1) NOT NULL DEFAULT '0',
  `move_cat` varchar(190) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_post_pass`
--

CREATE TABLE `dle_post_pass` (
  `id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL DEFAULT '0',
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_question`
--

CREATE TABLE `dle_question` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL DEFAULT '',
  `answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_read_log`
--

CREATE TABLE `dle_read_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `news_id` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(46) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_redirects`
--

CREATE TABLE `dle_redirects` (
  `id` int(11) UNSIGNED NOT NULL,
  `from` varchar(250) NOT NULL DEFAULT '',
  `to` varchar(250) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_rss`
--

CREATE TABLE `dle_rss` (
  `id` smallint(5) NOT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `allow_main` tinyint(1) NOT NULL DEFAULT '0',
  `allow_rating` tinyint(1) NOT NULL DEFAULT '0',
  `allow_comm` tinyint(1) NOT NULL DEFAULT '0',
  `text_type` tinyint(1) NOT NULL DEFAULT '0',
  `date` tinyint(1) NOT NULL DEFAULT '0',
  `search` text NOT NULL,
  `max_news` tinyint(3) NOT NULL DEFAULT '0',
  `cookie` text NOT NULL,
  `category` smallint(5) NOT NULL DEFAULT '0',
  `lastdate` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `dle_rss`
--

INSERT INTO `dle_rss` (`id`, `url`, `description`, `allow_main`, `allow_rating`, `allow_comm`, `text_type`, `date`, `search`, `max_news`, `cookie`, `category`, `lastdate`) VALUES
(1, 'https://dle-news.ru/rss.xml', 'Официальный сайт DataLife Engine', 1, 1, 1, 1, 1, '<div class=\"full-post-content row\">{get}</div><div class=\"full-post-footer ignore-select\">', 5, '', 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `dle_rssinform`
--

CREATE TABLE `dle_rssinform` (
  `id` smallint(5) NOT NULL,
  `tag` varchar(40) NOT NULL DEFAULT '',
  `descr` varchar(255) NOT NULL DEFAULT '',
  `category` varchar(200) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `template` varchar(40) NOT NULL DEFAULT '',
  `news_max` smallint(5) NOT NULL DEFAULT '0',
  `tmax` smallint(5) NOT NULL DEFAULT '0',
  `dmax` smallint(5) NOT NULL DEFAULT '0',
  `approve` tinyint(1) NOT NULL DEFAULT '1',
  `rss_date_format` varchar(20) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `dle_rssinform`
--

INSERT INTO `dle_rssinform` (`id`, `tag`, `descr`, `category`, `url`, `template`, `news_max`, `tmax`, `dmax`, `approve`, `rss_date_format`) VALUES
(1, 'dle', 'Новости с Яндекса', '0', 'https://news.yandex.ru/index.rss', 'informer', 3, 0, 200, 1, 'j F Y H:i');

-- --------------------------------------------------------

--
-- Структура таблицы `dle_sendlog`
--

CREATE TABLE `dle_sendlog` (
  `id` int(11) UNSIGNED NOT NULL,
  `user` varchar(40) NOT NULL DEFAULT '',
  `date` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `flag` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_social_login`
--

CREATE TABLE `dle_social_login` (
  `id` int(11) NOT NULL,
  `sid` varchar(40) NOT NULL DEFAULT '',
  `uid` int(11) NOT NULL DEFAULT '0',
  `password` varchar(32) NOT NULL DEFAULT '',
  `provider` varchar(15) NOT NULL DEFAULT '',
  `wait` tinyint(1) NOT NULL DEFAULT '0',
  `waitlogin` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_spam_log`
--

CREATE TABLE `dle_spam_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip` varchar(46) NOT NULL DEFAULT '',
  `is_spammer` tinyint(1) NOT NULL DEFAULT '0',
  `email` varchar(50) NOT NULL DEFAULT '',
  `date` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_static`
--

CREATE TABLE `dle_static` (
  `id` mediumint(8) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `descr` varchar(255) NOT NULL DEFAULT '',
  `template` mediumtext NOT NULL,
  `allow_br` tinyint(1) NOT NULL DEFAULT '0',
  `allow_template` tinyint(1) NOT NULL DEFAULT '0',
  `grouplevel` varchar(100) NOT NULL DEFAULT 'all',
  `tpl` varchar(40) NOT NULL DEFAULT '',
  `metadescr` varchar(300) NOT NULL DEFAULT '',
  `metakeys` text NOT NULL,
  `views` mediumint(8) NOT NULL DEFAULT '0',
  `template_folder` varchar(50) NOT NULL DEFAULT '',
  `date` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `metatitle` varchar(255) NOT NULL DEFAULT '',
  `allow_count` tinyint(1) NOT NULL DEFAULT '1',
  `sitemap` tinyint(1) NOT NULL DEFAULT '1',
  `disable_index` tinyint(1) NOT NULL DEFAULT '0',
  `disable_search` tinyint(1) NOT NULL DEFAULT '0',
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `dle_static`
--

INSERT INTO `dle_static` (`id`, `name`, `descr`, `template`, `allow_br`, `allow_template`, `grouplevel`, `tpl`, `metadescr`, `metakeys`, `views`, `template_folder`, `date`, `metatitle`, `allow_count`, `sitemap`, `disable_index`, `disable_search`, `password`) VALUES
(1, 'dle-rules-page', 'Общие правила на сайте', '<b>Общие правила поведения на сайте:</b><br><br>Начнем с того, что на сайте общаются сотни людей, разных религий и взглядов, и все они являются полноправными посетителями нашего сайта, поэтому если мы хотим чтобы это сообщество людей функционировало нам и необходимы правила. Мы настоятельно рекомендуем прочитать настоящие правила, это займет у вас всего минут пять, но сбережет нам и вам время и поможет сделать сайт более интересным и организованным.<br><br>Начнем с того, что на нашем сайте нужно вести себя уважительно ко всем посетителям сайта. Не надо оскорблений по отношению к участникам, это всегда лишнее. Если есть претензии - обращайтесь к Админам или Модераторам (воспользуйтесь личными сообщениями). Оскорбление других посетителей считается у нас одним из самых тяжких нарушений и строго наказывается администрацией. <b>У нас строго запрещен расизм, религиозные и политические высказывания.</b> Заранее благодарим вас за понимание и за желание сделать наш сайт более вежливым и дружелюбным.<br><br><b>На сайте строго запрещено:</b> <br><br>- сообщения, не относящиеся к содержанию статьи или к контексту обсуждения<br>- оскорбление и угрозы в адрес посетителей сайта<br>- в комментариях запрещаются выражения, содержащие ненормативную лексику, унижающие человеческое достоинство, разжигающие межнациональную рознь<br>- спам, а также реклама любых товаров и услуг, иных ресурсов, СМИ или событий, не относящихся к контексту обсуждения статьи<br><br>Давайте будем уважать друг друга и сайт, на который Вы и другие читатели приходят пообщаться и высказать свои мысли. Администрация сайта оставляет за собой право удалять комментарии или часть комментариев, если они не соответствуют данным требованиям.<br><br>При нарушении правил вам может быть дано <b>предупреждение</b>. В некоторых случаях может быть дан бан <b>без предупреждений</b>. По вопросам снятия бана писать администратору.<br><br><b>Оскорбление</b> администраторов или модераторов также караются <b>баном</b> - уважайте чужой труд.<br><br><div style=\"text-align:center;\">{ACCEPT-DECLINE}</div>', 1, 1, 'all', '', 'Общие правила', 'Общие правила', 12, '', 1589592632, '', 1, 1, 0, 0, ''),
(4, 'hostels', 'Каталог гуртожитків', '<table class=\\\"fr-solid-borders\\\"><tbody><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><b>№ </b><b>гуртожитку</b></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\"><b> <b>Прізвище ім’я по батькові</b></b></td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\"><p><b>Посада</b></p></td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\"><p><b>№ телефон</b><b>у</b></p></td><td style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\"><p><b>І</b><b>ндекс</b></p></td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><b> </b><br><b><b>Адрес</b><b>а</b></b><br><br></td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-1.html\\\"><b>Гуртожиток 1</b></a><b>  </b><br><b>(1-5</b><b> секції</b><b>)</b><br><br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\"><span class=\\\"xfm_30658132\\\"><span lang=\\\"ru\\\"><span lang=\\\"uk\\\">Болквадзе Мері Шукріївна</span></span></span></td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Комендант</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">707-63-89</td><td rowspan=\\\"4\\\" style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\"> <p>61024</p></td><td rowspan=\\\"4\\\" style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"> <p>вул. Пушкінська 79/1</p></td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><p><a href=\\\"https://espo.co.ua/hostels-1.html\\\"><b>Гуртожиток  1 </b></a></p><p><b>(6-9 </b><b>секції</b><b>)</b></p></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\"> <p><br></p><p>Корнієнко Ольга Миколаївна</p></td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\"> <p><br></p><p>Зав. гуртожитком</p></td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">707-61-05<p><br></p><p>707-68-13</p><p><sup>(</sup><sup>черговий гуртожитку</sup><sup>)</sup></p></td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-1.html\\\"><b>Гуртожиток 1 </b></a><br><b>(10 </b><b>секція</b><b>)</b><br><br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\">Лузан Світлана Вікторівна</td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Комендант</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">707-61-54</td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><b>Профілакторій</b><br><br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\">Гервольский Дмитро Михайлович</td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Комендант</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">707-65-31</td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-2.html\\\"><b>Гуртожиток  2</b></a><br><br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\">Капанадзе Нанулі Павлівна</td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Зав. гуртожитком</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">728-92-29</td><td style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\">61054</td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\">вул. Зубенко 9 А</td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-3.html\\\"><b>Гуртожиток  3</b></a><br>(для іноземних студентів)</td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\">Чен Надія Іннокентіївна</td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Зав. гуртожитком</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">336-77-47</td><td style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\">61202</td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\">вул. Целіноградська 56</td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-4.html\\\"><b>Гуртожиток  4</b></a><br>(для іноземних студентів)<br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\">Болдирєва Ольга Вікторівна</td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Зав. гуртожитком</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">707-66-31</td><td rowspan=\\\"2\\\" style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\"> <p><br></p><p>61024</p></td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\">вул. Пушкінська 79/4</td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-5.html\\\"><b>Гуртожиток  5</b></a><br><br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\"><span class=\\\"xfm_30658132\\\"><span lang=\\\"ru\\\"><span lang=\\\"uk\\\">Наливайко Галина Володимирівна</span></span></span></td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Зав. гуртожитком</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">707-61-13</td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\">вул. Пушкінська 79/5</td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-6.html\\\"><b>Гуртожиток 6</b></a><br>(для іноземних студентів)<br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\">Олійник Віра Романівна</td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Комендант</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">707-61-90</td><td style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\">61047</td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\">Студентська 15/17</td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-7.html\\\"><b>Гуртожиток  7</b></a><br><br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\">Саніна Зоя Федорівна</td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Зав. гуртожитком</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">336-77-31</td><td style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\">61202</td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\">вул. Преможна 19</td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-8.html\\\"><b>Гуртожиток  8</b></a><br><br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\">Гончарук Наталя Леонідівна</td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Комендант</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">705-14-99</td><td style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\">61058</td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\">вул. Літературна 3</td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-9.html\\\"><b>Гуртожиток  9</b></a><br><br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\">Аліханов Ельхан Султанмурадович</td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Зав. гуртожитком</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">336-18-98</td><td style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\">61202</td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\">вул. Цілиноградська  38</td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-10.html\\\"><b>Гуртожиток 10</b></a><br>(для іноземних студентів)<br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\">Алтинцева Наталя Михайлівна</td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Зав. гуртожитком</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">704-16-14</td><td style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\">61023</td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\">Провулок Дизайнерський 4</td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-11.html\\\"><b>Гуртожиток 11</b></a><br><br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\">Зюзьгін Геннадій Валеріанович</td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Зав. гуртожитком</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">336-75-38</td><td style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\">61202</td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\">вул. Целіноградська  48</td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-12.html\\\"><b>Гуртожиток 12</b></a><br><br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\">Щетковська Людмила Василівна</td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Комендант</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">336-80-56</td><td style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\">61202</td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\">вул. Целіноградська 32</td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-13.html\\\"><b>Гуртожиток 13</b></a><br><br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\">Пономаренко Лідія Яківна</td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Зав. гуртожитком</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">340-92-45</td><td style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\">61045</td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\">вул. Клочківска 218 А</td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-14.html\\\"><b>Гуртожиток 14</b></a><br><br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\">Занько Алла Вікторівна</td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Зав. гуртожитком</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">738-14-92</td><td style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\">61110</td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\">Фрунзенський район<p><br></p><p>Балканська 19</p></td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-15.html\\\"><b>Гуртожиток 15</b></a><br><br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\">Федерякіна Катерина Анатоліївна</td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Зав. гуртожитком</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">762-89-90</td><td style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\">61038</td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><p>вул. Спортивна 9</p></td></tr></tbody></table>', 0, 1, 'all', 'static', 'Перелик гуртожиткив № гуртожитку Прізвище ім’я по батькові Посада № телефону Індекс АдресаГуртожиток 1 (1-5 секції)Болквадзе Мері ШукріївнаКомендант707-63-89 61024 вул. Пушкінська 79/1 Гуртожиток 1 (6-9 секції) Корнієнко Ольга Миколаївна Зав. гуртожитком707-61-05', 'Целіноградська, гуртожитком336, гуртожитком707, Пушкінська, район, ВікторівнаЗав, Ольга, Гуртожиток, 19Гуртожиток, 4Гуртожиток, Наталя, АГуртожиток, гуртожитку, секції, гуртожитком704, 1461023Провулок, МихайлівнаЗав, Дизайнерський, 10Алтинцева, СултанмурадовичЗав', 209, '', 1589839200, '', 1, 1, 0, 0, ''),
(5, 'hostels-1', 'Гуртожиток №1', '<b>Студентське містечко «Гігант» (гуртожиток студентів політехнічного інституту). Десять пов\\\'язаних між собою п\\\'яти і шести-поверхових секцій побудовані в 1928-1931рр. (архітектори професор А. Г. Молокіні, Г. Д. Іконніков). Зовнішній вигляд змінений при відновленні будівлі після війни.</b><br><br><a class=\\\"highslide\\\" href=\\\"https://espo.co.ua/uploads/posts/2020-05/1589845240_hostel-1.jpg\\\"><img src=\\\"https://espo.co.ua/uploads/posts/2020-05/medium/1589845240_hostel-1.jpg\\\" class=\\\"fr-fic fr-dib fr-padded fr-rounded\\\" alt=\\\"Гуртожиток №1, гігант\\\" style=\\\"width:664px;\\\"></a><br>В гуртожитку розташована дирекція студмістечка, медичний пункт, медичний центр. Гуртожиток розбитий на групу секцій, що мають окремий вхід та управління. Кухні, туалети та умивальні кімнати знаходяться на кожному поверсі, душові – на 1-му поверсі. Є спортивні кімнати, кімнати для занять та відпочинку. Встановлено відеонагляд входу в гуртожиток та на поверхах.<br><br><b>Базові факультети для проживання</b> – КН, СГТ.<br><b>Додаткові побутові послуги</b> – є пральня.<br><b>Адреса:</b> вул. Пушкінська 79/1.', 0, 1, 'all', 'hostel-1', 'Студентське містечко «Гігант» (гуртожиток студентів політехнічного інституту). Десять пов\'язаних між собою п\'яти і шести-поверхових секцій побудовані в 1928-1931рр. (архітектори професор А. Г. Молокіні, Г. Д. Іконніков). Зовнішній вигляд змінений при відновленні будівлі після війни. В гуртожитку', 'кімнати, медичний, гуртожиток, поверсі, секцій, Студентське, умивальні, кожному, знаходяться, Кухні, туалети, спортивні, управління, окремий, мають, групу, душові, відпочинку, занять, Гуртожиток', 167, '', 1591843240, '', 1, 1, 0, 0, ''),
(6, 'hostels-1-map', 'Маршрут проїзду', '<div class=\\\"googlemaps\\\"><iframe width=\\\"100%\\\" height=\\\"500\\\" frameborder=\\\"0\\\" scrolling=\\\"no\\\" marginheight=\\\"0\\\" marginwidth=\\\"0\\\" src=\\\"https://www.google.com/maps/embed?pb=!1m24!1m8!1m3!1d5128.896432088194!2d36.2472939!3d50.0029537!3m2!1i1024!2i768!4f13.1!4m13!3e3!4m5!1s0x4127a0e98e8e2a0d:0xfa916cfdbe03ae0a!2z0KPRh9C10LHQvdGL0Lkg0LouIOKEliAyINCd0KLQoyAi0KXQn9CYIiwg0LLRg9C70LjRhtGPINCa0LjRgNC_0LjRh9C-0LLQsCwgMiwg0KXQsNGA0LrRltCyLCDQpdCw0YDQutGW0LLRgdGM0LrQsCDQvtCx0LvQsNGB0YLRjCwgNjEwMDA!3m2!1d49.9983406!2d36.2478177!4m5!1s0x4127a0db4d2217a7:0x88302ffcb8386ae8!2z0LLRg9C70LjRhtGPINCf0YPRiNC60ZbQvdGB0YzQutCwLCA3OS8xLCDQpdCw0YDQutGW0LIsINCl0LDRgNC60ZbQstGB0YzQutCwINC-0LHQu9Cw0YHRgtGM!3m2!1d50.006206!2d36.249603!5e0!3m2!1suk!2sua!4v1489486584349\\\"></iframe></div>', 0, 1, 'all', 'hostel-1', '', '', 23, '', 1589846452, '', 1, 1, 0, 0, '');
INSERT INTO `dle_static` (`id`, `name`, `descr`, `template`, `allow_br`, `allow_template`, `grouplevel`, `tpl`, `metadescr`, `metakeys`, `views`, `template_folder`, `date`, `metatitle`, `allow_count`, `sitemap`, `disable_index`, `disable_search`, `password`) VALUES
(7, 'rent', 'Оплата за проживання', '<table border=\\\"0\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"border-collapse:collapse;width:97%;\\\" width=\\\"636\\\"><tbody><tr><td class=\\\"xl72\\\" colspan=\\\"6\\\" height=\\\"47\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:bottom;border:none;border-top:none;border-right:none;border-bottom:0.5pt solid;border-left:none;height:35.25pt;width:477pt;\\\" width=\\\"100%\\\"><h3 class=\\\"fr-text-bordered\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">Зведена таблиця вартості проживання у гуртожитках  на 2020 рік<br> з 05.02.2020 р.</span></span></h3></td></tr><tr><td class=\\\"xl73\\\" height=\\\"148\\\" rowspan=\\\"2\\\" style=\\\"font-size:16px;font-weight:700;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:0.5pt solid;height:111pt;border-top:none;width:15.0943%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">Гуртожиток</span></span></td><td class=\\\"xl75\\\" colspan=\\\"2\\\" style=\\\"font-size:16px;font-weight:700;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:0.5pt solid;border-right:0.5pt solid #000000;border-bottom:0.5pt solid;border-left:none;width:37.5786%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">Студент денної форми навчання</span></span></td><td class=\\\"xl67\\\" style=\\\"font-size:16px;font-weight:700;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:19.3396%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">Контракт (проживання за договором)</span></span></td><td class=\\\"xl68\\\" style=\\\"font-size:16px;font-weight:700;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:14.7799%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">Студент - заочного відділення</span></span></td><td class=\\\"xl68\\\" style=\\\"font-size:16px;font-weight:700;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:13.522%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">Гість (сторонній за добу)</span></span></td></tr><tr><td class=\\\"xl66\\\" height=\\\"72\\\" style=\\\"font-size:16px;font-weight:700;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:none;border-left:none;height:54pt;width:19.4969%;\\\" width=\\\"21%\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">Собівартість<br>  з 05.02.2020</span></span></td><td class=\\\"xl66\\\" style=\\\"font-size:16px;font-weight:700;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:none;border-left:none;width:18.0818%;\\\" width=\\\"21%\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">До сплати<br> з 05.02.2020</span></span></td><td class=\\\"xl66\\\" style=\\\"font-size:16px;font-weight:700;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:none;border-left:none;width:19.3396%;\\\" width=\\\"21%\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">До сплати<br> з 05.02.2020</span></span></td><td class=\\\"xl66\\\" style=\\\"font-size:16px;font-weight:700;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:none;border-left:none;width:14.7799%;\\\" width=\\\"17%\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">До сплати<br> з 05.02.2020</span></span></td><td class=\\\"xl66\\\" style=\\\"font-size:16px;font-weight:700;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:none;border-left:none;width:13.522%;\\\" width=\\\"17%\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">До сплати<br> з 05.02.2020</span></span></td></tr><tr><td class=\\\"xl69\\\" height=\\\"45\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border:0.5pt solid;height:33.75pt;width:15.0943%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">№ 1</span></span></td><td class=\\\"xl70\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:0.5pt solid;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:19.4969%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">886,30</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:0.5pt solid;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:18.0818%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">520,00</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:0.5pt solid;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:19.3396%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">1 063,56</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:0.5pt solid;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:14.7799%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">29,54</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:0.5pt solid;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:13.522%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">52,14</span></span></td></tr><tr><td class=\\\"xl69\\\" height=\\\"45\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:0.5pt solid;height:33.75pt;border-top:none;width:15.0943%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">№ 2</span></span></td><td class=\\\"xl70\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:19.4969%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">969,14</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:18.0818%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">520,00</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:19.3396%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">1 162,97</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:14.7799%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">32,30</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:13.522%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">55,45</span></span></td></tr><tr><td class=\\\"xl69\\\" height=\\\"45\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:0.5pt solid;height:33.75pt;border-top:none;width:15.0943%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">№ 3</span></span></td><td class=\\\"xl70\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:19.4969%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">1 442,87</span></span></td><td class=\\\"xl70\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:18.0818%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">1 442,87</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:19.3396%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">1 731,44</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:14.7799%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">48,10</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:13.522%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">74,40</span></span></td></tr><tr><td class=\\\"xl69\\\" height=\\\"45\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:0.5pt solid;height:33.75pt;border-top:none;width:15.0943%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">№ 4</span></span></td><td class=\\\"xl70\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:19.4969%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">837,17</span></span></td><td class=\\\"xl70\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:18.0818%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">837,17</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:19.3396%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">1 004,60</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:14.7799%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">27,91</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:13.522%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">50,17</span></span></td></tr><tr><td class=\\\"xl69\\\" height=\\\"45\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:0.5pt solid;height:33.75pt;border-top:none;width:15.0943%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">№ 5</span></span></td><td class=\\\"xl70\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:19.4969%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">780,03</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:18.0818%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">520,00</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:19.3396%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">936,04</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:14.7799%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">26,00</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:13.522%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">47,88</span></span></td></tr><tr><td class=\\\"xl69\\\" height=\\\"45\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:0.5pt solid;height:33.75pt;border-top:none;width:15.0943%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">№ 6</span></span></td><td class=\\\"xl70\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:19.4969%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">921,28</span></span></td><td class=\\\"xl70\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:18.0818%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">921,28</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:19.3396%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">1 105,54</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:14.7799%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">30,71</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:13.522%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">53,53</span></span></td></tr><tr><td class=\\\"xl69\\\" height=\\\"45\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:0.5pt solid;height:33.75pt;border-top:none;width:15.0943%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">№ 7</span></span></td><td class=\\\"xl70\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:19.4969%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">934,91</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:18.0818%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">520,00</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:19.3396%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">1 121,89</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:14.7799%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">31,16</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:13.522%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">54,08</span></span></td></tr><tr><td class=\\\"xl69\\\" height=\\\"45\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:0.5pt solid;height:33.75pt;border-top:none;width:15.0943%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">№ 9</span></span></td><td class=\\\"xl70\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:19.4969%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">837,45</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:18.0818%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">520,00</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:19.3396%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">1 004,94</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:14.7799%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">27,92</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:13.522%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">50,18</span></span></td></tr><tr><td class=\\\"xl69\\\" height=\\\"45\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:0.5pt solid;height:33.75pt;border-top:none;width:15.0943%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">№ 10</span></span></td><td class=\\\"xl70\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:19.4969%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">1 206,06</span></span></td><td class=\\\"xl70\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:18.0818%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">1 206,06</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:19.3396%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">1 447,27</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:14.7799%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">40,20</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:13.522%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">64,93</span></span></td></tr><tr><td class=\\\"xl69\\\" height=\\\"45\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:0.5pt solid;height:33.75pt;border-top:none;width:15.0943%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">№ 11</span></span></td><td class=\\\"xl70\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:19.4969%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">866,78</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:18.0818%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">520,00</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:19.3396%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">1 040,14</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:14.7799%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">28,89</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:13.522%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">51,35</span></span></td></tr><tr><td class=\\\"xl69\\\" height=\\\"45\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:0.5pt solid;height:33.75pt;border-top:none;width:15.0943%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">№ 12</span></span></td><td class=\\\"xl70\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:19.4969%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">787,98</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:18.0818%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">520,00</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:19.3396%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">945,58</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:14.7799%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">26,27</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:13.522%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">48,20</span></span></td></tr><tr><td class=\\\"xl69\\\" height=\\\"45\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:0.5pt solid;height:33.75pt;border-top:none;width:15.0943%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">№ 13</span></span></td><td class=\\\"xl70\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:19.4969%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">806,69</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:18.0818%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">520,00</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:19.3396%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">968,03</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:14.7799%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">26,89</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:13.522%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">48,95</span></span></td></tr><tr><td class=\\\"xl69\\\" height=\\\"45\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:0.5pt solid;height:33.75pt;border-top:none;width:15.0943%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">№ 14</span></span></td><td class=\\\"xl70\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:19.4969%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">880,77</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:18.0818%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">520,00</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:19.3396%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">1 056,92</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:14.7799%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">29,36</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:13.522%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">51,91</span></span></td></tr><tr><td class=\\\"xl69\\\" height=\\\"45\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:0.5pt solid;height:33.75pt;border-top:none;width:15.0943%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">№ 15</span></span></td><td class=\\\"xl70\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-top:none;border-right:0.5pt solid;border-bottom:0.5pt solid;border-left:none;width:19.4969%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">929,38</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:18.0818%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">520,00</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:19.3396%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">1 115,26</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:14.7799%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">30,98</span></span></td><td class=\\\"xl71\\\" style=\\\"font-size:19px;font-weight:400;font-style:normal;text-decoration:none;font-family:\\\'Times New Roman\\\', serif;text-align:center;vertical-align:middle;border-right:0.5pt solid;border-bottom:0.5pt solid;border-top:none;border-left:none;width:13.522%;\\\"><span style=\\\"font-family:GothaPro, Helvetica, sans-serif;\\\"><span style=\\\"color:rgb(0,0,0);\\\">53,86</span></span></td></tr></tbody></table>', 0, 1, 'all', 'static', 'Зведена таблиця вартості проживання у гуртожитках на 2020 рік з 05.02.2020 р.ГуртожитокСтудент денної форми навчанняКонтракт (проживання за договором)Студент - заочного відділенняГість (сторонній за добу)Собівартість з 05.02.2020До сплати з 05.02.2020До сплати з 05.02.2020К оплате з 05.02.2020До', 'сплати, 2020До, проживання, Зведена, 78520, 00936, 28921, 91520, 45520, 11866, 98520, 12787, 17837, 00945, 13806, 69520, 00968, 14880, 77520, 15929', 52, '', 1589846581, '', 1, 1, 0, 0, ''),
(8, 'settlement', 'Порядок поселення', '<h3 style=\\\"text-align:center;\\\"><b>Студенту потрібно</b></h3><h5>У дирекції інституту, деканаті факультету: </h5><ul style=\\\"list-style-type:disc;\\\"><li>Ознайомитися з <a href=\\\"https://espo.co.ua/pologennja.html\\\"><b>«Положенням про студентські гуртожитки НТУ «ХПІ»</b></a>; </li><li>Підписати договір на проживання (встановленої форми). Для неповнолітніх студентів (не виповнилось 18 років) в договорі обов’язково підпис батьків, ; </li><li>Отримати ордер на проживання, (згідно даного наказу комісія по поселенню приймає рішення про зміну місця проживання, студенти поселяються на базові місця факультетів у гуртожитках); </li><li>Отримати 2 довідки встановленого зразку для студентів 1-го курсу (про те, що він є студентом). </li><li>Студентам 1- го курсу отримати квитанцію на оплату за проживання, з урахуванням пільг (комісія по поселенню зобов’язана довести до відома кожного студента наявні пільги після чого виписати квитанцію). </li></ul><br>*Студенти 2-6 курсів отримують квитанцію на оплату у завідуючого гуртожитком, або бухгалтера студмістечка, закріпленого за гуртожитком (обов’язково врахувати заборгованість та переплату, а також при наявності субсидію). Оплата за проживання вноситься за 5 місяців (з 1.09.20xx р. по 31.01.20xx р.) .<br><br><h5 style=\\\"text-align:left;\\\"><b>У гуртожитку, пред\\\'явити:</b></h5><ul style=\\\"list-style-type:disc;\\\"><li>Ордер на поселення; </li><li>Довідку про проходження медогляду:<ol style=\\\"list-style-type:circle;\\\"><li>1 — й курс Інститути/факультети: КІТ, СГТ, Е, І, КН, БЕМ та СТРАШІ КУРСИ УСІХ ФАКУЛЬТЕТІВ. Центр первинної медико - санітарної допомоги, розташований за адресою: вул. Пушкинская, 79/1 секція 3</li><li>1 — й курс факультетів: ХТ,МІТ, - гуртожиток № 9, розташований за адресою: вул. Цілиноградська, 38</li></ol></li><li>Квитанцію про сплату за проживання (оплата через банк) (для студентів 1-го курсу);</li><li>Довідка про зняття з попереднього місця проживання.</li><li>Здати ID-картку/паспорт та оригінал витягу з Єдиного державного демографічного реєстру щодо реєстрації місця проживання на реєстрацію, до подачі документів на реєстрацію студентам 1-го курсу та тим, що поселяються вперше, заповнити картку особистого обліку та здати її паспортисту;</li><li>Військовий документ для військовозобов\\\'язаних (знятим з військового обліку);</li><li>Пройти відповідні інструктажі під підпис у журналі;</li><li>Отримати квитанцію на сплату за проживання з урахуванням боргів та переплат, якщо такі є (для студентів 2-6 курсів).*</li><li>Військовозобов\\\'язаним стати на військовий облік: - 1– му курсу в районному військкоматі та у другому відділі університету - страшим курсам у другому відділі університету .</li><li>Оформити у завідуючого гуртожитком: <ol style=\\\"list-style-type:circle;\\\"><li>Особисту картку </li><li>Арматурну картку </li><li>Перепустку у гуртожиток </li><li>При собі мати 3 фотокартки розміром 3×4 см</li></ol></li></ul><br>* Прим. Для студентів 2-6 курсу обов’язково врахувати наявні в них борги, або переплати та скорегувати відповідно суму оплати за проживання, а також наявність пільги. Студент - боржник не поселяється у гуртожиток до погашення боргу за проживання в попередній період. Довідки для оформлення субсидій надають після повного поселення, сплати за проживання, відсутності заборгованості.<br><br><div style=\\\"text-align:center;\\\"><span class=\\\"fr-video fr-dvb fr-draggable\\\" contenteditable=\\\"false\\\"><iframe src=\\\"//player.vimeo.com/video/72771595\\\" frameborder=\\\"0\\\" allowfullscreen style=\\\"width:660px;height:368px;\\\"></iframe></span></div>', 0, 1, 'all', '', 'iframe width=560 height=315 src=https://www.youtube.com/embed/8zMmi9AypaU frameborder=0 allow=accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture allowfullscreen/iframe', 'picture, iframe, width, height, https, youtube, embed, 8zMmi9AypaU, frameborder, allow, accelerometer, autoplay, encrypted, media, gyroscope, allowfullscreen', 159, '', 1590416655, '', 1, 1, 0, 0, '');
INSERT INTO `dle_static` (`id`, `name`, `descr`, `template`, `allow_br`, `allow_template`, `grouplevel`, `tpl`, `metadescr`, `metakeys`, `views`, `template_folder`, `date`, `metatitle`, `allow_count`, `sitemap`, `disable_index`, `disable_search`, `password`) VALUES
(9, 'abiturientu', 'Абітурієнту', '<h4 style=\\\"text-align:center;\\\"><a href=\\\"https://espo.co.ua/hostels.html\\\">Каталог гуртожитків</a>   <a href=\\\"https://espo.co.ua/settlement.html\\\">Порядок поселення</a>   <a href=\\\"https://espo.co.ua/subsidies.html\\\">Субсидія</a></h4><hr><p style=\\\"text-align:left;\\\">В підпорядкуванні студмістечка знаходяться  15 гуртожитків загальною житловою площею 41272 м.кв на 6400 ліжко-місць, з яких 5 блочного, 9 коридорного та 1 сімейного типу .<br><br>Чотири гуртожитки призначені для компактного проживання іноземних студентів та один для проживання аспірантів. Найближче до університету, по вулиці Пушкінська, 79 , розташований комплекс споруд, який включає в себе гуртожитки № 1, № 4, № 5 , учбовий корпус У-5, Палац студентів. Головним корпусом студмістечка є гуртожиток № 1 «Гігант», в якому розміщена дирекція студмістечка, медичний пункт та медичний центр, в гуртожитку No 5 — студентська їдальня.<br><br>На території споруджено спортивний майданчик та кафетерій, напроти Палацу студентів мається невеличка паркова зона. Даний житловий комплексграничить з міським парком «Молодіжний» та навчально  — спортивним комплексом НТУ «ХПІ».  Гуртожитки № 3, № 7, № 9, № 11, № 12 є складовою студентських гуртожитків «Олексіївського» житлового масиву де також розташовані гуртожитки інших вищих навчальних закладів.</p><p style=\\\"text-align:left;\\\"><br>На території є спортивні майданчики, комплекси розважальних та побутових послуг. В 2015 році поряд зі студентським містечком   відкрито станцію метрополітену «Олексіївська». Окремо знаходяться гуртожитки № 2, № 13, № 14, № 15. Всі в они розташовані поблизу станцій метрополітену та доступного транспортного сполучення, що звязує гуртожитки з університетом та дає можливість швидко добиратися до нього . Гуртожитки розташовані в густонаселених районах де є в сі необхідні побутові та торговельні послуги.<br><br>У всіх гуртожитках є спортивні зали, кімнати для занять та відпочинку, також проведено локальну ком ’ютерну мережу з  можливістю доступу до Інтернету. Для кожного гуртожитку складена своя вартість проживання з урахуванням витрат на одного проживаючого. Гуртожитки закріплені за факультетами університету, в кожному є один чи два базові факультети, які мають пріоритетне право поселення своїх студентів в даний гуртожиток. Відповідно факультетами формується студентське самоуправління,<br><br>Кімнати в гуртожитках коридорного типу розраховані для проживання 3 -4 -ох осіб, кухні, туалети, умивальні кімнати по дві на кожному поверсі, душова кімната на першому поверсі. Кімнати в гуртожитках блочного типу розраховані для проживання  2, 3, 4  — ох осіб, кухні — по дві на кожному поверсі,  душова кімната, туалет в блоці або в кімнаті.</p><h2 style=\\\"text-align:center;\\\"><b>Факультети та інститути</b></h2><table border=\\\"1\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"width:74%;\\\"><tbody><tr><td style=\\\"width:82.6667%;\\\" valign=\\\"top\\\" width=\\\"76%\\\"><div><b>Факультети та науково-навчальні інститути</b></div></td><td style=\\\"width:17.1852%;text-align:center;vertical-align:middle;\\\" valign=\\\"top\\\" width=\\\"23%\\\"><div><b>Гуртожитки</b></div></td></tr><tr><td style=\\\"width:82.6667%;\\\" valign=\\\"top\\\" width=\\\"76%\\\"><div>Навчально-науковий інститут економіки, менеджменту і міжнародного бізнесу (БЕМ)</div></td><td style=\\\"width:17.1852%;text-align:left;vertical-align:middle;\\\" valign=\\\"top\\\" width=\\\"23%\\\"><div>12,14</div></td></tr><tr><td style=\\\"width:82.6667%;\\\" valign=\\\"top\\\" width=\\\"76%\\\"><div>Навчально-науковий інститут хімічних технологій та інженерії (ХТ)</div></td><td style=\\\"width:17.1852%;text-align:left;vertical-align:middle;\\\" valign=\\\"top\\\" width=\\\"23%\\\"><div>11</div></td></tr><tr><td style=\\\"width:82.6667%;\\\" valign=\\\"top\\\" width=\\\"76%\\\"><div>Навчально-науковий інженерно-фізичний інститут (І)</div></td><td style=\\\"width:17.1852%;text-align:left;vertical-align:middle;\\\" valign=\\\"top\\\" width=\\\"23%\\\"><div>1</div></td></tr><tr><td style=\\\"width:82.6667%;\\\" valign=\\\"top\\\" width=\\\"76%\\\"><div>Навчально-науковий інститут механічної інженерії і транспорту (МІТ)</div></td><td style=\\\"width:17.1852%;text-align:left;vertical-align:middle;\\\" valign=\\\"top\\\" width=\\\"23%\\\"><div>7,9</div></td></tr><tr><td style=\\\"width:82.6667%;\\\" valign=\\\"top\\\" width=\\\"76%\\\"><div>Навчально-науковий інститут енергетики, електроніки та електромеханіки (Е)</div></td><td style=\\\"width:17.1852%;text-align:left;vertical-align:middle;\\\" valign=\\\"top\\\" width=\\\"23%\\\"><div>2,15</div></td></tr><tr><td style=\\\"width:82.6667%;\\\" valign=\\\"top\\\" width=\\\"76%\\\"><div>Факультет міжнародної освіти (МО)</div></td><td style=\\\"width:17.1852%;text-align:left;vertical-align:middle;\\\" valign=\\\"top\\\" width=\\\"23%\\\"><div>3,4,6,10</div></td></tr><tr><td style=\\\"width:82.6667%;\\\" valign=\\\"top\\\" width=\\\"76%\\\"><div>Факультет соціально-гуманітарних технологій (СГТ)</div></td><td style=\\\"width:17.1852%;text-align:left;vertical-align:middle;\\\" valign=\\\"top\\\" width=\\\"23%\\\"><div>13</div></td></tr><tr><td style=\\\"width:82.6667%;\\\" valign=\\\"top\\\" width=\\\"76%\\\"><div>Факультет комп’ютерних наук і програмної інженерії (КН)</div></td><td style=\\\"width:17.1852%;text-align:left;vertical-align:middle;\\\" valign=\\\"top\\\" width=\\\"23%\\\"><div>1</div></td></tr><tr><td style=\\\"width:82.6667%;\\\" valign=\\\"top\\\" width=\\\"76%\\\"><div>Факультет комп’ютерних та інформаційних технологій (КІТ)</div></td><td style=\\\"width:17.1852%;text-align:left;vertical-align:middle;\\\" valign=\\\"top\\\" width=\\\"23%\\\"><div>5</div></td></tr></tbody></table><br><h2 style=\\\"text-align:center;\\\"><b>Корпуси</b></h2>АК — Адміністративний корпус<br>ВК — Вечірній корпус<br>ГК (РК) — Головний (ректорський) корпус<br>ГАК — Головний аудиторний корпус<br>ДС — Палац студентів<br>ІК — Інженерний корпус<br>КОО — Корпус громадських організацій<br>МК — Математичний корпус<br>РК — Радіокорпус<br>СК — Спорткомплекс<br>ТК — Технічний корпус<br>У-1 — Навчальний корпус № 1<br>У-2 — Навчальний корпус № 2<br>У-3 — Навчальний корпус № 3<br>У-4 — Навчальний корпус № 4<br>У-5 — Навчальний корпус № 5<div class=\\\"heateor_sss_sharing_container heateor_sss_horizontal_sharing\\\"><br></div>', 0, 1, 'all', '', 'Каталог гуртожитків Порядок поселення СубсидіяВ підпорядкуванні студмістечка знаходяться 15 гуртожитків загальною житловою площею 41272 м.кв на 6400 ліжко-місць, з яких 5 блочного, 9 коридорного та 1 сімейного типу . Чотири гуртожитки призначені для компактного проживання іноземних студентів та', 'корпус, інститут, Навчальний, гуртожитки, студентів, проживання, науковий, студмістечка, Гуртожитки, гуртожитках, розташовані, кожному, поверсі, гуртожитків, розраховані, гуртожиток, комп\'ютерних, медичний, Кімнати, знаходяться', 131, '', 1590416874, '', 1, 1, 0, 0, ''),
(10, 'hostels-2', 'Гуртожиток №2', '<b>Гуртожиток поліпшених умов. 9-ти поверхова будівля обладнана 2-ма сучасними ліфтами.Знаходиться поблизу станції метрополітену «Академіка Павлова».</b><br><br><a class=\\\"highslide\\\" href=\\\"https://espo.co.ua/uploads/posts/2020-05/1590439309_obshezhitie_02.jpg\\\"><img src=\\\"https://espo.co.ua/uploads/posts/2020-05/1590439309_obshezhitie_02.jpg\\\" class=\\\"fr-fic fr-dib\\\" style=\\\"width:500px;\\\" alt=\\\"\\\"></a><br>Знаходиться поблизу станції метрополітену «Академіка Павлова». Проведено капітальний ремонт всіх служб, приміщень та житлових кімнат. В кожній житловій кімнаті є санвузол з туалетом та душовою, кухня загальна по 2 на кожному поверсі. Є спортивні кімнати, кімнати для занять та відпочинку. Встановлено відеонагляд входу в гуртожиток та на поверхах.<br><br><b>Базові факультети для проживання</b> – Е, ЕМС.<br><b>Додаткові побутові послуги</b> – є пральня, два ліфти.<br><b>Адреса:</b> вул. Зубенко 9 А.', 0, 1, 'all', 'hostel-2', 'гурт 2', 'кімнати, Знаходиться, поблизу, станції, метрополітену, «Академіка, Павлова», Гуртожиток, поверхах, спортивні, занять, відпочинку, Встановлено, відеонагляд, входу, гуртожиток, Додаткові, Базові, факультети, проживання', 62, '', 1591843560, '', 1, 1, 0, 0, ''),
(11, 'hostels-3', 'Гуртожиток №3', 'Побудований у 1971 році.Знаходиться на території комплексу «Олексіївський». Гуртожиток поліпшених умов для проживання іноземних студентів. 9-ти поверхова будівля обладнана 2-ма сучасними ліфтами.<br><br><a class=\\\"highslide\\\" href=\\\"https://espo.co.ua/uploads/posts/2020-06/1591844851_xxxl.jpg\\\"><img src=\\\"https://espo.co.ua/uploads/posts/2020-06/medium/1591844851_xxxl.jpg\\\" class=\\\"fr-fic fr-dib\\\" style=\\\"width:468px;\\\" alt=\\\"\\\"></a><br>Проведено капітальний ремонт всіх служб, приміщень та житлових кімнат. В кожній житловій кімнаті є санвузол з туалетом та душовою, кухня загальна – по 2 на кожному поверсі. Є спортивні кімнати, кімнати для занять та відпочинку. Встановлено відеонагляд входу в гуртожиток та на поверхах.<br>Гуртожиток приймав гостей при проведені ЄВРО-2012.<br><br><b>Базові факультети для проживання</b> – Факультет міжнародної освіти.<br><b>Адреса:</b> вул. Целіноградська 56.', 0, 1, 'all', 'hostel-3', 'Побудований у 1971 році.Знаходиться на території комплексу «Олексіївський». Гуртожиток поліпшених умов для проживання іноземних студентів. 9-ти поверхова будівля обладнана 2-ма сучасними ліфтами. Проведено капітальний ремонт всіх служб, приміщень та житлових кімнат. В кожній житловій кімнаті є', 'Гуртожиток, проживання, кімнати, Побудований, входу, загальна, кожному, поверсі, спортивні, занять, відпочинку, Встановлено, відеонагляд, поверхах, гуртожиток, душовою, приймав, гостей, проведені, Базові', 31, '', 1591843839, '', 1, 1, 0, 0, ''),
(12, 'firstaid', 'Медпункт', '<h3 style=\\\"text-align:center;\\\"><b>Відомість про оздоровчий пункт НТУ «ХПІ»</b></h3><a class=\\\"highslide\\\" href=\\\"https://espo.co.ua/uploads/posts/2020-05/1590429647_p1240445.jpg\\\"><img src=\\\"https://espo.co.ua/uploads/posts/2020-05/1590429647_p1240445.jpg\\\" class=\\\"fr-fic fr-dii fr-fil\\\" style=\\\"width:489px;\\\" alt=\\\"\\\"></a>Оздоровчий пункт розташований у зручному для відвідувачів місці, на перехресті шляхів міського громадського  транспорту, біля станції метро «Пушкінська», де також є зупинки маршрутних таксі, на вул.. Пушкінській,79/1 у приміщенні гуртожитку «Гігант» НТУ «ХПІ». Також неподалік, на території студмістечка, розміщені гуртожитки №4, №5, №6 та №10, що дозволяє досягнути вільного та зручного відвідування закладу охорони здоров’я студентами.<br><br>Оздоровчий пункт працює шість днів на тиждень: понеділок – п’ятниця з 8:00 до 18:00; субота – з 8:00 до 14:00. Телефон реєстратури: 707-60-34. Центр укомплектований мед. персоналом, що складається з:<br><br><br><br><br>Лікар центру – Єрмоленко Ірина Миколаївна, лікар-спеціаліст, стаж роботи  4,5 роки..<br>Дільничий лікар терапевт – Драніщева Ія Мирославівна, лікар другої категорії, стаж роботи 10 років.<br>Дільничий лікар терапевт – Жеребцова Катерина Олегівна, лікар-спеціаліст, стаж роботи 5 років.<br>Дільничий лікар терапевт – Нєдєлько Тетяна Михайлівна, лікар вищої категорії, стаж роботи 37 років.<br>Дільничий лікар терапевт – Тютюрікова Наталія Сергіївна, стаж роботи 39 років.<br>Дільничий лікар терапевт – Ясногородська Вікторія Володимирівна, лікар вищої категорії,  стаж роботи 39 років.<br>Стоматолог – Панкова Наталія Юріївна, лікар першої категорії,  стаж роботи 29 років.<br>Стоматолог – Соколова Ірина Миколаївна, лікар першої категорії,  стаж роботи 16 років.<br>Стоматолог – Тищенко Людмила Юріївна, лікар першої категорії,  стаж роботи 27 років.<br>Стоматолог –Христоєва Юлія Олександрівна, лікар першої категорії,  стаж роботи 18 років.<br>Акушер-гінеколог – Бойко Ольга Леонідівна,  лікар першої категорії,  стаж роботи 34 роки<br><br><hr><br><h3 style=\\\"text-align:center;\\\"><b>В склад оздоровчого пункту входять наступні функціональні кабінети:</b></h3><br><ul style=\\\"list-style-type:disc;\\\"><li>реєстратура</li><li>кабінет завідувача оздоровчим пунктом</li><li>3 кабінети дільничних лікарів</li><li>2 кабінети стоматологів</li><li>гінекологічний кабінет</li><li>кабінет долікарського огляду</li><li>кабінет щеплень</li><li>кабінет клінічної лабораторії</li><li>та інші приміщення</li></ul><h3 style=\\\"text-align:center;\\\"><b>Задачі центру ПМСД:</b></h3><br><ul style=\\\"list-style-type:disc;\\\"><li>Надання кваліфікованої лікувально – діагностичної допомоги в повному обсязі в умовах центру та вдома.</li><li>Надання кваліфікованої невідкладної медичної допомоги при раптових захворюваннях та нещасних випадках.</li><li>Направлення хворих студентів на консультування до лікарів інших спеціальностей поліклінічного відділення та у стаціонарні відділення студентської лікарні, а також у інші лікувально – профілактичні заклади.</li><li>Організація та проведення санітарно – протиепідемічних заходів у осередках інфекційних чи інших заразних захворювань.</li><li>Навчання жінок фертильного віку питанням планування сім’ї.</li><li>Організація та проведення комплексних профілактичних медичних оглядів студентів.</li><li>Організація проведення флюорографічного обстеження органів грудної клітини.</li><li>Організація та проведення заходів по імунізації студентів проти керованих інфекцій.</li><li>Забезпечення своєчасного комплексного обстеження іноземних студентів, що прибули для навчання в вуз.</li><li>Проведення санітарно – освітньої роботи серед студентів, удосконалення валеологічного навчання та виховання студентської молоді з метою раннього попередження захворювань і формування особистої відповідальності за збереження власного здоров’я. Мета нашої роботи – забезпечення доступної якісної та кваліфікованої медичної допомоги студентам.</li></ul><br><h5><b>Також посилання на міську студентську лікарню:</b><a href=\\\"http://studhosp.city.kharkov.ua/\\\" rel=\\\"external noopener noreferrer\\\"> </a><a href=\\\"http://studhosp.city.kharkov.ua/\\\" rel=\\\"noopener noreferrer external\\\" target=\\\"_blank\\\">STUDHOSP.CITY.KH.UA</a></h5>', 0, 1, 'all', '', 'Відомість про оздоровчий пункт НТУ «ХПІ»Оздоровчий пункт розташований у зручному для відвідувачів місці, на перехресті шляхів міського громадського транспорту, біля станції метро «Пушкінська», де також є зупинки маршрутних таксі, на вул.. Пушкінській,79/1 у приміщенні гуртожитку «Гігант» НТУ «ХПІ».', 'лікар, роботи, років, категорії, Дільничий, першої, терапевт, Стоматолог, пункт, спеціаліст, Юріївна, Миколаївна, Наталія, вищої, Ірина, Мирославівна, другої, Відомість, Драніщева, Катерина', 99, '', 1590429671, '', 1, 1, 0, 0, ''),
(13, 'subsidies', 'Субсидії', '<h3 style=\\\"text-align:center;\\\"><b>Для гуртожитків, що розташовані в Київському районі м. Харкова</b></h3><h5>Гуртожитки № 1, №2, №5</h5>Необхідні документи:<br>– Довідка про склад сім’ї та розмір платежів за житлово-комунальні послуги<br>Надається дирекцією студмістечка вул. Пушкінська 79/1<br>Оформлення субсидії за адресою: вул. Чернишевського, 55<br>Центр надання соціальних послуг «Прозорий офіс»<br><br><h5><a href=\\\"http://prozoriy-office.kharkov.ua\\\" target=\\\"_blank\\\" rel=\\\"noopener external noreferrer\\\">ПРОЗОРИЙ ОФІС</a></h5><br> <br><br><h3 style=\\\"text-align:center;\\\"><b>Для гуртожитків, що розташовані в Шевченківському районі м. Харкова</b></h3><h5>Гуртожитки № 7, №9, №11, №12, №13</h5><br>Необхідні документи:<br>Довідка про склад сім’ї та розмір платежів за житлово-комунальні послуги<br>Надається дирекцією студмістечка вул. Пушкінська 79/1<br><br>Довідка про дохід :<br>Для всіх надається в стипендіальному відділі універститету (2 –ий поверх адміністративного корпусу)<br><br>Крім того при собі мати: довідку про дохід, якщо є пенсія по втраті годувальника або соціальна пільга на проживання для внутрішньо переміщених осіб. Довідка надається за місцем оформлення.<br><br>Оформлення субсидії за адресою: пр.. Науки, 17А<br>Центр надання соціальних послуг «Прозорий офіс»<br><br><h5><a href=\\\"http://prozoriy-office.kharkov.ua\\\" target=\\\"_blank\\\" rel=\\\"noopener external noreferrer\\\">ПРОЗОРИЙ ОФІС</a></h5><br> <br><br><h3 style=\\\"text-align:center;\\\"><b>Для гуртожитків, що розташовані в Московському районі м. Харкова</b></h3><h5>Гуртожиток № 15</h5><br>Необхідні документи:<br>Довідка про склад сім’ї та розмір платежів за житлово-комунальні послуги<br>Надається дирекцією студмістечка вул. Пушкінська 79/1<br><br>Довідка про дохід<br>Для всіх надається в стипендіальному відділі універститету (2 –ий поверх адміністративного корпусу)<br>Крім того: довідка про дохід, якщо є пенсія по втраті годувальника або соціальна пільга на проживання для внутрішньо переміщених осіб. Довідка надається за місцем оформлення.<br><br>Довідка, що є студентом<br>Надається деканатом факультету<br><br>Оформлення субсидії за адресою: вул. Гвардійців-Широнінців, 38-Г<br><br><h3 style=\\\"text-align:center;\\\"><b>Для гуртожитків, що розташовані в Немишлянському районі м. Харкова</b></h3><h5>Гуртожиток № 14</h5><br>Необхідні документи:<br><br>Довідка про склад сім’ї та розмір платежів за житлово-комунальні послуги<br>Надається дирекцією студмістечка вул. Пушкінська 79/1<br><br>Довідка про дохід<br>Для всіх надається в стипендіальному відділі універститету (2 –ий поверх адміністративного корпусу)<br><br>Крім того: довідка про дохід, якщо є пенсія по втраті годувальника або соціальна пільга на проживання для внутрішньо переміщених осіб. Довідка надається за місцем оформлення.<br><br>Оформлення субсидії за адресою: вул. Льва Ландау, 48', 0, 1, 'all', '', 'Для гуртожитків, що розташовані в Київському районі м. ХарковаГуртожитки № 1, №2, №5Необхідні документи: – Довідка про склад сім’ї та розмір платежів за житлово-комунальні послуги Надається дирекцією студмістечка вул. Пушкінська 79/1 Оформлення субсидії за адресою: вул. Чернишевського, 55 Центр', 'Довідка, надається, дохід, Надається, гуртожитків, послуги, розташовані, адресою, субсидії, Оформлення, Пушкінська, дирекцією, студмістечка, комунальні, платежів, розмір, сім’ї, склад, документи, районі', 63, '', 1590438648, '', 1, 1, 0, 0, ''),
(14, 'hostels-2-map', 'Маршрут проїзду', '<iframe width=\\\"100%\\\" height=\\\"500\\\" frameborder=\\\"0\\\" scrolling=\\\"no\\\" marginheight=\\\"0\\\" marginwidth=\\\"0\\\" src=\\\"https://www.google.com/maps/embed?pb=!1m24!1m8!1m3!1d41026.15840831369!2d36.2689134!3d50.0088267!3m2!1i1024!2i768!4f13.1!4m13!3e3!4m5!1s0x4127a0e98e8e2a0d:0xfa916cfdbe03ae0a!2z0KPRh9C10LHQvdGL0Lkg0LouIOKEliAyINCd0KLQoyAi0KXQn9CYIiwg0LLRg9C70LjRhtGPINCa0LjRgNC_0LjRh9C-0LLQsCwgMiwg0KXQsNGA0LrRltCyLCDQpdCw0YDQutGW0LLRgdGM0LrQsCDQvtCx0LvQsNGB0YLRjCwgNjEwMDA!3m2!1d49.9983406!2d36.2478177!4m5!1s0x412709e25d6b8355:0xf8e9299677d8f783!2z0LLRg9C70LjRhtGPINCS0LvQsNC00LjRgdC70LDQstCwINCX0YPQsdC10L3QutCwLCA5LCDQpdCw0YDQutGW0LIsINCl0LDRgNC60ZbQstGB0YzQutCwINC-0LHQu9Cw0YHRgtGM!3m2!1d50.0109931!2d36.325514!5e0!3m2!1suk!2sua!4v1489490722220\\\"></iframe>', 0, 1, 'all', 'hostel-2', 'Маршрут проезда общежития 2', 'маршрут проезда, маршрут проїзду', 8, '', 1590439905, '', 1, 1, 0, 0, ''),
(15, 'foreigners', 'Гуртожитки для іноземних студентів', '<table class=\\\"fr-solid-borders\\\"><tbody><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><b>№ </b><b>гуртожитку</b></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\"><b> <b>Прізвище ім’я по батькові</b></b></td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\"><b>Посада</b></td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\"><b>№ телефон</b><b>у</b></td><td style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\"><b>І</b><b>ндекс</b></td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><b> </b><br><b><b>Адрес</b><b>а</b></b><br><br></td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-3.html\\\"><b>Гуртожиток  3</b></a><br><br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\">Чен Надія Іннокентіївна</td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Зав. гуртожитком</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">336-77-47</td><td style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\">61202</td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\">вул. Целіноградська 56</td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-4.html\\\"><b>Гуртожиток  4</b></a><br><br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\">Болдирєва Ольга Вікторівна</td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Зав. гуртожитком</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">707-66-31</td><td style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\"> <br>61024</td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\">вул. Пушкінська 79/4</td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-6.html\\\"><b>Гуртожиток 6</b></a><br><br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\">Олійник Віра Романівна</td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Комендант</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">707-61-90</td><td style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\">61047</td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\">Студентська 15/17</td></tr><tr><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\"><br><a href=\\\"https://espo.co.ua/hostels-10.html\\\"><b>Гуртожиток 10</b></a><br><br></td><td style=\\\"width:22.0153%;vertical-align:middle;text-align:center;\\\">Алтинцева Наталя Михайлівна</td><td style=\\\"width:15.3341%;vertical-align:middle;text-align:center;\\\">Зав. гуртожитком</td><td style=\\\"width:15.9912%;vertical-align:middle;text-align:center;\\\">704-16-14</td><td style=\\\"width:11.391%;vertical-align:middle;text-align:center;\\\">61023</td><td style=\\\"width:150.667px;vertical-align:middle;text-align:center;\\\">Провулок Дизайнерський 4</td></tr></tbody></table>', 0, 1, 'all', '', '№ гуртожитку Прізвище ім’я по батьковіПосада№ телефонуІндекс Адреса Гуртожиток 3 Чен Надія ІннокентіївнаЗав. гуртожитком336-77-4761202вул. Целіноградська 56 Гуртожиток 4 Болдирєва Ольга ВікторівнаЗав. гуртожитком707-66-31 61024вул. Пушкінська 79/4 Гуртожиток 6 Олійник Віра', 'Гуртожиток, гуртожитку, гуртожитком707, 1461023Провулок, гуртожитком704, МихайлівнаЗав, Наталя, Алтинцева, 9061047Студентська, РоманівнаКомендант707, Олійник, Пушкінська, 61024вул, ВікторівнаЗав, Прізвище, Ольга, Болдирєва, Целіноградська, 4761202вул, гуртожитком336', 13, '', 1589839200, '', 1, 1, 0, 0, ''),
(16, 'hostels-4', 'Гуртожиток №4', '<img src=\\\"https://espo.co.ua/uploads/posts/2020-06/1593260062_93_big.jpg\\\" alt=\\\"\\\" class=\\\"fr-dib\\\" style=\\\"width:450px;\\\"><br class=\\\"Apple-interchange-newline\\\">Побудований у 1962 році 5-и поверховий гуртожиток коридорного типу. Гуртожиток для проживання іноземних студентів. Знаходиться на території комплексу «Гігант». Кухні, туалети та умивальні кімнати – на кожному поверсі, душові – на 1-му поверсі. Є спортивні кімнати, кімнати для занять та відпочинку.<br><br><b>Базові факультети для проживання</b> – Факультет міжнародної освіти<br><b>Адреса:</b> вул. Пушкінська 79/4.', 0, 1, 'all', 'hostel-4', 'Побудований у 1962 році 5-и поверховий гуртожиток коридорного типу. Гуртожиток для проживання іноземних студентів. Знаходиться на території комплексу «Гігант». Кухні, туалети та умивальні кімнати – на кожному поверсі, душові – на 1-му поверсі. Є спортивні кімнати, кімнати для занять та відпочинку.', 'кімнати, проживання, поверсі, Побудований, Адреса, освіти, міжнародної, Факультет, факультети, Базові, відпочинку, занять, спортивні, душові, кожному, умивальні, поверховий, туалети, Кухні, «Гігант»', 65, '', 1591844305, '', 1, 1, 0, 0, ''),
(17, 'hostels-5', 'Гуртожиток №5', 'Побудований у 1964 році 5-и поверховий гуртожиток коридорного типу. Знаходиться на території комплексу «Гігант».<br><br><a class=\\\"highslide\\\" href=\\\"https://espo.co.ua/uploads/posts/2020-06/1591851669_5.jpg\\\"><img src=\\\"https://espo.co.ua/uploads/posts/2020-06/medium/1591851669_5.jpg\\\" class=\\\"fr-fic fr-dib\\\" alt=\\\"\\\"></a><br>Кухні, туалети та умивальні кімнати – на кожному поверсі, душові – в підвальному приміщені Є спортивні кімнати, кімнати для занять та відпочинку. На першому поверсі розташована студентська їдальна. Встановлено відеонагляд входу в гуртожиток та на поверхах.<br><br><b>Базові інститути/факультети для проживання</b> – Факультет комп’ютерних та інформаційних технологій<br><b>Адреса:</b> вул. Пушкінська 79/5.', 0, 1, 'all', 'hostel-5', 'Побудований у 1964 році 5-и поверховий гуртожиток коридорного типу. Знаходиться на території комплексу «Гігант». Кухні, туалети та умивальні кімнати – на кожному поверсі, душові – в підвальному приміщені Є спортивні кімнати, кімнати для занять та відпочинку. На першому поверсі розташована', 'кімнати, гуртожиток, поверсі, Побудований, їдальна, Встановлено, відеонагляд, входу, поверхах, Базові, інститути, розташована, факультети, проживання, Факультет, комп’ютерних, інформаційних, технологій, Адреса, студентська', 22, '', 1591851585, '', 1, 1, 0, 0, ''),
(18, 'hostels-6', 'Гуртожиток №6', '<br><a class=\\\"highslide\\\" href=\\\"https://espo.co.ua/uploads/posts/2020-06/1591851936_dsc_1975.jpg\\\"><img src=\\\"https://espo.co.ua/uploads/posts/2020-06/medium/1591851936_dsc_1975.jpg\\\" class=\\\"fr-fic fr-dib\\\" alt=\\\"\\\"></a><br>Побудований у 1955 році 4-ох поверховий гуртожиток коридорного типу. Гуртожиток для проживання іноземних студентів. Знаходиться в десяти хвилинах пішки від університету. Кухні, туалети, умивальні кімнати та душові – на кожному поверсі.<br><br><b>Базові інститути/факультети для проживання</b> – Факультет міжнародної освіти.<br><b>Адреса:</b> вул. Студентська 15/17.', 0, 1, 'all', 'hostel-6', 'Побудований у 1955 році 4-ох поверховий гуртожиток коридорного типу. Гуртожиток для проживання іноземних студентів. Знаходиться в десяти хвилинах пішки від університету. Кухні, туалети, умивальні кімнати та душові – на кожному поверсі. Базові інститути/факультети для проживання – Факультет', 'проживання, Побудований, умивальні, Адреса, освіти, міжнародної, Факультет, факультети, інститути, Базові, поверсі, кожному, душові, кімнати, туалети, поверховий, Кухні, університету, пішки, хвилинах', 22, '', 1591851874, '', 1, 1, 0, 0, ''),
(19, 'hostels-7', 'Гуртожиток №7', '<div style=\\\"text-align:center;\\\"><a class=\\\"highslide\\\" href=\\\"https://espo.co.ua/uploads/posts/2020-06/1593246787_img_7084.jpg\\\"><img src=\\\"https://espo.co.ua/uploads/posts/2020-06/1593246787_img_7084.jpg\\\" class=\\\"fr-fic fr-dib\\\" alt=\\\"\\\"></a></div>Проведено капітальний ремонт всіх служб, приміщень. В кожному блоці є санвузол з туалетом та душовою, кухня загальна – по 2 на кожному поверсі. Є спортивні кімнати, кімнати для занять та відпочинку. Встановлено відеонагляд входу в гуртожиток та на поверхах.<br><br><b>Базові факультети для проживання</b> – Інститут механічної інженерії і транспорту<br><b>Адреса:</b> вул. Преможна 19.', 0, 1, 'all', 'hostel-7', 'Проведено капітальний ремонт всіх служб, приміщень. В кожному блоці є санвузол з туалетом та душовою, кухня загальна – по 2 на кожному поверсі. Є спортивні кімнати, кімнати для занять та відпочинку. Встановлено відеонагляд входу в гуртожиток та на поверхах. Базові факультети для проживання –', 'кімнати, кожному, Проведено, Встановлено, Адреса, транспорту, інженерії, механічної, Інститут, проживання, факультети, Базові, поверхах, гуртожиток, входу, відеонагляд, занять, відпочинку, капітальний, спортивні', 49, '', 1593246878, '', 1, 1, 0, 0, ''),
(20, 'hostels-8', 'Гуртожиток №8', '<a class=\\\"highslide\\\" href=\\\"https://espo.co.ua/uploads/posts/2020-06/1593248082_snimok.png\\\"><img src=\\\"https://espo.co.ua/uploads/posts/2020-06/1593248082_snimok.png\\\" class=\\\"fr-fic fr-dib\\\" style=\\\"width:600px;\\\" alt=\\\"\\\"></a>Вхід знаходиться у під\\\'їзді в жилому домі. Перші три поверхи - жилі квартири, далі - три поверхи гуртожитка. Знаходиться біля обласного клінічного містечка. Кухні, туалети, душові та умивальні кімнати – на кожному поверсі.<br><br><b>Базові інститути/факультети для проживання</b> – відідл аспірантури<br><b>Адреса:</b> вул. Літературна  3.', 0, 1, 'all', 'hostel-8', 'Побудований у 196 році. 3 поверхи гуртожитку коридорного типу. Знаходиться біля обласного клінічного містечка. Кухні, туалети та умивальні кімнати – на кожному поверсі, душові Базові інститути/факультети для проживання – відідл аспірантури Адреса: вул. Літературна 3.', 'Побудований, кожному, Адреса, аспірантури, відідл, проживання, факультети, інститути, Базові, душові, поверсі, кімнати, поверхи, умивальні, туалети, Кухні, містечка, клінічного, обласного, Знаходиться', 17, '', 1593248414, '', 1, 1, 0, 0, ''),
(21, 'hostels-9', 'Гуртожиток №9', '<div style=\\\"text-align:center;\\\"><img src=\\\"https://espo.co.ua/uploads/posts/2020-06/1593250384_foto_17828.jpg\\\" alt=\\\"\\\" class=\\\"fr-dii\\\"><a class=\\\"highslide\\\" href=\\\"https://espo.co.ua/uploads/posts/2020-06/1593250430_x_22c8a434.jpg\\\"><img src=\\\"https://espo.co.ua/uploads/posts/2020-06/medium/1593250430_x_22c8a434.jpg\\\" class=\\\"fr-fic fr-dii\\\" style=\\\"width:345px;\\\" alt=\\\"\\\"></a></div><br>Побудований у 1964 році 5-и поверховий гуртожиток коридорного типу. Знаходиться на території комплексу «Олексіївський».Кухні, туалети та умивальні кімнати – на кожному поверсі, душові – на 1-му поверсі. Є спортивні кімнати, кімнати для занять та відпочинку.<br><br><b>Базові інститути/факультети для проживання</b> – Інститут механічної інженерії і транспорту<br><b>Адреса:</b> вул. Цілиноградська 38.', 0, 1, 'all', 'hostel-9', 'Побудований у 1964 році 5-и поверховий гуртожиток коридорного типу. Знаходиться на території комплексу «Олексіївський».Кухні, туалети та умивальні кімнати – на кожному поверсі, душові – на 1-му поверсі. Є спортивні кімнати, кімнати для занять та відпочинку. Базові інститути/факультети для', 'кімнати, поверсі, Побудований, занять, Адреса, транспорту, інженерії, механічної, Інститут, проживання, факультети, інститути, Базові, відпочинку, душові, спортивні, поверховий, кожному, умивальні, туалети', 32, '', 1593250452, '', 1, 1, 0, 0, ''),
(22, 'hostels-10', 'Гуртожиток №10', '<img src=\\\"https://espo.co.ua/uploads/posts/2020-06/1593250780_snimo1k.png\\\" alt=\\\"\\\" class=\\\"fr-dib\\\" style=\\\"width:600px;\\\"><br>Побудований у 1975 році 5-ох поверховий гуртожиток блочного типу. В блоці – по 2 двомісні та 2 тримісні кімнати, душова та туалет. Кухні – по дві на кожному поверсі.Гуртожиток для проживання іноземних студентів.Знаходиться в пятнадцяти хвилинах пішки від університету та в пяти хвилинах від Харьківського центрального парку розваг. Кухні, туалети, умивальні кімнати та душові – на кожному поверсі.<br><br><b>Базові факультети для проживання </b>– Факультет міжнародної освіти.<br><b>Адреса:</b> Провулок Дизайнерський 4.', 0, 1, 'all', 'hostel-10', 'Побудований у 1975 році 5-ох поверховий гуртожиток блочного типу. В блоці – по 2 двомісні та 2 тримісні кімнати, душова та туалет. Кухні – по дві на кожному поверсі.Гуртожиток для проживання іноземних студентів.Знаходиться в пятнадцяти хвилинах пішки від університету та в пяти хвилинах від', 'Кухні, кожному, хвилинах, кімнати, проживання, поверсі, парку, розваг, туалети, умивальні, душові, Побудований, Харьківського, Базові, факультети, Факультет, міжнародної, освіти, Адреса, Провулок', 54, '', 1593250817, '', 1, 1, 0, 0, ''),
(23, 'hostels-11', 'Гуртожиток №11', '<img src=\\\"https://espo.co.ua/uploads/posts/2020-06/1593251227_dsc_2685-1024x685.jpg\\\" alt=\\\"\\\" class=\\\"fr-dib\\\" style=\\\"width:600px;\\\"><br>Побудований у 1968 році 5-и поверховий гуртожиток коридорного типу. Знаходиться на території комплексу «Олексіївький».<br>Кухні, туалети та умивальні кімнати – на кожному поверсі, душові на 1-му поверсі. Є спортивні кімнати, кімнати для занять та відпочинку.<br><br><b>Базові факультети для проживання</b> – Інститут хімічних технологій та інженерії.<br><b>Адреса:</b> вул. Целіноградська 48.', 0, 1, 'all', 'hostel-11', 'Побудований у 1968 році 5-и поверховий гуртожиток коридорного типу. Знаходиться на території комплексу «Олексіївький». Кухні, туалети та умивальні кімнати – на кожному поверсі, душові на 1-му поверсі. Є спортивні кімнати, кімнати для занять та відпочинку. Базові факультети для проживання –', 'кімнати, поверсі, спортивні, Адреса, інженерії, технологій, хімічних, Інститут, проживання, факультети, Базові, відпочинку, занять, Побудований, душові, поверховий, кожному, умивальні, туалети, Кухні', 18, '', 1593251330, '', 1, 1, 0, 0, ''),
(24, 'hostels-12', 'Гуртожиток №12', '<img src=\\\"https://espo.co.ua/uploads/posts/2020-06/1593251874_img_0028-12.jpg\\\" alt=\\\"\\\" class=\\\"fr-dib\\\" style=\\\"width:600px;\\\"><br>Побудований у 1996 році 5-и поверховий гуртожиток коридорного типу. Знаходиться на території комплексу «Олексіївський».Кухні, туалети та умивальні кімнати – на кожному поверсі, душові – на 1-му поверсі.Є спортивні кімнати, кімнати для занять та відпочинку.<br><br><b>Базові інститути/факультети для проживання</b> – Інститут економіки, менеджменту та міжнародного бізнесу.<br><b>Адреса:</b> вул. Целіноградська 32.', 0, 1, 'all', 'hostel-12', 'Побудований у 1996 році 5-и поверховий гуртожиток коридорного типу. Знаходиться на території комплексу «Олексіївський».Кухні, туалети та умивальні кімнати – на кожному поверсі, душові – на 1-му поверсі.Є спортивні кімнати, кімнати для занять та відпочинку. Базові інститути/факультети для', 'кімнати, поверсі, Побудований, занять, Адреса, бізнесу, міжнародного, менеджменту, економіки, Інститут, проживання, факультети, інститути, Базові, відпочинку, душові, спортивні, поверховий, кожному, умивальні', 23, '', 1593251918, '', 1, 1, 0, 0, ''),
(25, 'hostels-13', 'Гуртожиток №13', '<img src=\\\"https://espo.co.ua/uploads/posts/2020-06/1593255433_2017-10-17.jpg\\\" alt=\\\"\\\" class=\\\"fr-dib\\\"><br>Побудований у 1979 році. Знаходиться поблизу станції метрополітену «Ботанічний сад». 9-ти поверховий гуртожиток обладнаний 2-ма ліфтами, блочного типу – по чотири кімнати в блоці. В кожному блоці є санвузол з туалетом та душовою, кухня загальна – по 2 на кожному поверсі. Є спортивні кімнати, кімнати для занять та відпочинку.<br><br><b>Базові інститути/факультети для проживання</b> – Факультет соціально – гуманітарних технологій<br><b>Адреса:</b> вул. Клочківска 218 А.', 0, 1, 'all', 'hostel-13', 'Побудований у 1979 році. Знаходиться поблизу станції метрополітену «Ботанічний сад». 9-ти поверховий гуртожиток обладнаний 2-ма ліфтами, блочного типу – по чотири кімнати в блоці. В кожному блоці є санвузол з туалетом та душовою, кухня загальна – по 2 на кожному поверсі. Є спортивні кімнати,', 'кімнати, кожному, блоці, Побудований, Базові, спортивні, занять, відпочинку, інститути, загальна, факультети, проживання, Факультет, соціально, гуманітарних, технологій, Адреса, поверсі, душовою, кухня', 18, '', 1593255473, '', 1, 1, 0, 0, ''),
(26, 'hostels-14', 'Гуртожиток №14', '<img src=\\\"https://espo.co.ua/uploads/posts/2020-06/1593258477_img_0267-1024x683.jpg\\\" alt=\\\"\\\" class=\\\"fr-dib\\\" style=\\\"width:600px;\\\"><br>Побудований у 1968 році 5-ти поверховий гуртожиток коридорного типу. Кухні, туалети та умивальні кімнати – на кожному поверсі, душові –на 1-му поверсі. Є спортивні кімнати, кімнати для занять та відпочинку. Побудований у 1968 році 5-и поверховий гуртожиток коридорного типу. Кухні, туалети та умивальні кімнати – на кожному поверсі, душові –на 1-му поверсі. Є спортивні кімнати, кімнати для занять та відпочинку.<br><br><b>Базові інститути/факультети для проживання </b>– Інститут економіки, менеджменту та міжнародного бізнесу.<br><b>Адреса:</b> Балканська 19.', 0, 1, 'all', 'hostel-14', 'Побудований у 1968 році 5-ти поверховий гуртожиток коридорного типу. Кухні, туалети та умивальні кімнати – на кожному поверсі, душові –на 1-му поверсі. Є спортивні кімнати, кімнати для занять та відпочинку. Побудований у 1968 році 5-и поверховий гуртожиток коридорного типу. Кухні, туалети та', 'кімнати, поверсі, Побудований, відпочинку, поверховий, спортивні, душові, занять, кожному, туалети, Кухні, коридорного, гуртожиток, умивальні, менеджменту, Адреса, бізнесу, міжнародного, факультети, економіки', 20, '', 1593258486, '', 1, 1, 0, 0, ''),
(27, 'hostels-15', 'Гуртожиток №15', '<img src=\\\"https://espo.co.ua/uploads/posts/2020-06/1593259588_sn2imok.png\\\" alt=\\\"\\\" class=\\\"fr-dib\\\" style=\\\"width:600px;\\\"><br>Побудований у 1971 році. Знаходиться поблизу станції метрополітену «Академіка Барабашова». 5-ти поверховий гуртожиток, блочного типу – по чотири кімнати в блоці.Санвузол з туалетом та душовою – на два блоки розташований в коридорі, кухня загальна – по 2 на кожному поверсі. Є спортивні кімнати, кімнати для занять та відпочинку. Встановлено відеонагляд входу в гуртожиток та на поверхах.<br><br><b>Базові інститути/факультети для проживання</b> – Інститут енергетики, електроніки та електромеханіки<br><b>Адреса:</b> вул. Спортивна 9.', 0, 1, 'all', 'hostel-15', 'Побудований у 1971 році. Знаходиться поблизу станції метрополітену «Академіка Барабашова». 5-ти поверховий гуртожиток, блочного типу – по чотири кімнати в блоці.Санвузол з туалетом та душовою – на два блоки розташований в коридорі, кухня загальна – по 2 на кожному поверсі. Є спортивні кімнати,', 'кімнати, гуртожиток, Побудований, занять, відпочинку, Встановлено, відеонагляд, входу, поверхах, Базові, інститути, поверсі, факультети, проживання, Інститут, енергетики, електроніки, електромеханіки, Адреса, спортивні', 20, '', 1593259584, '', 1, 1, 0, 0, ''),
(28, 'about', 'Про нас', 'Здавалось, почувши слово \\\"гуртожиток\\\" будуть згадуватись веселі моменти, безсонні ночі за підручниками та відчуття безпеки,спокою та весело проведеного часу.<br>Але в реальності згадки про нього наводять страх майже на всіх абітурієнтів та змушують нервувати студентів вже не один рік. Чому? Вся справа в невдалих пошуках потрібної інформації, іноді тривалістю в декілька годин та розміром в не одну тисячу втрачених нервових клітин.<br><br>Багато років абітурієнтам та студентам доводиться занурюватись в купу незрозумілих сторінок, документів аби знайти необхідну інформацію.  І, здавалось, тільки в мріях можна було отримати потрібне лише в декілька кліків. Тож, отримавши  досвід роботи з таким пошуком даних, та втративши не одну тисячу нервових клітин, <b>ми змогли розробили портал</b><b>, який вже не перший рік штат спеціалістів НТУ \\\"ХПІ\\\" не можуть створити.</b><i> Щоб допомогти кожному майбутньому студенту НТУ «ХПІ».</i><br><br><i>Завжди в пріоритеті буде перше враження і добре, коли воно гарне.</i><br><br><b>З чого починається пошук?</b><br>Логічна та вже звична дія, коли абітурієнт звертається за допомогою до сайту університету та, на його подив, не отримує актуальну інформацію. Тож проблеми зі зручністю використання можна виключити, використавши Єдиний соціальний портал гуртожитків «Політехніку».<br><br><b>Отже, яка мета нашого сервісу?</b><br>Полегшити життя студентам, абітурієнтам та зберегти, такі необхідні їм, нервові клітини.<br><br><b>Яку інформацію можна знайти на порталі?</b><br>Описи гуртожитків, ціну проживання, умови, контактні дані, адреси, маршрути та актуальні новини студмістечка.<br>Інтерфейс порталу зручний та інтуїтивно зрозумілий. Вся інформація подана на сайті без сторонніх посилань та невідомих документів.<br>Для повного розуміння всіх можливостей додаємо карту навігації.<br><br><img src=\\\"https://espo.co.ua/uploads/posts/2020-06/1593381028_karta-sajta.png\\\" alt=\\\"\\\" class=\\\"fr-dib\\\" style=\\\"width:600px;\\\"><br><br><b>Ми піклуємось про вас та ваш час. Приємного та вдалого пошуку користувач!</b>', 0, 1, 'all', '', 'Здавалось, почувши слово гуртожиток будуть згадуватись веселі моменти, безсонні ночі за підручниками та відчуття безпеки,спокою та весело проведеного часу. Але в реальності згадки про нього наводять страх майже на всіх абітурієнтів та змушують нервувати студентів вже не один рік. Чому? Вся справа в', 'інформацію, можна, допомогою, декілька, перше, інформація, сайті, створити, подана, знайти, документів, гуртожитків, абітурієнтам, клітин, нервових, тисячу, студентам, зручний, громіздкий, карту', 30, '', 1593294035, '', 1, 1, 0, 0, ''),
(29, 'hostels-3-map', 'Маршрут проїзду', '<div class=\\\"googlemaps\\\"><iframe width=\\\"100%\\\" height=\\\"500\\\" frameborder=\\\"0\\\" scrolling=\\\"no\\\" marginheight=\\\"0\\\" marginwidth=\\\"0\\\" src=\\\"https://www.google.com/maps/embed?pb=!1m22!1m8!1m3!1d41012.220757052986!2d36.2000267!3d50.0251526!3m2!1i1024!2i768!4f13.1!4m11!3e3!4m5!1s0x4127a0e98e8e2a0d:0xfa916cfdbe03ae0a!2z0KPRh9C10LHQvdGL0Lkg0LouIOKEliAyINCd0KLQoyAi0KXQn9CYIiwg0LLRg9C70LjRhtGPINCa0LjRgNC_0LjRh9C-0LLQsCwgMiwg0KXQsNGA0LrRltCyLCDQpdCw0YDQutGW0LLRgdGM0LrQsCDQvtCx0LvQsNGB0YLRjCwgNjEwMDA!3m2!1d49.9983406!2d36.2478177!4m3!3m2!1d50.046220999999996!2d36.2069152!5e0!3m2!1suk!2sua!4v1489490747560\\\"></iframe></div>', 0, 1, 'all', 'hostel-3', 'Маршрут проезда общежития 2', 'Маршрут проезда общежития 3, маршрут проїзду', 5, '', 1593435495, '', 1, 1, 0, 0, ''),
(30, 'hostels-4-map', 'Маршрут проїзду', '<div class=\\\"googlemaps\\\"><iframe width=\\\"100%\\\" height=\\\"500\\\" frameborder=\\\"0\\\" scrolling=\\\"no\\\" marginheight=\\\"0\\\" marginwidth=\\\"0\\\" src=\\\"https://www.google.com/maps/embed?pb=!1m28!1m12!1m3!1d5128.951424562051!2d36.24485687797469!3d50.002438267664175!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m13!3e3!4m5!1s0x4127a0e98e8e2a0d:0xfa916cfdbe03ae0a!2z0KPRh9C10LHQvdGL0Lkg0LouIOKEliAyINCd0KLQoyAi0KXQn9CYIiwg0LLRg9C70LjRhtGPINCa0LjRgNC_0LjRh9C-0LLQsCwgMiwg0KXQsNGA0LrRltCyLCDQpdCw0YDQutGW0LLRgdGM0LrQsCDQvtCx0LvQsNGB0YLRjCwgNjEwMDA!3m2!1d49.9983406!2d36.2478177!4m5!1s0x4127a0db63d1230d:0x2f89f8bfd341b91c!2z0LLRg9C70LjRhtGPINCf0YPRiNC60ZbQvdGB0YzQutCwLCA3OS80LCDQpdCw0YDQutGW0LIsINCl0LDRgNC60ZbQstGB0YzQutCwINC-0LHQu9Cw0YHRgtGMLCA2MTAyNA!3m2!1d50.0065361!2d36.2468066!5e0!3m2!1suk!2sua!4v1489491414126\\\"></iframe></div>', 0, 1, 'all', 'hostel-4', 'Маршрут проезда общежития 4', 'Маршрут проезда общежития 4, маршрут проїзду', 12, '', 1593435786, '', 1, 1, 0, 0, ''),
(31, 'hostels-5-map', 'Маршрут проїзду', '<div class=\\\"googlemaps\\\"><iframe width=\\\"100%\\\" height=\\\"500\\\" frameborder=\\\"0\\\" scrolling=\\\"no\\\" marginheight=\\\"0\\\" marginwidth=\\\"0\\\" src=\\\"https://www.google.com/maps/embed?pb=!1m28!1m12!1m3!1d5128.981410155851!2d36.24456457797484!3d50.00215721766401!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m13!3e3!4m5!1s0x4127a0e98e8e2a0d:0xfa916cfdbe03ae0a!2z0KPRh9C10LHQvdGL0Lkg0LouIOKEliAyINCd0KLQoyAi0KXQn9CYIiwg0LLRg9C70LjRhtGPINCa0LjRgNC_0LjRh9C-0LLQsCwgMiwg0KXQsNGA0LrRltCyLCDQpdCw0YDQutGW0LLRgdGM0LrQsCDQvtCx0LvQsNGB0YLRjCwgNjEwMDA!3m2!1d49.9983406!2d36.2478177!4m5!1s0x4127a0dca77e6d1d:0x282269afa6ccb9c6!2z0J7QsdGJ0LXQttC40YLQuNC1IOKEljUg0J3QotCjICLQpdCf0JgiLCDQstGD0LvQuNGG0Y8g0J_Rg9GI0LrRltC90YHRjNC60LAsIDc5LzUsINCl0LDRgNC60ZbQsiwg0KXQsNGA0LrRltCy0YHRjNC60LAg0L7QsdC70LDRgdGC0YwsIDYxMDI0!3m2!1d50.005973999999995!2d36.2476345!5e0!3m2!1suk!2sua!4v1489491531164\\\"></iframe></div>', 0, 1, 'all', 'hostel-5', 'Маршрут проезда общежития 5', 'Маршрут проезда общежития 5, маршрут проїзду', 2, '', 1593435952, '', 1, 1, 0, 0, ''),
(32, 'hostels-6-map', 'Маршрут проїзду', '<div class=\\\"googlemaps\\\"><iframe width=\\\"100%\\\" height=\\\"500\\\" frameborder=\\\"0\\\" scrolling=\\\"no\\\" marginheight=\\\"0\\\" marginwidth=\\\"0\\\" src=\\\"https://www.google.com/maps/embed?pb=!1m24!1m8!1m3!1d5128.8881954349845!2d36.2469827!3d50.0030309!3m2!1i1024!2i768!4f13.1!4m13!3e2!4m5!1s0x4127a0e98e8e2a0d:0xfa916cfdbe03ae0a!2z0KPRh9C10LHQvdGL0Lkg0LouIOKEliAyINCd0KLQoyAi0KXQn9CYIiwg0LLRg9C70LjRhtGPINCa0LjRgNC_0LjRh9C-0LLQsCwgMiwg0KXQsNGA0LrRltCyLCDQpdCw0YDQutGW0LLRgdGM0LrQsCDQvtCx0LvQsNGB0YLRjCwgNjEwMDA!3m2!1d49.9983406!2d36.2478177!4m5!1s0x4127a0c5a9eb7a99:0x79a8cd66623ad76c!2z0KHRgtGD0LTQtdC90YLRgdGM0LrQsCDQstGD0LvQuNGG0Y8sIDE1LzE3LCDQpdCw0YDQutGW0LIsINCl0LDRgNC60ZbQstGB0YzQutCwINC-0LHQu9Cw0YHRgtGM!3m2!1d50.006480499999995!2d36.2557615!5e0!3m2!1suk!2sua!4v1489491565915\\\"></iframe></div>', 0, 1, 'all', 'hostel-6', 'Маршрут проезда общежития 6', 'Маршрут проезда общежития 6, маршрут проїзду', 7, '', 1593436194, '', 1, 1, 0, 0, ''),
(33, 'hostels-7-map', 'Маршрут проїзду', '<div class=\\\"googlemaps\\\"><iframe width=\\\"100%\\\" height=\\\"500\\\" frameborder=\\\"0\\\" scrolling=\\\"no\\\" marginheight=\\\"0\\\" marginwidth=\\\"0\\\" src=\\\"https://www.google.com/maps/embed?pb=!1m28!1m12!1m3!1d41012.80866530612!2d36.19340208832139!3d50.024464031102504!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m13!3e3!4m5!1s0x4127a0e98e8e2a0d:0xfa916cfdbe03ae0a!2z0KPRh9C10LHQvdGL0Lkg0LouIOKEliAyINCd0KLQoyAi0KXQn9CYIiwg0LLRg9C70LjRhtGPINCa0LjRgNC_0LjRh9C-0LLQsCwgMiwg0KXQsNGA0LrRltCyLCDQpdCw0YDQutGW0LLRgdGM0LrQsCDQvtCx0LvQsNGB0YLRjCwgNjEwMDA!3m2!1d49.9983406!2d36.2478177!4m5!1s0x4127a6abe4281361:0x8f4eb83cd39461e6!2z0J_QtdGA0LXQvNC-0LbQvdCwINCy0YPQu9C40YbRjywgMTksINCl0LDRgNC60ZbQsiwg0KXQsNGA0LrRltCy0YHRjNC60LAg0L7QsdC70LDRgdGC0Yw!3m2!1d50.0440705!2d36.206235!5e0!3m2!1suk!2sua!4v1489491594715\\\"></iframe></div>', 0, 1, 'all', 'hostel-7', 'Маршрут проезда общежития 7', 'Маршрут проезда общежития 7, маршрут проїзду', 7, '', 1593436481, '', 1, 1, 0, 0, ''),
(34, 'hostels-8-map', 'Маршрут проїзду', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m28!1m12!1m3!1d10257.434104186503!2d36.23154418344194!3d50.00463496211226!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m13!3e2!4m5!1s0x4127a0c201eba8df%3A0x4847f00cec333b6!2z0J3QsNGG0LjQvtC90LDQu9GM0L3Ri9C5INGC0LXRhdC90LjRh9C10YHQutC40Lkg0YPQvdC40LLQtdGA0YHQuNGC0LXRgiAi0KXQsNGA0YzQutC-0LLRgdC60LjQuSDQv9C-0LvQuNGC0LXRhdC90LjRh9C10YHQutC40Lkg0LjQvdGB0YLQuNGC0YPRgiIsINCy0YPQu9C40YbRjyDQmtC40YDQv9C40YfQvtCy0LAsIDIsINCl0LDRgNC60ZbQsiwg0KXQsNGA0LrRltCy0YHRjNC60LAg0L7QsdC70LDRgdGC0YwsIDYxMDAw!3m2!1d49.9990373!2d36.248402899999995!4m5!1s0x4127a120859e72d3%3A0xef9b3a7c0e4f9a19!2z0YPQuy4g0JvQuNGC0LXRgNCw0YLRg9GA0L3QsNGPLCAzLCDQpdCw0YDRjNC60L7Qsiwg0KXQsNGA0YzQutC-0LLRgdC60LDRjyDQvtCx0LvQsNGB0YLRjCwgNjEwMDA!3m2!1d50.009412499999996!2d36.2315699!5e0!3m2!1sru!2sua!4v1593436581920!5m2!1sru!2sua\\\" width=\\\"100%\\\" height=\\\"500\\\" frameborder=\\\"0\\\" style=\\\"border:0;\\\" allowfullscreen></iframe>', 0, 1, 'all', 'hostel-8', 'Маршрут проезда общежития 8', 'Маршрут проезда общежития 8, маршрут проїзду', 5, '', 1593436854, '', 1, 1, 0, 0, ''),
(35, 'hostels-10-map', 'Маршрут проїзду', '<div class=\\\"googlemaps\\\"><iframe width=\\\"100%\\\" height=\\\"500\\\" frameborder=\\\"0\\\" scrolling=\\\"no\\\" marginheight=\\\"0\\\" marginwidth=\\\"0\\\" src=\\\"https://www.google.com/maps/embed?pb=!1m22!1m8!1m3!1d10257.174035472337!2d36.2441861!3d50.0058537!3m2!1i1024!2i768!4f13.1!4m11!3e2!4m5!1s0x4127a0e98e8e2a0d:0xfa916cfdbe03ae0a!2z0KPRh9C10LHQvdGL0Lkg0LouIOKEliAyINCd0KLQoyAi0KXQn9CYIiwg0LLRg9C70LjRhtGPINCa0LjRgNC_0LjRh9C-0LLQsCwgMiwg0KXQsNGA0LrRltCyLCDQpdCw0YDQutGW0LLRgdGM0LrQsCDQvtCx0LvQsNGB0YLRjCwgNjEwMDA!3m2!1d49.9983406!2d36.2478177!4m3!3m2!1d50.0118234!2d36.2498943!5e0!3m2!1suk!2sua!4v1489491669362\\\"></iframe></div>', 0, 1, 'all', 'hostel-10', 'Маршрут проезда общежития 10', 'Маршрут проезда общежития 10, маршрут проїзду', 3, '', 1593437100, '', 1, 1, 0, 0, ''),
(36, 'hostels-9-map', 'Маршрут проїзду', '<div class=\\\"googlemaps\\\"><iframe width=\\\"100%\\\" height=\\\"500\\\" frameborder=\\\"0\\\" scrolling=\\\"no\\\" marginheight=\\\"0\\\" marginwidth=\\\"0\\\" src=\\\"https://www.google.com/maps/embed?pb=!1m22!1m8!1m3!1d41014.57877872811!2d36.2098095!3d50.0223908!3m2!1i1024!2i768!4f13.1!4m11!3e3!4m5!1s0x4127a0e98e8e2a0d:0xfa916cfdbe03ae0a!2z0KPRh9C10LHQvdGL0Lkg0LouIOKEliAyINCd0KLQoyAi0KXQn9CYIiwg0LLRg9C70LjRhtGPINCa0LjRgNC_0LjRh9C-0LLQsCwgMiwg0KXQsNGA0LrRltCyLCDQpdCw0YDQutGW0LLRgdGM0LrQsCDQvtCx0LvQsNGB0YLRjCwgNjEwMDA!3m2!1d49.9983406!2d36.2478177!4m3!3m2!1d50.0463186!2d36.203488199999995!5e0!3m2!1suk!2sua!4v1489491631995\\\"></iframe></div>', 0, 1, 'all', 'hostel-9', 'Маршрут проезда общежития 9', 'Маршрут проезда общежития 9, маршрут проїзду', 5, '', 1593437224, '', 1, 1, 0, 0, ''),
(37, 'hostels-11-map', 'Маршрут проїзду', '<div class=\\\"googlemaps\\\"><iframe width=\\\"100%\\\" height=\\\"500\\\" frameborder=\\\"0\\\" scrolling=\\\"no\\\" marginheight=\\\"0\\\" marginwidth=\\\"0\\\" src=\\\"https://www.google.com/maps/embed?pb=!1m22!1m8!1m3!1d41014.14659288061!2d36.2067633!3d50.022897!3m2!1i1024!2i768!4f13.1!4m11!3e3!4m5!1s0x4127a0e98e8e2a0d:0xfa916cfdbe03ae0a!2z0KPRh9C10LHQvdGL0Lkg0LouIOKEliAyINCd0KLQoyAi0KXQn9CYIiwg0LLRg9C70LjRhtGPINCa0LjRgNC_0LjRh9C-0LLQsCwgMiwg0KXQsNGA0LrRltCyLCDQpdCw0YDQutGW0LLRgdGM0LrQsCDQvtCx0LvQsNGB0YLRjCwgNjEwMDA!3m2!1d49.9983406!2d36.2478177!4m3!3m2!1d50.0475479!2d36.2046582!5e0!3m2!1suk!2sua!4v1489491709001\\\"></iframe></div>', 0, 1, 'all', 'hostel-11', 'Маршрут проезда общежития 11', 'Маршрут проезда общежития 11, маршрут проїзда', 6, '', 1593437403, '', 1, 1, 0, 0, '');
INSERT INTO `dle_static` (`id`, `name`, `descr`, `template`, `allow_br`, `allow_template`, `grouplevel`, `tpl`, `metadescr`, `metakeys`, `views`, `template_folder`, `date`, `metatitle`, `allow_count`, `sitemap`, `disable_index`, `disable_search`, `password`) VALUES
(38, 'hostels-12-map', 'Маршрут проїзду', '<div class=\\\"googlemaps\\\"><iframe width=\\\"100%\\\" height=\\\"500\\\" frameborder=\\\"0\\\" scrolling=\\\"no\\\" marginheight=\\\"0\\\" marginwidth=\\\"0\\\" src=\\\"https://www.google.com/maps/embed?pb=!1m24!1m8!1m3!1d41014.00810831466!2d36.1968451!3d50.0230592!3m2!1i1024!2i768!4f13.1!4m13!3e3!4m5!1s0x4127a0e98e8e2a0d:0xfa916cfdbe03ae0a!2z0KPRh9C10LHQvdGL0Lkg0LouIOKEliAyINCd0KLQoyAi0KXQn9CYIiwg0LLRg9C70LjRhtGPINCa0LjRgNC_0LjRh9C-0LLQsCwgMiwg0KXQsNGA0LrRltCyLCDQpdCw0YDQutGW0LLRgdGM0LrQsCDQvtCx0LvQsNGB0YLRjCwgNjEwMDA!3m2!1d49.9983406!2d36.2478177!4m5!1s0x4127a6aa291141c9:0xb684b423cd8a7bb4!2z0KbRltC70LjQvdC-0LPRgNCw0LTRgdGM0LrQsCDQstGD0LvQuNGG0Y8sIDMyLCDQpdCw0YDQutGW0LIsINCl0LDRgNC60ZbQstGB0YzQutCwINC-0LHQu9Cw0YHRgtGMLCA2MTAwMA!3m2!1d50.045181299999996!2d36.2037044!5e0!3m2!1suk!2sua!4v1489491926542\\\"></iframe></div>', 0, 1, 'all', 'hostel-12', 'Маршрут проезда общежития 12', 'Маршрут проезда общежития 12, маршрут проїзда', 4, '', 1593437642, '', 1, 1, 0, 0, ''),
(39, 'hostels-13-map', 'Маршрут проїзду', '<div class=\\\"googlemaps\\\"><iframe width=\\\"100%\\\" height=\\\"500\\\" frameborder=\\\"0\\\" scrolling=\\\"no\\\" marginheight=\\\"0\\\" marginwidth=\\\"0\\\" src=\\\"https://www.google.com/maps/embed?pb=!1m22!1m8!1m3!1d20511.47693019557!2d36.2186709!3d50.0125807!3m2!1i1024!2i768!4f13.1!4m11!3e3!4m5!1s0x4127a0e98e8e2a0d:0xfa916cfdbe03ae0a!2z0KPRh9C10LHQvdGL0Lkg0LouIOKEliAyINCd0KLQoyAi0KXQn9CYIiwg0LLRg9C70LjRhtGPINCa0LjRgNC_0LjRh9C-0LLQsCwgMiwg0KXQsNGA0LrRltCyLCDQpdCw0YDQutGW0LLRgdGM0LrQsCDQvtCx0LvQsNGB0YLRjCwgNjEwMDA!3m2!1d49.9983406!2d36.2478177!4m3!3m2!1d50.0234123!2d36.2120982!5e0!3m2!1suk!2sua!4v1489491986914\\\"></iframe></div>', 0, 1, 'all', 'hostel-13', 'Маршрут проезда общежития 13', 'Маршрут проезда общежития 13, маршурт проїзду', 5, '', 1593437875, '', 1, 1, 0, 0, ''),
(40, 'hostels-14-map', 'Маршрут проїзду', '<div class=\\\"googlemaps\\\"><iframe width=\\\"100%\\\" height=\\\"500\\\" frameborder=\\\"0\\\" scrolling=\\\"no\\\" marginheight=\\\"0\\\" marginwidth=\\\"0\\\" src=\\\"https://www.google.com/maps/embed?pb=!1m24!1m8!1m3!1d41033.1498405056!2d36.2654897!3d50.0006358!3m2!1i1024!2i768!4f13.1!4m13!3e3!4m5!1s0x4127a0e98e8e2a0d:0xfa916cfdbe03ae0a!2z0KPRh9C10LHQvdGL0Lkg0LouIOKEliAyINCd0KLQoyAi0KXQn9CYIiwg0LLRg9C70LjRhtGPINCa0LjRgNC_0LjRh9C-0LLQsCwgMiwg0KXQsNGA0LrRltCyLCDQpdCw0YDQutGW0LLRgdGM0LrQsCDQvtCx0LvQsNGB0YLRjCwgNjEwMDA!3m2!1d49.9983406!2d36.2478177!4m5!1s0x41270a2298cbe6d7:0x3518f3df84bf8f14!2z0LLRg9C70LjRhtGPINCR0LDQu9C60LDQvdGB0YzQutCwLCAxOSwg0KXQsNGA0LrRltCyLCDQpdCw0YDQutGW0LLRgdGM0LrQsCDQvtCx0LvQsNGB0YLRjA!3m2!1d49.984735!2d36.327236!5e0!3m2!1suk!2sua!4v1489492029194\\\"></iframe></div>', 0, 1, 'all', 'hostel-14', 'Маршрут проезда общежития 14', 'Маршрут проїзду, Маршрут проезда общежития 14', 6, '', 1593438116, '', 1, 1, 0, 0, ''),
(41, 'hostels-15-map', 'Маршрут проїзду', '<div class=\\\"googlemaps\\\"><iframe width=\\\"100%\\\" height=\\\"500\\\" frameborder=\\\"0\\\" scrolling=\\\"no\\\" marginheight=\\\"0\\\" marginwidth=\\\"0\\\" src=\\\"https://www.google.com/maps/embed?pb=!1m24!1m8!1m3!1d20514.738578870983!2d36.2645907!3d50.0049387!3m2!1i1024!2i768!4f13.1!4m13!3e3!4m5!1s0x4127a0e98e8e2a0d:0xfa916cfdbe03ae0a!2z0KPRh9C10LHQvdGL0Lkg0LouIOKEliAyINCd0KLQoyAi0KXQn9CYIiwg0LLRg9C70LjRhtGPINCa0LjRgNC_0LjRh9C-0LLQsCwgMiwg0KXQsNGA0LrRltCyLCDQpdCw0YDQutGW0LLRgdGM0LrQsCDQvtCx0LvQsNGB0YLRjCwgNjEwMDA!3m2!1d49.9983406!2d36.2478177!4m5!1s0x41270a038f6dbe73:0x336a1b70e09ba63e!2z0KHQv9C-0YDRgtC40LLQvdCwINCy0YPQu9C40YbRjywgOSwg0KXQsNGA0LrRltCyLCDQpdCw0YDQutGW0LLRgdGM0LrQsCDQvtCx0LvQsNGB0YLRjA!3m2!1d49.994766899999995!2d36.3127986!5e0!3m2!1suk!2sua!4v1489492060926\\\"></iframe></div>', 0, 1, 'all', 'hostel-15', 'Маршрут проезда общежития 15', 'Маршрут проїзду, Маршрут проезда общежития 15', 2, '', 1593438235, '', 1, 1, 0, 0, ''),
(42, 'pologennja', '«Положення про студентські гуртожитки НТУ «ХПІ»', '<div style=\\\"text-align: center;\\\"><iframe src=\\\"https://drive.google.com/file/d/15w5tF49gkDQlbooAhH9z3okKIENPflye/preview\\\" width=\\\"700px\\\" height=\\\"750px\\\"></iframe></div>', 2, 1, 'all', 'static_file', '«Положенням про студентські гуртожитки НТУ «ХПІ»', '«Положенням про студентські гуртожитки НТУ «ХПІ»', 17, '', 1595806429, '«Положенням про студентські гуртожитки НТУ «ХПІ»', 1, 1, 0, 0, '');

-- --------------------------------------------------------

--
-- Структура таблицы `dle_static_files`
--

CREATE TABLE `dle_static_files` (
  `id` int(11) NOT NULL,
  `static_id` int(11) NOT NULL DEFAULT '0',
  `author` varchar(40) NOT NULL DEFAULT '',
  `date` varchar(15) NOT NULL DEFAULT '',
  `name` varchar(200) NOT NULL DEFAULT '',
  `onserver` varchar(190) NOT NULL DEFAULT '',
  `dcount` int(11) NOT NULL DEFAULT '0',
  `size` bigint(20) NOT NULL DEFAULT '0',
  `checksum` char(32) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `dle_static_files`
--

INSERT INTO `dle_static_files` (`id`, `static_id`, `author`, `date`, `name`, `onserver`, `dcount`, `size`, `checksum`) VALUES
(1, 5, 'ADMIN', '1589844427', '2020-05/1589844444_dsc_3991-1.jpg', '', 0, 0, ''),
(2, 5, 'ADMIN', '1589845032', '2020-05/1589845050_hostel-1.jpg', '', 0, 0, ''),
(3, 5, 'ADMIN', '1589845168', '2020-05/1589845240_hostel-1.jpg', '', 0, 0, ''),
(4, 12, 'ADMIN', '1590429536', '2020-05/1590429604_p1240411-1024x545.jpg', '', 0, 0, ''),
(5, 12, 'ADMIN', '1590429632', '2020-05/1590429647_p1240445.jpg', '', 0, 0, ''),
(6, 10, 'ADMIN', '1590439224', '2020-05/1590439309_obshezhitie_02.jpg', '', 0, 0, ''),
(7, 11, 'ADMIN', '1591844769', '2020-06/1591844851_xxxl.jpg', '', 0, 0, ''),
(8, 17, 'ADMIN', '1591851574', '2020-06/1591851669_5.jpg', '', 0, 0, ''),
(9, 18, 'ADMIN', '1591851856', '2020-06/1591851936_dsc_1975.jpg', '', 0, 0, ''),
(10, 19, 'ADMIN', '1593246779', '2020-06/1593246787_img_7084.jpg', '', 0, 0, ''),
(11, 20, 'ADMIN', '1593247983', '2020-06/1593248082_snimok.png', '', 0, 0, ''),
(12, 21, 'ADMIN', '1593250336', '2020-06/1593250384_foto_17828.jpg', '', 0, 0, ''),
(13, 21, 'ADMIN', '1593250371', '2020-06/1593250430_x_22c8a434.jpg', '', 0, 0, ''),
(14, 22, 'ADMIN', '1593250742', '2020-06/1593250780_snimo1k.png', '', 0, 0, ''),
(15, 23, 'ADMIN', '1593251164', '2020-06/1593251227_dsc_2685-1024x685.jpg', '', 0, 0, ''),
(16, 24, 'ADMIN', '1593251871', '2020-06/1593251874_img_0028-12.jpg', '', 0, 0, ''),
(17, 25, 'ADMIN', '1593255422', '2020-06/1593255433_2017-10-17.jpg', '', 0, 0, ''),
(18, 26, 'ADMIN', '1593258424', '2020-06/1593258477_img_0267-1024x683.jpg', '', 0, 0, ''),
(19, 27, 'ADMIN', '1593259536', '2020-06/1593259588_sn2imok.png', '', 0, 0, ''),
(20, 16, 'ADMIN', '1593259996', '2020-06/1593260062_93_big.jpg', '', 0, 0, ''),
(21, 28, 'ADMIN', '1593293993', '2020-06/1593294021_karta-sajta.png', '', 0, 0, ''),
(22, 28, 'ADMIN', '1593380955', '2020-06/1593381028_karta-sajta.png', '', 0, 0, '');

-- --------------------------------------------------------

--
-- Структура таблицы `dle_subscribe`
--

CREATE TABLE `dle_subscribe` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(40) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `news_id` int(11) NOT NULL DEFAULT '0',
  `hash` varchar(32) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_tags`
--

CREATE TABLE `dle_tags` (
  `id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL DEFAULT '0',
  `tag` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_twofactor`
--

CREATE TABLE `dle_twofactor` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `pin` varchar(10) NOT NULL DEFAULT '',
  `attempt` tinyint(1) NOT NULL DEFAULT '0',
  `date` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_usergroups`
--

CREATE TABLE `dle_usergroups` (
  `id` smallint(5) NOT NULL,
  `group_name` varchar(50) NOT NULL DEFAULT '',
  `allow_cats` text NOT NULL,
  `allow_adds` tinyint(1) NOT NULL DEFAULT '1',
  `cat_add` text NOT NULL,
  `allow_admin` tinyint(1) NOT NULL DEFAULT '0',
  `allow_addc` tinyint(1) NOT NULL DEFAULT '0',
  `allow_editc` tinyint(1) NOT NULL DEFAULT '0',
  `allow_delc` tinyint(1) NOT NULL DEFAULT '0',
  `edit_allc` tinyint(1) NOT NULL DEFAULT '0',
  `del_allc` tinyint(1) NOT NULL DEFAULT '0',
  `moderation` tinyint(1) NOT NULL DEFAULT '0',
  `allow_all_edit` tinyint(1) NOT NULL DEFAULT '0',
  `allow_edit` tinyint(1) NOT NULL DEFAULT '0',
  `allow_pm` tinyint(1) NOT NULL DEFAULT '0',
  `max_pm` smallint(5) NOT NULL DEFAULT '0',
  `max_foto` varchar(10) NOT NULL DEFAULT '',
  `allow_files` tinyint(1) NOT NULL DEFAULT '0',
  `allow_hide` tinyint(1) NOT NULL DEFAULT '1',
  `allow_short` tinyint(1) NOT NULL DEFAULT '0',
  `time_limit` tinyint(1) NOT NULL DEFAULT '0',
  `rid` smallint(5) NOT NULL DEFAULT '0',
  `allow_fixed` tinyint(1) NOT NULL DEFAULT '0',
  `allow_feed` tinyint(1) NOT NULL DEFAULT '1',
  `allow_search` tinyint(1) NOT NULL DEFAULT '1',
  `allow_poll` tinyint(1) NOT NULL DEFAULT '1',
  `allow_main` tinyint(1) NOT NULL DEFAULT '1',
  `captcha` tinyint(1) NOT NULL DEFAULT '0',
  `icon` varchar(200) NOT NULL DEFAULT '',
  `allow_modc` tinyint(1) NOT NULL DEFAULT '0',
  `allow_rating` tinyint(1) NOT NULL DEFAULT '1',
  `allow_offline` tinyint(1) NOT NULL DEFAULT '0',
  `allow_image_upload` tinyint(1) NOT NULL DEFAULT '0',
  `allow_file_upload` tinyint(1) NOT NULL DEFAULT '0',
  `allow_signature` tinyint(1) NOT NULL DEFAULT '0',
  `allow_url` tinyint(1) NOT NULL DEFAULT '1',
  `news_sec_code` tinyint(1) NOT NULL DEFAULT '1',
  `allow_image` tinyint(1) NOT NULL DEFAULT '0',
  `max_signature` smallint(6) NOT NULL DEFAULT '0',
  `max_info` smallint(6) NOT NULL DEFAULT '0',
  `admin_addnews` tinyint(1) NOT NULL DEFAULT '0',
  `admin_editnews` tinyint(1) NOT NULL DEFAULT '0',
  `admin_comments` tinyint(1) NOT NULL DEFAULT '0',
  `admin_categories` tinyint(1) NOT NULL DEFAULT '0',
  `admin_editusers` tinyint(1) NOT NULL DEFAULT '0',
  `admin_wordfilter` tinyint(1) NOT NULL DEFAULT '0',
  `admin_xfields` tinyint(1) NOT NULL DEFAULT '0',
  `admin_userfields` tinyint(1) NOT NULL DEFAULT '0',
  `admin_static` tinyint(1) NOT NULL DEFAULT '0',
  `admin_editvote` tinyint(1) NOT NULL DEFAULT '0',
  `admin_newsletter` tinyint(1) NOT NULL DEFAULT '0',
  `admin_blockip` tinyint(1) NOT NULL DEFAULT '0',
  `admin_banners` tinyint(1) NOT NULL DEFAULT '0',
  `admin_rss` tinyint(1) NOT NULL DEFAULT '0',
  `admin_iptools` tinyint(1) NOT NULL DEFAULT '0',
  `admin_rssinform` tinyint(1) NOT NULL DEFAULT '0',
  `admin_googlemap` tinyint(1) NOT NULL DEFAULT '0',
  `allow_html` tinyint(1) NOT NULL DEFAULT '1',
  `group_prefix` text NOT NULL,
  `group_suffix` text NOT NULL,
  `allow_subscribe` tinyint(1) NOT NULL DEFAULT '0',
  `allow_image_size` tinyint(1) NOT NULL DEFAULT '0',
  `cat_allow_addnews` text NOT NULL,
  `flood_news` smallint(6) NOT NULL DEFAULT '0',
  `max_day_news` smallint(6) NOT NULL DEFAULT '0',
  `force_leech` tinyint(1) NOT NULL DEFAULT '0',
  `edit_limit` smallint(6) NOT NULL DEFAULT '0',
  `captcha_pm` tinyint(1) NOT NULL DEFAULT '0',
  `max_pm_day` smallint(6) NOT NULL DEFAULT '0',
  `max_mail_day` smallint(6) NOT NULL DEFAULT '0',
  `admin_tagscloud` tinyint(1) NOT NULL DEFAULT '0',
  `allow_vote` tinyint(1) NOT NULL DEFAULT '0',
  `admin_complaint` tinyint(1) NOT NULL DEFAULT '0',
  `news_question` tinyint(1) NOT NULL DEFAULT '0',
  `comments_question` tinyint(1) NOT NULL DEFAULT '0',
  `max_comment_day` smallint(6) NOT NULL DEFAULT '0',
  `max_images` smallint(6) NOT NULL DEFAULT '0',
  `max_files` smallint(6) NOT NULL DEFAULT '0',
  `disable_news_captcha` smallint(6) NOT NULL DEFAULT '0',
  `disable_comments_captcha` smallint(6) NOT NULL DEFAULT '0',
  `pm_question` tinyint(1) NOT NULL DEFAULT '0',
  `captcha_feedback` tinyint(1) NOT NULL DEFAULT '1',
  `feedback_question` tinyint(1) NOT NULL DEFAULT '0',
  `files_type` varchar(255) NOT NULL DEFAULT '',
  `max_file_size` mediumint(9) NOT NULL DEFAULT '0',
  `files_max_speed` smallint(6) NOT NULL DEFAULT '0',
  `spamfilter` tinyint(1) NOT NULL DEFAULT '2',
  `allow_comments_rating` tinyint(1) NOT NULL DEFAULT '1',
  `max_edit_days` tinyint(1) NOT NULL DEFAULT '0',
  `spampmfilter` tinyint(1) NOT NULL DEFAULT '0',
  `force_reg` tinyint(1) NOT NULL DEFAULT '0',
  `force_reg_days` mediumint(9) NOT NULL DEFAULT '0',
  `force_reg_group` smallint(6) NOT NULL DEFAULT '4',
  `force_news` tinyint(1) NOT NULL DEFAULT '0',
  `force_news_count` mediumint(9) NOT NULL DEFAULT '0',
  `force_news_group` smallint(6) NOT NULL DEFAULT '4',
  `force_comments` tinyint(1) NOT NULL DEFAULT '0',
  `force_comments_count` mediumint(9) NOT NULL DEFAULT '0',
  `force_comments_group` smallint(6) NOT NULL DEFAULT '4',
  `force_rating` tinyint(1) NOT NULL DEFAULT '0',
  `force_rating_count` mediumint(9) NOT NULL DEFAULT '0',
  `force_rating_group` smallint(6) NOT NULL DEFAULT '4',
  `not_allow_cats` text NOT NULL,
  `allow_up_image` tinyint(1) NOT NULL DEFAULT '0',
  `allow_up_watermark` tinyint(1) NOT NULL DEFAULT '0',
  `allow_up_thumb` tinyint(1) NOT NULL DEFAULT '0',
  `up_count_image` smallint(6) NOT NULL DEFAULT '0',
  `up_image_side` varchar(20) NOT NULL DEFAULT '',
  `up_image_size` mediumint(9) NOT NULL DEFAULT '0',
  `up_thumb_size` varchar(20) NOT NULL DEFAULT '',
  `allow_mail_files` tinyint(1) NOT NULL DEFAULT '0',
  `max_mail_files` smallint(6) NOT NULL DEFAULT '0',
  `max_mail_allfiles` mediumint(9) NOT NULL DEFAULT '0',
  `mail_files_type` varchar(100) NOT NULL DEFAULT '',
  `video_comments` tinyint(1) NOT NULL DEFAULT '0',
  `media_comments` tinyint(1) NOT NULL DEFAULT '0',
  `min_image_side` varchar(20) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `dle_usergroups`
--

INSERT INTO `dle_usergroups` (`id`, `group_name`, `allow_cats`, `allow_adds`, `cat_add`, `allow_admin`, `allow_addc`, `allow_editc`, `allow_delc`, `edit_allc`, `del_allc`, `moderation`, `allow_all_edit`, `allow_edit`, `allow_pm`, `max_pm`, `max_foto`, `allow_files`, `allow_hide`, `allow_short`, `time_limit`, `rid`, `allow_fixed`, `allow_feed`, `allow_search`, `allow_poll`, `allow_main`, `captcha`, `icon`, `allow_modc`, `allow_rating`, `allow_offline`, `allow_image_upload`, `allow_file_upload`, `allow_signature`, `allow_url`, `news_sec_code`, `allow_image`, `max_signature`, `max_info`, `admin_addnews`, `admin_editnews`, `admin_comments`, `admin_categories`, `admin_editusers`, `admin_wordfilter`, `admin_xfields`, `admin_userfields`, `admin_static`, `admin_editvote`, `admin_newsletter`, `admin_blockip`, `admin_banners`, `admin_rss`, `admin_iptools`, `admin_rssinform`, `admin_googlemap`, `allow_html`, `group_prefix`, `group_suffix`, `allow_subscribe`, `allow_image_size`, `cat_allow_addnews`, `flood_news`, `max_day_news`, `force_leech`, `edit_limit`, `captcha_pm`, `max_pm_day`, `max_mail_day`, `admin_tagscloud`, `allow_vote`, `admin_complaint`, `news_question`, `comments_question`, `max_comment_day`, `max_images`, `max_files`, `disable_news_captcha`, `disable_comments_captcha`, `pm_question`, `captcha_feedback`, `feedback_question`, `files_type`, `max_file_size`, `files_max_speed`, `spamfilter`, `allow_comments_rating`, `max_edit_days`, `spampmfilter`, `force_reg`, `force_reg_days`, `force_reg_group`, `force_news`, `force_news_count`, `force_news_group`, `force_comments`, `force_comments_count`, `force_comments_group`, `force_rating`, `force_rating_count`, `force_rating_group`, `not_allow_cats`, `allow_up_image`, `allow_up_watermark`, `allow_up_thumb`, `up_count_image`, `up_image_side`, `up_image_size`, `up_thumb_size`, `allow_mail_files`, `max_mail_files`, `max_mail_allfiles`, `mail_files_type`, `video_comments`, `media_comments`, `min_image_side`) VALUES
(1, 'Администраторы', 'all', 1, 'all', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 50, '101', 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 0, '{THEME}/images/icon_1.gif', 0, 1, 1, 1, 1, 1, 1, 0, 1, 500, 1000, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '<b><span style=\"color:red\">', '</span></b>', 1, 1, 'all', 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 'zip,rar,exe,doc,pdf,swf', 4096, 0, 2, 1, 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 1, 0, 0, 1, '', 1, 1, 1, 3, '800x600', 300, '200x150', 1, 3, 1000, 'jpg,png,zip,pdf', 1, 1, '10x10'),
(2, 'Главные редакторы', 'all', 1, 'all', 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 50, '101', 1, 1, 1, 0, 2, 1, 1, 1, 1, 1, 0, '{THEME}/images/icon_2.gif', 0, 1, 0, 1, 1, 1, 1, 0, 1, 500, 1000, 1, 1, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, '', '', 1, 1, 'all', 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 'zip,rar,exe,doc,pdf,swf', 4096, 0, 2, 1, 0, 0, 0, 0, 2, 0, 0, 2, 0, 0, 2, 0, 0, 2, '', 1, 1, 1, 3, '800x600', 300, '200x150', 1, 3, 1000, 'jpg,png,zip,pdf', 1, 1, '10x10'),
(3, 'Журналисты', 'all', 1, 'all', 1, 1, 1, 1, 0, 0, 1, 0, 1, 1, 50, '101', 1, 1, 1, 0, 3, 0, 1, 1, 1, 1, 0, '{THEME}/images/icon_3.gif', 0, 1, 0, 1, 1, 1, 1, 0, 1, 500, 1000, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, '', '', 1, 1, 'all', 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 'zip,rar,exe,doc,pdf,swf', 4096, 0, 2, 1, 0, 0, 0, 0, 3, 0, 0, 3, 0, 0, 3, 0, 0, 3, '', 1, 1, 1, 3, '800x600', 300, '200x150', 0, 3, 1000, 'jpg,png,zip,pdf', 1, 1, '10x10'),
(4, 'Посетители', 'all', 1, 'all', 0, 1, 1, 1, 0, 0, 0, 0, 0, 1, 20, '101', 1, 1, 1, 0, 4, 0, 1, 1, 1, 1, 0, '{THEME}/images/icon_4.gif', 0, 1, 0, 1, 0, 1, 1, 1, 0, 500, 1000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, '', '', 1, 0, 'all', 0, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 'zip,rar,exe,doc,pdf,swf', 4096, 0, 2, 1, 0, 2, 0, 0, 4, 0, 0, 4, 0, 0, 4, 0, 0, 4, '', 0, 0, 0, 1, '800x600', 300, '200x150', 0, 3, 1000, 'jpg,png,zip,pdf', 0, 0, '10x10'),
(5, 'Гости', 'all', 0, 'all', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0', 1, 0, 1, 0, 5, 0, 1, 1, 1, 0, 1, '{THEME}/images/icon_5.gif', 0, 1, 0, 0, 0, 0, 1, 1, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', 0, 0, 'all', 0, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, '', 0, 0, 2, 1, 0, 2, 0, 0, 5, 0, 0, 5, 0, 0, 5, 0, 0, 5, '', 0, 0, 0, 1, '800x600', 300, '200x150', 0, 3, 1000, 'jpg,png,zip,pdf', 0, 0, '10x10');

-- --------------------------------------------------------

--
-- Структура таблицы `dle_users`
--

CREATE TABLE `dle_users` (
  `email` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(40) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL,
  `news_num` mediumint(8) NOT NULL DEFAULT '0',
  `comm_num` mediumint(8) NOT NULL DEFAULT '0',
  `user_group` smallint(5) NOT NULL DEFAULT '4',
  `lastdate` varchar(20) NOT NULL DEFAULT '',
  `reg_date` varchar(20) NOT NULL DEFAULT '',
  `banned` varchar(5) NOT NULL DEFAULT '',
  `allow_mail` tinyint(1) NOT NULL DEFAULT '1',
  `info` text NOT NULL,
  `signature` text NOT NULL,
  `foto` varchar(255) NOT NULL DEFAULT '',
  `fullname` varchar(100) NOT NULL DEFAULT '',
  `land` varchar(100) NOT NULL DEFAULT '',
  `favorites` text NOT NULL,
  `pm_all` smallint(5) NOT NULL DEFAULT '0',
  `pm_unread` smallint(5) NOT NULL DEFAULT '0',
  `time_limit` varchar(20) NOT NULL DEFAULT '',
  `xfields` text NOT NULL,
  `allowed_ip` varchar(255) NOT NULL DEFAULT '',
  `hash` varchar(32) NOT NULL DEFAULT '',
  `logged_ip` varchar(46) NOT NULL DEFAULT '',
  `restricted` tinyint(1) NOT NULL DEFAULT '0',
  `restricted_days` smallint(4) NOT NULL DEFAULT '0',
  `restricted_date` varchar(15) NOT NULL DEFAULT '',
  `timezone` varchar(100) NOT NULL DEFAULT '',
  `news_subscribe` tinyint(1) NOT NULL DEFAULT '0',
  `comments_reply_subscribe` tinyint(1) NOT NULL DEFAULT '0',
  `twofactor_auth` tinyint(1) NOT NULL DEFAULT '0',
  `cat_add` varchar(500) NOT NULL DEFAULT '',
  `cat_allow_addnews` varchar(500) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `dle_users`
--

INSERT INTO `dle_users` (`email`, `password`, `name`, `user_id`, `news_num`, `comm_num`, `user_group`, `lastdate`, `reg_date`, `banned`, `allow_mail`, `info`, `signature`, `foto`, `fullname`, `land`, `favorites`, `pm_all`, `pm_unread`, `time_limit`, `xfields`, `allowed_ip`, `hash`, `logged_ip`, `restricted`, `restricted_days`, `restricted_date`, `timezone`, `news_subscribe`, `comments_reply_subscribe`, `twofactor_auth`, `cat_add`, `cat_allow_addnews`) VALUES
('metimbios@gmail.com', '$2y$10$M5eNPIEK5UiiqV40nL5egOlOcTnMD1PdLnso59TJNIeeIlwreRTzu', 'ADMIN', 1, 2, 0, 1, '1605307236', '1589592632', '', 1, '', '', '', '', '', '', 0, 0, '', '', '', 'a27fa98f6321c85adcec36a8739fc38b', '31.133.81.157', 0, 0, '', '', 0, 0, 0, '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `dle_views`
--

CREATE TABLE `dle_views` (
  `id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dle_vote`
--

CREATE TABLE `dle_vote` (
  `id` mediumint(8) NOT NULL,
  `category` text NOT NULL,
  `vote_num` mediumint(8) NOT NULL DEFAULT '0',
  `date` varchar(25) NOT NULL DEFAULT '0',
  `title` varchar(200) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `approve` tinyint(1) NOT NULL DEFAULT '1',
  `start` varchar(15) NOT NULL DEFAULT '',
  `end` varchar(15) NOT NULL DEFAULT '',
  `grouplevel` varchar(250) NOT NULL DEFAULT 'all'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `dle_vote`
--

INSERT INTO `dle_vote` (`id`, `category`, `vote_num`, `date`, `title`, `body`, `approve`, `start`, `end`, `grouplevel`) VALUES
(1, 'all', 3, '2020-05-16 04:30:32', 'Оцените работу движка', 'Лучший из новостных<br>Неплохой движок<br>Устраивает ... но ...<br>Встречал и получше<br>Совсем не понравился', 1, '', '', 'all');

-- --------------------------------------------------------

--
-- Структура таблицы `dle_vote_result`
--

CREATE TABLE `dle_vote_result` (
  `id` int(10) NOT NULL,
  `ip` varchar(46) NOT NULL DEFAULT '',
  `name` varchar(40) NOT NULL DEFAULT '',
  `vote_id` mediumint(8) NOT NULL DEFAULT '0',
  `answer` tinyint(3) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `dle_vote_result`
--

INSERT INTO `dle_vote_result` (`id`, `ip`, `name`, `vote_id`, `answer`) VALUES
(1, '193.110.169.218', 'ADMIN', 1, 1),
(2, '77.120.182.191', 'guest', 1, 4),
(3, '31.133.84.220', 'guest', 1, 4);

-- --------------------------------------------------------

--
-- Структура таблицы `dle_xfsearch`
--

CREATE TABLE `dle_xfsearch` (
  `id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL DEFAULT '0',
  `tagname` varchar(50) NOT NULL DEFAULT '',
  `tagvalue` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `dle_admin_logs`
--
ALTER TABLE `dle_admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `date` (`date`);

--
-- Индексы таблицы `dle_admin_sections`
--
ALTER TABLE `dle_admin_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `dle_banned`
--
ALTER TABLE `dle_banned`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`users_id`);

--
-- Индексы таблицы `dle_banners`
--
ALTER TABLE `dle_banners`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dle_banners_logs`
--
ALTER TABLE `dle_banners_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bid` (`bid`),
  ADD KEY `ip` (`ip`);

--
-- Индексы таблицы `dle_banners_rubrics`
--
ALTER TABLE `dle_banners_rubrics`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dle_category`
--
ALTER TABLE `dle_category`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dle_comments`
--
ALTER TABLE `dle_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `approve` (`approve`),
  ADD KEY `parent` (`parent`),
  ADD KEY `rating` (`rating`);
ALTER TABLE `dle_comments` ADD FULLTEXT KEY `text` (`text`);

--
-- Индексы таблицы `dle_comments_files`
--
ALTER TABLE `dle_comments_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `c_id` (`c_id`),
  ADD KEY `author` (`author`);

--
-- Индексы таблицы `dle_comment_rating_log`
--
ALTER TABLE `dle_comment_rating_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `c_id` (`c_id`),
  ADD KEY `member` (`member`),
  ADD KEY `ip` (`ip`);

--
-- Индексы таблицы `dle_complaint`
--
ALTER TABLE `dle_complaint`
  ADD PRIMARY KEY (`id`),
  ADD KEY `c_id` (`c_id`),
  ADD KEY `p_id` (`p_id`),
  ADD KEY `n_id` (`n_id`);

--
-- Индексы таблицы `dle_email`
--
ALTER TABLE `dle_email`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dle_files`
--
ALTER TABLE `dle_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`);

--
-- Индексы таблицы `dle_flood`
--
ALTER TABLE `dle_flood`
  ADD PRIMARY KEY (`f_id`),
  ADD KEY `ip` (`ip`),
  ADD KEY `id` (`id`),
  ADD KEY `flag` (`flag`);

--
-- Индексы таблицы `dle_ignore_list`
--
ALTER TABLE `dle_ignore_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`),
  ADD KEY `user_from` (`user_from`);

--
-- Индексы таблицы `dle_images`
--
ALTER TABLE `dle_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author` (`author`),
  ADD KEY `news_id` (`news_id`);

--
-- Индексы таблицы `dle_links`
--
ALTER TABLE `dle_links`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dle_login_log`
--
ALTER TABLE `dle_login_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip` (`ip`),
  ADD KEY `date` (`date`);

--
-- Индексы таблицы `dle_logs`
--
ALTER TABLE `dle_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`),
  ADD KEY `member` (`member`),
  ADD KEY `ip` (`ip`);

--
-- Индексы таблицы `dle_lostdb`
--
ALTER TABLE `dle_lostdb`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lostid` (`lostid`);

--
-- Индексы таблицы `dle_mail_log`
--
ALTER TABLE `dle_mail_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hash` (`hash`);

--
-- Индексы таблицы `dle_metatags`
--
ALTER TABLE `dle_metatags`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dle_notice`
--
ALTER TABLE `dle_notice`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `dle_plugins`
--
ALTER TABLE `dle_plugins`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dle_plugins_files`
--
ALTER TABLE `dle_plugins_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plugin_id` (`plugin_id`),
  ADD KEY `active` (`active`);

--
-- Индексы таблицы `dle_plugins_logs`
--
ALTER TABLE `dle_plugins_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plugin_id` (`plugin_id`);

--
-- Индексы таблицы `dle_pm`
--
ALTER TABLE `dle_pm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `folder` (`folder`),
  ADD KEY `user` (`user`),
  ADD KEY `user_from` (`user_from`),
  ADD KEY `pm_read` (`pm_read`);

--
-- Индексы таблицы `dle_poll`
--
ALTER TABLE `dle_poll`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`);

--
-- Индексы таблицы `dle_poll_log`
--
ALTER TABLE `dle_poll_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`),
  ADD KEY `member` (`member`);

--
-- Индексы таблицы `dle_post`
--
ALTER TABLE `dle_post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `autor` (`autor`),
  ADD KEY `alt_name` (`alt_name`),
  ADD KEY `category` (`category`),
  ADD KEY `approve` (`approve`),
  ADD KEY `allow_main` (`allow_main`),
  ADD KEY `date` (`date`),
  ADD KEY `symbol` (`symbol`),
  ADD KEY `comm_num` (`comm_num`),
  ADD KEY `fixed` (`fixed`);
ALTER TABLE `dle_post` ADD FULLTEXT KEY `short_story` (`short_story`,`full_story`,`xfields`,`title`);

--
-- Индексы таблицы `dle_post_extras`
--
ALTER TABLE `dle_post_extras`
  ADD PRIMARY KEY (`eid`),
  ADD KEY `news_id` (`news_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `rating` (`rating`),
  ADD KEY `disable_search` (`disable_search`),
  ADD KEY `allow_rss` (`allow_rss`),
  ADD KEY `news_read` (`news_read`);

--
-- Индексы таблицы `dle_post_extras_cats`
--
ALTER TABLE `dle_post_extras_cats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`),
  ADD KEY `cat_id` (`cat_id`);

--
-- Индексы таблицы `dle_post_log`
--
ALTER TABLE `dle_post_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`),
  ADD KEY `expires` (`expires`);

--
-- Индексы таблицы `dle_post_pass`
--
ALTER TABLE `dle_post_pass`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`);

--
-- Индексы таблицы `dle_question`
--
ALTER TABLE `dle_question`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dle_read_log`
--
ALTER TABLE `dle_read_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`),
  ADD KEY `ip` (`ip`);

--
-- Индексы таблицы `dle_redirects`
--
ALTER TABLE `dle_redirects`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dle_rss`
--
ALTER TABLE `dle_rss`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dle_rssinform`
--
ALTER TABLE `dle_rssinform`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dle_sendlog`
--
ALTER TABLE `dle_sendlog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`),
  ADD KEY `date` (`date`),
  ADD KEY `flag` (`flag`);

--
-- Индексы таблицы `dle_social_login`
--
ALTER TABLE `dle_social_login`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sid` (`sid`);

--
-- Индексы таблицы `dle_spam_log`
--
ALTER TABLE `dle_spam_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip` (`ip`),
  ADD KEY `is_spammer` (`is_spammer`);

--
-- Индексы таблицы `dle_static`
--
ALTER TABLE `dle_static`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `disable_search` (`disable_search`);
ALTER TABLE `dle_static` ADD FULLTEXT KEY `template` (`template`);

--
-- Индексы таблицы `dle_static_files`
--
ALTER TABLE `dle_static_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `static_id` (`static_id`),
  ADD KEY `onserver` (`onserver`),
  ADD KEY `author` (`author`);

--
-- Индексы таблицы `dle_subscribe`
--
ALTER TABLE `dle_subscribe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `dle_tags`
--
ALTER TABLE `dle_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`),
  ADD KEY `tag` (`tag`);

--
-- Индексы таблицы `dle_twofactor`
--
ALTER TABLE `dle_twofactor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pin` (`pin`),
  ADD KEY `date` (`date`);

--
-- Индексы таблицы `dle_usergroups`
--
ALTER TABLE `dle_usergroups`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dle_users`
--
ALTER TABLE `dle_users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Индексы таблицы `dle_views`
--
ALTER TABLE `dle_views`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dle_vote`
--
ALTER TABLE `dle_vote`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approve` (`approve`);

--
-- Индексы таблицы `dle_vote_result`
--
ALTER TABLE `dle_vote_result`
  ADD PRIMARY KEY (`id`),
  ADD KEY `answer` (`answer`),
  ADD KEY `vote_id` (`vote_id`),
  ADD KEY `ip` (`ip`),
  ADD KEY `name` (`name`);

--
-- Индексы таблицы `dle_xfsearch`
--
ALTER TABLE `dle_xfsearch`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`),
  ADD KEY `tagname` (`tagname`),
  ADD KEY `tagvalue` (`tagvalue`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `dle_admin_logs`
--
ALTER TABLE `dle_admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=325;

--
-- AUTO_INCREMENT для таблицы `dle_admin_sections`
--
ALTER TABLE `dle_admin_sections`
  MODIFY `id` mediumint(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_banned`
--
ALTER TABLE `dle_banned`
  MODIFY `id` smallint(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_banners`
--
ALTER TABLE `dle_banners`
  MODIFY `id` smallint(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `dle_banners_logs`
--
ALTER TABLE `dle_banners_logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_banners_rubrics`
--
ALTER TABLE `dle_banners_rubrics`
  MODIFY `id` mediumint(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_category`
--
ALTER TABLE `dle_category`
  MODIFY `id` mediumint(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `dle_comments`
--
ALTER TABLE `dle_comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `dle_comments_files`
--
ALTER TABLE `dle_comments_files`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_comment_rating_log`
--
ALTER TABLE `dle_comment_rating_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_complaint`
--
ALTER TABLE `dle_complaint`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_email`
--
ALTER TABLE `dle_email`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `dle_files`
--
ALTER TABLE `dle_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_flood`
--
ALTER TABLE `dle_flood`
  MODIFY `f_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_ignore_list`
--
ALTER TABLE `dle_ignore_list`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_images`
--
ALTER TABLE `dle_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `dle_links`
--
ALTER TABLE `dle_links`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_login_log`
--
ALTER TABLE `dle_login_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `dle_logs`
--
ALTER TABLE `dle_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `dle_lostdb`
--
ALTER TABLE `dle_lostdb`
  MODIFY `id` mediumint(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_mail_log`
--
ALTER TABLE `dle_mail_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_metatags`
--
ALTER TABLE `dle_metatags`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_notice`
--
ALTER TABLE `dle_notice`
  MODIFY `id` mediumint(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_plugins`
--
ALTER TABLE `dle_plugins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_plugins_files`
--
ALTER TABLE `dle_plugins_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_plugins_logs`
--
ALTER TABLE `dle_plugins_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_pm`
--
ALTER TABLE `dle_pm`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_poll`
--
ALTER TABLE `dle_poll`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_poll_log`
--
ALTER TABLE `dle_poll_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_post`
--
ALTER TABLE `dle_post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `dle_post_extras`
--
ALTER TABLE `dle_post_extras`
  MODIFY `eid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `dle_post_extras_cats`
--
ALTER TABLE `dle_post_extras_cats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `dle_post_log`
--
ALTER TABLE `dle_post_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_post_pass`
--
ALTER TABLE `dle_post_pass`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_question`
--
ALTER TABLE `dle_question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_read_log`
--
ALTER TABLE `dle_read_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_redirects`
--
ALTER TABLE `dle_redirects`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_rss`
--
ALTER TABLE `dle_rss`
  MODIFY `id` smallint(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `dle_rssinform`
--
ALTER TABLE `dle_rssinform`
  MODIFY `id` smallint(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `dle_sendlog`
--
ALTER TABLE `dle_sendlog`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_social_login`
--
ALTER TABLE `dle_social_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_spam_log`
--
ALTER TABLE `dle_spam_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_static`
--
ALTER TABLE `dle_static`
  MODIFY `id` mediumint(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT для таблицы `dle_static_files`
--
ALTER TABLE `dle_static_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT для таблицы `dle_subscribe`
--
ALTER TABLE `dle_subscribe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_tags`
--
ALTER TABLE `dle_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_twofactor`
--
ALTER TABLE `dle_twofactor`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_usergroups`
--
ALTER TABLE `dle_usergroups`
  MODIFY `id` smallint(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `dle_users`
--
ALTER TABLE `dle_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `dle_views`
--
ALTER TABLE `dle_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dle_vote`
--
ALTER TABLE `dle_vote`
  MODIFY `id` mediumint(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `dle_vote_result`
--
ALTER TABLE `dle_vote_result`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `dle_xfsearch`
--
ALTER TABLE `dle_xfsearch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
