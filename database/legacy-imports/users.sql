-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 25, 2026 at 05:25 AM
-- Server version: 8.0.46-0ubuntu0.22.04.4
-- PHP Version: 8.2.35

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventori_ict_ppa`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nrp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `role`, `jabatan`, `nrp`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(2, 'security', 'user', 'security', '01010101', NULL, NULL, '$2y$12$rtWRkSMfCptJl0TpMfsANub0lTlc3B9cbk0XY22SmMJfsply0UHAG', NULL, '2026-09-08 14:39:37', '2026-09-08 14:41:28'),
(4, 'Elprida Panjaitan', 'user', 'ICT', '26000009', NULL, NULL, '$2y$12$eIuCFFAaH85WnKcF6rsDRebHr2Ce0gIVlGOLvpVTCoGD3GegKZdB6', NULL, '2026-09-08 14:42:39', '2026-09-08 14:43:02'),
(5, 'Bayu Rezky Ramadhan', 'admin', 'helper', '250504', NULL, NULL, '$2y$12$KL0M2qy5mkazrmj.f7uAUeJOFdUek.X88GxjOJkScIc9eJLN3f6kO', NULL, '2026-09-08 14:43:54', '2026-09-21 17:07:16'),
(6, 'Slamet Huda Firmanysah', 'user', 'GL', '18125648', NULL, NULL, '$2y$12$7DP/k74LXzbDcEGaxXKk8uWI1xjZLg76fmNPQz/hGwUvcp35YnJu6', NULL, '2026-09-08 14:44:14', '2026-09-18 17:16:54'),
(7, 'Angga Margi Saputro', 'user', 'GL', '18064116', NULL, NULL, '$2y$12$iUlm33XV6/FF4LqHqpTHJ.xzCdEJqd9t5YbYkIlV9nUIBBhcCjZ3y', NULL, '2026-09-08 14:44:50', '2026-09-08 14:44:50'),
(8, 'T M Fathin Rifat', 'user', 'GL', '24006332', NULL, NULL, '$2y$12$BUr8FUx8sCFYUGHfO9TT3.miDJj4dd.GM0lWj0QCkUuMVgWjuQyCC', NULL, '2026-09-08 14:45:19', '2026-09-08 14:45:19'),
(9, 'Akhdiat Qudratullah', 'user', 'ICT', '23000711', NULL, NULL, '$2y$12$kUIrXKU//1Ax0wWPp8O9p.LjC5K5p4yyJK8f95hKVa0KxG1rmKQRu', NULL, '2026-09-08 14:45:57', '2026-09-08 14:45:57'),
(10, 'Rahmad Ubaidillah', 'user', 'ICT', '24005289', NULL, NULL, '$2y$12$0feZTB1ofTxSJDKQZkLY3umUi7jdNHBcnV41RUXQO.te60gZuP3le', NULL, '2026-09-08 14:46:28', '2026-09-08 14:46:28'),
(11, 'Ahmad Yoga Maulida', 'user', 'ICT', '25000760', NULL, NULL, '$2y$12$rmmnBLnJlZthifs3SIWoeuImko/DTnsYrRp/0Datbawz4diH94zXy', NULL, '2026-09-08 14:47:01', '2026-09-08 14:47:01'),
(12, 'Andika Ramadani Putra', 'user', 'ICT', '26002405', NULL, NULL, '$2y$12$9AnfdouT8iRroeapfFTcP..pFSO6GrDVC7docc6EnKRirrEzzPgAO', NULL, '2026-09-08 14:47:28', '2026-09-08 14:47:28'),
(13, 'Dwi Risky Arifianto', 'user', 'helper', '220231', NULL, NULL, '$2y$12$qFExMv27hKAyYkkaIrNbsOJ6s7yHmwSucX8NSZ1Tgbc.a5yywIi.m', NULL, '2026-09-08 14:48:10', '2026-09-08 14:48:21'),
(14, 'Randy Nugraha', 'user', 'ICT_technician', '2410225', NULL, NULL, '$2y$12$jp3u9zEHQYiOZhWb.Z1Ii.7qHg0V0xFOPPNaFUtvClu5mQ7o3WHte', NULL, '2026-09-08 14:49:13', '2026-09-08 14:49:13'),
(15, 'Muhammad Rahmatul Ulum', 'user', 'ICT_technician', '030725', NULL, NULL, '$2y$12$vBUcnY7jVbcRcWFmZjZi7.1tvymiFOrFwzrorHNTlpM/oSkL0jhJy', NULL, '2026-09-08 14:49:52', '2026-09-08 14:49:52'),
(16, 'Ahmad Dzakiyyul Fikri', 'user', 'hardware_enggineer', '260602', NULL, NULL, '$2y$12$Z8VcyfSQu63i6GSHmmeemuIWUziuV6B4VA/8XUjUvl.MwqLCr8Okq', NULL, '2026-09-10 22:37:11', '2026-09-23 16:55:03'),
(17, 'Tegar Arif Berbudi', 'user', 'hardware_enggineer', '25002859', NULL, NULL, '$2y$12$mfULxpy7S2rxp6aTJ02zAeDklsu7tKDGfdwcYnka.5.2ld6jyqGT.', NULL, '2026-09-11 16:43:12', '2026-09-11 16:49:10'),
(18, 'Bada Agung', 'user', 'hardware_enggineer', '25002856', NULL, NULL, '$2y$12$mD9WUr3KPc9/Oa/IguBAA.n2xr3v65iQzWAGRjgjMLOOjY5TcNfu.', NULL, '2026-09-11 16:44:44', '2026-09-11 16:44:44'),
(19, 'Choirul Ibadillah', 'user', 'hardware_enggineer', '11260022', NULL, NULL, '$2y$12$z1A.yaS0dxg2dhzoqBOmY.KwzmSGM7jlu8k.z91eKp7Yx7HoLNnFy', NULL, '2026-09-23 16:54:24', '2026-09-23 16:54:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_nrp_unique` (`nrp`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
