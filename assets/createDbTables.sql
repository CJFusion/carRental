-- Create Users Table
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password CHAR(60) NOT NULL,
    email VARCHAR(100) NOT NULL,
    user_type ENUM('customer', 'agency') NOT NULL
);

-- Create Agencies Table
CREATE TABLE Agencies (
    agency_id INT AUTO_INCREMENT PRIMARY KEY,
    agency_name VARCHAR(100) NOT NULL,
    agency_address VARCHAR(200) NOT NULL,
    contact_details VARCHAR(100)
);

-- Create Cars Table
CREATE TABLE Cars (
    car_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_model VARCHAR(100) NOT NULL,
    vehicle_number VARCHAR(20) NOT NULL,
    seating_capacity INT NOT NULL,
    rent_per_day DECIMAL(10, 2) NOT NULL,
    agency_id INT,
    FOREIGN KEY (agency_id) REFERENCES Agencies(agency_id)
);

-- Create Bookings Table
CREATE TABLE Bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT,
    customer_id INT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    FOREIGN KEY (car_id) REFERENCES Cars(car_id),
    FOREIGN KEY (customer_id) REFERENCES Users(user_id)
);

-- Create Customer Details Table (Additional details for customers)
CREATE TABLE CustomerDetails (
    user_id INT PRIMARY KEY,
    agency_name VARCHAR(100),
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address_state VARCHAR(50) NOT NULL,
    dob DATE,
    gender ENUM('Male', 'Female', 'Other'),
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);