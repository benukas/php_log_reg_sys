-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2024 at 04:51 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `formosdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `city_id` int(11) NOT NULL,
  `city_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`city_id`, `city_name`) VALUES
(1, 'Vilnius'),
(2, 'Kaunas'),
(3, 'Klaipėda'),
(4, 'Šiauliai'),
(5, 'Panevėžys'),
(6, 'Alytus'),
(7, 'Marijampolė'),
(8, 'Mažeikiai'),
(9, 'Utena'),
(10, 'Jonava'),
(11, 'Kėdainiai'),
(12, 'Telšiai'),
(13, 'Tauragė'),
(14, 'Ukmergė'),
(15, 'Visaginas'),
(16, 'Palanga'),
(17, 'Plungė'),
(18, 'Kretinga'),
(19, 'Šilutė'),
(20, 'Radviliškis'),
(21, 'Gargždai'),
(22, 'Druskininkai'),
(23, 'Elektrėnai'),
(24, 'Jurbarkas'),
(25, 'Rokiškis'),
(26, 'Kuršėnai'),
(27, 'Biržai'),
(28, 'Vilkaviškis'),
(29, 'Garliava'),
(30, 'Grigiškės'),
(31, 'Lentvaris'),
(32, 'Raseiniai'),
(33, 'Prienai'),
(34, 'Anykščiai'),
(35, 'Kaišiadorys'),
(36, 'Joniškis'),
(37, 'Naujoji Akmenė'),
(38, 'Varėna'),
(39, 'Kelmė'),
(40, 'Šalčininkai'),
(41, 'Pasvalys'),
(42, 'Kupiškis'),
(43, 'Zarasai'),
(44, 'Trakai'),
(45, 'Širvintos'),
(46, 'Molėtai'),
(47, 'Kazlų Rūda'),
(48, 'Šakiai'),
(49, 'Skuodas'),
(50, 'Ignalina'),
(51, 'Pabradė'),
(52, 'Šilalė'),
(53, 'Švenčionėliai'),
(54, 'Nemenčinė'),
(55, 'Pakruojis'),
(56, 'Švenčionys'),
(57, 'Neringa'),
(58, 'Vievis'),
(59, 'Kalvarija'),
(60, 'Kybartai'),
(61, 'Lazdijai'),
(62, 'Rietavas'),
(63, 'Birštonas'),
(64, 'Žiežmariai'),
(65, 'Eišiškės'),
(66, 'Ariogala'),
(67, 'Šeduva'),
(68, 'Akmenė'),
(69, 'Venta'),
(70, 'Viekšniai'),
(71, 'Rūdiškės'),
(72, 'Tytuvėnai'),
(73, 'Vilkija'),
(74, 'Ežerėlis'),
(75, 'Pagėgiai'),
(76, 'Gelgaudiškis'),
(77, 'Skaudvilė'),
(78, 'Kudirkos Naumiestis'),
(79, 'Žagarė'),
(80, 'Priekulė'),
(81, 'Linkuva'),
(82, 'Salantai'),
(83, 'Ramygala'),
(84, 'Simnas'),
(85, 'Veisiejai'),
(86, 'Jieznas');

-- --------------------------------------------------------

--
-- Table structure for table `hidden_users`
--

CREATE TABLE `hidden_users` (
  `person_id` int(11) NOT NULL,
  `person_username` varchar(255) NOT NULL,
  `person_city` varchar(255) NOT NULL,
  `person_password_encrypt` varchar(255) NOT NULL,
  `person_hobbies` varchar(255) DEFAULT NULL,
  `hobby_id` int(11) NOT NULL,
  `person_about_me` text DEFAULT NULL,
  `person_city_id` int(11) NOT NULL,
  `is_hidden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hobbies`
--

CREATE TABLE `hobbies` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hobbies`
--

INSERT INTO `hobbies` (`id`, `name`) VALUES
(1, 'Futbolas'),
(2, 'Krepsinis'),
(3, 'Tenisas'),
(4, 'Rankinis'),
(5, 'Automobiliai'),
(6, 'Autobusai');

-- --------------------------------------------------------

--
-- Table structure for table `person_info`
--

CREATE TABLE `person_info` (
  `person_id` int(10) NOT NULL,
  `person_city` text NOT NULL,
  `person_username` varchar(255) NOT NULL,
  `person_password_encrypt` varchar(255) NOT NULL,
  `person_about_me` varchar(255) NOT NULL,
  `hobby_id` int(11) NOT NULL,
  `person_hobbies` varchar(255) NOT NULL,
  `is_hidden` int(11) NOT NULL,
  `person_city_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `person_info`
--

INSERT INTO `person_info` (`person_id`, `person_city`, `person_username`, `person_password_encrypt`, `person_about_me`, `hobby_id`, `person_hobbies`, `is_hidden`, `person_city_id`) VALUES
(13, 'Jonava', 'admin', '$2y$10$lZJQ9r4.UvhQacVE4QRV3u/UvrZU6/2ol5aCCYLQB9MQ8hJYKNSWq', 'abcd', 0, '1', 0, 15);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`city_id`);

--
-- Indexes for table `hidden_users`
--
ALTER TABLE `hidden_users`
  ADD PRIMARY KEY (`person_id`);

--
-- Indexes for table `hobbies`
--
ALTER TABLE `hobbies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `person_info`
--
ALTER TABLE `person_info`
  ADD PRIMARY KEY (`person_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `hidden_users`
--
ALTER TABLE `hidden_users`
  MODIFY `person_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `hobbies`
--
ALTER TABLE `hobbies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `person_info`
--
ALTER TABLE `person_info`
  MODIFY `person_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
