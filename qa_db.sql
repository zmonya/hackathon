-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2025 at 03:43 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qa_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `visit_date` date NOT NULL,
  `age` int(11) DEFAULT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `region` varchar(100) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `office_id` int(11) NOT NULL,
  `service_availed` varchar(255) NOT NULL,
  `community` enum('Faculty/Staff','Students','Visitor') NOT NULL,
  `cc1` enum('1 - I know what a CC is and I saw this office''s CC','2 - I know what a CC is but I did NOT see this office''s CC','3 - I learned of the CC only when I saw this office''s CC','4 - I do not know what a CC is and I did not see one') DEFAULT NULL,
  `cc2` enum('1 - Easy to see','2 - Somewhat easy to see','3 - Difficult to see','4 - Not visible at all','5 - N/A') DEFAULT NULL,
  `cc3` enum('1 - Helped very much','2 - Somewhat helped','3 - Did not help','4 - N/A') DEFAULT NULL,
  `sqd0` enum('1','2','3','4','5','NA') NOT NULL,
  `sqd1` enum('1','2','3','4','5','NA') NOT NULL,
  `sqd2` enum('1','2','3','4','5','NA') NOT NULL,
  `sqd3` enum('1','2','3','4','5','NA') NOT NULL,
  `sqd4` enum('1','2','3','4','5','NA') NOT NULL,
  `sqd5` enum('1','2','3','4','5','NA') NOT NULL,
  `sqd6` enum('1','2','3','4','5','NA') NOT NULL,
  `sqd7` enum('1','2','3','4','5','NA') NOT NULL,
  `sqd8` enum('1','2','3','4','5','NA') NOT NULL,
  `sqd_average` decimal(3,1) DEFAULT NULL,
  `service_type` enum('Good','Bad') DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `visit_date`, `age`, `sex`, `region`, `phone_number`, `office_id`, `service_availed`, `community`, `cc1`, `cc2`, `cc3`, `sqd0`, `sqd1`, `sqd2`, `sqd3`, `sqd4`, `sqd5`, `sqd6`, `sqd7`, `sqd8`, `sqd_average`, `service_type`, `comments`, `submitted_at`) VALUES
