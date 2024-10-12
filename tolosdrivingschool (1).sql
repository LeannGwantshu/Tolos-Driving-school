-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2024 at 05:54 PM
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
-- Database: `tolosdrivingschool`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `booking_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `added_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `student_id`, `course_id`, `quantity`, `total_amount`, `added_date`) VALUES
(69, 4, 2, 1, 4500.00, '2024-10-12 17:44:50');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `C_code` varchar(10) DEFAULT NULL,
  `imageurl` varchar(250) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `name`, `description`, `duration`, `price`, `C_code`, `imageurl`, `username`, `password`) VALUES
(1, 'Motorcycles', 'Learn to drive \nMotorcycles', 25, 3500.00, 'Code-A', 'https://i.ibb.co/QMhy3mX/CodeA-FF.png', '', ''),
(2, 'Light Motor Vehicles', 'Learn to drive light motor vehicles.', 25, 4500.00, 'Code-B', 'https://i.ibb.co/wCbVZt3/code-B-full.png', '', ''),
(3, 'Heavy Motor Vehicles', 'Learn to drive Heavy motor vehicles', 25, 5500.00, 'Code-C', 'https://i.ibb.co/swQtgWT/code-C-full.png', '', ''),
(4, 'Motorcycle Course', 'Learn to drive Motorcycles', 1, 200.00, 'Code-A', 'https://i.ibb.co/P4Y3X26/CodeA-1.png', '', ''),
(5, 'Light Motor Vehicle Course', 'Learn to drive light motor vehicles ', 1, 250.00, 'Code-B', 'https://i.ibb.co/fFK023v/codeB11.png', '', ''),
(6, 'Heavy Motor Vehicle Course', 'Learn to drive heavy motor vehicles', 1, 350.00, 'Code-C', 'https://i.ibb.co/xX9Hd4Q/code-C-11.png', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `course_registrations`
--

CREATE TABLE `course_registrations` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `registration_date` datetime NOT NULL,
  `status` enum('active','completed','dropped') DEFAULT 'active',
  `start_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_registrations`
--

INSERT INTO `course_registrations` (`id`, `student_id`, `course_id`, `registration_date`, `status`, `start_date`) VALUES
(7, 4, 5, '2024-10-12 16:25:19', '', '2024-10-25'),
(8, 6, 2, '2024-10-12 16:39:34', '', '2024-10-23'),
(9, 6, 5, '2024-10-12 16:42:28', '', '2024-10-23');

-- --------------------------------------------------------

--
-- Table structure for table `course_schedule`
--

CREATE TABLE `course_schedule` (
  `id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `lesson_date` datetime DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `status` enum('Scheduled','Completed','Cancelled') DEFAULT 'Scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_schedule`
--

INSERT INTO `course_schedule` (`id`, `course_id`, `student_id`, `instructor_id`, `lesson_date`, `duration`, `status`) VALUES
(3, 5, 4, 2, '2024-10-26 13:00:00', 60, 'Scheduled'),
(4, 2, 6, 3, '2024-10-23 12:00:00', 60, 'Scheduled'),
(5, 5, 6, 2, '2024-10-24 13:00:00', 60, 'Scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `instructors`
--

CREATE TABLE `instructors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `license_number` varchar(50) NOT NULL,
  `experience_years` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instructors`
--

INSERT INTO `instructors` (`id`, `name`, `surname`, `email`, `phone`, `license_number`, `experience_years`, `course_id`, `username`, `password`) VALUES
(1, 'John', 'Doe', 'johndoe@example.com', '555-123-4567', 'LN12345', 10, 1, 'John', 'John123'),
(2, 'Sarah', 'Smith', 'sarahsmith@example.com', '555-234-5678', 'LN23456', 8, 5, 'Sarah', '$2y$10$K5zgROfffsGEMgse7BwflOPKFIdcIVsfqWiO2a03xdrFmyIrUvvXa'),
(3, 'Michael', 'Johnson', 'michaeljohnson@example.com', '555-345-6789', 'LN34567', 5, 2, 'Michael', 'Michael123'),
(4, 'Emily', 'Davis', 'emilydavis@example.com', '555-456-7890', 'LN45678', 7, 3, 'Emily', 'Emily123'),
(5, 'Robert', 'Brown', 'robertbrown@example.com', '555-567-8901', 'LN56789', 12, 4, 'Robert', 'Robert123'),
(11, 'Chris', 'Brown', 'chris.brown@example.com', '1234567894', 'LIC12349', 6, 5, 'chris_brown', 'password123'),
(12, 'Sarah', 'Wilson', 'sarah.wilson@example.com', '1234567895', 'LIC12350', 4, 6, 'sarah_wilson', 'password123'),
(13, 'David', 'Lee', 'david.lee@example.com', '1234567896', 'LIC12351', 8, 1, 'david_lee', 'password123'),
(14, 'Laura', 'Moore', 'laura.moore@example.com', '1234567897', 'LIC12352', 2, 2, 'laura_moore', 'password123'),
(15, 'Robert', 'Taylor', 'robert.taylor@example.com', '1234567898', 'LIC12353', 9, 3, 'robert_taylor', 'password123'),
(16, 'Anna', 'Anderson', 'anna.anderson@example.com', '1234567899', 'LIC12354', 5, 4, 'anna_anderson', 'password123'),
(17, 'James', 'Thomas', 'james.thomas@example.com', '1234567800', 'LIC12355', 7, 5, 'james_thomas', 'password123'),
(18, 'Sophia', 'Jackson', 'sophia.jackson@example.com', '1234567801', 'LIC12356', 3, 6, 'sophia_jackson', 'password123'),
(19, 'Daniel', 'White', 'daniel.white@example.com', '1234567802', 'LIC12357', 6, 1, 'daniel_white', 'password123'),
(20, 'Emma', 'Harris', 'emma.harris@example.com', '1234567803', 'LIC12358', 8, 2, 'emma_harris', 'password123'),
(21, 'Matthew', 'Martin', 'matthew.martin@example.com', '1234567804', 'LIC12359', 4, 3, 'matthew_martin', 'password123'),
(22, 'Olivia', 'Garcia', 'olivia.garcia@example.com', '1234567805', 'LIC12360', 10, 4, 'olivia_garcia', 'password123'),
(23, 'Joshua', 'Martinez', 'joshua.martinez@example.com', '1234567806', 'LIC12361', 9, 5, 'joshua_martinez', 'password123'),
(24, 'Grace', 'Robinson', 'grace.robinson@example.com', '1234567807', 'LIC12362', 2, 6, 'grace_robinson', 'password123'),
(25, 'Andrew', 'Clark', 'andrew.clark@example.com', '1234567808', 'LIC12363', 5, 1, 'andrew_clark', 'password123'),
(26, 'Mia', 'Rodriguez', 'mia.rodriguez@example.com', '1234567809', 'LIC12364', 7, 2, 'mia_rodriguez', 'password123');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime NOT NULL,
  `method` enum('cash','credit_card','debit_card','paypal') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `student_id`, `amount`, `payment_date`, `method`) VALUES
(1, 4, 4500.00, '2024-10-11 12:01:34', 'cash'),
(2, 4, 5500.00, '2024-10-11 14:21:46', ''),
(3, 4, 4500.00, '2024-10-11 15:43:42', 'debit_card'),
(4, 4, 3500.00, '2024-10-11 15:44:38', 'credit_card'),
(5, 4, 4500.00, '2024-10-12 13:01:11', 'cash'),
(6, 4, 250.00, '2024-10-12 16:25:19', 'cash'),
(7, 6, 4500.00, '2024-10-12 16:39:34', 'cash'),
(8, 6, 250.00, '2024-10-12 16:42:28', 'debit_card');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `enrolled_date` date DEFAULT curdate(),
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `surname`, `phone`, `email`, `date_of_birth`, `enrolled_date`, `username`, `password`) VALUES
(4, 'Leann', 'Gwantshu', '0817748530', 'Leann.gwantshu@gmail.com', '2001-03-23', '2024-10-10', 'Leann', '$2y$10$daenBZ4L.N89YB/2pPPkq.vbE/JpZzfQlcMALPnsBKCBOotRrQ8ju'),
(5, 'Yathitha', 'Pasi', '0871111111', 'nzukisogwantshu9@gmail.com', '1990-03-06', '2024-10-10', 'Thitha@2021', '$2y$10$F4D7JhjUFs92I8waRnGyLO8iP3bChfz9lDDxLkh0TyrAjc4l1G1n.'),
(6, 'nolubabalo', 'tyokolo', '06237555244', 'nolutyokolo062@gmail.com', '1997-11-28', '2024-10-12', 'litha', '$2y$10$p/S1KdfYlIyOrmF1pX8Ymedk8MQmlkPFIlcqMoygtCgZUyMTWkkUW');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `make` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `V_year` int(11) NOT NULL,
  `license_plate` varchar(20) NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `course_code` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `make`, `model`, `V_year`, `license_plate`, `is_available`, `course_code`) VALUES
