-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 31, 2026 at 08:18 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cbt_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-app_settings', 'a:13:{s:11:\"school_name\";s:11:\"Little Bird\";s:12:\"school_motto\";s:23:\"Excellence in Education\";s:14:\"school_address\";s:23:\"12, Bells Street, Lagos\";s:12:\"school_phone\";s:12:\"080222333443\";s:12:\"school_email\";s:12:\"bird@cbt.com\";s:11:\"school_logo\";s:45:\"uploads/settings/1792240302_6ad36aae939e7.jpg\";s:18:\"landing_page_title\";s:25:\"Welcome to Our CBT System\";s:24:\"landing_page_description\";s:70:\"A modern computer-based testing platform for schools and institutions.\";s:11:\"footer_text\";s:40:\"© 2026 CBT System. All rights reserved.\";s:13:\"theme_primary\";s:7:\"#1038b2\";s:15:\"theme_secondary\";s:7:\"#1cc88a\";s:19:\"theme_report_header\";s:7:\"#1a6489\";s:17:\"theme_report_text\";s:7:\"#ffffff\";}', 2107600303),
('laravel-cache-default_grading_scale', 'O:23:\"App\\Models\\GradingScale\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:14:\"grading_scales\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:7:{s:2:\"id\";i:1;s:4:\"name\";s:22:\"Standard Grading Scale\";s:11:\"description\";s:26:\"Default A-F grading system\";s:10:\"is_default\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-10-14 15:25:47\";s:10:\"updated_at\";s:19:\"2026-10-14 15:25:47\";}s:11:\"\0*\0original\";a:7:{s:2:\"id\";i:1;s:4:\"name\";s:22:\"Standard Grading Scale\";s:11:\"description\";s:26:\"Default A-F grading system\";s:10:\"is_default\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-10-14 15:25:47\";s:10:\"updated_at\";s:19:\"2026-10-14 15:25:47\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"is_default\";s:7:\"boolean\";s:9:\"is_active\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:5:\"items\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:6:{i:0;O:27:\"App\\Models\\GradingScaleItem\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:19:\"grading_scale_items\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:1;s:16:\"grading_scale_id\";i:1;s:5:\"grade\";s:1:\"A\";s:9:\"min_score\";s:5:\"80.00\";s:9:\"max_score\";s:6:\"100.00\";s:6:\"remark\";s:9:\"Excellent\";s:5:\"color\";s:7:\"success\";s:5:\"order\";i:0;s:10:\"created_at\";s:19:\"2026-10-14 15:25:47\";s:10:\"updated_at\";s:19:\"2026-10-14 15:25:47\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:1;s:16:\"grading_scale_id\";i:1;s:5:\"grade\";s:1:\"A\";s:9:\"min_score\";s:5:\"80.00\";s:9:\"max_score\";s:6:\"100.00\";s:6:\"remark\";s:9:\"Excellent\";s:5:\"color\";s:7:\"success\";s:5:\"order\";i:0;s:10:\"created_at\";s:19:\"2026-10-14 15:25:47\";s:10:\"updated_at\";s:19:\"2026-10-14 15:25:47\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:9:\"min_score\";s:9:\"decimal:2\";s:9:\"max_score\";s:9:\"decimal:2\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:16:\"grading_scale_id\";i:1;s:5:\"grade\";i:2;s:9:\"min_score\";i:3;s:9:\"max_score\";i:4;s:6:\"remark\";i:5;s:5:\"color\";i:6;s:5:\"order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:27:\"App\\Models\\GradingScaleItem\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:19:\"grading_scale_items\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:2;s:16:\"grading_scale_id\";i:1;s:5:\"grade\";s:1:\"B\";s:9:\"min_score\";s:5:\"70.00\";s:9:\"max_score\";s:5:\"79.99\";s:6:\"remark\";s:9:\"Very Good\";s:5:\"color\";s:7:\"primary\";s:5:\"order\";i:1;s:10:\"created_at\";s:19:\"2026-10-14 15:25:47\";s:10:\"updated_at\";s:19:\"2026-10-14 15:25:47\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:2;s:16:\"grading_scale_id\";i:1;s:5:\"grade\";s:1:\"B\";s:9:\"min_score\";s:5:\"70.00\";s:9:\"max_score\";s:5:\"79.99\";s:6:\"remark\";s:9:\"Very Good\";s:5:\"color\";s:7:\"primary\";s:5:\"order\";i:1;s:10:\"created_at\";s:19:\"2026-10-14 15:25:47\";s:10:\"updated_at\";s:19:\"2026-10-14 15:25:47\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:9:\"min_score\";s:9:\"decimal:2\";s:9:\"max_score\";s:9:\"decimal:2\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:16:\"grading_scale_id\";i:1;s:5:\"grade\";i:2;s:9:\"min_score\";i:3;s:9:\"max_score\";i:4;s:6:\"remark\";i:5;s:5:\"color\";i:6;s:5:\"order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:27:\"App\\Models\\GradingScaleItem\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:19:\"grading_scale_items\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:3;s:16:\"grading_scale_id\";i:1;s:5:\"grade\";s:1:\"C\";s:9:\"min_score\";s:5:\"60.00\";s:9:\"max_score\";s:5:\"69.99\";s:6:\"remark\";s:4:\"Good\";s:5:\"color\";s:4:\"info\";s:5:\"order\";i:2;s:10:\"created_at\";s:19:\"2026-10-14 15:25:47\";s:10:\"updated_at\";s:19:\"2026-10-14 15:25:47\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:3;s:16:\"grading_scale_id\";i:1;s:5:\"grade\";s:1:\"C\";s:9:\"min_score\";s:5:\"60.00\";s:9:\"max_score\";s:5:\"69.99\";s:6:\"remark\";s:4:\"Good\";s:5:\"color\";s:4:\"info\";s:5:\"order\";i:2;s:10:\"created_at\";s:19:\"2026-10-14 15:25:47\";s:10:\"updated_at\";s:19:\"2026-10-14 15:25:47\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:9:\"min_score\";s:9:\"decimal:2\";s:9:\"max_score\";s:9:\"decimal:2\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:16:\"grading_scale_id\";i:1;s:5:\"grade\";i:2;s:9:\"min_score\";i:3;s:9:\"max_score\";i:4;s:6:\"remark\";i:5;s:5:\"color\";i:6;s:5:\"order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:3;O:27:\"App\\Models\\GradingScaleItem\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:19:\"grading_scale_items\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:4;s:16:\"grading_scale_id\";i:1;s:5:\"grade\";s:1:\"D\";s:9:\"min_score\";s:5:\"50.00\";s:9:\"max_score\";s:5:\"59.99\";s:6:\"remark\";s:4:\"Fair\";s:5:\"color\";s:7:\"warning\";s:5:\"order\";i:3;s:10:\"created_at\";s:19:\"2026-10-14 15:25:47\";s:10:\"updated_at\";s:19:\"2026-10-14 15:25:47\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:4;s:16:\"grading_scale_id\";i:1;s:5:\"grade\";s:1:\"D\";s:9:\"min_score\";s:5:\"50.00\";s:9:\"max_score\";s:5:\"59.99\";s:6:\"remark\";s:4:\"Fair\";s:5:\"color\";s:7:\"warning\";s:5:\"order\";i:3;s:10:\"created_at\";s:19:\"2026-10-14 15:25:47\";s:10:\"updated_at\";s:19:\"2026-10-14 15:25:47\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:9:\"min_score\";s:9:\"decimal:2\";s:9:\"max_score\";s:9:\"decimal:2\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:16:\"grading_scale_id\";i:1;s:5:\"grade\";i:2;s:9:\"min_score\";i:3;s:9:\"max_score\";i:4;s:6:\"remark\";i:5;s:5:\"color\";i:6;s:5:\"order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:4;O:27:\"App\\Models\\GradingScaleItem\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:19:\"grading_scale_items\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:5;s:16:\"grading_scale_id\";i:1;s:5:\"grade\";s:1:\"E\";s:9:\"min_score\";s:5:\"40.00\";s:9:\"max_score\";s:5:\"49.99\";s:6:\"remark\";s:4:\"Poor\";s:5:\"color\";s:7:\"warning\";s:5:\"order\";i:4;s:10:\"created_at\";s:19:\"2026-10-14 15:25:47\";s:10:\"updated_at\";s:19:\"2026-10-14 15:25:47\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:5;s:16:\"grading_scale_id\";i:1;s:5:\"grade\";s:1:\"E\";s:9:\"min_score\";s:5:\"40.00\";s:9:\"max_score\";s:5:\"49.99\";s:6:\"remark\";s:4:\"Poor\";s:5:\"color\";s:7:\"warning\";s:5:\"order\";i:4;s:10:\"created_at\";s:19:\"2026-10-14 15:25:47\";s:10:\"updated_at\";s:19:\"2026-10-14 15:25:47\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:9:\"min_score\";s:9:\"decimal:2\";s:9:\"max_score\";s:9:\"decimal:2\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:16:\"grading_scale_id\";i:1;s:5:\"grade\";i:2;s:9:\"min_score\";i:3;s:9:\"max_score\";i:4;s:6:\"remark\";i:5;s:5:\"color\";i:6;s:5:\"order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:5;O:27:\"App\\Models\\GradingScaleItem\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:19:\"grading_scale_items\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:6;s:16:\"grading_scale_id\";i:1;s:5:\"grade\";s:1:\"F\";s:9:\"min_score\";s:4:\"0.00\";s:9:\"max_score\";s:5:\"39.99\";s:6:\"remark\";s:9:\"Very Poor\";s:5:\"color\";s:6:\"danger\";s:5:\"order\";i:5;s:10:\"created_at\";s:19:\"2026-10-14 15:25:47\";s:10:\"updated_at\";s:19:\"2026-10-14 15:25:47\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:6;s:16:\"grading_scale_id\";i:1;s:5:\"grade\";s:1:\"F\";s:9:\"min_score\";s:4:\"0.00\";s:9:\"max_score\";s:5:\"39.99\";s:6:\"remark\";s:9:\"Very Poor\";s:5:\"color\";s:6:\"danger\";s:5:\"order\";i:5;s:10:\"created_at\";s:19:\"2026-10-14 15:25:47\";s:10:\"updated_at\";s:19:\"2026-10-14 15:25:47\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:9:\"min_score\";s:9:\"decimal:2\";s:9:\"max_score\";s:9:\"decimal:2\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:16:\"grading_scale_id\";i:1;s:5:\"grade\";i:2;s:9:\"min_score\";i:3;s:9:\"max_score\";i:4;s:6:\"remark\";i:5;s:5:\"color\";i:6;s:5:\"order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:4:\"name\";i:1;s:11:\"description\";i:2;s:10:\"is_default\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}', 1793376165);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `class_teacher_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`, `code`, `description`, `class_teacher_id`, `created_at`, `updated_at`) VALUES
(1, 'SSS 1', 'SSS001', 'Senior Secondary School 1', 2, '2026-10-06 12:52:45', '2026-10-14 14:05:26'),
(2, 'SS 2', 'SS001', 'SSS 2', 6, '2026-10-09 14:52:50', '2026-10-14 14:05:53'),
(3, 'SS3', 'SS003', 'SSS 3', NULL, '2026-10-09 14:53:21', '2026-10-09 14:53:21');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assessment_type` enum('test','exam') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'exam',
  `exam_mode` enum('objective','theory') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'objective',
  `shuffle_questions` tinyint(1) NOT NULL DEFAULT '0',
  `max_marks` int NOT NULL DEFAULT '100',
  `benchmark` int NOT NULL DEFAULT '50',
  `term` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `academic_year` int DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `subject_id` bigint UNSIGNED NOT NULL,
  `duration_minutes` int NOT NULL,
  `total_questions` int NOT NULL,
  `total_score` int NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `schedule_type` enum('no_date','single_date','date_range') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'single_date',
  `status` enum('upcoming','active','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'upcoming',
  `created_by_role` enum('admin','teacher') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `created_by` bigint UNSIGNED NOT NULL,
  `instructions` text COLLATE utf8mb4_unicode_ci,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `available_from` datetime DEFAULT NULL,
  `available_to` datetime DEFAULT NULL,
  `max_attempts` int NOT NULL DEFAULT '1',
  `passing_score` int DEFAULT NULL,
  `show_answers_after_completion` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `title`, `assessment_type`, `exam_mode`, `shuffle_questions`, `max_marks`, `benchmark`, `term`, `academic_year`, `description`, `subject_id`, `duration_minutes`, `total_questions`, `total_score`, `start_date`, `end_date`, `schedule_type`, `status`, `created_by_role`, `created_by`, `instructions`, `is_published`, `available_from`, `available_to`, `max_attempts`, `passing_score`, `show_answers_after_completion`, `created_at`, `updated_at`) VALUES
(1, 'Math (mid-test)', 'test', 'objective', 0, 40, 40, 'First Term', 2026, 'Just test', 1, 19, 5, 7, '2026-10-06 14:45:00', '2026-10-06 14:55:00', 'single_date', 'active', 'teacher', 2, 'Soft Landing', 1, NULL, NULL, 2, 40, 1, '2026-10-06 13:38:37', '2026-10-06 13:38:37'),
(2, 'Math (mid-Exam)', 'exam', 'objective', 0, 70, 81, 'First Term', 2026, 'This is the exam', 1, 20, 5, 7, '2026-10-06 15:18:00', '2026-10-06 15:40:00', 'single_date', 'active', 'teacher', 2, 'Another mid term exam', 1, NULL, NULL, 1, 81, 1, '2026-10-06 13:41:04', '2026-10-06 13:48:02'),
(3, 'Mid Term Test', 'test', 'objective', 0, 30, 40, 'Second Term', 2026, 'Soft landing', 1, 30, 5, 7, '2026-10-06 18:30:00', '2026-10-06 18:40:00', 'single_date', 'active', 'admin', 1, 'The test of the moment', 1, NULL, NULL, 2, 40, 1, '2026-10-06 17:19:03', '2026-10-07 09:36:42'),
(4, 'Math', 'exam', 'objective', 0, 70, 50, 'First Term', 2026, 'Exams', 1, 17, 7, 10, NULL, NULL, 'date_range', 'active', 'admin', 1, 'The exam of the year', 1, '2026-10-10 15:45:00', '2026-10-20 15:45:00', 3, 50, 1, '2026-10-10 14:46:50', '2026-10-10 14:46:50'),
(5, 'Math - Test', 'test', 'objective', 0, 30, 40, 'Second Term', 2026, 'A test for maths', 1, 20, 7, 10, NULL, NULL, 'date_range', 'active', 'admin', 1, 'Do well the test', 0, '2026-10-14 14:00:00', '2026-10-16 13:57:00', 3, 40, 1, '2026-10-14 12:58:42', '2026-10-30 13:41:19'),
(6, 'English', 'test', 'objective', 0, 80, 60, 'Second Term', 2026, 'This is an English Test', 4, 30, 1, 80, '2026-10-21 16:40:00', '2026-10-22 16:25:00', 'single_date', 'active', 'admin', 1, 'Do it easy', 1, NULL, NULL, 1, 60, 0, '2026-10-21 15:26:37', '2026-10-21 15:26:37'),
(7, 'History Test', 'test', 'objective', 0, 30, 40, 'Third Term', 2026, 'HIstory For all', 5, 30, 3, 12, NULL, NULL, 'date_range', 'active', 'admin', 1, 'All students should attempt the question.', 1, '2026-10-30 15:14:00', '2026-10-31 15:05:00', 2, 40, 1, '2026-10-30 14:08:56', '2026-10-30 14:08:56'),
(8, 'Math Test', 'test', 'objective', 0, 30, 25, 'Second Term', 2026, 'For the test', 1, 20, 4, 6, NULL, NULL, 'date_range', 'active', 'teacher', 2, 'Do well', 1, '2026-10-31 10:13:00', '2026-11-02 09:13:00', 5, 25, 1, '2026-10-31 08:14:22', '2026-10-31 08:14:22');

-- --------------------------------------------------------

--
-- Table structure for table `exam_attempts`
--

CREATE TABLE `exam_attempts` (
  `id` bigint UNSIGNED NOT NULL,
  `exam_id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `started_at` datetime NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `answers` json DEFAULT NULL,
  `score` int DEFAULT NULL,
  `total_questions_answered` int NOT NULL DEFAULT '0',
  `attempt_number` int NOT NULL DEFAULT '1',
  `status` enum('in_progress','submitted','timed_out') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in_progress',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_attempts`