(1, '2025-02-14', 25, 'Female', 'Region III', '09444748952', 1, 'Grades Inquiry', 'Faculty/Staff', '4 - I do not know what a CC is and I did not see one', '3 - Difficult to see', '2 - Somewhat helped', 'NA', '3', '4', 'NA', '1', '4', 'NA', 'NA', '4', '3.2', 'Good', 'Efficient and polite.', '2025-02-14 08:56:00'),
(2, '2025-01-23', 30, 'Female', 'Region I', '09786686935', 1, 'Form Request', 'Faculty/Staff', '1 - I know what a CC is and I saw this office\'s CC', '3 - Difficult to see', '1 - Helped very much', '4', '4', 'NA', '5', 'NA', '4', '2', '1', '2', '3.1', 'Bad', 'Unclear instructions.', '2025-01-23 05:12:00'),
(3, '2025-02-03', 29, 'Male', 'Region IV-A', '09773089926', 1, 'Subject Advising', 'Faculty/Staff', '4 - I do not know what a CC is and I did not see one', '1 - Easy to see', '2 - Somewhat helped', '1', '1', '5', '2', '5', '2', 'NA', 'NA', '4', '2.9', 'Good', 'Very helpful.', '2025-02-03 03:04:00'),
(4, '2025-03-25', 19, 'Female', 'Region V', '09851851994', 1, 'Consultation', 'Visitor', '1 - I know what a CC is and I saw this office\'s CC', '2 - Somewhat easy to see', '2 - Somewhat helped', '1', '3', '3', '4', '1', '2', '3', '4', '3', '2.7', 'Bad', 'Needs improvement.', '2025-03-25 06:55:00'),
(5, '2025-02-05', 30, 'Female', 'Region I', '09846022735', 1, 'Grades Inquiry', 'Visitor', '4 - I do not know what a CC is and I did not see one', '4 - Not visible at all', '3 - Did not help', '1', '4', 'NA', '3', '3', '4', '2', '5', '2', '3.0', 'Bad', 'Crowded and slow.', '2025-02-05 02:57:00'),
(6, '2025-01-03', 19, 'Male', 'Region V', '09839503089', 1, 'Subject Advising', 'Students', '3 - I learned of the CC only when I saw this office\'s CC', '1 - Easy to see', '2 - Somewhat helped', '3', '2', '4', '5', 'NA', 'NA', '4', 'NA', '3', '3.5', 'Bad', 'Needs improvement.', '2025-01-03 01:25:00'),
(7, '2025-02-13', 21, 'Female', 'Region V', '09336484831', 1, 'Form Request', 'Visitor', '2 - I know what a CC is but I did NOT see this office\'s CC', '5 - N/A', '1 - Helped very much', '5', '4', 'NA', '1', '1', '5', '3', '3', '5', '3.4', 'Bad', 'Long waiting time.', '2025-02-13 06:19:00'),
(8, '2025-03-17', 18, 'Male', 'Region II', '09989193142', 1, 'Subject Advising', 'Students', '4 - I do not know what a CC is and I did not see one', '2 - Somewhat easy to see', '1 - Helped very much', 'NA', '4', 'NA', '4', '3', 'NA', '5', '2', '1', '3.2', 'Bad', 'Long waiting time.', '2025-03-17 05:31:00'),
(9, '2025-02-11', 22, 'Male', 'Region II', '09604544867', 1, 'Subject Advising', 'Faculty/Staff', '2 - I know what a CC is but I did NOT see this office\'s CC', '5 - N/A', '2 - Somewhat helped', '2', 'NA', '3', 'NA', 'NA', '1', 'NA', '5', '3', '2.8', 'Bad', 'Unclear instructions.', '2025-02-11 09:23:00'),
(10, '2025-01-30', 23, 'Female', 'Region III', '09558133620', 1, 'Grades Inquiry', 'Visitor', '4 - I do not know what a CC is and I did not see one', '4 - Not visible at all', '3 - Did not help', '1', '3', '5', '2', '3', '2', '3', '3', 'NA', '2.8', 'Bad', 'Needs improvement.', '2025-01-30 01:06:00'),
(11, '2025-01-26', 26, 'Female', 'Region III', '09184162879', 2, 'Subject Advising', 'Visitor', '2 - I know what a CC is but I did NOT see this office\'s CC', '2 - Somewhat easy to see', '2 - Somewhat helped', '3', '3', '2', '4', '1', '4', '1', '3', '2', '2.6', 'Good', 'Smooth process.', '2025-01-26 01:05:00'),
(12, '2025-02-09', 23, 'Female', 'Region I', '09635138668', 2, 'Consultation', 'Faculty/Staff', '4 - I do not know what a CC is and I did not see one', '5 - N/A', '3 - Did not help', '4', '1', '3', '2', '2', '3', '3', 'NA', 'NA', '2.6', 'Good', 'Great service!', '2025-02-09 08:37:00'),
(13, '2025-03-29', 30, 'Male', 'Region III', '09857700180', 2, 'Consultation', 'Faculty/Staff', '4 - I do not know what a CC is and I did not see one', '1 - Easy to see', '2 - Somewhat helped', '3', '4', '4', '4', '4', '3', '5', '1', '2', '3.3', 'Bad', 'Needs improvement.', '2025-03-29 09:58:00'),
(14, '2025-02-27', 27, 'Male', 'Region IV-A', '09128881762', 2, 'Subject Advising', 'Visitor', '4 - I do not know what a CC is and I did not see one', '3 - Difficult to see', '3 - Did not help', '2', '4', 'NA', '5', '3', '1', '5', '3', '3', '3.2', 'Good', 'Friendly staff.', '2025-02-27 08:44:00'),
(15, '2025-03-29', 23, 'Male', 'Region II', '09355658667', 2, 'Grades Inquiry', 'Visitor', '3 - I learned of the CC only when I saw this office\'s CC', '4 - Not visible at all', '3 - Did not help', '3', '2', 'NA', '4', 'NA', '3', '2', 'NA', '2', '2.7', 'Bad', 'Needs improvement.', '2025-03-29 03:08:00'),
(16, '2025-02-15', 29, 'Female', 'Region I', '09421909337', 2, 'Consultation', 'Students', '1 - I know what a CC is and I saw this office\'s CC', '2 - Somewhat easy to see', '2 - Somewhat helped', '3', '5', '2', '3', '1', '4', '4', 'NA', '3', '3.1', 'Bad', 'Long waiting time.', '2025-02-15 04:54:00'),
(17, '2025-02-16', 30, 'Female', 'Region III', '09346164560', 2, 'Grades Inquiry', 'Faculty/Staff', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '3 - Did not help', '4', 'NA', '4', 'NA', '1', '2', 'NA', '2', '3', '2.7', 'Good', 'Very helpful.', '2025-02-16 08:51:00'),
(18, '2025-01-09', 20, 'Male', 'Region I', '09266276708', 2, 'Subject Advising', 'Visitor', '2 - I know what a CC is but I did NOT see this office\'s CC', '3 - Difficult to see', '3 - Did not help', 'NA', '4', 'NA', '3', '5', 'NA', '2', '1', 'NA', '3.0', 'Bad', 'Needs improvement.', '2025-01-09 06:44:00'),
(19, '2025-03-13', 19, 'Male', 'Region I', '09147267071', 2, 'Grades Inquiry', 'Students', '3 - I learned of the CC only when I saw this office\'s CC', '3 - Difficult to see', '4 - N/A', '2', '1', '5', '1', '4', '3', '3', '4', '2', '2.8', 'Good', 'Great service!', '2025-03-13 09:21:00'),
(20, '2025-02-07', 27, 'Male', 'Region II', '09586669039', 2, 'Form Request', 'Faculty/Staff', '2 - I know what a CC is but I did NOT see this office\'s CC', '4 - Not visible at all', '4 - N/A', '3', '2', '1', 'NA', '1', '3', '4', '5', '2', '2.6', 'Good', 'Efficient and polite.', '2025-02-07 07:49:00'),
(21, '2025-01-08', 23, 'Male', 'Region IV-A', '09551041911', 3, 'Consultation', 'Students', '1 - I know what a CC is and I saw this office\'s CC', '3 - Difficult to see', '3 - Did not help', '4', '2', '5', '2', 'NA', '1', '5', 'NA', '4', '3.3', 'Good', 'Smooth process.', '2025-01-08 09:27:00'),
(22, '2025-01-04', 19, 'Male', 'Region II', '09556125157', 3, 'Grades Inquiry', 'Students', '2 - I know what a CC is but I did NOT see this office\'s CC', '4 - Not visible at all', '2 - Somewhat helped', '2', '5', '1', '4', '4', '3', '1', '2', '3', '2.8', 'Bad', 'Unclear instructions.', '2025-01-04 06:35:00'),
(23, '2025-03-23', 25, 'Female', 'Region V', '09548130983', 3, 'Consultation', 'Faculty/Staff', '3 - I learned of the CC only when I saw this office\'s CC', '4 - Not visible at all', '3 - Did not help', '5', '2', '1', '3', '4', '5', '1', '4', '5', '3.3', 'Bad', 'Staff not helpful.', '2025-03-23 04:03:00'),
(24, '2025-03-29', 30, 'Female', 'Region III', '09431811378', 3, 'Form Request', 'Faculty/Staff', '4 - I do not know what a CC is and I did not see one', '2 - Somewhat easy to see', '1 - Helped very much', '5', '4', '5', '4', '4', '1', '3', '3', '4', '3.7', 'Bad', 'Needs improvement.', '2025-03-29 05:35:00'),
(25, '2025-03-30', 19, 'Male', 'Region I', '09292691563', 3, 'Enrollment', 'Visitor', '2 - I know what a CC is but I did NOT see this office\'s CC', '5 - N/A', '1 - Helped very much', '3', '5', '2', '3', '1', '5', '5', 'NA', '1', '3.1', 'Good', 'Very helpful.', '2025-03-30 07:22:00'),
(26, '2025-02-07', 23, 'Male', 'Region III', '09871922301', 3, 'Grades Inquiry', 'Visitor', '3 - I learned of the CC only when I saw this office\'s CC', '3 - Difficult to see', '4 - N/A', '5', '4', '1', '2', 'NA', 'NA', '5', 'NA', '4', '3.5', 'Good', 'Very helpful.', '2025-02-07 06:57:00'),
(27, '2025-02-01', 20, 'Male', 'Region I', '09815996647', 3, 'Consultation', 'Faculty/Staff', '1 - I know what a CC is and I saw this office\'s CC', '5 - N/A', '1 - Helped very much', '5', '4', '4', '5', 'NA', '2', '1', 'NA', '5', '3.7', 'Bad', 'Needs improvement.', '2025-02-01 05:16:00'),
(28, '2025-01-16', 20, 'Female', 'Region III', '09569914280', 3, 'Subject Advising', 'Faculty/Staff', '2 - I know what a CC is but I did NOT see this office\'s CC', '2 - Somewhat easy to see', '2 - Somewhat helped', '1', '1', '5', '3', '4', '1', 'NA', '1', '2', '2.2', 'Bad', 'Unclear instructions.', '2025-01-16 09:31:00'),
(29, '2025-02-21', 20, 'Female', 'Region I', '09884827867', 3, 'Consultation', 'Faculty/Staff', '3 - I learned of the CC only when I saw this office\'s CC', '5 - N/A', '4 - N/A', '1', '4', '3', '1', '4', '2', '2', '3', '2', '2.4', 'Good', 'Friendly staff.', '2025-02-21 08:14:00'),
(30, '2025-02-22', 26, 'Female', 'Region II', '09423310429', 3, 'Enrollment', 'Students', '4 - I do not know what a CC is and I did not see one', '2 - Somewhat easy to see', '2 - Somewhat helped', '4', 'NA', '1', '1', '4', '3', '1', '1', '5', '2.5', 'Good', 'Friendly staff.', '2025-02-22 05:24:00'),
(31, '2025-02-02', 26, 'Female', 'Region IV-A', '09444531524', 4, 'Form Request', 'Students', '1 - I know what a CC is and I saw this office\'s CC', '3 - Difficult to see', '2 - Somewhat helped', '1', '4', '2', '2', '1', 'NA', 'NA', '5', '5', '2.9', 'Good', 'Efficient and polite.', '2025-02-02 07:21:00'),
(32, '2025-01-14', 20, 'Female', 'Region II', '09143897498', 4, 'Grades Inquiry', 'Faculty/Staff', '3 - I learned of the CC only when I saw this office\'s CC', '3 - Difficult to see', '1 - Helped very much', 'NA', 'NA', '2', '3', '3', '5', '4', '3', '1', '3.0', 'Bad', 'Crowded and slow.', '2025-01-14 03:09:00'),
(33, '2025-02-26', 18, 'Male', 'Region IV-A', '09213920171', 4, 'Subject Advising', 'Faculty/Staff', '4 - I do not know what a CC is and I did not see one', '2 - Somewhat easy to see', '3 - Did not help', '2', '5', 'NA', '2', '4', '2', '4', '5', '4', '3.5', 'Bad', 'Staff not helpful.', '2025-02-26 03:39:00'),
(34, '2025-01-07', 24, 'Female', 'Region IV-A', '09868346563', 4, 'Enrollment', 'Students', '3 - I learned of the CC only when I saw this office\'s CC', '2 - Somewhat easy to see', '2 - Somewhat helped', '5', '2', '1', '3', '2', '5', '1', '3', 'NA', '2.8', 'Good', 'Great service!', '2025-01-07 00:37:00'),
(35, '2025-01-22', 29, 'Male', 'Region IV-A', '09841777883', 4, 'Grades Inquiry', 'Faculty/Staff', '3 - I learned of the CC only when I saw this office\'s CC', '5 - N/A', '4 - N/A', '3', '4', '5', '4', 'NA', '1', 'NA', '5', '2', '3.4', 'Good', 'Smooth process.', '2025-01-22 05:49:00'),
(36, '2025-02-07', 27, 'Female', 'Region IV-A', '09788617545', 4, 'Subject Advising', 'Faculty/Staff', '3 - I learned of the CC only when I saw this office\'s CC', '2 - Somewhat easy to see', '1 - Helped very much', '5', '2', '5', 'NA', '5', '3', 'NA', '4', '5', '4.1', 'Good', 'Efficient and polite.', '2025-02-07 06:16:00'),
(37, '2025-02-21', 30, 'Male', 'Region IV-A', '09535711967', 4, 'Subject Advising', 'Students', '2 - I know what a CC is but I did NOT see this office\'s CC', '3 - Difficult to see', '1 - Helped very much', '2', '3', '4', '3', '4', '1', 'NA', '5', '3', '3.1', 'Bad', 'Crowded and slow.', '2025-02-21 01:05:00'),
(38, '2025-02-23', 19, 'Male', 'Region I', '09321747522', 4, 'Enrollment', 'Visitor', '4 - I do not know what a CC is and I did not see one', '2 - Somewhat easy to see', '1 - Helped very much', '3', 'NA', '2', '2', '3', '3', '1', 'NA', '5', '2.7', 'Bad', 'Crowded and slow.', '2025-02-23 07:33:00'),
(39, '2025-03-30', 24, 'Female', 'Region IV-A', '09244041911', 4, 'Consultation', 'Faculty/Staff', '1 - I know what a CC is and I saw this office\'s CC', '4 - Not visible at all', '3 - Did not help', 'NA', '4', '4', '1', '3', '2', '3', '3', 'NA', '2.9', 'Good', 'Great service!', '2025-03-30 04:05:00'),
(40, '2025-04-01', 22, 'Female', 'Region IV-A', '09392991482', 4, 'Consultation', 'Faculty/Staff', '1 - I know what a CC is and I saw this office\'s CC', '5 - N/A', '3 - Did not help', '2', '5', '3', '2', '3', '5', '4', '5', '4', '3.7', 'Bad', 'Long waiting time.', '2025-04-01 01:24:00'),
(41, '2025-02-10', 24, 'Female', 'Region I', '09189360116', 5, 'Enrollment', 'Faculty/Staff', '2 - I know what a CC is but I did NOT see this office\'s CC', '5 - N/A', '2 - Somewhat helped', '4', '4', '2', '1', '4', '1', '1', '3', 'NA', '2.5', 'Good', 'Very helpful.', '2025-02-10 07:55:00'),
(42, '2025-01-06', 23, 'Male', 'Region II', '09243768244', 5, 'Enrollment', 'Faculty/Staff', '1 - I know what a CC is and I saw this office\'s CC', '2 - Somewhat easy to see', '1 - Helped very much', '1', '2', 'NA', '4', '2', '4', '4', '5', '5', '3.4', 'Good', 'Very helpful.', '2025-01-06 07:10:00'),
(43, '2025-01-14', 22, 'Female', 'Region II', '09376612489', 5, 'Grades Inquiry', 'Visitor', '4 - I do not know what a CC is and I did not see one', '2 - Somewhat easy to see', '3 - Did not help', 'NA', '5', '3', 'NA', '2', '2', '3', '4', '5', '3.4', 'Good', 'Friendly staff.', '2025-01-14 06:36:00'),
(44, '2025-01-26', 24, 'Male', 'Region II', '09984589125', 5, 'Enrollment', 'Visitor', '1 - I know what a CC is and I saw this office\'s CC', '3 - Difficult to see', '2 - Somewhat helped', '5', '4', '1', '2', '3', '4', '4', '4', '2', '3.2', 'Good', 'Friendly staff.', '2025-01-26 08:50:00'),
(45, '2025-03-23', 23, 'Male', 'Region III', '09163890873', 5, 'Form Request', 'Faculty/Staff', '3 - I learned of the CC only when I saw this office\'s CC', '3 - Difficult to see', '1 - Helped very much', 'NA', '3', '2', '1', 'NA', 'NA', 'NA', '4', '4', '2.8', 'Good', 'Very helpful.', '2025-03-23 01:40:00'),
(46, '2025-01-16', 27, 'Male', 'Region II', '09929051793', 5, 'Consultation', 'Students', '4 - I do not know what a CC is and I did not see one', '3 - Difficult to see', '3 - Did not help', '2', '1', '3', '1', '5', '3', '2', '2', '1', '2.2', 'Good', 'Friendly staff.', '2025-01-16 04:37:00'),
(47, '2025-02-06', 27, 'Female', 'Region V', '09129810221', 5, 'Form Request', 'Visitor', '4 - I do not know what a CC is and I did not see one', '1 - Easy to see', '1 - Helped very much', '4', 'NA', '2', '5', '2', '2', 'NA', 'NA', '5', '3.3', 'Good', 'Smooth process.', '2025-02-06 09:16:00'),
(48, '2025-02-19', 26, 'Female', 'Region III', '09143122516', 5, 'Grades Inquiry', 'Students', '4 - I do not know what a CC is and I did not see one', '5 - N/A', '2 - Somewhat helped', '3', '2', '3', '2', '5', '5', '4', 'NA', '5', '3.6', 'Good', 'Smooth process.', '2025-02-19 01:45:00'),
(49, '2025-01-03', 20, 'Male', 'Region II', '09965018616', 5, 'Grades Inquiry', 'Faculty/Staff', '4 - I do not know what a CC is and I did not see one', '5 - N/A', '2 - Somewhat helped', '2', '5', '2', '1', '4', '5', '3', '3', '2', '3.0', 'Good', 'Great service!', '2025-01-03 05:51:00'),
(50, '2025-03-06', 21, 'Female', 'Region I', '09656069956', 5, 'Subject Advising', 'Visitor', '3 - I learned of the CC only when I saw this office\'s CC', '1 - Easy to see', '2 - Somewhat helped', '2', 'NA', '2', 'NA', '1', '3', '1', '1', 'NA', '1.7', 'Good', 'Friendly staff.', '2025-03-06 03:59:00'),
(51, '2025-02-24', 23, 'Male', 'Region I', '09292105090', 6, 'Consultation', 'Students', '1 - I know what a CC is and I saw this office\'s CC', '5 - N/A', '3 - Did not help', '3', '5', '2', '3', '2', '2', '2', '4', 'NA', '2.9', 'Bad', 'Crowded and slow.', '2025-02-24 06:11:00'),
(52, '2025-01-25', 22, 'Male', 'Region II', '09753630159', 6, 'Form Request', 'Students', '1 - I know what a CC is and I saw this office\'s CC', '4 - Not visible at all', '3 - Did not help', 'NA', '4', '5', '4', '2', '2', 'NA', 'NA', '3', '3.3', 'Bad', 'Crowded and slow.', '2025-01-25 05:56:00'),
(53, '2025-01-16', 25, 'Female', 'Region V', '09146902667', 6, 'Grades Inquiry', 'Visitor', '4 - I do not know what a CC is and I did not see one', '3 - Difficult to see', '3 - Did not help', '5', '2', '1', '3', 'NA', '2', '5', '1', 'NA', '2.7', 'Bad', 'Crowded and slow.', '2025-01-16 00:34:00'),
(54, '2025-02-04', 26, 'Male', 'Region V', '09259728977', 6, 'Grades Inquiry', 'Visitor', '4 - I do not know what a CC is and I did not see one', '3 - Difficult to see', '4 - N/A', '4', '4', '2', '2', '2', '1', '4', '4', '3', '2.9', 'Good', 'Friendly staff.', '2025-02-04 04:06:00'),
(55, '2025-03-10', 29, 'Female', 'Region I', '09179143578', 6, 'Consultation', 'Students', '4 - I do not know what a CC is and I did not see one', '1 - Easy to see', '1 - Helped very much', 'NA', '4', '4', '1', 'NA', '4', '2', '1', '4', '2.9', 'Good', 'Efficient and polite.', '2025-03-10 05:48:00'),
(56, '2025-03-17', 24, 'Male', 'Region II', '09396367989', 6, 'Subject Advising', 'Visitor', '1 - I know what a CC is and I saw this office\'s CC', '5 - N/A', '4 - N/A', '5', '1', '3', '2', 'NA', '5', '4', '3', '2', '3.1', 'Good', 'Smooth process.', '2025-03-17 01:26:00'),
(57, '2025-02-16', 19, 'Female', 'Region V', '09682293315', 6, 'Subject Advising', 'Visitor', '1 - I know what a CC is and I saw this office\'s CC', '5 - N/A', '2 - Somewhat helped', '5', '4', '2', 'NA', '2', '1', '4', '1', '1', '2.5', 'Bad', 'Crowded and slow.', '2025-02-16 04:59:00'),
(58, '2025-03-17', 23, 'Female', 'Region III', '09859979459', 6, 'Subject Advising', 'Visitor', '4 - I do not know what a CC is and I did not see one', '3 - Difficult to see', '4 - N/A', '4', '1', '2', '3', '1', 'NA', '1', '5', 'NA', '2.4', 'Bad', 'Staff not helpful.', '2025-03-17 05:42:00'),
(59, '2025-01-02', 24, 'Female', 'Region V', '09873473152', 6, 'Grades Inquiry', 'Faculty/Staff', '2 - I know what a CC is but I did NOT see this office\'s CC', '1 - Easy to see', '3 - Did not help', '5', '5', '2', 'NA', '1', 'NA', '1', '3', '3', '2.9', 'Bad', 'Unclear instructions.', '2025-01-02 00:20:00'),
(60, '2025-02-04', 29, 'Female', 'Region IV-A', '09643487859', 6, 'Subject Advising', 'Students', '3 - I learned of the CC only when I saw this office\'s CC', '4 - Not visible at all', '2 - Somewhat helped', 'NA', '4', '2', '1', '2', 'NA', '1', '4', '1', '2.1', 'Good', 'Smooth process.', '2025-02-04 02:02:00'),
(61, '2025-01-28', 28, 'Male', 'Region III', '09561931561', 7, 'Enrollment', 'Faculty/Staff', '3 - I learned of the CC only when I saw this office\'s CC', '4 - Not visible at all', '2 - Somewhat helped', '4', '1', '4', '4', '4', '1', '2', '2', '1', '2.6', 'Good', 'Smooth process.', '2025-01-28 06:38:00'),
(62, '2025-02-22', 26, 'Female', 'Region III', '09453863504', 7, 'Enrollment', 'Faculty/Staff', '2 - I know what a CC is but I did NOT see this office\'s CC', '4 - Not visible at all', '4 - N/A', '1', '3', 'NA', '4', '4', 'NA', '4', '5', '4', '3.6', 'Good', 'Efficient and polite.', '2025-02-22 01:14:00'),
(63, '2025-02-03', 23, 'Male', 'Region I', '09876214229', 7, 'Consultation', 'Faculty/Staff', '2 - I know what a CC is but I did NOT see this office\'s CC', '1 - Easy to see', '3 - Did not help', '4', 'NA', '1', 'NA', '1', '4', '4', '1', '3', '2.6', 'Good', 'Very helpful.', '2025-02-03 01:55:00'),
(64, '2025-01-31', 19, 'Female', 'Region V', '09832455406', 7, 'Subject Advising', 'Visitor', '4 - I do not know what a CC is and I did not see one', '1 - Easy to see', '4 - N/A', 'NA', '5', '1', 'NA', '2', 'NA', 'NA', '4', '5', '3.4', 'Good', 'Smooth process.', '2025-01-31 03:17:00'),
(65, '2025-01-28', 19, 'Female', 'Region I', '09945400162', 7, 'Enrollment', 'Visitor', '1 - I know what a CC is and I saw this office\'s CC', '2 - Somewhat easy to see', '4 - N/A', 'NA', '5', '4', '3', '3', '2', 'NA', '2', '2', '3.0', 'Bad', 'Crowded and slow.', '2025-01-28 00:43:00'),
(66, '2025-03-21', 19, 'Female', 'Region V', '09633968432', 7, 'Grades Inquiry', 'Visitor', '2 - I know what a CC is but I did NOT see this office\'s CC', '3 - Difficult to see', '1 - Helped very much', '3', '4', '3', '2', '3', '1', 'NA', '3', '2', '2.6', 'Good', 'Friendly staff.', '2025-03-21 00:11:00'),
(67, '2025-01-02', 23, 'Male', 'Region V', '09184540647', 7, 'Enrollment', 'Visitor', '2 - I know what a CC is but I did NOT see this office\'s CC', '4 - Not visible at all', '4 - N/A', '4', '5', '5', 'NA', '4', '1', '2', 'NA', '4', '3.6', 'Bad', 'Unclear instructions.', '2025-01-02 08:20:00'),
(68, '2025-02-18', 27, 'Male', 'Region II', '09438389873', 7, 'Subject Advising', 'Visitor', '1 - I know what a CC is and I saw this office\'s CC', '3 - Difficult to see', '1 - Helped very much', '3', '1', 'NA', '2', '3', '1', '5', '4', '3', '2.8', 'Bad', 'Unclear instructions.', '2025-02-18 00:41:00'),
(69, '2025-01-20', 19, 'Male', 'Region III', '09926708342', 7, 'Enrollment', 'Faculty/Staff', '2 - I know what a CC is but I did NOT see this office\'s CC', '5 - N/A', '2 - Somewhat helped', '5', '2', '1', '3', '3', '1', '1', '5', '4', '2.8', 'Good', 'Efficient and polite.', '2025-01-20 07:01:00'),
(70, '2025-03-23', 23, 'Female', 'Region IV-A', '09435573587', 7, 'Enrollment', 'Visitor', '2 - I know what a CC is but I did NOT see this office\'s CC', '2 - Somewhat easy to see', '2 - Somewhat helped', '2', '5', 'NA', '5', '1', '1', '2', 'NA', '5', '3.0', 'Good', 'Efficient and polite.', '2025-03-23 09:52:00'),
(71, '2025-02-27', 20, 'Female', 'Region V', '09781512828', 8, 'Enrollment', 'Visitor', '2 - I know what a CC is but I did NOT see this office\'s CC', '1 - Easy to see', '3 - Did not help', '4', '3', '5', '5', 'NA', '4', '2', '3', '1', '3.4', 'Good', 'Efficient and polite.', '2025-02-27 07:39:00'),
(72, '2025-03-02', 23, 'Male', 'Region III', '09236828867', 8, 'Form Request', 'Visitor', '3 - I learned of the CC only when I saw this office\'s CC', '3 - Difficult to see', '4 - N/A', '3', '1', '3', '1', '5', '4', '5', '3', '4', '3.2', 'Good', 'Efficient and polite.', '2025-03-02 08:37:00'),
(73, '2025-02-22', 29, 'Female', 'Region IV-A', '09134472603', 8, 'Form Request', 'Students', '4 - I do not know what a CC is and I did not see one', '3 - Difficult to see', '4 - N/A', '5', '5', '1', '4', 'NA', '1', '3', '3', 'NA', '3.1', 'Bad', 'Needs improvement.', '2025-02-22 00:26:00'),
(74, '2025-03-08', 29, 'Male', 'Region II', '09691051016', 8, 'Consultation', 'Faculty/Staff', '3 - I learned of the CC only when I saw this office\'s CC', '1 - Easy to see', '3 - Did not help', '2', '4', 'NA', '3', '1', '3', '2', '4', 'NA', '2.7', 'Good', 'Great service!', '2025-03-08 03:04:00'),
(75, '2025-02-08', 22, 'Male', 'Region V', '09868262433', 8, 'Grades Inquiry', 'Faculty/Staff', '1 - I know what a CC is and I saw this office\'s CC', '5 - N/A', '1 - Helped very much', '4', '4', 'NA', 'NA', '3', '2', '5', 'NA', '4', '3.7', 'Bad', 'Unclear instructions.', '2025-02-08 00:30:00'),
(76, '2025-03-30', 21, 'Female', 'Region V', '09954254054', 8, 'Grades Inquiry', 'Students', '4 - I do not know what a CC is and I did not see one', '5 - N/A', '2 - Somewhat helped', '4', '1', '4', '1', '4', '5', '2', '5', '3', '3.2', 'Bad', 'Unclear instructions.', '2025-03-30 06:24:00'),
(77, '2025-01-15', 24, 'Male', 'Region III', '09386143180', 8, 'Consultation', 'Students', '3 - I learned of the CC only when I saw this office\'s CC', '3 - Difficult to see', '1 - Helped very much', '1', '5', '4', '1', '3', '4', '2', '4', 'NA', '3.0', 'Good', 'Very helpful.', '2025-01-15 07:43:00'),
(78, '2025-03-06', 28, 'Female', 'Region I', '09888278643', 8, 'Grades Inquiry', 'Faculty/Staff', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '2 - Somewhat helped', '3', '2', 'NA', '5', 'NA', '5', '2', '2', '3', '3.1', 'Bad', 'Long waiting time.', '2025-03-06 01:26:00'),
(79, '2025-02-15', 22, 'Male', 'Region III', '09611319697', 8, 'Grades Inquiry', 'Visitor', '4 - I do not know what a CC is and I did not see one', '3 - Difficult to see', '4 - N/A', '4', '3', '4', 'NA', '4', '5', '4', '1', '3', '3.5', 'Bad', 'Staff not helpful.', '2025-02-15 03:29:00'),
(80, '2025-02-17', 22, 'Female', 'Region I', '09487994941', 8, 'Consultation', 'Faculty/Staff', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '1 - Helped very much', '2', '4', 'NA', '4', '1', '3', 'NA', '5', '3', '3.1', 'Bad', 'Long waiting time.', '2025-02-17 05:43:00'),
(90, '2025-04-17', 11, 'Female', '3', 'rerer', 7, 'rer', 'Students', '2 - I know what a CC is but I did NOT see this office\'s CC', '2 - Somewhat easy to see', '2 - Somewhat helped', '5', '5', '5', '5', '5', '5', '5', '5', '5', NULL, 'Good', '', '2025-04-17 00:49:51'),
(95, '2025-04-17', 11, 'Female', '3', 'rerer', 8, 'rer', 'Students', '4 - I do not know what a CC is and I did not see one', '2 - Somewhat easy to see', '2 - Somewhat helped', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1.0', 'Good', '', '2025-04-17 01:17:22'),
(96, '2025-04-17', 11, 'Female', '3', 'rerer', 1, 'rer', 'Faculty/Staff', '2 - I know what a CC is but I did NOT see this office\'s CC', '3 - Difficult to see', '2 - Somewhat helped', 'NA', 'NA', '5', '5', 'NA', '5', 'NA', 'NA', 'NA', '5.0', '', '', '2025-04-17 01:18:02'),
(97, '2025-04-17', 11, 'Female', '3', 'rerer', 1, 'rer', 'Students', '2 - I know what a CC is but I did NOT see this office\'s CC', '3 - Difficult to see', '2 - Somewhat helped', '4', '4', '4', '4', '4', '4', '4', '4', '4', '4.0', 'Bad', '', '2025-04-17 01:18:41'),
(98, '2025-04-17', 11, 'Female', '3', 'rerer', 8, 'rer', 'Students', '3 - I learned of the CC only when I saw this office\'s CC', '3 - Difficult to see', '3 - Did not help', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1.0', 'Good', '', '2025-04-17 01:31:35'),
(99, '2025-04-17', 11, 'Female', '3', NULL, 4, 'rer', 'Students', '2 - I know what a CC is but I did NOT see this office\'s CC', '3 - Difficult to see', '2 - Somewhat helped', '1', '2', '3', '4', '5', 'NA', 'NA', 'NA', 'NA', '3.0', 'Good', '', '2025-04-17 01:55:39'),
(100, '2025-04-26', 11, 'Male', '3', NULL, 2, 'rer', 'Students', '2 - I know what a CC is but I did NOT see this office\'s CC', '4 - Not visible at all', '2 - Somewhat helped', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1.0', 'Bad', '', '2025-04-17 01:57:00'),
(101, '2025-04-17', 11, 'Male', '3', NULL, 5, 'rer', 'Visitor', '1 - I know what a CC is and I saw this office\'s CC', '2 - Somewhat easy to see', '2 - Somewhat helped', 'NA', 'NA', 'NA', 'NA', 'NA', 'NA', 'NA', 'NA', 'NA', NULL, 'Good', '', '2025-04-17 02:44:30'),
(102, '2025-04-17', 11, 'Female', '3', NULL, 5, 'rer', 'Students', '2 - I know what a CC is but I did NOT see this office\'s CC', '2 - Somewhat easy to see', '3 - Did not help', '1', '2', '3', '4', '5', 'NA', '5', '4', '3', '3.4', 'Good', '', '2025-04-17 02:57:02'),
(103, '2025-04-17', 11, 'Female', '3', NULL, 7, 'rer', 'Visitor', '2 - I know what a CC is but I did NOT see this office\'s CC', '3 - Difficult to see', '1 - Helped very much', '1', '2', '3', '4', '5', 'NA', '5', '4', '3', '3.4', 'Good', '', '2025-04-17 03:01:20'),
(104, '2025-04-17', 11, 'Female', '1', NULL, 2, 'qwerqewrwqe', 'Students', '3 - I learned of the CC only when I saw this office\'s CC', '3 - Difficult to see', '2 - Somewhat helped', 'NA', 'NA', 'NA', 'NA', 'NA', 'NA', 'NA', 'NA', 'NA', NULL, 'Good', '', '2025-04-17 03:03:07'),
(105, '2025-04-17', 11, 'Female', '1', NULL, 2, 'qwerqewrwqe', 'Students', '2 - I know what a CC is but I did NOT see this office\'s CC', '2 - Somewhat easy to see', '2 - Somewhat helped', '4', '4', '4', '4', '4', '4', '4', '4', '4', '4.0', 'Good', '', '2025-04-17 03:03:58'),
(106, '2025-04-17', 22, 'Male', '3', '09318996081', 1, 'test', 'Students', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '1 - Helped very much', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1.0', 'Bad', 'panget', '2025-04-17 09:18:32'),
(107, '2025-04-17', 22, 'Male', '2', '09318996081', 1, 'test', 'Students', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '1 - Helped very much', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1.0', 'Bad', 'panget', '2025-04-17 09:32:31'),
(108, '2025-04-17', 22, 'Male', '3', '09318996081', 1, 'test', 'Students', '1 - I know what a CC is and I saw this office\'s CC', '2 - Somewhat easy to see', '3 - Did not help', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1.0', 'Bad', 'panget', '2025-04-17 09:46:17'),
(109, '2025-04-17', 22, 'Male', '3', '09318996081', 8, 'test', 'Visitor', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '1 - Helped very much', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1.0', 'Bad', 'panget', '2025-04-17 09:53:59'),
(110, '2025-04-17', 22, 'Male', '2', '09318996081', 1, 'test', 'Students', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '1 - Helped very much', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1.0', 'Bad', 'panget', '2025-04-17 09:58:19'),
(111, '2025-04-17', 22, 'Male', '2`', '09318996081', 8, 'test', 'Students', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '1 - Helped very much', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1.0', 'Bad', 'badtrip', '2025-04-17 09:59:56'),
(112, '2025-04-17', 22, 'Male', '3', '09318996081', 1, 'test', 'Students', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '1 - Helped very much', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1.0', 'Bad', 'panget', '2025-04-17 10:07:03'),
(113, '2025-04-17', 22, 'Male', '3', '09318996081', 1, 'test', 'Students', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '1 - Helped very much', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1.0', 'Bad', 'panget niyo', '2025-04-17 10:17:16'),
(114, '2025-04-17', 22, 'Male', '3', '09318996081', 1, 'test', 'Students', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '1 - Helped very much', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1.0', 'Bad', 'panget ka ', '2025-04-17 10:20:30'),
(115, '2025-04-17', 33, 'Female', '3', '09318996081', 4, 'test', 'Visitor', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '1 - Helped very much', '5', '5', '5', '5', '5', '5', '5', '5', '5', '5.0', 'Good', 'gwapo', '2025-04-17 10:21:24'),
(116, '2025-04-17', 22, 'Male', '3', '09318996081', 4, 'test', 'Faculty/Staff', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '1 - Helped very much', '3', '3', '3', '3', '3', '3', '3', '3', '3', '3.0', 'Good', 'medyo', '2025-04-17 10:23:15'),
(117, '2025-04-17', 22, 'Male', '3', '09318996081', 3, 'test', 'Faculty/Staff', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '1 - Helped very much', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1.0', 'Bad', 'bad ka', '2025-04-17 10:31:03'),
(118, '2025-04-17', 23, 'Male', '3', '09318996081', 1, 'test', 'Faculty/Staff', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '1 - Helped very much', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1.0', 'Bad', 'bad ka', '2025-04-17 11:19:14'),
(119, '2025-04-17', 22, 'Male', '3', '09318996081', 1, 'test', 'Students', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '1 - Helped very much', '3', '3', '3', '3', '3', '3', '3', '3', '3', '3.0', 'Good', 'good', '2025-04-17 11:43:07'),
(120, '2025-04-17', 3, 'Male', '22', '09318996081', 1, 'test', 'Students', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '1 - Helped very much', '4', '4', '4', '4', '4', '4', '4', '4', '4', '4.0', 'Good', 'medyo', '2025-04-17 12:05:15'),
(121, '2025-04-17', 33, 'Male', '4', '09318996081', 1, 'test', 'Students', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '1 - Helped very much', '5', '5', '5', '5', '5', '5', '5', '5', '5', '5.0', 'Good', 'good', '2025-04-17 12:07:28'),
(122, '2025-04-17', 22, 'Male', '3', '09318996081', 1, 'test', 'Visitor', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '1 - Helped very much', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1.0', 'Bad', 'badding', '2025-04-17 12:19:27'),
(123, '2025-04-20', 12, 'Male', '3', NULL, 7, 'qwer', 'Students', '2 - I know what a CC is but I did NOT see this office\'s CC', '3 - Difficult to see', '2 - Somewhat helped', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2.0', 'Good', '', '2025-04-20 10:26:12'),
(124, '2025-04-20', 0, 'Female', '3', NULL, 3, 'qwer', 'Faculty/Staff', '2 - I know what a CC is but I did NOT see this office\'s CC', '2 - Somewhat easy to see', '3 - Did not help', '4', '4', '4', '4', '4', '4', '4', '4', '4', '4.0', 'Good', '', '2025-04-20 10:27:45'),
(125, '2025-04-20', 21, 'Female', '3', NULL, 7, 'qwer', 'Students', '2 - I know what a CC is but I did NOT see this office\'s CC', '3 - Difficult to see', '2 - Somewhat helped', '1', '2', '3', '4', '5', 'NA', '5', '4', '3', '3.4', 'Good', '', '2025-04-20 10:39:15'),
(126, '2025-04-20', 21, 'Male', '3', NULL, 2, 'qwer', 'Students', '2 - I know what a CC is but I did NOT see this office\'s CC', '2 - Somewhat easy to see', '2 - Somewhat helped', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1.0', 'Good', '', '2025-04-20 10:41:10'),
(127, '2025-04-20', 21, 'Male', '3', NULL, 3, 'qwer', 'Students', '3 - I learned of the CC only when I saw this office\'s CC', '3 - Difficult to see', '1 - Helped very much', '1', '2', '3', '4', '5', 'NA', 'NA', '5', '4', '3.4', 'Good', '', '2025-04-20 11:23:57'),
(128, '2025-04-20', 11, 'Female', '3', NULL, 3, 'qwer', 'Students', '3 - I learned of the CC only when I saw this office\'s CC', '2 - Somewhat easy to see', '2 - Somewhat helped', '5', '5', '4', '5', '4', '4', '5', '5', '4', '4.6', 'Good', '', '2025-04-20 11:25:42'),
(129, '2025-04-20', 0, 'Female', '3', NULL, 3, 'qwer', 'Students', '1 - I know what a CC is and I saw this office\'s CC', '1 - Easy to see', '2 - Somewhat helped', '1', '2', '3', '4', '5', '4', '3', '2', '1', '2.8', 'Good', '', '2025-04-20 11:35:43'),
(130, '2025-04-20', 21, 'Female', '3', NULL, 2, 'qwer', 'Faculty/Staff', '2 - I know what a CC is but I did NOT see this office\'s CC', '2 - Somewhat easy to see', '3 - Did not help', '1', '2', '3', '4', '5', 'NA', '5', '4', '3', '3.4', 'Good', '', '2025-04-20 11:55:18'),
(131, '2025-04-20', 21, 'Female', '3', NULL, 3, 'ere', 'Students', '1 - I know what a CC is and I saw this office\'s CC', '3 - Difficult to see', '2 - Somewhat helped', '3', '3', '4', '4', '5', '5', '5', '5', '5', '4.3', 'Good', '', '2025-04-20 13:02:02'),
(132, '2025-04-20', 1, 'Female', '3', NULL, 8, 're', 'Faculty/Staff', '2 - I know what a CC is but I did NOT see this office\'s CC', '3 - Difficult to see', '2 - Somewhat helped', '4', '4', '5', '5', '5', '5', '5', '5', '5', '4.8', 'Good', '', '2025-04-20 13:02:19'),
(133, '2025-04-20', 12, 'Female', '3', NULL, 1, 'rerer', 'Faculty/Staff', '2 - I know what a CC is but I did NOT see this office\'s CC', '4 - Not visible at all', '3 - Did not help', '1', '2', '3', '4', '5', '5', '4', '3', '2', '3.2', 'Good', '', '2025-04-20 13:07:40');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `feedback_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `feedback_id`, `message`, `is_read`, `created_at`) VALUES
(12, 125, 'New feedback received for QA', 1, '2025-04-20 10:39:15'),
(13, 126, 'New feedback received for CAS', 1, '2025-04-20 10:41:10'),
(14, 127, 'New feedback received for CAF', 1, '2025-04-20 11:23:57'),
(15, 128, 'New feedback received for CAF', 1, '2025-04-20 11:25:42'),
(16, 129, 'New feedback received for CAF', 1, '2025-04-20 11:35:43'),
(17, 130, 'New feedback received for CAS', 1, '2025-04-20 11:55:18'),
(18, 131, 'New feedback received for CAF', 1, '2025-04-20 13:02:02'),
(19, 132, 'New feedback received for OP', 1, '2025-04-20 13:02:19'),
(20, 133, 'New feedback received for CET', 1, '2025-04-20 13:07:40');

-- --------------------------------------------------------

--
-- Table structure for table `offices`
--

CREATE TABLE `offices` (
  `office_id` int(11) NOT NULL,
  `office_name` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `offices`
--

INSERT INTO `offices` (`office_id`, `office_name`, `is_active`) VALUES
(1, 'CET', 1),
(2, 'CAS', 1),
(3, 'CAF', 1),
(4, 'CVM', 1),
(5, 'CBM', 1),
(6, 'CED', 1),
(7, 'QA', 1),
(8, 'OP', 1);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'user'),
(2, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `fname` varchar(100) DEFAULT NULL,
  `mname` varchar(100) DEFAULT NULL,
  `lname` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `office_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fname`, `mname`, `lname`, `username`, `password`, `role_id`, `office_id`, `is_active`) VALUES
(4, 'cvm', 'cvm', 'cvm', 'cvm', '$2y$10$tiOMBl8cBx.PgueQKEAUt..eDa05FcEH8M2iznkhmo/RtZB/54bA2', 1, 4, 1),
(7, 'caf', 'caf', 'caf', 'caf', '$2y$10$Q1aRetA.spnij58szurit.7/SaUswThRuDu1Pn381053ah2DRSui2', 1, 3, 1),
(9, 'cas', 'cas', 'cas', 'cas', '$2y$10$iJNMGzqdncDJlhS5kxtt0ub.hhO5rRDqJa4BhGEwi0HgnL.WESuSa', 1, 2, 1),
(10, 'cbm', 'cbm', 'cbm', 'cbm', '$2y$10$lGbqfuRLVFpjQKNyWfDlteytK/k8dmlRoYt4LQPVUCC7pXw2MrBF2', 1, 5, 1),
(13, 'qa', 'qa', 'qa', 'qa', '$2y$10$PvmigW5OFs8LOddzTiJSV.DnRNETLGLGBFSCWiAjid9d.UkgAsWq6', 2, 7, 1),
(14, 'ced', 'ced', 'ced', 'ced', '$2y$10$r2/JzJO4lxyeQa7siPh2luwvMcNMJN6gUZMa/6xRB7Dc275VHWgPi', 1, 6, 1),
(16, 'cet', 'cet', 'cet', 'cet', '$2y$10$MoS3MvnZOlGwo2kDigqM2O/4cUXYvFuKTGF/SO60u0eZUYUYSvzUy', 1, 1, 1),
(18, 'op', 'op', 'op', 'op', '$2y$10$Um8o5R1w3Jw8pF6Ld3mCxe3GdX/fiM47L/yDEXvThd/3dqS/C6eKC', 2, 8, 1),
(25, 'kanneth', 'tangonan', 'sanchez', 'net', '$2y$10$LaQWYoCkff0Yyr1Ndp7Q4.9immdoPunZvyPO2c/sEXxCkIeJiSYZS', 1, 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `office_id` (`office_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `feedback_id` (`feedback_id`);

--
-- Indexes for table `offices`
--
ALTER TABLE `offices`
  ADD PRIMARY KEY (`office_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `office_id` (`office_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `offices`
--
ALTER TABLE `offices`
  MODIFY `office_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`office_id`) REFERENCES `offices` (`office_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`office_id`) REFERENCES `offices` (`office_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
