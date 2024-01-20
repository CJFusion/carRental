START TRANSACTION;
--
-- Table structure for table `AgencyDetails`
--

CREATE TABLE `AgencyDetails` (
  `agencyId` int(11) NOT NULL,
  `agencyName` varchar(100) NOT NULL,
  `fullName` varchar(100) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `addressState` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `AgencyDetails`
--

INSERT INTO `AgencyDetails` (`agencyId`, `agencyName`, `fullName`, `phone`, `addressState`) VALUES
(2, 'Mark Braum Ltd.', 'Mark Braum', '9612926663', 'Goa'),
(11, 'Jason Miles Ltd.', 'Jason Miles', '9366875399', 'Kolkata'),
(13, 'Percival Graves Ltd.', 'Percival Graves', '9344254977', 'Bihar'),
(21, 'Bingchillinh', 'Sugon deez', '8131887051', 'Assam');

-- --------------------------------------------------------

--
-- Table structure for table `Bookings`
--

CREATE TABLE `Bookings` (
  `bookingId` int(11) NOT NULL,
  `carId` int(11) NOT NULL,
  `customerId` int(11) NOT NULL,
  `agencyId` int(11) NOT NULL,
  `bookDate` date NOT NULL,
  `endDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Bookings`
--

INSERT INTO `Bookings` (`bookingId`, `carId`, `customerId`, `agencyId`, `bookDate`, `endDate`) VALUES
(1, 3, 1, 2, '2023-01-01', '2023-01-05'),
(2, 4, 1, 2, '2023-02-01', '2023-02-10'),
(3, 5, 1, 2, '2023-03-01', '2023-03-15'),
(4, 6, 1, 2, '2023-04-01', '2023-04-20'),
(5, 7, 1, 2, '2023-05-01', '2023-05-25'),
(19, 3, 1, 2, '2024-01-08', '2024-01-09'),
(20, 3, 27, 2, '2024-01-10', '2024-01-11'),
(21, 3, 28, 2, '3218-01-09', '3218-02-09'),
(22, 5, 1, 2, '2024-01-17', '2024-01-18'),
(23, 4, 28, 2, '2024-01-24', '2024-01-29'),
(24, 17, 1, 13, '2024-01-21', '2024-01-23');

-- --------------------------------------------------------

--
-- Table structure for table `Cars`
--

CREATE TABLE `Cars` (
  `carId` int(11) NOT NULL,
  `agencyId` int(11) NOT NULL,
  `model` varchar(100) NOT NULL,
  `licenseNumber` varchar(20) NOT NULL,
  `capacity` int(11) NOT NULL,
  `rentPerDay` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Cars`
--

INSERT INTO `Cars` (`carId`, `agencyId`, `model`, `licenseNumber`, `capacity`, `rentPerDay`) VALUES
(3, 2, 'Tesla Model S', 'MH 01 AB 1234', 5, 600.00),
(4, 2, 'Porsche Cayenne', 'DL 02 CD 5678', 4, 550.00),
(5, 2, 'Mercedes-Benz GLE', 'KA 05 EF 9012', 5, 700.00),
(6, 2, 'BMW X5', 'TN 06 GH 3456', 4, 620.00),
(7, 2, 'Audi Q7', 'UP 07 IJ 7890', 5, 580.00),
(8, 11, 'Range Rover', 'GJ 08 KL 1234', 4, 800.00),
(9, 11, 'Bentley Bentayga', 'MH 09 MN 5678', 4, 900.00),
(10, 11, 'Rolls-Royce Cullinan', 'UP 10 OP 9012', 4, 850.00),
(11, 11, 'Ferrari Portofino', 'DL 11 QR 3456', 4, 1000.00),
(12, 11, 'Lamborghini Urus', 'TN 12 ST 7890', 4, 950.00),
(13, 13, 'Maserati Levante', 'KA 13 UV 1234', 5, 750.00),
(14, 13, 'Aston Martin DBX', 'MH 14 WX 5678', 4, 700.00),
(15, 13, 'McLaren GT', 'GJ 15 YZ 9012', 5, 820.00),
(16, 13, 'Bugatti Chiron', 'DL 16 AB 3456', 5, 1500.00),
(17, 13, 'Porsche 911', 'TN 17 CD 7890', 5, 1200.00);

-- --------------------------------------------------------

--
-- Table structure for table `Images`
--

CREATE TABLE `Images` (
  `imageId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `carId` int(11) DEFAULT NULL,
  `fileName` varchar(100) NOT NULL,
  `uploadedOn` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Images`
--

INSERT INTO `Images` (`imageId`, `userId`, `carId`, `fileName`, `uploadedOn`) VALUES
(6, 2, 3, 'teslaS_1.jpg', '2024-01-11 13:16:34'),
(7, 2, 3, 'teslaS_2.jpg', '2024-01-11 13:16:34'),
(8, 2, 3, 'teslaS_3.jpg', '2024-01-11 13:16:34'),
(9, 2, 3, 'teslaS_4.jpg', '2024-01-11 13:16:34'),
(10, 2, 4, 'porscheCayenne_1.jpg', '2024-01-11 13:16:34'),
(11, 2, 4, 'porscheCayenne_2.jpg', '2024-01-11 13:16:34'),
(12, 2, 4, 'porscheCayenne_3.jpg', '2024-01-11 13:16:34'),
(13, 2, 4, 'porscheCayenne_4.jpg', '2024-01-11 13:16:34'),
(14, 2, 5, 'MercedesBenzGle_1.jpg', '2024-01-11 13:16:34'),
(15, 2, 5, 'MercedesBenzGle_2.jpg', '2024-01-11 13:16:34'),
(16, 2, 5, 'MercedesBenzGle_3.jpg', '2024-01-11 13:16:34'),
(17, 2, 5, 'MercedesBenzGle_4.jpg', '2024-01-11 13:16:34'),
(18, 2, 6, 'bmwX5_1.jpg', '2024-01-11 13:16:34'),
(19, 2, 6, 'bmwX5_2.jpg', '2024-01-11 13:16:34'),
(20, 2, 6, 'bmwX5_3.jpg', '2024-01-11 13:16:34'),
(21, 2, 6, 'bmwX5_4.jpg', '2024-01-11 13:16:34'),
(22, 2, 7, 'audiQ7_1.jpg', '2024-01-11 13:16:34'),
(23, 2, 7, 'audiQ7_2.jpg', '2024-01-11 13:16:34'),
(24, 2, 7, 'audiQ7_3.jpg', '2024-01-11 13:16:34'),
(25, 2, 7, 'audiQ7_4.jpg', '2024-01-11 13:16:34'),
(26, 11, 8, 'rangeRover_1.jpg', '2024-01-11 13:23:51'),
(27, 11, 8, 'rangeRover_2.jpg', '2024-01-11 13:23:51'),
(28, 11, 8, 'rangeRover_3.jpg', '2024-01-11 13:23:51'),
(29, 11, 8, 'rangeRover_4.jpg', '2024-01-11 13:23:51'),
(30, 11, 9, 'bentleyBentayga_1.jpg', '2024-01-11 13:23:51'),
(31, 11, 9, 'bentleyBentayga_2.jpg', '2024-01-11 13:23:51'),
(32, 11, 9, 'bentleyBentayga_3.jpg', '2024-01-11 13:23:51'),
(33, 11, 9, 'bentleyBentayga_4.jpg', '2024-01-11 13:23:51'),
(34, 11, 10, 'rollsRoyceCullinan_1.jpg', '2024-01-11 13:23:51'),
(35, 11, 10, 'rollsRoyceCullinan_2.jpg', '2024-01-11 13:23:51'),
(36, 11, 10, 'rollsRoyceCullinan_3.jpg', '2024-01-11 13:23:51'),
(37, 11, 10, 'rollsRoyceCullinan_4.jpg', '2024-01-11 13:23:51'),
(38, 11, 11, 'ferrariPortofino_1.jpg', '2024-01-11 13:23:51'),
(39, 11, 11, 'ferrariPortofino_2.jpg', '2024-01-11 13:23:51'),
(40, 11, 11, 'ferrariPortofino_3.jpg', '2024-01-11 13:23:51'),
(41, 11, 11, 'ferrariPortofino_4.jpg', '2024-01-11 13:23:51'),
(42, 11, 12, 'lamborghiniUrus_1.jpg', '2024-01-11 13:23:51'),
(43, 11, 12, 'lamborghiniUrus_2.jpg', '2024-01-11 13:23:51'),
(44, 11, 12, 'lamborghiniUrus_3.jpg', '2024-01-11 13:23:51'),
(45, 11, 12, 'lamborghiniUrus_4.jpg', '2024-01-11 13:23:51'),
(46, 13, 13, 'maseratiLevante_1.jpg', '2024-01-11 13:27:49'),
(47, 13, 13, 'maseratiLevante_2.jpg', '2024-01-11 13:27:49'),
(48, 13, 13, 'maseratiLevante_3.jpg', '2024-01-11 13:27:49'),
(49, 13, 13, 'maseratiLevante_4.jpg', '2024-01-11 13:27:49'),
(50, 13, 14, 'astonMartinDbx_1.jpg', '2024-01-11 13:27:49'),
(51, 13, 14, 'astonMartinDbx_2.jpg', '2024-01-11 13:27:49'),
(52, 13, 14, 'astonMartinDbx_3.jpg', '2024-01-11 13:27:49'),
(53, 13, 14, 'astonMartinDbx_4.jpg', '2024-01-11 13:27:49'),
(54, 13, 15, 'mclarenGt_1.jpg', '2024-01-11 13:27:49'),
(55, 13, 15, 'mclarenGt_2.jpg', '2024-01-11 13:27:49'),
(56, 13, 15, 'mclarenGt_3.jpg', '2024-01-11 13:27:49'),
(57, 13, 15, 'mclarenGt_4.jpg', '2024-01-11 13:27:49'),
(58, 13, 16, 'bugattiChiron_1.jpg', '2024-01-11 13:27:49'),
(59, 13, 16, 'bugattiChiron_2.jpg', '2024-01-11 13:27:49'),
(60, 13, 16, 'bugattiChiron_3.jpg', '2024-01-11 13:27:49'),
(61, 13, 16, 'bugattiChiron_4.jpg', '2024-01-11 13:27:49'),
(62, 13, 17, 'porsche911_1.jpg', '2024-01-11 13:27:49'),
(63, 13, 17, 'porsche911_2.jpg', '2024-01-11 13:27:49'),
(64, 13, 17, 'porsche911_3.jpg', '2024-01-11 13:27:49'),
(65, 13, 17, 'porsche911_4.jpg', '2024-01-11 13:27:49');

-- --------------------------------------------------------

--
-- Table structure for table `UserDetails`
--

CREATE TABLE `UserDetails` (
  `customerId` int(11) NOT NULL,
  `fullName` varchar(100) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `addressState` varchar(50) NOT NULL,
  `dob` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `UserDetails`
--

INSERT INTO `UserDetails` (`customerId`, `fullName`, `phone`, `addressState`, `dob`, `gender`) VALUES
(1, 'Josh Willington', '8131965233', 'Noida', '1995-06-25', 'Male'),
(12, 'Russel Dwight', '9865375456', 'Sikkim', '1997-05-17', 'Male'),
(15, 'Bapyn', '9876543210', 'Meghalaya', '1950-01-01', 'Male'),
(16, 'Bapynshgain Nongkynrih', '8974576322', 'Meghalaya', '1999-10-18', 'Male'),
(27, 'Ben', '9774591127', 'Meghalaya', '2015-07-01', 'Male'),
(28, 'Bapyn', '9876543210', 'Meghalaya', '1950-01-01', 'Other'),
(29, 'Gb', '8131887051', 'Assam', '2022-11-21', 'Male');

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `userId` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` char(60) NOT NULL,
  `email` varchar(100) NOT NULL,
  `userType` enum('customer','agency') NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`userId`, `username`, `password`, `email`, `userType`, `createTime`) VALUES
(1, 'JoshWillington', '$2y$10$xC6tUD3CC2NsPDZSaJGE.OWxYvXsuGZx1.OZI1zDyK748n8R69vBW', 'josh.will@gmail.com', 'customer', '2023-12-26 15:48:50'),
(2, 'MarkBraum', '$2y$10$q7fGQXY5zcJRPvcIvmP4ruPQrlqOIvN/xy/0U58O.w3fnHFkqyyk2', 'mark.braum@gmail.com', 'agency', '2023-12-26 15:51:53'),
(11, 'JasonMiles', '$2y$10$vn8xA6Zjal7zVTkMFsEpt.83ITmbHMGrZ7z7VyWcrbcCe/7OOIbpC', 'jason.miles@gmail.com', 'agency', '2023-12-26 17:09:16'),
(12, 'RusselDwight', '$2y$10$Rfv04OzbQnIEP.qq2PW5BOiqwi9ZDAws0NuiQyMpYgPWpJfmWtzhC', 'russel.dwight@gmail.com', 'customer', '2023-12-26 17:14:57'),
(13, 'PercivalGraves', '$2y$10$5sc.Gx5jo3Pb.M1.DDaBCes.osMLYR5Sa/8ya6YnRcjJlpCruLKIG', 'percival.graves@gmail.com', 'agency', '2023-12-26 19:29:29'),
(15, 'Bapyn', '$2y$10$EoabzHozTMysw0CvlCH93.uKeI8/ak9kEKNqAgcE6hDp0jQOr5lai', 'iambapyn@gmail.com', 'customer', '2023-12-29 01:18:38'),
(16, 'StormBrine', '$2y$10$YY.0giLsM0pPfSg2LqoZ2.LG2xBLWuKRvOB41HCy8c9fNF7824wVa', 'bapynshngain.nongkynrih@gmail.com', 'customer', '2023-12-29 20:15:12'),
(21, 'Holly', '$2y$10$Lmgv4Fq43UtoOwmwfInAJu0iA0KN4lB.dFyNbNurCHHbXlYfFgwzm', 'okbass@gmail.com', 'agency', '2024-01-02 15:11:11'),
(27, 'Bmarak', '$2y$10$cwhN7.GMWHAn3HIHhEAEFOiEKJQCEvXz9M.xESR6BWTdwOyT27fgi', 'benielmarak@gmail.com', 'customer', '2024-01-09 05:34:44'),
(28, 'Bapyn69', '$2y$10$E4J7ZQ6hcBWNxeXdeYMdcuHWf1t6pyVgxUs2O6xAsBWsHxTHvAIDq', 'bapynshngain.nongkynrih@gmail.com', 'customer', '2024-01-09 15:20:51'),
(29, 'Gh', '$2y$10$HfmTmbLMwM8k33lmGqFAWen9KJwCgSNjW23NsYtvNpmg0hE1gSir2', 'tbhi@gail.com', 'customer', '2024-01-15 18:06:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `AgencyDetails`
--
ALTER TABLE `AgencyDetails`
  ADD PRIMARY KEY (`agencyId`);

--
-- Indexes for table `Bookings`
--
ALTER TABLE `Bookings`
  ADD PRIMARY KEY (`bookingId`),
  ADD KEY `car_id` (`carId`),
  ADD KEY `Bookings_ibfk_2` (`agencyId`),
  ADD KEY `Bookings_ibfk_3` (`customerId`);

--
-- Indexes for table `Cars`
--
ALTER TABLE `Cars`
  ADD PRIMARY KEY (`carId`),
  ADD KEY `agency_id` (`agencyId`);

--
-- Indexes for table `Images`
--
ALTER TABLE `Images`
  ADD PRIMARY KEY (`imageId`),
  ADD KEY `userId` (`userId`),
  ADD KEY `carId` (`carId`);

--
-- Indexes for table `UserDetails`
--
ALTER TABLE `UserDetails`
  ADD PRIMARY KEY (`customerId`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`userId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Bookings`
--
ALTER TABLE `Bookings`
  MODIFY `bookingId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `Cars`
--
ALTER TABLE `Cars`
  MODIFY `carId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `Images`
--
ALTER TABLE `Images`
  MODIFY `imageId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `AgencyDetails`
--
ALTER TABLE `AgencyDetails`
  ADD CONSTRAINT `AgencyDetails_ibfk_1` FOREIGN KEY (`agencyId`) REFERENCES `Users` (`userId`);

--
-- Constraints for table `Bookings`
--
ALTER TABLE `Bookings`
  ADD CONSTRAINT `Bookings_ibfk_1` FOREIGN KEY (`carId`) REFERENCES `Cars` (`carId`),
  ADD CONSTRAINT `Bookings_ibfk_2` FOREIGN KEY (`agencyId`) REFERENCES `Users` (`userId`),
  ADD CONSTRAINT `Bookings_ibfk_3` FOREIGN KEY (`customerId`) REFERENCES `Users` (`userId`);

--
-- Constraints for table `Cars`
--
ALTER TABLE `Cars`
  ADD CONSTRAINT `Cars_ibfk_1` FOREIGN KEY (`agencyId`) REFERENCES `Users` (`userId`);

--
-- Constraints for table `Images`
--
ALTER TABLE `Images`
  ADD CONSTRAINT `Images_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `Users` (`userId`),
  ADD CONSTRAINT `Images_ibfk_2` FOREIGN KEY (`carId`) REFERENCES `Cars` (`carId`);

--
-- Constraints for table `UserDetails`
--
ALTER TABLE `UserDetails`
  ADD CONSTRAINT `UserDetails_ibfk_1` FOREIGN KEY (`customerId`) REFERENCES `Users` (`userId`);
COMMIT;
