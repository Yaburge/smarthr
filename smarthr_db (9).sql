-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20250905.4c34850c0b
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 02, 2025 at 06:41 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smarthr_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int NOT NULL,
  `employee_id` int NOT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `status` enum('Present','Late','Absent','Half Day','No Time Out','Early Leave','On Leave') DEFAULT 'Absent',
  `minutes_late` int DEFAULT '0',
  `hours_worked` decimal(4,2) DEFAULT '0.00',
  `is_early_leave` tinyint(1) DEFAULT '0',
  `is_on_leave` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `employee_id`, `date`, `time_in`, `time_out`, `status`, `minutes_late`, `hours_worked`, `is_early_leave`, `is_on_leave`, `created_at`) VALUES
(3, 9, '2025-12-02', '14:03:55', '14:04:12', 'Early Leave', 363, 0.00, 1, 0, '2025-12-02 06:03:55');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_settings`
--

CREATE TABLE `attendance_settings` (
  `setting_id` int NOT NULL,
  `shift_start_time` time NOT NULL DEFAULT '08:00:00',
  `shift_end_time` time NOT NULL DEFAULT '17:00:00',
  `grace_period_minutes` int NOT NULL DEFAULT '15',
  `half_day_hours` decimal(4,2) NOT NULL DEFAULT '4.00',
  `break_duration_minutes` int NOT NULL DEFAULT '60',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attendance_settings`
--

INSERT INTO `attendance_settings` (`setting_id`, `shift_start_time`, `shift_end_time`, `grace_period_minutes`, `half_day_hours`, `break_duration_minutes`, `created_at`, `updated_at`) VALUES
(1, '08:00:00', '17:00:00', 5, 4.00, 60, '2025-12-02 05:07:17', '2025-12-02 05:07:17');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `name`, `description`, `created_at`) VALUES
(3, 'Department1', NULL, '2025-12-01 20:01:18'),
(4, 'Department2', NULL, '2025-12-01 20:03:51'),
(5, 'Department3', NULL, '2025-12-01 20:05:16'),
(6, 'Department6', NULL, '2025-12-01 20:06:07'),
(7, 'mark', NULL, '2025-12-01 20:28:17');

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

