-- Create Users Table
CREATE TABLE Users (
    userId INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password CHAR(60) NOT NULL,
    email VARCHAR(100) NOT NULL,
    userType ENUM('customer', 'agency') NOT NULL,
	createTime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP 
);

-- Create Customer Details Table (Additional details for customers)
CREATE TABLE UserDetails (
    customerId INT PRIMARY KEY,
    fullName VARCHAR(100) NOT NULL,
    phone VARCHAR(10) NOT NULL,
    addressState VARCHAR(50) NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    FOREIGN KEY (customerId) REFERENCES Users(userId)
);

-- Create Agencies Table
CREATE TABLE AgencyDetails (
    agencyId INT PRIMARY KEY,
    agencyName VARCHAR(100) NOT NULL,
	fullName VARCHAR(100) NOT NULL,
    phone VARCHAR(10) NOT NULL,
    addressState VARCHAR(50) NOT NULL,
	FOREIGN KEY (agencyId) REFERENCES Users(userId)
);

-- Create Cars Table
CREATE TABLE Cars (
    carId INT AUTO_INCREMENT PRIMARY KEY,
	agencyId INT NOT NULL,
    model VARCHAR(100) NOT NULL,
    licenseNumber VARCHAR(20) NOT NULL,
    capacity INT NOT NULL,
    rentPerDay DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (agencyId) REFERENCES Users(userId)
);

-- Create Bookings Table
CREATE TABLE Bookings (
    bookingId INT AUTO_INCREMENT PRIMARY KEY,
    carId INT NOT NULL,
    customerId INT NOT NULL,
	agencyId INT NOT NULL,
    bookDate DATE NOT NULL,
    endDate DATE NOT NULL,
    FOREIGN KEY (carId) REFERENCES Cars(carId),
    FOREIGN KEY (customerId) REFERENCES Users(userId),
	FOREIGN KEY (agencyId) REFERENCES Users(userId)
);

-- // SQL file to be executed
-- $sqlFile = 'path/to/your_sql_file.sql';

-- // Read the SQL file
-- $sql = file_get_contents($sqlFile);

-- // Execute multi-query (assuming the SQL file contains multiple queries separated by ';')
-- if ($conn->multi_query($sql) === TRUE) {
--     echo "SQL file executed successfully";
-- } else {
--     echo "Error executing SQL file: " . $conn->error;
-- }

-- // Close connection
-- $conn->close();