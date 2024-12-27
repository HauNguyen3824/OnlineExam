-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 27, 2024 lúc 09:49 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `onlineexam`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `addques`
--

CREATE TABLE `addques` (
  `AQId` varchar(10) NOT NULL,
  `ExamId` varchar(10) NOT NULL,
  `QuesId` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `addques`
--

INSERT INTO `addques` (`AQId`, `ExamId`, `QuesId`) VALUES
('AQ014', 'EX024', 'QUES3173'),
('AQ043', 'EX540', 'QUES000'),
('AQ052', 'EX540', 'QUES7109'),
('AQ103', 'EX540', 'QUES8139'),
('AQ138', 'EX024', 'QUES7532'),
('AQ152', 'EX578', 'QUES000'),
('AQ173', 'EX578', 'QUES7109'),
('AQ234', 'EX540', 'QUES7532'),
('AQ351', 'EX540', 'QUES3173'),
('AQ393', 'EX540', 'QUES3209'),
('AQ570', 'EX578', 'QUES7532'),
('AQ584', 'EX024', 'QUES3209'),
('AQ678', 'EX024', 'QUES000'),
('AQ722', 'EX024', 'QUES7109'),
('AQ791', 'EX578', 'QUES3209'),
('AQ841', 'EX024', 'QUES8139'),
('AQ924', 'EX578', 'QUES3173');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `adduser`
--

CREATE TABLE `adduser` (
  `AUId` varchar(10) NOT NULL,
  `ExamId` varchar(10) NOT NULL,
  `UserId` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `adduser`
--

INSERT INTO `adduser` (`AUId`, `ExamId`, `UserId`) VALUES
('AU020', 'EX578', '2210123'),
('AU065', 'EX540', '2410123'),
('AU393', 'EX559', '2210123'),
('AU631', 'EX578', '2410123'),
('AU706', 'EX540', '2210123');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chat`
--

CREATE TABLE `chat` (
  `ChatId` varchar(10) NOT NULL,
  `Content` text NOT NULL,
  `Seen` tinyint(1) NOT NULL DEFAULT 0,
  `userid` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chat`
--

INSERT INTO `chat` (`ChatId`, `Content`, `Seen`, `userid`) VALUES
('1', 'admin dep trai oi ciu em voi, em bi loi lam bai, cho em lam lai di', 1, '2210123');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `exams`
--

CREATE TABLE `exams` (
  `ExamId` varchar(10) NOT NULL,
  `ExamTitle` text NOT NULL,
  `Duration` time DEFAULT NULL,
  `NumOfQues` int(11) NOT NULL,
  `Subject` varchar(50) NOT NULL,
  `Difficult` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `exams`
--

INSERT INTO `exams` (`ExamId`, `ExamTitle`, `Duration`, `NumOfQues`, `Subject`, `Difficult`) VALUES
('EX024', 'kt 3p 27/12', '00:01:01', 10, 'Toán', 'Dễ'),
('EX316', 'kt 10p 23/12', '00:00:00', 45, 'Toán', 'Dễ'),
('EX540', 'kt 10p 25/12', '00:02:00', 7, 'Toán', 'Dễ'),
('EX559', 'kt 10p 26/12', '00:03:00', 8, 'Toán', 'Dễ'),
('EX578', 'test 123', '00:00:30', 5, 'Toán', 'Dễ'),
('EX656', 'kt 10p 24/12', '00:00:00', 6, 'Toán', 'Dễ'),
('EX662', 'kt 10p 27/12', '00:01:01', 9, 'Toán', 'Dễ'),
('EX747', 'test 123', '00:00:30', 5, 'Toán', 'Dễ'),
('EX772', 'kt 10p 23/12', '00:03:00', 45, 'Toán', 'Dễ');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `questions`
--

CREATE TABLE `questions` (
  `QuesId` varchar(10) NOT NULL,
  `QuesTitle` text NOT NULL,
  `Difficult` varchar(100) DEFAULT NULL,
  `Answer1` text DEFAULT NULL,
  `Answer2` text DEFAULT NULL,
  `Answer3` text DEFAULT NULL,
  `Answer4` text DEFAULT NULL,
  `Correct` varchar(10) DEFAULT NULL,
  `Class` int(11) DEFAULT NULL,
  `Subject` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `questions`
--

INSERT INTO `questions` (`QuesId`, `QuesTitle`, `Difficult`, `Answer1`, `Answer2`, `Answer3`, `Answer4`, `Correct`, `Class`, `Subject`) VALUES
('QUES000', '1+3=44', 'Dễ', 'A: đúng', 'B: sai', 'C: chắc vậy', '', '2', 10, 'Toán'),
('QUES3173', '1+1=?', 'Dễ', '1', '2', '3', '', '2', 10, 'Toán'),
('QUES3209', '6x6=20', 'Dễ', 'đúng', 'sai', '', '', '2', 10, 'Toán'),
('QUES7109', '1+3=53', 'Dễ', '1', '2', '', '', '1', 10, 'Toán'),
('QUES7532', '5 x 5 = 25', 'Dễ', 'đúng', 'sai', '', '', '1', 10, 'Toán'),
('QUES8139', '1 x 3 =?', 'Dễ', '1', '2', '3', '4', '3', 10, 'Toán');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `results`
--

CREATE TABLE `results` (
  `ResultId` varchar(10) NOT NULL,
  `AUId` varchar(10) NOT NULL,
  `Score` int(11) NOT NULL,
  `TimeStart` datetime NOT NULL,
  `TimeSubmit` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `results`
--

INSERT INTO `results` (`ResultId`, `AUId`, `Score`, `TimeStart`, `TimeSubmit`) VALUES
('2AXlp0eVWd', 'AU020', 0, '2024-12-27 09:22:29', '2024-12-27 09:23:00'),
('pZJumL3Sib', 'AU631', 2, '2024-12-27 09:18:50', '2024-12-27 09:19:26'),
('tDGj9I3LQl', 'AU706', 1, '2024-12-27 09:23:42', '2024-12-27 09:24:03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `useranswers`
--

CREATE TABLE `useranswers` (
  `AnsId` varchar(10) NOT NULL,
  `UserChoice` varchar(10) DEFAULT NULL,
  `IsCorrect` tinyint(1) NOT NULL,
  `QuesId` varchar(10) DEFAULT NULL,
  `auid` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `useranswers`
--

INSERT INTO `useranswers` (`AnsId`, `UserChoice`, `IsCorrect`, `QuesId`, `auid`) VALUES
('8QX4gONCYn', '2', 0, 'QUES7532', 'AU706'),
('Anlj1cGb9s', '3', 1, 'QUES8139', 'AU706'),
('ayguzU4DtZ', '1', 0, 'QUES3173', 'AU631'),
('cTm9EIOHjW', '1', 0, 'QUES3173', 'AU020'),
('dGRpITaoDQ', '2', 0, 'QUES7109', 'AU631'),
('DJlFiQk3og', '1', 0, 'QUES000', 'AU631'),
('Gh5MwHPWtE', '0', 0, 'undefined', 'AU020'),
('hycrJHMtZa', '1', 1, 'QUES7532', 'AU631'),
('ik0ywQpOGL', '1', 0, 'QUES000', 'AU706'),
('JsqldCw7Dr', '1', 0, 'QUES3173', 'AU706'),
('nwEFB0AgYS', '2', 1, 'QUES3209', 'AU631'),
('p8CeARWoj2', '1', 0, 'QUES3209', 'AU706'),
('ypDwlXYcFo', '1', 0, 'QUES000', 'AU020'),
('ZHlo7DmP86', '2', 0, 'QUES7109', 'AU706');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `UserId` varchar(10) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Phone` int(11) DEFAULT NULL,
  `Role` varchar(100) NOT NULL,
  `Class` int(11) DEFAULT NULL,
  `Year` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`UserId`, `FullName`, `Username`, `Password`, `Email`, `Phone`, `Role`, `Class`, `Year`) VALUES
('2210123', 'aaaaaa44', '2210123', '$2y$10$YrO97Kl4CMs.uBmFLPudP.sSqHvfL4v7ly9q6cuh5T4N0jzatfDHC', 'aaa@gmail.com', 707653310, 'user', 10, 2022),
('2410123', 'Tô Thanh Tuấn1', '2410123', '$2y$10$PRYJ5DTy1H2XDyAqJri9feXceEy/uWvlL/EpT7y44zJQ4iUtFLQJa', NULL, NULL, 'user', 10, 2024),
('admin1', 'Tô Thanh Tuấn', 'admin1', '$2y$10$hSRBdanA6cjpQ3P7oY6Jxe4vWyuNr8AnyGVffcn2d15We9x4wDuTm', 'aaa@gmail.com', 707653310, 'admin', NULL, NULL),
('admin2', 'Tô Thanh Tuấn', 'admin2', '$2y$10$7InkvxMAzoB0gnS5.owp9uqrPmQvvvhn4eCXyXJEwWhprVTUQYMGW', 'tuan@gmail.com', 707653311, 'admin', NULL, NULL),
('admin3', 'Tô Thanh Tuấn', 'admin4', '$2y$10$ImJ3f7VaNGXZwYPnm2F9auzEiUL.moqSQWs01V1AiwB3pC1P6ly6i', 'aaa@gmail.com', 707653314, 'admin', NULL, NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `addques`
--
ALTER TABLE `addques`
  ADD PRIMARY KEY (`AQId`),
  ADD KEY `ExamId` (`ExamId`),
  ADD KEY `QuesId` (`QuesId`);

--
-- Chỉ mục cho bảng `adduser`
--
ALTER TABLE `adduser`
  ADD PRIMARY KEY (`AUId`),
  ADD KEY `ExamId` (`ExamId`),
  ADD KEY `UserId` (`UserId`);

--
-- Chỉ mục cho bảng `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`ChatId`);

--
-- Chỉ mục cho bảng `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`ExamId`);

--
-- Chỉ mục cho bảng `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`QuesId`);

--
-- Chỉ mục cho bảng `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`ResultId`),
  ADD KEY `AUId` (`AUId`);

--
-- Chỉ mục cho bảng `useranswers`
--
ALTER TABLE `useranswers`
  ADD PRIMARY KEY (`AnsId`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserId`);

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `addques`
--
ALTER TABLE `addques`
  ADD CONSTRAINT `addques_ibfk_1` FOREIGN KEY (`ExamId`) REFERENCES `exams` (`ExamId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `addques_ibfk_2` FOREIGN KEY (`QuesId`) REFERENCES `questions` (`QuesId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `adduser`
--
ALTER TABLE `adduser`
  ADD CONSTRAINT `adduser_ibfk_1` FOREIGN KEY (`ExamId`) REFERENCES `exams` (`ExamId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `adduser_ibfk_2` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`AUId`) REFERENCES `adduser` (`AUId`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