CREATE TABLE `designations` (
  `designation_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `department_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `designations`
--

INSERT INTO `designations` (`designation_id`, `name`, `department_id`, `created_at`) VALUES
(1, 'puday', 3, '2025-12-01 21:10:40'),
(2, 'sili', 4, '2025-12-01 22:21:41');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `employee_code` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_initial` varchar(10) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `marital_status` enum('Single','Married','Divorced','Widowed') DEFAULT NULL,
  `address` text,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `department_id` int DEFAULT NULL,
  `designation_id` int DEFAULT NULL,
  `employment_status` enum('Regular','Part-time','Inactive') DEFAULT 'Regular',
  `hire_date` date DEFAULT NULL,
  `degree_suffix` enum('None','Bachelor','Masters','Doctorate') DEFAULT NULL,
  `salary_amount` decimal(10,2) DEFAULT '0.00',
  `salary_type` enum('Monthly','Daily','Hourly') DEFAULT 'Monthly',
  `allowance_amount` decimal(10,2) DEFAULT '0.00',
  `philhealth_no` varchar(50) DEFAULT NULL,
  `pagibig_no` varchar(50) DEFAULT NULL,
  `sss_no` varchar(50) DEFAULT NULL,
  `tin_no` varchar(50) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT 'default_avatar.jpg',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `user_id`, `employee_code`, `first_name`, `middle_initial`, `last_name`, `birthdate`, `gender`, `marital_status`, `address`, `phone_number`, `email`, `department_id`, `designation_id`, `employment_status`, `hire_date`, `degree_suffix`, `salary_amount`, `salary_type`, `allowance_amount`, `philhealth_no`, `pagibig_no`, `sss_no`, `tin_no`, `profile_picture`, `created_at`, `updated_at`) VALUES
(1, NULL, 'EMP-2025-0001', 'dasd', 'asdas', 'dasd', '2025-12-02', 'Male', 'Single', 'sdfsdf', '09055412837', 'gamesnidodot@gmail.com', 3, 1, 'Regular', '2025-12-02', 'Bachelor', 5000.00, 'Monthly', 500.00, '5435435', '5434534', '3213', '213', 'pfp_1764624470_Gemini_Generated_Image_l6a4pll6a4pll6a4-Photoroom.png', '2025-12-01 21:27:50', '2025-12-01 21:27:50'),
(2, NULL, 'EMP-2025-0002', 'Day', 'Day', 'Pu', '2025-12-31', 'Female', 'Married', 'Street 122', '09055412811', 'acemanluyo@gmail.com', 3, 1, 'Part-time', '2025-12-02', 'Doctorate', 6000.00, 'Monthly', 200.00, '523674567', '3432423', '627485762', '3234234', 'pfp_1764625447_5ae0c64e-38bc-4ecd-8203-6b56cf866012.jpg', '2025-12-01 21:44:07', '2025-12-01 21:44:07'),
(3, NULL, 'EMP-2025-0003', 'Justin', 'Sht', 'Baby', '2025-12-02', 'Male', 'Single', 'Street 169', '09055412822', 'acema313luyo@gmail.com', 3, 1, 'Regular', '2025-12-02', 'Masters', 6000.00, 'Monthly', 50000.00, '4764436', '3153624', '787987', '673856987', 'pfp_1764625761_marshall-mathers-iii-drawing-murphy-art-elliott.jpg', '2025-12-01 21:49:21', '2025-12-01 21:49:21'),
(6, NULL, 'EMP-2025-0005', 'Apex', 'Royale', 'Legends', '2025-12-03', 'Male', 'Single', 'Street 169', '09055412855', 'acemanluyosss@gmail.com', 3, 1, 'Part-time', '2025-12-02', 'Masters', 50003.00, 'Monthly', 100.00, '2435432', '23727', '324254', '727527557', 'pfp_1764626115_Gemini_Generated_Image_h27t4ih27t4ih27t-Photoroom.png', '2025-12-01 21:55:15', '2025-12-01 21:55:15'),
(7, NULL, 'EMP-2025-0006', 'Gas', 'T', 'La', '2025-12-04', 'Male', NULL, 'Street 111', '09055412111', 'gamesnido31231dot@gmail.com', 3, 1, 'Regular', '2025-12-02', 'Bachelor', 600022.00, 'Monthly', 5000090.00, '255752828', '222829992', '288282288', '299727745', 'pfp_1764626253_Gemini_Generated_Image_jvca6mjvca6mjvca.png', '2025-12-01 21:57:33', '2025-12-01 21:57:33'),
(8, NULL, 'EMP-2025-0007', 'Siaomai', 'L', 'Rice', '2025-12-05', 'Male', 'Divorced', 'Street 1155', '09055412345', '11gamesnidodot@gmail.com', 4, 2, 'Regular', '2025-12-02', 'Doctorate', 5000111.00, 'Daily', 5001.00, '609696060', '62527738', '066068068006', '99769570808', 'pfp_1764628105_tumb.png', '2025-12-01 22:28:25', '2025-12-02 00:33:39'),
(9, NULL, 'EMP-2025-0008', 'Bin', 'Omar', 'Ladin', '2025-12-24', 'Male', 'Single', 'Street 169 B7 L5', '09055412888', 'gamesn69idodot69@gmail.com', 3, 1, 'Regular', '2025-12-02', 'Masters', 6000.00, 'Monthly', 50.00, '35773577', '388838485', '772939227761', '453398278', 'pfp_1764635774_Screenshot 2025-10-07 181158.png', '2025-12-02 00:36:14', '2025-12-02 00:36:14'),
(10, NULL, 'EMP-2025-0009', 'Kaloy', 'G', 'Manoy', '2025-12-03', 'Male', 'Married', 'Street 12212313', '09055412666', 'kokoy142412@gmail.com', 4, 2, 'Regular', '2025-12-02', 'None', 60002255.00, 'Hourly', 1414.00, '83633863658', '2776495598372', '86386336858', '983836856883', 'pfp_1764656409_Gemini_Generated_Image_cttpxfcttpxfcttp.png', '2025-12-02 06:20:09', '2025-12-02 06:28:04'),
(11, 7, 'EMP-2025-0010', 'Dave', 'Tuban', 'Cupta', '2025-12-12', 'Male', 'Married', 'Street 69', '09055411111', 'aguyesnidodot@gmail.com', 4, 2, 'Regular', '2025-12-02', 'Bachelor', 696969.00, 'Monthly', 69.00, '27577825872', '8984983368', '3868383', '494944949', 'pfp_1764657542_360_F_96683737_rm2HQMEXTHoIHV9Fd4Jj6z8J2fiLvT7c.png', '2025-12-02 06:39:02', '2025-12-02 06:39:02');

-- --------------------------------------------------------

--
-- Table structure for table `employee_documents`
--

CREATE TABLE `employee_documents` (
  `document_id` int NOT NULL,
  `employee_id` int NOT NULL,
  `document_type` enum('Resume','Cover Letter','TOR','Diploma','NBI','Medical','ID','TIN','SSS','Pagibig','Other') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `employee_documents`
--

INSERT INTO `employee_documents` (`document_id`, `employee_id`, `document_type`, `file_path`, `uploaded_at`) VALUES
(1, 1, 'Resume', 'resume_1764624470_resume-sample.pdf', '2025-12-01 21:27:50'),
(2, 1, 'Cover Letter', 'cover_1764624470_resume-sample.pdf', '2025-12-01 21:27:50'),
(3, 1, 'TOR', 'tor_1764624470_resume-sample.pdf', '2025-12-01 21:27:50'),
(4, 1, 'Diploma', 'diploma_1764624470_resume-sample.pdf', '2025-12-01 21:27:50'),
(5, 1, 'NBI', 'nbi_1764624470_resume-sample.pdf', '2025-12-01 21:27:50'),
(6, 1, 'TIN', 'tin_1764624470_resume-sample.pdf', '2025-12-01 21:27:50'),
(7, 1, 'Pagibig', 'pagibig_1764624470_resume-sample.pdf', '2025-12-01 21:27:50'),
(8, 1, 'SSS', 'sss_1764624470_resume-sample.pdf', '2025-12-01 21:27:50'),
(9, 1, 'ID', 'id_1764624470_resume-sample.pdf', '2025-12-01 21:27:50'),
(10, 1, 'Medical', 'medical_1764624470_resume-sample.pdf', '2025-12-01 21:27:50'),
(11, 2, 'Resume', 'resume_1764625447_resume-sample.pdf', '2025-12-01 21:44:07'),
(12, 2, 'Cover Letter', 'cover_1764625447_resume-sample.pdf', '2025-12-01 21:44:07'),
(13, 2, 'TOR', 'tor_1764625447_resume-sample.pdf', '2025-12-01 21:44:07'),
(14, 2, 'Diploma', 'diploma_1764625447_resume-sample.pdf', '2025-12-01 21:44:07'),
(15, 2, 'NBI', 'nbi_1764625447_resume-sample.pdf', '2025-12-01 21:44:07'),
(16, 2, 'TIN', 'tin_1764625447_resume-sample.pdf', '2025-12-01 21:44:07'),
(17, 2, 'Pagibig', 'pagibig_1764625447_resume-sample.pdf', '2025-12-01 21:44:07'),
(18, 2, 'SSS', 'sss_1764625447_resume-sample.pdf', '2025-12-01 21:44:07'),
(19, 2, 'ID', 'id_1764625447_resume-sample.pdf', '2025-12-01 21:44:07'),
(20, 2, 'Medical', 'medical_1764625447_resume-sample.pdf', '2025-12-01 21:44:07'),
(21, 3, 'Resume', 'resume_1764625761_resume-sample.pdf', '2025-12-01 21:49:21'),
(22, 3, 'Cover Letter', 'cover_1764625761_resume-sample.pdf', '2025-12-01 21:49:21'),
(23, 3, 'TOR', 'tor_1764625761_resume-sample.pdf', '2025-12-01 21:49:21'),
(24, 3, 'Diploma', 'diploma_1764625761_resume-sample.pdf', '2025-12-01 21:49:21'),
(25, 3, 'NBI', 'nbi_1764625761_resume-sample.pdf', '2025-12-01 21:49:21'),
(26, 3, 'TIN', 'tin_1764625761_resume-sample.pdf', '2025-12-01 21:49:21'),
(27, 3, 'Pagibig', 'pagibig_1764625761_resume-sample.pdf', '2025-12-01 21:49:21'),
(28, 3, 'SSS', 'sss_1764625761_resume-sample.pdf', '2025-12-01 21:49:21'),
(29, 3, 'ID', 'id_1764625761_resume-sample.pdf', '2025-12-01 21:49:21'),
(30, 3, 'Medical', 'medical_1764625761_resume-sample.pdf', '2025-12-01 21:49:21'),
(41, 6, 'Resume', 'resume_1764626115_resume-sample.pdf', '2025-12-01 21:55:15'),
(42, 6, 'Cover Letter', 'cover_1764626115_resume-sample.pdf', '2025-12-01 21:55:15'),
(43, 6, 'TOR', 'tor_1764626115_resume-sample.pdf', '2025-12-01 21:55:15'),
(44, 6, 'Diploma', 'diploma_1764626115_resume-sample.pdf', '2025-12-01 21:55:15'),
(45, 6, 'NBI', 'nbi_1764626115_resume-sample.pdf', '2025-12-01 21:55:15'),
(46, 6, 'TIN', 'tin_1764626115_resume-sample.pdf', '2025-12-01 21:55:15'),
(47, 6, 'Pagibig', 'pagibig_1764626115_resume-sample.pdf', '2025-12-01 21:55:15'),
(48, 6, 'SSS', 'sss_1764626115_resume-sample.pdf', '2025-12-01 21:55:15'),
(49, 6, 'ID', 'id_1764626115_resume-sample.pdf', '2025-12-01 21:55:15'),
(50, 6, 'Medical', 'medical_1764626115_resume-sample.pdf', '2025-12-01 21:55:15'),
(51, 7, 'Resume', 'resume_1764626253_resume-sample.pdf', '2025-12-01 21:57:33'),
(52, 7, 'Cover Letter', 'cover_1764626253_resume-sample.pdf', '2025-12-01 21:57:33'),
(53, 7, 'TOR', 'tor_1764626253_resume-sample.pdf', '2025-12-01 21:57:33'),
(54, 7, 'Diploma', 'diploma_1764626253_resume-sample.pdf', '2025-12-01 21:57:33'),
(55, 7, 'NBI', 'nbi_1764626253_resume-sample.pdf', '2025-12-01 21:57:33'),
(56, 7, 'TIN', 'tin_1764626253_resume-sample.pdf', '2025-12-01 21:57:33'),
(57, 7, 'Pagibig', 'pagibig_1764626253_resume-sample.pdf', '2025-12-01 21:57:33'),
(58, 7, 'SSS', 'sss_1764626253_resume-sample.pdf', '2025-12-01 21:57:33'),
(59, 7, 'ID', 'id_1764626253_resume-sample.pdf', '2025-12-01 21:57:33'),
(60, 7, 'Medical', 'medical_1764626253_resume-sample.pdf', '2025-12-01 21:57:33'),
(61, 8, 'Resume', 'resume_1764628105_resume-sample.pdf', '2025-12-01 22:28:25'),
(62, 8, 'Cover Letter', 'cover_1764628105_resume-sample.pdf', '2025-12-01 22:28:25'),
(63, 8, 'TOR', 'tor_1764628105_resume-sample.pdf', '2025-12-01 22:28:25'),
(64, 8, 'Diploma', 'diploma_1764628105_resume-sample.pdf', '2025-12-01 22:28:25'),
(65, 8, 'NBI', 'nbi_1764628105_resume-sample.pdf', '2025-12-01 22:28:25'),
(66, 8, 'TIN', 'tin_1764628105_resume-sample.pdf', '2025-12-01 22:28:25'),
(67, 8, 'Pagibig', 'pagibig_1764628105_resume-sample.pdf', '2025-12-01 22:28:25'),
(68, 8, 'SSS', 'sss_1764628105_resume-sample.pdf', '2025-12-01 22:28:25'),
(70, 8, 'Medical', 'medical_1764628105_resume-sample.pdf', '2025-12-01 22:28:25'),
(72, 8, 'ID', 'id_1764632220_Inventory Report - December 2020.pdf', '2025-12-01 23:37:00'),
(73, 9, 'Resume', 'resume_1764635774_resume-sample.pdf', '2025-12-02 00:36:14'),
(74, 9, 'Cover Letter', 'cover_1764635774_resume-sample.pdf', '2025-12-02 00:36:14'),
(75, 9, 'TOR', 'tor_1764635774_resume-sample.pdf', '2025-12-02 00:36:14'),
(76, 9, 'Diploma', 'diploma_1764635774_resume-sample.pdf', '2025-12-02 00:36:14'),
(77, 9, 'NBI', 'nbi_1764635774_resume-sample.pdf', '2025-12-02 00:36:14'),
(78, 9, 'TIN', 'tin_1764635774_resume-sample.pdf', '2025-12-02 00:36:14'),
(79, 9, 'Pagibig', 'pagibig_1764635774_resume-sample.pdf', '2025-12-02 00:36:14'),
(80, 9, 'SSS', 'sss_1764635774_internmatch_student_progress_monitoring_2025-11-30 (1).pdf', '2025-12-02 00:36:14'),
(81, 9, 'ID', 'id_1764635774_resume-sample.pdf', '2025-12-02 00:36:14'),
(82, 9, 'Medical', 'medical_1764635774_resume-sample.pdf', '2025-12-02 00:36:14'),
(83, 10, 'Resume', 'resume_1764656409_resume-sample.pdf', '2025-12-02 06:20:09'),
(84, 10, 'Cover Letter', 'cover_1764656409_resume-sample.pdf', '2025-12-02 06:20:09'),
(85, 10, 'TOR', 'tor_1764656409_resume-sample.pdf', '2025-12-02 06:20:09'),
(86, 10, 'Diploma', 'diploma_1764656409_resume-sample.pdf', '2025-12-02 06:20:09'),
(87, 10, 'NBI', 'nbi_1764656409_resume-sample.pdf', '2025-12-02 06:20:09'),
(88, 10, 'TIN', 'tin_1764656409_resume-sample.pdf', '2025-12-02 06:20:09'),
(89, 10, 'Pagibig', 'pagibig_1764656409_resume-sample.pdf', '2025-12-02 06:20:09'),
(90, 10, 'SSS', 'sss_1764656409_resume-sample.pdf', '2025-12-02 06:20:09'),
(91, 10, 'ID', 'id_1764656409_resume-sample.pdf', '2025-12-02 06:20:09'),
(92, 10, 'Medical', 'medical_1764656409_resume-sample.pdf', '2025-12-02 06:20:09'),
(93, 11, 'Resume', 'resume_1764657542_resume-sample.pdf', '2025-12-02 06:39:02'),
(94, 11, 'Cover Letter', 'cover_1764657542_resume-sample.pdf', '2025-12-02 06:39:02'),
(95, 11, 'TOR', 'tor_1764657542_resume-sample.pdf', '2025-12-02 06:39:02'),
(96, 11, 'Diploma', 'diploma_1764657542_resume-sample.pdf', '2025-12-02 06:39:02'),
(97, 11, 'NBI', 'nbi_1764657542_resume-sample.pdf', '2025-12-02 06:39:02'),
(98, 11, 'TIN', 'tin_1764657542_resume-sample.pdf', '2025-12-02 06:39:02'),
(99, 11, 'Pagibig', 'pagibig_1764657542_resume-sample.pdf', '2025-12-02 06:39:02'),
(100, 11, 'SSS', 'sss_1764657542_resume-sample.pdf', '2025-12-02 06:39:02'),
(101, 11, 'ID', 'id_1764657542_resume-sample.pdf', '2025-12-02 06:39:02'),
(102, 11, 'Medical', 'medical_1764657542_resume-sample.pdf', '2025-12-02 06:39:02');

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `holiday_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `type` enum('Regular','Special Non-Working') DEFAULT 'Regular'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `request_id` int NOT NULL,
  `employee_id` int NOT NULL,
  `leave_type_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text,
  `attachment_path` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `leave_type_id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `is_paid` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `overtime_requests`
--

CREATE TABLE `overtime_requests` (
  `ot_id` int NOT NULL,
  `employee_id` int NOT NULL,
  `request_date` date NOT NULL,
  `overtime_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `duration_hours` decimal(4,2) NOT NULL,
  `reason` text,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_items`
--

CREATE TABLE `payroll_items` (
  `item_id` int NOT NULL,
  `payroll_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('Earning','Deduction') NOT NULL,
  `category` enum('Statutory','Other','Attendance','Basic') DEFAULT 'Other'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_periods`
--

CREATE TABLE `payroll_periods` (
  `period_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `type` enum('Monthly','Semi-Monthly') DEFAULT 'Semi-Monthly',
  `status` enum('Draft','Locked','Paid') DEFAULT 'Draft',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_records`
--

CREATE TABLE `payroll_records` (
  `payroll_id` int NOT NULL,
  `period_id` int NOT NULL,
  `employee_id` int NOT NULL,
  `basic_salary_snapshot` decimal(10,2) DEFAULT NULL,
  `hourly_rate_snapshot` decimal(10,2) DEFAULT NULL,
  `gross_pay` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_allowance` decimal(10,2) DEFAULT '0.00',
  `total_overtime_pay` decimal(10,2) DEFAULT '0.00',
  `total_deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_tardiness_deduction` decimal(10,2) DEFAULT '0.00',
  `net_pay` decimal(10,2) NOT NULL DEFAULT '0.00',
  `date_generated` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('Admin','Employee','HR') DEFAULT 'Employee',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `role`, `is_active`, `created_at`) VALUES
(1, 'admin', '$2y$10$3//h2Jc839HNVwt7VnANwe2Fi3WWKjQ/RVrROOdVtkMeVha1oh4fe', 'Admin', 1, '2025-12-01 18:54:20'),
(7, 'dave', '$2y$10$n.VzqsjVXP4goshfhGnetOc0.oXayCEWwY1lqWkJ6h.8Iyvx2Ll0K', 'Employee', 1, '2025-12-02 06:39:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `idx_employee_date` (`employee_id`,`date`);

--
-- Indexes for table `attendance_settings`
--
ALTER TABLE `attendance_settings`
  ADD PRIMARY KEY (`setting_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `designations`
--
ALTER TABLE `designations`
  ADD PRIMARY KEY (`designation_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `employee_code` (`employee_code`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `designation_id` (`designation_id`);

--
-- Indexes for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`holiday_id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `leave_type_id` (`leave_type_id`),
  ADD KEY `idx_leave_status` (`employee_id`,`status`,`start_date`,`end_date`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`leave_type_id`);

--
-- Indexes for table `overtime_requests`
--
ALTER TABLE `overtime_requests`
  ADD PRIMARY KEY (`ot_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `payroll_items`
--
ALTER TABLE `payroll_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `payroll_id` (`payroll_id`);

--
-- Indexes for table `payroll_periods`
--
ALTER TABLE `payroll_periods`
  ADD PRIMARY KEY (`period_id`);

--
-- Indexes for table `payroll_records`
--
ALTER TABLE `payroll_records`
  ADD PRIMARY KEY (`payroll_id`),
  ADD KEY `period_id` (`period_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `attendance_settings`
--
ALTER TABLE `attendance_settings`
  MODIFY `setting_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `designations`
--
ALTER TABLE `designations`
  MODIFY `designation_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `employee_documents`
--
ALTER TABLE `employee_documents`
  MODIFY `document_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `holiday_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `request_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `leave_type_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `overtime_requests`
--
ALTER TABLE `overtime_requests`
  MODIFY `ot_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_items`
--
ALTER TABLE `payroll_items`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_periods`
--
ALTER TABLE `payroll_periods`
  MODIFY `period_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_records`
--
ALTER TABLE `payroll_records`
  MODIFY `payroll_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `designations`
--
ALTER TABLE `designations`
  ADD CONSTRAINT `designations_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE SET NULL;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_ibfk_3` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`designation_id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD CONSTRAINT `employee_documents_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_requests_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`leave_type_id`);

--
-- Constraints for table `overtime_requests`
--
ALTER TABLE `overtime_requests`
  ADD CONSTRAINT `overtime_requests_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll_items`
--
ALTER TABLE `payroll_items`
  ADD CONSTRAINT `payroll_items_ibfk_1` FOREIGN KEY (`payroll_id`) REFERENCES `payroll_records` (`payroll_id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll_records`
--
ALTER TABLE `payroll_records`
  ADD CONSTRAINT `payroll_records_ibfk_1` FOREIGN KEY (`period_id`) REFERENCES `payroll_periods` (`period_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payroll_records_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
