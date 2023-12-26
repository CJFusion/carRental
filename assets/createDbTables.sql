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
    vehicle_model VARCHAR(100) NOT NULL,
    vehicle_number VARCHAR(20) NOT NULL,
    seating_capacity INT NOT NULL,
    rent_per_day DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (agency_id) REFERENCES Users(user_id)
);

-- Create Bookings Table
CREATE TABLE Bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT,
    customer_id INT,
    book_date DATE NOT NULL,
    end_date DATE NOT NULL,
    FOREIGN KEY (car_id) REFERENCES Cars(car_id),
    FOREIGN KEY (customer_id) REFERENCES Users(user_id)
);

