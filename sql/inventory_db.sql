-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jul 11, 2025 at 10:02 AM
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
-- Database: `inventory_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `code` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `code`, `name`, `description`, `location`, `status`, `created_at`) VALUES
(1, 'A-0010-Z', 'Сака', 'Бяли и черни, S, M, L размер', 'Склад', 'За обличане', '2025-07-08 10:18:32'),
(2, 'ABC-abc-1234', 'Панталони', 'Кафяви и черни, с копчетa и с цип', 'Склад', 'За почистване', '2025-07-08 18:49:53'),
(3, 'A-0012-Z', 'Ризи', 'Бели и черни', 'Отдел за гладене', 'За гладене', '2025-07-08 22:17:28'),
(4, 'A-0011-Y', 'Сака', 'Бежеви и сини, с два джоба', 'Шивално', 'За шиене', '2025-07-09 00:35:00');

-- --------------------------------------------------------

--
-- Table structure for table `operations`
--

CREATE TABLE `operations` (
  `id` int(11) NOT NULL,
  `inventory_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `operations`
--

INSERT INTO `operations` (`id`, `inventory_id`, `user_id`, `start_time`, `end_time`, `description`) VALUES
(87, 2, 459791251, '2025-07-10 18:16:58', '2025-07-10 18:17:10', 'Гладене'),
(88, 3, 459791251, '2025-07-11 08:04:37', '2025-07-11 08:04:50', 'Гладене'),
(89, 3, 459791251, '2025-07-11 08:45:24', '2025-07-11 08:45:30', 'Шиене'),
(90, 4, 459791251, '2025-07-11 08:45:30', '2025-07-11 08:45:37', 'Шиене'),
(91, 1, 312077783, '2025-07-11 08:55:20', '2025-07-11 08:55:27', 'Обличане'),
(93, 4, 459791251, '2025-07-11 08:56:38', '2025-07-11 08:56:40', 'Почистване'),
(94, 1, 459791251, '2025-07-11 10:35:46', '2025-07-11 10:36:44', 'Гладене');

-- --------------------------------------------------------

--
-- Table structure for table `operations_prices`
--

CREATE TABLE `operations_prices` (
  `id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `price` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `operations_prices`
--

INSERT INTO `operations_prices` (`id`, `name`, `type`, `price`) VALUES
(1, 'Гладене', 'На час', 8.5),
(2, 'Почистване', 'На час', 5.8),
(3, 'Шиене', 'На бройка', 10),
(4, 'Обличане', 'На час', 6.6);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `status`, `image`) VALUES
(312077783, 'Исмаил Летиф', 'ismail.letif@pmggd.bg', '$2y$10$zWBqffmZ6V3s8GRDA1LSvuhmSn4ndGwCgLXoIBvjIL7/ACVdewhrW', '2025-07-11 07:54:08', 'Работник', 'uploads/user_6870a7007569b5.99078987.jpeg'),
(459791251, 'Юсуф Капанък', 'yusuf.kapanak@pmggd.bg', '$2y$10$G2YBFGRrX0uR5s5oAekDT.f8R7hSa9ixRYChm/r62kybPYvMzCjFi', '2025-07-08 10:57:14', 'Работник', 'uploads/user_686f73ddea3f18.48443594.webp'),
(837089647, 'Aхмед Бакиев', 'ahmed.bakiev@pmggd.bg', '$2y$10$or2MqkyizG7KKg6fF.GjLecJkqnXQkVxG.w2wmaSUnv9cLjjy/Q8.', '2025-07-10 08:57:31', 'Админ', 'uploads/user_686f645b931a60.01777413.jpg'),
(1217509289, 'Веселин Пелтеков', 'veselin.peltekov@pmggd.bg', '$2y$10$oGwReOvLITygXGrrTbYiw.gyQSpM4xcMTD4bpGa7KhYoNKt91gCH.', '2025-07-10 08:55:46', 'Админ', 'uploads/user_686f63f2b8b688.78913611.jpg'),
(1326605994, 'Кирил Медарев', 'kiril.medarev@pmggd.bg', '$2y$10$U8hvhW1Yqx15Xe0vhdeZLenAN.eJJuMu.R.iGEsQK0J.6mSg7zr3K', '2025-07-10 07:48:43', 'Админ', 'uploads/user_686f746c20ad33.04731460.jpg'),
(1568504295, 'Администратор', 'admin@pmggd.bg', '$2y$10$ta5Y22WadkZbkuHSKWLiguu2cHsJlL0C6q3.atAR3Gc9m/ikJsUFO', '2025-07-09 16:29:40', 'Админ', 'uploads/user_686e744cb7b778.66657285.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `operations`
--
ALTER TABLE `operations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_id` (`inventory_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `operations_prices`
--
ALTER TABLE `operations_prices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `operations`
--
ALTER TABLE `operations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `operations_prices`
--
ALTER TABLE `operations_prices`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `operations`
--
ALTER TABLE `operations`
  ADD CONSTRAINT `operations_ibfk_1` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`),
  ADD CONSTRAINT `operations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
