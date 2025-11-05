-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 05, 2025 at 12:16 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `expiry_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `retailer_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `expiry_date` date NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `added_by` int(11) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `retailer_name`, `quantity`, `expiry_date`, `category`, `remarks`, `added_by`, `added_on`) VALUES
(1, 'tropicana', 'saiSales', 1, '2025-11-03', '', '', 9, '2025-11-04 17:54:19'),
(2, 'fruity', 'hrSale', 4, '2025-10-29', 'expired', 'already expire', 9, '2025-11-04 19:11:35'),
(3, 'fanta', 'saiSales', 1, '2025-11-04', 'test category', 'test remark', 8, '2025-11-04 20:01:20'),
(4, 'dew', 'saiSales', 8, '2025-11-04', 'test category', 'testtest', 8, '2025-11-04 20:30:31'),
(5, 'dew', 'saiSales', 4, '2025-11-04', '', '', 8, '2025-11-04 20:35:39'),
(6, 'test', 'test2', 2, '2025-11-02', '', '', 8, '2025-11-04 20:41:16'),
(7, 'fanta', 'testfanta', 1, '2025-11-04', '', '', 8, '2025-11-04 21:32:49'),
(8, 'hello world', 'world', 1, '2025-11-04', '', '', 9, '2025-11-04 21:55:28');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','salesman') NOT NULL DEFAULT 'salesman',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `is_blocked`, `reset_token`, `reset_expires`) VALUES
(1, 'Super Admin', 'admin@company.com', '$2y$10$cwazBa7MP.5YoTxi5bx81e56wjWDiFrSEt3O3vNT8uRO8BEigqVFW', 'admin', '2025-11-04 16:04:29', 0, '962cdbd3e9e968616aa81ae98c72c3bd43d81d5fc05a7908c1ae7560e9a90950', '2025-11-05 00:41:41'),
(6, 'tukaram', 'tukara@g', '$2y$10$RkaaGUfU2x2em1BWP.i2f.5MUNcifv3VTpkXfK8sxG1w4ciD9OkjC', 'salesman', '2025-11-04 17:14:25', 0, NULL, NULL),
(7, 'tukaram', 'tukaram@gmail.com', '$2y$10$V3NgZSaJpIib803zMARwZuMw1bsYwHEpERmR0fBEH2hwuFy5Xt5Z6', 'salesman', '2025-11-04 17:14:57', 0, NULL, NULL),
(8, 'test', 'test@gmail.com', '$2y$10$TlaLZ6U1m9YbZC3kMJvvl.j343nt6b7T.bvnx5KgFRUheBsdsWlDC', 'salesman', '2025-11-04 17:22:11', 0, NULL, NULL),
(9, 'test2', 'test2@gmail.com', '$2y$10$n1PJ/tOm7DfvxT8ThTDit.0W50yyFvnnt0yZtcHMEwKJmEh./eAoS', 'salesman', '2025-11-04 17:52:55', 0, '88914ba8ed184c37b3d7ef1ab7f812647c9be89545d8ff6867a1ff8b01a2cde2', '2025-11-05 00:02:57'),
(10, 'Guaresh', 'reshrekdo@gmail.com', '$2y$10$zY8R24t9d7PJTpqdN760LuAzUtx8kirahMWyNzCfdEYADhqrVhdtm', 'salesman', '2025-11-04 22:33:52', 0, '9cca54b4c2233388a7cbbf3f444a9abd1c66f0fd608834f995d801999391f9c9', '2025-11-05 00:04:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added_by` (`added_by`);

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
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
