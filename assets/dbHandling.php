<?php

include 'dbConnect.php';
include 'sqlPrepare.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_action'])) {
	$submit_action = $_POST['submit_action'];

	$case = [
		'submit_users' => fn() => insertUsers($conn),
		'submit_agencies' => fn() => insertUsers($conn),
		'submit_cars' => fn() => insertCars($conn),
		'submit_bookings' => fn() => insertBookings($conn)
	];

	if (array_key_exists($submit_action, $case))
		$data += $case[$submit_action]();
	else
		$data['formStatus'] = "Invalid action";
}

function userExists($conn, $username)
{
	$sql = "SELECT user_id FROM Users WHERE BINARY username = ?";
	$stmtResult = executePreparedStatement($conn, $sql, "s", $username);
	if ($stmtResult->get_result()->num_rows > 0)
		return true;
	return false;
}

// Function to insert data into Users Table
function insertUsers($conn)
{
	// Check if the form data for Users Table is set

	$username = $_POST['username'];

	if (userExists($conn, $username)) {
		http_response_code(409);
		return [
			'error' => 'User already exists',
			'message' => 'The username is already in use. Please choose a different one.'
		];
	}

	$password = $_POST['password'];
	$email = $_POST['email'];
	$user_type = $_POST['user_type'];

	$full_name = $_POST['full_name'];
	$phone = $_POST['phone'];
	$address_state = $_POST['address_state'];

	$passHash = password_hash("$password", PASSWORD_DEFAULT);


	$sql = "INSERT INTO Users (username, password, email, user_type) VALUES (?, ?, ?, ?)";
	$stmtResult = executePreparedStatement($conn, $sql, "ssss", $username, $passHash, $email, $user_type);

	if ($stmtResult->affected_rows === 1) {
		$user_id = $conn->insert_id;

		if ($_POST['user_type'] == 'customer') {
			$dob = $_POST['dob'];
			$gender = $_POST['gender'];

			$sql = "INSERT INTO UserDetails(user_id, full_name, dob, gender, phone, address_state) 
                VALUES (?, ?, ?, ?, ?, ?)";
			$stmtResult = executePreparedStatement($conn, $sql, "ssssss", $user_id, $full_name, $dob, $gender, $phone, $address_state);
		} else {
			$agency_name = $_POST["agency_name"];

			$sql = "INSERT INTO AgencyDetails(agency_id, agency_name, full_name, phone, address_state) 
                VALUES (?, ?, ?, ?, ?)";
			$stmtResult = executePreparedStatement($conn, $sql, "sssss", $user_id, $agency_name, $full_name, $phone, $address_state);
		}
		if ($stmtResult->affected_rows === 1) {
			http_response_code(201);
			$insertStatus = [
				'message' => 'New user: "' . $username . '" recorded into Users table successfully',
				'user_id' => '$userId', // Include any relevant user details like user ID
				'token' => 'your_authentication_token_here' // Include the authentication token/session information
			];
		} else {
			http_response_code(500);
			$insertStatus = [
				"error" => "Account details creation failed",
				"message" => "Error: " . $sql . " " . $conn->error
			];
		}
	} else {
		http_response_code(500);
		$insertStatus = [
			"error" => "Account creation failed.",
			"message" => "Error: " . $sql . " " . $conn->error
		];
	}

	$data = $insertStatus;
	return $data;
}

// Function to insert data into Agencies Table
function insertAgencies($conn)
{
	// Check if the form data for Agencies Table is set
	if (isset($_POST['agency_name']) && isset($_POST['agency_address']) && isset($_POST['contact_details'])) {
		$agency_name = $_POST['agency_name'];
		$agency_address = $_POST['agency_address'];
		$contact_details = $_POST['contact_details'];

		// Insert data into Agencies Table
		$sqlAgencies = "INSERT INTO Agencies (agency_name, agency_address, contact_details)
                    VALUES ('$agency_name', '$agency_address', '$contact_details')";

		if ($conn->query($sqlAgencies) === TRUE) {
			$insertStatus = "New record inserted into Agencies table successfully";
		} else {
			$insertStatus = "Error: " . $sqlAgencies . " " . $conn->error;
		}
	} else {
		$insertStatus = "All form fields for Agencies Table are required!";
	}

	$data["agencyCreationStatus"] = $insertStatus;
	return $data;
}

// Function to insert data into Cars Table
function insertCars($conn)
{
	// Check if the form data for Cars Table is set
	if (isset($_POST['vehicle_model']) && isset($_POST['vehicle_number']) && isset($_POST['seating_capacity']) && isset($_POST['rent_per_day']) && isset($_POST['agency_id'])) {
		$vehicle_model = $_POST['vehicle_model'];
		$vehicle_number = $_POST['vehicle_number'];
		$seating_capacity = $_POST['seating_capacity'];
		$rent_per_day = $_POST['rent_per_day'];
		$agency_id = $_POST['agency_id'];

		// Insert data into Cars Table
		$sqlCars = "INSERT INTO Cars (vehicle_model, vehicle_number, seating_capacity, rent_per_day, agency_id)
                    VALUES ('$vehicle_model', '$vehicle_number', '$seating_capacity', '$rent_per_day', '$agency_id')";

		if ($conn->query($sqlCars) === TRUE) {
			$insertStatus = "New record inserted into Cars table successfully";
		} else {
			$insertStatus = "Error: " . $sqlCars . " " . $conn->error;
		}
	} else {
		$insertStatus = "All form fields for Cars Table are required!";
	}

	$data["insertCarStatus"] = $insertStatus;
	return $data;
}

// Function to insert data into Bookings Table
function insertBookings($conn)
{
	// Check if the form data for Bookings Table is set
	if (isset($_POST['car_id']) && isset($_POST['customer_id']) && isset($_POST['start_date']) && isset($_POST['end_date'])) {
		$car_id = $_POST['car_id'];
		$customer_id = $_POST['customer_id'];
		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];

		// Insert data into Bookings Table
		$sqlBookings = "INSERT INTO Bookings (car_id, customer_id, start_date, end_date)
                    VALUES ('$car_id', '$customer_id', '$start_date', '$end_date')";

		if ($conn->query($sqlBookings) === TRUE) {
			$insertStatus = "New record inserted into Bookings table successfully";
		} else {
			$insertStatus = "Error: " . $sqlBookings . " " . $conn->error;
		}
	} else {
		$insertStatus = "All form fields for Bookings Table are required!";
	}

	$data["insertBookingStatus"] = $insertStatus;
	return $data;
}

echo json_encode($data);
$conn->close();
?>





<?php
//FETCH DATA


// include 'dbConnect.php';

// // Fetch data from Users Table
// $sql = "SELECT * FROM Users";
// $result = $conn->query($sql);

// $data = array();
// if ($result->num_rows > 0) {
//     while ($row = $result->fetch_assoc()) {
//         $data[] = $row;
//     }
// }

// $insertStatus = json_encode($data);

// $conn->close();
?>