-- Create Users Table
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password CHAR(60) NOT NULL,
    email VARCHAR(100) NOT NULL,
    user_type ENUM('customer', 'agency') NOT NULL,
	create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP 
);

-- Create Customer Details Table (Additional details for customers)
CREATE TABLE UserDetails (
    user_id INT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(10) NOT NULL,
    address_state VARCHAR(50) NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- Create Agencies Table
CREATE TABLE AgencyDetails (
    agency_id INT PRIMARY KEY,
    agency_name VARCHAR(100) NOT NULL,
	full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(10) NOT NULL,
    address_state VARCHAR(50) NOT NULL,
	FOREIGN KEY (agency_id) REFERENCES Users(user_id)
);

-- Create Cars Table
CREATE TABLE Cars (
    car_id INT AUTO_INCREMENT PRIMARY KEY,
	agency_id INT NOT NULL,
    model VARCHAR(100) NOT NULL,
    license_number VARCHAR(20) NOT NULL,
    capacity INT NOT NULL,
    rent_per_day DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (agency_id) REFERENCES Users(user_id)
);


-- INSERT INTO Cars (agency_id, model, license_number, capacity, rent_per_day)
-- VALUES 
--     -- For agency_id = 2
--     (2, 'Tesla Model S', 'MH 01 AB 1234', 5, 600.00),
--     (2, 'Porsche Cayenne', 'DL 02 CD 5678', 4, 550.00),
--     (2, 'Mercedes-Benz GLE', 'KA 05 EF 9012', 5, 700.00),
--     (2, 'BMW X5', 'TN 06 GH 3456', 4, 620.00),
--     (2, 'Audi Q7', 'UP 07 IJ 7890', 5, 580.00),
    
--     -- For agency_id = 11
--     (11, 'Land Rover Range Rover', 'GJ 08 KL 1234', 4, 800.00),
--     (11, 'Bentley Bentayga', 'MH 09 MN 5678', 4, 900.00),
--     (11, 'Rolls-Royce Cullinan', 'UP 10 OP 9012', 4, 850.00),
--     (11, 'Ferrari Portofino', 'DL 11 QR 3456', 4, 1000.00),
--     (11, 'Lamborghini Urus', 'TN 12 ST 7890', 4, 950.00),
    
--     -- For agency_id = 13
--     (13, 'Maserati Levante', 'KA 13 UV 1234', 5, 750.00),
--     (13, 'Aston Martin DBX', 'MH 14 WX 5678', 4, 700.00),
--     (13, 'McLaren GT', 'GJ 15 YZ 9012', 5, 820.00),
--     (13, 'Bugatti Chiron', 'DL 16 AB 3456', 5, 1500.00),
--     (13, 'Porsche 911 Turbo', 'TN 17 CD 7890', 5, 1200.00);




-- Create Bookings Table
CREATE TABLE Bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT,
    customer_id INT,
	agency_id INT,
    book_date DATE NOT NULL,
    end_date DATE NOT NULL,
    FOREIGN KEY (car_id) REFERENCES Cars(car_id),
    FOREIGN KEY (customer_id) REFERENCES Users(user_id),
	FOREIGN KEY (agency_id) REFERENCES Users(user_id)
);


-- -- Camel Case
-- -- Create Users Table
-- CREATE TABLE Users (
--     userId INT AUTO_INCREMENT PRIMARY KEY,
--     userName VARCHAR(50) NOT NULL,
--     password CHAR(60) NOT NULL,
--     email VARCHAR(100) NOT NULL,
--     userType ENUM('customer', 'agency') NOT NULL,
--     createTime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP 
-- );
-- 
-- -- Create Customer Details Table (Additional details for customers)
-- CREATE TABLE UserDetails (
--     userId INT PRIMARY KEY,
--     fullName VARCHAR(100) NOT NULL,
--     phone VARCHAR(10) NOT NULL,
--     addressState VARCHAR(50) NOT NULL,
--     dob DATE NOT NULL,
--     gender ENUM('Male', 'Female', 'Other') NOT NULL,
--     FOREIGN KEY (userId) REFERENCES Users(userId)
-- );

-- -- Create Agencies Table
-- CREATE TABLE AgencyDetails (
--     agencyId INT PRIMARY KEY,
--     agencyName VARCHAR(100) NOT NULL,
--     fullName VARCHAR(100) NOT NULL,
--     phone VARCHAR(10) NOT NULL,
--     addressState VARCHAR(50) NOT NULL,
--     FOREIGN KEY (agencyId) REFERENCES Users(userId)
-- );

-- -- Create Cars Table
-- CREATE TABLE Cars (
--     carId INT AUTO_INCREMENT PRIMARY KEY,
--     agencyId INT NOT NULL,
--     model VARCHAR(100) NOT NULL,
--     licenseNumber VARCHAR(20) NOT NULL,
--     capacity INT NOT NULL,
--     rentPerDay DECIMAL(10, 2) NOT NULL,
--     FOREIGN KEY (agencyId) REFERENCES Users(userId)
-- );

-- -- Create Bookings Table
-- CREATE TABLE Bookings (
--     bookingId INT AUTO_INCREMENT PRIMARY KEY,
--     carId INT,
--     customerId INT,
--     bookDate DATE NOT NULL,
--     endDate DATE NOT NULL,
--     FOREIGN KEY (carId) REFERENCES Cars(carId),
--     FOREIGN KEY (customerId) REFERENCES Users(userId)
-- );



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