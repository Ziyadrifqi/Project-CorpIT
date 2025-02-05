-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 04, 2025 at 11:29 AM
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
-- Database: `corpit`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `status` enum('pending','hadir','pulang') NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `judul_kegiatan` varchar(255) DEFAULT NULL,
  `kegiatan_harian` text DEFAULT NULL,
  `no_tiket` varchar(50) DEFAULT NULL,
  `nik` varchar(20) NOT NULL,
  `pbr_tugas` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `user_id`, `tanggal`, `jam_masuk`, `jam_keluar`, `tanggal_keluar`, `status`, `created_at`, `updated_at`, `category_id`, `judul_kegiatan`, `kegiatan_harian`, `no_tiket`, `nik`, `pbr_tugas`) VALUES
(21, 1, '2024-11-19', '12:22:27', '16:07:21', '2024-11-19', 'pulang', '2024-11-19 12:22:27', '2024-11-19 16:07:21', 2, 'lembur', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book', '020202', '', ''),
(38, 1, '2024-11-20', '16:40:00', '02:00:00', '2024-11-21', 'pulang', '2024-11-21 09:25:22', '2024-11-21 09:32:21', 2, 'Lembur Kegiatan ', 'Hari ini melakukan monitoring', '123456', '', ''),
(44, 1, '2024-11-21', '16:35:59', '19:41:14', '2024-11-21', 'pulang', '2024-11-21 16:35:22', '2024-11-21 19:41:14', 1, 'Lembur Kegiatan', 'Monitoring website', '124567', '', ''),
(45, 1, '2024-11-21', '19:46:48', '01:00:00', '2024-11-22', 'pulang', '2024-11-21 19:42:11', '2024-11-22 07:53:40', 1, 'Monitoring', 'Melakukan Kegiatan Monitoring website', '162726', '', ''),
(48, 1, '2024-11-22', '10:56:49', '10:58:40', '2024-11-22', 'pulang', '2024-11-22 10:56:39', '2024-11-22 10:58:40', 2, 'Monitoring Website', 'Melakukan monitoring', '172728', '', ''),
(49, 1, '2024-11-24', '20:03:19', '22:00:00', '2024-11-24', 'pulang', '2024-11-24 20:03:12', '2024-11-26 10:49:13', 2, 'Monitoring Website 3', 'Hari ini melakukan monitoring', '826382', '', ''),
(50, 1, '2024-11-26', '22:41:50', '01:00:00', '2024-11-27', 'pulang', '2024-11-26 22:41:43', '2024-11-28 08:22:09', 2, 'Monitoring Website 2', 'Melakukan monitoring', '737793', '', ''),
(53, 1, '2024-12-06', '10:14:56', '22:00:00', '2024-12-06', 'pulang', '2024-12-06 10:14:48', '2024-12-09 13:36:28', 2, 'Monitoring aktivitas website', 'melakukan monitoring ', '263729', '', ''),
(54, 1, '2024-12-09', '13:40:21', '15:00:00', '2024-12-09', 'pulang', '2024-12-09 13:40:09', '2024-12-10 10:15:39', 1, 'Monitoring aktivitas website', 'melakukan monitoring', '393648', '', ''),
(55, 1, '2024-12-10', '15:16:37', '16:30:00', '2024-12-10', 'pulang', '2024-12-10 15:16:29', '2024-12-11 08:57:06', 1, 'Review website', 'melakukan kegiatan review', '736384', '', ''),
(56, 1, '2024-12-13', '09:11:19', '19:00:00', '2024-12-13', 'pulang', '2024-12-13 09:10:50', '2024-12-18 16:29:01', 1, 'Review website', 'Melakukan kegiatan monitoring pada website', '726382', '', ''),
(57, 1, '2025-01-23', '09:37:27', '10:47:49', '2025-01-23', 'pulang', '2025-01-23 09:36:44', '2025-01-23 10:47:49', 2, 'Melakukan proses billing', 'Melakukan proses billing', '739249', '', ''),
(58, 1, '2025-02-03', '13:32:24', '13:55:21', '2025-02-03', 'pulang', '2025-02-03 13:32:14', '2025-02-03 13:55:21', 2, 'Monitoring aktivitas website', 'Melakukan Proses billing', '202718', '35647891', 'Randi Salam'),
(59, 3, '2025-02-03', '14:37:09', '14:39:43', '2025-02-03', 'pulang', '2025-02-03 14:37:01', '2025-02-03 14:39:43', 2, 'Billing', 'Melakukan Billing', '262739', '72839929', 'Rio Facrudin');

-- --------------------------------------------------------

--
-- Table structure for table `absen_category`
--

CREATE TABLE `absen_category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absen_category`
--

INSERT INTO `absen_category` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Absensi ', '2024-11-19 03:29:04', '2024-11-19 03:31:38'),
(2, 'Absen Lembur', '2024-11-19 03:29:19', '2024-11-19 03:29:19');

-- --------------------------------------------------------

--
-- Table structure for table `admin_activities`
--

CREATE TABLE `admin_activities` (
  `id` int(11) NOT NULL,
  `task` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `activity_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(10) UNSIGNED NOT NULL,
  `nik` varchar(8) NOT NULL,
  `pbr_tugas` text NOT NULL,
  `no_tiket` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_activities`
--

INSERT INTO `admin_activities` (`id`, `task`, `location`, `start_time`, `end_time`, `activity_date`, `description`, `created_at`, `updated_at`, `user_id`, `nik`, `pbr_tugas`, `no_tiket`) VALUES
(1, 'monitoring', 'TB simatupang', '09:00:00', '14:00:00', '2025-01-15', 'Melakukan kegiatan', '2025-01-15 03:48:45', '2025-02-03 06:08:17', 1, '35647891', 'Rio Facrudin', '637484'),
(3, 'Survei', 'Bogor', '09:00:00', '17:00:00', '2025-01-16', 'melakukan survei kepada', '2025-01-16 07:33:09', '2025-02-03 09:24:13', 3, '', 'Randi Salam', '728392'),
(5, 'monitoring', 'jakarta pusat', '10:00:00', '15:00:00', '2025-02-03', 'Melakukan Proses billing', '2025-02-03 06:13:18', '2025-02-03 06:13:18', 1, '35647891', 'Randi Salam', '202718'),
(11, 'Training', 'Surabaya', '00:00:00', '00:41:00', '1970-01-01', 'Pelatihan teknis', '2025-02-04 16:30:44', '2025-02-04 16:30:44', 1, '45678912', 'Mark Lee', 'TKT125');

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `author` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `status` enum('draft','published') DEFAULT 'draft',
  `type` enum('public','internal') NOT NULL DEFAULT 'public'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `content`, `image`, `author`, `created_at`, `updated_at`, `status`, `type`) VALUES
(1, 'Agenda Bulan November', '<p><img alt=\"\" src=\"/ckfinder/userfiles/images/Merah%20Abstrak%20HUT%20Kemerdekaan%20Indonesia%20Latar%20Belakang%20Virtual%20Zoom%20(2).png\" style=\"height:197px; width:350px\" /></p>\r\n\r\n<p><span style=\"font-size:14px\"><strong>Agenda Bulanan</strong></span></p>\r\n\r\n<p>Saat ini telah</p>\r\n\r\n<p>&nbsp;</p>\r\n', '1.png', 'ziyad', '2024-11-08 13:32:57', '2024-12-11 02:27:50', 'published', 'internal'),
(10, 'Agenda Tahunan 2025', '<p>Saat ini</p>\r\n', '10.png', 'ZiyadRifqi', '2024-11-15 01:36:16', '2024-12-11 02:26:13', 'published', 'internal'),
(30, 'Peran Teknologi dalam Mempercepat Transformasi Digital', '<p>Hari ini telah diumumkan</p>\r\n', '30.png', 'Ziyad', '2024-11-24 11:53:41', '2024-12-11 02:27:28', 'published', 'internal'),
(33, 'Pentingnya Transformasi Bisnis Digital​​ perusahaan', '<p>hari ini telah dialkukan</p>\r\n', '33.png', 'safira', '2024-12-02 02:49:02', '2025-01-30 09:39:51', 'published', 'public'),
(35, 'Lintasarta berhasil dalam...', '<p>melakukan penting kegiatan yang berlangsung</p>\r\n', '35.png', 'Ziyad', '2025-01-31 02:02:26', '2025-01-31 02:02:26', 'published', 'public');

-- --------------------------------------------------------

--
-- Table structure for table `article_categories`
--

CREATE TABLE `article_categories` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `article_categories`
--

INSERT INTO `article_categories` (`id`, `article_id`, `category_id`) VALUES
(34, 10, 4),
(35, 30, 3),
(36, 30, 4),
(37, 1, 3),
(42, 33, 3),
(43, 33, 4),
(44, 35, 4);

-- --------------------------------------------------------

--
-- Table structure for table `article_distributions`
--

CREATE TABLE `article_distributions` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `target_type` enum('directorate','division','department','sub_department') NOT NULL,
  `target_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `article_distributions`
--

INSERT INTO `article_distributions` (`id`, `article_id`, `target_type`, `target_id`, `created_at`) VALUES
(534, 10, 'sub_department', 3, '2024-12-11 09:26:13'),
(535, 30, 'sub_department', 1, '2024-12-11 09:27:28'),
(536, 1, 'division', 1, '2024-12-11 09:27:50'),
(537, 1, 'department', 1, '2024-12-11 09:27:50'),
(538, 1, 'sub_department', 1, '2024-12-11 09:27:50'),
(539, 1, 'department', 2, '2024-12-11 09:27:50'),
(540, 1, 'sub_department', 2, '2024-12-11 09:27:50'),
(542, 33, 'sub_department', 1, '2025-01-14 16:01:10');

-- --------------------------------------------------------

--
-- Table structure for table `auth_activation_attempts`
--

CREATE TABLE `auth_activation_attempts` (
  `id` int(10) UNSIGNED NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_groups`
--

CREATE TABLE `auth_groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auth_groups`
--

INSERT INTO `auth_groups` (`id`, `name`, `description`) VALUES
(1, 'admin', 'site administrasion'),
(2, 'user', 'reguler users'),
(3, 'Super Admin', 'Full System Administrator');

-- --------------------------------------------------------

--
-- Table structure for table `auth_groups_permissions`
--

CREATE TABLE `auth_groups_permissions` (
  `group_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `permission_id` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auth_groups_permissions`
--

INSERT INTO `auth_groups_permissions` (`group_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `auth_groups_users`
--

CREATE TABLE `auth_groups_users` (
  `group_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auth_groups_users`
--

INSERT INTO `auth_groups_users` (`group_id`, `user_id`) VALUES
(1, 1),
(1, 3),
(2, 2),
(3, 5);

-- --------------------------------------------------------

--
-- Table structure for table `auth_logins`
--

CREATE TABLE `auth_logins` (
  `id` int(10) UNSIGNED NOT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `date` datetime NOT NULL,
  `success` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auth_logins`
--

INSERT INTO `auth_logins` (`id`, `ip_address`, `email`, `user_id`, `date`, `success`) VALUES
(1, '::1', 'ziyad@gmail.com', 1, '2024-10-18 02:15:59', 1),
(2, '::1', 'ziyad@gmail.com', 1, '2024-10-18 02:26:27', 1),
(3, '::1', 'Rassya@gmail.com', 2, '2024-10-18 02:32:23', 1),
(4, '::1', 'ziyad@gmail.com', 1, '2024-10-18 02:32:43', 1),
(5, '::1', 'Rassya@gmail.com', 2, '2024-10-18 02:38:21', 1),
(6, '::1', 'Rassya@gmail.com', 2, '2024-10-18 06:51:06', 1),
(7, '::1', 'Rassya@gmail.com', 2, '2024-10-18 06:52:03', 1),
(8, '::1', 'Rassya@gmail.com', 2, '2024-10-18 06:52:25', 1),
(9, '::1', 'ziyad@gmail.com', 1, '2024-10-20 01:13:57', 1),
(10, '::1', 'Rassya@gmail.com', 2, '2024-10-20 01:14:35', 1),
(11, '::1', 'rassya', NULL, '2024-10-20 01:43:34', 0),
(12, '::1', 'Rassya@gmail.com', 2, '2024-10-20 01:43:42', 1),
(13, '::1', 'ziyad@gmail.com', 1, '2024-10-20 01:49:45', 1),
(14, '::1', 'Rassya@gmail.com', 2, '2024-10-20 01:59:44', 1),
(15, '::1', 'Rassya@gmail.com', 2, '2024-10-20 02:00:50', 1),
(16, '::1', 'ziyad@gmail.com', 1, '2024-10-20 03:21:41', 1),
(17, '::1', 'ziyad@gmail.com', 1, '2024-10-20 08:30:46', 1),
(18, '::1', 'Rassya@gmail.com', 2, '2024-10-20 10:02:46', 1),
(19, '::1', 'rassya', NULL, '2024-10-20 10:06:30', 0),
(20, '::1', 'Rassya@gmail.com', 2, '2024-10-20 10:06:40', 1),
(21, '::1', 'ziyad@gmail.com', 1, '2024-10-21 03:21:46', 1),
(22, '::1', 'Rassya@gmail.com', 2, '2024-10-21 05:21:57', 1),
(23, '::1', 'Rassya@gmail.com', 2, '2024-10-21 05:24:47', 1),
(24, '::1', 'Rassya@gmail.com', 2, '2024-10-21 05:44:06', 1),
(25, '::1', 'ziyad@gmail.com', 1, '2024-10-21 05:45:22', 1),
(26, '::1', 'rassya', NULL, '2024-10-21 05:47:15', 0),
(27, '::1', 'Rassya@gmail.com', 2, '2024-10-21 05:47:21', 1),
(28, '::1', 'Rassya@gmail.com', 2, '2024-10-21 05:53:58', 1),
(29, '::1', 'Rassya@gmail.com', 2, '2024-10-21 05:56:26', 1),
(30, '::1', 'Rassya@gmail.com', 2, '2024-10-21 05:58:34', 1),
(31, '::1', 'ziyad@gmail.com', 1, '2024-10-21 06:03:58', 1),
(32, '::1', 'ziyad@gmail.com', NULL, '2024-10-21 06:06:43', 0),
(33, '::1', 'Rassya@gmail.com', 2, '2024-10-21 06:06:51', 1),
(34, '::1', 'Rassya@gmail.com', 2, '2024-10-21 06:08:18', 1),
(35, '::1', 'Rassya@gmail.com', 2, '2024-10-21 06:15:04', 1),
(36, '::1', 'ziyad@gmail.com', 1, '2024-10-21 06:20:20', 1),
(37, '::1', 'Rassya@gmail.com', 2, '2024-10-21 08:11:18', 1),
(38, '::1', 'Rassya@gmail.com', 2, '2024-10-21 08:12:23', 1),
(39, '::1', 'rassya', NULL, '2024-10-21 08:13:01', 0),
(40, '::1', 'Rassya@gmail.com', 2, '2024-10-21 08:13:08', 1),
(41, '::1', 'Rassya@gmail.com', 2, '2024-10-21 08:41:51', 1),
(42, '::1', 'rassya', NULL, '2024-10-21 09:15:50', 0),
(43, '::1', 'Rassya@gmail.com', 2, '2024-10-21 09:15:57', 1),
(44, '::1', 'Rassya@gmail.com', 2, '2024-10-21 12:07:18', 1),
(45, '::1', 'ziyad@gmail.com', 1, '2024-10-21 12:08:17', 1),
(46, '::1', 'ziyad@gmail.com', 1, '2024-10-21 13:09:52', 1),
(47, '::1', 'ziyad@gmail.com', 1, '2024-10-21 13:49:00', 1),
(48, '::1', 'ziyad@gmail.com', 1, '2024-10-22 02:20:22', 1),
(49, '::1', 'Rassya@gmail.com', 2, '2024-10-22 02:43:44', 1),
(50, '::1', 'ziyad@gmail.com', 1, '2024-10-22 03:19:55', 1),
(51, '::1', 'ziyad@gmail.com', 1, '2024-10-22 04:51:01', 1),
(52, '::1', 'Rassya@gmail.com', 2, '2024-10-22 06:09:39', 1),
(53, '::1', 'rassya', NULL, '2024-10-22 06:10:22', 0),
(54, '::1', 'Rassya@gmail.com', 2, '2024-10-22 06:10:29', 1),
(55, '::1', 'rassya', NULL, '2024-10-22 06:39:42', 0),
(56, '::1', 'Rassya@gmail.com', 2, '2024-10-22 06:39:52', 1),
(57, '::1', 'ziyad@gmail.com', 1, '2024-10-22 07:17:34', 1),
(58, '::1', 'ziyad@gmail.com', 1, '2024-10-22 08:23:18', 1),
(59, '::1', 'rassya', NULL, '2024-10-22 08:23:38', 0),
(60, '::1', 'Rassya@gmail.com', 2, '2024-10-22 08:23:48', 1),
(61, '::1', 'ziyad@gmail.com', 1, '2024-10-22 08:25:05', 1),
(62, '::1', 'ziyad@gmail.com', 1, '2024-10-23 01:31:36', 1),
(63, '::1', 'Rassya@gmail.com', 2, '2024-10-23 03:03:58', 1),
(64, '::1', 'rassya', NULL, '2024-10-23 06:46:14', 0),
(65, '::1', 'Rassya@gmail.com', 2, '2024-10-23 06:46:23', 1),
(66, '::1', 'rassya', NULL, '2024-10-23 07:21:23', 0),
(67, '::1', 'Rassya@gmail.com', 2, '2024-10-23 07:21:32', 1),
(68, '::1', 'ziyad@gmail.com', 1, '2024-10-23 08:31:23', 1),
(69, '::1', 'ziyad@gmail.com', 1, '2024-10-23 08:31:31', 1),
(70, '::1', 'ziyad@gmail.com', NULL, '2024-10-23 08:40:15', 0),
(71, '::1', 'ziyad@gmail.com', 1, '2024-10-23 08:40:46', 1),
(72, '::1', 'ziyad@gmail.com', 1, '2024-10-23 08:43:13', 1),
(73, '::1', 'ziyad@gmail.com', NULL, '2024-10-24 00:53:17', 0),
(74, '::1', 'ziyad@gmail.com', 1, '2024-10-24 00:53:27', 1),
(75, '::1', 'Rassya@gmail.com', 2, '2024-10-24 02:06:40', 1),
(76, '::1', 'Rassya@gmail.com', 2, '2024-10-24 02:29:33', 1),
(77, '::1', 'Rassya@gmail.com', 2, '2024-10-24 06:53:40', 1),
(78, '::1', 'Rassya@gmail.com', 2, '2024-10-25 01:22:23', 1),
(79, '::1', 'Rassya@gmail.com', 2, '2024-10-25 01:22:52', 1),
(80, '::1', 'Rassya@gmail.com', 2, '2024-10-25 01:23:57', 1),
(81, '::1', 'Rassya@gmail.com', 2, '2024-10-25 01:24:43', 1),
(82, '::1', 'Rassya@gmail.com', 2, '2024-10-25 01:25:39', 1),
(83, '::1', 'ziyad@gmail.com', 1, '2024-10-25 01:28:23', 1),
(84, '::1', 'Rassya@gmail.com', 2, '2024-10-25 01:40:02', 1),
(85, '::1', 'ziyad@gmail.com', 1, '2024-10-25 01:42:45', 1),
(86, '::1', 'ziyad@gmail.com', 1, '2024-10-25 07:51:14', 1),
(87, '::1', 'ziyad@gmail.com', 1, '2024-10-27 02:58:29', 1),
(88, '::1', 'ziyad@gmail.com', 1, '2024-10-28 01:04:31', 1),
(89, '::1', 'ziyad@gmail.com', 1, '2024-10-28 01:04:33', 1),
(90, '::1', 'ziyad@gmail.com', 1, '2024-10-28 05:33:03', 1),
(91, '::1', 'ziyad@gmail.com', 1, '2024-10-28 12:30:29', 1),
(92, '::1', 'Rassya@gmail.com', 2, '2024-10-28 13:04:23', 1),
(93, '::1', 'Rassya@gmail.com', 2, '2024-10-28 13:27:07', 1),
(94, '::1', 'ziyad@gmail.com', 1, '2024-10-29 01:16:30', 1),
(95, '::1', 'Rassya@gmail.com', 2, '2024-10-29 01:17:04', 1),
(96, '::1', 'ziyad@gmail.com', 1, '2024-10-29 06:13:41', 1),
(97, '::1', 'Rassya@gmail.com', 2, '2024-10-29 06:22:30', 1),
(98, '::1', 'raihan@gmail.com', 3, '2024-10-29 06:36:43', 1),
(99, '::1', 'ziyad@gmail.com', 1, '2024-10-30 01:29:08', 1),
(100, '::1', 'Rassya@gmail.com', 2, '2024-10-30 01:30:01', 1),
(101, '::1', 'ziyad@gmail.com', 1, '2024-10-30 07:13:18', 1),
(102, '::1', 'raihan@gmail.com', 3, '2024-10-30 07:13:40', 1),
(103, '::1', 'Rassya@gmail.com', 2, '2024-10-30 09:04:02', 1),
(104, '::1', 'ziyad@gmail.com', 1, '2024-10-31 01:14:10', 1),
(105, '::1', 'Rassya@gmail.com', 2, '2024-10-31 01:14:40', 1),
(106, '::1', 'raihan@gmail.com', 3, '2024-10-31 01:14:51', 1),
(107, '::1', 'ziyad@gmail.com', 1, '2024-10-31 04:54:23', 1),
(108, '::1', 'ziyad@gmail.com', 1, '2024-11-05 06:58:26', 1),
(109, '::1', 'Rassya@gmail.com', 2, '2024-11-05 07:26:15', 1),
(110, '::1', 'raihan@gmail.com', 3, '2024-11-05 07:26:41', 1),
(111, '::1', 'raihan@gmail.com', 3, '2024-11-05 07:27:18', 1),
(112, '::1', 'ziyad@gmail.com', 1, '2024-11-06 00:52:33', 1),
(113, '::1', 'raihan@gmail.com', 3, '2024-11-06 06:00:09', 1),
(114, '::1', 'ziyad@gmail.com', 1, '2024-11-06 06:02:05', 1),
(115, '::1', 'ziyad@gmail.com', 1, '2024-11-06 08:23:17', 1),
(116, '::1', 'raihan@gmail.com', NULL, '2024-11-06 08:29:51', 0),
(117, '::1', 'raihan@gmail.com', 3, '2024-11-06 08:29:59', 1),
(118, '::1', 'Rassya@gmail.com', 2, '2024-11-06 08:30:22', 1),
(119, '::1', 'ziyad@gmail.com', 1, '2024-11-07 01:01:57', 1),
(120, '::1', 'ziyad@gmail.com', 1, '2024-11-07 07:46:31', 1),
(121, '::1', 'raihan@gmail.com', 3, '2024-11-07 07:47:30', 1),
(122, '::1', 'Rassya@gmail.com', 2, '2024-11-07 07:48:49', 1),
(123, '::1', 'ziyad@gmail.com', 1, '2024-11-07 08:22:22', 1),
(124, '::1', 'ziyad@gmail.com', 1, '2024-11-08 01:11:42', 1),
(125, '::1', 'ziyad@gmail.com', 1, '2024-11-08 06:07:03', 1),
(126, '::1', 'Rassya@gmail.com', 2, '2024-11-08 08:12:54', 1),
(127, '::1', 'raihan@gmail.com', 3, '2024-11-08 08:13:25', 1),
(128, '::1', 'ziyad@gmail.com', 1, '2024-11-10 02:31:23', 1),
(129, '::1', 'Rassya@gmail.com', 2, '2024-11-10 03:29:52', 1),
(130, '::1', 'raihan@gmail.com', 3, '2024-11-10 04:26:28', 1),
(131, '::1', 'raihan@gmail.com', 3, '2024-11-10 04:26:37', 1),
(132, '::1', 'raihan@gmail.com', 3, '2024-11-10 04:28:01', 1),
(133, '::1', 'raihan@gmail.com', 3, '2024-11-10 04:29:07', 1),
(134, '::1', 'raihan@gmail.com', 3, '2024-11-10 04:34:30', 1),
(135, '::1', 'raihan@gmail.com', 3, '2024-11-10 04:36:59', 1),
(136, '::1', 'raihan@gmail.com', 3, '2024-11-10 04:37:40', 1),
(137, '::1', 'ziyad@gmail.com', 1, '2024-11-10 08:14:06', 1),
(138, '::1', 'ziyad@gmail.com', 1, '2024-11-10 11:25:42', 1),
(139, '::1', 'ziyad@gmail.com', 1, '2024-11-11 01:11:57', 1),
(140, '::1', 'ziyad@gmail.com', 1, '2024-11-11 07:51:55', 1),
(141, '::1', 'Rassya@gmail.com', 2, '2024-11-11 08:02:00', 1),
(142, '::1', 'ziyad@gmail.com', 1, '2024-11-11 12:15:36', 1),
(143, '::1', 'Rassya@gmail.com', 2, '2024-11-11 12:17:36', 1),
(144, '::1', 'ziyad@gmail.com', 1, '2024-11-12 02:57:24', 1),
(145, '::1', 'ziyad@gmail.com', 1, '2024-11-12 10:48:54', 1),
(146, '::1', 'rassya', NULL, '2024-11-12 11:01:36', 0),
(147, '::1', 'Rassya@gmail.com', 2, '2024-11-12 11:01:44', 1),
(148, '::1', 'ziyad@gmail.com', 1, '2024-11-13 01:11:28', 1),
(149, '::1', 'Rassya@gmail.com', 2, '2024-11-13 01:13:27', 1),
(150, '::1', 'ziyad@gmail.com', 1, '2024-11-14 01:36:51', 1),
(151, '::1', 'ziyad@gmail.com', 1, '2024-11-15 01:30:33', 1),
(152, '::1', 'Rassya@gmail.com', 2, '2024-11-15 01:37:34', 1),
(153, '::1', 'ziyad@gmail.com', 1, '2024-11-15 02:41:04', 1),
(154, '::1', 'raihan@gmail.com', 3, '2024-11-15 02:50:31', 1),
(155, '::1', 'raihan@gmail.com', 3, '2024-11-15 07:24:31', 1),
(156, '::1', 'rassya', NULL, '2024-11-15 07:28:43', 0),
(157, '::1', 'Rassya@gmail.com', 2, '2024-11-15 07:28:49', 1),
(158, '::1', 'ziyad@gmail.com', 1, '2024-11-15 07:34:41', 1),
(159, '::1', 'ziyad@gmail.com', 1, '2024-11-18 03:31:28', 1),
(160, '::1', 'syakira', NULL, '2024-11-18 03:41:18', 0),
(161, '::1', 'syakira', NULL, '2024-11-18 03:42:02', 0),
(162, '::1', 'syakira', NULL, '2024-11-18 03:42:14', 0),
(163, '::1', 'syakira', NULL, '2024-11-18 03:46:19', 0),
(164, '::1', 'syakira02@gmail.com', NULL, '2024-11-18 03:47:09', 0),
(165, '::1', 'syakira', NULL, '2024-11-18 03:49:46', 0),
(166, '::1', 'syakira', NULL, '2024-11-18 03:50:14', 0),
(167, '::1', 'syakira', NULL, '2024-11-18 03:51:13', 0),
(168, '::1', 'syakira02@gmail.com', NULL, '2024-11-18 03:51:28', 0),
(169, '::1', 'syakira02@gmail.com', 5, '2024-11-18 03:52:55', 1),
(170, '::1', 'syakira02@gmail.com', 5, '2024-11-18 03:53:34', 1),
(171, '::1', 'syakira02@gmail.com', 5, '2024-11-18 03:54:36', 1),
(172, '::1', 'raihan@gmail.com', 3, '2024-11-18 04:25:30', 1),
(173, '::1', 'raihan@gmail.com', NULL, '2024-11-18 04:26:00', 0),
(174, '::1', 'raihan@gmail.com', 3, '2024-11-18 04:26:07', 1),
(175, '::1', 'syakira02@gmail.com', 5, '2024-11-18 07:32:01', 1),
(176, '::1', 'ziyad@gmail.com', 1, '2024-11-18 07:32:11', 1),
(177, '::1', 'syakira02@gmail.com', 5, '2024-11-19 02:05:05', 1),
(178, '::1', 'ziyad@gmail.com', 1, '2024-11-19 02:05:35', 1),
(179, '::1', 'ziyad@gmail.com', 1, '2024-11-19 02:34:31', 1),
(180, '::1', 'syakira02@gmail.com', 5, '2024-11-19 02:37:16', 1),
(181, '::1', 'syakira', NULL, '2024-11-19 05:14:20', 0),
(182, '::1', 'syakira02@gmail.com', 5, '2024-11-19 05:14:28', 1),
(183, '::1', 'syakira02@gmail.com', 5, '2024-11-19 08:30:25', 1),
(184, '::1', 'ziyad@gmail.com', 1, '2024-11-19 08:30:40', 1),
(185, '::1', 'Rassya@gmail.com', 2, '2024-11-19 08:30:53', 1),
(186, '::1', 'ziyad@gmail.com', 1, '2024-11-20 01:04:47', 1),
(187, '::1', 'syakira02@gmail.com', 5, '2024-11-20 06:48:36', 1),
(188, '::1', 'Rassya@gmail.com', 2, '2024-11-20 07:41:55', 1),
(189, '::1', 'ziyad@gmail.com', 1, '2024-11-21 00:38:35', 1),
(190, '::1', 'syakira02@gmail.com', 5, '2024-11-21 07:06:51', 1),
(191, '::1', 'ziyad@gmail.com', 1, '2024-11-21 12:40:19', 1),
(192, '::1', 'syakira02@gmail.com', 5, '2024-11-21 13:07:26', 1),
(193, '::1', 'syakira02@gmail.com', 5, '2024-11-21 14:03:44', 1),
(194, '::1', 'ziyad@gmail.com', 1, '2024-11-22 00:44:37', 1),
(195, '::1', 'syakira02@gmail.com', 5, '2024-11-22 01:11:58', 1),
(196, '::1', 'Rassya@gmail.com', 2, '2024-11-22 03:55:27', 1),
(197, '::1', 'syakira02@gmail.com', 5, '2024-11-22 06:00:07', 1),
(198, '::1', 'ziyad@gmail.com', 1, '2024-11-24 06:35:40', 1),
(199, '::1', 'syakira02@gmail.com', 5, '2024-11-24 07:08:49', 1),
(200, '::1', 'ziyad@gmail.com', 1, '2024-11-24 11:40:41', 1),
(201, '::1', 'Rassya@gmail.com', 2, '2024-11-24 11:44:52', 1),
(202, '::1', 'syakira02@gmail.com', 5, '2024-11-24 12:58:48', 1),
(203, '::1', 'ziyad@gmail.com', 1, '2024-11-25 06:51:12', 1),
(204, '::1', 'Rassya@gmail.com', 2, '2024-11-25 07:02:52', 1),
(205, '::1', 'Rassya@gmail.com', 2, '2024-11-25 07:45:00', 1),
(206, '::1', 'rassya', NULL, '2024-11-25 11:43:11', 0),
(207, '::1', 'Rassya@gmail.com', 2, '2024-11-25 11:43:18', 1),
(208, '::1', 'ziyad@gmail.com', 1, '2024-11-25 12:16:20', 1),
(209, '::1', 'Rassya@gmail.com', 2, '2024-11-25 14:18:29', 1),
(210, '::1', 'Rassya@gmail.com', 2, '2024-11-25 14:20:45', 1),
(211, '::1', 'ziyad@gmail.com', 1, '2024-11-26 01:42:59', 1),
(212, '::1', 'raihan@gmail.com', 3, '2024-11-26 01:43:20', 1),
(213, '::1', 'Rassya@gmail.com', 2, '2024-11-26 02:01:16', 1),
(214, '::1', 'ziyad@gmail.com', 1, '2024-11-26 06:16:58', 1),
(215, '::1', 'Rassya@gmail.com', 2, '2024-11-26 06:18:12', 1),
(216, '::1', 'Rassya@gmail.com', 2, '2024-11-26 12:13:24', 1),
(217, '::1', 'raihan@gmail.com', 3, '2024-11-26 12:13:51', 1),
(218, '::1', 'Rassya@gmail.com', 2, '2024-11-26 12:14:18', 1),
(219, '::1', 'ziyad@gmail.com', 1, '2024-11-26 15:41:20', 1),
(220, '::1', 'syakira02@gmail.com', 5, '2024-11-26 15:45:11', 1),
(221, '::1', 'raihan@gmail.com', 3, '2024-11-28 01:18:53', 1),
(222, '::1', 'Rassya@gmail.com', 2, '2024-11-28 01:20:17', 1),
(223, '::1', 'ziyad@gmail.com', 1, '2024-11-28 01:21:17', 1),
(224, '::1', 'Rassya@gmail.com', 2, '2024-11-28 03:21:31', 1),
(225, '::1', 'syakira02@gmail.com', 5, '2024-11-28 04:32:38', 1),
(226, '::1', 'ziyad@gmail.com', 1, '2024-11-28 12:23:42', 1),
(227, '::1', 'Rassya@gmail.com', 2, '2024-11-28 13:18:39', 1),
(228, '::1', 'ziyad@gmail.com', 1, '2024-11-29 00:48:59', 1),
(229, '::1', 'Rassya@gmail.com', 2, '2024-11-29 01:08:32', 1),
(230, '::1', 'ziyad@gmail.com', 1, '2024-11-29 03:41:04', 1),
(231, '::1', 'ziyad@gmail.com', 1, '2024-11-29 06:36:55', 1),
(232, '::1', 'Rassya@gmail.com', 2, '2024-12-01 08:27:32', 1),
(233, '::1', 'Rassya@gmail.com', 2, '2024-12-01 08:50:29', 1),
(234, '::1', 'Rassya@gmail.com', 2, '2024-12-01 08:51:45', 1),
(235, '::1', 'Rassya@gmail.com', 2, '2024-12-01 08:59:26', 1),
(236, '::1', 'Rassya@gmail.com', 2, '2024-12-01 10:03:23', 1),
(237, '::1', 'Rassya@gmail.com', 2, '2024-12-01 11:00:04', 1),
(238, '::1', 'Rassya@gmail.com', 2, '2024-12-01 11:00:41', 1),
(239, '::1', 'Rassya@gmail.com', 2, '2024-12-02 01:59:17', 1),
(240, '::1', 'ziyad@gmail.com', 1, '2024-12-02 02:48:04', 1),
(241, '::1', 'Rassya@gmail.com', 2, '2024-12-02 08:28:00', 1),
(242, '::1', 'Rassya@gmail.com', 2, '2024-12-02 12:19:23', 1),
(243, '::1', 'ziyad@gmail.com', 1, '2024-12-02 13:02:26', 1),
(244, '::1', 'ziyad@gmail.com', 1, '2024-12-02 13:03:31', 1),
(245, '::1', 'ziyad@gmail.com', 1, '2024-12-02 13:04:13', 1),
(246, '::1', 'ziyad@gmail.com', 1, '2024-12-02 13:16:18', 1),
(247, '::1', 'ziyad@gmail.com', 1, '2024-12-02 13:16:42', 1),
(248, '::1', 'ziyad@gmail.com', 1, '2024-12-02 13:20:34', 1),
(249, '::1', 'ziyad@gmail.com', 1, '2024-12-02 13:24:33', 1),
(250, '::1', 'syakira', NULL, '2024-12-02 13:25:37', 0),
(251, '::1', 'syakira02@gmail.com', 5, '2024-12-02 13:25:46', 1),
(252, '::1', 'ziyad@gmail.com', 1, '2024-12-02 14:43:28', 1),
(253, '::1', 'Rassya@gmail.com', 2, '2024-12-02 14:43:44', 1),
(254, '::1', 'raihan@gmail.com', NULL, '2024-12-02 14:44:35', 0),
(255, '::1', 'raihan@gmail.com', 3, '2024-12-02 14:44:42', 1),
(256, '::1', 'ziyad@gmail.com', 1, '2024-12-02 14:49:58', 1),
(257, '::1', 'ziyad@gmail.com', 1, '2024-12-03 06:43:27', 1),
(258, '::1', 'syakira02@gmail.com', 5, '2024-12-03 08:07:05', 1),
(259, '::1', 'Rassya@gmail.com', 2, '2024-12-03 08:54:58', 1),
(260, '::1', 'Rassya@gmail.com', 2, '2024-12-04 01:11:35', 1),
(261, '::1', 'ziyad@gmail.com', 1, '2024-12-04 01:39:42', 1),
(262, '::1', 'raihan@gmail.com', NULL, '2024-12-04 01:41:11', 0),
(263, '::1', 'raihan@gmail.com', 3, '2024-12-04 01:41:20', 1),
(264, '::1', 'Rassya@gmail.com', 2, '2024-12-04 01:43:47', 1),
(265, '::1', 'raihan@gmail.com', 3, '2024-12-04 01:50:17', 1),
(266, '::1', 'Rassya@gmail.com', 2, '2024-12-04 01:51:52', 1),
(267, '::1', 'syakira02@gmail.com', 5, '2024-12-04 02:30:57', 1),
(268, '::1', 'ziyad@gmail.com', 1, '2024-12-04 08:31:57', 1),
(269, '::1', 'ziyad@gmail.com', 1, '2024-12-05 01:22:02', 1),
(270, '::1', 'Rassya@gmail.com', 2, '2024-12-05 01:22:15', 1),
(271, '::1', 'Rassya@gmail.com', 2, '2024-12-05 09:19:19', 1),
(272, '::1', 'ziyad@gmail.com', 1, '2024-12-06 02:32:15', 1),
(273, '::1', 'Rassya@gmail.com', 2, '2024-12-06 02:34:08', 1),
(274, '::1', 'Rassya@gmail.com', 2, '2024-12-09 02:53:56', 1),
(275, '::1', 'ziyad@gmail.com', 1, '2024-12-09 03:29:58', 1),
(276, '::1', 'Rassya@gmail.com', 2, '2024-12-09 06:03:32', 1),
(277, '::1', 'ziyad@gmail.com', 1, '2024-12-09 06:03:54', 1),
(278, '::1', 'Rassya@gmail.com', 2, '2024-12-09 06:44:51', 1),
(279, '::1', 'syakira02@gmail.com', 5, '2024-12-09 08:41:15', 1),
(280, '::1', 'ziyad@gmail.com', 1, '2024-12-09 12:06:40', 1),
(281, '::1', 'ziyad@gmail.com', 1, '2024-12-10 03:15:03', 1),
(282, '::1', 'ziyad@gmail.com', 1, '2024-12-10 08:16:00', 1),
(283, '::1', 'syakira02@gmail.com', 5, '2024-12-10 08:17:25', 1),
(284, '::1', 'Rassya@gmail.com', 2, '2024-12-10 08:21:05', 1),
(285, '::1', 'ziyad@gmail.com', 1, '2024-12-11 01:24:31', 1),
(286, '::1', 'Rassya@gmail.com', 2, '2024-12-11 01:25:06', 1),
(287, '::1', 'syakira02@gmail.com', 5, '2024-12-11 02:00:16', 1),
(288, '::1', 'ziyad@gmail.com', 1, '2024-12-11 02:23:47', 1),
(289, '::1', 'syakira02@gmail.com', 5, '2024-12-11 06:28:43', 1),
(290, '::1', 'ziyad@gmail.com', 1, '2024-12-11 08:52:37', 1),
(291, '::1', 'ziyad@gmail.com', 1, '2024-12-12 02:08:23', 1),
(292, '::1', 'Rassya@gmail.com', 2, '2024-12-12 04:11:07', 1),
(293, '::1', 'syakira02@gmail.com', 5, '2024-12-12 07:49:14', 1),
(294, '::1', 'ziyad@gmail.com', 1, '2024-12-13 01:58:18', 1),
(295, '::1', 'syakira02@gmail.com', 5, '2024-12-13 02:04:39', 1),
(296, '::1', 'Rassya@gmail.com', 2, '2024-12-13 02:12:13', 1),
(297, '::1', 'ziyad@gmail.com', 1, '2024-12-18 09:28:16', 1),
(298, '::1', 'Rassya@gmail.com', 2, '2024-12-18 09:29:40', 1),
(299, '::1', 'ziyad@gmail.com', 1, '2025-01-02 01:04:38', 1),
(300, '::1', 'ziyad@gmail.com', 1, '2025-01-02 06:46:26', 1),
(301, '::1', 'ziyad@gmail.com', 1, '2025-01-08 02:58:32', 1),
(302, '::1', 'Rassya@gmail.com', 2, '2025-01-09 03:20:58', 1),
(303, '::1', 'ziyadrpe@gmail.com', NULL, '2025-01-09 03:21:51', 0),
(304, '::1', 'ziyad@gmail.com', 1, '2025-01-09 03:22:10', 1),
(305, '::1', 'Rassya@gmail.com', 2, '2025-01-09 04:36:55', 1),
(306, '::1', 'Rassya@gmail.com', 2, '2025-01-09 04:42:28', 1),
(307, '::1', 'Rassya@gmail.com', 2, '2025-01-09 04:42:44', 1),
(308, '::1', 'Rassya@gmail.com', 2, '2025-01-09 07:12:23', 1),
(309, '::1', 'ziyad@gmail.com', 1, '2025-01-14 02:07:47', 1),
(310, '::1', 'syakira02@gmail.com', 5, '2025-01-14 02:25:27', 1),
(311, '::1', 'ziyad@gmail.com', 1, '2025-01-14 08:44:50', 1),
(312, '::1', 'syakira02@gmail.com', 5, '2025-01-14 09:30:17', 1),
(313, '::1', 'ziyad@gmail.com', 1, '2025-01-14 12:08:13', 1),
(314, '::1', 'syakira02@gmail.com', 5, '2025-01-14 12:26:25', 1),
(315, '::1', 'ziyad@gmail.com', 1, '2025-01-15 00:51:13', 1),
(316, '::1', 'syakira02@gmail.com', 5, '2025-01-15 01:10:56', 1),
(317, '::1', 'syakira02@gmail.com', 5, '2025-01-15 03:50:39', 1),
(318, '::1', 'ziyad@gmail.com', 1, '2025-01-16 01:31:20', 1),
(319, '::1', 'syakira02@gmail.com', 5, '2025-01-16 01:31:32', 1),
(320, '::1', 'Rassya@gmail.com', 2, '2025-01-16 04:49:48', 1),
(321, '::1', 'Rassya@gmail.com', 2, '2025-01-16 04:50:47', 1),
(322, '::1', 'ziyad@gmail.com', 1, '2025-01-16 04:51:05', 1),
(323, '::1', 'raihan@gmail.com', 3, '2025-01-16 07:32:27', 1),
(324, '::1', 'raihan@gmail.com', 3, '2025-01-16 07:45:00', 1),
(325, '::1', 'syakira02@gmail.com', 5, '2025-01-17 01:13:42', 1),
(326, '::1', 'ziyad@gmail.com', 1, '2025-01-17 01:39:50', 1),
(327, '172.18.0.1', 'ziyad@gmail.com', 1, '2025-01-21 08:31:10', 1),
(328, '172.18.0.1', 'ziyad@gmail.com', 1, '2025-01-22 01:08:01', 1),
(329, '172.18.0.1', 'ziyad@gmail.com', 1, '2025-01-22 01:09:12', 1),
(330, '172.18.0.1', 'ziyad@gmail.com', 1, '2025-01-22 06:29:29', 1),
(331, '172.18.0.1', 'ziyad@gmail.com', 1, '2025-01-22 06:29:58', 1),
(332, '172.18.0.1', 'Rassya@gmail.com', 2, '2025-01-22 09:04:27', 1),
(333, '172.18.0.1', 'syakira02@gmail.com', 5, '2025-01-22 09:12:30', 1),
(334, '172.18.0.1', 'ziyad@gmail.com', 1, '2025-01-23 01:08:42', 1),
(335, '172.18.0.1', 'Rassya@gmail.com', 2, '2025-01-23 01:10:18', 1),
(336, '172.18.0.1', 'ziyad@gmail.com', 1, '2025-01-23 02:02:41', 1),
(337, '172.18.0.1', 'Rassya@gmail.com', 2, '2025-01-23 02:08:07', 1),
(338, '172.18.0.1', 'syakira02@gmail.com', 5, '2025-01-23 02:13:36', 1),
(339, '::1', 'Rassya@gmail.com', 2, '2025-01-31 01:43:28', 1),
(340, '::1', 'ziyad@gmail.com', 1, '2025-01-31 01:48:22', 1),
(341, '::1', 'ziyad@gmail.com', 1, '2025-01-31 01:49:29', 1),
(342, '::1', 'ziyad@gmail.com', 1, '2025-01-31 01:49:50', 1),
(343, '::1', 'syakira02@gmail.com', 5, '2025-01-31 01:51:33', 1),
(344, '::1', 'ziyad@gmail.com', 1, '2025-01-31 01:51:52', 1),
(345, '::1', 'syakira02@gmail.com', 5, '2025-01-31 01:52:13', 1),
(346, '::1', 'syakira02@gmail.com', 5, '2025-01-31 01:52:48', 1),
(347, '::1', 'syakira02@gmail.com', 5, '2025-01-31 01:56:28', 1),
(348, '::1', 'ziyad@gmail.com', 1, '2025-01-31 01:58:51', 1),
(349, '::1', 'rassya', NULL, '2025-01-31 02:00:10', 0),
(350, '::1', 'Rassya@gmail.com', 2, '2025-01-31 02:00:19', 1),
(351, '::1', 'syakira02@gmail.com', 5, '2025-01-31 02:05:24', 1),
(352, '::1', 'syakira02@gmail.com', 5, '2025-01-31 02:05:45', 1),
(353, '::1', 'syakira02@gmail.com', 5, '2025-01-31 02:22:50', 1),
(354, '::1', 'syakira02@gmail.com', 5, '2025-01-31 02:36:11', 1),
(355, '::1', 'syakira02@gmail.com', 5, '2025-01-31 02:37:45', 1),
(356, '::1', 'ziyad@gmail.com', 1, '2025-01-31 02:38:17', 1),
(357, '::1', 'syakira02@gmail.com', 5, '2025-01-31 02:38:55', 1),
(358, '::1', 'ziyad@gmail.com', 1, '2025-01-31 03:53:46', 1),
(359, '::1', 'ziyad@gmail.com', 1, '2025-01-31 03:53:56', 1),
(360, '::1', 'ziyad@gmail.com', 1, '2025-01-31 03:54:16', 1),
(361, '::1', 'ziyad@gmail.com', 1, '2025-01-31 04:07:57', 1),
(362, '::1', 'syakira02@gmail.com', 5, '2025-01-31 04:08:37', 1),
(363, '::1', 'Rassya@gmail.com', 2, '2025-01-31 06:13:32', 1),
(364, '::1', 'Rassya@gmail.com', 2, '2025-01-31 06:15:19', 1),
(365, '::1', 'ziyad@gmail.com', 1, '2025-01-31 06:17:26', 1),
(366, '::1', 'ziyad@gmail.com', 1, '2025-01-31 06:46:26', 1),
(367, '::1', 'ziyad@gmail.com', 1, '2025-01-31 06:59:41', 1),
(368, '::1', 'Rassya@gmail.com', 2, '2025-01-31 07:35:38', 1),
(369, '::1', 'ziyad@gmail.com', 1, '2025-01-31 07:37:05', 1),
(370, '::1', 'ziyad@gmail.com', 1, '2025-01-31 07:40:19', 1),
(371, '::1', 'Rassya@gmail.com', 2, '2025-01-31 07:50:31', 1),
(372, '::1', 'Rassya@gmail.com', 2, '2025-01-31 07:56:31', 1),
(373, '::1', 'syakira02@gmail.com', 5, '2025-01-31 08:01:01', 1),
(374, '::1', 'Rassya@gmail.com', 2, '2025-02-01 12:18:02', 1),
(375, '::1', 'ziyad@gmail.com', 1, '2025-02-01 12:18:24', 1),
(376, '::1', 'Rassya@gmail.com', 2, '2025-02-01 13:38:05', 1),
(377, '::1', 'ziyad@gmail.com', 1, '2025-02-01 13:38:42', 1),
(378, '::1', 'syakira02@gmail.com', 5, '2025-02-01 14:18:17', 1),
(379, '::1', 'ziyad@gmail.com', 1, '2025-02-03 01:28:35', 1),
(380, '::1', 'syakira02@gmail.com', 5, '2025-02-03 01:28:50', 1),
(381, '::1', 'ziyad@gmail.com', 1, '2025-02-03 04:46:28', 1),
(382, '::1', 'syakira02@gmail.com', 5, '2025-02-03 04:47:44', 1),
(383, '::1', 'raihan@gmail.com', NULL, '2025-02-03 07:36:29', 0),
(384, '::1', 'raihan@gmail.com', 3, '2025-02-03 07:36:36', 1),
(385, '::1', 'ziyad@gmail.com', 1, '2025-02-03 12:42:04', 1),
(386, '::1', 'syakira02@gmail.com', 5, '2025-02-03 12:59:40', 1),
(387, '::1', 'ziyad@gmail.com', 1, '2025-02-04 02:15:51', 1),
(388, '::1', 'syakira02@gmail.com', 5, '2025-02-04 04:27:55', 1),
(389, '::1', 'ziyad@gmail.com', 1, '2025-02-04 07:42:09', 1);

-- --------------------------------------------------------

--
-- Table structure for table `auth_permissions`
--

CREATE TABLE `auth_permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auth_permissions`
--

INSERT INTO `auth_permissions` (`id`, `name`, `description`) VALUES
(1, 'manage-users', 'manage All Users'),
(2, 'manage-profile', 'manage User\'s profile'),
(3, 'manage-articles', 'Manage All Articles');

-- --------------------------------------------------------

--
-- Table structure for table `auth_reset_attempts`
--

CREATE TABLE `auth_reset_attempts` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_tokens`
--

CREATE TABLE `auth_tokens` (
  `id` int(10) UNSIGNED NOT NULL,
  `selector` varchar(255) NOT NULL,
  `hashedValidator` varchar(255) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_users_permissions`
--

CREATE TABLE `auth_users_permissions` (
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `permission_id` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(3, 'Berita Acara', '2024-12-12 09:13:07', '2024-12-12 09:13:07'),
(4, 'Edaran', '2024-12-12 07:42:55', '2024-12-12 07:42:55'),
(5, 'Panduan', '2024-11-24 06:07:03', '2024-11-24 13:07:03'),
(7, 'Pengumuman', '2024-12-13 02:00:22', '2024-12-13 09:00:22');

-- --------------------------------------------------------

--
-- Table structure for table `category_permissions`
--

CREATE TABLE `category_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_permissions`
--

INSERT INTO `category_permissions` (`id`, `user_id`, `category_id`) VALUES
(2, 2, 3),
(3, 2, 4),
(18, 1, 3),
(19, 1, 4),
(20, 1, 7);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `division_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `division_id`, `name`) VALUES
(1, 1, 'IT SERVICES MANAGEMENT'),
(2, 1, 'IT SERVICES GOVERNANCE'),
(3, 2, 'FSI AND DIGITAL COMPANIES ACCOUNT 1'),
(4, 2, 'FSI AND DIGITAL COMPANIES ACCOUNT 2'),
(5, 2, 'FSI AND DIGITAL COMPANIES ACCOUNT 3');

-- --------------------------------------------------------

--
-- Table structure for table `directorates`
--

CREATE TABLE `directorates` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `directorates`
--

INSERT INTO `directorates` (`id`, `name`) VALUES
(1, 'IT CORPORATE'),
(2, 'DIRECTOR AND CHIFF COMMERCIAL OFFICER');

-- --------------------------------------------------------

--
-- Table structure for table `divisions`
--

CREATE TABLE `divisions` (
  `id` int(11) NOT NULL,
  `directorate_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `divisions`
--

INSERT INTO `divisions` (`id`, `directorate_id`, `name`) VALUES
(1, 1, 'IT CORPORATE'),
(2, 2, 'FSI AND DIGITAL COMPANIES ACCOUNT');

-- --------------------------------------------------------

--
-- Table structure for table `fileuploads`
--

CREATE TABLE `fileuploads` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `author` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `type` enum('public','internal') NOT NULL DEFAULT 'public'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fileuploads`
--

INSERT INTO `fileuploads` (`id`, `title`, `description`, `author`, `file_path`, `created_at`, `updated_at`, `status`, `type`) VALUES
(1, 'Pengumuman Indosat Run', 'saat ini', 'ZiyadRifqi', 'public/fileupload/1731239156_9185c19da18efa4ab3c4.pdf', '2024-11-10 11:45:56', '2025-01-30 14:45:49', 'published', 'internal'),
(6, 'Agenda Bulan Pertengahan November 2024', 'Edaran', 'Ziyad', 'public/fileupload/1732799064_1d5c42a3dcdf46531c48.pdf', '2024-11-15 08:08:35', '2025-01-30 14:45:57', 'published', 'internal'),
(11, 'Pentingnya Transformasi Bisnis Digital​​ perusahaan', 'Melakukan kegiatan yang sangat penting untuk perusahaan ', 'minji', 'public/fileupload/1733819087_f403560aa78066b3a9f7.pdf', '2024-12-10 08:24:47', '2025-02-04 02:54:49', 'published', 'public'),
(13, 'pendaftaran', 'Melakukan kegiatan', 'ziyadr', 'public/fileupload/1738639839_c25b0142129bce0247d0.pdf', '2025-02-04 03:30:39', '2025-02-04 05:23:13', 'published', 'public');

-- --------------------------------------------------------

--
-- Table structure for table `file_categories`
--

CREATE TABLE `file_categories` (
  `id` int(11) NOT NULL,
  `fileuploads_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `file_categories`
--

INSERT INTO `file_categories` (`id`, `fileuploads_id`, `category_id`) VALUES
(24, 6, 3),
(25, 1, 3),
(32, 11, 3),
(33, 11, 4),
(41, 13, 4);

-- --------------------------------------------------------

--
-- Table structure for table `file_distributions`
--

CREATE TABLE `file_distributions` (
  `id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  `target_type` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `fileuploads_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `file_distributions`
--

INSERT INTO `file_distributions` (`id`, `target_id`, `target_type`, `created_at`, `fileuploads_id`) VALUES
(47, 1, 'sub_department', '2024-12-11 02:30:33', 6),
(48, 1, 'department', '2024-12-11 02:31:02', 1),
(49, 1, 'sub_department', '2024-12-11 02:31:02', 1),
(59, 2, 'department', '2025-02-04 02:54:34', 11),
(60, 2, 'sub_department', '2025-02-04 02:54:34', 11),
(61, 2, 'directorate', '2025-02-03 20:30:39', NULL),
(62, 4, 'sub_department', '2025-02-03 20:30:39', NULL),
(66, 5, 'sub_department', '2025-02-04 05:23:01', 13);

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `order_pos` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `name`, `icon`, `url`, `parent_id`, `order_pos`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'User Management', NULL, NULL, NULL, 3, 1, '2024-10-22 05:50:58', '2024-11-19 03:26:08'),
(2, 'Users', 'fas fa-user', 'admin', 1, 1, 1, '2024-10-22 05:50:58', '2024-11-07 08:17:25'),
(3, 'Role Management', 'fas fa-user-tag', NULL, 1, 2, 1, '2024-10-22 05:50:58', '2024-10-22 08:20:49'),
(4, 'Assign User Roles', 'far fa-circle', 'admin/user_roles', 3, 1, 1, '2024-10-22 05:50:58', '2024-10-22 05:50:58'),
(5, 'Manage Roles', 'far fa-circle', 'admin/roles', 3, 2, 1, '2024-10-22 05:50:58', '2024-11-07 08:17:49'),
(9, 'Content Management System', 'fas fa-newspaper', NULL, NULL, 2, 1, '2024-10-22 05:50:58', '2024-11-19 03:26:01'),
(10, 'Article', 'fas fa-newspaper', 'admin/article', 9, 1, 1, '2024-10-22 05:50:58', '2024-11-07 08:15:31'),
(11, 'Categories', 'fas fa-boxes', 'admin/categories', 24, 1, 1, '2024-11-08 01:19:18', '2024-11-24 13:14:17'),
(12, 'Work Instruction', 'fas fa-file-upload', 'admin/fileuploads', 9, 3, 1, '2024-11-08 09:19:53', '2025-01-23 01:07:14'),
(13, 'Management Levels', 'fas fa-sitemap', NULL, 1, 3, 1, '2024-11-13 02:59:12', '2024-11-13 02:59:12'),
(14, 'Directorates', 'far fa-circle', 'admin/hirarki/directorate', 13, 1, 1, '2024-11-13 03:01:34', '2024-11-13 06:46:15'),
(15, 'Divisions', 'far fa-circle', 'admin/hirarki/division', 13, 2, 1, '2024-11-13 03:07:30', '2024-11-13 08:29:38'),
(16, 'Departments', 'far fa-circle', 'admin/hirarki/departement', 13, 3, 1, '2024-11-13 03:08:08', '2024-11-13 06:17:48'),
(17, 'Sub Departements', 'far fa-circle', 'admin/hirarki/subdepart', 13, 4, 1, '2024-11-13 03:09:15', '2024-11-14 08:27:36'),
(18, 'Attendance', 'fas fa-user-check', 'absensi', 19, 1, 1, '2024-11-19 03:07:52', '2024-11-20 07:49:11'),
(19, 'Attendance Management', NULL, NULL, NULL, 1, 1, '2024-11-19 09:46:21', '2024-11-20 07:49:21'),
(20, 'Attendance Categories', 'fas fa-tasks', 'Absensi/categories', 19, 1, 1, '2024-11-19 10:18:01', '2025-02-01 14:19:45'),
(21, 'Attendance history', 'fas fa-history', 'absensi/history', 19, 3, 1, '2024-11-20 04:49:03', '2024-11-20 07:48:39'),
(22, 'User Attendance List', 'fas fa-history', 'absensi/superadmin/history', 19, 2, 1, '2024-11-21 07:06:02', '2025-02-01 14:19:49'),
(23, 'Category User Permission', 'fas fa-lock', 'admin/category-permissions', 24, 2, 1, '2024-11-21 13:29:06', '2024-12-03 08:54:03'),
(24, 'Category Management', 'fas fa-th-list', NULL, NULL, 1, 1, '2024-11-22 08:42:32', '2024-11-24 13:08:34'),
(25, 'Activity', NULL, NULL, NULL, 1, 1, '2025-01-14 02:41:40', '2025-01-14 02:41:40'),
(26, 'Field Activities', 'fas fa-tasks', 'admin/activity', 19, 4, 1, '2025-01-14 02:44:28', '2025-02-01 14:17:13'),
(27, 'Admin Activities History', 'fas fa-clipboard-list', 'activity/history', 19, 4, 1, '2025-01-14 09:29:34', '2025-02-01 14:17:57'),
(28, 'HOME', 'fas fa-home', 'user/', NULL, 0, 1, '2025-02-01 14:13:25', '2025-02-01 14:14:51'),
(29, 'Manajemen Visitor', NULL, NULL, NULL, 2, 1, '2025-02-04 08:32:06', '2025-02-04 08:35:03'),
(31, 'Guest Visitor', 'fas fa-users', 'guest-visitor', 29, 1, 1, '2025-02-04 08:32:50', '2025-02-04 08:32:50'),
(32, 'Referesh Token', 'fas fa-sync', 'token', 29, 2, 1, '2025-02-04 09:00:06', '2025-02-04 09:00:06');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(2, '2017-11-20-223112', 'Myth\\Auth\\Database\\Migrations\\CreateAuthTables', 'default', 'Myth\\Auth', 1729217321, 1);

-- --------------------------------------------------------

--
-- Table structure for table `monitoring_tickets`
--

CREATE TABLE `monitoring_tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ticket_number` varchar(6) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `status_ticket` enum('open','closed','resolved','rejected','stopclock') NOT NULL DEFAULT 'open',
  `status_approval` enum('pending','Approval Not Provided','successfully approval') NOT NULL DEFAULT 'pending',
  `resolution` text DEFAULT NULL,
  `conversation` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monitoring_tickets`
--

INSERT INTO `monitoring_tickets` (`id`, `user_id`, `ticket_number`, `subject`, `status_ticket`, `status_approval`, `resolution`, `conversation`, `created_at`, `updated_at`) VALUES
(5, 2, '627277', 'proses ba', 'open', 'pending', 'melakukan', 'kepada yang terhormat', '2024-12-01 19:46:12', '2024-12-01 19:46:12'),
(6, 2, '378728', 'Monitoring website 3', 'stopclock', 'successfully approval', 'bukan  untuk', 'Kepada yang terhormat ', '2024-12-02 02:55:00', '2024-12-02 02:55:00'),
(7, 3, '263762', 'Proses BAP', 'open', 'Approval Not Provided', 'Akan dilakukan', 'Kepada yang terhormat', '2024-12-02 07:45:36', '2024-12-02 07:45:36');

-- --------------------------------------------------------

--
-- Table structure for table `role_menus`
--

CREATE TABLE `role_menus` (
  `id` int(11) NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `menu_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_menus`
--

INSERT INTO `role_menus` (`id`, `role_id`, `menu_id`, `created_at`) VALUES
(1, 1, 1, '2024-10-22 05:51:43'),
(2, 1, 9, '2024-10-22 05:51:43'),
(3, 1, 2, '2024-10-22 05:51:43'),
(4, 3, 3, '2024-10-22 05:51:43'),
(6, 3, 4, '2024-10-22 05:51:43'),
(7, 3, 5, '2024-10-22 05:51:43'),
(10, 1, 10, '2024-10-22 05:51:43'),
(11, 3, 11, '2024-11-08 01:20:14'),
(12, 3, 14, '2024-11-13 04:36:55'),
(13, 3, 1, '2024-11-18 03:46:04'),
(14, 3, 2, '2024-11-18 03:57:43'),
(15, 1, 12, '2024-11-18 09:00:28'),
(16, 3, 16, '2024-11-18 09:28:42'),
(17, 3, 15, '2024-11-18 09:29:08'),
(18, 3, 17, '2024-11-18 09:29:08'),
(19, 1, 18, '2024-11-19 03:08:13'),
(20, 3, 20, '2024-11-19 10:18:22'),
(21, 1, 21, '2024-11-20 04:49:25'),
(22, 3, 19, '2024-11-21 07:06:17'),
(23, 3, 22, '2024-11-21 07:06:30'),
(24, 3, 9, '2024-11-21 09:23:51'),
(25, 3, 23, '2024-11-21 13:29:24'),
(26, 3, 24, '2024-11-22 08:44:32'),
(27, 1, 25, '2025-01-14 02:46:46'),
(28, 1, 26, '2025-01-14 02:46:46'),
(29, 3, 25, '2025-01-14 09:30:05'),
(30, 3, 27, '2025-01-14 09:30:05'),
(33, 1, 28, '2025-02-01 14:14:08'),
(34, 3, 28, '2025-02-01 14:14:08'),
(35, 1, 29, '2025-02-04 08:33:24'),
(36, 1, 31, '2025-02-04 08:33:24'),
(37, 1, 32, '2025-02-04 09:00:27');

-- --------------------------------------------------------

--
-- Table structure for table `sub_departments`
--

CREATE TABLE `sub_departments` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_departments`
--

INSERT INTO `sub_departments` (`id`, `department_id`, `name`) VALUES
(1, 1, 'ITSM'),
(2, 2, 'ITSG'),
(3, 3, 'FSIDCA 1'),
(4, 4, 'FSIDCA 2'),
(5, 5, 'FSIDCA 3');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(30) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `initial` varchar(10) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `user_image` varchar(255) NOT NULL DEFAULT 'default.png',
  `password_hash` varchar(255) NOT NULL,
  `reset_hash` varchar(255) DEFAULT NULL,
  `reset_at` datetime DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `activate_hash` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `status_message` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `force_pass_reset` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `directorate_id` int(11) DEFAULT NULL,
  `division_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `sub_department_id` int(11) DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `fullname`, `initial`, `position`, `user_image`, `password_hash`, `reset_hash`, `reset_at`, `reset_expires`, `activate_hash`, `status`, `status_message`, `active`, `force_pass_reset`, `created_at`, `updated_at`, `deleted_at`, `directorate_id`, `division_id`, `department_id`, `sub_department_id`, `signature`) VALUES
(1, 'ziyad@gmail.com', 'ZiyadRifqi', 'ZiyadRifqi Permana', NULL, 'Programmer', '1731054156_e3b6a61f7842719fc65b.jpg', '$2y$10$uJvfrqNeXtBFqI4jMWctqOM0C0rurnq5IylD48a5dWhTPyJoEa75O', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2024-10-18 02:15:11', '2024-10-18 02:15:11', NULL, 1, 1, 1, 1, '1_20250201_144649.png'),
(2, 'Rassya@gmail.com', 'Rassya', 'Rassya putra', 'RSY', 'Manager', 'default.png', '$2y$10$BG2KPonEID0IuaaVqTxDkO7RcVoHGIoN/bbmqS5lyVQFgMsbavUgK', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2024-10-18 02:32:13', '2024-10-18 02:32:13', NULL, 1, 1, 1, 1, NULL),
(3, 'raihan@gmail.com', 'Raihan Putra', 'Raihan Putra', 'RPA', 'Officer', 'default.png', '$2y$10$btcpZQMRF0Qh5HxR5dPf2.NI5gfDdJkd/iUPqIpI16gDu5fZKWn0O', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2024-10-29 06:36:27', '2024-10-29 06:36:27', NULL, 2, 2, 3, 3, NULL),
(5, 'syakira02@gmail.com', 'syakira', NULL, NULL, NULL, 'default.png', '$2y$10$w7jofpeiSGUtmwcb52W7Z.hAFb2htwwyutPmjzla0zEptNmPOFZXe', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2024-11-18 03:52:45', '2024-11-18 03:52:45', NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_absensi_user` (`user_id`),
  ADD KEY `fk_absensi_category` (`category_id`);

--
-- Indexes for table `absen_category`
--
ALTER TABLE `absen_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_activities`
--
ALTER TABLE `admin_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_admin_activities_users` (`user_id`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `article_categories`
--
ALTER TABLE `article_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `article_distributions`
--
ALTER TABLE `article_distributions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`);

--
-- Indexes for table `auth_activation_attempts`
--
ALTER TABLE `auth_activation_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auth_groups`
--
ALTER TABLE `auth_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auth_groups_permissions`
--
ALTER TABLE `auth_groups_permissions`
  ADD KEY `auth_groups_permissions_permission_id_foreign` (`permission_id`),
  ADD KEY `group_id_permission_id` (`group_id`,`permission_id`);

--
-- Indexes for table `auth_groups_users`
--
ALTER TABLE `auth_groups_users`
  ADD KEY `auth_groups_users_user_id_foreign` (`user_id`),
  ADD KEY `group_id_user_id` (`group_id`,`user_id`);

--
-- Indexes for table `auth_logins`
--
ALTER TABLE `auth_logins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `auth_permissions`
--
ALTER TABLE `auth_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auth_reset_attempts`
--
ALTER TABLE `auth_reset_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `auth_tokens_user_id_foreign` (`user_id`),
  ADD KEY `selector` (`selector`);

--
-- Indexes for table `auth_users_permissions`
--
ALTER TABLE `auth_users_permissions`
  ADD KEY `auth_users_permissions_permission_id_foreign` (`permission_id`),
  ADD KEY `user_id_permission_id` (`user_id`,`permission_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_permissions`
--
ALTER TABLE `category_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `division_id` (`division_id`);

--
-- Indexes for table `directorates`
--
ALTER TABLE `directorates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `divisions`
--
ALTER TABLE `divisions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `directorate_id` (`directorate_id`);

--
-- Indexes for table `fileuploads`
--
ALTER TABLE `fileuploads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `file_categories`
--
ALTER TABLE `file_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fileuploads_id` (`fileuploads_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `file_distributions`
--
ALTER TABLE `file_distributions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fileuploads_id` (`fileuploads_id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monitoring_tickets`
--
ALTER TABLE `monitoring_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_number` (`ticket_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `role_menus`
--
ALTER TABLE `role_menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_id` (`menu_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `sub_departments`
--
ALTER TABLE `sub_departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_directorate_id` (`directorate_id`),
  ADD KEY `fk_division_id` (`division_id`),
  ADD KEY `fk_department_id` (`department_id`),
  ADD KEY `fk_sub_department_id` (`sub_department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `absen_category`
--
ALTER TABLE `absen_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin_activities`
--
ALTER TABLE `admin_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `article_categories`
--
ALTER TABLE `article_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `article_distributions`
--
ALTER TABLE `article_distributions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=568;

--
-- AUTO_INCREMENT for table `auth_activation_attempts`
--
ALTER TABLE `auth_activation_attempts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auth_groups`
--
ALTER TABLE `auth_groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `auth_logins`
--
ALTER TABLE `auth_logins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=390;

--
-- AUTO_INCREMENT for table `auth_permissions`
--
ALTER TABLE `auth_permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `auth_reset_attempts`
--
ALTER TABLE `auth_reset_attempts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `category_permissions`
--
ALTER TABLE `category_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `directorates`
--
ALTER TABLE `directorates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `divisions`
--
ALTER TABLE `divisions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `fileuploads`
--
ALTER TABLE `fileuploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `file_categories`
--
ALTER TABLE `file_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `file_distributions`
--
ALTER TABLE `file_distributions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `monitoring_tickets`
--
ALTER TABLE `monitoring_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `role_menus`
--
ALTER TABLE `role_menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `sub_departments`
--
ALTER TABLE `sub_departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `fk_absensi_category` FOREIGN KEY (`category_id`) REFERENCES `absen_category` (`id`),
  ADD CONSTRAINT `fk_absensi_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `admin_activities`
--
ALTER TABLE `admin_activities`
  ADD CONSTRAINT `fk_admin_activities_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `article_categories`
--
ALTER TABLE `article_categories`
  ADD CONSTRAINT `article_categories_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `article_distributions`
--
ALTER TABLE `article_distributions`
  ADD CONSTRAINT `article_distributions_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `auth_groups_permissions`
--
ALTER TABLE `auth_groups_permissions`
  ADD CONSTRAINT `auth_groups_permissions_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `auth_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auth_groups_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `auth_permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `auth_groups_users`
--
ALTER TABLE `auth_groups_users`
  ADD CONSTRAINT `auth_groups_users_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `auth_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auth_groups_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD CONSTRAINT `auth_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `auth_users_permissions`
--
ALTER TABLE `auth_users_permissions`
  ADD CONSTRAINT `auth_users_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `auth_permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auth_users_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `category_permissions`
--
ALTER TABLE `category_permissions`
  ADD CONSTRAINT `category_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_permissions_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_ibfk_1` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `divisions`
--
ALTER TABLE `divisions`
  ADD CONSTRAINT `divisions_ibfk_1` FOREIGN KEY (`directorate_id`) REFERENCES `directorates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `file_categories`
--
ALTER TABLE `file_categories`
  ADD CONSTRAINT `file_categories_ibfk_1` FOREIGN KEY (`fileuploads_id`) REFERENCES `fileuploads` (`id`),
  ADD CONSTRAINT `file_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `file_distributions`
--
ALTER TABLE `file_distributions`
  ADD CONSTRAINT `file_distributions_ibfk_1` FOREIGN KEY (`fileuploads_id`) REFERENCES `fileuploads` (`id`);

--
-- Constraints for table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `monitoring_tickets`
--
ALTER TABLE `monitoring_tickets`
  ADD CONSTRAINT `monitoring_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `role_menus`
--
ALTER TABLE `role_menus`
  ADD CONSTRAINT `role_menus_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_menus_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `auth_groups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sub_departments`
--
ALTER TABLE `sub_departments`
  ADD CONSTRAINT `sub_departments_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_department_id` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `fk_directorate_id` FOREIGN KEY (`directorate_id`) REFERENCES `directorates` (`id`),
  ADD CONSTRAINT `fk_division_id` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`),
  ADD CONSTRAINT `fk_sub_department_id` FOREIGN KEY (`sub_department_id`) REFERENCES `sub_departments` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
