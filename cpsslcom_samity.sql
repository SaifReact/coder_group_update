-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 16, 2026 at 09:55 PM
-- Server version: 11.4.10-MariaDB
-- PHP Version: 8.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cpsslcom_samity`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_close`
--

CREATE TABLE `account_close` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `member_code` varchar(100) NOT NULL,
  `reasons` text DEFAULT NULL,
  `total_amt` decimal(14,2) NOT NULL DEFAULT 0.00,
  `none_refund` decimal(14,2) NOT NULL DEFAULT 0.00,
  `deduction` decimal(14,2) NOT NULL DEFAULT 0.00,
  `refund_amt` decimal(14,2) NOT NULL DEFAULT 0.00,
  `status` varchar(3) DEFAULT NULL,
  `agreed` varchar(3) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account_close`
--

INSERT INTO `account_close` (`id`, `member_id`, `member_code`, `reasons`, `total_amt`, `none_refund`, `deduction`, `refund_amt`, `status`, `agreed`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 69, 'CPSS-00069', 'Not interested. For kind information, refund amount will be 49000.', 45000.00, 1500.00, 4500.00, 40500.00, 'I', '1', '2026-02-15 12:55:20', 31, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `banner`
--

CREATE TABLE `banner` (
  `id` int(11) NOT NULL,
  `banner_name_bn` varchar(255) NOT NULL,
  `banner_name_en` varchar(255) NOT NULL,
  `banner_image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `banner_category` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banner`
--

INSERT INTO `banner` (`id`, `banner_name_bn`, `banner_name_en`, `banner_image`, `created_at`, `banner_category`) VALUES
(1, 'ব্যানার-১', 'Banner-1', 'banner_1760121722_5628.jpg', '2025-09-20 17:33:14', 'ban'),
(2, 'ব্যানার-২', 'Banner-2', 'banner_1759411483_5300.jpg', '2025-09-06 03:30:27', 'ban'),
(3, 'ব্যানার-৩', 'Banner-3', 'banner_1759411507_3439.jpg', '2025-09-06 02:07:54', 'ban'),
(4, 'ব্যানার-৪', 'Banner-4', 'banner_1759411534_5124.png', '2025-10-02 13:25:34', 'ban'),
(10, 'নিবন্ধন সনদপত্র', 'Registration Certificate', 'banner_1761845042_5250.pdf', '2025-10-30 17:24:02', 'oth'),
(14, 'তথ্য বিবরণী', 'Information Statement', 'banner_1762169922_9058.pdf', '2025-11-03 11:38:42', 'oth'),
(15, 'নিবন্ধিত কমিটি', 'Registered Committee', 'banner_1762169978_9881.pdf', '2025-11-03 11:39:38', 'oth'),
(16, 'উপ-আইন', 'By-laws', 'banner_1762169992_3139.pdf', '2025-11-03 11:39:52', 'oth'),
(17, 'সভাপতি', 'President', 'banner_1762663910_8982.png', '2025-11-09 04:51:50', 'sig'),
(18, 'সম্পাদক', 'Secretary', 'banner_1762663944_7592.png', '2025-11-09 04:52:24', 'sig'),
(19, 'কোষাধ্যক্ষ', 'Cashier', 'banner_1763823270_8917.png', '2025-11-22 14:54:30', 'sig');

-- --------------------------------------------------------

--
-- Table structure for table `committee_member`
--

CREATE TABLE `committee_member` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `member_code` varchar(50) NOT NULL,
  `committee_role_id` int(11) NOT NULL,
  `fb` varchar(100) NOT NULL,
  `li` varchar(100) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `committee_member`
--

INSERT INTO `committee_member` (`id`, `member_id`, `member_code`, `committee_role_id`, `fb`, `li`, `role`, `created_at`) VALUES
(11, 1, 'CPSS-00001', 2, 'https://www.facebook.com/md.saifur.rahman.635543/', 'linkedin.com', 'Entrepreneur', NULL),
(12, 42, 'CPSS-00042', 4, 'facebook.com', 'linkedin.com', 'Entrepreneur', NULL),
(13, 30, 'CPSS-00002', 5, 'ana_pvz@yahoo.com', 'linkedin.com', 'Entrepreneur', NULL),
(14, 34, 'CPSS-00032', 3, 'facebook.com', 'anwarapparels@gmail.com', 'Committee Member', NULL),
(15, 76, 'CPSS-00076', 6, 'facebook.com', 'linkedin.com', 'Entrepreneur', NULL),
(16, 74, 'CPSS-00074', 7, 'facebook.com', 'linkedin.com', 'Committee Member', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `committee_role`
--

CREATE TABLE `committee_role` (
  `id` int(11) NOT NULL,
  `position_en` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `position_bn` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `committee_role`
--

INSERT INTO `committee_role` (`id`, `position_en`, `position_bn`, `created_at`) VALUES
(1, 'Advisor', 'উপদেষ্টা', '2025-11-05 08:14:32'),
(2, 'President', 'সভাপতি', '2025-11-05 08:20:01'),
(3, 'Vice-President', 'উপ-সভাপতি', '2025-11-05 08:20:01'),
(4, 'Secretary', 'সম্পাদক', '2025-11-05 08:20:01'),
(5, 'Joint Secretary', 'যুগ্ম সম্পাদক', '2025-11-05 08:20:01'),
(6, 'Treasurer', 'কোষাধ্যক্ষ', '2025-11-05 08:20:01'),
(7, 'Member', 'সদস্য, ব্যবস্থাপনা কমিটি', '2025-11-05 08:23:35'),
(8, 'Executive', 'কার্যকরী কমিটি', '2025-11-05 08:23:35');

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `id` int(11) NOT NULL,
  `company_name_bn` varchar(255) NOT NULL,
  `company_name_en` varchar(255) NOT NULL,
  `company_image` varchar(255) NOT NULL,
  `about_company` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`id`, `company_name_bn`, `company_name_en`, `company_image`, `about_company`, `created_at`) VALUES
(8, 'কোডার ফিন্যান্স', 'Coder Finance', '1760107958_Finance.jpg', '<p>কোডার পেশাজীবী সমবায় সমিতি লিঃ নিজস্ব পরিচালিত প্রজেক্ট হলো কোডার সঞ্চয় ও ঋন এর ফিন্যান্স প্রজেক্টটি।</p><ul><li>সদস্যদের সঞ্চয়কৃত আমানত সমিতির নামে ইসলামিক হিসাব নম্বরে সঞ্চয় করা।&nbsp;</li><li>স্বল্প সার্ভিস চার্জে সদস্যদের ঋন সুবিধা প্রদান করা।</li><li>সঞ্চয়কৃত আমানত নির্ধারিত বিভিন্ন প্রজেক্ট এ বিনিয়োগ করে ব্যবসা পরিচালনা করা।</li></ul>', '2025-10-02 19:07:40'),
(9, 'কোডার হোমস ও বিল্ডার্স', 'Coder Homes & Builders', '1759410595_building.JPG', '<ul><li>আবাসিক ও বানিজ্যিক প্রকল্পে (ফ্ল্যাট, জমি) ক্রয় করে বিনিয়োগ করা।</li><li>সদস্যদের মাঝে স্বল্প সার্ভিস চার্জে বসতবাড়ি অথবা জায়গা ডেভেলপ করা।</li><li>নতুন বাজারে রিয়েল এস্টেট ব্যবসার আধুনিকতা সৃষ্টি করা।</li></ul>', '2025-10-02 19:09:55'),
(10, 'কোডার মার্ট ও এগ্রো', 'Coder Mart & Agro', '1759410688_ghee-honey.jpg', '<ul><li>সদস্য বা গ্রাহকদের মাসিক ও সাপ্তাহিক বাজার প্যাকেজ হোম ডেলিভারী ব্যবস্থা গ্রহন করা।</li><li>অরগানিক ও কৃষি ও খুচরা পণ্য সরবারহ চেইন সম্প্রসারন করা।</li><li>স্থানীয় চাষী ও সাপ্লায়দের সাথে অংশীদারিত্ব বৃদ্ধি করা।</li></ul>', '2025-10-02 19:11:28'),
(11, 'কোডার আইটি ও ইনস্টিটিউট', 'Coder IT & Institute', '1759410837_it.jpg', '<ul><li>সফটওয়্যার সেবা, আইটি কনসালটিং ও আউটসোসিং থেকে আয় করা।</li><li>সদস্যদের মাঝে আধুুনিক আইটি ট্রেনিং এর ব্যবস্থা করা।</li><li>সার্টিফিকেশন কোর্স চালু করা, যা সদস্য ও অনলাইন-অফলাইন শিক্ষার্থীদের আইটি নির্ভর করা।</li></ul>', '2025-10-02 19:13:57'),
(12, 'কোডার হজ্জ্ব ও ওমরাহ্', 'Coder Hajj & Umrah', '1759410941_hijaz-umrah.png', '<ul><li>সদস্যদের ইএমআই ভিত্তিতে মানসম্মত হজ্জ্ব ও ওমরাহ্ প্যাকেজ ব্যবস্থা গ্রহন করা।</li><li>সরাসরি ভ্রমন এজেন্সী ও ধর্মীয় প্রতিষ্ঠানের সাথে অংশীদারিত্ব করা।</li><li>গ্রাহক-বিস্তারিত সার্ভিস এবং ভ্রমনপূর্বক নির্দেশিকা প্রদান।</li></ul>', '2025-10-02 19:15:41'),
(13, 'কোডার ফাউন্ডেশন', 'Coder Foundation', '1759411020_foundation.jpg', '<ul><li>সমাজকল্যান মূলক কার্যক্রম (শিক্ষা, স্বাস্থ্য ও দাতব্য) চালু করা।</li><li>সম্প্রদায়ের সাথে দৃঢ় সম্পর্ক তৈরি করা।</li><li>দাতব্য অনুদান ও সামাজিক প্রকল্প থেকে টেকসই অর্থায়ন।</li></ul>', '2025-10-02 19:17:00');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `exp_date` text NOT NULL,
  `exp_cat` int(11) NOT NULL,
  `amount` int(100) NOT NULL,
  `reference` text NOT NULL,
  `note` text NOT NULL,
  `exp_slip` text NOT NULL,
  `status` varchar(3) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_category`
--

CREATE TABLE `expense_category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(3) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `glac_mst`
--

CREATE TABLE `glac_mst` (
  `id` int(11) NOT NULL,
  `glac_code` varchar(30) NOT NULL,
  `glac_name` varchar(200) NOT NULL,
  `parent_child` varchar(3) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `glac_type` varchar(1) DEFAULT NULL,
  `level_code` int(11) DEFAULT NULL,
  `gl_nature` varchar(1) DEFAULT NULL,
  `allow_manual_dr` varchar(1) DEFAULT NULL,
  `allow_manual_cr` varchar(1) DEFAULT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'N',
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(50) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_bank_balance` tinyint(1) NOT NULL DEFAULT 0,
  `is_cash_in_hand` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `glac_mst`
--

INSERT INTO `glac_mst` (`id`, `glac_code`, `glac_name`, `parent_child`, `parent_id`, `glac_type`, `level_code`, `gl_nature`, `allow_manual_dr`, `allow_manual_cr`, `status`, `created_by`, `created_at`, `updated_by`, `updated_at`, `is_bank_balance`, `is_cash_in_hand`) VALUES
(1, '1', 'Asset', 'P', 0, '1', 1, 'D', 'N', 'N', 'A', '2', '2026-01-20 09:52:06', NULL, '2026-01-20 09:52:06', 0, 0),
(2, '101', 'Fixed Asset', 'P', 1, '1', 2, 'D', 'N', 'N', 'A', '2', '2026-01-20 09:52:29', NULL, '2026-01-20 09:52:29', 0, 0),
(3, '10101', 'Land & Development', 'P', 2, '1', 3, 'D', 'N', 'N', 'A', '2', '2026-01-20 09:52:42', NULL, '2026-01-20 09:52:42', 0, 0),
(4, '10101001', 'Land', 'C', 3, '1', 4, 'D', 'Y', 'Y', 'A', '2', '2026-01-20 09:52:58', NULL, '2026-01-20 09:52:58', 0, 0),
(5, '10101002', 'Building', 'C', 3, '1', 4, 'D', 'Y', 'Y', 'A', '2', '2026-01-20 09:53:13', NULL, '2026-01-20 09:53:13', 0, 0),
(6, '102', 'Current Assets', 'P', 1, '1', 2, 'D', 'N', 'N', 'A', '2', '2026-01-20 23:47:23', NULL, '2026-01-20 23:47:23', 0, 0),
(7, '10201', 'Cash in Hand', 'P', 6, '1', 3, 'D', 'N', 'N', 'A', '2', '2026-01-20 23:47:59', NULL, '2026-01-20 23:47:59', 0, 0),
(8, '10201001', 'Cash in Hand', 'C', 7, '1', 4, 'D', 'Y', 'Y', 'A', '2', '2026-01-20 23:48:13', '65', '2026-02-11 16:41:29', 0, 1),
(10, '10202', 'Cash At Bank', 'P', 6, '1', 3, 'D', 'N', 'N', 'A', '2', '2026-01-20 23:49:34', NULL, '2026-01-20 23:49:34', 0, 0),
(11, '10202001', 'Bank Asia Ltd (50301001763)', 'C', 10, '1', 4, 'D', 'Y', 'Y', 'A', '2', '2026-01-20 23:49:56', '65', '2026-02-11 16:39:44', 1, 0),
(12, '10202002', 'Bank Asia Ltd (50311010265)', 'C', 10, '1', 4, 'D', 'Y', 'Y', 'A', '2', '2026-01-20 23:51:41', '65', '2026-02-11 16:39:50', 1, 0),
(14, '103', 'Investments', 'P', 1, '1', 2, 'D', 'N', 'N', 'A', '65', '2026-02-05 09:33:27', NULL, '2026-02-05 09:33:27', 0, 0),
(15, '10301', 'Investment from Samity Share', 'P', 14, '1', 3, 'D', 'N', 'N', 'A', '65', '2026-02-05 09:33:57', NULL, '2026-02-05 09:33:57', 0, 0),
(16, '10301001', 'Investment Land', 'C', 15, '1', 4, 'D', 'N', 'N', 'A', '65', '2026-02-05 09:34:20', NULL, '2026-02-05 09:34:20', 0, 0),
(17, '10301002', 'Investment in Other Businesses', 'C', 15, '1', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 09:34:57', NULL, '2026-02-05 09:34:57', 0, 0),
(18, '10302', 'Investment From Project Share', 'P', 14, '1', 3, 'D', 'N', 'N', 'A', '65', '2026-02-05 09:35:47', NULL, '2026-02-05 09:35:47', 0, 0),
(19, '10302001', 'Investment Land - Dhaleshwari Project-1', 'C', 18, '1', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 09:36:49', NULL, '2026-02-05 09:36:49', 0, 0),
(20, '10302002', 'Investment in Other Project', 'C', 18, '1', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 09:37:23', NULL, '2026-02-05 09:37:23', 0, 0),
(21, '104', 'Accounts Receivable', 'P', 1, '1', 2, 'C', 'N', 'N', 'A', '65', '2026-02-05 09:39:01', NULL, '2026-02-05 09:39:01', 0, 0),
(22, '10401', 'Samity Accounts Receivable', 'P', 21, '1', 3, 'C', 'N', 'N', 'A', '65', '2026-02-05 09:39:30', NULL, '2026-02-05 09:39:30', 0, 0),
(23, '10401001', 'Accounts Receivable from Party 1', 'C', 22, '1', 4, 'C', 'Y', 'Y', 'A', '65', '2026-02-05 09:39:59', NULL, '2026-02-05 09:39:59', 0, 0),
(24, '10402', 'Project  Accounts Receivable', 'P', 21, '1', 3, 'C', 'N', 'N', 'A', '65', '2026-02-05 09:40:35', NULL, '2026-02-05 09:40:35', 0, 0),
(25, '10402001', 'Accounts Receivable from Party 1', 'C', 24, '1', 4, 'C', 'Y', 'Y', 'A', '65', '2026-02-05 09:41:04', NULL, '2026-02-05 09:41:04', 0, 0),
(26, '2', 'Liability', 'P', 0, '2', 1, 'C', 'N', 'N', 'A', '65', '2026-02-05 09:41:47', NULL, '2026-02-05 09:41:47', 0, 0),
(27, '201', 'Current Liability', 'P', 26, '2', 2, 'C', 'Y', 'Y', 'A', '65', '2026-02-05 09:42:10', NULL, '2026-02-05 09:42:10', 0, 0),
(28, '20101', 'Member Savings', 'P', 27, '2', 3, 'C', 'N', 'N', 'A', '65', '2026-02-05 09:42:43', NULL, '2026-02-05 09:42:43', 0, 0),
(29, '20101001', 'Monthly Member Savings', 'C', 28, '2', 4, 'C', 'Y', 'Y', 'A', '65', '2026-02-05 09:43:04', NULL, '2026-02-05 09:43:04', 0, 0),
(30, '202', 'Share Capital', 'P', 26, '2', 2, 'C', 'N', 'N', 'A', '65', '2026-02-05 09:43:47', NULL, '2026-02-05 09:43:47', 0, 0),
(31, '20201', 'Samity Share Capital', 'P', 30, '2', 3, 'C', 'N', 'N', 'A', '65', '2026-02-05 09:44:14', NULL, '2026-02-05 09:44:14', 0, 0),
(32, '20201001', 'Samity Issued Shares', 'C', 31, '2', 4, 'C', 'Y', 'Y', 'A', '65', '2026-02-05 09:44:36', NULL, '2026-02-05 09:44:36', 0, 0),
(33, '20202', 'Project Share Capital', 'P', 30, '2', 3, 'C', 'N', 'N', 'A', '65', '2026-02-05 09:45:56', NULL, '2026-02-05 09:45:56', 0, 0),
(34, '20202001', 'Dhaleshwari Project-1  Issued Shares', 'C', 33, '2', 4, 'C', 'Y', 'Y', 'A', '65', '2026-02-05 09:46:18', NULL, '2026-02-05 09:46:18', 0, 0),
(35, '203', 'Accounts Payable', 'P', 26, '2', 2, 'C', 'N', 'N', 'A', '65', '2026-02-05 09:47:04', NULL, '2026-02-05 09:47:04', 0, 0),
(36, '20301', 'Samity Accounts Payable', 'P', 35, '2', 3, 'C', 'N', 'N', 'A', '65', '2026-02-05 09:47:32', '65', '2026-02-05 09:48:10', 0, 0),
(37, '20301001', 'Accounts Payable to Vendors 1', 'C', 36, '2', 4, 'C', 'Y', 'Y', 'A', '65', '2026-02-05 09:48:52', NULL, '2026-02-05 09:48:52', 0, 0),
(38, '20302', 'Project Accounts Payable', 'P', 35, '2', 3, 'C', 'N', 'N', 'A', '65', '2026-02-05 09:49:53', NULL, '2026-02-05 09:49:53', 0, 0),
(39, '20302001', 'Accounts Payable to Vendors 1', 'C', 38, '2', 4, 'C', 'Y', 'Y', 'A', '65', '2026-02-05 09:50:14', NULL, '2026-02-05 09:50:14', 0, 0),
(40, '3', 'Income', 'P', 0, '3', 1, 'C', 'N', 'N', 'A', '65', '2026-02-05 09:51:12', NULL, '2026-02-05 09:51:12', 0, 0),
(41, '301', 'General Income', 'P', 40, '3', 2, 'C', 'N', 'N', 'A', '65', '2026-02-05 09:52:00', NULL, '2026-02-05 09:52:00', 0, 0),
(42, '30101', 'Income From Member', 'P', 41, '3', 3, 'C', 'N', 'N', 'A', '65', '2026-02-05 09:52:47', NULL, '2026-02-05 09:52:47', 0, 0),
(43, '30101001', 'Admission Fee', 'C', 42, '3', 4, 'C', 'Y', 'Y', 'A', '65', '2026-02-05 09:53:15', NULL, '2026-02-05 09:53:15', 0, 0),
(44, '30101002', 'Monthly Late Fee', 'C', 42, '3', 4, 'C', 'Y', 'Y', 'A', '65', '2026-02-05 09:53:41', NULL, '2026-02-05 09:53:41', 0, 0),
(45, '302', 'Profit', 'P', 40, '3', 2, 'C', 'N', 'N', 'A', '65', '2026-02-05 09:54:41', NULL, '2026-02-05 09:54:41', 0, 0),
(46, '30201', 'Bank Profit', 'P', 45, '3', 3, 'C', 'N', 'N', 'A', '65', '2026-02-05 09:55:05', NULL, '2026-02-05 09:55:05', 0, 0),
(47, '30201001', 'Bank Asia Ltd (50311010265)', 'C', 46, '3', 4, 'C', 'Y', 'Y', 'A', '65', '2026-02-05 09:55:30', '65', '2026-02-11 16:40:50', 1, 0),
(48, '30201002', 'Bank Asia Ltd (50301001763)', 'C', 46, '3', 4, 'C', 'Y', 'Y', 'A', '65', '2026-02-05 09:55:50', NULL, '2026-02-05 09:55:50', 0, 0),
(49, '30202', 'Other Profit', 'P', 45, '3', 3, 'C', 'N', 'N', 'A', '65', '2026-02-05 09:56:18', NULL, '2026-02-05 09:56:18', 0, 0),
(50, '30202001', 'Miscellaneous Profit', 'C', 49, '3', 4, 'C', 'Y', 'Y', 'A', '65', '2026-02-05 09:56:47', NULL, '2026-02-05 09:56:47', 0, 0),
(51, '303', 'Investment Income', 'P', 40, '3', 2, 'D', 'N', 'N', 'A', '65', '2026-02-05 09:58:14', NULL, '2026-02-05 09:58:14', 0, 0),
(52, '30301', 'Investment Income from Samity Share', 'P', 51, '3', 3, 'C', 'N', 'N', 'A', '65', '2026-02-05 09:58:59', NULL, '2026-02-05 09:58:59', 0, 0),
(53, '30301001', 'Gain on Sale of Investment Land', 'C', 52, '3', 4, 'C', 'Y', 'Y', 'A', '65', '2026-02-05 09:59:25', NULL, '2026-02-05 09:59:25', 0, 0),
(54, '30302', 'Investment Income from Project Share', 'P', 51, '3', 3, 'C', 'N', 'N', 'A', '65', '2026-02-05 09:59:52', NULL, '2026-02-05 09:59:52', 0, 0),
(55, '30302001', 'Gain on Sale of  Dhaleshwari Project-1', 'C', 54, '3', 4, 'C', 'Y', 'Y', 'A', '65', '2026-02-05 10:00:15', NULL, '2026-02-05 10:00:15', 0, 0),
(56, '4', 'Expense', 'P', 0, '4', 1, 'D', 'N', 'N', 'A', '65', '2026-02-05 10:01:45', NULL, '2026-02-05 10:01:45', 0, 0),
(57, '401', 'Financial Expense', 'P', 56, '4', 2, 'D', 'N', 'N', 'A', '65', '2026-02-05 10:02:15', NULL, '2026-02-05 10:02:15', 0, 0),
(58, '40101', 'Financial Expense', 'P', 57, '4', 3, 'D', 'N', 'N', 'A', '65', '2026-02-05 10:02:45', NULL, '2026-02-05 10:02:45', 0, 0),
(59, '40101001', 'Staff Salary & Festival Bonus', 'C', 58, '4', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 10:03:23', NULL, '2026-02-05 10:03:23', 0, 0),
(60, '40101002', 'Allowance', 'C', 58, '4', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 10:03:41', NULL, '2026-02-05 10:03:41', 0, 0),
(61, '40101003', 'VAT/TAX', 'C', 58, '4', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 10:14:04', NULL, '2026-02-05 10:14:04', 0, 0),
(62, '40101004', 'SMS Service Fee', 'C', 58, '4', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 10:14:24', NULL, '2026-02-05 10:14:24', 0, 0),
(63, '40101005', 'Bank Charges & Commissions', 'C', 58, '4', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 10:14:43', '65', '2026-02-05 10:20:58', 0, 0),
(64, '40101006', 'Mobile Bill', 'C', 58, '4', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 10:15:00', NULL, '2026-02-05 10:15:00', 0, 0),
(65, '402', 'Administrative Expense', 'P', 56, '4', 2, 'D', 'N', 'N', 'A', '65', '2026-02-05 10:15:35', NULL, '2026-02-05 10:15:35', 0, 0),
(66, '40201', 'Administrative Expense', 'P', 65, '4', 3, 'D', 'N', 'N', 'A', '65', '2026-02-05 10:15:58', NULL, '2026-02-05 10:15:58', 0, 0),
(67, '40201001', 'Office Rent', 'C', 66, '4', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 10:16:16', NULL, '2026-02-05 10:16:16', 0, 0),
(68, '40201002', 'Electricity Bill', 'C', 66, '4', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 10:16:33', NULL, '2026-02-05 10:16:33', 0, 0),
(69, '40201003', 'Printing & Stationery', 'C', 66, '4', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 10:16:52', NULL, '2026-02-05 10:16:52', 0, 0),
(70, '40201004', 'Transport', 'C', 66, '4', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 10:17:10', NULL, '2026-02-05 10:17:10', 0, 0),
(71, '40201005', 'Marketing', 'C', 66, '4', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 10:17:30', NULL, '2026-02-05 10:17:30', 0, 0),
(72, '40201006', 'Entertainment Cost', 'C', 66, '4', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 10:17:50', NULL, '2026-02-05 10:17:50', 0, 0),
(73, '40201007', 'Audit & Professional Fee', 'C', 66, '4', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 10:18:11', NULL, '2026-02-05 10:18:11', 0, 0),
(74, '40201008', 'Operating Cost', 'C', 66, '4', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 10:18:29', NULL, '2026-02-05 10:18:29', 0, 0),
(75, '40201009', 'Software operation and maintenance', 'C', 66, '4', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 10:18:47', NULL, '2026-02-05 10:18:47', 0, 0),
(76, '40201010', 'Computer Accessories', 'C', 66, '4', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 10:19:05', NULL, '2026-02-05 10:19:05', 0, 0),
(77, '40201011', 'AGM', 'C', 66, '4', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 10:19:37', NULL, '2026-02-05 10:19:37', 0, 0),
(78, '40201012', 'Recruitment Expenses', 'C', 66, '4', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 10:20:00', NULL, '2026-02-05 10:20:00', 0, 0),
(79, '40201013', 'Miscellaneous Expense', 'C', 66, '4', 4, 'D', 'Y', 'Y', 'A', '65', '2026-02-05 10:20:16', NULL, '2026-02-05 10:20:16', 0, 0),
(80, '30202002', 'Gain From  Other Businesses', 'C', 49, '3', 4, 'C', 'Y', 'Y', 'A', '65', '2026-02-05 12:29:28', NULL, '2026-02-05 12:29:28', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `gl_mapping`
--

CREATE TABLE `gl_mapping` (
  `id` bigint(20) NOT NULL,
  `tran_type` int(11) NOT NULL,
  `tran_type_name` varchar(100) NOT NULL,
  `glac_id` int(11) NOT NULL,
  `contra_glac_id` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gl_mapping`
--

INSERT INTO `gl_mapping` (`id`, `tran_type`, `tran_type_name`, `glac_id`, `contra_glac_id`, `is_active`, `created_by`, `created_at`) VALUES
(11, 1, 'এন্ট্রি ফি', 4, 4, 1, '65', '2026-01-28 10:34:07'),
(12, 2, 'মাসিক কিস্তি', 8, 11, 1, '65', '2026-01-28 10:34:07'),
(13, 3, 'বিলম্ব ফি', 4, 4, 1, '65', '2026-01-28 10:34:07'),
(14, 4, 'সমিতি শেয়ার', 4, 5, 1, '65', '2026-01-28 10:34:07'),
(15, 5, 'প্রকল্প শেয়ার', 5, 4, 1, '65', '2026-01-28 10:34:07');

-- --------------------------------------------------------

--
-- Table structure for table `gl_summary`
--

CREATE TABLE `gl_summary` (
  `id` bigint(20) NOT NULL,
  `tran_date` date NOT NULL,
  `glac_id` int(11) NOT NULL,
  `debit_amt` decimal(32,2) NOT NULL,
  `credit_amt` decimal(32,2) NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(50) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gl_transaction`
--

CREATE TABLE `gl_transaction` (
  `id` int(11) NOT NULL,
  `glac_id` int(11) NOT NULL,
  `tran_date` date NOT NULL,
  `tran_amount` decimal(32,2) NOT NULL,
  `drcr_code` text NOT NULL,
  `remarks` text DEFAULT NULL,
  `status` text NOT NULL,
  `created_by` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meeting`
--

CREATE TABLE `meeting` (
  `id` int(11) NOT NULL,
  `mdate` date NOT NULL,
  `place` text NOT NULL,
  `agenda` text NOT NULL,
  `decision` text NOT NULL,
  `members` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`members`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `presided_by` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members_info`
--

CREATE TABLE `members_info` (
  `id` int(11) NOT NULL,
  `member_code` varchar(50) NOT NULL,
  `name_bn` varchar(100) DEFAULT NULL,
  `name_en` varchar(100) DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `mother_name` varchar(100) DEFAULT NULL,
  `nid` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `religion` varchar(50) DEFAULT NULL,
  `marital_status` varchar(20) DEFAULT NULL,
  `spouse_name` varchar(100) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `education` varchar(100) DEFAULT NULL,
  `agreed_rules` tinyint(1) DEFAULT NULL,
  `profile_image` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ref_no` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `member_type` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members_info`
--

INSERT INTO `members_info` (`id`, `member_code`, `name_bn`, `name_en`, `father_name`, `mother_name`, `nid`, `dob`, `religion`, `marital_status`, `spouse_name`, `mobile`, `gender`, `education`, `agreed_rules`, `profile_image`, `created_at`, `ref_no`, `email`, `member_type`) VALUES
(1, 'CPSS-00001', 'মোঃ সাইফুর রহমান', 'MD SAIFUR RAHMAN', 'মোঃ ফজলুল হক মোল্লা', 'সেলিনা আক্তার', '2355765385', '1985-07-17', 'ইসলাম', 'Married', 'সুমাইয়া আক্তার', '01540505646', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00001/profile_image_1760369517_68ed1b6dc987c.jpg', '2025-10-13 15:31:57', 'D005', NULL, 'MP'),
(30, 'CPSS-00002', 'আনোয়ার পারভেজ', 'ANWAR PARVEZ', 'Md. Abu Hanif', 'Mrs. Luchiea Bagum', '8682027266', '1984-02-09', 'ইসলাম', 'Married', 'Maria Keboty', '01941787809', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00002/profile_image_1760372921_68ed28b9c7d7b.jpg', '2025-10-13 16:28:41', 'D020', NULL, 'MP'),
(31, 'CPSS-00031', 'মারিয়া কিবতি', 'MARIA KEBOTY', 'Sardar Abdur Rahim', 'Tahmina Yesmin', '9572231638', '1996-05-07', 'ইসলাম', 'Married', 'Anwar Parvez', '01641982202', 'Female', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00031/profile_image_1760373363_68ed2a73d6b39.jpg', '2025-10-13 16:36:03', 'D020', NULL, 'MP'),
(34, 'CPSS-00032', 'মোঃ সাইফুল ইসলাম', 'MD SAIFUL ISLAM', 'Md.Warish Mollah', 'Hezera Begum', '2359148299', '1986-01-08', 'ইসলাম', 'Married', 'Sumaiya Adrin Romi', '01715867456', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00032/profile_image_1760436269_68ee202df1bae.jpg', '2025-10-14 10:04:29', 'Anwar Parvez', NULL, 'MP'),
(35, 'CPSS-00035', 'মুহাম্মদ ইকবাল হোসেন', 'MOHAMMAD IQBAL HOSSAON', 'Abdul Bashir Munshi', 'Afzolen Nessa', '9131567076', '1979-04-04', 'ইসলাম', 'Married', 'Rano Akter', '01978333448', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00035/profile_image_1760439119_68ee2b4fcf37c.jpg', '2025-10-14 10:51:59', 'CPSS-00035', NULL, 'MP'),
(36, 'CPSS-00036', 'সালাহ উদ্দিন', 'MD SALAHUDDIN', 'Md. Abdul Mannan', 'Mrs. Shahanara Akhtar', '19862696829656960', '1986-12-28', 'ইসলাম', 'Single', '', '01918923063', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00036/profile_image_1760446126_68ee46aeb6ea8.jpg', '2025-10-14 12:48:46', 'CPSS-00036', NULL, 'MP'),
(39, 'CPSS-00037', 'এইচ. এন.  আশিকুর রুহুল্লাহ', 'H N ASHIQUR RUHULLAH', 'মোঃ আখতার হোসেন', 'উম্মে কুলসুম', '6454134666', '1998-07-08', 'ইসলাম', 'Married', 'সাবরিনা রেজা', '01518403106', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00037/profile_image_1760610926_68f0ca6eba572.jpg', '2025-10-16 10:35:26', 'CPSS-00037', NULL, 'MP'),
(40, 'CPSS-00040', 'মোঃ তারিকুল ইসলাম', 'MD TARIQUL ISLAM', 'Jahangir Hossain', 'Mrs. Parvin Begum', '1935967172', '1989-11-15', 'ইসলাম', 'Married', '', '01913215568', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00040/profile_image_1760614323_68f0d7b360823.jpg', '2025-10-16 11:32:03', 'CPSS-00040', NULL, 'MP'),
(41, 'CPSS-00041', 'মোঃ ইমতিয়াজ হাসান', 'MDIMTIAZ HASAN', 'মরহুম বাদল আক্তার', 'মমতাজ আক্তার', '3255768222', '1986-11-10', 'ইসলাম', 'Married', 'সৌয়দা নাজমা আক্তার', '01685092236', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00041/profile_image_1760850730_68f4732ab7bc8.jpg', '2025-10-19 05:12:10', 'Md Saifur Rahman', NULL, NULL),
(42, 'CPSS-00042', 'সিরাজুল ইসলাম', 'SHIRAJUL ISLAM', 'Md. Shamsul Haque', 'Rushanara Haque', '5960104221', '1985-01-22', 'ইসলাম', 'Married', 'Sajia Afrin Akhi', '01919787839', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00042/profile_image_1761109271_68f86517cc147.jpg', '2025-10-22 05:01:11', 'CPSS-00042', NULL, NULL),
(45, 'CPSS-00043', 'মোহা: সুমরিয়া রাইহান', 'MD SUMRIA RAIHAN', 'Md. Abdur Rashid', 'Mst. Sorifa', '7793619235', '1984-11-30', 'ইসলাম', 'Married', 'Mst. Mahamuda Khatun', '01716035300', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00043/profile_image_1761211289_68f9f399f3dd1.jpg', '2025-10-23 09:21:29', 'Anwar Parvez', 'sraihan68@gmail.com', 'OM'),
(46, 'CPSS-00046', 'মোঃ আলাউদ্দিন', 'MD ALAUDDIN', 'Late Md. Borhan uddin', 'Late Hasina Begum', '2806165631', '1983-02-01', 'ইসলাম', 'Married', 'Luna Laila', '01904119216', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00046/profile_image_1761214791_68fa0147decdf.jpg', '2025-10-23 10:19:51', 'Md. Anwar Parvez', 'alauddin@erainfotechbd.com', 'OM'),
(47, 'CPSS-00047', 'মোঃ মসিদুজ্জামান', 'MD MOSHIDUZZAMAN', 'MD MONIRUZZAMAN', 'SHEFALI KHANOM', '1016014779', '1982-02-01', 'ইসলাম', 'Married', 'Asma Akter', '01716712046', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00047/profile_image_1761235331_68fa5183a41c7.png', '2025-10-23 16:02:11', 'Shirajul Islam', 'moshidchamak@gmail.com', 'MP'),
(48, 'CPSS-00048', 'মাহ্দী মোহাম্মাদ', 'MAHDI MOHAMMAD', 'Md. Mobarak Hossain', 'Hasna Hena', '7300400459', '1994-01-01', 'ইসলাম', 'Married', 'Ummay Kulsom', '01990859786', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00048/profile_image_1761360267_68fc398b371d8.jpg', '2025-10-25 02:44:27', 'Anwar Parvez', 'mahdi174@gmail.com', 'MP'),
(50, 'CPSS-00049', 'আসলাম হোসাইন', 'MD ASLAM HOSSAIN', 'Abdul Aziz', 'Most Morjina Khatun', '6888476634', '1984-10-07', 'ইসলাম', 'Married', '', '01917074634', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00049/profile_image_1761411621_68fd0225365bc.jpg', '2025-10-25 17:00:21', 'Aslam', 'aslamrp07@gmail.com', 'MP'),
(51, 'CPSS-00051', 'সোনিয়া আফরোজ', 'SONIA AFROSE', 'Mohammad Hanif', 'Masuda Begum', '9143519586', '1985-06-05', 'ইসলাম', 'Married', 'Md. Abul Hassan', '01904119219', 'Female', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00051/profile_image_1761461193_68fdc3c900a64.jpg', '2025-10-26 06:46:33', 'Anwar Parvez', 'sonia@erainfotechbd.com', 'OM'),
(52, 'CPSS-00052', 'ফারহানা শিরিন', 'FARHANA SHIRIN', 'মোঃ ফজলুল হক মোল্লা', 'সেলিনা আক্তার', '3255751624', '1988-10-21', 'ইসলাম', 'Married', 'শাহ মোঃ আশফাক রহমান', '01912504305', 'Female', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00052/profile_image_1761474008_68fdf5d89e2e5.jpg', '2025-10-26 10:20:08', 'Md Saifur Rahman', '', 'MP'),
(55, 'CPSS-00053', 'ফয়সাল মাহমুদ', 'FOISAL MAHMUD', 'HABIBUR RAHMAN', 'SHAMIMA SHULTANA', '6412558576', '1992-08-08', 'ইসলাম', 'Married', 'PAPIATUL ZANNAT', '01672443734', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00053/profile_image_1761547308_68ff142c88859.jpg', '2025-10-27 06:41:48', 'Md. Aslam Hossain', 'foisalmahmud34@gmail.com', 'MP'),
(56, 'CPSS-00056', 'মোঃ মোশারফ হোসেন', 'MD MOSHAROF HOSSAN', 'Md SIRAJUL ISLAM', 'RAMESA KHATUN', '8680630749', '1988-10-20', 'ইসলাম', 'Married', 'SABICUN NAHAR', '01722276090', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00056/profile_image_1761569031_68ff6907f06d4.jpg', '2025-10-27 12:43:51', 'CPSS-00056', 'mithumosharof@gmail.com', 'MP'),
(57, 'CPSS-00057', 'মোঃ আরিফুল ইসলাম', 'MD ARIFUL ISLAM', 'Md. Shahidul Islam', 'Jahanara Begum', '1025555564', '1986-09-04', 'ইসলাম', 'Married', 'Syeda Tanjila', '01717819612', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00057/profile_image_1761666480_6900e5b0e64d2.jpeg', '2025-10-28 15:48:00', 'CPSS-00057', 'arifultonu007@gmail.com', 'MP'),
(59, 'CPSS-00058', 'খন্দকার ফারজানা রহমান', 'KHANDAKER FARZANA RAHMAN', 'Khandaker Habibur Rahman', 'Fahima Khanam', '2854465149', '1993-12-10', 'ইসলাম', 'Married', 'Kazi Mahmud Morshed', '01990839119', 'Female', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00058/profile_image_1761812325_69031f659d8e4.jpg', '2025-10-30 08:18:45', 'Md. Mosharof Hossan', 'swe.merry@gmail.com', 'MP'),
(62, 'CPSS-00060', 'মোঃ আশিকুর রহমান', 'MD ASHIQUR RAHMAN', 'মোঃ ফজলুল হক মোল্লা', 'সেলিনা আক্তার', '4159140765', '1992-04-24', 'ইসলাম', 'Single', '', '01829041699', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00060/profile_image_1761928787_6904e6538f721.jpg', '2025-10-31 16:39:47', 'Md. Saifur Rahman', '', 'MP'),
(63, 'CPSS-00063', 'সাকিফ আব্দুল্লাহ', 'SAKIF ABDULLAH', 'Md. Shamsul Haque Shaheen', 'Mst. Dilara Haque', '8255473053', '1997-12-06', 'ইসলাম', 'Single', '', '01535421765', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00063/profile_image_1762321305_690ae3991f225.jpg', '2025-11-05 05:41:45', 'Md Saifur Rahman', 'sakif4646@gmail.com', 'MP'),
(65, 'CPSS-00064', 'মাসুমা আক্তার', 'MASUMA AKTER', 'Shaikh Abdur Rahman', 'Mrs. Aleya', '1935991685', '1986-08-20', 'ইসলাম', 'Married', '', '01913152270', 'Female', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00064/profile_image_1762596529_690f16b1c54fd.jpg', '2025-11-08 10:08:49', 'Hera', '', 'MP'),
(66, 'CPSS-00066', 'এনামুল হক', 'ENAMUL HAQUE', 'Abdul Mannan', 'Shirina Akter', '3255638193', '1986-12-31', 'ইসলাম', 'Married', '', '01921687433', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00066/profile_image_1762597112_690f18f8348bd.jpg', '2025-11-08 10:18:32', 'Hera', '', 'MP'),
(67, 'CPSS-00067', 'এনামুল হক', 'ENAMUL HAQUE', 'Md. Nazim Uddin', 'Mrs, Kohinur Begum', '2369306275', '1986-10-11', 'ইসলাম', 'Married', '', '01719885508', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00067/profile_image_1762597668_690f1b24d446b.jpg', '2025-11-08 10:27:48', 'Hera', '', 'MP'),
(68, 'CPSS-00068', 'আ. হাকিম', 'A HAKIM', 'Sanowar Hosen', 'Johura Begum', '4178206332', '1985-12-31', 'ইসলাম', 'Married', '', '01729999519', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00068/profile_image_1762598139_690f1cfb1d3e1.jpg', '2025-11-08 10:35:39', 'Hera', '', 'MP'),
(69, 'CPSS-00069', 'হাসিনা আক্তার', 'HASINA AKTER', 'Mohammad Abdul Wadud', 'Hajera Begum', '4639722968', '1980-10-22', 'ইসলাম', 'Married', '', '01904119209', 'Female', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00069/profile_image_1762598877_690f1fdd44939.jpg', '2025-11-08 10:47:57', 'Hera', '', 'MP'),
(70, 'CPSS-00070', 'সুলতান আহমেদ', 'SULTAN AHMED', 'Abdul Mannan', 'Hasina Begum', '9152673704', '1991-09-24', 'ইসলাম', 'Married', '', '01954476301', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00070/profile_image_1762599556_690f2284b20b0.jpg', '2025-11-08 10:59:16', 'Hera', '', 'MP'),
(71, 'CPSS-00071', 'মহমুদা খাতুন', 'MAHAMUDA KHATUN', 'Md Mokbul Hossain', 'Shamsun Nahar', '5505319425', '1982-08-10', 'ইসলাম', 'Married', '', '01714625345', 'Female', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00071/profile_image_1762600570_690f267ac16a0.jpg', '2025-11-08 11:16:10', 'Hera', '', 'MP'),
(72, 'CPSS-00072', 'মোহাম্মদ আশরাফউদ্দিন ফেরদৌসী', 'MOHAMMAD ASHRAFUDDIN FERDOUSI', 'Baharuddin Ahmed', 'Insan Ara Rahman', '5991292722', '1985-12-27', 'ইসলাম', 'Married', 'Nigar Sultana', '01682777240', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00072/profile_image_1762622505_690f7c290998b.jpg', '2025-11-08 17:21:45', 'Md. Saifur Rahman', 'it.codeperl@gmail.com', 'MP'),
(73, 'CPSS-00073', 'মুকতাসিব উন নুর', 'MUKTASIB UN NUR', 'Mohammad Abul Kalam', 'Firoza Begum', '4652463417', '1995-03-07', 'ইসলাম', 'Married', 'Maria Akter', '01515652983', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00073/profile_image_1762622700_690f7cec38a31.jpg', '2025-11-08 17:25:00', 'Shirajul Islam Hera', 'muktasib.noor@gmail.com', 'MP'),
(74, 'CPSS-00074', 'মোঃ আরিফ হোসেন', 'MD ARIF HOSSAIN', 'MD ABDUL MAJID', 'SAFALY BEGUM', '1494681867', '1987-04-04', 'ইসলাম', 'Married', 'MEHERUN NESA', '01719173908', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00074/profile_image_1762622735_690f7d0fa2a60.jpg', '2025-11-08 17:25:35', 'D014', 'honours4all@gmail.com', 'MP'),
(75, 'CPSS-00075', 'মোঃ ফাহিম শেখ', 'MD FAHIM SHEKH', 'Arman Shekh', 'Farida Parvin', '3765514934', '2003-03-05', 'ইসলাম', 'Single', '', '0180613180', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00075/profile_image_1762623917_690f81ad1b862.jpg', '2025-11-08 17:45:17', 'Md Arif Hossain', 'honours4all@gmail.com', 'MP'),
(76, 'CPSS-00076', 'মোঃ জাকির হাসান', 'MD JAKIR HASAN', 'MD. AFSAR UDDIN', 'NURJAHAN BEGUM', '4639196197', '1982-04-08', 'ইসলাম', 'Married', 'SHAHRINA PARVIN SATHY', '01911977707', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00076/profile_image_1762658099_691007334c35b.jpg', '2025-11-09 03:14:59', 'CPSS-00076', 'aponworld07@gmail.com', 'MP'),
(77, 'CPSS-00077', 'এ কে এম আশরাফুল আলম', 'A K M ASHRAFUL ALAM', 'Late. Md. Amirul Hossen', 'MST Saleha Akhtari', '7324001077', '1984-02-15', 'ইসলাম', 'Married', 'Yasmin Parvin', '01727499949', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00077/profile_image_1762660327_69100fe7d6467.jpg', '2025-11-09 03:52:07', 'Md. Enamul Haque', 'akmalam011@gmail.com', 'MP'),
(78, 'CPSS-00078', 'মেহেদী হাসান জিদান', 'MEHEDI HASSAN ZIDAN', 'Md. Zahid Hassan', 'Sabina Yesmin', '6455107042', '1998-06-29', 'ইসলাম', 'Single', '', '01787172492', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00078/profile_image_1762701900_6910b24c86505.jpg', '2025-11-09 15:25:00', 'Zidan', 'zidanmehedi101@gmail.com', 'MP'),
(79, 'CPSS-00079', 'সৈয়দা জিহান আজগর', 'SYEDA ZEHAN ASGAR', 'Syeda Ali Asgar', 'Farida Yasmin', '19922696406001573', '1992-07-15', 'ইসলাম', 'Married', '', '01670060260', 'Female', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00079/profile_image_1762757614_69118bee94091.jpg', '2025-11-10 06:53:34', 'MD. MOSHAROF HOSSAN', 'zehanasgar1507@gmail.com', 'MP'),
(82, 'CPSS-00080', 'মোঃ রুহুল আমিন', 'MD RUHUL AMIN', 'MD MOSLEH UDDIN', 'MONOWARA BEGOM', '8235660852', '1991-02-15', 'ইসলাম', 'Married', 'RABEA SULTANA', '01723374234', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00080/profile_image_1763138237_69175abdae896.jpg', '2025-11-14 16:37:17', 'CPSS-00080', 'ruhulamin34@gmail.com', 'MP'),
(85, 'CPSS-00083', 'মাসুমা সুলতানা', 'MASUMA SULTANA', 'কবির উদ্দিন আহমেদ', 'ফজিলাতুন নেছা', '2377042029', '1989-01-01', 'ইসলাম', 'Married', 'আল আমিন আহমেদ', '01935741518', 'Female', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00083/profile_image_1763550712_691da5f848e03.jpg', '2025-11-19 11:11:52', 'Md Saifur Rahman', 'masuma.cse@gmail.com', 'MP'),
(89, 'CPSS-00086', 'তানজিনা জাহান', 'TANJINA JAHAN', 'A M Fakheruddin Ahmed', 'Rokeya Begum', '8654950677', '1983-01-08', 'ইসলাম', 'Single', '', '01716504936', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00086/profile_image_1763793010_6921587206712.jpg', '2025-11-22 06:30:10', 'Hera', '', 'MP'),
(90, 'CPSS-00090', 'রাহিমুল ইসলাম', 'RAHEMUL ISLAM', 'RAFIQUL ISLAM', 'RAHEMA AKTER', '3305216289', '1999-12-11', 'ইসলাম', 'Married', 'ARJINA AKTER', '01836999981', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00090/profile_image_1764071479_69259837e3129.jpeg', '2025-11-25 11:51:19', 'Md Saifur Rahman', 'rahimulislam14@gmail.com', 'MP'),
(92, 'CPSS-00091', 'মোঃ নাফিস ইমতিয়াজ', 'MD NAFES IMTIAZ', 'Late Mamunoor Rashid', 'Hosne Ara Pervin', '5969069326', '1994-12-11', 'ইসলাম', 'Single', '', '01674396127', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00091/profile_image_1764137206_692698f62e1a0.jpg', '2025-11-26 06:06:46', 'Md Saifur Rahman', 'imtiaz.nafes@gmail.com', 'MP'),
(93, 'CPSS-00093', 'মোঃ জাহিদ হাসান নোমান', 'MD JAHID HASAN NOMAN', 'MD Abu Bakkar Siddique', 'Jahanara Begum', '8251804962', '1997-02-12', 'ইসলাম', 'Single', '', '01630353588', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00093/profile_image_1764225700_6927f2a420cd6.jpeg', '2025-11-27 06:41:40', 'Md Saifur Rahman', 'nomanjahid203@gmail.com', 'MP'),
(95, 'CPSS-00094', 'মোঃ নাজমুল হক', 'MDNAZMUL HAQUE', 'Ramjan Ali', 'Nasrin Sultana', '3737053169', '1996-07-01', 'ইসলাম', 'Married', 'Tasnova Islam', '01626100302', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00094/profile_image_1764263356_692885bc441b9.png', '2025-11-27 17:09:16', 'Md Saifur Rahman', 'nazmulhs030@gmail.com', 'MP'),
(97, 'CPSS-00096', 'মো: জিয়াউর রহমান', 'MD ZIAUR RAHAMAN', 'মোঃ আবুল বাশার', 'শিরিন বেগম', '6410834169', '1994-09-03', 'ইসলাম', 'Married', 'Sabiha Rahman', '01679714001', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00096/profile_image_1764265210_69288cfa4d8ab.jpg', '2025-11-27 17:40:10', 'Md Saifur Rahman', 'ziaurrahaman939@gmail.com', 'MP'),
(98, 'CPSS-00098', 'মোঃ রাজু আহমেদ', 'MD RAJU AHMED', 'Md Ab Salam Mia', 'Mst Nasrin Salam', '2406718441', '1996-04-09', 'ইসলাম', 'Single', '', '01771522411', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00098/profile_image_1764333248_692996c03cf52.jpg', '2025-11-28 12:34:08', 'Md Saifur Rahman', 'rajucse1705@gmail.com', 'MP'),
(100, 'CPSS-00099', 'খন্দকার অনিম হাসান আদনান', 'KHANDAKAR ANIM HASSAN ADNAN', 'Khandakar Ayub', 'Shamsunnahar', '3754399909', '1999-11-10', 'ইসলাম', 'Single', '', '01638147671', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00099/profile_image_1764342244_6929b9e48809d.jpg', '2025-11-28 15:04:04', 'Md Saifur Rahman', 'khandakar.adnan21@gmail.com', 'MP'),
(101, 'CPSS-00101', 'ঋত্বিক রুদ্র', 'HRITHIK RUDRA', 'Late Pranab Kumar Rudra', 'Rama Chowdhury', '3305506283', '1995-10-08', 'হিন্দু', 'Married', 'Bipasha Das', '01875525591', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00101/profile_image_1764348060_6929d09c38b16.jpg', '2025-11-28 16:41:00', 'Md Saifur Rahman', 'hrithik08.rudra@gmail.com', 'MP'),
(102, 'CPSS-00102', 'শেখ মোঃ তুহিন সিদ্দিক', 'SHEIKH MD TUHIN SIDDIK', 'মোঃ আবু বকর', 'Mrs. Halema khatun', '6453415512', '1997-10-14', 'ইসলাম', 'Married', 'Mrs Asmaul husna boisakhi', '01521116842', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00102/profile_image_1764348853_6929d3b5bf798.jpg', '2025-11-28 16:54:13', 'Md Saifur Rahman', 'hituhin09@gmail.com', 'MP'),
(103, 'CPSS-00103', 'মোহাম্মদ আল মামুন', 'MOHAMMAD AL MAMUN', 'Mohammad Habibur Rahman', 'Sahanara Begum', '6416700331', '1977-12-28', 'ইসলাম', 'Married', 'Ripa Akter', '01717647410', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00103/profile_image_1764417214_692adebe90f7d.jpg', '2025-11-29 11:53:34', 'Md Saifur Rahman', 'md.almamun.bd@gmail.com', 'MP'),
(104, 'CPSS-00104', 'মোঃ হাসিবুজ্জামান', 'MD HASIBUZZAMAN', 'মোঃ বকুল হোসেন', 'জরিনা বেগম', '5976797547', '1994-03-22', 'ইসলাম', 'Married', 'এসকে. লিমা', '01618356180', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00104/profile_image_1764420960_692aed609e5e7.jpg', '2025-11-29 12:56:00', 'Md Saifur Rahman', 'hasib.9437.hu@gmail.com', 'MP'),
(106, 'CPSS-00105', 'তৌফিকুল ইসলাম', 'TAWFIQUL ISLAM', 'Shamsuddin Ahmed', 'Razia Ahmed', '5525875000', '1995-07-09', 'ইসলাম', 'Married', 'Israt Jahan Badhon', '01676797239', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00105/profile_image_1764432449_692b1a4181a6f.jpg', '2025-11-29 16:07:29', 'Md Saifur Rahman', 'tawfiqul01@gmail.com', 'MP'),
(107, 'CPSS-00107', 'মো: তারিক হোসেন', 'MD TARIK HOSSAIN', 'Md. Abu Jafar', 'Jakia Begum', '19892610413960946', '1989-10-04', 'ইসলাম', 'Married', 'Masuma Yasmin Mithee', '01717967656', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00107/profile_image_1764477872_692bcbb07ff13.jpg', '2025-11-30 04:44:32', 'Md Saifur Rahman', 'tarif.isjnu@gmail.com', 'MP'),
(108, 'CPSS-00108', 'মোঃ আব্দুল্লাহ আল হাদী', 'MD ABDULLA AL HADI', 'Md. Abdul Hamid', 'Most. Maksuda Begum', '2402054734', '1995-09-05', 'ইসলাম', 'Married', 'Joly Khatun', '01724666834', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00108/profile_image_1764564534_692d1e362d804.jpg', '2025-12-01 04:48:54', 'CPSS-00108', 'hadihamid.cse@gmail.com', 'MP'),
(109, 'CPSS-00109', 'মো: দিদারুল ইসলাম', 'MD DIDARUL ISLAM', 'রফিকুল আলম', 'Sarmin Akter', '6452209882', '1997-08-01', 'ইসলাম', 'Married', 'Anika Akter', '01679706957', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00109/profile_image_1764581857_692d61e1ead2e.jpg', '2025-12-01 09:37:37', 'Saifur', 'mddidarulislamrony778@gmail.com', 'MP'),
(110, 'CPSS-00110', 'মোঃ আল সামিউল আমিন রিশাত', 'MD AL SAMIUL AMIN RISHAT', 'আল আমিন', 'শিউলী আক্তার', '4654183583', '1999-10-27', 'ইসলাম', 'Single', '', '01738356180', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00110/profile_image_1764608387_692dc983c0171.jpg', '2025-12-01 16:59:47', 'Saifur Rahman', 'samiaulamin@gmail.com', 'MP'),
(112, 'CPSS-00111', 'রানো আক্তার', 'RANO AKTER', 'মোঃ জসিম উদ্দিন', 'সুরেখা বেগম', '8681441195', '1986-06-01', 'ইসলাম', 'Married', 'মুহাম্মদ ইকবাল হোসেন', '01810031390', 'Female', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00111/profile_image_1764819618_693102a2a3bc9.jpg', '2025-12-04 03:40:18', 'AP', '', 'MP'),
(123, 'CPSS-00113', 'মোঃ সিদ্দিকুর রহমান খান', 'MD SIDDQUR RAHMAN KHAN', 'Md. Abdul Jalil Khan', 'Mrs, Sufia Akhter', '5080305435', '1977-12-01', 'ইসলাম', 'Married', 'Ayesha Siddika', '01917540114', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00113/profile_image_1767520537_695a391914094.jpg', '2026-01-04 09:55:37', 'AP', 'siddqurera@gmail.com', 'MP'),
(130, 'CPSS-00124', 'মোছাঃ ফাহমিদা শাহরিন', 'MS FAHMIDA SHAHRIN', 'Abdul Hamid', 'MS Latifa Begum', '3709039089', '1990-02-20', 'ইসলাম', 'Married', 'Shakil', '01748065751', 'Female', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00124/profile_image_1770041397_6980b035e50d8.jpg', '2026-02-02 14:09:57', 'D010', '', 'MP'),
(131, 'CPSS-00131', 'মোঃ আতিকুল হাসান', 'MD ATIQUL HASAN', 'Md Abdul Mannan', 'Sahana Akter', '4602476212', '1988-11-26', 'ইসলাম', 'Single', '', '01911937558', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00131/profile_image_1770042289_6980b3b1021fc.jpg', '2026-02-02 14:24:49', 'D013', '', 'MP'),
(132, 'CPSS-00132', 'মোহাম্মদ আবুল কালাম আজাদ', 'MOHAMMAD ABUL KALAM AZAD', 'Md. Siddikur Rahman', 'Hamida Begum', '5970961958', '1979-12-31', 'ইসলাম', 'Married', 'Jenifa Islam Tisa', '01711659915', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00132/profile_image_1770532357_69882e0551c76.png', '2026-02-08 06:32:37', 'Md. Jakir Hasan', 'mak.azad.79@gmail.com', 'MP'),
(134, 'CPSS-00133', 'মোঃ আরিফুর রহমান', 'MD ARIFUR RAHMAN', 'মোঃ আব্দুর রহমান ঢালী', 'মহিমা রহমান', '1004636658', '1984-12-31', 'ইসলাম', 'Married', 'সামিয়া আকতার', '01557781493', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00133/profile_image_1770635200_6989bfc07f9d5.jpeg', '2026-02-09 11:06:40', 'Md. Saifur Rahman', '', 'MP');

-- --------------------------------------------------------

--
-- Table structure for table `member_bank`
--

CREATE TABLE `member_bank` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `member_code` varchar(50) NOT NULL,
  `ac_no` varchar(50) NOT NULL,
  `ac_title` varchar(255) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `branch_name` varchar(255) NOT NULL,
  `routing_no` varchar(50) DEFAULT NULL,
  `status` varchar(10) DEFAULT 'A',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `member_bank`
--

INSERT INTO `member_bank` (`id`, `member_id`, `member_code`, `ac_no`, `ac_title`, `bank_name`, `branch_name`, `routing_no`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'CPSS-00001', '04934004088', 'মোঃ সাইফুর রহমান', 'ব্যাংক এশিয়া লিঃ (Bank Asia Ltd.)', 'Paltan Branch, Dhaka', '', 'A', '2025-11-26 10:48:37', '2025-11-26 10:48:37'),
(2, 78, 'CPSS-00078', '04934008982', 'MEHEDI HASAN ZIDAN', 'BANK ASIA LTD', 'PALTAN', '', 'A', '2025-12-06 13:47:44', '2025-12-06 13:47:44'),
(3, 45, 'CPSS-00043', '04934010066', 'MD. SUMRIA RAIHAN', 'BANK ASIA PLC', 'PALTAN', '070275207', 'A', '2025-12-07 05:16:12', '2025-12-07 05:22:21'),
(4, 41, 'CPSS-00041', '2801475888001', 'Current Account', 'City Bank Ltd', 'Mirpur-1, Br', '225262986', 'A', '2026-01-14 05:24:04', '2026-01-14 05:24:04');

-- --------------------------------------------------------

--
-- Table structure for table `member_documents`
--

CREATE TABLE `member_documents` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `member_code` varchar(32) NOT NULL,
  `doc_type` int(11) NOT NULL,
  `doc_path` text NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member_documents`
--

INSERT INTO `member_documents` (`id`, `member_id`, `member_code`, `doc_type`, `doc_path`, `created_at`) VALUES
(1, 59, 'CPSS-00058', 101, 'user_images/member_CPSS-00058/doc_101_1762163627_c9235706.jpg', '2025-11-03 15:53:47'),
(2, 56, 'CPSS-00056', 101, 'user_images/member_CPSS-00056/doc_101_1762243509_ba3a7821.jpg', '2025-11-04 14:05:09'),
(3, 56, 'CPSS-00056', 102, 'user_images/member_CPSS-00056/doc_102_1762243526_1cb3ee8e.png', '2025-11-04 14:05:26'),
(4, 56, 'CPSS-00056', 103, 'user_images/member_CPSS-00056/doc_103_1762243785_3fdb4ac1.png', '2025-11-04 14:09:45'),
(5, 30, 'CPSS-00002', 101, 'user_images/member_CPSS-00002/doc_101_1762605645_f0a401a9.jpg', '2025-11-08 18:40:45'),
(6, 30, 'CPSS-00002', 102, 'user_images/member_CPSS-00002/doc_102_1762605753_2c214f3f.jpg', '2025-11-08 18:42:33'),
(9, 31, 'CPSS-00031', 101, 'user_images/member_CPSS-00031/doc_101_1762607037_78054ef2.jpg', '2025-11-08 19:03:57'),
(10, 31, 'CPSS-00031', 102, 'user_images/member_CPSS-00031/doc_102_1762607123_ea53818d.jpg', '2025-11-08 19:05:23'),
(11, 85, 'CPSS-00083', 101, 'user_images/member_CPSS-00083/doc_101_1763552756_ca69ceaa.jpg', '2025-11-19 17:45:56'),
(13, 106, 'CPSS-00105', 101, 'user_images/member_CPSS-00105/doc_101_1767815115_a2d92fb4.jpg', '2026-01-08 01:45:15'),
(14, 106, 'CPSS-00105', 103, 'user_images/member_CPSS-00105/doc_103_1767815285_f054e290.jpg', '2026-01-08 01:48:05'),
(15, 98, 'CPSS-00098', 101, 'user_images/member_CPSS-00098/doc_101_1769988051_584676bc.jpg', '2026-02-02 05:20:51'),
(16, 98, 'CPSS-00098', 103, 'user_images/member_CPSS-00098/doc_103_1769988170_136a0c86.jpg', '2026-02-02 05:22:50'),
(17, 98, 'CPSS-00098', 102, 'user_images/member_CPSS-00098/doc_102_1769988259_74766c52.png', '2026-02-02 05:24:19'),
(18, 100, 'CPSS-00099', 101, 'user_images/member_CPSS-00099/doc_101_1770305324_10ab72b7.jpg', '2026-02-05 21:28:44'),
(19, 100, 'CPSS-00099', 102, 'user_images/member_CPSS-00099/doc_102_1770305441_52fc7b8c.jpg', '2026-02-05 21:30:41'),
(20, 66, 'CPSS-00066', 101, 'user_images/member_CPSS-00066/doc_101_1770366904_1bb06132.jpg', '2026-02-06 14:35:04'),
(21, 66, 'CPSS-00066', 103, 'user_images/member_CPSS-00066/doc_103_1770366996_41246fca.jpg', '2026-02-06 14:36:36'),
(22, 66, 'CPSS-00066', 104, 'user_images/member_CPSS-00066/doc_104_1770367110_f580eb0e.png', '2026-02-06 14:38:30'),
(23, 66, 'CPSS-00066', 102, 'user_images/member_CPSS-00066/doc_102_1770367199_ae5ab2dd.jpg', '2026-02-06 14:39:59'),
(24, 63, 'CPSS-00063', 101, 'user_images/member_CPSS-00063/doc_101_1770409658_76d9af76.jpg', '2026-02-07 02:27:38'),
(25, 63, 'CPSS-00063', 103, 'user_images/member_CPSS-00063/doc_103_1770409980_84b9090c.jpg', '2026-02-07 02:33:00'),
(26, 63, 'CPSS-00063', 102, 'user_images/member_CPSS-00063/doc_102_1770410252_fdcee4b1.png', '2026-02-07 02:37:32'),
(28, 45, 'CPSS-00043', 101, 'user_images/member_CPSS-00043/doc_101_1770527827_17bf107d.jpg', '2026-02-08 11:17:07'),
(29, 45, 'CPSS-00043', 102, 'user_images/member_CPSS-00043/doc_102_1770527844_e53c9133.jpg', '2026-02-08 11:17:24'),
(31, 45, 'CPSS-00043', 103, 'user_images/member_CPSS-00043/doc_103_1770528280_b55e928a.jpg', '2026-02-08 11:24:40'),
(32, 45, 'CPSS-00043', 104, 'user_images/member_CPSS-00043/doc_104_1770528294_cff6ae03.jpg', '2026-02-08 11:24:54'),
(33, 132, 'CPSS-00132', 101, 'user_images/member_CPSS-00132/doc_101_1770533748_ba275d50.png', '2026-02-08 12:55:48'),
(34, 132, 'CPSS-00132', 102, 'user_images/member_CPSS-00132/doc_102_1770533760_f95fb175.jpg', '2026-02-08 12:56:00'),
(35, 132, 'CPSS-00132', 103, 'user_images/member_CPSS-00132/doc_103_1770533791_1fb182b5.jpg', '2026-02-08 12:56:31'),
(36, 132, 'CPSS-00132', 104, 'user_images/member_CPSS-00132/doc_104_1770533818_1b4c475d.png', '2026-02-08 12:56:58'),
(37, 63, 'CPSS-00063', 105, 'user_images/member_CPSS-00063/doc_105_1771006787_06f36437.jpg', '2026-02-14 00:19:47');

-- --------------------------------------------------------

--
-- Table structure for table `member_nominee`
--

CREATE TABLE `member_nominee` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `member_code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `relation` varchar(50) NOT NULL,
  `nid` varchar(50) NOT NULL,
  `dob` datetime NOT NULL,
  `percentage` float NOT NULL,
  `nominee_image` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member_nominee`
--

INSERT INTO `member_nominee` (`id`, `member_id`, `member_code`, `name`, `relation`, `nid`, `dob`, `percentage`, `nominee_image`, `created_at`) VALUES
(1, 1, 'CPSS-00001', 'সুমাইয়া আক্তার', 'Wife', '8279788759', '2000-06-09 00:00:00', 40, 'user_images/member_CPSS-00001/nominee_1_1760369517_68ed1b6dc9dca.jpg', '2025-10-13 21:31:57'),
(2, 1, 'CPSS-00001', 'মোঃ আবরার ফাইয়াজ ', 'Son', '20182692509654120', '2018-12-15 00:00:00', 30, 'user_images/member_CPSS-00001/nominee_2_1760369517_68ed1b6dca04d.jpg', '2025-10-13 21:31:57'),
(3, 1, 'CPSS-00001', 'মোঃ আবরার ফাহাদ', 'Son', '20212692509654119', '2021-04-06 00:00:00', 30, 'user_images/member_CPSS-00001/nominee_3_1760369517_68ed1b6dca185.png', '2025-10-13 21:31:57'),
(31, 30, 'CPSS-00002', 'Aafreen Zahan Zara', 'Daughter', '8682027265', '2018-11-11 00:00:00', 50, 'user_images/member_CPSS-00002/nominee_1_1760372921_68ed28b9c80bc.jpg', '2025-10-13 22:28:41'),
(32, 30, 'CPSS-00002', 'Anaya Ayzal Sara', 'Daughter', '8682027264', '2023-03-11 00:00:00', 50, 'user_images/member_CPSS-00002/nominee_2_1760372921_68ed28b9c843f.jpg', '2025-10-13 22:28:41'),
(33, 31, 'CPSS-00031', 'Anwar Parvez', 'Husband', '8682027266', '1984-02-09 00:00:00', 100, 'user_images/member_CPSS-00031/nominee_1_1760373363_68ed2a73d76d1.jpg', '2025-10-13 22:36:03'),
(35, 34, 'CPSS-00032', 'SUMAIYA ADRIN ROMI', 'Wife', '1524088802', '1993-09-08 00:00:00', 100, 'user_images/member_CPSS-00032/nominee_1_1760436269_68ee202df2963.jpg', '2025-10-14 16:04:29'),
(36, 35, 'CPSS-00035', 'রানো আক্তার', 'স্ত্রী', '8681441195', '1986-01-06 00:00:00', 100, 'user_images/member_CPSS-00035/nominee_1_1760439119_68ee2b4fcfed4.jpg', '2025-10-14 16:51:59'),
(37, 36, 'CPSS-00036', 'Abu Bakor Siddik', 'Brother', '6901134541', '1992-11-29 00:00:00', 100, 'user_images/member_CPSS-00036/nominee_1_1760446126_68ee46aeb8359.jpeg', '2025-10-14 18:48:46'),
(38, 39, 'CPSS-00037', 'উম্মে কুলসুম', 'Mother', '19731917512075258', '1974-12-30 00:00:00', 50, 'user_images/member_CPSS-00037/nominee_1_1760610926_68f0ca6ebb7c6.jpg', '2025-10-16 16:35:26'),
(39, 40, 'CPSS-00040', 'Mst. Lata Khatun', 'Wife', '3310996271', '2003-03-03 00:00:00', 100, 'user_images/member_CPSS-00040/nominee_1_1760614323_68f0d7b360dfb.jpg', '2025-10-16 17:32:03'),
(40, 41, 'CPSS-00041', 'সৌয়দা নাজমা আক্তার', 'wife', '9106855506', '1988-11-23 00:00:00', 100, 'user_images/member_CPSS-00041/nominee_1_1760850730_68f4732abd9a5.jpg', '2025-10-19 11:12:10'),
(41, 42, 'CPSS-00042', 'Afnan Islam Ayaz', 'Son', '20200003802018122', '2020-03-04 00:00:00', 100, 'user_images/member_CPSS-00042/nominee_1_1761109271_68f86517cd8d7.jpg', '2025-10-22 11:01:11'),
(42, 45, 'CPSS-00043', 'Mst. Mahamuda Khatun', 'Wife', '19908122503000036', '1990-08-30 00:00:00', 100, 'user_images/member_CPSS-00043/nominee_1_1761211290_68f9f39a00765.jpg', '2025-10-23 15:21:30'),
(43, 46, 'CPSS-00046', 'Runa Laila', 'wife', '5925615122410', '1983-08-02 00:00:00', 100, 'user_images/member_CPSS-00046/nominee_1_1761214791_68fa0147dfa7d.jpg', '2025-10-23 16:19:51'),
(44, 47, 'CPSS-00047', 'ASMA AKTER', 'Wife', '6901993144', '1995-06-08 00:00:00', 100, 'user_images/member_CPSS-00047/nominee_1_1761235331_68fa5183a55ba.jpg', '2025-10-23 22:02:11'),
(45, 48, 'CPSS-00048', 'Hasna Hena', 'Mother', '6434502644', '1967-02-04 00:00:00', 100, 'user_images/member_CPSS-00048/nominee_1_1761360267_68fc398b38677.jpg', '2025-10-25 08:44:27'),
(46, 50, 'CPSS-00049', 'Metu Akhter', 'Wife', '6004075518', '1999-01-03 00:00:00', 100, 'user_images/member_CPSS-00049/nominee_1_1761411621_68fd0225368ac.jpg', '2025-10-25 23:00:21'),
(47, 51, 'CPSS-00051', 'Sumia Ajmin Maisha', 'Younger Sister', '9577481048', '1999-08-28 00:00:00', 100, 'user_images/member_CPSS-00051/nominee_1_1761461193_68fdc3c901f8d.jpeg', '2025-10-26 12:46:33'),
(48, 52, 'CPSS-00052', 'শাহ মোঃ আশফাক রহমান ', 'স্বামী', '1906274681', '1983-06-29 00:00:00', 50, 'user_images/member_CPSS-00052/nominee_1_1761474008_68fdf5d89f4d7.jpg', '2025-10-26 16:20:08'),
(49, 52, 'CPSS-00052', 'আশফি বিনতে রহমান', 'মেয়ে', '1111111111', '2024-11-04 00:00:00', 50, 'user_images/member_CPSS-00052/nominee_2_1761474008_68fdf5d89f8b0.jpg', '2025-10-26 16:20:08'),
(51, 55, 'CPSS-00053', 'SHAMIMA SHULTANA', 'MOTHER', '5983889295', '1973-11-20 00:00:00', 50, 'user_images/member_CPSS-00053/nominee_1_1761547308_68ff142c88d85.jpg', '2025-10-27 12:41:48'),
(52, 55, 'CPSS-00053', 'PAPIATUL ZANNAT', 'WIFE', '9552707367', '1992-12-07 00:00:00', 50, 'user_images/member_CPSS-00053/nominee_2_1761547308_68ff142c890a2.jpg', '2025-10-27 12:41:48'),
(53, 56, 'CPSS-00056', 'SABICUN NAHAR', 'SPOUSE', '4184933754', '1994-01-18 00:00:00', 100, 'user_images/member_CPSS-00056/nominee_1_1761569031_68ff6907f1495.jpeg', '2025-10-27 18:43:51'),
(54, 57, 'CPSS-00057', 'Syeda Tanjila', 'WIfe', '7315858352', '1988-02-01 00:00:00', 100, 'user_images/member_CPSS-00057/nominee_1_1761666480_6900e5b0e78c7.png', '2025-10-28 21:48:00'),
(55, 59, 'CPSS-00058', 'Kazi Mahmud Morshed', 'Spouse', '2389056454', '1991-06-27 00:00:00', 100, 'user_images/member_CPSS-00058/nominee_1_1761812325_69031f659dd64.jpg', '2025-10-30 14:18:45'),
(56, 62, 'CPSS-00060', 'মোঃ ফজলুল হক মোল্লা ', 'বাবা ', '5505742956', '1956-01-01 00:00:00', 50, 'user_images/member_CPSS-00060/nominee_1_1761928787_6904e65390d6b.jpg', '2025-10-31 22:39:47'),
(57, 62, 'CPSS-00060', 'সেলিনা আক্তার', 'মা', '9552864564', '1982-12-28 00:00:00', 50, 'user_images/member_CPSS-00060/nominee_2_1761928787_6904e653912ba.jpg', '2025-10-31 22:39:47'),
(58, 63, 'CPSS-00063', 'Mst. Dilara Haque', 'MOTHER', '1469190118', '1970-07-06 00:00:00', 100, 'user_images/member_CPSS-00063/nominee_1_1762321305_690ae39920046.png', '2025-11-05 11:41:45'),
(59, 65, 'CPSS-00064', 'Shaikh Abdur Rahman', 'Father', '4635953831', '1950-10-20 00:00:00', 100, 'user_images/member_CPSS-00064/nominee_1_1762596529_690f16b1c66a8.jpg', '2025-11-08 16:08:49'),
(60, 66, 'CPSS-00066', 'Israt Jahan Tania', 'Wife', '4623814334', '1991-01-21 00:00:00', 100, 'user_images/member_CPSS-00066/nominee_1_1762597112_690f18f835006.jpg', '2025-11-08 16:18:32'),
(61, 67, 'CPSS-00067', 'Yasmin', 'Wife', '1478581166', '1996-12-25 00:00:00', 100, 'user_images/member_CPSS-00067/nominee_1_1762597668_690f1b24d4a6b.jpg', '2025-11-08 16:27:48'),
(62, 68, 'CPSS-00068', 'Taslima', 'Wife', '5553864041', '1997-04-24 00:00:00', 100, 'user_images/member_CPSS-00068/nominee_1_1762598139_690f1cfb1d8ea.jpg', '2025-11-08 16:35:39'),
(63, 69, 'CPSS-00069', 'Sahin Akter Beli', 'Sister', '4639722968', '1986-11-17 00:00:00', 100, 'user_images/member_CPSS-00069/nominee_1_1762598877_690f1fdd44ee3.jpg', '2025-11-08 16:47:57'),
(64, 70, 'CPSS-00070', 'Tarek', 'Brother', '5532116539', '1985-10-30 00:00:00', 100, 'user_images/member_CPSS-00070/nominee_1_1762599556_690f2284b25f8.jpg', '2025-11-08 16:59:16'),
(65, 71, 'CPSS-00071', 'Sumaiya Sharin Aurpy', 'Daughter ', '035325', '2008-01-20 00:00:00', 100, 'user_images/member_CPSS-00071/nominee_1_1762600570_690f267ac24e4.jpg', '2025-11-08 17:16:10'),
(66, 72, 'CPSS-00072', 'Insan Ara Rahman', 'Mother', '5991267955', '1958-09-17 00:00:00', 50, 'user_images/member_CPSS-00072/nominee_1_1762622505_690f7c290a91a.jpg', '2025-11-08 23:21:45'),
(67, 72, 'CPSS-00072', 'Nigar Sultana', 'Wife', '5542974653', '1988-01-30 00:00:00', 50, 'user_images/member_CPSS-00072/nominee_2_1762622505_690f7c290b802.jpg', '2025-11-08 23:21:45'),
(68, 73, 'CPSS-00073', 'Maria Akter', 'Wife', '1901914307', '1996-03-03 00:00:00', 100, 'user_images/member_CPSS-00073/nominee_1_1762622700_690f7cec38ebe.jpg', '2025-11-08 23:25:00'),
(69, 74, 'CPSS-00074', 'MEHERUN NESA', 'WIFE', '5558481700', '2000-12-16 00:00:00', 100, 'user_images/member_CPSS-00074/nominee_1_1762622735_690f7d0fa2f77.jpg', '2025-11-08 23:25:35'),
(70, 75, 'CPSS-00075', 'Farida Parvin', 'mother', '2613813199198', '1980-03-04 00:00:00', 100, 'user_images/member_CPSS-00075/nominee_1_1762623917_690f81ad1bcef.jpg', '2025-11-08 23:45:17'),
(71, 76, 'CPSS-00076', 'SHAHRINA PARVIN SATHY', 'Wife', '1938649835', '1993-03-29 00:00:00', 100, 'user_images/member_CPSS-00076/nominee_1_1762658099_691007334d67e.jpg', '2025-11-09 09:14:59'),
(72, 77, 'CPSS-00077', 'Yasmin Parvin', 'Wife', '4204449575', '1998-11-13 00:00:00', 100, 'user_images/member_CPSS-00077/nominee_1_1762660327_69100fe7d6b33.jpg', '2025-11-09 09:52:07'),
(73, 78, 'CPSS-00078', 'Sabina Yesmin', 'Mother', '6887873344', '1982-12-21 00:00:00', 100, 'user_images/member_CPSS-00078/nominee_1_1762701900_6910b24c872b5.jpg', '2025-11-09 21:25:00'),
(74, 79, 'CPSS-00079', 'Arfa Wajahat Azveena', 'Daughter', '20212692506000466', '2021-04-28 00:00:00', 100, 'user_images/member_CPSS-00079/nominee_1_1762757614_69118bee9506f.png', '2025-11-10 12:53:35'),
(75, 82, 'CPSS-00080', 'RABEA SULTANA', 'WIFE', '6448492576', '1992-03-05 00:00:00', 100, 'user_images/member_CPSS-00080/nominee_1_1763138237_69175abdaec75.jpg', '2025-11-14 22:37:17'),
(76, 85, 'CPSS-00083', 'ফজিলাতুন নেছা', 'মা ', '8677069661', '1943-01-10 00:00:00', 100, 'user_images/member_CPSS-00083/nominee_1_1763550712_691da5f849e3b.jpg', '2025-11-19 17:11:52'),
(80, 89, 'CPSS-00086', 'Rokeya Begum', 'Mother', '8654947038', '1956-02-01 00:00:00', 100, 'user_images/member_CPSS-00086/nominee_1_1763793010_692158720822b.jpg', '2025-11-22 12:30:10'),
(81, 90, 'CPSS-00090', 'ARJINA AKTER', 'Wife', '2868878113', '2006-10-30 00:00:00', 100, 'user_images/member_CPSS-00090/nominee_1_1764071479_69259837e4942.jpg', '2025-11-25 17:51:19'),
(82, 92, 'CPSS-00091', 'Hosne Ara Pervin', 'Mother', '19688829404760750', '1968-05-04 00:00:00', 100, 'user_images/member_CPSS-00091/nominee_1_1764137206_692698f62f939.jpg', '2025-11-26 12:06:46'),
(83, 93, 'CPSS-00093', 'Jahanara Begum', 'Mother', '8251804962', '1984-01-27 00:00:00', 100, 'user_images/member_CPSS-00093/nominee_1_1764225700_6927f2a42308b.jpeg', '2025-11-27 12:41:40'),
(84, 95, 'CPSS-00094', 'Most. Tasnova Islam Tonu', 'Wife', '6455267556', '1999-10-26 00:00:00', 100, 'user_images/member_CPSS-00094/nominee_1_1764263356_692885bc44933.png', '2025-11-27 23:09:16'),
(85, 97, 'CPSS-00096', 'Sabiha Rahman', 'Wife', '6013834509', '2000-06-27 00:00:00', 100, 'user_images/member_CPSS-00096/nominee_1_1764265210_69288cfa4fa87.jpg', '2025-11-27 23:40:10'),
(86, 98, 'CPSS-00098', 'Mst Nasrin Salam', 'Mother', '19759317614031468', '1975-01-01 00:00:00', 100, 'user_images/member_CPSS-00098/nominee_1_1764333248_692996c03ecbc.jpg', '2025-11-28 18:34:08'),
(87, 100, 'CPSS-00099', 'Khandakar Amit Hassan Adar', 'Brother', '1519153223', '2005-12-31 00:00:00', 100, 'user_images/member_CPSS-00099/nominee_1_1764342244_6929b9e488fda.jpg', '2025-11-28 21:04:04'),
(88, 101, 'CPSS-00101', 'Bipasha Das', 'Wife', '5978952793', '1994-06-04 00:00:00', 100, 'user_images/member_CPSS-00101/nominee_1_1764348060_6929d09c3951a.jpg', '2025-11-28 22:41:00'),
(89, 102, 'CPSS-00102', 'Asmaul husna Boisakhi ', 'Wife ', '20057615577009954', '2005-06-20 00:00:00', 75, 'user_images/member_CPSS-00102/nominee_1_1764348853_6929d3b5bfdb9.jpg', '2025-11-28 22:54:13'),
(90, 102, 'CPSS-00102', 'Md. Abu Bakar', 'Father ', '8665948108', '1974-11-01 00:00:00', 25, 'user_images/member_CPSS-00102/nominee_2_1764348853_6929d3b5c0063.jpeg', '2025-11-28 22:54:13'),
(91, 103, 'CPSS-00103', 'RIPA AKTER', 'WIFE', '5915622245', '1982-10-01 00:00:00', 100, 'user_images/member_CPSS-00103/nominee_1_1764417214_692adebe9214a.jpg', '2025-11-29 17:53:34'),
(92, 104, 'CPSS-00104', 'এসকে. লিমা ', 'Wife', '4652649528', '1997-01-01 00:00:00', 100, 'user_images/member_CPSS-00104/nominee_1_1764420960_692aed609f4d5.jpg', '2025-11-29 18:56:00'),
(93, 106, 'CPSS-00105', 'Israt Jahan Badhon', 'Wife', '2854944408', '1998-08-15 00:00:00', 100, 'user_images/member_CPSS-00105/nominee_1_1764432449_692b1a41824a9.jpeg', '2025-11-29 22:07:29'),
(94, 107, 'CPSS-00107', 'Masuma Yasmin Mithee', 'Wife', '3317240954', '2005-04-15 00:00:00', 100, 'user_images/member_CPSS-00107/nominee_1_1764477872_692bcbb080a67.jpg', '2025-11-30 10:44:32'),
(95, 108, 'CPSS-00108', 'Joly Khatun', 'Spouse', '6452016956', '1997-04-04 00:00:00', 100, 'user_images/member_CPSS-00108/nominee_1_1764564534_692d1e362e3e6.jpeg', '2025-12-01 10:48:54'),
(96, 109, 'CPSS-00109', 'Anika Akter', 'Wife', '20080610213106639', '2008-07-26 00:00:00', 100, 'user_images/member_CPSS-00109/nominee_1_1764581857_692d61e1ec4f1.jpg', '2025-12-01 15:37:38'),
(97, 110, 'CPSS-00110', 'Sheulee Akter', 'Mother', '5548069995', '1970-04-10 00:00:00', 100, 'user_images/member_CPSS-00110/nominee_1_1764608387_692dc983c1154.jpeg', '2025-12-01 22:59:47'),
(98, 112, 'CPSS-00111', 'মুহাম্মদ ইকবাল হোসেন', 'Husband', '19796816474388035', '1979-04-04 00:00:00', 100, 'user_images/member_CPSS-00111/nominee_1_1764819618_693102a2aca65.jpg', '2025-12-04 09:40:18'),
(99, 123, 'CPSS-00113', 'Ayesha Siddika', 'Wife', '1234567890', '1981-01-12 00:00:00', 100, 'user_images/member_CPSS-00113/nominee_1_1767520537_695a391915241.jpg', '2026-01-04 15:55:37'),
(100, 130, 'CPSS-00124', 'Shakil', 'Husband', '1111111111', '1995-01-01 00:00:00', 100, 'user_images/member_CPSS-00124/nominee_1_1770041397_6980b035e6b15.jpg', '2026-02-02 20:09:57'),
(101, 131, 'CPSS-00131', 'Shahana Akter', 'Mother', '7752479480', '1957-04-28 00:00:00', 100, 'user_images/member_CPSS-00131/nominee_1_1770042289_6980b3b102f59.jpg', '2026-02-02 20:24:49'),
(102, 132, 'CPSS-00132', 'Jenifa Islam Tisa', 'Spouse', '2829797766', '1993-01-01 00:00:00', 100, 'user_images/member_CPSS-00132/nominee_1_1770532357_69882e05521f4.jpg', '2026-02-08 12:32:37'),
(103, 134, 'CPSS-00133', 'সামিয়া আকতার', 'wife', '9109811167', '1987-01-01 00:00:00', 100, 'user_images/member_CPSS-00133/nominee_1_1770635200_6989bfc0808e8.jpeg', '2026-02-09 17:06:40');

-- --------------------------------------------------------

--
-- Table structure for table `member_office`
--

CREATE TABLE `member_office` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `member_code` varchar(50) NOT NULL,
  `office_name` varchar(100) NOT NULL,
  `office_address` text NOT NULL,
  `position` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `present_address` text DEFAULT NULL,
  `permanent_address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member_office`
--

INSERT INTO `member_office` (`id`, `member_id`, `member_code`, `office_name`, `office_address`, `position`, `created_at`, `present_address`, `permanent_address`) VALUES
(1, 1, 'CPSS-00001', 'ERA InfoTech Ltd', '35, Farest Tower, (3rd Floor), Purana Paltan, Dhaka-1000', 'Senior Software Engineer', '2025-10-13 21:31:57', '১০/এ-৩, বর্ধন বাড়ি, দারুস সালাম থানা, মিরপুর, ঢাকা', '১০/এ-৩, বর্ধন বাড়ি, দারুস সালাম থানা, মিরপুর, ঢাকা'),
(30, 30, 'CPSS-00002', 'ERA Infotech Limited', 'Fareast Tower, 35 Topkhana Road, (Level-3), Dhaka-1000', 'Implementation Engineer', '2025-10-13 22:28:41', 'Gazi Bari, Flat#B1, Nayabazar, Ati, Sakta, Keranigonj, Dhaka', 'Pabla, Natun Rastar More, Abu Shufian Sarani, Holding No# 379, Daulatpur, Khulna'),
(31, 31, 'CPSS-00031', 'Coder Mart', 'Gazi Bari, Flat#B1, Nayabazar, Ati, Sakta, Keranigonj, Dhaka', 'Manager', '2025-10-13 22:36:03', 'Gazi Bari, Flat#B1, Nayabazar, Ati, Sakta, Keranigonj, Dhaka', 'Pabla, Natun Rastar More, Abu Shufian Sarani, Holding No# 379, Daulatpur, Khulna'),
(33, 34, 'CPSS-00032', 'ERA InfoTech Ltd.', 'Fareast Tower, 35 Topkhana Road, (Level-3), Dhaka-1000', 'SR.Executive', '2025-10-14 16:04:29', 'Block: C,Road:13,House:11,Banaseree,Rampura,Dhaka', 'Village: West Sutarpara,P.O+P.S: Dohar,Dhaka'),
(34, 35, 'CPSS-00035', 'ERA-InfoTech Limited', 'Fareast Tower, Level # 03, 35, Topkhana Road, Dhaka-1229', 'Head of Company Affairs & Admin', '2025-10-14 16:51:59', 'Fareast Tower, Level # 03, 35, Topkhana Road, Dhaka-1229', 'Lake City Concord, Madhobi building, House # 13EB3/2, Khilkhet Nama Para, Dhaka-1229'),
(35, 36, 'CPSS-00036', 'ERA-InfoTech Limited.', 'ERA Infotech Ltd. ,Fareast Tower ~ Paltan, Level 3, Fareast Tower, 35 Topkhana Road.', 'Executive', '2025-10-14 18:48:46', '11/3-B, Mugdapara, Dhaka -1214', 'ward: 7, Bauphal, Patuakhali-8620'),
(37, 39, 'CPSS-00037', 'ERA INFOTECH LIMITED', 'Level 3, Fareast Tower, 35 Topkhana Road, Dhaka 1000', 'ASSOCIATE SOFTWARE ENGINEER', '2025-10-16 16:35:26', 'শারুলিয়া, ঢাকা, ডেমরা', 'ভাওরখোলা, মেঘনা, কুমিল্লা'),
(38, 40, 'CPSS-00040', 'ERA InfoTech Ltd.', 'Farest Tower(Level 3), 35 Topkhana road, Dhaka-1000', 'Executive', '2025-10-16 17:32:03', 'Sonir Akhra, Jatrabari, Dhaka', 'Vill: Rajnogor, PO: Kalekhar Bir, PS: Rampal, Dis: Bagerhat'),
(39, 41, 'CPSS-00041', 'AZIZ & COMPANY LTD', 'House-16. Road-01, Block-B, Nikatan, Gulshan-1.', 'Asst. Manager', '2025-10-19 11:12:10', '২৯/৪-এ মধ্য পাইকপাড়া, মিরপুর -১, ঢাকা-১২১৬।', '২৯/৪-এ মধ্য পাইকপাড়া, মিরপুর -১, ঢাকা-১২১৬।'),
(40, 42, 'CPSS-00042', 'Era Infotech Limited', 'Level 3, Fareast Tower, 35 Topkhana Road, Dhaka 1000', 'Head Of System Design', '2025-10-22 11:01:11', '59 S.C.C Road,Bangshal,Dhaka', '59 S.C.C Road,Bangshal,Dhaka'),
(43, 45, 'CPSS-00043', 'ERA INFOTECH LTD', 'Fareast Tower, Level-3, 35 Topkhana Road, Dhaka-1000, Bangladesh.', 'Engineer, Software Engineering', '2025-10-23 15:21:30', '36/Ka, Parvin Villa, PC Culture Road, Shyamoli, Dhaka 1207', '02/08, PTI Masterpara, Chapainawabganj Sadar 6300'),
(44, 46, 'CPSS-00046', 'ERA-InfoTech Limited', '35, Topkhana Road, Fareast Tower (3rd and 4th fl), Dhaka-1000', 'Asst. Manager (FAD)', '2025-10-23 16:19:51', '1272/1 and 1272/2 East shewrapara, Mirpur, Dhaka-1216', '1272/1 and 1272/2 East shewrapara, Mirpur, Dhaka-1216'),
(45, 47, 'CPSS-00047', 'ERA-Info Tech Ltd', 'Fareast Tower, 35 Topkhana Road, (Level-3), Dhaka-1000', 'Sr. Software Engineer', '2025-10-23 22:02:11', 'House no:20,Road no:2,Kallyanpur,Dhaka', 'Vill+post:Mohadevpur,Ps:Shivalya,Dist:Manikganj'),
(46, 48, 'CPSS-00048', 'ERA Infotech Ltd.', '35, Fareast Tower(3rd, 4th Floor), Paltan More, Dhaka', 'Associate Engineer', '2025-10-25 08:44:27', 'Mollaretk, Dakkhinkhan, Uttara, Dhaka', 'Mollaretk, Dakkhinkhan, Uttara, Dhaka'),
(48, 50, 'CPSS-00049', 'ERA', 'palton', 'Software Eng.', '2025-10-25 23:00:21', 'Sonir akhra jatrabari', 'Pabna'),
(49, 51, 'CPSS-00051', 'ERA-InfoTech Limited', '35 Topkhana, Road, Fareast Tower Level-3, Dhaka-1000.', 'Manager, FAD', '2025-10-26 12:46:33', '1/1 Ka, South Begunbari, Tejgaon, Dhaka.', 'Vill-Sujanagor, P.O-Rampal, P.S-Munshigonj, Dist-Munshigonj.'),
(50, 52, 'CPSS-00052', 'মৎস্য অধিদপ্তর', 'মৎস্য অধিদপ্তর, রমনা, ঢাকা', 'একাউন্টস অফিসার', '2025-10-26 16:20:08', '১০/এ-৩, বর্ধন বাড়ি, দারুস সালাম থানা, মিরপুর, ঢাকা', '১০/এ-৩, বর্ধন বাড়ি, দারুস সালাম থানা, মিরপুর, ঢাকা'),
(53, 55, 'CPSS-00053', 'Era Infotech Ltd.', 'Fareast Tower, Topkhana road, Paltan ,Dhaka -1000', 'Associate Software Engineer', '2025-10-27 12:41:48', '345/2, Jafrabad , Mohammadpur , Dhaka-1207', 'East Harinahati, Kotalipara, Goplaganj'),
(54, 56, 'CPSS-00056', 'DBH FINANCE PLC.', '12-14, LANDMARK BUILDING,14 FLOOR,GULSHAN-2, DHAKA-1212', 'ASSISTANT MANAGER', '2025-10-27 18:43:51', 'House : 7/B, Road : 2, Kallyanpur, Dhaka-1207', 'Vill : Ramer Danga, P.O : Baladanga, P.S+Dist : Satkhira-9400'),
(55, 57, 'CPSS-00057', 'Walton', 'Plot-1088, Block-I, Sabrina Sobhan Road P.O-Khilkhet, P.S-Vatara, Bashundhara R/A', 'Deputy Operative Director', '2025-10-28 21:48:00', 'House No. 416, Road No. 7, Block D, Bashundhara RA', '17/D, Jahanara Garden, Housing State, Goalchamot, Faridpur'),
(56, 59, 'CPSS-00058', 'National Housing Finance PLC', 'Plot: 11-A, Road No.- 48, Block- CWN (A), Gulshan- 2, Dhaka- 1212', 'Senior Officer (IT)', '2025-10-30 14:18:45', '394/A, West Nakhalpara, Tejgaon, Dhaka', 'Kazi Bari, Kachua, Kachua, Chandpur, PO : 3630'),
(58, 62, 'CPSS-00060', 'Crystal International General Trading LLC', 'Gold Souk, Deira, Dubai, UAE', 'Manager', '2025-10-31 22:39:47', '2/F/1, Gold Souk, Deira, Dubai, UAE', '১০/এ-৩, বর্ধন বাড়ি, দারুস সালাম থানা, মিরপুর, ঢাকা'),
(59, 63, 'CPSS-00063', 'Era InfoTech LTD.', 'Level 3, Fareast Tower, 35 Topkhana Road, Dhaka 1000', 'Associate Engineer, Software Quality Assurance', '2025-11-05 11:41:45', '1ka, Dhanmondi 7/A', '1ka, Dhanmondi 7/A'),
(60, 65, 'CPSS-00064', '', '', '', '2025-11-08 16:08:49', 'Flat#7-A, Plot#4,Block#3,Road#12,Basila,Mohammadpur,Dhaka', 'H# 43, Road:154, Khalishpur, Central Block, Khulna-9000,'),
(61, 66, 'CPSS-00066', '', '', '', '2025-11-08 16:18:32', 'Vill# (Amani Bari)Shaitala, P/O# Moricha, P:S# Debidwar, Dist# Cumilla,', 'Vill# (Amani Bari)Shaitala, P/O# Moricha, P:S# Debidwar, Dist# Cumilla,'),
(62, 67, 'CPSS-00067', '', '', '', '2025-11-08 16:27:48', 'House#339, Muktijoddha Safiuddin Road, Ainusbagh, Dakkhinkhan, Dhaka', 'Vill# Singahar, post# Abadpukur Hat, P:S# Adamdhigi, Dist#Bagura,'),
(63, 68, 'CPSS-00068', '', '', '', '2025-11-08 16:35:39', '21/6, Khandhoker Garden ,Prof.Irsdullah Road, Sanir Akhra, Jatrabari, Dhaka', 'vill:Kanchanpur Kazira para, post:Kanchanpur, P.S#Bashail, Dist:Tangail,'),
(64, 69, 'CPSS-00069', '', '', '', '2025-11-08 16:47:57', '6/8(B-1), Pallabi, Mirpur,  Dhaka-1216', '6/8(B-1), Pallabi, Mirpur,  Dhaka-1216'),
(65, 70, 'CPSS-00070', '', '', '', '2025-11-08 16:59:16', 'D-4, F-17, D Type Colony, NoorJahan Road,Mohammadpur, Dhaka-1207', 'D-4, F-17, D Type Colony, NoorJahan Road,Mohammadpur, Dhaka-1207'),
(66, 71, 'CPSS-00071', '', '', '', '2025-11-08 17:16:10', 'House No# 314/316,Lalbagh, Dhaka', 'Vill# Joat satnala, P:O# Taroksahar Hat, P:S# Chirirbandor, Dist# Dinajpur'),
(67, 72, 'CPSS-00072', 'Vantage Labs', 'Mirpur-11, Dhaka, Bangladesh', 'Software Engineer', '2025-11-08 23:21:45', '2nd Floor, House: 43,44, Block: B, Kajifuri, Gudaraghat, Mirpur-1, Dhaka-1216', 'Same as present address'),
(68, 73, 'CPSS-00073', 'ERA InfoTech Ltd', 'Level 3, Fareast Tower, 35 Topkhana Road, Dhaka 1000', 'Software Engineer', '2025-11-08 23:25:00', 'H#24, R#3, Block-E, Banasree, Dhaka', 'H#24, R#3, Block-E, Banasree, Dhaka'),
(69, 74, 'CPSS-00074', 'Prime Bank PLC.', 'Prime tower , Khilkhet, Nikunja Dhaka', 'Executive Officer', '2025-11-08 23:25:35', '9, Shahid Faruk Sarak, West Jatrabari, Dhaka-1204', '9, Shahid Faruk Sarak, West Jatrabari, Dhaka-1204'),
(70, 75, 'CPSS-00075', 'Al Modina, Dubai', 'Ajman, Dubai', 'Salesman', '2025-11-08 23:45:17', '9, Shahid Faruk Sarak, West Jatrabari, Dhaka-1204', '9, Shahid Faruk Sarak, West Jatrabari, Dhaka-1204'),
(71, 76, 'CPSS-00076', 'ERA Infotech Ltd.', 'Fareast Tower, 35 Topkhana Road, (Level-3,4), Dhaka-1000', 'Software Engineer', '2025-11-09 09:14:59', 'Ka-16/A Rasulbag, Mohakhali, Dhaka', '10/A-3, Bardhan Bari, Darus Salam Thana Road, Mirpur-1, Dhaka.'),
(72, 77, 'CPSS-00077', 'ERA INFO TECH LTD.', 'Farest Tower(Level 3rd & 4th) , 35, Topkhana Road, Dhaka-1000', 'Senior Engineer', '2025-11-09 09:52:07', 'House No#20, Road No #02, Kollayanpur ,Dhaka', 'House No #53, Dr. Koffar Road, Munshipara,Saidpara,Nilphamari'),
(73, 78, 'CPSS-00078', 'ERA Infotech LTD', 'Fareast Tower, Topkhana Road, Puran Paltan, Dhaka', 'Junior Executive', '2025-11-09 21:25:00', '12 no. Isdair, Pilkhana Road, Narayanganj', '12 no. Isdair, Pilkhana Road, Narayanganj'),
(74, 79, 'CPSS-00079', 'DBH Finance PLC.', 'Landmark Building(14th floor) , 12-14 North Gulshan C/A , Gulshan-2 , Dhaka-  1212', 'Assistant Manager - IT', '2025-11-10 12:53:34', 'House- 1 , Road - N/1 , Block- J, Eastern Housing , Pallabi 2nd Phase , Rupnagar , Dhaka - 1216', 'House- 1 , Road - N/1 , Block- J, Eastern Housing , Pallabi 2nd Phase , Rupnagar , Dhaka - 1216'),
(77, 82, 'CPSS-00080', 'ERA INFOTECH LTD', 'Fareast Tower, 3rd floor, 35 Topkhana Road, Dhaka-1000', 'Software Architect', '2025-11-14 22:37:17', 'JATRABARI, DHAKA', 'Baukathi, Jhalokathi Sadar'),
(78, 85, 'CPSS-00083', 'ইরা ইনফোটেক লিমিটেড', 'লেভেল ৩, ফারইস্ট টাওয়ার, ৩৫ তোপখানা রোড, ঢাকা ১০০০', 'Engineer', '2025-11-19 17:11:52', 'বাড়ি: ১৭, রোড: ১, দক্ষিণ আনন্দ নগর, আফতাবনগর, ঢাকা -১৪১৪', 'গ্রাম- ধন্যপুর, পোস্ট অফিস- সোনাইমুড়ি, জিপ কোড: 3827 জেলা-নোয়াখালী'),
(82, 89, 'CPSS-00086', 'Era Infotech Limited', 'Level 3, Fareast Tower, 35 Topkhana Road, Dhaka 1000', 'Sr. Software Engineer', '2025-11-22 12:30:10', 'House No# 33, Plot# A, Ward# 2, Canelpar Road, North Bhuigor, P:O# Bhuigor, P:S# Fatulla, Dist# Narayanganj', 'Vill# Haripur, P:O#Haripur Bazar, P:S#Chhagalnaiya, Dist# Feni'),
(83, 90, 'CPSS-00090', 'Era Infotech LTD', 'Level 3, Fareast Tower, 35 Topkhana Road, Dhaka 1000', 'Associate Software Engineer', '2025-11-25 17:51:19', '155 Central Bashaboo, Dhaka-1214', 'East Debpur, Chhagalnaiya, Feni'),
(84, 92, 'CPSS-00091', 'ERA Infotech LTD', 'Level 3, Fareast Tower, 35 Topkhana Road, Dhaka 1000', 'Software Engineer', '2025-11-26 12:06:46', 'House-643,  level-2, Girl\'s School Road, Dakhinkhan Bazar, Dhaka', 'House-643,  level-2, Girl\'s School Road, Dakhinkhan Bazar, Dhaka'),
(85, 93, 'CPSS-00093', 'ERA Info-Tech Ltd.', 'Level 3, Fareast Tower, 35 Topkhana Road, Dhaka 1000', 'Jr.Executive(Software Implementation)', '2025-11-27 12:41:40', 'House:87,Vatulia Moszid Road,Turag,Dhaka-1230', 'House:87,Vatulia Moszid Road,Turag,Dhaka-1230'),
(87, 95, 'CPSS-00094', 'Co Operative Of Credit Union Legue Of Bangladesh', 'Khilbarirtek School Rd,NotunBazar,Dhaka', 'Full Stack Softwear Eng.', '2025-11-27 23:09:16', 'Prembagan,Dakhinkhan,Dhaka', 'Ufulki,Mirzapur,Tangail'),
(89, 97, 'CPSS-00096', 'Reddot Digital Ltd', '57/A Uday Tower, Gulshan 1', 'Software engineer', '2025-11-27 23:40:10', '৩৭ রাজারবাগ, বাসাবো, সবুজবাগ ঢাকা ১২১৪', '৩৭ রাজারবাগ, বাসাবো, সবুজবাগ ঢাকা ১২১৪'),
(90, 98, 'CPSS-00098', 'Aury Care GmbH', 'Am Mühlenberg 11, 14476 Potsdam, Germany', 'Software Engineer', '2025-11-28 18:34:08', 'Potsdam, Germany', 'Rakhaliachaka, Shafipur, Gazipur'),
(91, 100, 'CPSS-00099', 'W3 Engineers Ltd.', 'World Business Centrum, 4th Floor, 76/A, Banani-11, Dhaka-1213', 'Software Engineer', '2025-11-28 21:04:04', '802/3, West Kazipara, Mirpur, Dhaka- 1216', 'Village: Dwimukha, Post Office: Dwimukha, Police Station: Dhamrai, District: Dhaka'),
(92, 101, 'CPSS-00101', 'Shanta Asset Management Limited', 'Level 13,Glass House,Gulshan 1,Dhaka', 'Assistant Manager-Software Development', '2025-11-28 22:41:00', 'Sultana Garden,H#14/2/A,Kalachandpur,Moral Bazar Back Side,Vatara', 'Ward no 8,Shadhonpur,Bashkhali,Chattogram'),
(93, 102, 'CPSS-00102', 'Technonext software ltd', 'Sayed nogor, notun bazar, Vatara', 'Software engineer', '2025-11-28 22:54:13', 'সাঈদ নগর, নতুন বাজার, ভাটারা', 'শ্যামনগর, সাতবাড়িয়া, সুজানগর, পাবনা'),
(94, 103, 'CPSS-00103', 'ERA InfoTech Ltd.', 'Fareast Tower, 3rd and 4th Floor, topkhana Road, Dhaka-1000', 'Vice President', '2025-11-29 17:53:34', 'House#41/17 ,  Chandmia Housing Limited, 4th Floor, Mohammadpur, Dhaka-1207', 'Vill:Baherpara, PO:Sonarang, Thana:Tongibari, Dist:Munshiganj'),
(95, 104, 'CPSS-00104', 'Era Infotech Ltd', 'Fareast Tower, 35 Topkhana Road, (Level-3), Dhaka-1000', 'Associate Software Engineer', '2025-11-29 18:56:00', 'House No: 217, 3rd colony Lalkuthi, mirpur-1', 'House No: 217, 3rd colony Lalkuthi, mirpur-1'),
(97, 106, 'CPSS-00105', 'Era InfoTech Ltd.', 'Level 3, Fareast Tower, 35 Topkhana Road', 'Project Manager', '2025-11-29 22:07:29', 'Vill: Binod Bari, PO: Amin Bazar, Savar, Dhaka', 'Vill: Binod Bari, PO: Amin Bazar, Savar, Dhaka'),
(98, 107, 'CPSS-00107', 'ERA Info-Tech Ltd.', 'Fareast Tower, 35 Topkhana Road, (Level-3), Dhaka-1000', 'Software Engineer', '2025-11-30 10:44:32', '26/6 Omar Ali Lane,West Rampura,Dhaka-1219', 'Bengal Center, 28 Topkhana Road, Dhaka 1000'),
(99, 108, 'CPSS-00108', 'Era Infotech Ltd.', 'Fareast Tower, 35 Topkhana Road, (Level-3), Dhaka-1000', 'Associate Engineer', '2025-12-01 10:48:54', 'SA Tower, House No: 11, Road No: 03, Basila Model Town, Basila, Mohammadpur', 'Vill: Hoza Annata Kandi, P.O: Pananagar, P.S: Durgapur, District: Rajshahi'),
(100, 109, 'CPSS-00109', 'Era Infotech Ltd', 'Purana paltan, Dhaka', 'Associate Software Engineer', '2025-12-01 15:37:37', '4a, 134, south kamlapur, Dhaka', 'Village+Post: Vojergati, Thana+District :Gopalgonj'),
(101, 110, 'CPSS-00110', 'ERA Infotech Ltd.', 'Level 3, Fareast Tower, 35 Topkhana Road, Dhaka 1000, Bangladesh', 'Associate Software Engineer', '2025-12-01 22:59:47', 'House#7,Road #2/E Sector-4,Uttara Dhaka-1230', 'C-Block, House 562, MouBhag, West Madda, Brahmanbaria-3400, Brahmanbaria'),
(102, 112, 'CPSS-00111', 'Bank Asia PLC', 'Purana Paltan', 'Senior Officer', '2025-12-04 09:40:18', 'গ্রামঃ গোবিন্দপুর, ডাকঘরঃ আশারামপুর, উপজেলাঃ রায়পুরা, জেলাঃ নরসিংদী', 'গ্রামঃ গোবিন্দপুর, ডাকঘরঃ আশারামপুর, উপজেলাঃ রায়পুরা, জেলাঃ নরসিংদী'),
(104, 123, 'CPSS-00113', 'ERA', 'Dhaka', 'Manager', '2026-01-04 15:55:37', '778, East, Monipur, Mirpur, Dhaka', '19, Shankipara, Cantonment Mour, Mymensingh -2200'),
(105, 130, 'CPSS-00124', 'NRBG', 'Gulshan', 'Executive', '2026-02-02 20:09:57', '51, Dhapzel Road, Ragunathganj, Rangpur', '51, Dhapzel Road, Ragunathganj, Rangpur'),
(106, 131, 'CPSS-00131', 'ERA InfoTech Ltd', '35, Farest Tower, Topkhana Road, Dhaka-1000', 'Excutive', '2026-02-02 20:24:49', 'Dewvog, Munshiganj', 'Dewvog, Munshiganj'),
(107, 132, 'CPSS-00132', 'ERA Infotech Ltd.', '35, Topkhana Road, 3rd floor', 'Asst. Manager', '2026-02-08 12:32:37', 'Kazi Shaheber Project, Bongram, Teghoria, South Keranigonj, Dhaka', 'Vill. Nagar Soondardi, P.O+P.s: Muksudpur, Dist. Gopalgonj'),
(108, 134, 'CPSS-00133', 'Islamic Commercial Insurance Ltd', 'City Center (Level-16), 90/1, Motijheel C/A, Dhaka.', 'AVP(Reinsurance & Claim)', '2026-02-09 17:06:40', 'Md. Arifur Rahman, 554/1,Flat-7B, East kazipara, Kafrul, Mirpur, Dhaka-1216.', 'Md. Arifur Rahman, 554/1,Flat-7B, East kazipara, Kafrul, Mirpur, Dhaka-1216.');

-- --------------------------------------------------------

--
-- Table structure for table `member_payments`
--

CREATE TABLE `member_payments` (
  `id` bigint(20) NOT NULL,
  `member_id` bigint(20) NOT NULL,
  `member_code` varchar(50) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `project_id` int(11) DEFAULT 0,
  `bank_pay_date` timestamp NULL DEFAULT current_timestamp(),
  `bank_trans_no` varchar(100) DEFAULT NULL,
  `trans_no` varchar(100) NOT NULL,
  `amount` int(11) NOT NULL,
  `payment_year` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `serial_no` int(11) DEFAULT NULL,
  `for_fees` varchar(20) DEFAULT NULL,
  `payment_slip` text DEFAULT NULL,
  `status` varchar(3) DEFAULT NULL,
  `pay_mode` text DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member_payments`
--

INSERT INTO `member_payments` (`id`, `member_id`, `member_code`, `payment_method`, `project_id`, `bank_pay_date`, `bank_trans_no`, `trans_no`, `amount`, `payment_year`, `created_at`, `created_by`, `serial_no`, `for_fees`, `payment_slip`, `status`, `pay_mode`, `remarks`) VALUES
(103, 1, 'CPSS-00001', 'admission', 0, NULL, '', 'TRADMISSION20251', 1500, 2025, '2025-12-05 18:12:23', 3, 1, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit For Saifur D005'),
(104, 1, 'CPSS-00001', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE20251', 26000, 2025, '2025-12-05 18:33:06', 2, 1, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit For Saifur D005'),
(105, 1, 'CPSS-00001', 'Samity Share', 1, '2025-11-29 18:00:00', '049WB317833', 'TRSAMITY SHARE20252', 99000, 2025, '2025-12-05 18:35:20', 2, 2, 'Samity Share', 'payment_slip_1_1764959720_8421.jpg', 'A', 'BP', 'Adjustment From This Slip'),
(106, 45, 'CPSS-00043', 'admission', 0, NULL, '', 'TRADMISSION20252', 1500, 2025, '2025-12-05 19:13:41', 2, 2, 'admission', NULL, 'A', 'AD', 'Adjustment From Bank Online Slip'),
(107, 45, 'CPSS-00043', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE20253', 10000, 2025, '2025-12-05 19:15:02', 2, 3, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Bank Online Slip (549WB008834)'),
(108, 45, 'CPSS-00043', 'Project Share', 4, '2025-11-10 18:00:00', 'Bank Online Slip', 'TRPROJECT SHARE20251', 10000, 2025, '2025-12-05 19:17:19', 2, 1, 'Project Share', NULL, 'A', 'BP', 'Adjustment From Bank Online Slip (549WB008834)'),
(109, 30, 'CPSS-00002', 'admission', 0, NULL, '', 'TRADMISSION20253', 1500, 2025, '2025-12-06 07:27:48', 2, 3, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Anwar'),
(110, 30, 'CPSS-00002', 'Samity Share', 1, '2025-10-29 18:00:00', 'Bank Online Slip', 'TRSAMITY SHARE20254', 50000, 2025, '2025-12-06 07:29:03', 2, 4, 'Samity Share', NULL, 'A', 'BP', 'Adjustment From Bank Online Slip'),
(111, 30, 'CPSS-00002', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE20255', 15000, 2025, '2025-12-06 07:30:09', 2, 5, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Anwar'),
(112, 31, 'CPSS-00031', 'admission', 0, NULL, '', 'TRADMISSION20254', 1500, 2025, '2025-12-06 07:30:24', 2, 4, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Anwar'),
(113, 31, 'CPSS-00031', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE20256', 10000, 2025, '2025-12-06 07:30:36', 2, 6, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Anwar'),
(114, 36, 'CPSS-00036', 'admission', 0, NULL, '', 'TRADMISSION20255', 1500, 2025, '2025-12-06 07:32:34', 2, 5, 'admission', NULL, 'A', 'AD', 'Adjustment From Bank Slip No 7EF0D07F'),
(115, 36, 'CPSS-00036', 'Samity Share', 1, '2025-11-29 18:00:00', '049WB323337', 'TRSAMITY SHARE20257', 10000, 2025, '2025-12-06 07:33:54', 2, 7, 'Samity Share', 'payment_slip_36_1765006434_9502.jpg', 'A', 'BP', 'Adjustment From Bank Deposit 7EF0D07F'),
(117, 39, 'CPSS-00037', 'admission', 0, NULL, '', 'TRADMISSION20256', 1500, 2025, '2025-12-06 07:35:49', 2, 6, 'admission', NULL, 'A', 'AD', 'Adjustment From Bank Deposit E77FF6F9'),
(118, 39, 'CPSS-00037', 'Samity Share', 1, '2025-11-08 18:00:00', '049WB319683', 'TRSAMITY SHARE20258', 10000, 2025, '2025-12-06 07:37:21', 2, 8, 'Samity Share', 'payment_slip_39_1765006641_6354.jpg', 'A', 'BP', 'Adjustment From This Slip'),
(119, 39, 'CPSS-00037', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE20253', 10000, 2025, '2025-12-06 07:38:02', 2, 3, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Bank Slip No E77FF6F9'),
(120, 41, 'CPSS-00041', 'admission', 0, NULL, '', 'TRADMISSION20257', 1500, 2025, '2025-12-06 07:39:34', 2, 7, 'admission', NULL, 'A', 'AD', 'Adjustment From Bank Slip No 253147743427'),
(121, 41, 'CPSS-00041', 'Samity Share', 1, '2025-11-09 18:00:00', '253147743427', 'TRSAMITY SHARE20259', 10000, 2025, '2025-12-06 07:40:53', 2, 9, 'Samity Share', 'payment_slip_41_1765006853_2904.jpg', 'A', 'BP', 'Adjustment From Bank Slip No 253147743427'),
(122, 52, 'CPSS-00052', 'admission', 0, NULL, '', 'TRADMISSION20258', 1500, 2025, '2025-12-06 07:51:25', 2, 8, 'admission', NULL, 'A', 'AD', 'Adjustment From Bank Slip No DB7936D3'),
(123, 52, 'CPSS-00052', 'Samity Share', 1, '2025-11-09 18:00:00', '049WB319754', 'TRSAMITY SHARE202510', 10000, 2025, '2025-12-06 07:52:31', 2, 10, 'Samity Share', 'payment_slip_52_1765007551_7324.jpg', 'A', 'BP', 'Adjustment From Bank Slip No DB7936D3'),
(125, 52, 'CPSS-00052', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE20254', 15000, 2025, '2025-12-06 07:57:58', 2, 4, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Bank Slip No DB7936D3'),
(126, 42, 'CPSS-00042', 'admission', 0, NULL, '', 'TRADMISSION20259', 1500, 2025, '2025-12-06 08:04:06', 2, 9, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Shirajul'),
(127, 42, 'CPSS-00042', 'Samity Share', 1, '2025-10-29 18:00:00', 'Bank Online Slip', 'TRSAMITY SHARE202511', 160000, 2025, '2025-12-06 08:07:04', 2, 11, 'Samity Share', NULL, 'A', 'BP', 'Adjustment From Bank Online Slip'),
(128, 47, 'CPSS-00047', 'admission', 0, NULL, '', 'TRADMISSION202510', 1500, 2025, '2025-12-06 08:08:48', 2, 10, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Chamak'),
(129, 47, 'CPSS-00047', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202512', 10000, 2025, '2025-12-06 08:09:09', 2, 12, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Shirajul'),
(130, 47, 'CPSS-00047', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE20255', 195000, 2025, '2025-12-06 08:09:33', 2, 5, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Shirajul'),
(131, 48, 'CPSS-00048', 'admission', 0, NULL, '', 'TRADMISSION202511', 1500, 2025, '2025-12-06 08:10:36', 2, 11, 'admission', NULL, 'A', 'AD', 'Adjustment From Bank Slip No 645AA4E8'),
(132, 48, 'CPSS-00048', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202513', 10000, 2025, '2025-12-06 08:11:12', 2, 13, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Bank Slip No 645AA4E8'),
(133, 48, 'CPSS-00048', 'Project Share', 4, '2025-11-30 18:00:00', '049WB327373', 'TRPROJECT SHARE20256', 90000, 2025, '2025-12-06 08:13:23', 2, 6, 'Project Share', 'payment_slip_48_1765008803_6867.jpg', 'A', 'BP', 'Adjustment From Bank Deposit Slip 645AA4E8'),
(134, 51, 'CPSS-00051', 'admission', 0, NULL, '', 'TRADMISSION202512', 1500, 2025, '2025-12-06 08:15:02', 2, 12, 'admission', NULL, 'A', 'AD', 'Adjustment From Bank Deposit Slip 049CI427969'),
(135, 51, 'CPSS-00051', 'Samity Share', 1, '2025-11-30 18:00:00', '049CI427969', 'TRSAMITY SHARE202514', 10000, 2025, '2025-12-06 08:16:08', 2, 14, 'Samity Share', 'payment_slip_51_1765008968_3761.jpg', 'A', 'BP', 'Adjustment From Bank Deposit Slip 049CI427969'),
(136, 62, 'CPSS-00060', 'admission', 0, NULL, '', 'TRADMISSION202513', 1500, 2025, '2025-12-06 08:18:10', 2, 13, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Saifur'),
(137, 62, 'CPSS-00060', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202515', 10000, 2025, '2025-12-06 08:18:41', 2, 15, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Saifur'),
(138, 62, 'CPSS-00060', 'Project Share', 4, '2025-11-18 18:00:00', '049WB321064', 'TRPROJECT SHARE20257', 115000, 2025, '2025-12-06 08:21:11', 2, 7, 'Project Share', 'payment_slip_62_1765009271_4294.jpg', 'A', 'BP', 'Adjustment From Bank Deposit Slip No DD9296BA'),
(139, 56, 'CPSS-00056', 'admission', 0, NULL, '', 'TRADMISSION202514', 1500, 2025, '2025-12-06 08:23:52', 2, 14, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Mosharof'),
(140, 56, 'CPSS-00056', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202516', 7500, 2025, '2025-12-06 08:24:48', 2, 16, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Mosharof'),
(141, 56, 'CPSS-00056', 'Samity Share', 1, '2025-10-29 18:00:00', '049WB317837', 'TRSAMITY SHARE202517', 2500, 2025, '2025-12-06 08:26:34', 2, 17, 'Samity Share', 'payment_slip_56_1765009594_8002.jpg', 'A', 'BP', 'Adjustment From Bank Deposit Slip'),
(142, 56, 'CPSS-00056', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE20258', 15000, 2025, '2025-12-06 08:27:43', 2, 8, 'Project Share', NULL, 'A', 'AD', 'Adjustment Bank Deposit Slip 049WB317837'),
(143, 57, 'CPSS-00057', 'admission', 0, NULL, '', 'TRADMISSION202515', 1500, 2025, '2025-12-06 12:04:33', 2, 15, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Tonu'),
(144, 57, 'CPSS-00057', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202518', 10000, 2025, '2025-12-06 12:05:08', 2, 18, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Tonu'),
(145, 57, 'CPSS-00057', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE20259', 40000, 2025, '2025-12-06 12:05:43', 2, 9, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Tonu'),
(146, 59, 'CPSS-00058', 'admission', 0, NULL, '', 'TRADMISSION202516', 1500, 2025, '2025-12-06 12:06:36', 2, 16, 'admission', NULL, 'A', 'AD', 'Adjustment From Bank Deposit Slip No A86DABF8'),
(147, 59, 'CPSS-00058', 'Samity Share', 1, '2025-11-10 18:00:00', '049WB319931', 'TRSAMITY SHARE202519', 10000, 2025, '2025-12-06 12:08:00', 2, 19, 'Samity Share', 'payment_slip_59_1765022880_9753.jpg', 'A', 'BP', 'Adjustment From Bank Deposit Slip'),
(148, 63, 'CPSS-00063', 'admission', 0, NULL, '', 'TRADMISSION202517', 1500, 2025, '2025-12-06 12:08:56', 2, 17, 'admission', NULL, 'A', 'AD', 'Adjustment From Bank Deposit Slip'),
(149, 63, 'CPSS-00063', 'Samity Share', 1, '2025-11-08 18:00:00', '049WB319664', 'TRSAMITY SHARE202520', 10000, 2025, '2025-12-06 12:10:31', 2, 20, 'Samity Share', 'payment_slip_63_1765023031_4581.jpg', 'A', 'BP', 'Adjustment From Bank Deposit Slip 327AE77F'),
(150, 65, 'CPSS-00064', 'admission', 0, NULL, '', 'TRADMISSION202518', 1500, 2025, '2025-12-06 12:11:09', 2, 18, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Masuma'),
(151, 65, 'CPSS-00064', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202521', 10000, 2025, '2025-12-06 12:13:07', 2, 21, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Masuma'),
(152, 65, 'CPSS-00064', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202510', 90000, 2025, '2025-12-06 12:13:59', 2, 10, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Masuma '),
(153, 66, 'CPSS-00066', 'admission', 0, NULL, '', 'TRADMISSION202519', 1500, 2025, '2025-12-06 12:16:34', 2, 19, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Enamul'),
(154, 66, 'CPSS-00066', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202522', 10000, 2025, '2025-12-06 12:16:50', 2, 22, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Enamul'),
(155, 66, 'CPSS-00066', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202511', 5000, 2025, '2025-12-06 12:17:05', 2, 11, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Enamul'),
(156, 67, 'CPSS-00067', 'admission', 0, NULL, '', 'TRADMISSION202520', 1500, 2025, '2025-12-06 12:17:22', 2, 20, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Enamul Hoque'),
(157, 67, 'CPSS-00067', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202523', 10000, 2025, '2025-12-06 12:17:37', 2, 23, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Enamul'),
(158, 67, 'CPSS-00067', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202512', 10000, 2025, '2025-12-06 12:18:01', 2, 12, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Enamul'),
(159, 69, 'CPSS-00069', 'admission', 0, NULL, '', 'TRADMISSION202521', 1500, 2025, '2025-12-06 12:19:17', 2, 21, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Hasina'),
(160, 69, 'CPSS-00069', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202524', 10000, 2025, '2025-12-06 12:19:39', 2, 24, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Hasina'),
(161, 69, 'CPSS-00069', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202513', 35000, 2025, '2025-12-06 12:22:09', 2, 13, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Hasina Due 2500'),
(162, 70, 'CPSS-00070', 'admission', 0, NULL, '', 'TRADMISSION202522', 1500, 2025, '2025-12-06 12:22:29', 2, 22, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Sultan'),
(163, 70, 'CPSS-00070', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202525', 10000, 2025, '2025-12-06 12:22:52', 2, 25, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Sultan'),
(164, 70, 'CPSS-00070', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202514', 220000, 2025, '2025-12-06 12:23:20', 2, 14, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Sultan'),
(165, 71, 'CPSS-00071', 'admission', 0, NULL, '', 'TRADMISSION202523', 1500, 2025, '2025-12-06 12:26:05', 2, 23, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Mahmuda'),
(166, 71, 'CPSS-00071', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202526', 10000, 2025, '2025-12-06 12:26:29', 2, 26, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Mahmuda'),
(167, 71, 'CPSS-00071', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202515', 15000, 2025, '2025-12-06 12:28:39', 2, 15, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Mahmuda Due 2500'),
(168, 72, 'CPSS-00072', 'admission', 0, NULL, '', 'TRADMISSION202524', 1500, 2025, '2025-12-06 12:29:24', 2, 24, 'admission', NULL, 'A', 'AD', 'Adjustment From Bank Deposit Slip 549WB008898'),
(169, 72, 'CPSS-00072', 'Samity Share', 1, '2025-11-22 18:00:00', 'Bank Online Slip', 'TRSAMITY SHARE202527', 10000, 2025, '2025-12-06 12:30:48', 2, 27, 'Samity Share', 'payment_slip_72_1765024248_5562.jpg', 'A', 'BP', 'Adjustment From Bank Online Slip 549WB008898'),
(170, 72, 'CPSS-00072', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202516', 40000, 2025, '2025-12-06 12:34:09', 2, 16, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Bank Online Slip 549WB008898'),
(171, 73, 'CPSS-00073', 'admission', 0, NULL, '', 'TRADMISSION202525', 1500, 2025, '2025-12-06 12:35:17', 2, 25, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Muktasib'),
(172, 73, 'CPSS-00073', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202528', 10000, 2025, '2025-12-06 12:35:41', 2, 28, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Muktasib'),
(173, 73, 'CPSS-00073', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202517', 65000, 2025, '2025-12-06 12:35:58', 2, 17, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Muktasib'),
(174, 74, 'CPSS-00074', 'admission', 0, NULL, '', 'TRADMISSION202526', 1500, 2025, '2025-12-06 12:36:48', 2, 26, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Arif'),
(175, 74, 'CPSS-00074', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202529', 10000, 2025, '2025-12-06 12:37:11', 2, 29, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Arif'),
(176, 74, 'CPSS-00074', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202518', 20000, 2025, '2025-12-06 12:37:33', 2, 18, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Arif'),
(177, 76, 'CPSS-00076', 'admission', 0, NULL, '', 'TRADMISSION202527', 1500, 2025, '2025-12-06 12:38:16', 2, 27, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Zakir'),
(178, 76, 'CPSS-00076', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202530', 10000, 2025, '2025-12-06 12:38:42', 2, 30, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Zakir'),
(179, 77, 'CPSS-00077', 'admission', 0, NULL, '', 'TRADMISSION202528', 1500, 2025, '2025-12-06 12:39:27', 2, 28, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Ashraful'),
(180, 77, 'CPSS-00077', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202531', 10000, 2025, '2025-12-06 12:39:50', 2, 31, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Ashraful'),
(181, 77, 'CPSS-00077', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202519', 15000, 2025, '2025-12-06 12:40:11', 2, 19, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Ashraful'),
(182, 78, 'CPSS-00078', 'admission', 0, NULL, '', 'TRADMISSION202529', 1500, 2025, '2025-12-06 12:40:59', 2, 29, 'admission', NULL, 'A', 'AD', 'Adjustment From Bank Deposit Slip 17FE31BE'),
(183, 78, 'CPSS-00078', 'Samity Share', 1, '2025-12-03 18:00:00', '049WB324373', 'TRSAMITY SHARE202532', 10000, 2025, '2025-12-06 12:42:57', 2, 32, 'Samity Share', 'payment_slip_78_1765024977_9652.jpg', 'A', 'BP', 'Adjustment From Bank Deposit Slip 17FE31BE'),
(184, 78, 'CPSS-00078', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202520', 15000, 2025, '2025-12-06 12:43:32', 2, 20, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Bank Deposit Slip 17FE31BE'),
(185, 79, 'CPSS-00079', 'admission', 0, NULL, '', 'TRADMISSION202530', 1500, 2025, '2025-12-06 12:45:52', 2, 30, 'admission', NULL, 'A', 'AD', 'Adjustment From Bank Deposit Slip 615CI420402'),
(186, 79, 'CPSS-00079', 'Samity Share', 1, '2025-11-16 18:00:00', '615CI420402', 'TRSAMITY SHARE202533', 10000, 2025, '2025-12-06 12:46:45', 2, 33, 'Samity Share', 'payment_slip_79_1765025205_9273.jpg', 'A', 'BP', 'Adjustment From Bank Deposit Slip 615CI420402'),
(187, 79, 'CPSS-00079', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202521', 25000, 2025, '2025-12-06 12:48:14', 2, 21, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Bank Deposit Slip 615CI420402'),
(188, 85, 'CPSS-00083', 'admission', 0, NULL, '', 'TRADMISSION202531', 1500, 2025, '2025-12-06 12:49:57', 2, 31, 'admission', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(189, 85, 'CPSS-00083', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202534', 10000, 2025, '2025-12-06 12:50:13', 2, 34, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(190, 85, 'CPSS-00083', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202522', 5000, 2025, '2025-12-06 12:50:33', 2, 22, 'Project Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(191, 89, 'CPSS-00086', 'admission', 0, NULL, '', 'TRADMISSION202532', 1500, 2025, '2025-12-06 12:51:14', 2, 32, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Tanjina'),
(192, 89, 'CPSS-00086', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202535', 10000, 2025, '2025-12-06 12:51:47', 2, 35, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Tanjina'),
(193, 89, 'CPSS-00086', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202523', 25000, 2025, '2025-12-06 12:52:07', 2, 23, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Deposit Tanjina'),
(194, 90, 'CPSS-00090', 'admission', 0, NULL, '', 'TRADMISSION202533', 1500, 2025, '2025-12-06 12:54:06', 2, 33, 'admission', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(195, 90, 'CPSS-00090', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202536', 10000, 2025, '2025-12-06 12:54:23', 2, 36, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(196, 90, 'CPSS-00090', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202524', 5000, 2025, '2025-12-06 12:55:29', 2, 24, 'Project Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(197, 92, 'CPSS-00091', 'admission', 0, NULL, '', 'TRADMISSION202534', 1500, 2025, '2025-12-06 12:55:41', 2, 34, 'admission', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(198, 92, 'CPSS-00091', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202537', 10000, 2025, '2025-12-06 12:55:56', 2, 37, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(199, 93, 'CPSS-00093', 'admission', 0, NULL, '', 'TRADMISSION202535', 1500, 2025, '2025-12-06 12:56:21', 2, 35, 'admission', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(200, 93, 'CPSS-00093', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202538', 10000, 2025, '2025-12-06 12:56:39', 2, 38, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(201, 93, 'CPSS-00093', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202525', 5000, 2025, '2025-12-06 12:56:55', 2, 25, 'Project Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(202, 95, 'CPSS-00094', 'admission', 0, NULL, '', 'TRADMISSION202536', 1500, 2025, '2025-12-06 12:58:00', 2, 36, 'admission', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(203, 95, 'CPSS-00094', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202539', 10000, 2025, '2025-12-06 12:58:20', 2, 39, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(204, 95, 'CPSS-00094', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202526', 5000, 2025, '2025-12-06 12:58:39', 2, 26, 'Project Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(205, 97, 'CPSS-00096', 'admission', 0, NULL, '', 'TRADMISSION202537', 1500, 2025, '2025-12-06 12:58:52', 2, 37, 'admission', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(206, 97, 'CPSS-00096', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202540', 10000, 2025, '2025-12-06 12:59:11', 2, 40, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(207, 97, 'CPSS-00096', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202527', 5000, 2025, '2025-12-06 12:59:30', 2, 27, 'Project Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(208, 98, 'CPSS-00098', 'admission', 0, NULL, '', 'TRADMISSION202538', 1500, 2025, '2025-12-06 12:59:47', 2, 38, 'admission', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(209, 98, 'CPSS-00098', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202541', 10000, 2025, '2025-12-06 13:00:00', 2, 41, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(210, 98, 'CPSS-00098', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202528', 5000, 2025, '2025-12-06 13:00:14', 2, 28, 'Project Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(211, 100, 'CPSS-00099', 'admission', 0, NULL, '', 'TRADMISSION202539', 1500, 2025, '2025-12-06 13:01:12', 2, 39, 'admission', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(212, 100, 'CPSS-00099', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202542', 10000, 2025, '2025-12-06 13:01:25', 2, 42, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(213, 100, 'CPSS-00099', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202529', 5000, 2025, '2025-12-06 13:02:06', 2, 29, 'Project Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(214, 101, 'CPSS-00101', 'admission', 0, NULL, '', 'TRADMISSION202540', 1500, 2025, '2025-12-06 13:02:21', 2, 40, 'admission', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(215, 101, 'CPSS-00101', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202543', 10000, 2025, '2025-12-06 13:02:41', 2, 43, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(216, 101, 'CPSS-00101', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202530', 5000, 2025, '2025-12-06 13:03:01', 2, 30, 'Project Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(217, 102, 'CPSS-00102', 'admission', 0, NULL, '', 'TRADMISSION202541', 1500, 2025, '2025-12-06 13:03:27', 2, 41, 'admission', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(218, 102, 'CPSS-00102', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202544', 10000, 2025, '2025-12-06 13:03:53', 2, 44, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(219, 102, 'CPSS-00102', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202531', 5000, 2025, '2025-12-06 13:04:15', 2, 31, 'Project Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(220, 103, 'CPSS-00103', 'admission', 0, NULL, '', 'TRADMISSION202542', 1500, 2025, '2025-12-06 13:04:31', 2, 42, 'admission', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(221, 103, 'CPSS-00103', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202545', 10000, 2025, '2025-12-06 13:04:46', 2, 45, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(222, 103, 'CPSS-00103', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202532', 5000, 2025, '2025-12-06 13:05:02', 2, 32, 'Project Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(223, 104, 'CPSS-00104', 'admission', 0, NULL, '', 'TRADMISSION202543', 1500, 2025, '2025-12-06 13:05:55', 2, 43, 'admission', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(224, 104, 'CPSS-00104', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202546', 10000, 2025, '2025-12-06 13:06:09', 2, 46, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(225, 104, 'CPSS-00104', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202533', 5000, 2025, '2025-12-06 13:06:26', 2, 33, 'Project Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(226, 106, 'CPSS-00105', 'admission', 0, NULL, '', 'TRADMISSION202544', 1500, 2025, '2025-12-06 13:06:54', 2, 44, 'admission', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(227, 106, 'CPSS-00105', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202547', 10000, 2025, '2025-12-06 13:07:09', 2, 47, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(228, 106, 'CPSS-00105', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202534', 5000, 2025, '2025-12-06 13:07:23', 2, 34, 'Project Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(229, 107, 'CPSS-00107', 'admission', 0, NULL, '', 'TRADMISSION202545', 1500, 2025, '2025-12-06 13:07:35', 2, 45, 'admission', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(230, 107, 'CPSS-00107', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202548', 10000, 2025, '2025-12-06 13:08:00', 2, 48, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(231, 108, 'CPSS-00108', 'admission', 0, NULL, '', 'TRADMISSION202546', 1500, 2025, '2025-12-06 13:08:19', 2, 46, 'admission', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(232, 108, 'CPSS-00108', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202549', 10000, 2025, '2025-12-06 13:08:34', 2, 49, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(233, 109, 'CPSS-00109', 'admission', 0, NULL, '', 'TRADMISSION202547', 1500, 2025, '2025-12-06 13:08:53', 2, 47, 'admission', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(234, 109, 'CPSS-00109', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202550', 10000, 2025, '2025-12-06 13:09:06', 2, 50, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(235, 109, 'CPSS-00109', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202535', 5000, 2025, '2025-12-06 13:09:24', 2, 35, 'Project Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(236, 110, 'CPSS-00110', 'admission', 0, NULL, '', 'TRADMISSION202548', 1500, 2025, '2025-12-06 13:09:37', 2, 48, 'admission', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(237, 110, 'CPSS-00110', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202551', 10000, 2025, '2025-12-06 13:09:49', 2, 51, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(238, 110, 'CPSS-00110', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202536', 5000, 2025, '2025-12-06 13:10:04', 2, 36, 'Project Share', NULL, 'A', 'AD', 'Adjustment From MicroTrust Samity Cheque No AA-2193703'),
(239, 107, 'CPSS-00107', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202537', 5000, 2025, '2025-12-07 04:22:24', 2, 37, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Bank Deposit Cheque No. AA-2193703'),
(240, 35, 'CPSS-00035', 'admission', 0, NULL, '', 'TRADMISSION202549', 1500, 2025, '2025-12-08 12:02:02', 2, 49, 'admission', NULL, 'A', 'AD', 'Adjustment From Land Price'),
(241, 35, 'CPSS-00035', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202552', 10000, 2025, '2025-12-08 12:02:34', 2, 52, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Land Price'),
(242, 112, 'CPSS-00111', 'admission', 0, NULL, '', 'TRADMISSION202550', 1500, 2025, '2025-12-08 12:02:59', 2, 50, 'admission', NULL, 'A', 'AD', 'Adjustment From Land Price'),
(243, 112, 'CPSS-00111', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE202553', 10000, 2025, '2025-12-08 12:03:24', 2, 53, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Land Price'),
(261, 36, 'CPSS-00036', 'Monthly', 0, '2026-01-13 18:00:00', '04934007904', 'TRJANUARY20261', 2000, 2026, '2026-01-15 10:50:14', 9, 1, 'january', 'payment_slip_36_1768474214_2032.pdf', 'A', 'BP', 'Monthly Payment Jan\'2026'),
(263, 41, 'CPSS-00041', 'Monthly', 0, '2026-01-13 18:00:00', '260143199720', 'TRJANUARY20261', 2000, 2026, '2026-01-15 11:00:32', 12, 1, 'january', 'payment_slip_41_1768474832_4248.jpeg', 'A', 'BP', 'Advance pay 1500 and new pay 500 = 2000'),
(264, 52, 'CPSS-00052', 'Monthly', 0, '2026-01-14 18:00:00', '049WB332059', 'TRJANUARY20261', 2000, 2026, '2026-01-15 11:02:17', 20, 1, 'january', 'payment_slip_52_1768474937_4722.jpeg', 'A', 'BP', 'January Month Pay for this slip'),
(288, 72, 'CPSS-00072', 'Monthly', 0, '2026-01-14 18:00:00', '049WB332057', 'TRJANUARY20262', 2000, 2026, '2026-01-15 17:45:45', 34, 2, 'january', 'payment_slip_72_1768499145_2408.jpeg', 'A', 'BP', 'January Monthly Payment'),
(289, 62, 'CPSS-00060', 'Monthly', 0, '2026-01-14 18:00:00', '049WB332059', 'TRJANUARY20263', 2000, 2026, '2026-01-15 17:47:52', 25, 3, 'january', 'payment_slip_62_1768499272_2335.jpeg', 'A', 'BP', 'January Month Payment for this slip'),
(290, 39, 'CPSS-00037', 'Monthly', 0, '2026-01-17 18:00:00', '049WB332500', 'TRJANUARY20264', 2000, 2026, '2026-01-18 07:05:43', 10, 4, 'january', NULL, 'A', 'BP', 'JUANUARY MONTH PAYMENT CLEAR. '),
(291, 48, 'CPSS-00048', 'Monthly', 0, '2026-01-18 18:00:00', '049WB332650', 'TRJANUARY20265', 2000, 2026, '2026-01-19 04:36:57', 17, 5, 'january', 'payment_slip_48_1768797417_1599.jpeg', 'A', 'BP', 'CPSS-00048 January payment'),
(292, 63, 'CPSS-00063', 'Monthly', 0, '2026-01-18 18:00:00', '04943010015', 'TRJANUARY20266', 2000, 2026, '2026-01-19 06:15:07', 26, 6, 'january', 'payment_slip_63_1768803307_3772.jpeg', 'A', 'BP', 'January Month Deposit'),
(296, 1, 'CPSS-00001', 'Monthly', 0, '2026-01-14 18:00:00', '57691730', 'TRJANUARY20261', 2000, 2026, '2026-01-19 10:12:19', 3, 1, 'january', 'payment_slip_1_1768817539_7871.jpeg', 'A', 'BP', 'Advance Monthly Deposit Jan-Jun\'2026'),
(297, 1, 'CPSS-00001', 'Monthly', 0, '2026-01-14 18:00:00', '57691730', 'TRFEBRUARY20261', 2000, 2026, '2026-01-19 10:12:19', 3, 1, 'february', 'payment_slip_1_1768817539_7871.jpeg', 'A', 'BP', 'Advance Monthly Deposit Jan-Jun\'2026'),
(298, 1, 'CPSS-00001', 'Monthly', 0, '2026-01-14 18:00:00', '57691730', 'TRMARCH20261', 2000, 2026, '2026-01-19 10:12:19', 3, 1, 'march', 'payment_slip_1_1768817539_7871.jpeg', 'A', 'BP', 'Advance Monthly Deposit Jan-Jun\'2026'),
(299, 1, 'CPSS-00001', 'Monthly', 0, '2026-01-14 18:00:00', '57691730', 'TRAPRIL20261', 2000, 2026, '2026-01-19 10:12:19', 3, 1, 'april', 'payment_slip_1_1768817539_7871.jpeg', 'A', 'BP', 'Advance Monthly Deposit Jan-Jun\'2026'),
(300, 1, 'CPSS-00001', 'Monthly', 0, '2026-01-14 18:00:00', '57691730', 'TRMAY20261', 2000, 2026, '2026-01-19 10:12:19', 3, 1, 'may', 'payment_slip_1_1768817539_7871.jpeg', 'A', 'BP', 'Advance Monthly Deposit Jan-Jun\'2026'),
(301, 1, 'CPSS-00001', 'Monthly', 0, '2026-01-14 18:00:00', '57691730', 'TRJUNE20261', 2000, 2026, '2026-01-19 10:12:19', 3, 1, 'june', 'payment_slip_1_1768817539_7871.jpeg', 'A', 'BP', 'Advance Monthly Deposit Jan-Jun\'2026'),
(302, 108, 'CPSS-00108', 'Monthly', 0, '2026-01-18 18:00:00', '049WB332687', 'TRJANUARY20261', 2000, 2026, '2026-01-25 07:40:23', 59, 1, 'january', NULL, 'A', 'BP', 'Monthly Payment for January 2026'),
(303, 78, 'CPSS-00078', 'Monthly', 0, '2026-01-17 18:00:00', '049WB332500', 'TRJANUARY20261', 2000, 2026, '2026-01-25 07:48:26', 40, 1, 'january', 'payment_slip_78_1769327306_7930.jpg', 'A', 'BP', ''),
(304, 73, 'CPSS-00073', 'Monthly', 0, NULL, '', 'TRJANUARY20261', 2000, 2026, '2026-01-25 08:48:09', 35, 1, 'january', NULL, 'A', 'AD', ''),
(305, 74, 'CPSS-00074', 'Monthly', 0, '2026-01-24 18:00:00', '602514580228', 'TRJANUARY20261', 2000, 2026, '2026-01-25 09:15:25', 36, 1, 'january', 'payment_slip_74_1769332525_2107.jpg', 'A', 'BP', 'Monthly Chada - 1'),
(306, 66, 'CPSS-00066', 'Monthly', 0, '2026-01-24 18:00:00', '049WB333754', 'TRJANUARY20261', 2000, 2026, '2026-01-25 15:34:31', 28, 1, 'january', 'payment_slip_66_1769355271_2774.jpeg', 'A', 'BP', 'paid'),
(307, 59, 'CPSS-00058', 'Monthly', 0, '2026-01-25 18:00:00', '049WB333882 and 049WB333911', 'TRJANUARY20261', 2000, 2026, '2026-01-26 05:42:05', 24, 1, 'january', 'payment_slip_59_1769406125_1086.pdf', 'A', 'BP', ''),
(308, 45, 'CPSS-00043', 'Monthly', 0, '2026-01-25 18:00:00', '04934010066', 'TRJANUARY20261', 2000, 2026, '2026-01-26 08:04:42', 14, 1, 'january', 'payment_slip_45_1769414682_1186.jpeg', 'A', 'BP', 'Monthly Deposit January 2026'),
(309, 47, 'CPSS-00047', 'Monthly', 0, '2026-01-18 18:00:00', '049WB332682', 'TRJANUARY20261', 2000, 2026, '2026-01-27 07:59:49', 16, 1, 'january', 'payment_slip_47_1769500789_6138.jpeg', 'A', 'BP', ''),
(310, 92, 'CPSS-00091', 'Monthly', 0, '2026-01-18 18:00:00', '049WB332682', 'TRJANUARY20261', 2000, 2026, '2026-01-28 03:39:28', 47, 1, 'january', 'payment_slip_92_1769571568_1841.jpeg', 'A', 'BP', 'January Monthly Payment'),
(311, 40, 'CPSS-00040', 'admission', 0, '2026-01-26 18:00:00', '049WB334277', 'TRADMISSION20261', 1500, 2026, '2026-01-28 03:45:44', 2, 1, 'admission', 'payment_slip_40_1769571944_9132.jpeg', 'A', 'BP', 'Admission Fee Adjustment From this Slip'),
(312, 40, 'CPSS-00040', 'Samity Share', 1, '2026-01-26 18:00:00', '049WB334277', 'TRSAMITY SHARE20261', 10000, 2026, '2026-01-28 03:47:12', 2, 1, 'Samity Share', 'payment_slip_40_1769572032_6788.jpeg', 'A', 'BP', 'Samity Share Fee Adjustment From This Slip'),
(313, 40, 'CPSS-00040', 'Project Share', 4, '2026-01-26 18:00:00', '049WB334277', 'TRPROJECT SHARE20261', 40000, 2026, '2026-01-28 03:48:39', 2, 1, 'Project Share', 'payment_slip_40_1769572119_9125.jpeg', 'A', 'BP', 'Project Share Fee Adjustment From this Slip'),
(314, 40, 'CPSS-00040', 'Monthly', 0, '2026-01-26 18:00:00', '049WB334277', 'TRJANUARY20261', 2000, 2026, '2026-01-28 03:50:46', 11, 1, 'january', 'payment_slip_40_1769572246_1794.jpeg', 'A', 'BP', 'January\'26 Monthly Deposit Adjustment From This Slip'),
(315, 93, 'CPSS-00093', 'Project Share', 4, '2026-01-26 18:00:00', 'AA2193704', 'TRPROJECT SHARE20262', 10000, 2026, '2026-01-28 09:18:41', 2, 2, 'Project Share', 'payment_slip_93_1769591921_6957.jpeg', 'A', 'BP', 'Adjustment From Cheque No.-AA 2193704'),
(316, 107, 'CPSS-00107', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE20263', 5000, 2026, '2026-01-28 09:20:12', 2, 3, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Cheque No.-AA 2193704'),
(317, 106, 'CPSS-00105', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE20264', 10000, 2026, '2026-01-28 09:20:51', 2, 4, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Cheque No.-AA 2193704'),
(318, 100, 'CPSS-00099', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE20265', 15000, 2026, '2026-01-28 09:22:23', 2, 5, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Cheque No.-AA 2193704'),
(319, 102, 'CPSS-00102', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE20266', 10000, 2026, '2026-01-28 09:22:51', 2, 6, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Cheque No.-AA 2193704'),
(320, 109, 'CPSS-00109', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE20267', 15000, 2026, '2026-01-28 09:25:21', 2, 7, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Cheque No.-AA 2193704'),
(321, 97, 'CPSS-00096', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE20268', 5000, 2026, '2026-01-28 09:26:06', 2, 8, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Cheque No.-AA 2193704'),
(322, 95, 'CPSS-00094', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE20269', 1000, 2026, '2026-01-28 09:27:29', 2, 9, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Cheque No.-AA 2193704'),
(323, 103, 'CPSS-00103', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202610', 10000, 2026, '2026-01-28 09:28:05', 2, 10, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Cheque No.-AA 2193704'),
(324, 85, 'CPSS-00083', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202611', 15000, 2026, '2026-01-28 09:28:37', 2, 11, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Cheque No.-AA 2193704'),
(325, 90, 'CPSS-00090', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202612', 15000, 2026, '2026-01-28 09:29:16', 2, 12, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Cheque No.-AA 2193704'),
(326, 110, 'CPSS-00110', 'Project Share', 4, '2025-12-17 18:00:00', '049WB326463', 'TRPROJECT SHARE202613', 15000, 2026, '2026-01-28 09:46:48', 2, 13, 'Project Share', 'payment_slip_110_1769593608_3070.jpeg', 'A', 'BP', 'Adjustment From Cheque AA 2193704( Bank Slip: 10000+ Cheque: 5000) = 15000'),
(327, 101, 'CPSS-00101', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202614', 10000, 2026, '2026-01-28 09:50:03', 2, 14, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Cheque No.-AA 2193704'),
(328, 95, 'CPSS-00094', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202615', 9000, 2026, '2026-01-28 09:50:56', 2, 15, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Cheque AA 2193704'),
(329, 107, 'CPSS-00107', 'Monthly', 0, NULL, '', 'TRJANUARY20261', 2000, 2026, '2026-01-28 09:56:33', 58, 1, 'january', NULL, 'A', 'AD', 'Adjustment From Cheque AA 2193704'),
(330, 106, 'CPSS-00105', 'Monthly', 0, NULL, '', 'TRJANUARY20261', 2000, 2026, '2026-01-28 09:58:34', 57, 1, 'january', NULL, 'A', 'AD', 'Adjustment From Cheque AA 2193704'),
(331, 98, 'CPSS-00098', 'Monthly', 0, '2026-01-26 18:00:00', 'AA 2193704', 'TRJANUARY20261', 2000, 2026, '2026-01-28 10:05:40', 51, 1, 'january', 'payment_slip_98_1769594740_3545.jpeg', 'A', 'BP', 'Adjustment From Cheque No.-AA 2193704'),
(332, 98, 'CPSS-00098', 'Monthly', 0, '2026-01-26 18:00:00', 'AA 2193704', 'TRFEBRUARY20261', 2000, 2026, '2026-01-28 10:05:40', 51, 1, 'february', 'payment_slip_98_1769594740_3545.jpeg', 'A', 'BP', 'Adjustment From Cheque No.-AA 2193704'),
(333, 109, 'CPSS-00109', 'Monthly', 0, '2026-01-26 18:00:00', '', 'TRJANUARY20261', 2000, 2026, '2026-01-28 12:12:51', 60, 1, 'january', NULL, 'A', 'AD', 'Adjustment From Cheque No.-AA 2193704'),
(334, 109, 'CPSS-00109', 'Monthly', 0, '2026-01-26 18:00:00', '', 'TRFEBRUARY20261', 2000, 2026, '2026-01-28 12:12:51', 60, 1, 'february', NULL, 'A', 'AD', 'Adjustment From Cheque No.-AA 2193704'),
(335, 109, 'CPSS-00109', 'Monthly', 0, '2026-01-26 18:00:00', '', 'TRMARCH20261', 2000, 2026, '2026-01-28 12:12:51', 60, 1, 'march', NULL, 'A', 'AD', 'Adjustment From Cheque No.-AA 2193704'),
(336, 101, 'CPSS-00101', 'Monthly', 0, NULL, '', 'TRJANUARY20261', 2000, 2026, '2026-01-28 12:20:18', 53, 1, 'january', NULL, 'A', 'AD', 'Adjustment From Cheque AA 2193704'),
(337, 97, 'CPSS-00096', 'Monthly', 0, NULL, '', 'TRJANUARY20261', 2000, 2026, '2026-01-28 12:23:31', 50, 1, 'january', NULL, 'A', 'AD', 'January\'2026 Monthly Fee Adjustment from Cheque No - AA 2193704'),
(338, 110, 'CPSS-00110', 'Monthly', 0, NULL, '', 'TRJANUARY20261', 2000, 2026, '2026-01-28 12:25:56', 61, 1, 'january', NULL, 'A', 'AD', 'January\'2026 Monthly Fee Adjustment from Cheque No- AA 2193704'),
(339, 104, 'CPSS-00104', 'Monthly', 0, '2026-01-28 18:00:00', '049WB334666', 'TRJANUARY20261', 2000, 2026, '2026-01-29 04:51:28', 56, 1, 'january', 'payment_slip_104_1769662288_6971.jpeg', 'A', 'BP', 'January-February\'26 Payment'),
(340, 104, 'CPSS-00104', 'Monthly', 0, '2026-01-28 18:00:00', '049WB334666', 'TRFEBRUARY20261', 2000, 2026, '2026-01-29 04:51:28', 56, 1, 'february', 'payment_slip_104_1769662288_6971.jpeg', 'A', 'BP', 'January-February\'26 Payment'),
(341, 104, 'CPSS-00104', 'Project Share', 4, '2026-01-28 18:00:00', '049WB334666', 'TRPROJECT SHARE202616', 10000, 2026, '2026-01-29 05:16:43', 65, 16, 'Project Share', 'payment_slip_104_1769663803_8059.jpeg', 'A', 'BP', 'Adjustment For this Slip'),
(342, 57, 'CPSS-00057', 'Monthly', 0, '2026-01-28 18:00:00', '260294481730', 'TRJANUARY20261', 2000, 2026, '2026-01-29 05:57:47', 23, 1, 'january', 'payment_slip_57_1769666267_9568.jpeg', 'A', 'BP', 'Paid 2000 for Jan 2026'),
(343, 34, 'CPSS-00032', 'admission', 0, '2025-11-12 18:00:00', '049WB320396', 'TRADMISSION20262', 1500, 2026, '2026-01-29 06:07:24', 65, 2, 'admission', 'payment_slip_34_1769666844_6835.jpeg', 'A', 'BP', 'Adjustment from this slip'),
(344, 34, 'CPSS-00032', 'Samity Share', 1, '2025-11-12 18:00:00', '049WB320396', 'TRSAMITY SHARE20262', 10000, 2026, '2026-01-29 06:08:25', 65, 2, 'Samity Share', 'payment_slip_34_1769666905_3355.jpeg', 'A', 'BP', 'Samity Share Fee Adjustment from this slip'),
(345, 76, 'CPSS-00076', 'Monthly', 0, '2026-01-24 18:00:00', '049WB333408', 'TRJANUARY20261', 2000, 2026, '2026-01-29 06:14:40', 38, 1, 'january', 'payment_slip_76_1769667280_4108.jpeg', 'A', 'BP', 'January\'26 Monthly Payment Done'),
(346, 76, 'CPSS-00076', 'Samity Share', 1, '2025-12-02 18:00:00', '049WB324109', 'TRSAMITY SHARE20263', 15000, 2026, '2026-01-29 06:21:59', 65, 3, 'Samity Share', 'payment_slip_76_1769667719_4833.jpeg', 'A', 'BP', 'Adjustment from this slip'),
(347, 123, 'CPSS-00113', 'admission', 0, '2026-01-28 18:00:00', '00334009987', 'TRADMISSION20263', 1500, 2026, '2026-01-29 07:57:36', 65, 3, 'admission', NULL, 'A', 'BP', 'Adjustment From this slip'),
(348, 123, 'CPSS-00113', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE20264', 10000, 2026, '2026-01-29 07:58:35', 65, 4, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From this slip 00334009987'),
(349, 123, 'CPSS-00113', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202617', 10000, 2026, '2026-01-29 08:02:45', 65, 17, 'Project Share', NULL, 'A', 'AD', 'Adjustment From this slip 00334009987'),
(350, 123, 'CPSS-00113', 'Monthly', 0, '2026-01-28 18:00:00', '00334009987', 'TRJANUARY20261', 2000, 2026, '2026-01-29 08:14:52', 63, 1, 'january', 'payment_slip_123_1769674492_2762.pdf', 'A', 'BP', 'Jan-Feb\'2026, Monthly Payment Done'),
(351, 123, 'CPSS-00113', 'Monthly', 0, '2026-01-28 18:00:00', '00334009987', 'TRFEBRUARY20261', 2000, 2026, '2026-01-29 08:14:52', 63, 1, 'february', 'payment_slip_123_1769674492_2762.pdf', 'A', 'BP', 'Jan-Feb\'2026, Monthly Payment Done'),
(352, 30, 'CPSS-00002', 'Monthly', 0, '2026-01-28 18:00:00', '049WB334726', 'TRJANUARY20261', 2000, 2026, '2026-01-30 05:27:21', 4, 1, 'january', 'payment_slip_30_1769750841_3994.jpeg', 'A', 'BP', 'January\' 26 monthly deposit'),
(353, 31, 'CPSS-00031', 'Monthly', 0, '2026-01-28 18:00:00', '049WB334726', 'TRJANUARY20261', 2000, 2026, '2026-01-30 05:29:26', 5, 1, 'january', 'payment_slip_31_1769750966_6126.jpeg', 'A', 'BP', 'January\'26 Monthly Deposit'),
(354, 56, 'CPSS-00056', 'Monthly', 0, '2026-01-29 18:00:00', 'vide chq# 5767B217', 'TRJANUARY20261', 2000, 2026, '2026-01-30 08:58:51', 22, 1, 'january', 'payment_slip_56_1769763531_7745.jpeg', 'A', 'BP', 'I forgot to take screen short of transaction, so attached the sms screen short'),
(355, 68, 'CPSS-00068', 'Monthly', 0, '2026-01-30 18:00:00', '049WB335181', 'TRJANUARY20261', 2000, 2026, '2026-01-31 10:47:19', 30, 1, 'january', 'payment_slip_68_1769856439_8120.jpeg', 'A', 'BP', 'January-26 amt.2000'),
(356, 34, 'CPSS-00032', 'Monthly', 0, '2026-01-29 18:00:00', '7D7D5B76', 'TRJANUARY20261', 2000, 2026, '2026-01-31 16:50:08', 7, 1, 'january', 'payment_slip_34_1769878208_3702.jpeg', 'A', 'BP', 'Md.Saiful Islam'),
(357, 68, 'CPSS-00068', 'admission', 0, NULL, '', 'TRADMISSION20264', 1500, 2026, '2026-02-01 05:46:03', 65, 4, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Due Amt=29000/-'),
(358, 68, 'CPSS-00068', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE20265', 10000, 2026, '2026-02-01 05:46:22', 65, 5, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Due Amt=29000/-'),
(359, 68, 'CPSS-00068', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202618', 15000, 2026, '2026-02-01 05:46:44', 65, 18, 'Project Share', NULL, 'A', 'AD', 'Adjustment From Coder Home Due Amt=29000/-'),
(360, 89, 'CPSS-00086', 'Monthly', 0, NULL, '', 'TRJANUARY20261', 2000, 2026, '2026-02-01 05:52:08', 45, 1, 'january', NULL, 'A', 'AD', 'Adjustment From Coder Home Due Amt=2500/-'),
(361, 71, 'CPSS-00071', 'Monthly', 0, NULL, '', 'TRJANUARY20261', 2000, 2026, '2026-02-01 05:52:33', 33, 1, 'january', NULL, 'A', 'AD', 'Adjustment From Coder Home Due Amt=2500/-'),
(362, 68, 'CPSS-00068', 'Monthly', 0, NULL, '', 'TRFEBRUARY20261', 2000, 2026, '2026-02-01 05:55:08', 30, 1, 'february', NULL, 'A', 'AD', 'Adjustment From Coder Home Due Amt=2500/-'),
(363, 51, 'CPSS-00051', 'Monthly', 0, '2026-01-31 18:00:00', '049WB335314', 'TRJANUARY20261', 2000, 2026, '2026-02-01 05:59:35', 19, 1, 'january', 'payment_slip_51_1769925575_4171.jpeg', 'A', 'BP', 'Adjustment from this slip'),
(364, 79, 'CPSS-00079', 'Monthly', 0, '2026-01-25 18:00:00', 'fte96f23f1e21b71', 'TRJANUARY20261', 2000, 2026, '2026-02-01 09:50:11', 41, 1, 'january', NULL, 'A', 'BP', ''),
(365, 130, 'CPSS-00124', 'admission', 0, NULL, '', 'TRADMISSION20265', 1500, 2026, '2026-02-02 14:11:44', 65, 5, 'admission', NULL, 'A', 'AD', 'Adjustment From CoderHome= 14000/-'),
(366, 130, 'CPSS-00124', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE20266', 10000, 2026, '2026-02-02 14:12:16', 65, 6, 'Samity Share', NULL, 'A', 'AD', 'Adjustment From CoderHome =14000/-'),
(367, 130, 'CPSS-00124', 'Monthly', 0, NULL, '', 'TRJANUARY20261', 2000, 2026, '2026-02-02 14:13:28', 66, 1, 'january', NULL, 'A', 'AD', 'Adjustment From CoderHome =14000/- Due of 500/- For February'),
(368, 123, 'CPSS-00113', 'Project Share', 4, '2026-02-02 18:00:00', 'Bank Online Slip', 'TRPROJECT SHARE202619', 10000, 2026, '2026-02-03 17:36:32', 65, 19, 'Project Share', 'payment_slip_123_1770140192_7966.jpeg', 'A', 'BP', 'New 2 Project Share Buy from CPSSL'),
(369, 36, 'CPSS-00036', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202620', 5000, 2026, '2026-02-03 17:46:04', 65, 20, 'Project Share', NULL, 'A', 'AD', 'Remove Data For Mismatch Again Adjustment Entry'),
(370, 45, 'CPSS-00043', 'Monthly', 0, '2026-02-02 18:00:00', '049WB336034', 'TRFEBRUARY20261', 2000, 2026, '2026-02-04 10:19:02', 14, 1, 'february', 'payment_slip_45_1770200342_9828.png', 'A', 'BP', 'Samity deposit for February 2026'),
(371, 52, 'CPSS-00052', 'Monthly', 0, '2026-02-04 18:00:00', '52CC75E4', 'TRFEBRUARY20261', 2000, 2026, '2026-02-05 03:18:55', 20, 1, 'february', 'payment_slip_52_1770261535_1771.jpeg', 'A', 'BP', 'CPSS-00052 (Feb\'26) Monthly Deposit'),
(372, 131, 'CPSS-00131', 'admission', 0, NULL, '', 'TRADMISSION20266', 1500, 2026, '2026-02-05 03:30:41', 65, 6, 'admission', NULL, 'A', 'AD', 'Adjustment From Coder Home Due Amt=9000/-'),
(373, 50, 'CPSS-00049', 'Monthly', 0, '2026-01-31 18:00:00', '049WB335248', 'TRJANUARY20261', 2000, 2026, '2026-02-05 03:35:33', 18, 1, 'january', 'payment_slip_50_1770262533_7319.jpeg', 'A', 'BP', 'CPSS-00049 (Jan\'26) Monthly Deposit'),
(374, 50, 'CPSS-00049', 'admission', 0, '2026-01-31 18:00:00', '049WB335247', 'TRADMISSION20267', 1500, 2026, '2026-02-05 03:49:38', 65, 7, 'admission', 'payment_slip_50_1770263378_3277.jpeg', 'A', 'BP', 'CPSS-00049 (Admission Fee)'),
(375, 50, 'CPSS-00049', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE20267', 10000, 2026, '2026-02-05 03:50:29', 65, 7, 'Samity Share', NULL, 'A', 'AD', 'CPSS-00049 (Samity Share Fee) - 049WB335247'),
(376, 100, 'CPSS-00099', 'Monthly', 0, '2026-02-07 18:00:00', '049WB336659', 'TRJANUARY20261', 2000, 2026, '2026-02-08 07:34:44', 52, 1, 'january', 'payment_slip_100_1770536084_6889.jpg', 'A', 'BP', ''),
(377, 134, 'CPSS-00133', 'admission', 0, '2026-02-08 18:00:00', 'Bank Slip', 'TRADMISSION20268', 1500, 2026, '2026-02-09 11:09:03', 65, 8, 'admission', 'payment_slip_134_1770635343_3508.jpeg', 'A', 'BP', 'CPSS-00133, Admission Fee From this Bank Slip'),
(378, 134, 'CPSS-00133', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE20268', 10000, 2026, '2026-02-09 11:10:03', 65, 8, 'Samity Share', NULL, 'A', 'AD', 'CPSS-00133, Samity Share From Admission Fee Bank Slip'),
(379, 134, 'CPSS-00133', 'Monthly', 0, '2026-02-08 18:00:00', 'Bank Slip', 'TRJANUARY20261', 2000, 2026, '2026-02-09 11:12:19', 69, 1, 'january', 'payment_slip_134_1770635539_6024.jpeg', 'A', 'BP', 'CPSS-00133, Jan\'26 Monthly Deposit'),
(380, 134, 'CPSS-00133', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202621', 40000, 2026, '2026-02-09 17:12:36', 65, 21, 'Project Share', NULL, 'A', 'AD', 'CPSS-00133, Project Share From Admission Fee Bank Slip'),
(381, 132, 'CPSS-00132', 'admission', 0, '2026-02-09 18:00:00', '79C6ABCA', 'TRADMISSION20269', 1500, 2026, '2026-02-10 05:44:50', 65, 9, 'admission', 'payment_slip_132_1770702290_3497.jpeg', 'A', 'BP', 'CPSS-00132, Admission Fee From This Slip'),
(382, 132, 'CPSS-00132', 'Samity Share', 1, NULL, '', 'TRSAMITY SHARE20269', 10000, 2026, '2026-02-10 05:45:30', 65, 9, 'Samity Share', NULL, 'A', 'AD', 'CPSS-00132, Samity Share Fee From Admission Fee Slip'),
(383, 132, 'CPSS-00132', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202622', 15000, 2026, '2026-02-10 05:46:15', 65, 22, 'Project Share', NULL, 'A', 'AD', 'CPSS-00132, Project Share Fee From Admission Fee Slip'),
(384, 132, 'CPSS-00132', 'Monthly', 0, '2026-02-09 18:00:00', '79C6ABCA', 'TRJANUARY20261', 2000, 2026, '2026-02-10 05:47:56', 68, 1, 'january', 'payment_slip_132_1770702476_9757.jpeg', 'A', 'BP', 'CPSS-00132, Jan\'26-Feb\'26, Monthly Deposit'),
(385, 132, 'CPSS-00132', 'Monthly', 0, '2026-02-09 18:00:00', '79C6ABCA', 'TRFEBRUARY20261', 2000, 2026, '2026-02-10 05:47:56', 68, 1, 'february', 'payment_slip_132_1770702476_9757.jpeg', 'A', 'BP', 'CPSS-00132, Jan\'26-Feb\'26, Monthly Deposit');
INSERT INTO `member_payments` (`id`, `member_id`, `member_code`, `payment_method`, `project_id`, `bank_pay_date`, `bank_trans_no`, `trans_no`, `amount`, `payment_year`, `created_at`, `created_by`, `serial_no`, `for_fees`, `payment_slip`, `status`, `pay_mode`, `remarks`) VALUES
(386, 63, 'CPSS-00063', 'Project Share', 4, '2026-02-09 18:00:00', 'E6038B95', 'TRPROJECT SHARE202623', 5000, 2026, '2026-02-10 06:14:55', 65, 23, 'Project Share', 'payment_slip_63_1770704095_7550.jpeg', 'A', 'BP', 'CPSS-00063, Project Share 1'),
(387, 63, 'CPSS-00063', 'Monthly', 0, '2026-02-09 18:00:00', 'E6038B95', 'TRFEBRUARY20261', 2000, 2026, '2026-02-10 06:15:53', 26, 1, 'february', 'payment_slip_63_1770704153_7043.jpeg', 'A', 'BP', 'CPSS-00063, Feb\'26 Monthly Deposit'),
(388, 57, 'CPSS-00057', 'Project Share', 4, NULL, '', 'TRPROJECT SHARE202624', 5000, 2026, '2026-02-10 16:52:42', 65, 24, 'Project Share', NULL, 'A', 'AD', 'CPSS-00057, Adjustment From Coder Home Due Amount'),
(389, 73, 'CPSS-00073', 'Monthly', 0, '2026-02-14 18:00:00', '04934007568', 'TRFEBRUARY20261', 2000, 2026, '2026-02-15 04:57:30', 35, 1, 'february', 'payment_slip_73_1771131450_6036.jpeg', 'I', 'BP', 'Old adjustment 500 + bank pay 1500');

-- --------------------------------------------------------

--
-- Table structure for table `member_project`
--

CREATE TABLE `member_project` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `member_code` varchar(50) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `project_share` int(11) NOT NULL,
  `share_amount` decimal(12,2) NOT NULL,
  `paid_amount` int(11) DEFAULT 0,
  `sundry_amount` int(11) DEFAULT 0,
  `status` varchar(2) DEFAULT 'I',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member_project`
--

INSERT INTO `member_project` (`id`, `member_id`, `member_code`, `project_id`, `project_share`, `share_amount`, `paid_amount`, `sundry_amount`, `status`, `created_at`) VALUES
(40, 1, 'CPSS-00001', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 00:33:06'),
(41, 45, 'CPSS-00043', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 01:15:02'),
(42, 45, 'CPSS-00043', 4, 2, 10000.00, 10000, 0, 'A', '2025-12-06 01:17:19'),
(43, 30, 'CPSS-00002', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 13:29:03'),
(44, 31, 'CPSS-00031', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 13:30:36'),
(45, 36, 'CPSS-00036', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 13:33:54'),
(47, 39, 'CPSS-00037', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 13:37:21'),
(48, 39, 'CPSS-00037', 4, 2, 10000.00, 10000, 0, 'A', '2025-12-06 13:38:02'),
(49, 41, 'CPSS-00041', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 13:40:53'),
(50, 52, 'CPSS-00052', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 13:52:31'),
(51, 52, 'CPSS-00052', 4, 3, 15000.00, 15000, 0, 'A', '2025-12-06 13:57:58'),
(52, 42, 'CPSS-00042', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 14:07:04'),
(53, 47, 'CPSS-00047', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 14:09:09'),
(54, 47, 'CPSS-00047', 4, 39, 195000.00, 195000, 0, 'A', '2025-12-06 14:09:33'),
(55, 48, 'CPSS-00048', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 14:11:12'),
(56, 48, 'CPSS-00048', 4, 18, 90000.00, 90000, 0, 'A', '2025-12-06 14:13:23'),
(57, 51, 'CPSS-00051', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 14:16:08'),
(58, 62, 'CPSS-00060', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 14:18:41'),
(59, 62, 'CPSS-00060', 4, 23, 115000.00, 115000, 0, 'A', '2025-12-06 14:21:11'),
(60, 56, 'CPSS-00056', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 14:24:48'),
(61, 56, 'CPSS-00056', 4, 3, 15000.00, 15000, 0, 'A', '2025-12-06 14:27:43'),
(62, 57, 'CPSS-00057', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:05:08'),
(63, 57, 'CPSS-00057', 4, 9, 45000.00, 45000, 0, 'A', '2025-12-06 18:05:43'),
(64, 59, 'CPSS-00058', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:08:00'),
(65, 63, 'CPSS-00063', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:10:31'),
(66, 65, 'CPSS-00064', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:13:07'),
(67, 65, 'CPSS-00064', 4, 18, 90000.00, 90000, 0, 'A', '2025-12-06 18:13:59'),
(68, 66, 'CPSS-00066', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:16:50'),
(69, 66, 'CPSS-00066', 4, 1, 5000.00, 5000, 0, 'A', '2025-12-06 18:17:05'),
(70, 67, 'CPSS-00067', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:17:37'),
(71, 67, 'CPSS-00067', 4, 2, 10000.00, 10000, 0, 'A', '2025-12-06 18:18:01'),
(72, 69, 'CPSS-00069', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:19:39'),
(73, 69, 'CPSS-00069', 4, 7, 35000.00, 35000, 0, 'A', '2025-12-06 18:22:09'),
(74, 70, 'CPSS-00070', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:22:52'),
(75, 70, 'CPSS-00070', 4, 44, 220000.00, 220000, 0, 'A', '2025-12-06 18:23:20'),
(76, 71, 'CPSS-00071', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:26:29'),
(77, 71, 'CPSS-00071', 4, 3, 15000.00, 15000, 0, 'A', '2025-12-06 18:28:39'),
(78, 72, 'CPSS-00072', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:30:48'),
(79, 72, 'CPSS-00072', 4, 8, 40000.00, 40000, 0, 'A', '2025-12-06 18:34:09'),
(80, 73, 'CPSS-00073', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:35:41'),
(81, 73, 'CPSS-00073', 4, 13, 65000.00, 65000, 0, 'A', '2025-12-06 18:35:58'),
(82, 74, 'CPSS-00074', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:37:11'),
(83, 74, 'CPSS-00074', 4, 4, 20000.00, 20000, 0, 'A', '2025-12-06 18:37:33'),
(84, 76, 'CPSS-00076', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:38:42'),
(85, 77, 'CPSS-00077', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:39:50'),
(86, 77, 'CPSS-00077', 4, 3, 15000.00, 15000, 0, 'A', '2025-12-06 18:40:11'),
(87, 78, 'CPSS-00078', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:42:57'),
(88, 78, 'CPSS-00078', 4, 3, 15000.00, 15000, 0, 'A', '2025-12-06 18:43:32'),
(89, 79, 'CPSS-00079', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:46:45'),
(90, 79, 'CPSS-00079', 4, 5, 25000.00, 25000, 0, 'A', '2025-12-06 18:48:14'),
(91, 85, 'CPSS-00083', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:50:13'),
(92, 85, 'CPSS-00083', 4, 4, 20000.00, 20000, 0, 'A', '2025-12-06 18:50:33'),
(93, 89, 'CPSS-00086', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:51:47'),
(94, 89, 'CPSS-00086', 4, 5, 25000.00, 25000, 0, 'A', '2025-12-06 18:52:07'),
(95, 90, 'CPSS-00090', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:54:23'),
(96, 90, 'CPSS-00090', 4, 4, 20000.00, 20000, 0, 'A', '2025-12-06 18:55:29'),
(97, 92, 'CPSS-00091', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:55:56'),
(98, 93, 'CPSS-00093', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:56:39'),
(99, 93, 'CPSS-00093', 4, 3, 15000.00, 15000, 0, 'A', '2025-12-06 18:56:55'),
(100, 95, 'CPSS-00094', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:58:20'),
(101, 95, 'CPSS-00094', 4, 3, 15000.00, 15000, 0, 'A', '2025-12-06 18:58:39'),
(102, 97, 'CPSS-00096', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 18:59:11'),
(103, 97, 'CPSS-00096', 4, 2, 10000.00, 10000, 0, 'A', '2025-12-06 18:59:30'),
(104, 98, 'CPSS-00098', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 19:00:00'),
(105, 98, 'CPSS-00098', 4, 1, 5000.00, 5000, 0, 'A', '2025-12-06 19:00:14'),
(106, 100, 'CPSS-00099', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 19:01:25'),
(107, 100, 'CPSS-00099', 4, 4, 20000.00, 20000, 0, 'A', '2025-12-06 19:02:06'),
(108, 101, 'CPSS-00101', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 19:02:41'),
(109, 101, 'CPSS-00101', 4, 3, 15000.00, 15000, 0, 'A', '2025-12-06 19:03:01'),
(110, 102, 'CPSS-00102', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 19:03:53'),
(111, 102, 'CPSS-00102', 4, 3, 15000.00, 15000, 0, 'A', '2025-12-06 19:04:15'),
(112, 103, 'CPSS-00103', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 19:04:46'),
(113, 103, 'CPSS-00103', 4, 3, 15000.00, 15000, 0, 'A', '2025-12-06 19:05:02'),
(114, 104, 'CPSS-00104', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 19:06:09'),
(115, 104, 'CPSS-00104', 4, 3, 15000.00, 15000, 0, 'A', '2025-12-06 19:06:26'),
(116, 106, 'CPSS-00105', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 19:07:09'),
(117, 106, 'CPSS-00105', 4, 3, 15000.00, 15000, 0, 'A', '2025-12-06 19:07:23'),
(118, 107, 'CPSS-00107', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 19:08:00'),
(119, 108, 'CPSS-00108', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 19:08:34'),
(120, 109, 'CPSS-00109', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 19:09:06'),
(121, 109, 'CPSS-00109', 4, 4, 20000.00, 20000, 0, 'A', '2025-12-06 19:09:24'),
(122, 110, 'CPSS-00110', 1, 0, 0.00, 0, 0, 'A', '2025-12-06 19:09:49'),
(123, 110, 'CPSS-00110', 4, 4, 20000.00, 20000, 0, 'A', '2025-12-06 19:10:04'),
(124, 107, 'CPSS-00107', 4, 2, 10000.00, 10000, 0, 'A', '2025-12-07 10:22:24'),
(125, 35, 'CPSS-00035', 1, 0, 0.00, 0, 0, 'A', '2025-12-08 18:02:34'),
(126, 112, 'CPSS-00111', 1, 0, 0.00, 0, 0, 'A', '2025-12-08 18:03:24'),
(127, 123, 'CPSS-00113', 1, 0, 0.00, 0, 0, 'A', '2026-01-04 15:55:37'),
(128, 40, 'CPSS-00040', 1, 0, 0.00, 0, 0, 'I', '2026-01-28 09:47:12'),
(129, 40, 'CPSS-00040', 4, 8, 40000.00, 40000, 0, 'A', '2026-01-28 09:48:39'),
(130, 34, 'CPSS-00032', 1, 0, 0.00, 0, 0, 'A', '2026-01-29 12:08:25'),
(131, 123, 'CPSS-00113', 4, 4, 20000.00, 20000, 0, 'A', '2026-01-29 14:02:45'),
(132, 68, 'CPSS-00068', 1, 0, 0.00, 0, 0, 'I', '2026-02-01 11:46:22'),
(133, 68, 'CPSS-00068', 4, 3, 15000.00, 15000, 0, 'A', '2026-02-01 11:46:44'),
(134, 130, 'CPSS-00124', 1, 0, 0.00, 0, 0, 'A', '2026-02-02 20:09:57'),
(135, 131, 'CPSS-00131', 1, 0, 0.00, 0, 0, 'I', '2026-02-02 20:24:49'),
(136, 36, 'CPSS-00036', 4, 1, 5000.00, 5000, 0, 'A', '2026-02-03 23:32:27'),
(137, 50, 'CPSS-00049', 1, 0, 0.00, 0, 0, 'A', '2026-02-05 09:50:29'),
(138, 132, 'CPSS-00132', 1, 0, 0.00, 0, 0, 'I', '2026-02-08 12:32:37'),
(139, 134, 'CPSS-00133', 1, 0, 0.00, 0, 0, 'A', '2026-02-09 17:06:40'),
(140, 134, 'CPSS-00133', 4, 8, 40000.00, 40000, 0, 'A', '2026-02-09 23:10:39'),
(141, 132, 'CPSS-00132', 4, 3, 15000.00, 15000, 0, 'A', '2026-02-10 11:46:15'),
(142, 63, 'CPSS-00063', 4, 1, 5000.00, 5000, 0, 'A', '2026-02-10 12:00:19');

-- --------------------------------------------------------

--
-- Table structure for table `member_share`
--

CREATE TABLE `member_share` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `member_code` varchar(50) NOT NULL,
  `no_share` int(11) NOT NULL,
  `samity_share` int(11) DEFAULT 0,
  `samity_share_amt` int(11) DEFAULT 0,
  `admission_fee` int(11) DEFAULT 0,
  `idcard_fee` int(11) DEFAULT 0,
  `passbook_fee` int(11) DEFAULT 0,
  `softuses_fee` int(11) DEFAULT 0,
  `sms_fee` int(11) DEFAULT 0,
  `office_rent` int(11) DEFAULT 0,
  `office_staff` int(11) DEFAULT 0,
  `other_fee` int(11) DEFAULT 0,
  `for_install` int(11) DEFAULT 0,
  `project_id` int(11) DEFAULT 0,
  `extra_share` int(11) DEFAULT 0,
  `install_advance` int(11) DEFAULT 0,
  `sundry_samity_share` int(11) DEFAULT 0,
  `share_amount` int(11) DEFAULT 0,
  `late_assign` varchar(3) NOT NULL DEFAULT 'A',
  `late_fee` int(11) NOT NULL DEFAULT 0,
  `late_cause` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member_share`
--

INSERT INTO `member_share` (`id`, `member_id`, `member_code`, `no_share`, `samity_share`, `samity_share_amt`, `admission_fee`, `idcard_fee`, `passbook_fee`, `softuses_fee`, `sms_fee`, `office_rent`, `office_staff`, `other_fee`, `for_install`, `project_id`, `extra_share`, `install_advance`, `sundry_samity_share`, `share_amount`, `late_assign`, `late_fee`, `late_cause`, `created_at`) VALUES
(1, 1, 'CPSS-00001', 25, 25, 125000, 1500, 150, 200, 400, 100, 300, 200, 750, 11400, 4, 0, 0, 0, 122500, 'I', 0, 'Two Months', '2026-01-19 04:12:19'),
(19, 30, 'CPSS-00002', 13, 13, 65000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 4, 0, 0, 0, 490000, 'I', 0, NULL, '2026-01-29 23:27:21'),
(20, 31, 'CPSS-00031', 2, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 4, 0, 0, 0, 9800, 'I', 0, NULL, '2026-01-29 23:29:26'),
(22, 34, 'CPSS-00032', 2, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 4, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-31 10:50:08'),
(23, 35, 'CPSS-00035', 2, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 150, 0, 4, 0, 0, 0, 0, 'I', 0, NULL, NULL),
(24, 36, 'CPSS-00036', 3, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 4, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-15 04:50:14'),
(25, 39, 'CPSS-00037', 4, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 4, 0, 0, 0, 19600, 'I', 0, NULL, '2026-01-18 01:05:43'),
(26, 40, 'CPSS-00040', 10, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 4, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-27 21:50:46'),
(27, 41, 'CPSS-00041', 2, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 4, 0, 0, 0, 9800, 'I', 0, NULL, '2026-01-15 05:00:32'),
(28, 42, 'CPSS-00042', 32, 32, 160000, 1500, 150, 200, 400, 100, 300, 200, 150, 0, 4, 0, 0, 0, 0, 'I', 0, NULL, NULL),
(29, 45, 'CPSS-00043', 4, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 350, 3800, 4, 0, 0, 0, 0, 'I', 0, NULL, '2026-02-04 04:19:02'),
(30, 46, 'CPSS-00046', 2, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 0, 0, 10000, 0, 'I', 0, NULL, NULL),
(31, 47, 'CPSS-00047', 41, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 4, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-27 01:59:49'),
(32, 48, 'CPSS-00048', 20, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 4, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-18 22:36:57'),
(33, 50, 'CPSS-00049', 2, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 150, 1900, 4, 0, 0, 0, 0, 'I', 0, NULL, '2026-02-04 21:35:33'),
(34, 51, 'CPSS-00051', 2, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 4, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-31 23:59:35'),
(35, 52, 'CPSS-00052', 5, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 350, 3800, 4, 0, 0, 0, 24500, 'I', 0, NULL, '2026-02-04 21:18:55'),
(36, 55, 'CPSS-00053', 4, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 2, 0, 10000, 0, 'I', 0, NULL, NULL),
(37, 56, 'CPSS-00056', 5, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 4, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-30 02:58:51'),
(38, 57, 'CPSS-00057', 11, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 4, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-28 23:57:47'),
(39, 59, 'CPSS-00058', 2, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 4, 0, 0, 0, 9800, 'I', 0, NULL, '2026-01-25 23:42:05'),
(40, 62, 'CPSS-00060', 25, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 4, 0, 0, 0, 122500, 'I', 0, NULL, '2026-01-15 11:47:52'),
(41, 63, 'CPSS-00063', 3, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 350, 3800, 4, 0, 0, 0, 9800, 'I', 0, NULL, '2026-02-10 00:15:53'),
(42, 65, 'CPSS-00064', 20, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 150, 0, 4, 0, 0, 0, 0, 'I', 0, NULL, '2025-11-08 10:08:49'),
(43, 66, 'CPSS-00066', 3, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 4, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-25 09:34:31'),
(44, 67, 'CPSS-00067', 4, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 150, 0, 4, 0, 0, 0, 0, 'I', 0, NULL, '2025-11-08 10:27:48'),
(45, 68, 'CPSS-00068', 5, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 3800, 4, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-31 23:55:08'),
(46, 69, 'CPSS-00069', 9, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 150, 0, 4, 0, 0, 0, 0, 'I', 0, NULL, '2025-11-08 10:47:57'),
(47, 70, 'CPSS-00070', 46, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 150, 0, 4, 0, 0, 0, 0, 'I', 0, NULL, '2025-11-08 10:59:16'),
(48, 71, 'CPSS-00071', 5, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 4, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-31 23:52:33'),
(49, 72, 'CPSS-00072', 10, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 350, 3800, 4, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-15 11:45:45'),
(50, 73, 'CPSS-00073', 15, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 350, 3800, 4, 0, 0, 0, 0, 'I', 0, NULL, '2026-02-14 22:57:30'),
(51, 74, 'CPSS-00074', 6, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 4, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-25 03:15:25'),
(52, 75, 'CPSS-00075', 2, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 0, 0, 10000, 0, 'I', 0, NULL, '2025-11-08 17:45:17'),
(53, 76, 'CPSS-00076', 5, 5, 25000, 1500, 150, 200, 400, 100, 300, 200, 450, 5700, 4, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-29 00:14:40'),
(54, 77, 'CPSS-00077', 5, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 150, 0, 4, 0, 0, 0, 0, 'I', 0, NULL, '2025-11-09 03:52:07'),
(55, 78, 'CPSS-00078', 5, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 4, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-25 01:48:26'),
(56, 79, 'CPSS-00079', 7, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 4, 0, 0, 0, 34300, 'I', 0, NULL, '2026-02-01 03:50:11'),
(57, 82, 'CPSS-00080', 2, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 0, 0, 10000, 0, 'I', 0, NULL, '2025-11-14 16:37:17'),
(58, 85, 'CPSS-00083', 6, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 150, 0, 4, 0, 0, 0, 0, 'I', 0, NULL, '2025-11-19 11:11:52'),
(62, 89, 'CPSS-00086', 7, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 0, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-31 23:52:08'),
(63, 90, 'CPSS-00090', 6, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 150, 0, 0, 0, 0, 0, 0, 'I', 0, NULL, '2025-11-25 11:51:19'),
(64, 92, 'CPSS-00091', 2, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 0, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-27 21:39:28'),
(65, 93, 'CPSS-00093', 5, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 150, 0, 0, 0, 0, 0, 0, 'I', 0, NULL, '2025-11-27 06:41:40'),
(66, 95, 'CPSS-00094', 5, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 150, 0, 0, 0, 0, 0, 0, 'I', 0, NULL, '2025-11-27 17:09:16'),
(67, 97, 'CPSS-00096', 4, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 0, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-28 06:23:31'),
(68, 98, 'CPSS-00098', 4, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 350, 3800, 0, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-28 04:05:40'),
(69, 100, 'CPSS-00099', 6, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 0, 0, 0, 0, 0, 'I', 0, NULL, '2026-02-08 01:34:44'),
(70, 101, 'CPSS-00101', 5, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 0, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-28 06:20:18'),
(71, 102, 'CPSS-00102', 5, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 150, 0, 0, 0, 0, 0, 0, 'I', 0, NULL, '2025-11-28 16:54:13'),
(72, 103, 'CPSS-00103', 5, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 150, 0, 0, 0, 0, 0, 0, 'I', 0, NULL, '2025-11-29 11:53:34'),
(73, 104, 'CPSS-00104', 5, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 350, 3800, 0, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-28 22:51:28'),
(74, 106, 'CPSS-00105', 5, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 0, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-28 03:58:34'),
(75, 107, 'CPSS-00107', 4, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 0, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-28 03:56:33'),
(76, 108, 'CPSS-00108', 2, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 0, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-25 01:40:23'),
(77, 109, 'CPSS-00109', 6, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 450, 5700, 0, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-28 06:12:51'),
(78, 110, 'CPSS-00110', 6, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 0, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-28 06:25:56'),
(79, 112, 'CPSS-00111', 2, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 150, 0, 0, 0, 0, 0, 0, 'I', 0, NULL, '2025-12-04 03:40:18'),
(80, 123, 'CPSS-00113', 6, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 350, 3800, 0, 0, 0, 0, 0, 'I', 0, NULL, '2026-01-29 02:14:52'),
(81, 130, 'CPSS-00124', 2, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 0, 0, 0, 0, 0, 'I', 0, '', '2026-02-02 08:13:28'),
(82, 131, 'CPSS-00131', 2, 2, 0, 1500, 150, 200, 400, 100, 300, 200, 150, 0, 0, 0, 0, 10000, 0, 'I', 0, '', '2026-02-02 14:24:49'),
(83, 132, 'CPSS-00132', 5, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 350, 3800, 0, 0, 0, 0, 0, 'I', 0, '', '2026-02-09 23:47:56'),
(84, 134, 'CPSS-00133', 10, 2, 10000, 1500, 150, 200, 400, 100, 300, 200, 250, 1900, 0, 0, 0, 0, 0, 'I', 0, '', '2026-02-09 05:12:19');

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `id` int(11) NOT NULL,
  `project_name_bn` text NOT NULL,
  `project_name_en` text NOT NULL,
  `about_project` text NOT NULL,
  `project_value` int(11) DEFAULT NULL,
  `project_share` int(11) DEFAULT NULL,
  `per_share_value` int(11) DEFAULT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `member_entry_last_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`id`, `project_name_bn`, `project_name_en`, `about_project`, `project_value`, `project_share`, `per_share_value`, `start_date`, `end_date`, `member_entry_last_date`) VALUES
(4, 'ধলেশ্বরী প্রকল্প-১', 'Dhaleshwari Project-1', '<p>ধলেশ্বরী প্রকল্প এর ৬ শতাংশ জমি ক্রয়</p>', 2500000, 500, 5000, '2025-10-01 04:08:27', '2030-09-30 04:08:27', '2025-11-30 04:08:27');

-- --------------------------------------------------------

--
-- Table structure for table `project_share`
--

CREATE TABLE `project_share` (
  `id` int(11) NOT NULL,
  `member_project_id` int(11) DEFAULT NULL,
  `member_id` int(11) NOT NULL,
  `member_code` varchar(50) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `share_id` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_share`
--

INSERT INTO `project_share` (`id`, `member_project_id`, `member_id`, `member_code`, `project_id`, `share_id`, `created_at`) VALUES
(230, 40, 1, 'CPSS-00001', 1, 'samity1401001', '2025-12-05 18:33:06'),
(231, 40, 1, 'CPSS-00001', 1, 'samity1401002', '2025-12-05 18:33:06'),
(232, 40, 1, 'CPSS-00001', 1, 'samity1401003', '2025-12-05 18:33:06'),
(233, 40, 1, 'CPSS-00001', 1, 'samity1401004', '2025-12-05 18:33:06'),
(234, 40, 1, 'CPSS-00001', 1, 'samity1401005', '2025-12-05 18:33:06'),
(235, 40, 1, 'CPSS-00001', 1, 'samity1401006', '2025-12-05 18:33:06'),
(236, 40, 1, 'CPSS-00001', 1, 'samity1401007', '2025-12-05 18:33:06'),
(237, 40, 1, 'CPSS-00001', 1, 'samity1401008', '2025-12-05 18:33:06'),
(238, 40, 1, 'CPSS-00001', 1, 'samity1401009', '2025-12-05 18:33:06'),
(239, 40, 1, 'CPSS-00001', 1, 'samity1401010', '2025-12-05 18:33:06'),
(240, 40, 1, 'CPSS-00001', 1, 'samity1401011', '2025-12-05 18:33:06'),
(241, 40, 1, 'CPSS-00001', 1, 'samity1401012', '2025-12-05 18:33:06'),
(242, 40, 1, 'CPSS-00001', 1, 'samity1401013', '2025-12-05 18:33:06'),
(243, 40, 1, 'CPSS-00001', 1, 'samity1401014', '2025-12-05 18:33:06'),
(244, 40, 1, 'CPSS-00001', 1, 'samity1401015', '2025-12-05 18:33:06'),
(245, 40, 1, 'CPSS-00001', 1, 'samity1401016', '2025-12-05 18:33:06'),
(246, 40, 1, 'CPSS-00001', 1, 'samity1401017', '2025-12-05 18:33:06'),
(247, 40, 1, 'CPSS-00001', 1, 'samity1401018', '2025-12-05 18:33:06'),
(248, 40, 1, 'CPSS-00001', 1, 'samity1401019', '2025-12-05 18:33:06'),
(249, 40, 1, 'CPSS-00001', 1, 'samity1401020', '2025-12-05 18:33:06'),
(250, 40, 1, 'CPSS-00001', 1, 'samity1401021', '2025-12-05 18:33:06'),
(251, 40, 1, 'CPSS-00001', 1, 'samity1401022', '2025-12-05 18:33:06'),
(252, 40, 1, 'CPSS-00001', 1, 'samity1401023', '2025-12-05 18:33:06'),
(253, 40, 1, 'CPSS-00001', 1, 'samity1401024', '2025-12-05 18:33:06'),
(254, 40, 1, 'CPSS-00001', 1, 'samity1401025', '2025-12-05 18:33:06'),
(255, 41, 45, 'CPSS-00043', 1, 'samity45411001', '2025-12-05 19:15:02'),
(256, 41, 45, 'CPSS-00043', 1, 'samity45411002', '2025-12-05 19:15:02'),
(257, 42, 45, 'CPSS-00043', 4, 'share45424001', '2025-12-05 19:17:19'),
(258, 42, 45, 'CPSS-00043', 4, 'share45424002', '2025-12-05 19:17:19'),
(259, 43, 30, 'CPSS-00002', 1, 'samity30431001', '2025-12-06 07:29:03'),
(260, 43, 30, 'CPSS-00002', 1, 'samity30431002', '2025-12-06 07:29:03'),
(261, 43, 30, 'CPSS-00002', 1, 'samity30431003', '2025-12-06 07:29:03'),
(262, 43, 30, 'CPSS-00002', 1, 'samity30431004', '2025-12-06 07:29:03'),
(263, 43, 30, 'CPSS-00002', 1, 'samity30431005', '2025-12-06 07:29:03'),
(264, 43, 30, 'CPSS-00002', 1, 'samity30431006', '2025-12-06 07:29:03'),
(265, 43, 30, 'CPSS-00002', 1, 'samity30431007', '2025-12-06 07:29:03'),
(266, 43, 30, 'CPSS-00002', 1, 'samity30431008', '2025-12-06 07:29:03'),
(267, 43, 30, 'CPSS-00002', 1, 'samity30431009', '2025-12-06 07:29:03'),
(268, 43, 30, 'CPSS-00002', 1, 'samity30431010', '2025-12-06 07:29:03'),
(269, 43, 30, 'CPSS-00002', 1, 'samity30431011', '2025-12-06 07:29:03'),
(270, 43, 30, 'CPSS-00002', 1, 'samity30431012', '2025-12-06 07:29:03'),
(271, 43, 30, 'CPSS-00002', 1, 'samity30431013', '2025-12-06 07:29:03'),
(272, 44, 31, 'CPSS-00031', 1, 'samity31441001', '2025-12-06 07:30:36'),
(273, 44, 31, 'CPSS-00031', 1, 'samity31441002', '2025-12-06 07:30:36'),
(274, 45, 36, 'CPSS-00036', 1, 'samity36451001', '2025-12-06 07:33:54'),
(275, 45, 36, 'CPSS-00036', 1, 'samity36451002', '2025-12-06 07:33:54'),
(277, 47, 39, 'CPSS-00037', 1, 'samity39471001', '2025-12-06 07:37:21'),
(278, 47, 39, 'CPSS-00037', 1, 'samity39471002', '2025-12-06 07:37:21'),
(279, 48, 39, 'CPSS-00037', 4, 'share39484001', '2025-12-06 07:38:02'),
(280, 48, 39, 'CPSS-00037', 4, 'share39484002', '2025-12-06 07:38:02'),
(281, 49, 41, 'CPSS-00041', 1, 'samity41491001', '2025-12-06 07:40:53'),
(282, 49, 41, 'CPSS-00041', 1, 'samity41491002', '2025-12-06 07:40:53'),
(283, 50, 52, 'CPSS-00052', 1, 'samity52501001', '2025-12-06 07:52:31'),
(284, 50, 52, 'CPSS-00052', 1, 'samity52501002', '2025-12-06 07:52:31'),
(285, 51, 52, 'CPSS-00052', 4, 'share52514001', '2025-12-06 07:57:58'),
(286, 51, 52, 'CPSS-00052', 4, 'share52514002', '2025-12-06 07:57:58'),
(287, 51, 52, 'CPSS-00052', 4, 'share52514003', '2025-12-06 07:57:58'),
(288, 52, 42, 'CPSS-00042', 1, 'samity42521001', '2025-12-06 08:07:04'),
(289, 52, 42, 'CPSS-00042', 1, 'samity42521002', '2025-12-06 08:07:04'),
(290, 52, 42, 'CPSS-00042', 1, 'samity42521003', '2025-12-06 08:07:04'),
(291, 52, 42, 'CPSS-00042', 1, 'samity42521004', '2025-12-06 08:07:04'),
(292, 52, 42, 'CPSS-00042', 1, 'samity42521005', '2025-12-06 08:07:04'),
(293, 52, 42, 'CPSS-00042', 1, 'samity42521006', '2025-12-06 08:07:04'),
(294, 52, 42, 'CPSS-00042', 1, 'samity42521007', '2025-12-06 08:07:04'),
(295, 52, 42, 'CPSS-00042', 1, 'samity42521008', '2025-12-06 08:07:04'),
(296, 52, 42, 'CPSS-00042', 1, 'samity42521009', '2025-12-06 08:07:04'),
(297, 52, 42, 'CPSS-00042', 1, 'samity42521010', '2025-12-06 08:07:04'),
(298, 52, 42, 'CPSS-00042', 1, 'samity42521011', '2025-12-06 08:07:04'),
(299, 52, 42, 'CPSS-00042', 1, 'samity42521012', '2025-12-06 08:07:04'),
(300, 52, 42, 'CPSS-00042', 1, 'samity42521013', '2025-12-06 08:07:04'),
(301, 52, 42, 'CPSS-00042', 1, 'samity42521014', '2025-12-06 08:07:04'),
(302, 52, 42, 'CPSS-00042', 1, 'samity42521015', '2025-12-06 08:07:04'),
(303, 52, 42, 'CPSS-00042', 1, 'samity42521016', '2025-12-06 08:07:04'),
(304, 52, 42, 'CPSS-00042', 1, 'samity42521017', '2025-12-06 08:07:04'),
(305, 52, 42, 'CPSS-00042', 1, 'samity42521018', '2025-12-06 08:07:04'),
(306, 52, 42, 'CPSS-00042', 1, 'samity42521019', '2025-12-06 08:07:04'),
(307, 52, 42, 'CPSS-00042', 1, 'samity42521020', '2025-12-06 08:07:04'),
(308, 52, 42, 'CPSS-00042', 1, 'samity42521021', '2025-12-06 08:07:04'),
(309, 52, 42, 'CPSS-00042', 1, 'samity42521022', '2025-12-06 08:07:04'),
(310, 52, 42, 'CPSS-00042', 1, 'samity42521023', '2025-12-06 08:07:04'),
(311, 52, 42, 'CPSS-00042', 1, 'samity42521024', '2025-12-06 08:07:04'),
(312, 52, 42, 'CPSS-00042', 1, 'samity42521025', '2025-12-06 08:07:04'),
(313, 52, 42, 'CPSS-00042', 1, 'samity42521026', '2025-12-06 08:07:04'),
(314, 52, 42, 'CPSS-00042', 1, 'samity42521027', '2025-12-06 08:07:04'),
(315, 52, 42, 'CPSS-00042', 1, 'samity42521028', '2025-12-06 08:07:04'),
(316, 52, 42, 'CPSS-00042', 1, 'samity42521029', '2025-12-06 08:07:04'),
(317, 52, 42, 'CPSS-00042', 1, 'samity42521030', '2025-12-06 08:07:04'),
(318, 52, 42, 'CPSS-00042', 1, 'samity42521031', '2025-12-06 08:07:04'),
(319, 52, 42, 'CPSS-00042', 1, 'samity42521032', '2025-12-06 08:07:04'),
(320, 53, 47, 'CPSS-00047', 1, 'samity47531001', '2025-12-06 08:09:09'),
(321, 53, 47, 'CPSS-00047', 1, 'samity47531002', '2025-12-06 08:09:09'),
(322, 54, 47, 'CPSS-00047', 4, 'share47544001', '2025-12-06 08:09:33'),
(323, 54, 47, 'CPSS-00047', 4, 'share47544002', '2025-12-06 08:09:33'),
(324, 54, 47, 'CPSS-00047', 4, 'share47544003', '2025-12-06 08:09:33'),
(325, 54, 47, 'CPSS-00047', 4, 'share47544004', '2025-12-06 08:09:33'),
(326, 54, 47, 'CPSS-00047', 4, 'share47544005', '2025-12-06 08:09:33'),
(327, 54, 47, 'CPSS-00047', 4, 'share47544006', '2025-12-06 08:09:33'),
(328, 54, 47, 'CPSS-00047', 4, 'share47544007', '2025-12-06 08:09:33'),
(329, 54, 47, 'CPSS-00047', 4, 'share47544008', '2025-12-06 08:09:33'),
(330, 54, 47, 'CPSS-00047', 4, 'share47544009', '2025-12-06 08:09:33'),
(331, 54, 47, 'CPSS-00047', 4, 'share47544010', '2025-12-06 08:09:33'),
(332, 54, 47, 'CPSS-00047', 4, 'share47544011', '2025-12-06 08:09:33'),
(333, 54, 47, 'CPSS-00047', 4, 'share47544012', '2025-12-06 08:09:33'),
(334, 54, 47, 'CPSS-00047', 4, 'share47544013', '2025-12-06 08:09:33'),
(335, 54, 47, 'CPSS-00047', 4, 'share47544014', '2025-12-06 08:09:33'),
(336, 54, 47, 'CPSS-00047', 4, 'share47544015', '2025-12-06 08:09:33'),
(337, 54, 47, 'CPSS-00047', 4, 'share47544016', '2025-12-06 08:09:33'),
(338, 54, 47, 'CPSS-00047', 4, 'share47544017', '2025-12-06 08:09:33'),
(339, 54, 47, 'CPSS-00047', 4, 'share47544018', '2025-12-06 08:09:33'),
(340, 54, 47, 'CPSS-00047', 4, 'share47544019', '2025-12-06 08:09:33'),
(341, 54, 47, 'CPSS-00047', 4, 'share47544020', '2025-12-06 08:09:33'),
(342, 54, 47, 'CPSS-00047', 4, 'share47544021', '2025-12-06 08:09:33'),
(343, 54, 47, 'CPSS-00047', 4, 'share47544022', '2025-12-06 08:09:33'),
(344, 54, 47, 'CPSS-00047', 4, 'share47544023', '2025-12-06 08:09:33'),
(345, 54, 47, 'CPSS-00047', 4, 'share47544024', '2025-12-06 08:09:33'),
(346, 54, 47, 'CPSS-00047', 4, 'share47544025', '2025-12-06 08:09:33'),
(347, 54, 47, 'CPSS-00047', 4, 'share47544026', '2025-12-06 08:09:33'),
(348, 54, 47, 'CPSS-00047', 4, 'share47544027', '2025-12-06 08:09:33'),
(349, 54, 47, 'CPSS-00047', 4, 'share47544028', '2025-12-06 08:09:33'),
(350, 54, 47, 'CPSS-00047', 4, 'share47544029', '2025-12-06 08:09:33'),
(351, 54, 47, 'CPSS-00047', 4, 'share47544030', '2025-12-06 08:09:33'),
(352, 54, 47, 'CPSS-00047', 4, 'share47544031', '2025-12-06 08:09:33'),
(353, 54, 47, 'CPSS-00047', 4, 'share47544032', '2025-12-06 08:09:33'),
(354, 54, 47, 'CPSS-00047', 4, 'share47544033', '2025-12-06 08:09:33'),
(355, 54, 47, 'CPSS-00047', 4, 'share47544034', '2025-12-06 08:09:33'),
(356, 54, 47, 'CPSS-00047', 4, 'share47544035', '2025-12-06 08:09:33'),
(357, 54, 47, 'CPSS-00047', 4, 'share47544036', '2025-12-06 08:09:33'),
(358, 54, 47, 'CPSS-00047', 4, 'share47544037', '2025-12-06 08:09:33'),
(359, 54, 47, 'CPSS-00047', 4, 'share47544038', '2025-12-06 08:09:33'),
(360, 54, 47, 'CPSS-00047', 4, 'share47544039', '2025-12-06 08:09:33'),
(361, 55, 48, 'CPSS-00048', 1, 'samity48551001', '2025-12-06 08:11:12'),
(362, 55, 48, 'CPSS-00048', 1, 'samity48551002', '2025-12-06 08:11:12'),
(363, 56, 48, 'CPSS-00048', 4, 'share48564001', '2025-12-06 08:13:23'),
(364, 56, 48, 'CPSS-00048', 4, 'share48564002', '2025-12-06 08:13:23'),
(365, 56, 48, 'CPSS-00048', 4, 'share48564003', '2025-12-06 08:13:23'),
(366, 56, 48, 'CPSS-00048', 4, 'share48564004', '2025-12-06 08:13:23'),
(367, 56, 48, 'CPSS-00048', 4, 'share48564005', '2025-12-06 08:13:23'),
(368, 56, 48, 'CPSS-00048', 4, 'share48564006', '2025-12-06 08:13:23'),
(369, 56, 48, 'CPSS-00048', 4, 'share48564007', '2025-12-06 08:13:23'),
(370, 56, 48, 'CPSS-00048', 4, 'share48564008', '2025-12-06 08:13:23'),
(371, 56, 48, 'CPSS-00048', 4, 'share48564009', '2025-12-06 08:13:23'),
(372, 56, 48, 'CPSS-00048', 4, 'share48564010', '2025-12-06 08:13:23'),
(373, 56, 48, 'CPSS-00048', 4, 'share48564011', '2025-12-06 08:13:23'),
(374, 56, 48, 'CPSS-00048', 4, 'share48564012', '2025-12-06 08:13:23'),
(375, 56, 48, 'CPSS-00048', 4, 'share48564013', '2025-12-06 08:13:23'),
(376, 56, 48, 'CPSS-00048', 4, 'share48564014', '2025-12-06 08:13:23'),
(377, 56, 48, 'CPSS-00048', 4, 'share48564015', '2025-12-06 08:13:23'),
(378, 56, 48, 'CPSS-00048', 4, 'share48564016', '2025-12-06 08:13:23'),
(379, 56, 48, 'CPSS-00048', 4, 'share48564017', '2025-12-06 08:13:23'),
(380, 56, 48, 'CPSS-00048', 4, 'share48564018', '2025-12-06 08:13:23'),
(381, 57, 51, 'CPSS-00051', 1, 'samity51571001', '2025-12-06 08:16:08'),
(382, 57, 51, 'CPSS-00051', 1, 'samity51571002', '2025-12-06 08:16:08'),
(383, 58, 62, 'CPSS-00060', 1, 'samity62581001', '2025-12-06 08:18:41'),
(384, 58, 62, 'CPSS-00060', 1, 'samity62581002', '2025-12-06 08:18:41'),
(385, 59, 62, 'CPSS-00060', 4, 'share62594001', '2025-12-06 08:21:11'),
(386, 59, 62, 'CPSS-00060', 4, 'share62594002', '2025-12-06 08:21:11'),
(387, 59, 62, 'CPSS-00060', 4, 'share62594003', '2025-12-06 08:21:11'),
(388, 59, 62, 'CPSS-00060', 4, 'share62594004', '2025-12-06 08:21:11'),
(389, 59, 62, 'CPSS-00060', 4, 'share62594005', '2025-12-06 08:21:11'),
(390, 59, 62, 'CPSS-00060', 4, 'share62594006', '2025-12-06 08:21:11'),
(391, 59, 62, 'CPSS-00060', 4, 'share62594007', '2025-12-06 08:21:11'),
(392, 59, 62, 'CPSS-00060', 4, 'share62594008', '2025-12-06 08:21:11'),
(393, 59, 62, 'CPSS-00060', 4, 'share62594009', '2025-12-06 08:21:11'),
(394, 59, 62, 'CPSS-00060', 4, 'share62594010', '2025-12-06 08:21:11'),
(395, 59, 62, 'CPSS-00060', 4, 'share62594011', '2025-12-06 08:21:11'),
(396, 59, 62, 'CPSS-00060', 4, 'share62594012', '2025-12-06 08:21:11'),
(397, 59, 62, 'CPSS-00060', 4, 'share62594013', '2025-12-06 08:21:11'),
(398, 59, 62, 'CPSS-00060', 4, 'share62594014', '2025-12-06 08:21:11'),
(399, 59, 62, 'CPSS-00060', 4, 'share62594015', '2025-12-06 08:21:11'),
(400, 59, 62, 'CPSS-00060', 4, 'share62594016', '2025-12-06 08:21:11'),
(401, 59, 62, 'CPSS-00060', 4, 'share62594017', '2025-12-06 08:21:11'),
(402, 59, 62, 'CPSS-00060', 4, 'share62594018', '2025-12-06 08:21:11'),
(403, 59, 62, 'CPSS-00060', 4, 'share62594019', '2025-12-06 08:21:11'),
(404, 59, 62, 'CPSS-00060', 4, 'share62594020', '2025-12-06 08:21:11'),
(405, 59, 62, 'CPSS-00060', 4, 'share62594021', '2025-12-06 08:21:11'),
(406, 59, 62, 'CPSS-00060', 4, 'share62594022', '2025-12-06 08:21:11'),
(407, 59, 62, 'CPSS-00060', 4, 'share62594023', '2025-12-06 08:21:11'),
(408, 60, 56, 'CPSS-00056', 1, 'samity56601001', '2025-12-06 08:24:48'),
(409, 60, 56, 'CPSS-00056', 1, 'samity56601002', '2025-12-06 08:24:48'),
(410, 61, 56, 'CPSS-00056', 4, 'share56614001', '2025-12-06 08:27:43'),
(411, 61, 56, 'CPSS-00056', 4, 'share56614002', '2025-12-06 08:27:43'),
(412, 61, 56, 'CPSS-00056', 4, 'share56614003', '2025-12-06 08:27:43'),
(413, 62, 57, 'CPSS-00057', 1, 'samity57621001', '2025-12-06 12:05:08'),
(414, 62, 57, 'CPSS-00057', 1, 'samity57621002', '2025-12-06 12:05:08'),
(415, 63, 57, 'CPSS-00057', 4, 'share57634001', '2025-12-06 12:05:43'),
(416, 63, 57, 'CPSS-00057', 4, 'share57634002', '2025-12-06 12:05:43'),
(417, 63, 57, 'CPSS-00057', 4, 'share57634003', '2025-12-06 12:05:43'),
(418, 63, 57, 'CPSS-00057', 4, 'share57634004', '2025-12-06 12:05:43'),
(419, 63, 57, 'CPSS-00057', 4, 'share57634005', '2025-12-06 12:05:43'),
(420, 63, 57, 'CPSS-00057', 4, 'share57634006', '2025-12-06 12:05:43'),
(421, 63, 57, 'CPSS-00057', 4, 'share57634007', '2025-12-06 12:05:43'),
(422, 63, 57, 'CPSS-00057', 4, 'share57634008', '2025-12-06 12:05:43'),
(423, 64, 59, 'CPSS-00058', 1, 'samity59641001', '2025-12-06 12:08:00'),
(424, 64, 59, 'CPSS-00058', 1, 'samity59641002', '2025-12-06 12:08:00'),
(425, 65, 63, 'CPSS-00063', 1, 'samity63651001', '2025-12-06 12:10:31'),
(426, 65, 63, 'CPSS-00063', 1, 'samity63651002', '2025-12-06 12:10:31'),
(427, 66, 65, 'CPSS-00064', 1, 'samity65661001', '2025-12-06 12:13:07'),
(428, 66, 65, 'CPSS-00064', 1, 'samity65661002', '2025-12-06 12:13:07'),
(429, 67, 65, 'CPSS-00064', 4, 'share65674001', '2025-12-06 12:13:59'),
(430, 67, 65, 'CPSS-00064', 4, 'share65674002', '2025-12-06 12:13:59'),
(431, 67, 65, 'CPSS-00064', 4, 'share65674003', '2025-12-06 12:13:59'),
(432, 67, 65, 'CPSS-00064', 4, 'share65674004', '2025-12-06 12:13:59'),
(433, 67, 65, 'CPSS-00064', 4, 'share65674005', '2025-12-06 12:13:59'),
(434, 67, 65, 'CPSS-00064', 4, 'share65674006', '2025-12-06 12:13:59'),
(435, 67, 65, 'CPSS-00064', 4, 'share65674007', '2025-12-06 12:13:59'),
(436, 67, 65, 'CPSS-00064', 4, 'share65674008', '2025-12-06 12:13:59'),
(437, 67, 65, 'CPSS-00064', 4, 'share65674009', '2025-12-06 12:13:59'),
(438, 67, 65, 'CPSS-00064', 4, 'share65674010', '2025-12-06 12:13:59'),
(439, 67, 65, 'CPSS-00064', 4, 'share65674011', '2025-12-06 12:13:59'),
(440, 67, 65, 'CPSS-00064', 4, 'share65674012', '2025-12-06 12:13:59'),
(441, 67, 65, 'CPSS-00064', 4, 'share65674013', '2025-12-06 12:13:59'),
(442, 67, 65, 'CPSS-00064', 4, 'share65674014', '2025-12-06 12:13:59'),
(443, 67, 65, 'CPSS-00064', 4, 'share65674015', '2025-12-06 12:13:59'),
(444, 67, 65, 'CPSS-00064', 4, 'share65674016', '2025-12-06 12:13:59'),
(445, 67, 65, 'CPSS-00064', 4, 'share65674017', '2025-12-06 12:13:59'),
(446, 67, 65, 'CPSS-00064', 4, 'share65674018', '2025-12-06 12:13:59'),
(447, 68, 66, 'CPSS-00066', 1, 'samity66681001', '2025-12-06 12:16:50'),
(448, 68, 66, 'CPSS-00066', 1, 'samity66681002', '2025-12-06 12:16:50'),
(449, 69, 66, 'CPSS-00066', 4, 'share66694001', '2025-12-06 12:17:05'),
(450, 70, 67, 'CPSS-00067', 1, 'samity67701001', '2025-12-06 12:17:37'),
(451, 70, 67, 'CPSS-00067', 1, 'samity67701002', '2025-12-06 12:17:37'),
(452, 71, 67, 'CPSS-00067', 4, 'share67714001', '2025-12-06 12:18:01'),
(453, 71, 67, 'CPSS-00067', 4, 'share67714002', '2025-12-06 12:18:01'),
(454, 72, 69, 'CPSS-00069', 1, 'samity69721001', '2025-12-06 12:19:39'),
(455, 72, 69, 'CPSS-00069', 1, 'samity69721002', '2025-12-06 12:19:39'),
(456, 73, 69, 'CPSS-00069', 4, 'share69734001', '2025-12-06 12:22:09'),
(457, 73, 69, 'CPSS-00069', 4, 'share69734002', '2025-12-06 12:22:09'),
(458, 73, 69, 'CPSS-00069', 4, 'share69734003', '2025-12-06 12:22:09'),
(459, 73, 69, 'CPSS-00069', 4, 'share69734004', '2025-12-06 12:22:09'),
(460, 73, 69, 'CPSS-00069', 4, 'share69734005', '2025-12-06 12:22:09'),
(461, 73, 69, 'CPSS-00069', 4, 'share69734006', '2025-12-06 12:22:09'),
(462, 73, 69, 'CPSS-00069', 4, 'share69734007', '2025-12-06 12:22:09'),
(464, 74, 70, 'CPSS-00070', 1, 'samity70741001', '2025-12-06 12:22:52'),
(465, 74, 70, 'CPSS-00070', 1, 'samity70741002', '2025-12-06 12:22:52'),
(466, 75, 70, 'CPSS-00070', 4, 'share70754001', '2025-12-06 12:23:20'),
(467, 75, 70, 'CPSS-00070', 4, 'share70754002', '2025-12-06 12:23:20'),
(468, 75, 70, 'CPSS-00070', 4, 'share70754003', '2025-12-06 12:23:20'),
(469, 75, 70, 'CPSS-00070', 4, 'share70754004', '2025-12-06 12:23:20'),
(470, 75, 70, 'CPSS-00070', 4, 'share70754005', '2025-12-06 12:23:20'),
(471, 75, 70, 'CPSS-00070', 4, 'share70754006', '2025-12-06 12:23:20'),
(472, 75, 70, 'CPSS-00070', 4, 'share70754007', '2025-12-06 12:23:20'),
(473, 75, 70, 'CPSS-00070', 4, 'share70754008', '2025-12-06 12:23:20'),
(474, 75, 70, 'CPSS-00070', 4, 'share70754009', '2025-12-06 12:23:20'),
(475, 75, 70, 'CPSS-00070', 4, 'share70754010', '2025-12-06 12:23:20'),
(476, 75, 70, 'CPSS-00070', 4, 'share70754011', '2025-12-06 12:23:20'),
(477, 75, 70, 'CPSS-00070', 4, 'share70754012', '2025-12-06 12:23:20'),
(478, 75, 70, 'CPSS-00070', 4, 'share70754013', '2025-12-06 12:23:20'),
(479, 75, 70, 'CPSS-00070', 4, 'share70754014', '2025-12-06 12:23:20'),
(480, 75, 70, 'CPSS-00070', 4, 'share70754015', '2025-12-06 12:23:20'),
(481, 75, 70, 'CPSS-00070', 4, 'share70754016', '2025-12-06 12:23:20'),
(482, 75, 70, 'CPSS-00070', 4, 'share70754017', '2025-12-06 12:23:20'),
(483, 75, 70, 'CPSS-00070', 4, 'share70754018', '2025-12-06 12:23:20'),
(484, 75, 70, 'CPSS-00070', 4, 'share70754019', '2025-12-06 12:23:20'),
(485, 75, 70, 'CPSS-00070', 4, 'share70754020', '2025-12-06 12:23:20'),
(486, 75, 70, 'CPSS-00070', 4, 'share70754021', '2025-12-06 12:23:20'),
(487, 75, 70, 'CPSS-00070', 4, 'share70754022', '2025-12-06 12:23:20'),
(488, 75, 70, 'CPSS-00070', 4, 'share70754023', '2025-12-06 12:23:20'),
(489, 75, 70, 'CPSS-00070', 4, 'share70754024', '2025-12-06 12:23:20'),
(490, 75, 70, 'CPSS-00070', 4, 'share70754025', '2025-12-06 12:23:20'),
(491, 75, 70, 'CPSS-00070', 4, 'share70754026', '2025-12-06 12:23:20'),
(492, 75, 70, 'CPSS-00070', 4, 'share70754027', '2025-12-06 12:23:20'),
(493, 75, 70, 'CPSS-00070', 4, 'share70754028', '2025-12-06 12:23:20'),
(494, 75, 70, 'CPSS-00070', 4, 'share70754029', '2025-12-06 12:23:20'),
(495, 75, 70, 'CPSS-00070', 4, 'share70754030', '2025-12-06 12:23:20'),
(496, 75, 70, 'CPSS-00070', 4, 'share70754031', '2025-12-06 12:23:20'),
(497, 75, 70, 'CPSS-00070', 4, 'share70754032', '2025-12-06 12:23:20'),
(498, 75, 70, 'CPSS-00070', 4, 'share70754033', '2025-12-06 12:23:20'),
(499, 75, 70, 'CPSS-00070', 4, 'share70754034', '2025-12-06 12:23:20'),
(500, 75, 70, 'CPSS-00070', 4, 'share70754035', '2025-12-06 12:23:20'),
(501, 75, 70, 'CPSS-00070', 4, 'share70754036', '2025-12-06 12:23:20'),
(502, 75, 70, 'CPSS-00070', 4, 'share70754037', '2025-12-06 12:23:20'),
(503, 75, 70, 'CPSS-00070', 4, 'share70754038', '2025-12-06 12:23:20'),
(504, 75, 70, 'CPSS-00070', 4, 'share70754039', '2025-12-06 12:23:20'),
(505, 75, 70, 'CPSS-00070', 4, 'share70754040', '2025-12-06 12:23:20'),
(506, 75, 70, 'CPSS-00070', 4, 'share70754041', '2025-12-06 12:23:20'),
(507, 75, 70, 'CPSS-00070', 4, 'share70754042', '2025-12-06 12:23:20'),
(508, 75, 70, 'CPSS-00070', 4, 'share70754043', '2025-12-06 12:23:20'),
(509, 75, 70, 'CPSS-00070', 4, 'share70754044', '2025-12-06 12:23:20'),
(510, 76, 71, 'CPSS-00071', 1, 'samity71761001', '2025-12-06 12:26:29'),
(511, 76, 71, 'CPSS-00071', 1, 'samity71761002', '2025-12-06 12:26:29'),
(512, 77, 71, 'CPSS-00071', 4, 'share71774001', '2025-12-06 12:28:39'),
(513, 77, 71, 'CPSS-00071', 4, 'share71774002', '2025-12-06 12:28:39'),
(514, 77, 71, 'CPSS-00071', 4, 'share71774003', '2025-12-06 12:28:39'),
(516, 78, 72, 'CPSS-00072', 1, 'samity72781001', '2025-12-06 12:30:48'),
(517, 78, 72, 'CPSS-00072', 1, 'samity72781002', '2025-12-06 12:30:48'),
(518, 79, 72, 'CPSS-00072', 4, 'share72794001', '2025-12-06 12:34:09'),
(519, 79, 72, 'CPSS-00072', 4, 'share72794002', '2025-12-06 12:34:09'),
(520, 79, 72, 'CPSS-00072', 4, 'share72794003', '2025-12-06 12:34:09'),
(521, 79, 72, 'CPSS-00072', 4, 'share72794004', '2025-12-06 12:34:09'),
(522, 79, 72, 'CPSS-00072', 4, 'share72794005', '2025-12-06 12:34:09'),
(523, 79, 72, 'CPSS-00072', 4, 'share72794006', '2025-12-06 12:34:09'),
(524, 79, 72, 'CPSS-00072', 4, 'share72794007', '2025-12-06 12:34:09'),
(525, 79, 72, 'CPSS-00072', 4, 'share72794008', '2025-12-06 12:34:09'),
(526, 80, 73, 'CPSS-00073', 1, 'samity73801001', '2025-12-06 12:35:41'),
(527, 80, 73, 'CPSS-00073', 1, 'samity73801002', '2025-12-06 12:35:41'),
(528, 81, 73, 'CPSS-00073', 4, 'share73814001', '2025-12-06 12:35:58'),
(529, 81, 73, 'CPSS-00073', 4, 'share73814002', '2025-12-06 12:35:58'),
(530, 81, 73, 'CPSS-00073', 4, 'share73814003', '2025-12-06 12:35:58'),
(531, 81, 73, 'CPSS-00073', 4, 'share73814004', '2025-12-06 12:35:58'),
(532, 81, 73, 'CPSS-00073', 4, 'share73814005', '2025-12-06 12:35:58'),
(533, 81, 73, 'CPSS-00073', 4, 'share73814006', '2025-12-06 12:35:58'),
(534, 81, 73, 'CPSS-00073', 4, 'share73814007', '2025-12-06 12:35:58'),
(535, 81, 73, 'CPSS-00073', 4, 'share73814008', '2025-12-06 12:35:58'),
(536, 81, 73, 'CPSS-00073', 4, 'share73814009', '2025-12-06 12:35:58'),
(537, 81, 73, 'CPSS-00073', 4, 'share73814010', '2025-12-06 12:35:58'),
(538, 81, 73, 'CPSS-00073', 4, 'share73814011', '2025-12-06 12:35:58'),
(539, 81, 73, 'CPSS-00073', 4, 'share73814012', '2025-12-06 12:35:58'),
(540, 81, 73, 'CPSS-00073', 4, 'share73814013', '2025-12-06 12:35:58'),
(541, 82, 74, 'CPSS-00074', 1, 'samity74821001', '2025-12-06 12:37:11'),
(542, 82, 74, 'CPSS-00074', 1, 'samity74821002', '2025-12-06 12:37:11'),
(543, 83, 74, 'CPSS-00074', 4, 'share74834001', '2025-12-06 12:37:33'),
(544, 83, 74, 'CPSS-00074', 4, 'share74834002', '2025-12-06 12:37:33'),
(545, 83, 74, 'CPSS-00074', 4, 'share74834003', '2025-12-06 12:37:33'),
(546, 83, 74, 'CPSS-00074', 4, 'share74834004', '2025-12-06 12:37:33'),
(547, 84, 76, 'CPSS-00076', 1, 'samity76841001', '2025-12-06 12:38:42'),
(548, 84, 76, 'CPSS-00076', 1, 'samity76841002', '2025-12-06 12:38:42'),
(549, 85, 77, 'CPSS-00077', 1, 'samity77851001', '2025-12-06 12:39:50'),
(550, 85, 77, 'CPSS-00077', 1, 'samity77851002', '2025-12-06 12:39:50'),
(551, 86, 77, 'CPSS-00077', 4, 'share77864001', '2025-12-06 12:40:11'),
(552, 86, 77, 'CPSS-00077', 4, 'share77864002', '2025-12-06 12:40:11'),
(553, 86, 77, 'CPSS-00077', 4, 'share77864003', '2025-12-06 12:40:11'),
(554, 87, 78, 'CPSS-00078', 1, 'samity78871001', '2025-12-06 12:42:57'),
(555, 87, 78, 'CPSS-00078', 1, 'samity78871002', '2025-12-06 12:42:57'),
(556, 88, 78, 'CPSS-00078', 4, 'share78884001', '2025-12-06 12:43:32'),
(557, 88, 78, 'CPSS-00078', 4, 'share78884002', '2025-12-06 12:43:32'),
(558, 88, 78, 'CPSS-00078', 4, 'share78884003', '2025-12-06 12:43:32'),
(559, 89, 79, 'CPSS-00079', 1, 'samity79891001', '2025-12-06 12:46:45'),
(560, 89, 79, 'CPSS-00079', 1, 'samity79891002', '2025-12-06 12:46:45'),
(561, 90, 79, 'CPSS-00079', 4, 'share79904001', '2025-12-06 12:48:14'),
(562, 90, 79, 'CPSS-00079', 4, 'share79904002', '2025-12-06 12:48:14'),
(563, 90, 79, 'CPSS-00079', 4, 'share79904003', '2025-12-06 12:48:14'),
(564, 90, 79, 'CPSS-00079', 4, 'share79904004', '2025-12-06 12:48:14'),
(565, 90, 79, 'CPSS-00079', 4, 'share79904005', '2025-12-06 12:48:14'),
(566, 91, 85, 'CPSS-00083', 1, 'samity85911001', '2025-12-06 12:50:13'),
(567, 91, 85, 'CPSS-00083', 1, 'samity85911002', '2025-12-06 12:50:13'),
(568, 92, 85, 'CPSS-00083', 4, 'share85924001', '2025-12-06 12:50:33'),
(569, 93, 89, 'CPSS-00086', 1, 'samity89931001', '2025-12-06 12:51:47'),
(570, 93, 89, 'CPSS-00086', 1, 'samity89931002', '2025-12-06 12:51:47'),
(571, 94, 89, 'CPSS-00086', 4, 'share89944001', '2025-12-06 12:52:07'),
(572, 94, 89, 'CPSS-00086', 4, 'share89944002', '2025-12-06 12:52:07'),
(573, 94, 89, 'CPSS-00086', 4, 'share89944003', '2025-12-06 12:52:07'),
(574, 94, 89, 'CPSS-00086', 4, 'share89944004', '2025-12-06 12:52:07'),
(575, 94, 89, 'CPSS-00086', 4, 'share89944005', '2025-12-06 12:52:07'),
(576, 95, 90, 'CPSS-00090', 1, 'samity90951001', '2025-12-06 12:54:23'),
(577, 95, 90, 'CPSS-00090', 1, 'samity90951002', '2025-12-06 12:54:23'),
(578, 96, 90, 'CPSS-00090', 4, 'share90964001', '2025-12-06 12:55:29'),
(579, 97, 92, 'CPSS-00091', 1, 'samity92971001', '2025-12-06 12:55:56'),
(580, 97, 92, 'CPSS-00091', 1, 'samity92971002', '2025-12-06 12:55:56'),
(581, 98, 93, 'CPSS-00093', 1, 'samity93981001', '2025-12-06 12:56:39'),
(582, 98, 93, 'CPSS-00093', 1, 'samity93981002', '2025-12-06 12:56:39'),
(583, 99, 93, 'CPSS-00093', 4, 'share93994001', '2025-12-06 12:56:55'),
(584, 100, 95, 'CPSS-00094', 1, 'samity951001001', '2025-12-06 12:58:20'),
(585, 100, 95, 'CPSS-00094', 1, 'samity951001002', '2025-12-06 12:58:20'),
(586, 101, 95, 'CPSS-00094', 4, 'share951014001', '2025-12-06 12:58:39'),
(587, 102, 97, 'CPSS-00096', 1, 'samity971021001', '2025-12-06 12:59:11'),
(588, 102, 97, 'CPSS-00096', 1, 'samity971021002', '2025-12-06 12:59:11'),
(589, 103, 97, 'CPSS-00096', 4, 'share971034001', '2025-12-06 12:59:30'),
(590, 104, 98, 'CPSS-00098', 1, 'samity981041001', '2025-12-06 13:00:00'),
(591, 104, 98, 'CPSS-00098', 1, 'samity981041002', '2025-12-06 13:00:00'),
(592, 105, 98, 'CPSS-00098', 4, 'share981054001', '2025-12-06 13:00:14'),
(593, 106, 100, 'CPSS-00099', 1, 'samity1001061001', '2025-12-06 13:01:25'),
(594, 106, 100, 'CPSS-00099', 1, 'samity1001061002', '2025-12-06 13:01:25'),
(595, 107, 100, 'CPSS-00099', 4, 'share1001074001', '2025-12-06 13:02:06'),
(596, 108, 101, 'CPSS-00101', 1, 'samity1011081001', '2025-12-06 13:02:41'),
(597, 108, 101, 'CPSS-00101', 1, 'samity1011081002', '2025-12-06 13:02:41'),
(598, 109, 101, 'CPSS-00101', 4, 'share1011094001', '2025-12-06 13:03:01'),
(599, 110, 102, 'CPSS-00102', 1, 'samity1021101001', '2025-12-06 13:03:53'),
(600, 110, 102, 'CPSS-00102', 1, 'samity1021101002', '2025-12-06 13:03:53'),
(601, 111, 102, 'CPSS-00102', 4, 'share1021114001', '2025-12-06 13:04:15'),
(602, 112, 103, 'CPSS-00103', 1, 'samity1031121001', '2025-12-06 13:04:46'),
(603, 112, 103, 'CPSS-00103', 1, 'samity1031121002', '2025-12-06 13:04:46'),
(604, 113, 103, 'CPSS-00103', 4, 'share1031134001', '2025-12-06 13:05:02'),
(605, 114, 104, 'CPSS-00104', 1, 'samity1041141001', '2025-12-06 13:06:09'),
(606, 114, 104, 'CPSS-00104', 1, 'samity1041141002', '2025-12-06 13:06:09'),
(607, 115, 104, 'CPSS-00104', 4, 'share1041154001', '2025-12-06 13:06:26'),
(608, 116, 106, 'CPSS-00105', 1, 'samity1061161001', '2025-12-06 13:07:09'),
(609, 116, 106, 'CPSS-00105', 1, 'samity1061161002', '2025-12-06 13:07:09'),
(610, 117, 106, 'CPSS-00105', 4, 'share1061174001', '2025-12-06 13:07:23'),
(611, 118, 107, 'CPSS-00107', 1, 'samity1071181001', '2025-12-06 13:08:00'),
(612, 118, 107, 'CPSS-00107', 1, 'samity1071181002', '2025-12-06 13:08:00'),
(613, 119, 108, 'CPSS-00108', 1, 'samity1081191001', '2025-12-06 13:08:34'),
(614, 119, 108, 'CPSS-00108', 1, 'samity1081191002', '2025-12-06 13:08:34'),
(615, 120, 109, 'CPSS-00109', 1, 'samity1091201001', '2025-12-06 13:09:06'),
(616, 120, 109, 'CPSS-00109', 1, 'samity1091201002', '2025-12-06 13:09:06'),
(617, 121, 109, 'CPSS-00109', 4, 'share1091214001', '2025-12-06 13:09:24'),
(618, 122, 110, 'CPSS-00110', 1, 'samity1101221001', '2025-12-06 13:09:49'),
(619, 122, 110, 'CPSS-00110', 1, 'samity1101221002', '2025-12-06 13:09:49'),
(620, 123, 110, 'CPSS-00110', 4, 'share1101234001', '2025-12-06 13:10:04'),
(621, 124, 107, 'CPSS-00107', 4, 'share1071244001', '2025-12-07 04:22:24'),
(622, 125, 35, 'CPSS-00035', 1, 'samity351251001', '2025-12-08 12:02:34'),
(623, 125, 35, 'CPSS-00035', 1, 'samity351251002', '2025-12-08 12:02:34'),
(624, 126, 112, 'CPSS-00111', 1, 'samity1121261001', '2025-12-08 12:03:24'),
(625, 126, 112, 'CPSS-00111', 1, 'samity1121261002', '2025-12-08 12:03:24'),
(626, 127, 123, 'CPSS-00113', 1, 'samity1231271001', '2026-01-04 09:55:37'),
(627, 127, 123, 'CPSS-00113', 1, 'samity1231271002', '2026-01-04 09:55:37'),
(628, 128, 40, 'CPSS-00040', 1, 'samity401281001', '2026-01-28 03:47:12'),
(629, 128, 40, 'CPSS-00040', 1, 'samity401281002', '2026-01-28 03:47:12'),
(630, 129, 40, 'CPSS-00040', 4, 'share401294001', '2026-01-28 03:48:39'),
(631, 129, 40, 'CPSS-00040', 4, 'share401294002', '2026-01-28 03:48:39'),
(632, 129, 40, 'CPSS-00040', 4, 'share401294003', '2026-01-28 03:48:39'),
(633, 129, 40, 'CPSS-00040', 4, 'share401294004', '2026-01-28 03:48:39'),
(634, 129, 40, 'CPSS-00040', 4, 'share401294005', '2026-01-28 03:48:39'),
(635, 129, 40, 'CPSS-00040', 4, 'share401294006', '2026-01-28 03:48:39'),
(636, 129, 40, 'CPSS-00040', 4, 'share401294007', '2026-01-28 03:48:39'),
(637, 129, 40, 'CPSS-00040', 4, 'share401294008', '2026-01-28 03:48:39'),
(638, 99, 93, 'CPSS-00093', 4, 'share93994002', '2026-01-28 08:14:17'),
(639, 99, 93, 'CPSS-00093', 4, 'share93994003', '2026-01-28 08:14:17'),
(640, 96, 90, 'CPSS-00090', 4, 'share90964002', '2026-01-28 09:13:29'),
(641, 96, 90, 'CPSS-00090', 4, 'share90964003', '2026-01-28 09:13:30'),
(642, 96, 90, 'CPSS-00090', 4, 'share90964004', '2026-01-28 09:13:30'),
(643, 92, 85, 'CPSS-00083', 4, 'share85924002', '2026-01-28 09:13:32'),
(644, 92, 85, 'CPSS-00083', 4, 'share85924003', '2026-01-28 09:13:32'),
(645, 92, 85, 'CPSS-00083', 4, 'share85924004', '2026-01-28 09:13:32'),
(646, 103, 97, 'CPSS-00096', 4, 'share971034002', '2026-01-28 09:13:34'),
(647, 113, 103, 'CPSS-00103', 4, 'share1031134002', '2026-01-28 09:13:38'),
(648, 113, 103, 'CPSS-00103', 4, 'share1031134003', '2026-01-28 09:13:38'),
(649, 101, 95, 'CPSS-00094', 4, 'share951014002', '2026-01-28 09:13:41'),
(650, 101, 95, 'CPSS-00094', 4, 'share951014003', '2026-01-28 09:13:41'),
(651, 109, 101, 'CPSS-00101', 4, 'share1011094002', '2026-01-28 09:13:45'),
(652, 109, 101, 'CPSS-00101', 4, 'share1011094003', '2026-01-28 09:13:45'),
(654, 111, 102, 'CPSS-00102', 4, 'share1021114002', '2026-01-28 09:13:50'),
(655, 111, 102, 'CPSS-00102', 4, 'share1021114003', '2026-01-28 09:13:50'),
(656, 107, 100, 'CPSS-00099', 4, 'share1001074002', '2026-01-28 09:13:56'),
(657, 107, 100, 'CPSS-00099', 4, 'share1001074003', '2026-01-28 09:13:56'),
(658, 107, 100, 'CPSS-00099', 4, 'share1001074004', '2026-01-28 09:13:56'),
(659, 117, 106, 'CPSS-00105', 4, 'share1061174002', '2026-01-28 09:13:59'),
(660, 117, 106, 'CPSS-00105', 4, 'share1061174003', '2026-01-28 09:13:59'),
(661, 124, 107, 'CPSS-00107', 4, 'share1071244002', '2026-01-28 09:14:04'),
(662, 121, 109, 'CPSS-00109', 4, 'share1091214002', '2026-01-28 09:14:07'),
(663, 121, 109, 'CPSS-00109', 4, 'share1091214003', '2026-01-28 09:14:07'),
(664, 121, 109, 'CPSS-00109', 4, 'share1091214004', '2026-01-28 09:14:07'),
(667, 123, 110, 'CPSS-00110', 4, 'share1101234002', '2026-01-28 09:42:05'),
(668, 123, 110, 'CPSS-00110', 4, 'share1101234003', '2026-01-28 09:42:05'),
(669, 123, 110, 'CPSS-00110', 4, 'share1101234004', '2026-01-28 09:42:05'),
(670, 115, 104, 'CPSS-00104', 4, 'share1041154002', '2026-01-29 05:15:20'),
(671, 115, 104, 'CPSS-00104', 4, 'share1041154003', '2026-01-29 05:15:20'),
(672, 130, 34, 'CPSS-00032', 1, 'samity341301001', '2026-01-29 06:08:25'),
(673, 130, 34, 'CPSS-00032', 1, 'samity341301002', '2026-01-29 06:08:25'),
(674, 84, 76, 'CPSS-00076', 1, 'samity76841003', '2026-01-29 06:15:32'),
(675, 84, 76, 'CPSS-00076', 1, 'samity76841004', '2026-01-29 06:15:32'),
(676, 84, 76, 'CPSS-00076', 1, 'samity76841005', '2026-01-29 06:15:32'),
(677, 131, 123, 'CPSS-00113', 4, 'share1231314001', '2026-01-29 08:02:45'),
(678, 131, 123, 'CPSS-00113', 4, 'share1231314002', '2026-01-29 08:02:45'),
(680, 132, 68, 'CPSS-00068', 1, 'samity681321001', '2026-02-01 05:46:22'),
(681, 132, 68, 'CPSS-00068', 1, 'samity681321002', '2026-02-01 05:46:22'),
(682, 133, 68, 'CPSS-00068', 4, 'share681334001', '2026-02-01 05:46:44'),
(683, 133, 68, 'CPSS-00068', 4, 'share681334002', '2026-02-01 05:46:44'),
(684, 133, 68, 'CPSS-00068', 4, 'share681334003', '2026-02-01 05:46:44'),
(685, 134, 130, 'CPSS-00124', 1, 'samity1301341001', '2026-02-02 14:09:57'),
(686, 134, 130, 'CPSS-00124', 1, 'samity1301341002', '2026-02-02 14:09:57'),
(687, 135, 131, 'CPSS-00131', 1, 'samity1311351001', '2026-02-02 14:24:49'),
(688, 135, 131, 'CPSS-00131', 1, 'samity1311351002', '2026-02-02 14:24:49'),
(689, 131, 123, 'CPSS-00113', 4, 'share1231314003', '2026-02-03 17:32:11'),
(690, 131, 123, 'CPSS-00113', 4, 'share1231314004', '2026-02-03 17:32:11'),
(691, 136, 36, 'CPSS-00036', 4, 'share361364001', '2026-02-03 17:32:27'),
(692, 137, 50, 'CPSS-00049', 1, 'samity501371001', '2026-02-05 03:50:29'),
(693, 137, 50, 'CPSS-00049', 1, 'samity501371002', '2026-02-05 03:50:29'),
(694, 138, 132, 'CPSS-00132', 1, 'samity1321381001', '2026-02-08 06:32:37'),
(695, 138, 132, 'CPSS-00132', 1, 'samity1321381002', '2026-02-08 06:32:37'),
(696, 139, 134, 'CPSS-00133', 1, 'samity1341391001', '2026-02-09 11:06:40'),
(697, 139, 134, 'CPSS-00133', 1, 'samity1341391002', '2026-02-09 11:06:40'),
(698, 140, 134, 'CPSS-00133', 4, 'share1341404001', '2026-02-09 17:10:39'),
(699, 140, 134, 'CPSS-00133', 4, 'share1341404002', '2026-02-09 17:10:39'),
(700, 140, 134, 'CPSS-00133', 4, 'share1341404003', '2026-02-09 17:10:39'),
(701, 140, 134, 'CPSS-00133', 4, 'share1341404004', '2026-02-09 17:10:39'),
(702, 140, 134, 'CPSS-00133', 4, 'share1341404005', '2026-02-09 17:10:39'),
(703, 140, 134, 'CPSS-00133', 4, 'share1341404006', '2026-02-09 17:10:39'),
(704, 140, 134, 'CPSS-00133', 4, 'share1341404007', '2026-02-09 17:10:39'),
(705, 140, 134, 'CPSS-00133', 4, 'share1341404008', '2026-02-09 17:10:39'),
(706, 141, 132, 'CPSS-00132', 4, 'share1321414001', '2026-02-10 05:46:15'),
(707, 141, 132, 'CPSS-00132', 4, 'share1321414002', '2026-02-10 05:46:15'),
(708, 141, 132, 'CPSS-00132', 4, 'share1321414003', '2026-02-10 05:46:15'),
(709, 142, 63, 'CPSS-00063', 4, 'share631424001', '2026-02-10 06:00:19'),
(710, 63, 57, 'CPSS-00057', 4, 'share57634009', '2026-02-10 16:51:03');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `service_name_bn` text NOT NULL,
  `service_name_en` text NOT NULL,
  `about_service` text NOT NULL,
  `icon` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name_bn`, `service_name_en`, `about_service`, `icon`) VALUES
(4, 'পেশাজীবী সদস্যভুক্তি', 'পেশাজীবী সদস্যদের জন্য নেটওয়ার্কিং সুযোগ তৈরি করা', '<ul><li>&nbsp;বাংলাদেশের বিভিন্ন অঞ্চলের &nbsp;এবং বহির্বিশ্বে প্রবাসী পেশাজীবীদের সদস্য হিসেবে অন্তর্ভুক্ত করা।</li><li>একটি কার্যকর সিস্টেম তৈরি করা যা সদস্যদের নিবন্ধন, সদস্যপদ পরিচালনা, ফাইনান্স, এবং অন্যান্য কার্যক্রম ট্র্যাক করতে সক্ষম হবে।</li><li>পেশাজীবী সদস্যদের জন্য বিভিন্ন ধরনের প্রশিক্ষণ, কর্মশালা এবং সেমিনার আয়োজন করা যাতে তাদের পেশাগত দক্ষতা বাড়ে।</li></ul>', 'fa-users'),
(8, 'সমবায়ভিত্তিক প্রকল্প', 'সমবায়ভিত্তিক প্রকল্প তৈরি করা যা সদস্যদের আর্থিক উন্নয়ন সাধন করা', '<ul><li>সদস্যদের জন্য ব্যবসায়িক প্রকল্পের সাশ্রয়ী ঋণ সুবিধা প্রদান করা।</li><li>সদস্যদের জন্য নিয়মিত ব্যবসায়িক প্রকল্পের মাধ্যমে তথ্য সরবরাহ করা, যেমন লাভ-ক্ষতি, সদস্যদের অর্থনৈতিক অবস্থা, এবং কর্মসূচির অগ্রগতি।</li><li>টেকসই পদ্ধতি অবলম্বন করা, যেমন জমি, ফ্ল্যাট , দোকান, গাড়ি ক্রয়-বিক্রয় এবং নির্ভরযোগ্য ব্যবসায় বিনিয়োগ করা।</li></ul>', 'fa-tasks'),
(12, 'আবাসন বা বাসস্থান ', 'সদস্যদের জন্য নিরাপদ, সাশ্রয়ী এবং স্বাস্থ্যসম্মত আবাসন ব্যবস্থা তৈরি করা', '<ul><li>সদস্যদের আবাসন সমস্যা সমাধানে সামাজিক ও সরকারি সহায়তা নিশ্চিত করা।</li><li>সাশ্রয়ী মূল্যে নিরাপদ আবাসন সুবিধা প্রদান করা।</li><li>আশ্রয়হীন বা অস্থায়ী বাসস্থানে বসবাসকারী সদস্যদের জন্য স্থায়ী আবাসন ব্যবস্থা গঠন করা।</li><li>আবাসন সুবিধা নিশ্চিত করার জন্য সরকারী এবং বেসরকারি উদ্যোগের সাথে যৌথভাবে কাজ করা।</li></ul>', 'fa-home'),
(13, 'স্বাস্থ্য', 'সদস্যদের শারীরিক ও মানসিক স্বাস্থ্য সেবা প্রদান করা', '<ul><li>স্বাস্থ্য সচেতনতা বৃদ্ধির জন্য বিভিন্ন কর্মসূচি চালু করা, যেমন স্বাস্থ্য পরীক্ষা ও টিকাদান ক্যাম্প।</li><li>স্বাস্থ্য সেবা ব্যবস্থাকে সদস্যদের জন্য অ্যাক্সেসযোগ্য ও সাশ্রয়ী করে তোলা।</li><li>সমিতির নিজস্ব হাসপাতাল বা ডায়াগনস্টিক থেকে সাশ্রয়ী মূল্যে চিকিৎসা সেবা ব্যবস্থা করা।</li><li>সদস্যদের জন্য স্বাস্থ্য বীমা বা সুরক্ষা পরিকল্পনা গঠন করা।</li></ul>', 'fa-medkit'),
(14, 'শিক্ষা', 'সদস্যদের জন্য মানসম্পন্ন শিক্ষা ও প্রশিক্ষণ সুবিধা প্রদান করা', '<ul><li>সদস্যদের জন্য বিভিন্ন শিক্ষা ও প্রশিক্ষণ কর্মসূচি চালু করা, যেমন শিশুদের জন্য স্কুল এবং প্রাপ্তবয়স্কদের জন্য বিভিন্ন প্রশিক্ষণ কোর্স।</li><li>সমিতির নিজস্ব বিদ্যালয়ে শিক্ষা উপকরণ এবং টিউশন ফি সাশ্রয়ী মূল্যে সরবরাহ করা।</li><li>সদস্যদের শিক্ষাগত উন্নতির জন্য স্কলারশিপ ও সহায়তার ব্যবস্থা করা।</li></ul>', 'fa-font'),
(15, 'অর্থনৈতিক সহায়তা', 'সদস্যদের আর্থিকভাবে স্বাবলম্বী করা এবং উন্নতি নিশ্চিত করা', '<ul><li>সদস্যদের জন্য সহজ শর্তে ঋণ বা আর্থিক সহায়তা প্রদান করা, বিশেষত ছোট ব্যবসা শুরু করার জন্য।</li><li>সদস্যদের জন্য ছোট ঋণ স্কিম এবং ক্ষুদ্রঋণ প্রকল্প চালু করা।</li><li>অর্থনৈতিক পরামর্শ ও সহায়তার মাধ্যমে সদস্যদের ব্যবসায়িক দেউলিয়া হওয়া থেকে রক্ষা করা।</li><li>উদ্যোক্তা বা ক্ষুদ্র ব্যবসায়ীদের জন্য কর্মশালা ও প্রশিক্ষণ পরিচালনা করা।</li></ul>', 'fa-file'),
(16, 'কর্মসংস্থান', 'সদস্যদের জন্য কর্মসংস্থান এবং চাকরি সৃষ্টির সুযোগ বৃদ্ধি করা', '<ul><li>সদস্যদের জন্য কাজের সুযোগ তৈরি করতে স্থানীয় ও আন্তর্জাতিক প্রতিষ্ঠানগুলির সঙ্গে যোগাযোগ স্থাপন করা।</li><li>সদস্যদের দক্ষতা উন্নয়নের জন্য প্রশিক্ষণ কর্মসূচি আয়োজন করা, যেমন প্রযুক্তি, ব্যবসা, এবং হস্তশিল্প।</li><li>উদ্যোক্তা হওয়ার জন্য কর্মশালা এবং সহায়তা প্রদান করা।</li><li>কর্মসংস্থানের জন্য সহায়ক নেটওয়ার্ক গঠন করা, যাতে সদস্যরা একে অপরের সহায়তায় কাজ পেতে পারে।</li></ul>', 'fa-file'),
(17, 'মানবাধিকার', 'সদস্যদের মানবাধিকার রক্ষা এবং নিরাপত্তা নিশ্চিত করা', '<ul><li>সদস্যদের মৌলিক মানবাধিকার সম্পর্কে সচেতনতা বৃদ্ধি এবং প্রয়োজনে আইনি সহায়তা করা।</li><li>নির্যাতন, বৈষম্য এবং অন্যায্য আচরণ থেকে সদস্যদের সুরক্ষা নিশ্চিত করা।</li><li>সদস্যদের জন্য একটি নিরাপদ পরিবেশ নিশ্চিত করা, যেখানে তারা স্বাধীনভাবে কথা বলতে ও কাজ করতে পারে।</li><li>মানুষের স্বাধীনতা এবং তাদের মত প্রকাশের অধিকার সুরক্ষা প্রদান করা।</li></ul>', 'fa-file'),
(18, 'আইটি এবং প্রযুক্তি শিক্ষা', 'সদস্যদের প্রযুক্তিগত দক্ষতা উন্নয়ন এবং আধুনিক প্রযুক্তিতে দক্ষ করে তোলা', '<ul><li>সদস্যদের জন্য আধুনিক সফটওয়্যার ও অ্যাপ্লিকেশন এবং ইন্টারনেট ভিত্তিক প্রশিক্ষণ প্রদান করা।</li><li>বিভিন্ন স্কুল ও কলেজ বা দাতব্য প্রতিষ্ঠানে শর্ট টাইম ও লং টাইম আইটি প্রশিক্ষণ প্রদান করা</li><li>প্রযুক্তির সুবিধা ব্যবহার করে সদস্যদের কর্মক্ষমতা এবং উৎপাদনশীলতা বৃদ্ধি করা।</li><li>ই-কমার্স এবং ডিজিটাল মার্কেটিং এবং আউটসোর্সিং এর মাধ্যমে সদস্যদের কর্মসংস্থান সুযোগ বৃদ্ধি করা।</li></ul>', 'fa-file'),
(19, 'খাদ্য বা খাদ্যদ্রব্য', 'সদস্যদের জন্য নিরাপদ এবং স্বাস্থ্যকর খাদ্য নিশ্চিত করা', '<ul><li>সমিতির সদস্যদের অর্গানিক ও ভেজালমুক্ত খাদ্য ও খাদ্যদ্রব্য প্রাপ্তি পরিচালনার জন্য কোডার মার্ট নামে অনলাইন শপ চালুকরণ।</li><li>সমিতির নিজস্ব বাগানে আধুনিক প্রযুক্তি এবং চাষাবাদ পদ্ধতি ব্যবহার করে খাদ্য উৎপাদন বৃদ্ধি করা।</li><li>খাদ্য বিষক্রিয়া, খাদ্যদূষণ এবং খাদ্যের নিরাপত্তা সংক্রান্ত ঝুঁকি কমানো।</li><li>টেকসই কৃষি পদ্ধতি ব্যবহার করে পরিবেশকে অক্ষত রাখা এবং পৃথিবীকে খাদ্য সংকটের ঝুঁকি থেকে মুক্ত রাখা।</li></ul>', 'fa-file'),
(20, 'পরিবার ও সম্পর্ক', 'সদস্যদের পারিবারিক সম্পর্ক এবং সম্পর্কের মান উন্নয়ন করা।', '<ul><li>সদস্যদের জন্য পারিবারিক সম্পর্কের উন্নতি সম্পর্কিত সেমিনার এবং কর্মশালা আয়োজন করা।</li><li>পরিবারে ভাল সম্পর্ক স্থাপনের জন্য পরামর্শ এবং সহায়তা প্রদান করা।</li><li>পারিবারিক সমস্যাগুলির সমাধান এবং তাদের সাথে সম্পর্কিত শিক্ষামূলক উদ্যোগ চালু করা।</li><li>দম্পতি, পিতামাতা, এবং সন্তানের মধ্যে ভালো সম্পর্ক প্রতিষ্ঠা করতে নির্দেশনা প্রদান করা।</li><li>সদস্যদের সন্তানদের জন্য বয়োজেষ্টদের শ্রদ্ধা ও নৈতিকতা প্রশিক্ষণ ও সচেতনতা তৈরি করা।</li></ul>', 'fa-file'),
(21, 'সামাজিক নিরাপত্তা', 'সদস্যদের জন্য সামাজিক নিরাপত্তা ব্যবস্থা গঠন করা', '<ul><li>সদস্যদের জন্য অবসরকালীন সুবিধা, পেনশন স্কিম এবং স্বাস্থ্য বীমা সুবিধা চালু করা।</li><li>দুর্যোগ ও অনাকাঙ্ক্ষিত পরিস্থিতিতে সদস্যদের সাহায্য করার জন্য ত্রাণ তহবিল তৈরি করা।</li><li>যৌথ নিরাপত্তা পরিকল্পনা চালু করা যা সকল সদস্যের জন্য সমান সুবিধা নিশ্চিত করবে।</li><li>পরিবার পরিজন ও অসুস্থ সদস্যদের জন্য সহায়তা প্যাকেজ প্রবর্তন করা।</li><li>সামাজিক নিরাপত্তা কর্মসূচি গ্রহণের মাধ্যমে সদস্যদের অবসরকালীন সুরক্ষা নিশ্চিত করা।</li></ul>', 'fa-file');

-- --------------------------------------------------------

--
-- Table structure for table `setup`
--

CREATE TABLE `setup` (
  `id` int(11) NOT NULL,
  `site_name_bn` varchar(255) NOT NULL,
  `site_name_en` varchar(255) NOT NULL,
  `registration_no` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone1` varchar(20) DEFAULT NULL,
  `phone2` varchar(20) DEFAULT NULL,
  `about_text` text DEFAULT NULL,
  `about_text_en` text DEFAULT NULL,
  `slogan_bn` text DEFAULT NULL,
  `slogan_en` text DEFAULT NULL,
  `ac_title` text DEFAULT NULL,
  `ac_no` text DEFAULT NULL,
  `logo` text DEFAULT NULL,
  `objectives` text DEFAULT NULL,
  `facebook` text DEFAULT NULL,
  `youtube` text DEFAULT NULL,
  `linkedin` text DEFAULT NULL,
  `instagram` text DEFAULT NULL,
  `bank_name` text DEFAULT NULL,
  `bank_address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setup`
--

INSERT INTO `setup` (`id`, `site_name_bn`, `site_name_en`, `registration_no`, `address`, `email`, `phone1`, `phone2`, `about_text`, `about_text_en`, `slogan_bn`, `slogan_en`, `ac_title`, `ac_no`, `logo`, `objectives`, `facebook`, `youtube`, `linkedin`, `instagram`, `bank_name`, `bank_address`) VALUES
(1, 'কোডার পেশাজীবী সমবায় সমিতি লিঃ', 'Coder Peshajibi Samabay Samity Ltd.', '২০২৫.১.৩২.২৬২৫.২৮২৩', '10/A-3, (7th Floor), Bardhan Bari, Darus Salam Thana, Mirpur-1, Dhaka-1216 - ( ১০/এ-৩, ( ৮ম তলা ) বর্ধন বাড়ি, দারুস সালাম থানা, মিরপুর-১, ঢাকা )', 'codersamity@gmail.com', '01540505646', '01919787839', 'কোডার পেশাজীবী সমবায় সমিতি লিঃ একটি স্বেচ্ছাসেবী, পেশাজীবী ও অরাজনৈতিক প্রতিষ্ঠান, যাহা ২০২৩ইং সালে প্রতিষ্ঠা করা হয়েছে এবং ২৯শে অক্টোবর, ২০২৫ইং সালে গণপ্রজাতন্ত্রী বাংলাদেশ সরকারের সমবায় অধিদপ্তর কর্তৃক নিবন্ধিত, যাহার নিবন্ধন নং- ২০২৫.১.৩২.২৬২৫.২৮২৩। &nbsp;আমাদের লক্ষ্য হলো পেশাজীবীদের মধ্যে সহযোগিতা বৃদ্ধি করা এবং তাদের পেশাগত ও আর্থিক উন্নয়ন সাধনে কাজ &nbsp;করা। আমরা বিভিন্ন প্রশিক্ষণ, কর্মশালা ও সেমিনার আয়োজন করি যাতে সদস্যরা তাদের দক্ষতা বৃদ্ধি করতে পারে এবং পেশাগত জীবনে সফল হতে পারে। আমাদের সদস্যরা বিভিন্ন পেশাগত ক্ষেত্রে কাজ করে এবং আমরা তাদের মধ্যে জ্ঞান ও অভিজ্ঞতা বিনিময় করি। সমিতির সদস্যদের জন্য একটি শক্তিশালী আর্থিক এবং পেশাদার প্ল্যাটফর্ম তৈরি করা, যেখানে সদস্যরা যৌথভাবে বিনিয়োগ করে, ব্যবসা পরিচালনা করে এবং মুনাফা ভাগাভাগি করতে পারে। আমরা বিশ্বাস করি যে, সহযোগিতা ও সমবায় মূলক কাজের মাধ্যমে আমরা আমাদের লক্ষ্য অর্জন করতে পারব এবং আমাদের সদস্যদের জন্য একটি উন্নত ও সমৃদ্ধ ভবিষ্যত গড়ে তুলতে পারব।', 'Coder Peshajibi Samabay Samity Ltd. is a voluntary, professional and non-political organization, established in 2023 and registered on 29th October 2025 by the Department of Cooperatives, Government of the People\'s Republic of Bangladesh, with registration number 2025.1.32.2625.2823. Our mission is to increase cooperation among professionals and work towards their professional and financial development. We organize various trainings, workshops and seminars so that members can enhance their skills and succeed in their professional lives. Our members work in different professional fields and we exchange knowledge and experience among them. To create a strong financial and professional platform for the members of the association, where members can jointly invest, run businesses and share profits. We believe that through cooperation and cooperative work, we can achieve our goals and build a better and prosperous future for our members.', 'একসাথে যেতে হবে বহুদূরে...', 'We have to go far together...', 'কোডার পেশাজীবী সমবায় সমিতি লিঃ (Coder Peshajibi Samabay Samity Ltd)', '৫০৩০১০০১৭৬৩ (50301001763)', 'logo.png', '<ul><li>hello</li><li>bangladesh</li></ul>', 'https://www.facebook.com/profile.php?id=61581789144846', 'youtube.com', 'linkedin.com', 'instagram.com', 'ব্যাংক এশিয়া লিঃ (Bank Asia Ltd.)', 'পুরানা পল্টন, ঢাকা-১০০০ (Purana Paltan, Dhaka-1000)');

-- --------------------------------------------------------

--
-- Table structure for table `share`
--

CREATE TABLE `share` (
  `id` int(11) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `member_code` varchar(25) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `no_share` int(11) DEFAULT NULL,
  `status` varchar(3) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `share`
--

INSERT INTO `share` (`id`, `member_id`, `member_code`, `type`, `project_id`, `no_share`, `status`, `created_at`) VALUES
(2, 110, 'CPSS-00110', 'project', 4, 3, 'A', '2026-01-27 05:56:27'),
(3, 109, 'CPSS-00109', 'project', 4, 3, 'A', '2026-01-27 05:57:44'),
(4, 107, 'CPSS-00107', 'project', 4, 1, 'A', '2026-01-27 05:59:37'),
(5, 106, 'CPSS-00105', 'project', 4, 2, 'A', '2026-01-27 06:00:32'),
(6, 100, 'CPSS-00099', 'project', 4, 3, 'A', '2026-01-27 06:01:35'),
(7, 102, 'CPSS-00102', 'project', 4, 2, 'A', '2026-01-27 06:02:48'),
(8, 101, 'CPSS-00101', 'project', 4, 3, 'A', '2026-01-27 06:03:28'),
(9, 95, 'CPSS-00094', 'project', 4, 2, 'A', '2026-01-27 06:04:18'),
(10, 103, 'CPSS-00103', 'project', 4, 2, 'A', '2026-01-27 06:05:35'),
(11, 97, 'CPSS-00096', 'project', 4, 1, 'A', '2026-01-27 06:06:19'),
(12, 85, 'CPSS-00083', 'project', 4, 3, 'A', '2026-01-27 06:07:47'),
(13, 90, 'CPSS-00090', 'project', 4, 3, 'A', '2026-01-27 06:09:09'),
(14, 93, 'CPSS-00093', 'project', 4, 2, 'A', '2026-01-27 06:10:03'),
(15, 104, 'CPSS-00104', 'project', 4, 2, 'A', '2026-01-29 04:49:58'),
(16, 76, 'CPSS-00076', 'samity', 1, 3, 'A', '2026-01-29 06:14:56'),
(17, 123, 'CPSS-00113', 'project', 4, 2, 'A', '2026-01-29 06:41:07'),
(18, 36, 'CPSS-00036', 'project', 4, 1, 'A', '2026-02-02 13:42:00'),
(19, 134, 'CPSS-00133', 'project', 4, 8, 'A', '2026-02-09 17:09:51'),
(20, 63, 'CPSS-00063', 'project', 4, 1, 'A', '2026-02-10 05:59:47'),
(21, 57, 'CPSS-00057', 'project', 4, 1, 'A', '2026-02-10 16:50:39');

-- --------------------------------------------------------

--
-- Table structure for table `user_access`
--

CREATE TABLE `user_access` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `login` datetime NOT NULL DEFAULT current_timestamp(),
  `logout` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_access`
--

INSERT INTO `user_access` (`id`, `user_id`, `member_id`, `login`, `logout`) VALUES
(133, 2, 0, '2025-10-18 22:04:49', '2025-10-18 22:04:49'),
(134, 2, 0, '2025-10-18 22:09:46', '2025-10-18 22:10:07'),
(135, 2, 0, '2025-10-18 22:12:42', '2025-10-18 22:14:11'),
(136, 2, 0, '2025-10-18 22:20:35', '2025-10-18 22:21:18'),
(137, 3, 1, '2025-10-18 22:22:18', '2025-10-18 22:22:18'),
(138, 3, 1, '2025-10-18 22:30:50', '2025-10-18 22:38:05'),
(139, 2, 0, '2025-10-18 22:38:23', '2025-10-18 23:06:31'),
(140, 2, 0, '2025-10-19 13:33:48', '2025-10-19 13:34:03'),
(141, 3, 1, '2025-10-19 13:34:29', '2025-10-19 13:36:26'),
(142, 2, 0, '2025-10-19 13:36:39', '2025-10-19 13:37:21'),
(143, 2, 0, '2025-10-19 13:38:53', '2025-10-19 13:38:53'),
(144, 2, 0, '2025-10-19 16:28:38', '2025-10-19 16:32:30'),
(145, 2, 0, '2025-10-20 14:22:58', '2025-10-20 14:38:21'),
(146, 2, 0, '2025-10-20 17:18:56', '2025-10-20 17:18:56'),
(147, 2, 0, '2025-10-21 09:24:16', '2025-10-21 09:24:16'),
(148, 2, 0, '2025-10-21 09:34:02', '2025-10-21 09:34:02'),
(149, 2, 0, '2025-10-21 11:58:33', '2025-10-21 11:58:33'),
(150, 2, 0, '2025-10-21 13:29:44', '2025-10-21 13:36:47'),
(151, 2, 0, '2025-10-21 13:38:58', '2025-10-21 13:48:09'),
(152, 2, 0, '2025-10-23 11:44:42', '2025-10-23 11:45:04'),
(153, 2, 0, '2025-10-23 13:27:24', '2025-10-23 13:28:05'),
(154, 3, 1, '2025-10-23 13:28:24', '2025-10-23 13:29:39'),
(155, 2, 0, '2025-10-23 14:45:59', '2025-10-23 14:46:11'),
(156, 3, 1, '2025-10-23 14:46:33', '2025-10-23 14:47:22'),
(157, 2, 0, '2025-10-23 14:47:31', '2025-10-23 14:48:39'),
(158, 2, 0, '2025-10-23 15:07:30', '2025-10-23 15:07:30'),
(159, 2, 0, '2025-10-23 17:03:18', '2025-10-23 17:03:39'),
(160, 2, 0, '2025-10-26 10:52:31', '2025-10-26 10:53:38'),
(161, 2, 0, '2025-10-26 14:37:46', '2025-10-26 14:37:55'),
(162, 2, 0, '2025-10-26 15:28:14', '2025-10-26 15:28:47'),
(163, 2, 0, '2025-10-26 15:29:01', '2025-10-26 15:29:25'),
(164, 3, 1, '2025-10-26 15:29:40', '2025-10-26 15:30:01'),
(165, 2, 0, '2025-10-26 15:32:30', '2025-10-26 15:32:30'),
(166, 2, 0, '2025-10-29 15:35:45', '2025-10-29 16:01:19'),
(167, 2, 0, '2025-10-30 12:42:24', '2025-10-30 12:50:10'),
(168, 3, 1, '2025-10-30 13:33:33', '2025-10-30 13:34:38'),
(169, 2, 0, '2025-10-30 13:34:52', '2025-10-30 13:36:25'),
(170, 2, 0, '2025-10-30 15:55:17', '2025-10-30 15:55:17'),
(171, 3, 1, '2025-10-30 16:44:43', '2025-10-30 16:55:15'),
(172, 3, 1, '2025-10-30 16:55:25', '2025-10-30 17:09:47'),
(173, 3, 1, '2025-10-30 17:22:12', '2025-10-30 17:27:19'),
(174, 2, 0, '2025-10-30 17:27:33', '2025-10-30 18:09:38'),
(175, 2, 0, '2025-10-30 21:07:25', '2025-10-30 21:07:25'),
(176, 2, 0, '2025-10-30 21:07:26', '2025-10-30 21:07:26'),
(177, 2, 0, '2025-10-30 23:23:36', '2025-10-30 23:29:32'),
(178, 2, 0, '2025-10-31 00:10:45', '2025-10-31 00:11:58'),
(179, 2, 0, '2025-10-31 00:33:49', '2025-10-31 00:35:41'),
(180, 2, 0, '2025-10-31 22:16:35', '2025-10-31 22:16:53'),
(181, 2, 0, '2025-10-31 22:45:10', '2025-10-31 22:45:35'),
(182, 20, 52, '2025-10-31 22:47:25', '2025-10-31 22:48:02'),
(183, 2, 0, '2025-10-31 23:37:58', '2025-10-31 23:37:58'),
(184, 24, 59, '2025-11-03 15:52:55', '2025-11-03 15:55:16'),
(185, 24, 59, '2025-11-03 15:56:26', '2025-11-03 15:57:13'),
(186, 2, 0, '2025-11-03 17:37:01', '2025-11-03 17:40:05'),
(187, 22, 56, '2025-11-04 13:59:00', '2025-11-04 14:11:17'),
(188, 3, 1, '2025-11-04 16:59:55', '2025-11-04 17:00:49'),
(189, 3, 1, '2025-11-05 10:59:31', '2025-11-05 11:01:19'),
(190, 2, 0, '2025-11-05 13:42:52', '2025-11-05 13:42:52'),
(191, 2, 0, '2025-11-05 15:35:49', '2025-11-05 15:35:49'),
(192, 10, 39, '2025-11-05 18:43:17', '2025-11-05 18:43:17'),
(193, 2, 0, '2025-11-06 17:02:25', '2025-11-06 17:18:49'),
(194, 3, 1, '2025-11-06 17:19:03', '2025-11-06 17:37:52'),
(195, 2, 0, '2025-11-06 17:37:59', '2025-11-06 17:49:38'),
(196, 22, 56, '2025-11-06 17:49:48', '2025-11-06 17:56:25'),
(197, 2, 0, '2025-11-06 17:56:33', '2025-11-06 17:58:55'),
(198, 22, 56, '2025-11-06 17:59:05', '2025-11-06 18:03:48'),
(199, 2, 0, '2025-11-08 18:20:31', '2025-11-08 18:20:31'),
(200, 2, 0, '2025-11-08 18:20:31', '2025-11-08 18:24:53'),
(201, 3, 1, '2025-11-08 18:26:15', '2025-11-08 18:27:57'),
(202, 2, 0, '2025-11-08 18:28:11', '2025-11-08 18:29:02'),
(203, 2, 0, '2025-11-08 18:32:39', '2025-11-08 18:37:11'),
(204, 4, 30, '2025-11-08 18:37:35', '2025-11-08 18:55:18'),
(205, 5, 31, '2025-11-08 18:55:32', '2025-11-08 19:00:30'),
(206, 4, 30, '2025-11-08 19:01:05', '2025-11-08 19:01:23'),
(207, 5, 31, '2025-11-08 19:01:39', '2025-11-08 19:03:18'),
(208, 5, 31, '2025-11-08 19:03:28', '2025-11-08 19:26:54'),
(209, 4, 30, '2025-11-08 19:54:14', '2025-11-08 19:56:10'),
(210, 3, 1, '2025-11-08 22:50:58', '2025-11-08 23:23:27'),
(211, 3, 1, '2025-11-09 00:53:41', '2025-11-09 01:09:05'),
(212, 2, 0, '2025-11-09 10:49:41', '2025-11-09 10:52:35'),
(213, 3, 1, '2025-11-09 10:52:50', '2025-11-09 10:53:49'),
(214, 4, 30, '2025-11-09 10:54:06', '2025-11-09 10:54:42'),
(215, 5, 31, '2025-11-09 10:55:00', '2025-11-09 10:55:47'),
(216, 4, 30, '2025-11-09 10:56:01', '2025-11-09 11:16:08'),
(217, 5, 31, '2025-11-09 11:16:23', '2025-11-09 12:16:12'),
(218, 3, 1, '2025-11-09 16:17:12', '2025-11-09 16:49:47'),
(219, 33, 71, '2025-11-09 17:29:26', '2025-11-09 17:30:52'),
(220, 26, 63, '2025-11-09 17:37:35', '2025-11-09 17:41:24'),
(221, 2, 0, '2025-11-09 17:41:31', '2025-11-09 17:42:07'),
(222, 26, 63, '2025-11-09 17:42:21', '2025-11-09 17:47:15'),
(223, 10, 39, '2025-11-09 18:23:30', '2025-11-09 18:24:51'),
(224, 2, 0, '2025-11-10 09:12:21', '2025-11-10 09:14:28'),
(225, 10, 39, '2025-11-10 09:16:34', '2025-11-10 09:21:34'),
(226, 2, 0, '2025-11-10 09:21:41', '2025-11-10 09:22:16'),
(227, 10, 39, '2025-11-10 09:22:27', '2025-11-10 09:25:03'),
(228, 10, 39, '2025-11-10 09:37:29', '2025-11-10 09:37:29'),
(229, 2, 0, '2025-11-10 09:41:01', '2025-11-10 09:57:45'),
(230, 2, 0, '2025-11-10 10:01:58', '2025-11-10 10:02:28'),
(231, 26, 63, '2025-11-10 11:14:04', '2025-11-10 11:14:04'),
(232, 20, 52, '2025-11-10 12:27:07', '2025-11-10 12:29:57'),
(233, 2, 0, '2025-11-10 12:30:10', '2025-11-10 12:31:05'),
(234, 20, 52, '2025-11-10 12:31:16', '2025-11-10 12:33:28'),
(235, 3, 1, '2025-11-10 12:49:21', '2025-11-10 12:50:45'),
(236, 3, 1, '2025-11-10 12:50:55', '2025-11-10 12:51:43'),
(237, 25, 62, '2025-11-10 12:52:25', '2025-11-10 12:59:34'),
(238, 3, 1, '2025-11-10 13:06:02', '2025-11-10 13:31:44'),
(239, 3, 1, '2025-11-10 13:31:59', '2025-11-10 13:55:27'),
(240, 2, 0, '2025-11-10 13:55:34', '2025-11-10 13:55:34'),
(241, 13, 42, '2025-11-10 14:01:27', '2025-11-10 14:07:01'),
(242, 13, 42, '2025-11-10 14:11:45', '2025-11-10 14:16:51'),
(243, 2, 0, '2025-11-10 14:18:52', '2025-11-10 14:23:47'),
(244, 41, 79, '2025-11-10 16:52:31', '2025-11-10 16:53:50'),
(245, 24, 59, '2025-11-11 12:00:15', '2025-11-11 12:04:18'),
(246, 24, 59, '2025-11-11 12:05:00', '2025-11-11 12:05:00'),
(247, 14, 45, '2025-11-11 12:12:43', '2025-11-11 12:13:18'),
(248, 14, 45, '2025-11-11 12:26:03', '2025-11-11 12:47:02'),
(249, 35, 73, '2025-11-11 15:24:46', '2025-11-11 15:26:21'),
(250, 41, 79, '2025-11-11 16:22:33', '2025-11-11 16:22:33'),
(251, 22, 56, '2025-11-11 16:27:22', '2025-11-11 16:31:37'),
(252, 24, 59, '2025-11-11 16:39:29', '2025-11-11 16:42:16'),
(253, 14, 45, '2025-11-11 18:19:16', '2025-11-11 18:20:07'),
(254, 2, 0, '2025-11-11 21:07:01', '2025-11-11 21:08:10'),
(255, 3, 1, '2025-11-11 21:08:41', '2025-11-11 21:09:41'),
(256, 2, 0, '2025-11-11 21:09:48', '2025-11-11 21:15:19'),
(257, 14, 45, '2025-11-13 10:36:24', '2025-11-13 10:36:24'),
(258, 12, 41, '2025-11-13 23:25:02', '2025-11-13 23:29:42'),
(259, 2, 0, '2025-11-13 23:29:50', '2025-11-13 23:30:13'),
(260, 12, 41, '2025-11-13 23:30:24', '2025-11-13 23:32:58'),
(261, 2, 0, '2025-11-14 00:43:21', '2025-11-14 00:43:53'),
(262, 2, 0, '2025-11-14 11:54:06', '2025-11-14 11:56:29'),
(263, 4, 30, '2025-11-16 12:36:45', '2025-11-16 12:43:47'),
(264, 4, 30, '2025-11-16 12:44:20', '2025-11-16 13:03:38'),
(265, 3, 1, '2025-11-16 17:54:34', '2025-11-16 17:54:54'),
(266, 41, 79, '2025-11-17 20:37:35', '2025-11-17 20:37:35'),
(267, 2, 0, '2025-11-17 20:43:21', '2025-11-17 20:44:02'),
(268, 41, 79, '2025-11-17 20:44:37', '2025-11-17 20:46:05'),
(269, 4, 30, '2025-11-18 14:38:59', '2025-11-18 14:42:22'),
(270, 33, 71, '2025-11-18 16:37:49', '2025-11-18 16:39:26'),
(271, 2, 0, '2025-11-18 22:23:10', '2025-11-18 22:24:27'),
(272, 2, 0, '2025-11-18 22:25:14', '2025-11-18 22:26:35'),
(273, 2, 0, '2025-11-18 22:29:00', '2025-11-18 22:29:50'),
(274, 2, 0, '2025-11-18 22:30:13', '2025-11-18 22:30:37'),
(275, 2, 0, '2025-11-18 22:30:54', '2025-11-18 22:31:39'),
(276, 2, 0, '2025-11-18 22:41:24', '2025-11-18 22:41:49'),
(277, 3, 1, '2025-11-19 11:38:41', '2025-11-19 11:39:14'),
(278, 25, 62, '2025-11-19 11:41:00', '2025-11-19 11:44:29'),
(279, 2, 0, '2025-11-19 11:44:42', '2025-11-19 11:45:06'),
(280, 25, 62, '2025-11-19 11:45:22', '2025-11-19 12:10:56'),
(281, 2, 0, '2025-11-19 17:25:17', '2025-11-19 17:25:50'),
(282, 43, 85, '2025-11-19 17:42:22', '2025-11-19 17:47:03'),
(283, 3, 1, '2025-11-20 16:10:41', '2025-11-20 16:16:05'),
(284, 3, 1, '2025-11-22 17:40:39', '2025-11-22 17:47:12'),
(285, 20, 52, '2025-11-22 17:47:16', '2025-11-22 17:56:12'),
(286, 2, 0, '2025-11-22 17:56:21', '2025-11-22 18:22:44'),
(287, 20, 52, '2025-11-22 18:22:53', '2025-11-22 18:38:10'),
(288, 2, 0, '2025-11-22 18:38:19', '2025-11-22 18:59:09'),
(289, 20, 52, '2025-11-22 18:59:22', '2025-11-22 18:59:22'),
(290, 20, 52, '2025-11-22 20:09:33', '2025-11-22 20:12:11'),
(291, 20, 52, '2025-11-22 20:21:55', '2025-11-22 20:31:46'),
(292, 2, 0, '2025-11-22 20:32:03', '2025-11-22 20:49:12'),
(293, 20, 52, '2025-11-22 20:49:20', '2025-11-22 20:50:06'),
(294, 2, 0, '2025-11-22 20:50:16', '2025-11-22 20:55:17'),
(295, 20, 52, '2025-11-22 20:55:26', '2025-11-22 21:07:12'),
(296, 20, 52, '2025-11-22 21:07:32', '2025-11-22 21:07:32'),
(297, 20, 52, '2025-11-22 21:08:30', '2025-11-22 21:08:30'),
(298, 20, 52, '2025-11-23 09:42:39', '2025-11-23 11:20:50'),
(299, 2, 0, '2025-11-23 11:20:57', '2025-11-23 11:22:32'),
(300, 20, 52, '2025-11-23 11:22:45', '2025-11-23 11:26:15'),
(301, 34, 72, '2025-11-23 11:26:24', '2025-11-23 11:36:21'),
(302, 2, 0, '2025-11-23 11:36:31', '2025-11-23 11:37:08'),
(303, 34, 72, '2025-11-23 11:37:21', '2025-11-23 11:38:47'),
(304, 20, 52, '2025-11-23 11:38:57', '2025-11-23 11:40:22'),
(305, 34, 72, '2025-11-23 11:44:40', '2025-11-23 11:52:27'),
(306, 24, 59, '2025-11-23 11:52:09', '2025-11-23 11:52:09'),
(307, 34, 72, '2025-11-23 11:53:05', '2025-11-23 11:55:44'),
(308, 34, 72, '2025-11-23 11:55:58', '2025-11-23 13:14:49'),
(309, 22, 56, '2025-11-23 12:43:14', '2025-11-23 12:46:35'),
(310, 3, 1, '2025-11-23 13:15:10', '2025-11-23 13:19:55'),
(311, 34, 72, '2025-11-23 13:20:41', '2025-11-23 13:41:33'),
(312, 20, 52, '2025-11-23 13:41:42', '2025-11-23 13:43:15'),
(313, 13, 42, '2025-11-23 13:44:07', '2025-11-23 13:45:20'),
(314, 2, 0, '2025-11-23 13:45:28', '2025-11-23 13:45:39'),
(315, 13, 42, '2025-11-23 13:45:48', '2025-11-23 13:52:01'),
(316, 25, 62, '2025-11-23 13:52:38', '2025-11-23 14:02:20'),
(317, 2, 0, '2025-11-23 14:02:29', '2025-11-23 14:03:17'),
(318, 25, 62, '2025-11-23 14:03:30', '2025-11-23 14:09:08'),
(319, 13, 42, '2025-11-23 14:09:29', '2025-11-23 14:09:56'),
(320, 13, 42, '2025-11-23 15:38:35', '2025-11-23 15:56:40'),
(321, 2, 0, '2025-11-23 15:56:47', '2025-11-23 15:58:33'),
(322, 13, 42, '2025-11-23 15:58:55', '2025-11-23 16:04:30'),
(323, 41, 79, '2025-11-23 16:04:42', '2025-11-23 16:13:07'),
(324, 2, 0, '2025-11-23 16:13:14', '2025-11-23 16:13:35'),
(325, 41, 79, '2025-11-23 16:13:45', '2025-11-23 16:23:12'),
(326, 41, 79, '2025-11-23 16:26:07', '2025-11-23 16:31:00'),
(327, 2, 0, '2025-11-23 22:28:35', '2025-11-23 22:28:35'),
(328, 2, 0, '2025-11-25 18:11:26', '2025-11-25 18:12:00'),
(329, 2, 0, '2025-11-25 21:35:42', '2025-11-25 21:49:11'),
(330, 2, 0, '2025-11-25 22:21:39', '2025-11-25 23:28:40'),
(331, 2, 0, '2025-11-26 11:44:06', '2025-11-26 11:44:26'),
(332, 13, 42, '2025-11-26 14:19:47', '2025-11-26 14:21:29'),
(333, 2, 0, '2025-11-26 14:21:39', '2025-11-26 14:26:11'),
(334, 2, 0, '2025-11-26 23:12:59', '2025-11-26 23:23:13'),
(335, 3, 1, '2025-11-26 23:23:28', '2025-11-26 23:23:51'),
(336, 2, 0, '2025-11-27 11:51:25', '2025-11-27 11:51:42'),
(337, 2, 0, '2025-11-27 13:54:15', '2025-11-27 15:26:14'),
(338, 2, 0, '2025-11-27 15:26:21', '2025-11-27 15:26:21'),
(339, 2, 0, '2025-11-27 16:16:09', '2025-11-27 16:39:55'),
(340, 3, 1, '2025-11-27 16:40:05', '2025-11-27 16:40:26'),
(341, 2, 0, '2025-11-27 16:40:36', '2025-11-27 16:40:36'),
(342, 2, 0, '2025-11-29 22:47:35', '2025-11-29 22:48:15'),
(343, 3, 1, '2025-11-30 10:36:54', '2025-11-30 10:38:51'),
(344, 2, 0, '2025-11-30 10:38:58', '2025-11-30 10:44:06'),
(345, 3, 1, '2025-11-30 10:44:15', '2025-11-30 10:46:05'),
(346, 2, 0, '2025-11-30 10:46:14', '2025-11-30 10:47:55'),
(347, 49, 95, '2025-11-30 14:42:37', '2025-11-30 14:42:37'),
(348, 46, 90, '2025-11-30 16:01:47', '2025-11-30 16:02:48'),
(349, 46, 90, '2025-11-30 16:04:36', '2025-11-30 16:05:08'),
(350, 47, 92, '2025-11-30 16:05:26', '2025-11-30 16:05:57'),
(351, 46, 90, '2025-11-30 16:08:15', '2025-11-30 16:12:53'),
(352, 47, 92, '2025-11-30 16:13:34', '2025-11-30 16:14:06'),
(353, 47, 92, '2025-11-30 16:35:05', '2025-11-30 16:35:05'),
(354, 2, 0, '2025-12-01 09:38:25', '2025-12-01 09:44:23'),
(355, 3, 1, '2025-12-01 09:44:31', '2025-12-01 09:48:22'),
(356, 2, 0, '2025-12-01 09:49:01', '2025-12-01 09:49:33'),
(357, 17, 48, '2025-12-01 15:15:40', '2025-12-01 15:16:32'),
(358, 17, 48, '2025-12-01 15:22:43', '2025-12-01 15:22:49'),
(359, 45, 89, '2025-12-01 15:59:20', '2025-12-01 15:59:31'),
(360, 17, 48, '2025-12-01 17:35:34', '2025-12-01 17:42:49'),
(361, 2, 0, '2025-12-01 18:09:41', '2025-12-01 18:12:01'),
(362, 59, 108, '2025-12-01 18:13:30', '2025-12-01 18:14:53'),
(363, 2, 0, '2025-12-01 18:15:50', '2025-12-01 18:19:02'),
(364, 2, 0, '2025-12-01 21:20:53', '2025-12-01 22:54:09'),
(365, 3, 1, '2025-12-01 22:54:24', '2025-12-01 23:29:08'),
(366, 2, 0, '2025-12-01 23:54:24', '2025-12-01 23:54:44'),
(367, 2, 0, '2025-12-02 09:15:34', '2025-12-02 09:28:42'),
(368, 45, 89, '2025-12-02 10:44:10', '2025-12-02 10:44:52'),
(369, 45, 89, '2025-12-02 10:45:05', '2025-12-02 10:46:51'),
(370, 45, 89, '2025-12-02 13:49:35', '2025-12-02 13:50:23'),
(371, 2, 0, '2025-12-02 22:02:01', '2025-12-02 22:27:25'),
(372, 2, 0, '2025-12-02 22:27:32', '2025-12-02 22:28:52'),
(373, 38, 76, '2025-12-03 15:42:47', '2025-12-03 15:44:27'),
(374, 2, 0, '2025-12-04 09:41:04', '2025-12-04 09:41:18'),
(375, 2, 0, '2025-12-04 10:57:09', '2025-12-04 10:58:05'),
(376, 14, 45, '2025-12-04 11:22:09', '2025-12-04 11:24:33'),
(377, 47, 92, '2025-12-04 12:03:21', '2025-12-04 12:08:53'),
(378, 47, 92, '2025-12-04 12:23:59', '2025-12-04 12:23:59'),
(379, 3, 1, '2025-12-04 13:29:24', '2025-12-04 13:32:36'),
(380, 40, 78, '2025-12-04 14:06:24', '2025-12-04 14:06:24'),
(381, 40, 78, '2025-12-04 14:09:31', '2025-12-04 14:09:31'),
(382, 2, 0, '2025-12-04 14:37:17', '2025-12-04 14:49:47'),
(383, 58, 107, '2025-12-04 14:49:00', '2025-12-04 14:49:00'),
(384, 58, 107, '2025-12-04 14:49:59', '2025-12-04 14:50:19'),
(385, 2, 0, '2025-12-04 14:50:26', '2025-12-04 14:58:00'),
(386, 58, 107, '2025-12-04 14:58:06', '2025-12-04 14:58:33'),
(387, 2, 0, '2025-12-04 14:58:40', '2025-12-04 15:00:51'),
(388, 58, 107, '2025-12-04 15:01:01', '2025-12-04 15:06:54'),
(389, 58, 107, '2025-12-04 17:53:53', '2025-12-04 17:54:07'),
(390, 14, 45, '2025-12-04 17:56:39', '2025-12-04 17:58:15'),
(391, 2, 0, '2025-12-05 22:59:57', '2025-12-05 23:45:09'),
(392, 14, 45, '2025-12-05 23:45:16', '2025-12-05 23:47:02'),
(393, 2, 0, '2025-12-05 23:47:09', '2025-12-05 23:47:28'),
(394, 14, 45, '2025-12-05 23:47:38', '2025-12-05 23:48:09'),
(395, 2, 0, '2025-12-05 23:48:16', '2025-12-05 23:52:15'),
(396, 14, 45, '2025-12-05 23:52:48', '2025-12-05 23:57:39'),
(397, 3, 1, '2025-12-05 23:57:50', '2025-12-05 23:59:37'),
(398, 3, 1, '2025-12-06 00:11:17', '2025-12-06 00:13:05'),
(399, 2, 0, '2025-12-06 00:13:12', '2025-12-06 00:13:49'),
(400, 2, 0, '2025-12-06 00:20:47', '2025-12-06 00:32:11'),
(401, 2, 0, '2025-12-06 00:32:18', '2025-12-06 00:35:30'),
(402, 3, 1, '2025-12-06 00:35:39', '2025-12-06 01:00:58'),
(403, 2, 0, '2025-12-06 01:01:05', '2025-12-06 01:01:23'),
(404, 3, 1, '2025-12-06 01:01:32', '2025-12-06 01:12:14'),
(405, 2, 0, '2025-12-06 01:12:22', '2025-12-06 01:17:29'),
(406, 14, 45, '2025-12-06 01:18:10', '2025-12-06 01:18:22'),
(407, 2, 0, '2025-12-06 01:18:30', '2025-12-06 01:18:47'),
(408, 14, 45, '2025-12-06 01:18:57', '2025-12-06 01:20:14'),
(409, 2, 0, '2025-12-06 13:26:59', '2025-12-06 14:29:34'),
(410, 22, 56, '2025-12-06 14:29:50', '2025-12-06 14:30:03'),
(411, 2, 0, '2025-12-06 18:01:46', '2025-12-06 18:27:44'),
(412, 2, 0, '2025-12-06 18:27:52', '2025-12-06 19:11:48'),
(413, 2, 0, '2025-12-06 19:15:23', '2025-12-06 19:36:05'),
(414, 3, 1, '2025-12-06 19:36:14', '2025-12-06 19:36:36'),
(415, 20, 52, '2025-12-06 19:36:46', '2025-12-06 19:44:47'),
(416, 40, 78, '2025-12-06 19:44:29', '2025-12-06 19:49:53'),
(417, 2, 0, '2025-12-06 20:03:34', '2025-12-06 20:03:34'),
(418, 2, 0, '2025-12-06 20:37:25', '2025-12-06 20:45:09'),
(419, 22, 56, '2025-12-06 21:53:31', '2025-12-06 21:56:55'),
(420, 16, 47, '2025-12-06 22:44:10', '2025-12-06 22:44:10'),
(421, 38, 76, '2025-12-07 08:55:04', '2025-12-07 08:56:02'),
(422, 17, 48, '2025-12-07 10:00:11', '2025-12-07 10:11:52'),
(423, 2, 0, '2025-12-07 10:20:16', '2025-12-07 10:20:33'),
(424, 58, 107, '2025-12-07 10:20:39', '2025-12-07 10:20:56'),
(425, 2, 0, '2025-12-07 10:21:03', '2025-12-07 10:22:32'),
(426, 58, 107, '2025-12-07 10:22:41', '2025-12-07 10:23:12'),
(427, 48, 93, '2025-12-07 10:23:31', '2025-12-07 10:23:31'),
(428, 46, 90, '2025-12-07 10:24:26', '2025-12-07 10:24:26'),
(429, 49, 95, '2025-12-07 10:29:58', '2025-12-07 10:29:58'),
(430, 52, 100, '2025-12-07 10:30:11', '2025-12-07 10:30:11'),
(431, 43, 85, '2025-12-07 10:37:33', '2025-12-07 10:38:29'),
(432, 43, 85, '2025-12-07 10:38:56', '2025-12-07 10:39:48'),
(433, 47, 92, '2025-12-07 10:40:39', '2025-12-07 10:40:39'),
(434, 47, 92, '2025-12-07 10:40:39', '2025-12-07 10:40:39'),
(435, 2, 0, '2025-12-07 10:42:44', '2025-12-07 10:44:13'),
(436, 56, 104, '2025-12-07 10:51:51', '2025-12-07 10:51:51'),
(437, 14, 45, '2025-12-07 11:11:39', '2025-12-07 11:11:39'),
(438, 59, 108, '2025-12-07 11:59:48', '2025-12-07 11:59:48'),
(439, 2, 0, '2025-12-07 14:58:05', '2025-12-07 14:59:35'),
(440, 9, 36, '2025-12-07 15:08:28', '2025-12-07 15:08:28'),
(441, 2, 0, '2025-12-07 15:10:13', '2025-12-07 15:12:22'),
(442, 47, 92, '2025-12-07 15:18:26', '2025-12-07 15:18:26'),
(443, 16, 47, '2025-12-07 17:45:04', '2025-12-07 18:05:27'),
(444, 16, 47, '2025-12-07 18:07:05', '2025-12-07 18:40:59'),
(445, 47, 92, '2025-12-07 18:17:07', '2025-12-07 18:17:07'),
(446, 2, 0, '2025-12-08 18:01:29', '2025-12-08 18:04:29'),
(447, 2, 0, '2025-12-08 18:05:17', '2025-12-08 18:06:56'),
(448, 22, 56, '2025-12-08 18:45:07', '2025-12-08 18:45:48'),
(449, 47, 92, '2025-12-09 16:57:58', '2025-12-09 16:58:42'),
(450, 17, 48, '2025-12-10 15:56:07', '2025-12-10 15:57:05'),
(451, 2, 0, '2025-12-11 14:21:43', '2025-12-11 14:22:35'),
(452, 2, 0, '2025-12-11 18:21:24', '2025-12-11 18:23:00'),
(453, 55, 103, '2025-12-11 18:23:10', '2025-12-11 18:26:33'),
(454, 2, 0, '2025-12-13 15:23:53', '2025-12-13 15:23:53'),
(455, 47, 92, '2025-12-14 13:25:04', '2025-12-14 13:25:04'),
(456, 2, 0, '2025-12-16 22:28:40', '2025-12-16 22:28:40'),
(457, 56, 104, '2025-12-16 22:29:28', '2025-12-16 22:30:49'),
(458, 61, 110, '2025-12-16 22:31:03', '2025-12-16 22:31:03'),
(459, 3, 1, '2025-12-16 23:11:30', '2025-12-16 23:11:30'),
(460, 38, 76, '2025-12-17 11:53:47', '2025-12-17 11:54:20'),
(461, 61, 110, '2025-12-17 12:02:18', '2025-12-17 12:25:34'),
(462, 61, 110, '2025-12-17 12:25:53', '2025-12-17 12:25:53'),
(463, 2, 0, '2025-12-17 15:43:16', '2025-12-17 15:44:00'),
(464, 58, 107, '2025-12-17 15:44:07', '2025-12-17 15:44:58'),
(465, 2, 0, '2025-12-17 15:45:05', '2025-12-17 15:45:22'),
(466, 56, 104, '2025-12-17 15:45:30', '2025-12-17 15:46:08'),
(467, 2, 0, '2025-12-17 15:46:14', '2025-12-17 15:46:30'),
(468, 57, 106, '2025-12-17 15:46:40', '2025-12-17 15:46:59'),
(469, 2, 0, '2025-12-17 15:47:07', '2025-12-17 15:47:42'),
(470, 51, 98, '2025-12-17 15:47:50', '2025-12-17 15:48:19'),
(471, 2, 0, '2025-12-17 15:48:27', '2025-12-17 15:48:45'),
(472, 52, 100, '2025-12-17 15:48:52', '2025-12-17 15:49:11'),
(473, 2, 0, '2025-12-17 15:49:18', '2025-12-17 15:49:34'),
(474, 54, 102, '2025-12-17 15:49:44', '2025-12-17 15:50:04'),
(475, 2, 0, '2025-12-17 15:50:12', '2025-12-17 15:52:29'),
(476, 53, 101, '2025-12-17 15:52:37', '2025-12-17 15:52:57'),
(477, 2, 0, '2025-12-17 15:53:03', '2025-12-17 15:53:24'),
(478, 60, 109, '2025-12-17 15:53:31', '2025-12-17 15:53:53'),
(479, 2, 0, '2025-12-17 15:54:02', '2025-12-17 15:54:19'),
(480, 50, 97, '2025-12-17 15:54:25', '2025-12-17 15:54:59'),
(481, 2, 0, '2025-12-17 15:55:05', '2025-12-17 15:55:23'),
(482, 49, 95, '2025-12-17 15:55:31', '2025-12-17 15:55:47'),
(483, 2, 0, '2025-12-17 15:55:54', '2025-12-17 15:56:12'),
(484, 61, 110, '2025-12-17 15:56:19', '2025-12-17 15:56:43'),
(485, 2, 0, '2025-12-17 15:56:50', '2025-12-17 15:56:50'),
(486, 2, 0, '2025-12-17 15:57:03', '2025-12-17 15:57:14'),
(487, 43, 85, '2025-12-17 15:57:24', '2025-12-17 15:57:52'),
(488, 2, 0, '2025-12-17 15:57:59', '2025-12-17 15:58:18'),
(489, 46, 90, '2025-12-17 15:58:26', '2025-12-17 15:58:46'),
(490, 2, 0, '2025-12-17 15:58:54', '2025-12-17 15:59:15'),
(491, 48, 93, '2025-12-17 15:59:20', '2025-12-17 15:59:38'),
(492, 56, 104, '2025-12-18 00:43:56', '2025-12-18 00:43:56'),
(493, 56, 104, '2025-12-18 15:10:05', '2025-12-18 15:13:49'),
(494, 2, 0, '2025-12-18 15:14:32', '2025-12-18 15:16:07'),
(495, 61, 110, '2025-12-18 15:16:21', '2025-12-18 15:16:21'),
(496, 2, 0, '2025-12-18 23:22:33', '2025-12-18 23:23:21'),
(497, 2, 0, '2025-12-18 23:24:10', '2025-12-18 23:24:21'),
(498, 61, 110, '2025-12-18 23:24:30', '2025-12-18 23:38:09'),
(499, 2, 0, '2025-12-18 23:38:18', '2025-12-18 23:39:37'),
(500, 61, 110, '2025-12-18 23:39:49', '2025-12-18 23:50:58'),
(501, 2, 0, '2025-12-18 23:51:06', '2025-12-19 00:08:39'),
(502, 22, 56, '2025-12-19 21:27:39', '2025-12-19 21:30:46'),
(503, 7, 34, '2025-12-19 21:54:03', '2025-12-19 21:55:52'),
(504, 22, 56, '2025-12-19 21:54:04', '2025-12-19 21:54:04'),
(505, 59, 108, '2025-12-19 21:55:34', '2025-12-19 22:14:31'),
(506, 13, 42, '2025-12-19 21:56:09', '2025-12-19 21:56:09'),
(507, 34, 72, '2025-12-19 22:22:47', '2025-12-19 22:26:52'),
(508, 23, 57, '2025-12-19 23:45:17', '2025-12-19 23:45:17'),
(509, 59, 108, '2025-12-19 23:53:39', '2025-12-19 23:57:06'),
(510, 59, 108, '2025-12-20 00:03:35', '2025-12-20 00:07:39'),
(511, 2, 0, '2025-12-22 13:49:33', '2025-12-22 14:23:02'),
(512, 10, 39, '2025-12-23 15:41:59', '2025-12-23 15:41:59'),
(513, 2, 0, '2025-12-25 01:31:22', '2025-12-25 01:31:55'),
(514, 3, 1, '2025-12-25 01:32:06', '2025-12-25 01:34:04'),
(515, 3, 1, '2025-12-25 01:34:14', '2025-12-25 01:36:44'),
(516, 2, 0, '2025-12-25 09:19:24', '2025-12-25 09:27:32'),
(517, 2, 0, '2025-12-29 09:44:21', '2025-12-29 09:45:19'),
(518, 17, 48, '2026-01-01 12:11:36', '2026-01-01 12:14:38'),
(519, 56, 104, '2026-01-02 23:05:12', '2026-01-02 23:05:12'),
(520, 2, 0, '2026-01-04 16:43:14', '2026-01-04 16:43:50'),
(521, 2, 0, '2026-01-04 17:29:00', '2026-01-04 17:31:55'),
(522, 3, 1, '2026-01-04 19:06:05', '2026-01-04 19:37:46'),
(523, 38, 76, '2026-01-05 14:35:37', '2026-01-05 14:39:38'),
(524, 38, 76, '2026-01-06 14:03:51', '2026-01-06 14:05:07'),
(525, 3, 1, '2026-01-06 21:42:16', '2026-01-06 21:42:16'),
(526, 2, 0, '2026-01-06 22:44:28', '2026-01-06 23:54:29'),
(527, 3, 1, '2026-01-06 23:54:39', '2026-01-06 23:54:39'),
(528, 3, 1, '2026-01-07 15:09:33', '2026-01-07 15:20:52'),
(529, 2, 0, '2026-01-07 15:21:05', '2026-01-07 15:22:33'),
(530, 3, 1, '2026-01-07 15:22:52', '2026-01-07 15:23:28'),
(531, 57, 106, '2026-01-07 15:23:32', '2026-01-07 15:32:35'),
(532, 2, 0, '2026-01-07 18:12:26', '2026-01-07 18:12:26'),
(533, 57, 106, '2026-01-08 01:29:51', '2026-01-08 01:29:51'),
(534, 57, 106, '2026-01-08 01:40:51', '2026-01-08 01:50:37'),
(535, 3, 1, '2026-01-08 22:00:12', '2026-01-08 22:00:12'),
(536, 2, 0, '2026-01-08 23:24:22', '2026-01-08 23:24:22'),
(537, 3, 1, '2026-01-08 23:37:43', '2026-01-09 00:05:27'),
(538, 63, 123, '2026-01-12 11:46:39', '2026-01-12 11:54:58'),
(539, 2, 0, '2026-01-12 11:55:12', '2026-01-12 11:56:06'),
(540, 2, 0, '2026-01-12 11:56:50', '2026-01-12 11:57:13'),
(541, 63, 123, '2026-01-12 11:57:17', '2026-01-12 12:08:17'),
(542, 2, 0, '2026-01-12 18:18:08', '2026-01-12 18:18:35'),
(543, 2, 0, '2026-01-12 18:18:45', '2026-01-12 18:18:57'),
(544, 2, 0, '2026-01-12 18:19:19', '2026-01-12 18:19:19'),
(545, 4, 30, '2026-01-12 18:20:50', '2026-01-12 18:21:49'),
(546, 2, 0, '2026-01-12 18:22:12', '2026-01-12 18:22:33'),
(547, 4, 30, '2026-01-12 18:22:43', '2026-01-12 18:26:55'),
(548, 2, 0, '2026-01-12 18:27:03', '2026-01-12 18:27:32'),
(549, 4, 30, '2026-01-12 18:27:45', '2026-01-12 18:28:18'),
(550, 2, 0, '2026-01-12 18:28:26', '2026-01-12 18:28:35'),
(551, 4, 30, '2026-01-12 18:28:47', '2026-01-12 18:29:12'),
(552, 4, 30, '2026-01-12 18:29:24', '2026-01-12 18:38:43'),
(553, 4, 30, '2026-01-12 18:38:59', '2026-01-12 18:50:02'),
(554, 38, 76, '2026-01-13 09:44:33', '2026-01-13 09:44:33'),
(555, 2, 0, '2026-01-13 09:51:48', '2026-01-13 09:52:49'),
(556, 2, 0, '2026-01-13 09:53:06', '2026-01-13 09:53:59'),
(557, 38, 76, '2026-01-13 09:54:10', '2026-01-13 09:56:12'),
(558, 2, 0, '2026-01-13 09:56:19', '2026-01-13 09:56:36'),
(559, 38, 76, '2026-01-13 09:56:47', '2026-01-13 09:57:59'),
(560, 2, 0, '2026-01-13 09:58:07', '2026-01-13 10:01:54'),
(561, 38, 76, '2026-01-13 10:02:06', '2026-01-13 10:04:30'),
(562, 2, 0, '2026-01-13 10:04:36', '2026-01-13 10:05:08'),
(563, 38, 76, '2026-01-13 10:05:20', '2026-01-13 10:10:21'),
(564, 2, 0, '2026-01-13 10:10:33', '2026-01-13 10:21:54'),
(565, 61, 110, '2026-01-13 11:20:01', '2026-01-13 11:20:01'),
(566, 2, 0, '2026-01-13 12:02:15', '2026-01-13 12:02:15'),
(567, 3, 1, '2026-01-13 12:40:35', '2026-01-13 12:49:27'),
(568, 2, 0, '2026-01-13 12:49:32', '2026-01-13 12:49:46'),
(569, 3, 1, '2026-01-13 12:49:56', '2026-01-13 12:53:51'),
(570, 3, 1, '2026-01-13 12:53:58', '2026-01-13 12:56:27'),
(571, 2, 0, '2026-01-13 12:56:33', '2026-01-13 12:56:45'),
(572, 3, 1, '2026-01-13 12:56:52', '2026-01-13 13:01:32'),
(573, 3, 1, '2026-01-13 13:21:35', '2026-01-13 13:21:55'),
(574, 2, 0, '2026-01-13 14:15:08', '2026-01-13 14:26:36'),
(575, 2, 0, '2026-01-13 15:10:12', '2026-01-13 15:20:00'),
(576, 2, 0, '2026-01-14 10:57:53', '2026-01-14 10:58:28'),
(577, 12, 41, '2026-01-14 10:58:37', '2026-01-14 11:01:54'),
(578, 2, 0, '2026-01-14 11:02:01', '2026-01-14 11:02:28'),
(579, 12, 41, '2026-01-14 11:02:37', '2026-01-14 11:09:07'),
(580, 2, 0, '2026-01-14 11:09:14', '2026-01-14 11:09:59'),
(581, 12, 41, '2026-01-14 11:10:08', '2026-01-14 11:10:25'),
(582, 12, 41, '2026-01-14 11:17:25', '2026-01-14 11:25:04'),
(583, 12, 41, '2026-01-14 11:59:03', '2026-01-14 11:59:32'),
(584, 2, 0, '2026-01-14 12:28:11', '2026-01-14 12:41:34'),
(585, 9, 36, '2026-01-14 12:33:01', '2026-01-14 12:33:01'),
(586, 17, 48, '2026-01-14 14:13:51', '2026-01-14 14:17:48'),
(587, 3, 1, '2026-01-14 16:20:10', '2026-01-14 16:20:28'),
(588, 12, 41, '2026-01-14 16:21:03', '2026-01-14 16:21:43'),
(589, 2, 0, '2026-01-14 17:58:43', '2026-01-14 18:06:13'),
(590, 3, 1, '2026-01-14 18:06:21', '2026-01-14 18:07:06'),
(591, 2, 0, '2026-01-14 18:07:13', '2026-01-14 18:11:59'),
(592, 3, 1, '2026-01-15 09:43:01', '2026-01-15 09:46:59'),
(593, 2, 0, '2026-01-15 09:47:08', '2026-01-15 09:57:18'),
(594, 3, 1, '2026-01-15 09:57:29', '2026-01-15 10:06:49'),
(595, 2, 0, '2026-01-15 10:06:59', '2026-01-15 10:07:40'),
(596, 25, 62, '2026-01-15 10:07:49', '2026-01-15 10:09:37'),
(597, 2, 0, '2026-01-15 10:09:45', '2026-01-15 10:12:21'),
(598, 20, 52, '2026-01-15 10:12:30', '2026-01-15 10:13:42'),
(599, 2, 0, '2026-01-15 10:13:50', '2026-01-15 10:14:21'),
(600, 39, 77, '2026-01-15 10:14:38', '2026-01-15 10:15:06'),
(601, 2, 0, '2026-01-15 10:15:14', '2026-01-15 10:15:45'),
(602, 34, 72, '2026-01-15 10:15:54', '2026-01-15 10:17:21'),
(603, 3, 1, '2026-01-15 10:17:38', '2026-01-15 10:18:07'),
(604, 9, 36, '2026-01-15 10:29:08', '2026-01-15 10:29:08'),
(605, 3, 1, '2026-01-15 10:33:30', '2026-01-15 10:33:30'),
(606, 49, 95, '2026-01-15 11:07:37', '2026-01-15 11:07:37'),
(607, 2, 0, '2026-01-15 12:09:56', '2026-01-15 12:28:56'),
(608, 9, 36, '2026-01-15 12:30:20', '2026-01-15 12:30:20'),
(609, 58, 107, '2026-01-15 14:53:11', '2026-01-15 14:53:37'),
(610, 2, 0, '2026-01-15 14:53:51', '2026-01-15 14:53:51'),
(611, 3, 1, '2026-01-15 15:30:21', '2026-01-15 15:30:21'),
(612, 2, 0, '2026-01-15 16:35:34', '2026-01-15 16:36:14'),
(613, 9, 36, '2026-01-15 16:36:23', '2026-01-15 16:50:53'),
(614, 2, 0, '2026-01-15 16:51:01', '2026-01-15 16:52:15'),
(615, 2, 0, '2026-01-15 16:52:48', '2026-01-15 16:54:41'),
(616, 12, 41, '2026-01-15 16:54:49', '2026-01-15 17:01:03'),
(617, 20, 52, '2026-01-15 17:01:14', '2026-01-15 17:02:52'),
(618, 2, 0, '2026-01-15 17:02:58', '2026-01-15 17:03:26'),
(619, 34, 72, '2026-01-15 17:03:35', '2026-01-15 17:03:35'),
(620, 3, 1, '2026-01-15 22:40:11', '2026-01-15 23:44:01'),
(621, 2, 0, '2026-01-15 23:44:08', '2026-01-15 23:44:24'),
(622, 34, 72, '2026-01-15 23:44:32', '2026-01-15 23:45:50'),
(623, 2, 0, '2026-01-15 23:46:08', '2026-01-15 23:46:55'),
(624, 25, 62, '2026-01-15 23:47:03', '2026-01-15 23:48:08'),
(625, 2, 0, '2026-01-15 23:48:15', '2026-01-15 23:48:53'),
(626, 25, 62, '2026-01-15 23:49:04', '2026-01-15 23:52:33'),
(627, 20, 52, '2026-01-15 23:52:44', '2026-01-15 23:53:22'),
(628, 34, 72, '2026-01-15 23:53:45', '2026-01-15 23:55:44'),
(629, 2, 0, '2026-01-15 23:55:54', '2026-01-15 23:57:52'),
(630, 12, 41, '2026-01-15 23:58:01', '2026-01-15 23:59:01'),
(631, 61, 110, '2026-01-16 01:12:05', '2026-01-16 01:12:05'),
(632, 59, 108, '2026-01-17 17:37:40', '2026-01-17 17:40:15'),
(633, 10, 39, '2026-01-18 12:59:56', '2026-01-18 12:59:56'),
(634, 2, 0, '2026-01-18 13:10:36', '2026-01-18 13:10:52'),
(635, 3, 1, '2026-01-18 13:11:04', '2026-01-18 13:11:12'),
(636, 2, 0, '2026-01-18 13:11:19', '2026-01-18 13:12:05'),
(637, 17, 48, '2026-01-19 10:34:44', '2026-01-19 10:38:41'),
(638, 2, 0, '2026-01-19 11:50:14', '2026-01-19 11:57:49'),
(639, 17, 48, '2026-01-19 11:54:56', '2026-01-19 11:56:50'),
(640, 2, 0, '2026-01-19 12:13:28', '2026-01-19 12:13:57'),
(641, 26, 63, '2026-01-19 12:14:10', '2026-01-19 12:15:13'),
(642, 2, 0, '2026-01-19 12:15:21', '2026-01-19 12:15:31'),
(643, 26, 63, '2026-01-19 12:15:50', '2026-01-19 12:16:38'),
(644, 3, 1, '2026-01-19 12:21:38', '2026-01-19 12:22:02'),
(645, 17, 48, '2026-01-19 12:36:26', '2026-01-19 12:36:53'),
(646, 3, 1, '2026-01-19 15:36:54', '2026-01-19 16:09:59'),
(647, 3, 1, '2026-01-19 16:10:08', '2026-01-19 16:16:25'),
(648, 2, 0, '2026-01-19 16:16:32', '2026-01-19 16:23:11'),
(649, 2, 0, '2026-01-19 16:23:19', '2026-01-19 16:24:14'),
(650, 3, 1, '2026-01-19 16:24:24', '2026-01-19 16:24:54'),
(651, 2, 0, '2026-01-19 16:25:03', '2026-01-19 16:34:15'),
(652, 2, 0, '2026-01-19 18:21:06', '2026-01-19 18:21:11'),
(653, 3, 1, '2026-01-19 18:21:38', '2026-01-19 18:22:38'),
(654, 2, 0, '2026-01-19 18:22:44', '2026-01-19 19:03:30'),
(655, 2, 0, '2026-01-20 21:40:17', '2026-01-20 21:45:30'),
(656, 2, 0, '2026-01-20 21:45:36', '2026-01-20 21:55:51'),
(657, 3, 1, '2026-01-20 21:56:01', '2026-01-20 21:56:01'),
(658, 2, 0, '2026-01-21 11:46:28', '2026-01-21 11:46:28'),
(659, 2, 0, '2026-01-21 18:20:41', '2026-01-21 18:25:23'),
(660, 26, 63, '2026-01-23 22:09:56', '2026-01-23 22:09:56'),
(661, 2, 0, '2026-01-25 11:40:56', '2026-01-25 11:41:10'),
(662, 2, 0, '2026-01-25 12:13:08', '2026-01-25 12:13:08'),
(663, 38, 76, '2026-01-25 12:32:28', '2026-01-25 12:33:03'),
(664, 59, 108, '2026-01-25 13:36:49', '2026-01-25 13:46:02'),
(665, 40, 78, '2026-01-25 13:43:48', '2026-01-25 13:43:48'),
(666, 2, 0, '2026-01-25 14:02:33', '2026-01-25 14:12:28'),
(667, 59, 108, '2026-01-25 14:06:00', '2026-01-25 14:07:22'),
(668, 40, 78, '2026-01-25 14:11:54', '2026-01-25 14:11:54'),
(669, 2, 0, '2026-01-25 14:25:41', '2026-01-25 14:51:24'),
(670, 59, 108, '2026-01-25 14:31:14', '2026-01-25 14:34:34'),
(671, 59, 108, '2026-01-25 14:34:41', '2026-01-25 14:36:06'),
(672, 59, 108, '2026-01-25 14:45:49', '2026-01-25 14:48:59'),
(673, 35, 73, '2026-01-25 14:46:35', '2026-01-25 14:46:35'),
(674, 56, 104, '2026-01-25 14:58:59', '2026-01-25 14:58:59'),
(675, 2, 0, '2026-01-25 15:02:16', '2026-01-25 15:03:03'),
(676, 2, 0, '2026-01-25 15:03:20', '2026-01-25 15:03:20'),
(677, 36, 74, '2026-01-25 15:10:36', '2026-01-25 15:21:57'),
(678, 56, 104, '2026-01-25 15:55:57', '2026-01-25 15:55:57'),
(679, 2, 0, '2026-01-25 20:57:46', '2026-01-25 20:58:44'),
(680, 28, 66, '2026-01-25 21:20:15', '2026-01-25 21:37:32'),
(681, 28, 66, '2026-01-25 21:38:36', '2026-01-25 21:39:01'),
(682, 24, 59, '2026-01-26 11:23:48', '2026-01-26 11:58:36'),
(683, 14, 45, '2026-01-26 13:57:19', '2026-01-26 14:11:34'),
(684, 14, 45, '2026-01-26 17:24:28', '2026-01-26 17:25:08'),
(685, 2, 0, '2026-01-27 09:45:52', '2026-01-27 09:45:52'),
(686, 47, 92, '2026-01-27 11:48:27', '2026-01-27 11:48:27'),
(687, 2, 0, '2026-01-27 11:53:53', '2026-01-27 11:55:36'),
(688, 2, 0, '2026-01-27 11:55:43', '2026-01-27 11:56:03'),
(689, 61, 110, '2026-01-27 11:56:10', '2026-01-27 11:56:57'),
(690, 2, 0, '2026-01-27 11:57:05', '2026-01-27 11:57:22'),
(691, 60, 109, '2026-01-27 11:57:28', '2026-01-27 11:58:32'),
(692, 2, 0, '2026-01-27 11:58:39', '2026-01-27 12:11:55'),
(693, 58, 107, '2026-01-27 11:59:16', '2026-01-27 11:59:53'),
(694, 57, 106, '2026-01-27 12:00:10', '2026-01-27 12:01:12'),
(695, 52, 100, '2026-01-27 12:01:19', '2026-01-27 12:01:56'),
(696, 54, 102, '2026-01-27 12:02:24', '2026-01-27 12:02:53'),
(697, 53, 101, '2026-01-27 12:03:14', '2026-01-27 12:03:48'),
(698, 49, 95, '2026-01-27 12:03:56', '2026-01-27 12:04:59'),
(699, 55, 103, '2026-01-27 12:05:06', '2026-01-27 12:05:43'),
(700, 50, 97, '2026-01-27 12:06:08', '2026-01-27 12:06:48'),
(701, 43, 85, '2026-01-27 12:07:07', '2026-01-27 12:07:55'),
(702, 46, 90, '2026-01-27 12:08:36', '2026-01-27 12:09:28'),
(703, 48, 93, '2026-01-27 12:09:49', '2026-01-27 12:10:14'),
(704, 49, 95, '2026-01-27 13:51:00', '2026-01-27 13:51:00'),
(705, 56, 104, '2026-01-27 13:51:36', '2026-01-27 13:51:36'),
(706, 16, 47, '2026-01-27 13:56:32', '2026-01-27 14:00:06'),
(707, 2, 0, '2026-01-27 14:43:03', '2026-01-27 14:43:03'),
(708, 47, 92, '2026-01-27 16:08:26', '2026-01-27 16:08:26'),
(709, 4, 30, '2026-01-27 16:31:11', '2026-01-27 16:37:16'),
(710, 13, 42, '2026-01-27 23:28:17', '2026-01-27 23:31:18'),
(711, 2, 0, '2026-01-27 23:28:23', '2026-01-27 23:28:23'),
(712, 47, 92, '2026-01-28 09:35:54', '2026-01-28 09:35:54'),
(713, 2, 0, '2026-01-28 09:39:17', '2026-01-28 09:49:36'),
(714, 11, 40, '2026-01-28 09:49:44', '2026-01-28 09:51:44'),
(715, 2, 0, '2026-01-28 09:50:11', '2026-01-28 09:50:11'),
(716, 2, 0, '2026-01-28 09:51:50', '2026-01-28 09:55:06'),
(717, 46, 90, '2026-01-28 09:51:57', '2026-01-28 09:54:55'),
(718, 11, 40, '2026-01-28 09:52:53', '2026-01-28 09:53:46'),
(719, 35, 73, '2026-01-28 11:03:02', '2026-01-28 11:03:02'),
(720, 9, 36, '2026-01-28 12:18:29', '2026-01-28 12:18:29'),
(721, 2, 0, '2026-01-28 14:11:33', '2026-01-28 14:11:33'),
(722, 2, 0, '2026-01-28 14:35:42', '2026-01-28 14:37:52'),
(723, 2, 0, '2026-01-28 15:13:17', '2026-01-28 15:13:17'),
(724, 2, 0, '2026-01-28 15:55:22', '2026-01-28 15:56:31'),
(725, 58, 107, '2026-01-28 15:56:03', '2026-01-28 15:57:16'),
(726, 57, 106, '2026-01-28 15:57:36', '2026-01-28 15:58:56'),
(727, 51, 98, '2026-01-28 16:03:29', '2026-01-28 16:03:29'),
(728, 2, 0, '2026-01-28 18:08:47', '2026-01-28 18:30:47'),
(729, 58, 107, '2026-01-28 18:09:57', '2026-01-28 18:09:57'),
(730, 58, 107, '2026-01-28 18:10:21', '2026-01-28 18:10:30'),
(731, 60, 109, '2026-01-28 18:11:30', '2026-01-28 18:14:50'),
(732, 53, 101, '2026-01-28 18:15:05', '2026-01-28 18:20:59'),
(733, 50, 97, '2026-01-28 18:21:07', '2026-01-28 18:24:58'),
(734, 61, 110, '2026-01-28 18:25:11', '2026-01-28 18:26:00'),
(735, 2, 0, '2026-01-28 18:37:11', '2026-01-28 18:44:00'),
(736, 11, 40, '2026-01-28 19:04:01', '2026-01-28 19:04:01'),
(737, 65, 2, '2026-01-28 22:13:42', '2026-01-28 22:13:42'),
(738, 2, 0, '2026-01-28 22:13:54', '2026-01-28 22:15:55'),
(739, 65, 2, '2026-01-28 22:16:05', '2026-01-28 22:16:05'),
(740, 2, 0, '2026-01-28 22:17:11', '2026-01-28 22:18:34'),
(741, 65, 2, '2026-01-28 22:18:48', '2026-01-28 22:20:41'),
(742, 65, 2, '2026-01-28 22:20:52', '2026-01-28 22:21:17'),
(743, 65, 2, '2026-01-28 22:21:54', '2026-01-28 22:22:45'),
(744, 2, 0, '2026-01-28 22:22:55', '2026-01-28 22:25:02'),
(745, 65, 2, '2026-01-28 22:25:12', '2026-01-28 22:25:28'),
(746, 65, 2, '2026-01-28 22:27:22', '2026-01-28 22:28:34'),
(747, 65, 2, '2026-01-28 22:31:04', '2026-01-28 22:31:04'),
(748, 65, 2, '2026-01-28 22:33:55', '2026-01-28 22:44:11'),
(749, 65, 2, '2026-01-28 22:44:24', '2026-01-28 22:46:14'),
(750, 2, 0, '2026-01-28 22:46:58', '2026-01-28 22:46:58'),
(751, 49, 95, '2026-01-29 09:36:27', '2026-01-29 09:36:27'),
(752, 47, 92, '2026-01-29 09:41:25', '2026-01-29 09:41:25'),
(753, 46, 90, '2026-01-29 09:41:25', '2026-01-29 09:43:12'),
(754, 58, 107, '2026-01-29 09:52:56', '2026-01-29 09:52:56'),
(755, 56, 104, '2026-01-29 10:37:53', '2026-01-29 10:37:53'),
(756, 2, 0, '2026-01-29 10:49:10', '2026-01-29 10:49:29'),
(757, 56, 104, '2026-01-29 10:49:37', '2026-01-29 10:51:32'),
(758, 65, 2, '2026-01-29 10:52:00', '2026-01-29 10:53:26'),
(759, 34, 72, '2026-01-29 10:53:58', '2026-01-29 10:56:32'),
(760, 23, 57, '2026-01-29 10:57:15', '2026-01-29 10:57:15'),
(761, 65, 2, '2026-01-29 11:08:08', '2026-01-29 11:15:01'),
(762, 2, 0, '2026-01-29 11:15:12', '2026-01-29 11:15:25'),
(763, 65, 2, '2026-01-29 11:15:39', '2026-01-29 11:17:18'),
(764, 56, 104, '2026-01-29 11:17:28', '2026-01-29 11:17:55'),
(765, 49, 95, '2026-01-29 11:42:23', '2026-01-29 11:42:23'),
(766, 49, 95, '2026-01-29 11:55:25', '2026-01-29 11:55:25'),
(767, 23, 57, '2026-01-29 11:56:25', '2026-01-29 11:56:25'),
(768, 2, 0, '2026-01-29 11:57:41', '2026-01-29 11:58:21'),
(769, 65, 2, '2026-01-29 11:58:51', '2026-01-29 12:10:01'),
(770, 38, 76, '2026-01-29 12:11:14', '2026-01-29 12:15:07'),
(771, 2, 0, '2026-01-29 12:15:20', '2026-01-29 12:15:35'),
(772, 38, 76, '2026-01-29 12:15:46', '2026-01-29 12:16:00'),
(773, 65, 2, '2026-01-29 12:16:25', '2026-01-29 12:24:30'),
(774, 63, 123, '2026-01-29 12:22:05', '2026-01-29 12:50:54'),
(775, 38, 76, '2026-01-29 12:24:42', '2026-01-29 12:26:21'),
(776, 34, 72, '2026-01-29 12:46:32', '2026-01-29 12:50:46'),
(777, 58, 107, '2026-01-29 12:51:10', '2026-01-29 12:58:11'),
(778, 2, 0, '2026-01-29 13:54:56', '2026-01-29 13:55:29'),
(779, 65, 2, '2026-01-29 13:55:44', '2026-01-29 14:13:07'),
(780, 2, 0, '2026-01-29 14:13:18', '2026-01-29 14:13:33'),
(781, 63, 123, '2026-01-29 14:13:41', '2026-01-29 14:14:56'),
(782, 65, 2, '2026-01-29 14:15:09', '2026-01-29 14:15:22'),
(783, 63, 123, '2026-01-29 14:15:33', '2026-01-29 14:16:55'),
(784, 2, 0, '2026-01-29 14:18:20', '2026-01-29 14:30:16'),
(785, 2, 0, '2026-01-30 00:21:29', '2026-01-30 00:37:10'),
(786, 2, 0, '2026-01-30 08:27:59', '2026-01-30 08:29:50'),
(787, 65, 2, '2026-01-30 11:21:38', '2026-01-30 11:22:09'),
(788, 2, 0, '2026-01-30 11:22:52', '2026-01-30 11:23:33'),
(789, 4, 30, '2026-01-30 11:24:05', '2026-01-30 11:27:41'),
(790, 5, 31, '2026-01-30 11:28:08', '2026-01-30 11:30:26'),
(791, 2, 0, '2026-01-30 11:31:16', '2026-01-30 11:31:16'),
(792, 22, 56, '2026-01-30 14:52:33', '2026-01-30 14:52:33'),
(793, 59, 108, '2026-01-30 19:41:42', '2026-01-30 19:43:20'),
(794, 59, 108, '2026-01-30 19:43:32', '2026-01-30 19:48:56'),
(795, 7, 34, '2026-01-30 23:57:24', '2026-01-30 23:57:24'),
(796, 2, 0, '2026-01-31 00:30:36', '2026-01-31 00:40:36'),
(797, 2, 0, '2026-01-31 11:12:03', '2026-01-31 11:12:03'),
(798, 30, 68, '2026-01-31 11:38:01', '2026-01-31 11:40:00'),
(799, 30, 68, '2026-01-31 11:48:25', '2026-01-31 11:48:25'),
(800, 30, 68, '2026-01-31 16:37:38', '2026-01-31 16:48:11'),
(801, 30, 68, '2026-01-31 16:48:19', '2026-01-31 16:48:19'),
(802, 7, 34, '2026-01-31 22:40:00', '2026-01-31 22:48:58'),
(803, 7, 34, '2026-01-31 22:49:06', '2026-01-31 22:49:06'),
(804, 24, 59, '2026-02-01 11:02:10', '2026-02-01 11:02:10'),
(805, 65, 2, '2026-02-01 11:19:37', '2026-02-01 11:48:28'),
(806, 2, 0, '2026-02-01 11:48:40', '2026-02-01 11:59:55'),
(807, 45, 89, '2026-02-01 11:51:38', '2026-02-01 11:52:12'),
(808, 33, 71, '2026-02-01 11:52:22', '2026-02-01 11:54:33'),
(809, 30, 68, '2026-02-01 11:54:42', '2026-02-01 11:55:40'),
(810, 24, 59, '2026-02-01 11:57:13', '2026-02-01 11:57:25'),
(811, 19, 51, '2026-02-01 11:57:51', '2026-02-01 11:59:40'),
(812, 65, 2, '2026-02-01 12:00:09', '2026-02-01 12:01:35'),
(813, 30, 68, '2026-02-01 12:35:13', '2026-02-01 12:35:13'),
(814, 2, 0, '2026-02-01 13:53:22', '2026-02-01 13:53:35'),
(815, 41, 79, '2026-02-01 14:23:51', '2026-02-01 14:23:51'),
(816, 30, 68, '2026-02-01 14:27:59', '2026-02-01 14:27:59'),
(817, 65, 2, '2026-02-01 15:22:05', '2026-02-01 15:28:17'),
(818, 41, 79, '2026-02-01 15:49:23', '2026-02-01 15:49:23'),
(819, 51, 98, '2026-02-02 05:12:03', '2026-02-02 05:25:20'),
(820, 11, 40, '2026-02-02 12:28:01', '2026-02-02 12:29:19'),
(821, 3, 1, '2026-02-02 16:19:37', '2026-02-02 16:44:58'),
(822, 2, 0, '2026-02-02 18:38:02', '2026-02-02 18:38:02'),
(823, 2, 0, '2026-02-02 19:41:08', '2026-02-02 19:41:41'),
(824, 9, 36, '2026-02-02 19:41:47', '2026-02-02 19:42:06'),
(825, 2, 0, '2026-02-02 19:42:19', '2026-02-02 19:43:26'),
(826, 65, 2, '2026-02-02 19:43:39', '2026-02-02 19:44:47'),
(827, 7, 34, '2026-02-02 19:43:45', '2026-02-02 19:45:40'),
(828, 2, 0, '2026-02-02 19:44:59', '2026-02-02 19:49:17'),
(829, 63, 123, '2026-02-02 19:49:27', '2026-02-02 19:50:18'),
(830, 2, 0, '2026-02-02 19:50:31', '2026-02-02 19:51:24'),
(831, 63, 123, '2026-02-02 19:51:34', '2026-02-02 19:51:46'),
(832, 63, 123, '2026-02-02 19:52:05', '2026-02-02 19:53:08'),
(833, 2, 0, '2026-02-02 20:10:17', '2026-02-02 20:10:32'),
(834, 66, 130, '2026-02-02 20:10:44', '2026-02-02 20:10:51'),
(835, 65, 2, '2026-02-02 20:11:05', '2026-02-02 20:12:28'),
(836, 66, 130, '2026-02-02 20:12:47', '2026-02-02 20:13:33'),
(837, 65, 2, '2026-02-02 20:13:49', '2026-02-02 20:13:58'),
(838, 2, 0, '2026-02-02 20:25:35', '2026-02-02 20:25:55'),
(839, 2, 0, '2026-02-02 20:36:35', '2026-02-02 20:46:33'),
(840, 19, 51, '2026-02-03 15:59:54', '2026-02-03 15:59:54'),
(841, 3, 1, '2026-02-03 17:07:12', '2026-02-03 17:08:09'),
(842, 63, 123, '2026-02-03 17:34:24', '2026-02-03 17:34:24'),
(843, 2, 0, '2026-02-03 17:44:17', '2026-02-03 17:44:17'),
(844, 2, 0, '2026-02-03 23:31:46', '2026-02-03 23:32:29'),
(845, 63, 123, '2026-02-03 23:32:40', '2026-02-03 23:33:10'),
(846, 65, 2, '2026-02-03 23:33:23', '2026-02-03 23:36:53'),
(847, 63, 123, '2026-02-03 23:37:13', '2026-02-03 23:38:48'),
(848, 65, 2, '2026-02-03 23:39:03', '2026-02-03 23:46:19'),
(849, 9, 36, '2026-02-03 23:46:31', '2026-02-03 23:47:42'),
(850, 9, 36, '2026-02-03 23:48:42', '2026-02-03 23:48:56'),
(851, 2, 0, '2026-02-04 00:57:56', '2026-02-04 01:05:51'),
(852, 14, 45, '2026-02-04 16:12:10', '2026-02-04 16:12:10'),
(853, 2, 0, '2026-02-05 09:11:08', '2026-02-05 09:12:07'),
(854, 20, 52, '2026-02-05 09:12:16', '2026-02-05 09:19:04'),
(855, 65, 2, '2026-02-05 09:19:20', '2026-02-05 09:20:52'),
(856, 2, 0, '2026-02-05 09:21:05', '2026-02-05 09:22:21'),
(857, 49, 95, '2026-02-05 09:23:09', '2026-02-05 09:23:09'),
(858, 65, 2, '2026-02-05 09:28:36', '2026-02-05 09:31:12'),
(859, 2, 0, '2026-02-05 09:31:22', '2026-02-05 09:32:04'),
(860, 18, 50, '2026-02-05 09:32:14', '2026-02-05 09:35:43'),
(861, 65, 2, '2026-02-05 09:35:57', '2026-02-05 09:51:40'),
(862, 63, 123, '2026-02-05 09:51:50', '2026-02-05 09:52:08'),
(863, 20, 52, '2026-02-05 09:52:51', '2026-02-05 09:54:29'),
(864, 63, 123, '2026-02-05 10:22:19', '2026-02-05 10:30:18'),
(865, 52, 100, '2026-02-05 21:18:36', '2026-02-05 21:18:36'),
(866, 65, 2, '2026-02-06 00:54:44', '2026-02-06 01:11:32'),
(867, 28, 66, '2026-02-06 14:27:54', '2026-02-06 14:42:20'),
(868, 3, 1, '2026-02-06 20:16:55', '2026-02-06 20:18:55'),
(869, 26, 63, '2026-02-07 02:18:17', '2026-02-07 02:18:17'),
(870, 34, 72, '2026-02-07 10:37:59', '2026-02-07 10:40:57'),
(871, 14, 45, '2026-02-07 17:20:00', '2026-02-07 17:20:00'),
(872, 2, 0, '2026-02-08 09:35:33', '2026-02-08 09:35:48'),
(873, 14, 45, '2026-02-08 11:16:37', '2026-02-08 11:41:56'),
(874, 38, 76, '2026-02-08 12:12:40', '2026-02-08 12:12:50'),
(875, 2, 0, '2026-02-08 12:48:01', '2026-02-08 12:48:38'),
(876, 3, 1, '2026-02-08 12:48:52', '2026-02-08 12:51:09'),
(877, 68, 132, '2026-02-08 12:52:13', '2026-02-08 12:55:04'),
(878, 68, 132, '2026-02-08 12:55:26', '2026-02-08 12:55:26'),
(879, 52, 100, '2026-02-08 13:29:32', '2026-02-08 13:29:32'),
(880, 65, 2, '2026-02-08 14:55:27', '2026-02-08 14:56:09'),
(881, 52, 100, '2026-02-09 01:34:47', '2026-02-09 01:34:47'),
(882, 9, 36, '2026-02-09 10:36:12', '2026-02-09 10:36:12'),
(883, 2, 0, '2026-02-09 17:06:57', '2026-02-09 17:07:08'),
(884, 65, 2, '2026-02-09 17:07:21', '2026-02-09 17:10:53'),
(885, 69, 134, '2026-02-09 17:11:09', '2026-02-09 17:12:23'),
(886, 65, 2, '2026-02-09 17:12:37', '2026-02-09 17:13:05'),
(887, 69, 134, '2026-02-09 23:09:31', '2026-02-09 23:10:10'),
(888, 2, 0, '2026-02-09 23:10:28', '2026-02-09 23:10:42'),
(889, 69, 134, '2026-02-09 23:10:55', '2026-02-09 23:11:18'),
(890, 65, 2, '2026-02-09 23:11:32', '2026-02-09 23:12:58'),
(891, 69, 134, '2026-02-09 23:13:09', '2026-02-09 23:23:33'),
(892, 52, 100, '2026-02-10 01:59:03', '2026-02-10 01:59:03'),
(893, 68, 132, '2026-02-10 09:37:20', '2026-02-10 09:37:20'),
(894, 68, 132, '2026-02-10 11:35:26', '2026-02-10 11:35:26'),
(895, 65, 2, '2026-02-10 11:43:03', '2026-02-10 11:46:27'),
(896, 2, 0, '2026-02-10 11:46:37', '2026-02-10 11:46:48'),
(897, 68, 132, '2026-02-10 11:46:56', '2026-02-10 11:48:02'),
(898, 49, 95, '2026-02-10 11:47:18', '2026-02-10 11:47:18'),
(899, 26, 63, '2026-02-10 11:47:20', '2026-02-10 11:47:20'),
(900, 65, 2, '2026-02-10 11:48:33', '2026-02-10 11:48:51'),
(901, 68, 132, '2026-02-10 11:48:58', '2026-02-10 11:50:20'),
(902, 47, 92, '2026-02-10 11:58:19', '2026-02-10 11:58:19'),
(903, 26, 63, '2026-02-10 11:59:29', '2026-02-10 12:00:00'),
(904, 2, 0, '2026-02-10 12:00:10', '2026-02-10 12:00:21'),
(905, 26, 63, '2026-02-10 12:00:38', '2026-02-10 12:01:35'),
(906, 65, 2, '2026-02-10 12:12:51', '2026-02-10 12:15:04'),
(907, 26, 63, '2026-02-10 12:15:18', '2026-02-10 12:15:56'),
(908, 65, 2, '2026-02-10 12:16:09', '2026-02-10 12:16:17'),
(909, 26, 63, '2026-02-10 15:19:19', '2026-02-10 15:19:19'),
(910, 47, 92, '2026-02-10 15:47:43', '2026-02-10 15:47:43'),
(911, 2, 0, '2026-02-10 22:49:24', '2026-02-10 22:50:13'),
(912, 23, 57, '2026-02-10 22:50:24', '2026-02-10 22:50:46'),
(913, 2, 0, '2026-02-10 22:50:57', '2026-02-10 22:51:07'),
(914, 23, 57, '2026-02-10 22:51:17', '2026-02-10 22:51:39'),
(915, 65, 2, '2026-02-10 22:51:52', '2026-02-10 22:52:53'),
(916, 23, 57, '2026-02-10 22:53:07', '2026-02-10 22:53:36'),
(917, 2, 0, '2026-02-11 01:27:47', '2026-02-11 01:29:53'),
(918, 3, 1, '2026-02-11 01:30:03', '2026-02-11 01:35:33'),
(919, 2, 0, '2026-02-11 01:35:49', '2026-02-11 01:36:04'),
(920, 65, 2, '2026-02-11 19:29:58', '2026-02-11 19:35:51'),
(921, 2, 0, '2026-02-11 19:36:02', '2026-02-11 19:38:06'),
(922, 3, 1, '2026-02-11 19:38:20', '2026-02-11 19:42:03'),
(923, 69, 134, '2026-02-11 19:44:41', '2026-02-11 20:11:32'),
(924, 59, 108, '2026-02-11 20:25:09', '2026-02-11 20:25:09'),
(925, 65, 2, '2026-02-11 22:23:46', '2026-02-11 22:23:46'),
(926, 65, 2, '2026-02-13 15:22:36', '2026-02-13 15:22:36'),
(927, 26, 63, '2026-02-14 00:14:42', '2026-02-14 00:14:42'),
(928, 2, 0, '2026-02-15 10:38:12', '2026-02-15 10:40:04'),
(929, 65, 2, '2026-02-15 10:40:19', '2026-02-15 10:40:19'),
(930, 35, 73, '2026-02-15 10:49:20', '2026-02-15 10:58:00'),
(931, 49, 95, '2026-02-15 10:59:47', '2026-02-15 10:59:47'),
(932, 22, 56, '2026-02-15 15:21:46', '2026-02-15 15:24:10'),
(933, 2, 0, '2026-02-15 15:39:30', '2026-02-15 15:41:21'),
(934, 31, 69, '2026-02-15 15:41:40', '2026-02-15 15:42:24'),
(935, 31, 69, '2026-02-15 15:42:56', '2026-02-15 15:48:53'),
(936, 31, 69, '2026-02-15 16:34:09', '2026-02-15 16:35:44'),
(937, 31, 69, '2026-02-15 17:50:00', '2026-02-15 17:50:00'),
(938, 31, 69, '2026-02-15 18:51:15', '2026-02-15 18:56:25'),
(939, 65, 2, '2026-02-16 17:11:36', '2026-02-16 17:11:36'),
(940, 65, 2, '2026-02-16 18:14:16', '2026-02-16 18:14:16');

-- --------------------------------------------------------

--
-- Table structure for table `user_login`
--

CREATE TABLE `user_login` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `member_code` varchar(50) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `password` text NOT NULL,
  `re_password` varchar(100) NOT NULL,
  `role` varchar(10) NOT NULL,
  `status` varchar(2) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_login`
--

INSERT INTO `user_login` (`id`, `member_id`, `member_code`, `user_name`, `password`, `re_password`, `role`, `status`, `created_at`) VALUES
(2, 0, '', 'admin', '706f600bc94ff2aca61fa0f6ebde3fd0', 'admin#321!', 'Admin', 'A', '2025-08-15 19:33:39'),
(3, 1, 'CPSS-00001', '505646', 'c04c747a06eadb597d44f0a28988f057', 'S#01540a', 'user', 'P', '2025-10-13 21:31:57'),
(4, 30, 'CPSS-00002', 'coderap', '1aea74bc195721a1d71234022d6721b5', 'Ap#123456', 'user', 'P', '2025-10-13 22:28:41'),
(5, 31, 'CPSS-00031', 'codermk', '1aea74bc195721a1d71234022d6721b5', 'Ap#123456', 'user', 'P', '2025-10-13 22:36:03'),
(7, 34, 'CPSS-00032', 'Saiful1986', '7e45d41d3d93659f5d92a3f964502127', 'Saiful1986', 'user', 'P', '2025-10-14 16:04:29'),
(8, 35, 'CPSS-00035', 'iqbal@erainfotechbd.com', '374a4198e2f0f5797133c3d28e15d037', 'I*qbal123', 'user', 'P', '2025-10-14 16:51:59'),
(9, 36, 'CPSS-00036', 'sadin_027', '9c23bdafebf6d7a14ff2e0a7b1bc7590', 'sadin_027', 'user', 'P', '2025-10-14 18:48:46'),
(10, 39, 'CPSS-00037', 'ashiqur_ruhullah', '3b192651dafa9a384b45d15bdd551c3a', 'Abc@33688', 'user', 'P', '2025-10-16 16:35:26'),
(11, 40, 'CPSS-00040', 'tariqul@erainfotechbd.com', 'd82f0f7bbb5e126328c20590e8bd8bee', 'Tariqul*sorse4', 'user', 'P', '2025-10-16 17:32:03'),
(12, 41, 'CPSS-00041', 'Samrat0026@', '6d4a2c2688cbf0b68a09db10bf21c4f0', '25800', 'user', 'P', '2025-10-19 11:12:10'),
(13, 42, 'CPSS-00042', 'hera', 'cfed9928364182f874060d57295e305c', 'Her@1234', 'user', 'P', '2025-10-22 11:01:11'),
(14, 45, 'CPSS-00043', 'sraihan68', '90973b6d611e8a9d3e9d6d4c18d99991', 'Pass@123#', 'user', 'P', '2025-10-23 15:21:30'),
(15, 46, 'CPSS-00046', 'Md. Alauddin', '558d84d0650a49634714a42d46183114', 'Alauddin', 'user', 'R', '2025-10-23 16:19:51'),
(16, 47, 'CPSS-00047', 'moshid', '13053ed9d25f40b415e709f3023c09b5', '19822006Cm', 'user', 'P', '2025-10-23 22:02:11'),
(17, 48, 'CPSS-00048', 'mahdi', 'a80efaf2c72ee24985535815fc3f5380', 'Era_321', 'user', 'P', '2025-10-25 08:44:27'),
(18, 50, 'CPSS-00049', 'aslamtmela', '1553a90b66d50da03d9b082f3880e3ad', 'Aslam@038297', 'user', 'P', '2025-10-25 23:00:21'),
(19, 51, 'CPSS-00051', 'Sonia Afrose', 'df7c8d1964ca7cc8c13f002afa4864c7', '2468u', 'user', 'P', '2025-10-26 12:46:33'),
(20, 52, 'CPSS-00052', '504305', '375de4af22691d85fffa4a6f694106c7', '504305', 'user', 'P', '2025-10-26 16:20:08'),
(21, 55, 'CPSS-00053', 'Foisal Mahmud', '670a7db8e714ea9f6d43ca7b72e8cf09', 'Foisal@3734', 'user', 'R', '2025-10-27 12:41:48'),
(22, 56, 'CPSS-00056', 'MMHO', '08e3099eac683f3cb2ca7ffea4497f55', 'Fathermother#1988', 'user', 'P', '2025-10-27 18:43:51'),
(23, 57, 'CPSS-00057', 'ariful', '17278d31f3c882ee29cb095ed6f64cad', 'Ariful@2050#!', 'user', 'P', '2025-10-28 21:48:00'),
(24, 59, 'CPSS-00058', 'farzana', '136c9221f517422a68e09fed3051ecc7', 'f@rzana123', 'user', 'P', '2025-10-30 14:18:45'),
(25, 62, 'CPSS-00060', 'Ashiq971', '817327651ecb7f95d02faedb062eeace', '543368166', 'user', 'P', '2025-10-31 22:39:47'),
(26, 63, 'CPSS-00063', 'Sakif Abdullah', 'cd6d5b15c39e02a814e680ebb86b5c4b', 'Sakif@006', 'user', 'P', '2025-11-05 11:41:45'),
(27, 65, 'CPSS-00064', 'masuma', '81ed54d1b3b9b349dbd4a53e0c1728a8', 'masuma', 'user', 'P', '2025-11-08 16:08:49'),
(28, 66, 'CPSS-00066', 'ENAMUL', 'e69e89935e0fbcc2d0ff9862814dc425', 'Enamul95@', 'user', 'P', '2025-11-08 16:18:32'),
(29, 67, 'CPSS-00067', 'IAMENAMUL', 'e7b0c94344dd8342fbeb011f922547aa', 'IAMENAMUL', 'user', 'P', '2025-11-08 16:27:48'),
(30, 68, 'CPSS-00068', 'hakim', 'c96041081de85714712a79319cb2be5f', 'hakim', 'user', 'P', '2025-11-08 16:35:39'),
(31, 69, 'CPSS-00069', 'HASINA', '48418ffeb238206941cbb049516c313b', 'HASINA', 'user', 'P', '2025-11-08 16:47:57'),
(32, 70, 'CPSS-00070', 'SULTAN', 'f7a5eecd9c08c7af83d946b194b7488a', 'SULTAN', 'user', 'P', '2025-11-08 16:59:16'),
(33, 71, 'CPSS-00071', 'MAHAMUDA', '799d402acdd78f76a4bb9af7cff8ff74', 'MAHAMUDA', 'user', 'P', '2025-11-08 17:16:10'),
(34, 72, 'CPSS-00072', 'ashraf', '581023f2e035e041380715e0fb01f265', 'aJaMeSbOnD007', 'user', 'P', '2025-11-08 23:21:45'),
(35, 73, 'CPSS-00073', 'muktasib', '7f9d61be837f579fe253aff04db03202', 'sifinCoder@25', 'user', 'P', '2025-11-08 23:25:00'),
(36, 74, 'CPSS-00074', 'arifd014', '727aa52727c69c597d30ddea53cd5acc', 'Weblogic#2', 'user', 'P', '2025-11-08 23:25:35'),
(37, 75, 'CPSS-00075', 'fahim001', '727aa52727c69c597d30ddea53cd5acc', 'Weblogic#2', 'user', 'R', '2025-11-08 23:45:17'),
(38, 76, 'CPSS-00076', '01911977707', '19602395382d4cbd587df30430120145', 'Hasan@123', 'user', 'P', '2025-11-09 09:14:59'),
(39, 77, 'CPSS-00077', 'ashraful@yahoo.com', 'd9409ff64823e924dc08b9a7e0f2cdc6', 'Yasmin@#$123', 'user', 'P', '2025-11-09 09:52:07'),
(40, 78, 'CPSS-00078', 'zidanmehedi', 'fb0c524afe57c3348cb88c3bef7530f4', 'amaterasu5654', 'user', 'P', '2025-11-09 21:25:00'),
(41, 79, 'CPSS-00079', 'ZehanAsgarA', '6c7d2810f3ad1eb835c720753c3128aa', 'zehanA1507', 'user', 'P', '2025-11-10 12:53:35'),
(42, 82, 'CPSS-00080', 'ruhulamin34@gmail.com', '04cb7b29d00ec6738fef8c4c3c11c426', '374234', 'user', 'I', '2025-11-14 22:37:17'),
(43, 85, 'CPSS-00083', 'masuma', '332a5b84a9ff0256bc8bef370738e57d', 'kidorkar', 'user', 'P', '2025-11-19 17:11:52'),
(45, 89, 'CPSS-00086', 'tanjina', '81ce88d6d1730234459b1a949ecb9874', 'Samity#2025', 'user', 'P', '2025-11-22 12:30:10'),
(46, 90, 'CPSS-00090', 'rhm1983', 'b43f68ed75fd91ca9127c7684ddf27be', 'RHM@rhm@1983', 'user', 'P', '2025-11-25 17:51:19'),
(47, 92, 'CPSS-00091', 'Imtiaz', '24e182b58b86c66bccc8ea2c7c8fdb35', 'abir_24308', 'user', 'P', '2025-11-26 12:06:46'),
(48, 93, 'CPSS-00093', 'Jahid', '81dc9bdb52d04dc20036dbd8313ed055', '1234', 'user', 'P', '2025-11-27 12:41:40'),
(49, 95, 'CPSS-00094', 'nazmul_haque', '2701a43c483d9686522b89244c091d7a', '01626100302', 'user', 'P', '2025-11-27 23:09:16'),
(50, 97, 'CPSS-00096', 'Ziaur Rahman', 'ae3d7b924282c6b8f6fed8222ce950f8', '161020', 'user', 'P', '2025-11-27 23:40:10'),
(51, 98, 'CPSS-00098', 'raju1705', 'fe99751b5ff352f2a4bb26a8d1549ecc', 'Najmul1705@', 'user', 'P', '2025-11-28 18:34:08'),
(52, 100, 'CPSS-00099', 'khandakar.adnan', '3caa8c0bf483d24e4e58fdd69529bdf3', '@Ab&Sham@adn02adr03', 'user', 'P', '2025-11-28 21:04:04'),
(53, 101, 'CPSS-00101', 'hrithik', '9c2d287109b312c07c0c9d093fd84ac6', 'Destroyer2712#', 'user', 'P', '2025-11-28 22:41:00'),
(54, 102, 'CPSS-00102', 'hituhin09@gmail.com', '3f12a9866bdbe4259a9f3ecd1e876267', 'Sheikhtuhin@09', 'user', 'P', '2025-11-28 22:54:13'),
(55, 103, 'CPSS-00103', 'mamun', '3934a0609f9b542e8ae71d591a571a2a', 'Mam@647410', 'user', 'P', '2025-11-29 17:53:34'),
(56, 104, 'CPSS-00104', 'hasib', 'efc17d25ad6b1319a0b07ddafa0c134a', 'Rajbari@123!!', 'user', 'P', '2025-11-29 18:56:00'),
(57, 106, 'CPSS-00105', 'tawfiq', 'e0f09c61c07c60adcbdc44fa0efda8d4', 'Ab#123456', 'user', 'P', '2025-11-29 22:07:29'),
(58, 107, 'CPSS-00107', 'tarik', 'e88eeb83a5f88905cce9f6d14b926a07', 'Antim@1999', 'user', 'P', '2025-11-30 10:44:32'),
(59, 108, 'CPSS-00108', 'alhadi', '5c4d27f49d5664d9210fe712b4cb1e02', 'alhadi666', 'user', 'P', '2025-12-01 10:48:54'),
(60, 109, 'CPSS-00109', 'Didar', '364a2111f8e12782adde750952cd789f', 'Did@r2016', 'user', 'P', '2025-12-01 15:37:38'),
(61, 110, 'CPSS-00110', 'rishat', '32c01a8b9975b275e241b2ed96d5dbfb', 'Rishat@2025', 'user', 'P', '2025-12-01 22:59:47'),
(62, 112, 'CPSS-00111', 'rano', '3ad7fdd561fbcb3cade725ff088f899f', 'rano@01810', 'user', 'P', '2025-12-04 09:40:18'),
(63, 123, 'CPSS-00113', 'ripon', '32bc1224fcea6153b13e61306d00ec5b', 'ripon123', 'user', 'P', '2026-01-04 15:55:37'),
(65, 2, '', 'account', 'a8393be1dbac23e874d835cd688974c4', 'account#123!', 'Account', 'A', '2026-01-28 22:11:56'),
(66, 130, 'CPSS-00124', 'shahrin', '409059d527d1f320a42e85c82d1ef509', 'shahrin', 'user', 'P', '2026-02-02 20:09:57'),
(67, 131, 'CPSS-00131', 'atiq', '4089c6c194e9386d8e75cc9be4a2f951', 'atiq', 'user', 'P', '2026-02-02 20:24:49'),
(68, 132, 'CPSS-00132', 'mak.azad.79@gmail.com', 'f91e15dbec69fc40f81f0876e7009648', 'Pass@123', 'user', 'P', '2026-02-08 12:32:37'),
(69, 134, 'CPSS-00133', 'arif493', '7367d15912117326067a1cde517e3d6e', 'arif493', 'user', 'P', '2026-02-09 17:06:40');

-- --------------------------------------------------------

--
-- Table structure for table `utils`
--

CREATE TABLE `utils` (
  `id` int(11) NOT NULL,
  `type_name_bn` varchar(255) NOT NULL,
  `fee` int(11) NOT NULL,
  `status` varchar(3) NOT NULL,
  `fee_type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utils`
--

INSERT INTO `utils` (`id`, `type_name_bn`, `fee`, `status`, `fee_type`) VALUES
(1, 'এন্ট্রি ফি', 1500, 'A', 'admission'),
(2, 'মাসিক কিস্তি', 2000, 'A', 'monthly'),
(3, 'বিলম্ব ফি', 200, 'A', 'late'),
(4, 'সমিতি শেয়ার', 5000, 'A', 'samity_share'),
(5, 'প্রকল্প শেয়ার', 5000, 'A', 'project_share');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_close`
--
ALTER TABLE `account_close`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `committee_member`
--
ALTER TABLE `committee_member`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `committee_role`
--
ALTER TABLE `committee_role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_category`
--
ALTER TABLE `expense_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `glac_mst`
--
ALTER TABLE `glac_mst`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gl_mapping`
--
ALTER TABLE `gl_mapping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gl_summary`
--
ALTER TABLE `gl_summary`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gl_transaction`
--
ALTER TABLE `gl_transaction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meeting`
--
ALTER TABLE `meeting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members_info`
--
ALTER TABLE `members_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_bank`
--
ALTER TABLE `member_bank`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_documents`
--
ALTER TABLE `member_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_nominee`
--
ALTER TABLE `member_nominee`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_office`
--
ALTER TABLE `member_office`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_payments`
--
ALTER TABLE `member_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_project`
--
ALTER TABLE `member_project`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_share`
--
ALTER TABLE `member_share`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_share`
--
ALTER TABLE `project_share`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setup`
--
ALTER TABLE `setup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `share`
--
ALTER TABLE `share`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_access`
--
ALTER TABLE `user_access`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_login`
--
ALTER TABLE `user_login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `utils`
--
ALTER TABLE `utils`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_close`
--
ALTER TABLE `account_close`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `committee_member`
--
ALTER TABLE `committee_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `committee_role`
--
ALTER TABLE `committee_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_category`
--
ALTER TABLE `expense_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `glac_mst`
--
ALTER TABLE `glac_mst`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `gl_mapping`
--
ALTER TABLE `gl_mapping`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `gl_summary`
--
ALTER TABLE `gl_summary`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gl_transaction`
--
ALTER TABLE `gl_transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meeting`
--
ALTER TABLE `meeting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `members_info`
--
ALTER TABLE `members_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `member_bank`
--
ALTER TABLE `member_bank`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `member_documents`
--
ALTER TABLE `member_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `member_nominee`
--
ALTER TABLE `member_nominee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `member_office`
--
ALTER TABLE `member_office`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `member_payments`
--
ALTER TABLE `member_payments`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=390;

--
-- AUTO_INCREMENT for table `member_project`
--
ALTER TABLE `member_project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `member_share`
--
ALTER TABLE `member_share`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `project_share`
--
ALTER TABLE `project_share`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=711;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `setup`
--
ALTER TABLE `setup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `share`
--
ALTER TABLE `share`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user_access`
--
ALTER TABLE `user_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=941;

--
-- AUTO_INCREMENT for table `user_login`
--
ALTER TABLE `user_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `utils`
--
ALTER TABLE `utils`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