--

INSERT INTO `exam_attempts` (`id`, `exam_id`, `student_id`, `started_at`, `completed_at`, `answers`, `score`, `total_questions_answered`, `attempt_number`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-10-06 14:46:07', '2026-10-06 14:46:50', '{\"1\": \"4\", \"2\": \"Abuja\", \"3\": \"Mars\", \"4\": \"1\", \"5\": \"Mango\"}', 7, 5, 1, 'submitted', '2026-10-06 13:46:07', '2026-10-06 13:46:50'),
(2, 2, 1, '2026-10-06 15:35:34', '2026-10-06 15:35:58', '{\"1\": \"4\", \"2\": \"Kano\", \"3\": \"Mars\", \"4\": \"2\", \"5\": \"Mango\"}', 3, 5, 1, 'submitted', '2026-10-06 14:35:34', '2026-10-06 14:35:58'),
(3, 4, 1, '2026-10-14 14:08:39', '2026-10-14 14:12:22', '{\"1\": \"4\", \"2\": \"Abuja\", \"3\": \"Jupiter\", \"4\": \"1\", \"5\": \"Mango\", \"6\": \"Map\", \"7\": \"4\"}', 6, 7, 1, 'submitted', '2026-10-14 13:08:39', '2026-10-14 13:12:22'),
(4, 6, 1, '2026-10-22 08:02:43', '2026-10-22 08:03:54', '{\"8\": \"The most interesting thing about nigeria is the people who live within her borders.\"}', 0, 0, 1, 'submitted', '2026-10-22 07:02:43', '2026-10-22 07:03:54'),
(5, 7, 1, '2026-10-30 15:14:44', '2026-10-30 16:07:05', '[]', 0, 0, 1, 'submitted', '2026-10-30 14:14:44', '2026-10-30 15:07:05'),
(6, 7, 1, '2026-10-30 16:14:37', '2026-10-30 16:15:26', '{\"15\": \"Metal\", \"16\": \"Allow\"}', 0, 2, 2, 'submitted', '2026-10-30 15:14:37', '2026-10-30 15:15:26');

-- --------------------------------------------------------

--
-- Table structure for table `exam_questions`
--

CREATE TABLE `exam_questions` (
  `id` bigint UNSIGNED NOT NULL,
  `exam_id` bigint UNSIGNED NOT NULL,
  `question_id` bigint UNSIGNED NOT NULL,
  `question_order` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_questions`
--

INSERT INTO `exam_questions` (`id`, `exam_id`, `question_id`, `question_order`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, NULL, NULL),
(2, 1, 2, 2, NULL, NULL),
(3, 1, 3, 3, NULL, NULL),
(5, 1, 5, 5, NULL, NULL),
(6, 2, 1, 1, NULL, NULL),
(7, 2, 2, 2, NULL, NULL),
(8, 2, 3, 3, NULL, NULL),
(10, 2, 5, 5, NULL, NULL),
(11, 3, 1, 1, NULL, NULL),
(12, 3, 2, 2, NULL, NULL),
(13, 3, 3, 3, NULL, NULL),
(15, 3, 5, 5, NULL, NULL),
(16, 4, 1, 1, NULL, NULL),
(17, 4, 2, 2, NULL, NULL),
(18, 4, 3, 3, NULL, NULL),
(20, 4, 5, 5, NULL, NULL),
(21, 4, 6, 6, NULL, NULL),
(22, 4, 7, 7, NULL, NULL),
(23, 5, 1, 1, NULL, NULL),
(24, 5, 2, 2, NULL, NULL),
(25, 5, 3, 3, NULL, NULL),
(27, 5, 5, 5, NULL, NULL),
(28, 5, 6, 6, NULL, NULL),
(29, 5, 7, 7, NULL, NULL),
(30, 6, 8, 1, NULL, NULL),
(31, 7, 15, 1, NULL, NULL),
(32, 7, 16, 2, NULL, NULL),
(33, 7, 19, 3, NULL, NULL),
(34, 8, 2, 1, NULL, NULL),
(35, 8, 3, 2, NULL, NULL),
(36, 8, 6, 3, NULL, NULL),
(37, 8, 7, 4, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grading_scales`
--

CREATE TABLE `grading_scales` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grading_scales`
--

INSERT INTO `grading_scales` (`id`, `name`, `description`, `is_default`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Standard Grading Scale', 'Default A-F grading system', 1, 1, '2026-10-14 14:25:47', '2026-10-14 14:25:47');

-- --------------------------------------------------------

--
-- Table structure for table `grading_scale_items`
--

CREATE TABLE `grading_scale_items` (
  `id` bigint UNSIGNED NOT NULL,
  `grading_scale_id` bigint UNSIGNED NOT NULL,
  `grade` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_score` decimal(5,2) NOT NULL,
  `max_score` decimal(5,2) NOT NULL,
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'secondary',
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grading_scale_items`
--

INSERT INTO `grading_scale_items` (`id`, `grading_scale_id`, `grade`, `min_score`, `max_score`, `remark`, `color`, `order`, `created_at`, `updated_at`) VALUES
(1, 1, 'A', 80.00, 100.00, 'Excellent', 'success', 0, '2026-10-14 14:25:47', '2026-10-14 14:25:47'),
(2, 1, 'B', 70.00, 79.99, 'Very Good', 'primary', 1, '2026-10-14 14:25:47', '2026-10-14 14:25:47'),
(3, 1, 'C', 60.00, 69.99, 'Good', 'info', 2, '2026-10-14 14:25:47', '2026-10-14 14:25:47'),
(4, 1, 'D', 50.00, 59.99, 'Fair', 'warning', 3, '2026-10-14 14:25:47', '2026-10-14 14:25:47'),
(5, 1, 'E', 40.00, 49.99, 'Poor', 'warning', 4, '2026-10-14 14:25:47', '2026-10-14 14:25:47'),
(6, 1, 'F', 0.00, 39.99, 'Very Poor', 'danger', 5, '2026-10-14 14:25:47', '2026-10-14 14:25:47');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_08_19_085907_create_classes_table', 1),
(5, '2026_08_19_090048_create_subjects_table', 1),
(6, '2026_08_19_090122_create_students_table', 1),
(7, '2026_08_19_090311_create_questions_table', 1),
(8, '2026_08_19_090528_create_exams_table', 1),
(9, '2026_08_19_090600_create_exam_questions_table', 1),
(10, '2026_08_19_090652_create_exam_attempts_table', 1),
(11, '2026_08_19_090724_create_results_table', 1),
(12, '2026_08_19_090759_create_report_cards_table', 1),
(13, '2026_09_28_165435_add_assessment_type_to_exams_table', 1),
(14, '2026_09_30_143315_update_report_cards_for_test_exam', 1),
(15, '2026_10_06_162703_create_settings_table', 2),
(16, '2026_10_09_181735_add_assessment_access_to_students_table', 3),
(17, '2026_10_14_143705_add_class_teacher_to_classes_table', 4),
(18, '2026_10_14_150633_create_grading_scales_table', 5),
(19, '2026_10_14_162143_add_photo_path_to_users_table', 6),
(20, '2026_10_16_153944_add_comprehensive_fields_to_report_cards_table', 7),
(21, '2026_10_16_182350_add_gender_to_students_table', 8),
(22, '2026_10_17_135317_add_question_type_to_questions_table', 9),
(23, '2026_10_21_163432_add_option_images_to_questions_table', 10),
(24, '2026_10_30_164137_add_exam_mode_to_exams_table', 11);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` bigint UNSIGNED NOT NULL,
  `subject_id` bigint UNSIGNED NOT NULL,
  `question_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_type` enum('objective','theory') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'objective',
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `options` json DEFAULT NULL,
  `option_images` json DEFAULT NULL,
  `correct_answer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `score` int NOT NULL DEFAULT '1',
  `difficulty` enum('easy','medium','hard') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `subject_id`, `question_text`, `question_type`, `image_path`, `options`, `option_images`, `correct_answer`, `score`, `difficulty`, `created_at`, `updated_at`) VALUES
(1, 1, 'What is 2+2?', 'objective', NULL, '[\"3\", \"4\", \"5\", \"6\"]', NULL, '4', 1, 'easy', '2026-10-06 13:34:51', '2026-10-06 13:34:51'),
(2, 1, 'What is the capital of Nigeria?', 'objective', NULL, '[\"Lagos\", \"Abuja\", \"Kano\", \"Ibadan\"]', NULL, 'Abuja', 2, 'medium', '2026-10-06 13:34:51', '2026-10-06 13:34:51'),
(3, 1, 'Which planet is known as the Red Planet?', 'objective', NULL, '[\"Venus\", \"Mars\", \"Jupiter\", \"Saturn\"]', NULL, 'Mars', 1, 'easy', '2026-10-06 13:34:51', '2026-10-06 13:34:51'),
(5, 1, 'What is the image here ?', 'objective', 'uploads/questions/1791365355_6ac610eb1637e.jpg', '[\"Tomatoes\", \"Mango\", \"Orange\", \"Vine\"]', NULL, 'Mango', 1, 'easy', '2026-10-06 13:35:59', '2026-10-07 09:29:15'),
(6, 1, 'What is this thing ?', 'objective', 'uploads/questions/1791365410_6ac611224023f.PNG', '[\"Land\", \"Map\", \"Thick\", \"House\"]', NULL, 'Land', 1, 'easy', '2026-10-07 09:30:10', '2026-10-07 09:30:10'),
(7, 1, '<p>What is the Formular for&nbsp; x = x<sup>2&nbsp;</sup>+ y<sup>2&nbsp;</sup></p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>', 'objective', NULL, '[\"4\", \"2\", \"1\", \"7\"]', NULL, '2', 2, 'medium', '2026-10-09 17:05:21', '2026-10-09 17:05:21'),
(8, 4, '<p>Write an essay for Nigeria for 1889 economy</p>', 'theory', 'uploads/questions/1792595823_6ad8d76fdd674.jpg', NULL, NULL, NULL, 80, 'easy', '2026-10-21 13:32:27', '2026-10-21 15:17:03'),
(9, 6, '<p>This is thoery</p>', 'theory', 'uploads/questions/1792764783_6adb6b6f39cdf.webp', NULL, NULL, NULL, 89, 'medium', '2026-10-23 13:54:10', '2026-10-23 14:13:03'),
(10, 6, '<p>What is the most delicious mushroom ?</p>', 'objective', 'uploads/questions/1792764766_6adb6b5ec1e09.jpg', '[\"m1\", \"m3\", \"m4\", \"m3\"]', NULL, 'm3', 8, 'easy', '2026-10-23 14:08:35', '2026-10-23 14:12:46'),
(12, 6, '<p>What is the most colorful one ?</p>', 'objective', 'uploads/questions/1792768791_6adb7b1773e85.webp', '[]', '[\"uploads/optioins/1792768791_6adb7b17742d0.jpg\", \"uploads/optioins/1792768791_6adb7b1774653.jpg\", \"uploads/optioins/1792768791_6adb7b1774952.jpg\", \"uploads/optioins/1792768791_6adb7b1774c2f.jpg\"]', NULL, 10, 'easy', '2026-10-23 15:19:51', '2026-10-23 15:19:51'),
(13, 6, '<p>What is the old mushroom ?</p>', 'objective', NULL, '[\"7\", \"6\", \"3\"]', NULL, '1', 4, 'medium', '2026-10-24 16:10:20', '2026-10-24 16:10:20'),
(14, 5, '<p>What is the longest serving king of the Ashanti in Ghana ?</p>', 'theory', 'uploads/questions/1793026024_6adf67e8d918d.PNG', NULL, NULL, NULL, 40, 'easy', '2026-10-26 14:47:04', '2026-10-26 14:47:04'),
(15, 5, '<p>Which among all of these is the correct answer ?</p>', 'objective', 'uploads/questions/1793026589_6adf6a1d85cc1.PNG', '[\"Gone soon\", \"Metal\", \"Iron\", \"Copper\"]', NULL, '1', 2, 'medium', '2026-10-26 14:56:29', '2026-10-26 14:56:29'),
(16, 5, '<p>Look at the man beyind the mirror. Who among them tried the most</p>', 'objective', 'uploads/questions/1793037082_6adf931a4518c.webp', '[\"Again\", \"Bien\", \"Allow\", \"Take\"]', NULL, '1', 6, 'hard', '2026-10-26 15:25:45', '2026-10-26 17:51:22'),
(17, 6, '<p>What is the most durable mushroom here ?</p>', 'objective', 'uploads/questions/1793030768_6adf7a708f9e9.jpg', '[]', '[\"uploads/options/1793030768_6adf7a708fd1c.jpg\", \"uploads/options/1793030768_6adf7a708fff1.jpg\", \"uploads/options/1793030768_6adf7a70902f6.jpg\", \"uploads/options/1793030768_6adf7a70905c1.webp\"]', '3', 8, 'hard', '2026-10-26 16:06:08', '2026-10-26 16:06:08'),
(19, 5, '<p>Discuss the fundamental differences between Nigeria and Cameroon and other things </p>', 'objective', 'uploads/questions/1793367163_6ae49c7bde30a.jpg', '[]', '[\"uploads/options/1793369019_6ae4a3bb9e70c.jpg\", \"uploads/options/1793369019_6ae4a3bb9eb92.jpg\", \"uploads/options/1793369019_6ae4a3bb9f02d.jpg\", \"uploads/options/1793369019_6ae4a3bb9f3dc.jpg\"]', '1', 4, 'medium', '2026-10-30 11:53:00', '2026-10-30 14:03:39');

-- --------------------------------------------------------

--
-- Table structure for table `report_cards`
--

CREATE TABLE `report_cards` (
  `id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age` int DEFAULT NULL,
  `house` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_in_class` int DEFAULT NULL,
  `class_id` bigint UNSIGNED NOT NULL,
  `term` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `academic_year` int NOT NULL,
  `session` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_scores` json NOT NULL,
  `subject_breakdown` json DEFAULT NULL,
  `total_score` int NOT NULL,
  `average_score` int NOT NULL,
  `total_test_score` int NOT NULL DEFAULT '0',
  `total_test_max` int NOT NULL DEFAULT '0',
  `total_exam_score` int NOT NULL DEFAULT '0',
  `total_exam_max` int NOT NULL DEFAULT '0',
  `grand_total` int NOT NULL DEFAULT '0',
  `grand_max` int NOT NULL DEFAULT '0',
  `max_class_score` decimal(8,2) DEFAULT NULL,
  `min_class_score` decimal(8,2) DEFAULT NULL,
  `marks_obtainable` int DEFAULT NULL,
  `grade` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `generated_date` date DEFAULT NULL,
  `position` int DEFAULT NULL,
  `total_students` int DEFAULT NULL,
  `times_school_opened` int DEFAULT NULL,
  `times_present` int DEFAULT NULL,
  `affective_traits` json DEFAULT NULL,
  `psychomotor_skills` json DEFAULT NULL,
  `class_teacher_comment` text COLLATE utf8mb4_unicode_ci,
  `class_teacher_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `principal_comment` text COLLATE utf8mb4_unicode_ci,
  `principal_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vacation_date` date DEFAULT NULL,
  `resumption_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `generated_by` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `report_cards`
--

INSERT INTO `report_cards` (`id`, `student_id`, `gender`, `age`, `house`, `no_in_class`, `class_id`, `term`, `academic_year`, `session`, `subject_scores`, `subject_breakdown`, `total_score`, `average_score`, `total_test_score`, `total_test_max`, `total_exam_score`, `total_exam_max`, `grand_total`, `grand_max`, `max_class_score`, `min_class_score`, `marks_obtainable`, `grade`, `remarks`, `generated_date`, `position`, `total_students`, `times_school_opened`, `times_present`, `affective_traits`, `psychomotor_skills`, `class_teacher_comment`, `class_teacher_name`, `principal_comment`, `principal_name`, `vacation_date`, `resumption_date`, `created_at`, `updated_at`, `generated_by`) VALUES
(1, 1, 'Male', 26, NULL, 3, 1, 'First Term', 2026, '2026/2027', '[{\"max\": 0, \"grade\": null, \"total\": null, \"remark\": null, \"subject\": \"English\", \"teacher\": \"Teacher Two\", \"exam_max\": 0, \"test_max\": 0, \"attempted\": false, \"exam_score\": null, \"percentage\": null, \"subject_id\": 4, \"test_score\": null, \"subject_code\": \"Lang001\", \"exam_attempted\": false, \"test_attempted\": false, \"has_assessments\": false}, {\"max\": 0, \"grade\": null, \"total\": null, \"remark\": null, \"subject\": \"Geography\", \"teacher\": \"Tom Samson\", \"exam_max\": 0, \"test_max\": 0, \"attempted\": false, \"exam_score\": null, \"percentage\": null, \"subject_id\": 6, \"test_score\": null, \"subject_code\": \"Geo001\", \"exam_attempted\": false, \"test_attempted\": false, \"has_assessments\": false}, {\"max\": 0, \"grade\": null, \"total\": null, \"remark\": null, \"subject\": \"History\", \"teacher\": \"Teacher User\", \"exam_max\": 0, \"test_max\": 0, \"attempted\": false, \"exam_score\": null, \"percentage\": null, \"subject_id\": 5, \"test_score\": null, \"subject_code\": \"Hist001\", \"exam_attempted\": false, \"test_attempted\": false, \"has_assessments\": false}, {\"max\": 180, \"grade\": \"C\", \"total\": 112, \"remark\": \"Good\", \"subject\": \"Mathematics\", \"teacher\": \"Teacher User\", \"exam_max\": 140, \"test_max\": 40, \"attempted\": true, \"exam_score\": 72, \"percentage\": 62.22, \"subject_id\": 1, \"test_score\": 40, \"session_avg\": 62.22, \"term_scores\": {\"first\": null, \"third\": null, \"second\": null}, \"subject_code\": \"MATH101\", \"exam_attempted\": true, \"test_attempted\": true, \"has_assessments\": true}]', '[{\"exams\": [], \"tests\": [], \"subject_id\": 4, \"subject_name\": \"English\"}, {\"exams\": [], \"tests\": [], \"subject_id\": 6, \"subject_name\": \"Geography\"}, {\"exams\": [], \"tests\": [], \"subject_id\": 5, \"subject_name\": \"History\"}, {\"exams\": [{\"id\": 2, \"title\": \"Math (mid-Exam)\", \"attempted\": true, \"max_marks\": 70, \"raw_score\": 3, \"raw_total\": 7, \"scaled_score\": 30}, {\"id\": 4, \"title\": \"Math\", \"attempted\": true, \"max_marks\": 70, \"raw_score\": 6, \"raw_total\": 10, \"scaled_score\": 42}], \"tests\": [{\"id\": 1, \"title\": \"Math (mid-test)\", \"attempted\": true, \"max_marks\": 40, \"raw_score\": 7, \"raw_total\": 7, \"scaled_score\": 40}], \"subject_id\": 1, \"subject_name\": \"Mathematics\"}]', 112, 62, 40, 40, 72, 140, 112, 180, 70.00, 70.00, 180, 'C', 'Good', '2026-10-17', 1, 3, 120, 116, '{\"Honesty\": \"5\", \"Respect\": \"5\", \"Neatness\": \"5\", \"Teamwork\": \"5\", \"Politeness\": \"5\", \"Punctuality\": \"4\", \"Mental Alertness\": \"4\", \"Peer Relationship\": \"5\", \"Willingness To Learn\": \"5\"}', '{\"Chess\": \"5\", \"Dance\": \"5\", \"Music\": \"5\", \"Verbal Skills\": \"5\", \"Sports & Games\": \"5\", \"Artistic Skills\": \"5\", \"Entrepreneurship\": \"5\"}', 'Very Good job.', 'Teacher User', 'A good result. Student should continue working hard to achieve even better next term.', NULL, '2026-10-18', '2026-10-28', '2026-10-06 14:47:54', '2026-10-30 15:04:40', 1),
(2, 2, 'Female', 19, 'Pauline', 3, 1, 'First Term', 2026, '2026/2027', '[{\"max\": 0, \"grade\": null, \"total\": null, \"remark\": null, \"subject\": \"English\", \"teacher\": \"Teacher Two\", \"exam_max\": 0, \"test_max\": 0, \"attempted\": false, \"exam_score\": null, \"percentage\": null, \"subject_id\": 4, \"test_score\": null, \"subject_code\": \"Lang001\", \"exam_attempted\": false, \"test_attempted\": false, \"has_assessments\": false}, {\"max\": 0, \"grade\": null, \"total\": null, \"remark\": null, \"subject\": \"Geography\", \"teacher\": \"Tom Samson\", \"exam_max\": 0, \"test_max\": 0, \"attempted\": false, \"exam_score\": null, \"percentage\": null, \"subject_id\": 6, \"test_score\": null, \"subject_code\": \"Geo001\", \"exam_attempted\": false, \"test_attempted\": false, \"has_assessments\": false}, {\"max\": 0, \"grade\": null, \"total\": null, \"remark\": null, \"subject\": \"History\", \"teacher\": \"Teacher User\", \"exam_max\": 0, \"test_max\": 0, \"attempted\": false, \"exam_score\": null, \"percentage\": null, \"subject_id\": 5, \"test_score\": null, \"subject_code\": \"Hist001\", \"exam_attempted\": false, \"test_attempted\": false, \"has_assessments\": false}, {\"max\": 180, \"grade\": null, \"total\": null, \"remark\": null, \"subject\": \"Mathematics\", \"teacher\": \"Teacher User\", \"exam_max\": 140, \"test_max\": 40, \"attempted\": false, \"exam_score\": null, \"percentage\": null, \"subject_id\": 1, \"test_score\": null, \"session_avg\": null, \"term_scores\": {\"first\": null, \"third\": null, \"second\": null}, \"subject_code\": \"MATH101\", \"exam_attempted\": false, \"test_attempted\": false, \"has_assessments\": true}]', '[{\"exams\": [], \"tests\": [], \"subject_id\": 4, \"subject_name\": \"English\"}, {\"exams\": [], \"tests\": [], \"subject_id\": 6, \"subject_name\": \"Geography\"}, {\"exams\": [], \"tests\": [], \"subject_id\": 5, \"subject_name\": \"History\"}, {\"exams\": [{\"id\": 2, \"title\": \"Math (mid-Exam)\", \"attempted\": false, \"max_marks\": 70, \"raw_score\": null, \"raw_total\": 7, \"scaled_score\": null}, {\"id\": 4, \"title\": \"Math\", \"attempted\": false, \"max_marks\": 70, \"raw_score\": null, \"raw_total\": 10, \"scaled_score\": null}], \"tests\": [{\"id\": 1, \"title\": \"Math (mid-test)\", \"attempted\": false, \"max_marks\": 40, \"raw_score\": null, \"raw_total\": 7, \"scaled_score\": null}], \"subject_id\": 1, \"subject_name\": \"Mathematics\"}]', 0, 0, 0, 40, 0, 140, 0, 180, 112.00, 112.00, 180, 'F', 'Very Poor', '2026-10-17', 2, 3, 120, 118, '{\"Honesty\": \"5\", \"Respect\": \"3\", \"Neatness\": \"3\", \"Teamwork\": \"5\", \"Politeness\": \"2\", \"Punctuality\": \"4\", \"Mental Alertness\": \"4\", \"Peer Relationship\": \"5\", \"Willingness To Learn\": \"5\"}', '{\"Chess\": \"4\", \"Dance\": \"4\", \"Music\": \"5\", \"Verbal Skills\": \"5\", \"Sports & Games\": \"5\", \"Artistic Skills\": \"5\", \"Entrepreneurship\": \"4\"}', 'Very Good job.', 'Teacher User', 'Result requires significant improvement. Joe should be more disciplined and focused.', 'Jonh Doe', '2026-10-22', '2026-11-20', '2026-10-17 11:50:45', '2026-10-30 15:03:54', 1);

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `subject_id` bigint UNSIGNED NOT NULL,
  `exam_id` bigint UNSIGNED DEFAULT NULL,
  `assessment_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `score` int NOT NULL,
  `max_score` int NOT NULL,
  `percentage` int NOT NULL,
  `grade` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `assessment_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `student_id`, `subject_id`, `exam_id`, `assessment_type`, `score`, `max_score`, `percentage`, `grade`, `remarks`, `assessment_date`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'CBT Exam', 7, 7, 100, 'A', 'Excellent', '2026-10-06', '2026-10-06 13:46:50', '2026-10-06 13:46:50'),
(2, 1, 1, 2, 'CBT Exam', 3, 7, 43, 'E', 'Poor', '2026-10-06', '2026-10-06 14:35:58', '2026-10-06 14:35:58'),
(4, 1, 1, NULL, 'test', 80, 100, 80, NULL, 'Excellent', '2026-10-07', '2026-10-07 10:32:59', '2026-10-30 13:47:22'),
(5, 1, 1, 4, 'CBT Exam', 6, 10, 60, 'C', 'Good', '2026-10-14', '2026-10-14 13:12:22', '2026-10-14 13:12:22'),
(6, 1, 4, 6, 'test', 70, 80, 88, 'F', 'Excellent', '2026-10-22', '2026-10-22 07:03:54', '2026-10-30 13:50:43'),
(8, 2, 5, NULL, 'test', 22, 100, 22, NULL, NULL, '2026-10-30', '2026-10-30 13:58:53', '2026-10-30 13:59:31'),
(12, 1, 5, 7, 'CBT Exam', 0, 12, 0, 'F', 'Very Poor', '2026-10-30', '2026-10-30 15:15:26', '2026-10-30 15:15:26');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('5bX3lQ2lA9uaCspEUFm0yejUKXLBIhs8CVcrSmQc', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0', 'eyJfdG9rZW4iOiJNRm44WDNLaDNLR3NtOTZKOXVKU2t4ZlBCY05kMWg0VTRhN2p5MWVpIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0XC9jYnQtZXhhbVwvcHVibGljXC90ZWFjaGVyXC9leGFtcyIsInJvdXRlIjoidGVhY2hlci5leGFtcyJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6Mn0=', 1793434463),
('SD5VjAFo6DjKCcpNBAsNFJNTOV7dfsrd74xaQgWn', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', 'eyJfdG9rZW4iOiJlU3paZjV6eEYxNXRDWEFJS2k3N2RpeGs4RmF5dTdlMXJDb3NZQ1VTIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvY2J0LWV4YW1cL3B1YmxpY1wvYWRtaW5cL2Rhc2hib2FyZCIsInJvdXRlIjoiYWRtaW4uZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjF9', 1793430990);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `group`, `label`, `description`, `created_at`, `updated_at`) VALUES
(1, 'school_name', 'Little Bird', 'string', 'school', 'School Name', 'The name of your school or institution', '2026-10-06 15:36:02', '2026-10-17 12:30:28'),
(2, 'school_motto', 'Excellence in Education', 'string', 'school', 'School Motto', 'Your school\'s slogan or motto', '2026-10-06 15:36:02', '2026-10-06 15:36:02'),
(3, 'school_address', '12, Bells Street, Lagos', 'string', 'school', 'School Address', 'Physical address of the school', '2026-10-06 15:36:02', '2026-10-17 12:30:28'),
(4, 'school_phone', '080222333443', 'string', 'school', 'School Phone', 'Contact phone number', '2026-10-06 15:36:02', '2026-10-17 12:30:28'),
(5, 'school_email', 'bird@cbt.com', 'string', 'school', 'School Email', 'Contact email address', '2026-10-06 15:36:02', '2026-10-17 12:30:28'),
(6, 'school_logo', 'uploads/settings/1792240302_6ad36aae939e7.jpg', 'image', 'school', 'School Logo', 'Upload a logo (PNG, JPG, SVG). Recommended: 200x200px, transparent background', '2026-10-06 15:36:02', '2026-10-17 12:31:42'),
(7, 'landing_page_title', 'Welcome to Our CBT System', 'string', 'landing', 'Landing Page Title', 'Title displayed on the landing page', '2026-10-06 15:36:02', '2026-10-06 15:36:02'),
(8, 'landing_page_description', 'A modern computer-based testing platform for schools and institutions.', 'string', 'landing', 'Landing Page Description', 'Brief description shown on the landing page', '2026-10-06 15:36:02', '2026-10-17 12:30:28'),
(9, 'footer_text', '© 2026 CBT System. All rights reserved.', 'string', 'general', 'Footer Text', 'Text shown in the footer', '2026-10-06 15:36:02', '2026-10-06 15:36:02'),
(10, 'theme_primary', '#1038b2', 'string', 'theme', 'Primary Color', 'Main brand color (buttons, navbar, links)', '2026-10-17 11:58:37', '2026-10-17 12:30:28'),
(11, 'theme_secondary', '#1cc88a', 'string', 'theme', 'Secondary Color', 'Accent color (success, secondary buttons)', '2026-10-17 11:58:37', '2026-10-17 12:30:28'),
(12, 'theme_report_header', '#1a6489', 'string', 'theme', 'Report Card Header Color', 'The dark red banner color on the report card', '2026-10-17 11:58:37', '2026-10-17 12:30:28'),
(13, 'theme_report_text', '#ffffff', 'string', 'theme', 'Report Card Header Text', 'Text color inside the report card header', '2026-10-17 11:58:37', '2026-10-17 12:30:28');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `class_id` bigint UNSIGNED NOT NULL,
  `admission_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','graduated','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `can_take_assessments` tinyint(1) NOT NULL DEFAULT '1',
  `deactivation_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deactivated_by` bigint UNSIGNED DEFAULT NULL,
  `deactivated_at` timestamp NULL DEFAULT NULL,
  `reactivate_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `class_id`, `admission_number`, `date_of_birth`, `gender`, `guardian_name`, `guardian_phone`, `status`, `can_take_assessments`, `deactivation_reason`, `created_at`, `updated_at`, `deactivated_by`, `deactivated_at`, `reactivate_at`) VALUES
(1, 3, 1, 'STU001', '2000-01-01', 'Male', 'Guardian Name', '08012345678', 'active', 1, NULL, '2026-10-06 12:52:45', '2026-10-16 18:00:25', NULL, NULL, NULL),
(2, 4, 1, 'STD002', '2025-06-10', 'Female', 'Joe Father', '090334444', 'active', 1, NULL, '2026-10-09 14:49:35', '2026-10-16 18:00:35', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_id` bigint UNSIGNED NOT NULL,
  `teacher_id` bigint UNSIGNED NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `code`, `class_id`, `teacher_id`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Mathematics', 'MATH101', 1, 2, 'Basic Mathematics', 'active', '2026-10-06 12:52:45', '2026-10-10 14:44:36'),
(2, 'Biology', 'Bio001', 3, 2, 'Biology classes', 'active', '2026-10-09 14:59:27', '2026-10-09 15:13:01'),
(3, 'Chemistry', 'Chem002', 2, 6, 'This is for science class', 'active', '2026-10-09 15:13:52', '2026-10-09 15:13:52'),
(4, 'English', 'Lang001', 1, 9, 'English Language Subject', 'active', '2026-10-15 13:02:35', '2026-10-15 13:02:35'),
(5, 'History', 'Hist001', 1, 2, 'The study of African History', 'active', '2026-10-15 13:03:25', '2026-10-15 13:03:25'),
(6, 'Geography', 'Geo001', 1, 5, 'Geography of the World', 'active', '2026-10-15 13:04:24', '2026-10-15 13:04:24'),
(8, 'Physics', 'PHY001', 1, 5, NULL, 'active', '2026-10-30 13:40:35', '2026-10-30 13:40:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','teacher','student') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'student',
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `address`, `photo_path`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@cbt.com', NULL, '$2y$12$2msydpg/43jBeRWp.xDpoOBExFSJCEuUucUKmpANuY7zjBIQOv6vm', 'admin', NULL, NULL, NULL, NULL, '2026-10-06 12:52:44', '2026-10-06 12:52:44'),
(2, 'Teacher User', 'teacher@cbt.com', NULL, '$2y$12$pxHosbWdwWQm67CxL67xRu6fNw3f8GjB3a2xzRbvw8bNyliO58V7K', 'teacher', NULL, NULL, 'uploads/teachers/1792069113_6ad0cdf9925d4.jpg', NULL, '2026-10-06 12:52:45', '2026-10-15 12:58:33'),
(3, 'Student User', 'student@cbt.com', NULL, '$2y$12$2VxW2pWGhqPAPIyptk8IlO/T6JHwM7sBIBCnjQaSqyyhUc2RPaL8i', 'student', NULL, NULL, 'uploads/students/1792069139_6ad0ce1335254.jpg', NULL, '2026-10-06 12:52:45', '2026-10-15 12:58:59'),
(4, 'Joe Marthins', 'joe@cbt.com', NULL, '$2y$12$wwg5BbFB1BDao8o1ULru9.SncyKDCaH6LiDWK11W4DqqiXMLWVcwO', 'student', NULL, NULL, 'uploads/students/1792596225_6ad8d90195789.jpg', NULL, '2026-10-09 14:49:35', '2026-10-21 15:23:45'),
(5, 'Tom Samson', 'tom@cbt.com', NULL, '$2y$12$RaRUeMct85hHcrsxhhIn3u2BqN/tS.aehd3BsXlQcX01lk5uD.Ltm', 'teacher', '080123456', 'Not far', NULL, NULL, '2026-10-09 14:50:57', '2026-10-09 14:50:57'),
(6, 'Carol Tim', 'carol@cbt.com', NULL, '$2y$12$qOGRXubS7A2RrtXTlI7wzu36n3juiDXENrZlaNcltFMWaj.lzXSSS', 'teacher', '0808888', 'Closer Bay', NULL, NULL, '2026-10-09 14:51:56', '2026-10-09 14:51:56'),
(9, 'Teacher Two', 'teachertwo@cbt.com', NULL, '$2y$12$m4p1V1Zmv7C959Wf.Zk7GOr4E3Fuq8d/MjM5MitahJYzKh6jxuXnW', 'teacher', '0908888333', '12, Aina cloase', 'uploads/teachers/1792069312_6ad0cec0857b6.jpg', NULL, '2026-10-15 13:01:52', '2026-10-15 13:01:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `classes_code_unique` (`code`),
  ADD KEY `classes_class_teacher_id_foreign` (`class_teacher_id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exams_subject_id_foreign` (`subject_id`),
  ADD KEY `exams_created_by_foreign` (`created_by`);

--
-- Indexes for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_attempts_exam_id_foreign` (`exam_id`),
  ADD KEY `exam_attempts_student_id_foreign` (`student_id`);

--
-- Indexes for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_questions_exam_id_foreign` (`exam_id`),
  ADD KEY `exam_questions_question_id_foreign` (`question_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `grading_scales`
--
ALTER TABLE `grading_scales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grading_scale_items`
--
ALTER TABLE `grading_scale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grading_scale_items_grading_scale_id_order_index` (`grading_scale_id`,`order`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questions_subject_id_foreign` (`subject_id`);

--
-- Indexes for table `report_cards`
--
ALTER TABLE `report_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_cards_student_id_foreign` (`student_id`),
  ADD KEY `report_cards_class_id_foreign` (`class_id`),
  ADD KEY `report_cards_generated_by_foreign` (`generated_by`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `results_student_id_foreign` (`student_id`),
  ADD KEY `results_subject_id_foreign` (`subject_id`),
  ADD KEY `results_exam_id_foreign` (`exam_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_admission_number_unique` (`admission_number`),
  ADD KEY `students_user_id_foreign` (`user_id`),
  ADD KEY `students_class_id_foreign` (`class_id`),
  ADD KEY `students_deactivated_by_foreign` (`deactivated_by`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subjects_code_unique` (`code`),
  ADD KEY `subjects_class_id_foreign` (`class_id`),
  ADD KEY `subjects_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `exam_questions`
--
ALTER TABLE `exam_questions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grading_scales`
--
ALTER TABLE `grading_scales`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `grading_scale_items`
--
ALTER TABLE `grading_scale_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `report_cards`
--
ALTER TABLE `report_cards`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_class_teacher_id_foreign` FOREIGN KEY (`class_teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exams_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD CONSTRAINT `exam_attempts_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_attempts_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD CONSTRAINT `exam_questions_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_questions_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grading_scale_items`
--
ALTER TABLE `grading_scale_items`
  ADD CONSTRAINT `grading_scale_items_grading_scale_id_foreign` FOREIGN KEY (`grading_scale_id`) REFERENCES `grading_scales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `report_cards`
--
ALTER TABLE `report_cards`
  ADD CONSTRAINT `report_cards_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `report_cards_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `report_cards_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `results_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `results_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_deactivated_by_foreign` FOREIGN KEY (`deactivated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subjects_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