(1, 'Toyota', 'Corolla', 2018, 'XYZ123GP', 1, 'Code-B'),
(2, 'Volkswagen', 'Golf', 2020, 'ABC456GP', 1, 'Code-B'),
(3, 'Ford', 'Fiesta', 2019, 'LMN789GP', 0, 'Code-B'),
(4, 'Honda', 'Civic', 2021, 'QRS987GP', 1, 'Code-B'),
(5, 'Nissan', 'Altima', 2017, 'TUV654GP', 0, 'Code-B'),
(6, 'Yamaha', 'YZF-R3', 2022, 'MC123YZ', 1, 'Code-A'),
(7, 'Kawasaki', 'Ninja 400', 2021, 'MC456KW', 1, 'Code-A'),
(8, 'Ducati', 'Panigale V2', 2020, 'MC789DU', 1, 'Code-A'),
(9, 'Mercedes-Benz', 'Actros', 2019, 'TR123MB', 0, 'Code-C'),
(10, 'Volvo', 'FH16', 2021, 'TR456VO', 1, 'Code-C'),
(11, 'MAN', 'TGX', 2020, 'TR789MA', 1, 'Code-C');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `instructor_id` (`instructor_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_registrations`
--
ALTER TABLE `course_registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `course_schedule`
--
ALTER TABLE `course_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `instructor_id` (`instructor_id`);

--
-- Indexes for table `instructors`
--
ALTER TABLE `instructors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `license_number` (`license_number`),
  ADD KEY `fk_course` (`course_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `license_plate` (`license_plate`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `course_registrations`
--
ALTER TABLE `course_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `course_schedule`
--
ALTER TABLE `course_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `instructors`
--
ALTER TABLE `instructors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `course_registrations`
--
ALTER TABLE `course_registrations`
  ADD CONSTRAINT `course_registrations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `course_registrations_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `course_schedule`
--
ALTER TABLE `course_schedule`
  ADD CONSTRAINT `course_schedule_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `course_schedule_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `course_schedule_ibfk_3` FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`id`);

--
-- Constraints for table `instructors`
--
ALTER TABLE `instructors`
  ADD CONSTRAINT `fk_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
