-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 29, 2025 at 03:53 PM
-- Server version: 11.4.8-MariaDB
-- PHP Version: 8.4.13

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
(4, 'ব্যানার-৪', 'Banner-4', 'banner_1759411534_5124.png', '2025-10-02 13:25:34', 'ban');

-- --------------------------------------------------------

--
-- Table structure for table `committee_member`
--

CREATE TABLE `committee_member` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `member_code` varchar(50) NOT NULL,
  `position` varchar(50) NOT NULL,
  `fb` varchar(100) NOT NULL,
  `li` varchar(100) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(41, 'CPSS-00041', 'মোঃ ইমতিয়াজ হাসান', 'MDIMTIAZ HASAN', 'মরহুম বাদল আক্তার', 'মমতাজ আক্তার', '3255768222', '1986-11-10', 'ইসলাম', 'Married', 'সৌয়দা নাজমা আক্তার', '01685092236', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00041/profile_image_1760850730_68f4732ab7bc8.jpg', '2025-10-19 05:12:10', 'D005', NULL, NULL),
(42, 'CPSS-00042', 'সিরাজুল ইসলাম', 'SHIRAJUL ISLAM', 'Md. Shamsul Haque', 'Rushanara Haque', '5960104221', '1985-01-22', 'ইসলাম', 'Married', 'Sajia Afrin Akhi', '01919787839', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00042/profile_image_1761109271_68f86517cc147.jpg', '2025-10-22 05:01:11', 'CPSS-00042', NULL, NULL),
(45, 'CPSS-00043', 'মোহা: সুমরিয়া রাইহান', 'MD SUMRIA RAIHAN', 'Md. Abdur Rashid', 'Mst. Sorifa', '7793619235', '1984-11-30', 'ইসলাম', 'Married', 'Mst. Mahamuda Khatun', '01716035300', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00043/profile_image_1761211289_68f9f399f3dd1.jpg', '2025-10-23 09:21:29', 'Anwar Parvez', 'sraihan68@gmail.com', 'OM'),
(46, 'CPSS-00046', 'মোঃ আলাউদ্দিন', 'MD ALAUDDIN', 'Late Md. Borhan uddin', 'Late Hasina Begum', '2806165631', '1983-02-01', 'ইসলাম', 'Married', 'Luna Laila', '01904119216', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00046/profile_image_1761214791_68fa0147decdf.jpg', '2025-10-23 10:19:51', 'Md. Anwar Parvez', 'alauddin@erainfotechbd.com', 'OM'),
(47, 'CPSS-00047', 'মোঃ মসিদুজ্জামান', 'MD MOSHIDUZZAMAN', 'MD MONIRUZZAMAN', 'SHEFALI KHANOM', '1016014779', '1982-02-01', 'ইসলাম', 'Married', 'Asma Akter', '01716712046', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00047/profile_image_1761235331_68fa5183a41c7.png', '2025-10-23 16:02:11', 'Shirajul Islam', 'moshidchamak@gmail.com', 'MP'),
(48, 'CPSS-00048', 'মাহ্দী মোহাম্মাদ', 'MAHDI MOHAMMAD', 'Md. Mobarak Hossain', 'Hasna Hena', '7300400459', '1994-01-01', 'ইসলাম', 'Married', 'Ummay Kulsom', '01990859786', 'Male', 'স্নাতক/সমমান', 1, 'user_images/member_CPSS-00048/profile_image_1761360267_68fc398b371d8.jpg', '2025-10-25 02:44:27', 'Anwar Parvez', 'mahdi174@gmail.com', 'MP'),
(50, 'CPSS-00049', 'আসলাম হোসাইন', 'MD ASLAM HOSSAIN', 'Abdul Aziz', 'Most Morjina Khatun', '6888476634', '1984-10-07', 'ইসলাম', 'Married', '', '01917074634', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00049/profile_image_1761411621_68fd0225365bc.jpg', '2025-10-25 17:00:21', 'Aslam', 'aslamrp07@gmail.com', 'MP'),
(51, 'CPSS-00051', 'সোনিয়া আফরোজ', 'SONIA AFROSE', 'Mohammad Hanif', 'Masuda Begum', '9143519586', '1985-06-05', 'ইসলাম', 'Married', 'Md. Abul Hassan', '01904119219', 'Female', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00051/profile_image_1761461193_68fdc3c900a64.jpg', '2025-10-26 06:46:33', 'Anwar Parvez', 'sonia@erainfotechbd.com', 'OM'),
(52, 'CPSS-00052', 'ফারহানা শিরিন', 'FARHANA SHIRIN', 'মোঃ ফজলুল হক মোল্লা', 'সেলিনা আক্তার', '3255751624', '1988-10-21', 'ইসলাম', 'Married', 'শাহ মোঃ আশফাক রহমান', '01912504305', 'Female', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00052/profile_image_1761474008_68fdf5d89e2e5.jpg', '2025-10-26 10:20:08', 'D005', '', 'MP'),
(55, 'CPSS-00053', 'ফয়সাল মাহমুদ', 'FOISAL MAHMUD', 'HABIBUR RAHMAN', 'SHAMIMA SHULTANA', '6412558576', '1992-08-08', 'ইসলাম', 'Married', 'PAPIATUL ZANNAT', '01672443734', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00053/profile_image_1761547308_68ff142c88859.jpg', '2025-10-27 06:41:48', 'Md. Aslam Hossain', 'foisalmahmud34@gmail.com', 'MP'),
(56, 'CPSS-00056', 'মোঃ মোশারফ হোসেন', 'MD MOSHAROF HOSSAN', 'Md SIRAJUL ISLAM', 'RAMESA KHATUN', '8680630749', '1988-10-20', 'ইসলাম', 'Married', 'SABICUN NAHAR', '01722276090', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00056/profile_image_1761569031_68ff6907f06d4.jpg', '2025-10-27 12:43:51', 'CPSS-00056', 'mithumosharof@gmail.com', 'MP'),
(57, 'CPSS-00057', 'মোঃ আরিফুল ইসলাম', 'MD ARIFUL ISLAM', 'Md. Shahidul Islam', 'Jahanara Begum', '1025555564', '1986-09-04', 'ইসলাম', 'Married', 'Syeda Tanjila', '01717819612', 'Male', 'স্নাতকোত্তর/সমমান', 1, 'user_images/member_CPSS-00057/profile_image_1761666480_6900e5b0e64d2.jpeg', '2025-10-28 15:48:00', 'CPSS-00057', 'arifultonu007@gmail.com', 'MP');

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
(54, 57, 'CPSS-00057', 'Syeda Tanjila', 'WIfe', '7315858352', '1988-02-01 00:00:00', 100, 'user_images/member_CPSS-00057/nominee_1_1761666480_6900e5b0e78c7.png', '2025-10-28 21:48:00');

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
(55, 57, 'CPSS-00057', 'Walton', 'Plot-1088, Block-I, Sabrina Sobhan Road P.O-Khilkhet, P.S-Vatara, Bashundhara R/A', 'Deputy Operative Director', '2025-10-28 21:48:00', 'House No. 416, Road No. 7, Block D, Bashundhara RA', '17/D, Jahanara Garden, Housing State, Goalchamot, Faridpur');

-- --------------------------------------------------------

--
-- Table structure for table `member_payments`
--

CREATE TABLE `member_payments` (
  `id` bigint(20) NOT NULL,
  `member_id` bigint(20) NOT NULL,
  `member_code` varchar(50) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `bank_pay_date` date NOT NULL,
  `bank_trans_no` varchar(100) NOT NULL,
  `trans_no` varchar(100) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_year` bigint(20) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `serial_no` int(11) DEFAULT NULL,
  `for_fees` varchar(20) DEFAULT NULL,
  `payment_slip` text DEFAULT NULL,
  `status` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member_share`
--

CREATE TABLE `member_share` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `member_code` varchar(50) NOT NULL,
  `no_share` int(11) NOT NULL,
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
  `share_amount` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member_share`
--

INSERT INTO `member_share` (`id`, `member_id`, `member_code`, `no_share`, `admission_fee`, `idcard_fee`, `passbook_fee`, `softuses_fee`, `sms_fee`, `office_rent`, `office_staff`, `other_fee`, `for_install`, `project_id`, `extra_share`, `share_amount`, `created_at`) VALUES
(1, 1, 'CPSS-00001', 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-13 21:31:57'),
(19, 30, 'CPSS-00002', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-13 22:28:41'),
(20, 31, 'CPSS-00031', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-13 22:36:03'),
(22, 34, 'CPSS-00032', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-14 16:04:29'),
(23, 35, 'CPSS-00035', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-14 16:51:59'),
(24, 36, 'CPSS-00036', 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-14 18:48:46'),
(25, 39, 'CPSS-00037', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-16 16:35:26'),
(26, 40, 'CPSS-00040', 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-16 17:32:03'),
(27, 41, 'CPSS-00041', 2, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, '2025-10-19 11:12:10'),
(28, 42, 'CPSS-00042', 40, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, '2025-10-22 11:01:11'),
(29, 45, 'CPSS-00043', 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 0, 0, '2025-10-23 15:21:30'),
(30, 46, 'CPSS-00046', 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 0, 0, '2025-10-23 16:19:51'),
(31, 47, 'CPSS-00047', 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 38, 0, '2025-10-23 22:02:11'),
(32, 48, 'CPSS-00048', 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 8, 0, '2025-10-25 08:44:27'),
(33, 50, 'CPSS-00049', 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 0, 0, '2025-10-25 23:00:21'),
(34, 51, 'CPSS-00051', 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 0, 0, '2025-10-26 12:46:33'),
(35, 52, 'CPSS-00052', 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 0, 0, '2025-10-26 16:20:08'),
(36, 55, 'CPSS-00053', 4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 2, 0, '2025-10-27 12:41:48'),
(37, 56, 'CPSS-00056', 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 0, 0, '2025-10-27 18:43:51'),
(38, 57, 'CPSS-00057', 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 8, 0, '2025-10-28 21:48:00');

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `id` int(11) NOT NULL,
  `project_name_bn` text NOT NULL,
  `project_name_en` text NOT NULL,
  `about_project` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`id`, `project_name_bn`, `project_name_en`, `about_project`) VALUES
(4, 'প্রজেক্ট-১', 'Project-1', '<p>ইকবাল ভাইয়ের ৬ শতাংশ জমি</p>');

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
(1, 'কোডার পেশাজীবী সমবায় সমিতি লিঃ (প্রস্তাবিত)', 'Coder Peshajibi Samabay Samity Ltd. (Proposed)', '২৫৫৩৫৮', '10/A-3, (7th Floor), Bardhan Bari, Darus Salam Thana, Mirpur-1, Dhaka-1216 - ( ১০/এ-৩, ( ৮ম তলা ) বর্ধন বাড়ি, দারুস সালাম থানা, মিরপুর-১, ঢাকা )', 'codersamity@gmail.com', '01540505646', '01919787839', 'কোডার পেশাজীবী সমবায় সমিতি লিঃ একটি স্বেচ্ছাসেবী, পেশাজীবী ও অরাজনৈতিক প্রতিষ্ঠান, যাহা ২০২৩ইং সালে প্রতিষ্ঠা করা হয়েছে এবং ২০২৫ইং সালে বাংলাদেশ সমবায় অধিদপ্তরে নিবন্ধন প্রক্রিয়া চলমান আছে, যাহার ফাইল নং- ২৫৫৩৫৮। &nbsp;আমাদের লক্ষ্য হলো পেশাজীবীদের মধ্যে সহযোগিতা বৃদ্ধি করা এবং তাদের পেশাগত ও আর্থিক উন্নয়ন সাধনে কাজ &nbsp;করা। আমরা বিভিন্ন প্রশিক্ষণ, কর্মশালা ও সেমিনার আয়োজন করি যাতে সদস্যরা তাদের দক্ষতা বৃদ্ধি করতে পারে এবং পেশাগত জীবনে সফল হতে পারে। আমাদের সদস্যরা বিভিন্ন পেশাগত ক্ষেত্রে কাজ করে এবং আমরা তাদের মধ্যে জ্ঞান ও অভিজ্ঞতা বিনিময় করি। সমিতির সদস্যদের জন্য একটি শক্তিশালী আর্থিক এবং পেশাদার প্ল্যাটফর্ম তৈরি করা, যেখানে সদস্যরা যৌথভাবে বিনিয়োগ করে, ব্যবসা পরিচালনা করে এবং মুনাফা ভাগাভাগি করতে পারে। আমরা বিশ্বাস করি যে, সহযোগিতা ও সমবায় মূলক কাজের মাধ্যমে আমরা আমাদের লক্ষ্য অর্জন করতে পারব এবং আমাদের সদস্যদের জন্য একটি উন্নত ও সমৃদ্ধ ভবিষ্যত গড়ে তুলতে পারব।', 'Coder Peshajibi Samabay Samity Ltd. is a voluntary, professional and non-political organization, which was established in 2023 and is in the process of registration with the Bangladesh Cooperatives Department in 2025, whose file no. is 255358. Our goal is to increase cooperation among professionals and work towards their professional and financial development. We organize various trainings, workshops and seminars so that members can enhance their skills and be successful in their professional lives. Our members work in different professional fields and we exchange knowledge and experience among them. To create a strong financial and professional platform for the members of the association, where members can jointly invest, run businesses and share profits. We believe that through cooperation and cooperative work, we can achieve our goals and build a better and prosperous future for our members.', 'একসাথে যেতে হবে বহুদূরে...', 'We have to go far together...', 'মোঃ সাইফুর রহমান ও সিরাজুল ইসলাম (Md. Saifur Rahman and Sirajul Islam)', '৫০৩১১০১০২৬৫ (50311010265)', 'logo.png', '<ul><li>hello</li><li>bangladesh</li></ul>', 'https://www.facebook.com/profile.php?id=61581789144846', 'youtube.com', 'linkedin.com', 'instagram.com', 'ব্যাংক এশিয়া লিঃ (Bank Asia Ltd.)', 'পুরানা পল্টন, ঢাকা-১০০০ (Purana Paltan, Dhaka-1000)');

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
(166, 2, 0, '2025-10-29 15:35:45', '2025-10-29 15:35:45');

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
(2, 0, '', 'admin', 'e10adc3949ba59abbe56e057f20f883e', '123456', 'Admin', 'A', '2025-08-15 19:33:39'),
(3, 1, 'CPSS-00001', '505646', 'c04c747a06eadb597d44f0a28988f057', 'S#01540a', 'user', 'P', '2025-10-13 21:31:57'),
(4, 30, 'CPSS-00002', 'coderap', '1aea74bc195721a1d71234022d6721b5', 'Ap#123456', 'user', 'I', '2025-10-13 22:28:41'),
(5, 31, 'CPSS-00031', 'codermk', '1aea74bc195721a1d71234022d6721b5', 'Ap#123456', 'user', 'I', '2025-10-13 22:36:03'),
(7, 34, 'CPSS-00032', 'Saiful1986', '7e45d41d3d93659f5d92a3f964502127', 'Saiful1986', 'user', 'I', '2025-10-14 16:04:29'),
(8, 35, 'CPSS-00035', 'iqbal@erainfotechbd.com', '374a4198e2f0f5797133c3d28e15d037', 'I*qbal123', 'user', 'I', '2025-10-14 16:51:59'),
(9, 36, 'CPSS-00036', 'sadin_027', '9c23bdafebf6d7a14ff2e0a7b1bc7590', 'sadin_027', 'user', 'I', '2025-10-14 18:48:46'),
(10, 39, 'CPSS-00037', 'ashiqur_ruhullah', '3b192651dafa9a384b45d15bdd551c3a', 'Abc@33688', 'user', 'I', '2025-10-16 16:35:26'),
(11, 40, 'CPSS-00040', 'tariqul@erainfotechbd.com', 'd82f0f7bbb5e126328c20590e8bd8bee', 'Tariqul*sorse4', 'user', 'I', '2025-10-16 17:32:03'),
(12, 41, 'CPSS-00041', 'Samrat0026@', '6d4a2c2688cbf0b68a09db10bf21c4f0', '25800', 'user', 'I', '2025-10-19 11:12:10'),
(13, 42, 'CPSS-00042', 'hera', 'cfed9928364182f874060d57295e305c', 'Her@1234', 'user', 'I', '2025-10-22 11:01:11'),
(14, 45, 'CPSS-00043', 'sraihan68', '90973b6d611e8a9d3e9d6d4c18d99991', 'Pass@123#', 'user', 'I', '2025-10-23 15:21:30'),
(15, 46, 'CPSS-00046', 'Md. Alauddin', '558d84d0650a49634714a42d46183114', 'Alauddin', 'user', 'I', '2025-10-23 16:19:51'),
(16, 47, 'CPSS-00047', 'moshid', '13053ed9d25f40b415e709f3023c09b5', '19822006Cm', 'user', 'I', '2025-10-23 22:02:11'),
(17, 48, 'CPSS-00048', 'mahdi', 'a80efaf2c72ee24985535815fc3f5380', 'Era_321', 'user', 'I', '2025-10-25 08:44:27'),
(18, 50, 'CPSS-00049', 'aslamtmela', '1553a90b66d50da03d9b082f3880e3ad', 'Aslam@038297', 'user', 'I', '2025-10-25 23:00:21'),
(19, 51, 'CPSS-00051', 'Sonia Afrose', 'df7c8d1964ca7cc8c13f002afa4864c7', '2468u', 'user', 'I', '2025-10-26 12:46:33'),
(20, 52, 'CPSS-00052', '504305', '375de4af22691d85fffa4a6f694106c7', '504305', 'user', 'I', '2025-10-26 16:20:08'),
(21, 55, 'CPSS-00053', 'Foisal Mahmud', '670a7db8e714ea9f6d43ca7b72e8cf09', 'Foisal@3734', 'user', 'I', '2025-10-27 12:41:48'),
(22, 56, 'CPSS-00056', 'MMHO', '08e3099eac683f3cb2ca7ffea4497f55', 'Fathermother#1988', 'user', 'I', '2025-10-27 18:43:51'),
(23, 57, 'CPSS-00057', 'ariful', '17278d31f3c882ee29cb095ed6f64cad', 'Ariful@2050#!', 'user', 'I', '2025-10-28 21:48:00');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members_info`
--
ALTER TABLE `members_info`
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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `committee_member`
--
ALTER TABLE `committee_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `members_info`
--
ALTER TABLE `members_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `member_documents`
--
ALTER TABLE `member_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member_nominee`
--
ALTER TABLE `member_nominee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `member_office`
--
ALTER TABLE `member_office`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `member_payments`
--
ALTER TABLE `member_payments`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member_share`
--
ALTER TABLE `member_share`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT for table `user_access`
--
ALTER TABLE `user_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;

--
-- AUTO_INCREMENT for table `user_login`
--
ALTER TABLE `user_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
