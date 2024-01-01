<?php
require_once('dbConnect.php');
// include 'dbConnect.php';
require_once('sqlPrepare.php');

$currentUrl = '/assets/dbHandling.php';

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

if ($_SERVER["REQUEST_METHOD"] == "GET" && $_SERVER['REQUEST_URI'] == $currentUrl . '/Cars/') {
	global $data;

	$sql = "SELECT * from Cars";
	$sqlResult = $conn->query($sql);

	if ($sqlResult->num_rows > 0) {
		// Fetch each row from the result set
		while ($row = $sqlResult->fetch_assoc()) {
			$agency_id = $row['agency_id'];
			$car_id = $row['car_id'];
			unset($row['agency_id']);
			unset($row['car_id']);

			if (!isset($data['availableCars']['agencyId'][$agency_id]))
				$data['availableCars']['agencyId'][$agency_id] = ['carId' => []];

			$data['availableCars']['agencyId'][$agency_id]['carId'] += [$car_id => $row];
		}

		http_response_code(200);
		$data += [
			'message' => 'Car list fetched successfully.'
		];
	} else {
		http_response_code(404);
		$data += [
			"error" => "There are no cars listed in the Cars table.",
			"message" => "Error: " . $sql . " " . $conn->error
		];
	}
} else if(str_contains($_SERVER['REQUEST_URI'], $currentUrl . '/Bookings/Agency/')) {
    
    // $data['url'] = $_SERVER['SERVER_NAME']; // returns with just the domain name
    // $data['url'] = $_SERVER['HTTP_HOST']; // returns domain name and port as well
    // $data['url'] = $_SERVER['HTTP_REFERER']; // returns the entire url from which request was made
    
	// Routing logic based on URL paths
	$requestUri = $_SERVER['REQUEST_URI'];

	// Remove the base URL from the request URI
	$agencyId = str_replace($currentUrl . "/Bookings/Agency/", '', $requestUri);
	
	// $path = str_replace($baseUrl, '', $requestUri);
	// $path = rtrim($path, '/');
	// $segments = explode('/', $path);

	// if (!isset($segments[1])) {
	if (empty($agencyId)) {
		$data['message'] = 'not a get request for bookings with car id.';
		echo json_encode($data);
		die();
	}

	$sql = 'SELECT * FROM Bookings WHERE agency_id = "' . $agencyId . '"';
	$sqlResult = $conn->query($sql);

	if ($sqlResult->num_rows > 0) {
		// Fetch each row from the result set
// 		while ($row = $sqlResult->fetch_assoc()) {
        while ( $row = $sqlResult->fetch_assoc() ) {
// booking_id 	car_id 	customer_id 	agency_id 	book_date 	end_date 	
			$agency_id = $row['agency_id'];
			$car_id = $row['car_id'];

			$sql = 'SELECT username FROM Users WHERE BINARY user_id = "' . $row['customer_id'] . '"';
			$sqlReturn = $conn->query($sql);
			$sqlReturn = $sqlReturn->fetch_assoc();

			$row['customer_name'] = $sqlReturn['username'];

			$sql = 'SELECT model FROM Cars WHERE BINARY car_id = "' . $row['car_id'] . '"';
			$sqlReturn = $conn->query($sql);
			$sqlReturn = $sqlReturn->fetch_assoc();

			$row['model'] = $sqlReturn['model'];

			unset($row['agency_id']);
			unset($row['booking_id']);

			// if (!isset($data['availableCars']['agencyId'][$agency_id]))
			// 	$data['availableCars']['agencyId'][$agency_id] = ['carId' => []];

			// $data['availableCars']['agencyId'][$agency_id]['carId'] += [$car_id => $row];


// 			if (!isset($data['bookedCars']))
// 				$data['bookedCars'] = [];
$carId = $row['car_id'];
unset($row['car_id']);
			$data['carId'][$carId] = $row;
		}

		http_response_code(200);
		$data += [
			'message' => "Agency's booked cars list fetched successfully."
		];
	} else {
		http_response_code(404);
		$data += [
			"error" => "There are no cars booked from the agency in the Bookings table.",
			"message" => "Error: " . $sql . " " . $conn->error
		];
	}
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
			$stmtResult = executePreparedStatement($conn, $sql, "isssss", $user_id, $full_name, $dob, $gender, $phone, $address_state);
		} else {
			$agency_name = $_POST["agency_name"];

			$sql = "INSERT INTO AgencyDetails(agency_id, agency_name, full_name, phone, address_state) 
                VALUES (?, ?, ?, ?, ?)";
			$stmtResult = executePreparedStatement($conn, $sql, "issss", $user_id, $agency_name, $full_name, $phone, $address_state);
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

	return $insertStatus;
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

function carExists($conn, $licenseNumber): bool
{
	$sql = "SELECT car_id FROM Cars WHERE BINARY license_number = ?";
	$stmtResult = executePreparedStatement($conn, $sql, "s", $licenseNumber);
	if ($stmtResult->get_result()->num_rows > 0)
		return true;
	return false;
}

// Function to insert data into Cars Table
function insertCars($conn)
{
	$licenseNumber = $_POST['licenseNumber'];
	if (carExists($conn, $licenseNumber)) {
		http_response_code(409);
		return [
			'error' => 'Car already exists',
			'message' => 'A car with the same license number already exists. Please add a different one.'
		];
	}

	$userId = 2; //Replace with function to retrieve userId from DB using userName

	$model = $_POST['model'];
	$capacity = $_POST['capacity'];
	$rentPerDay = $_POST['rentPerDay'];

	// Insert data into Cars Table
	$sql = "INSERT INTO Cars (agency_id, model, license_number, capacity, rent_per_day) VALUES (?, ?, ?, ?, ?)";
	$stmtResult = executePreparedStatement($conn, $sql, "issss", $userId, $model, $licenseNumber, $capacity, $rentPerDay);

	if ($stmtResult->affected_rows === 1) {
		http_response_code(201);
		$insertStatus = [
			'message' => 'New car: "' . $licenseNumber . '" recorded into Cars table successfully',
			'user_id' => '$userId', // Include any relevant user details like user ID
			'token' => 'your_authentication_token_here' // Include the authentication token/session information
		];
	} else {
		http_response_code(500);
		$insertStatus = [
			"error" => "Car addition failed",
			"message" => "Error: " . $sql . " " . $conn->error
		];
	}


	// Check if the form data for Cars Table is set
	// if (isset($_POST['vehicle_model']) && isset($_POST['vehicle_number']) && isset($_POST['seating_capacity']) && isset($_POST['rent_per_day']) && isset($_POST['agency_id'])) {

	// } else {
	// 	$insertStatus = "All form fields for Cars Table are required!";
	// }
	return $insertStatus;
}


function bookingExists($conn, $licenseNumber, $newBookDate): bool
{
	$sql = "SELECT end_date FROM Cars WHERE BINARY license_number = ?";
	$stmtResult = executePreparedStatement($conn, $sql, "s", $licenseNumber);

	if ($stmtResult->get_result()->num_rows > 0) {
		$newBookDate = new DateTime($newBookDate);
		while ($row = $stmtResult->get_result()->fetch_assoc()) {
			$oldEndDate = new DateTime($row['end_date']);
			if ($oldEndDate >= $newBookDate)
				return true;
		}
	}
	return false;
}

// Function to insert data into Bookings Table
function insertBookings($conn)
{
	// Check if the form data for Bookings Table is set
	if (isset($_POST['carId'])) { // && isset($_POST['customer_id']) && isset($_POST['start_date']) && isset($_POST['end_date'])) {
		$car_id = $_POST['carId'];
		$customer_id = $_POST['userId'];
		$start_date = $_POST['bookDate'];

		$days_booked = $_POST['daysBooked'];
		$end_date = date('Y-m-d', strtotime($start_date . ' + ' . $days_booked . ' days'));

		$sql = 'SELECT agency_id FROM Cars WHERE car_id = ?';
		$stmtResult = executePreparedStatement($conn, $sql, "i", $car_id);
		$stmtResult = $stmtResult->fetch_assoc();
		$agency_id = $stmtResult["agency_id"];
		
		// Insert data into Bookings Table
		$sql = "INSERT INTO Bookings (car_id, customer_id, agency_id, book_date, end_date) VALUES (?, ?, ?, ?)";
		$stmtResult = executePreparedStatement($conn, $sql, "iiiss", $car_id, $customer_id, $agency_id, $start_date, $end_date);

		if ($stmtResult->affected_rows === 1) {
			$insertStatus = [
				'message' => 'New booking on carId: "' . $car_id . '" recorded into Bookings table successfully',
				'user_id' => '$userId', // Include any relevant user details like user ID
				'token' => 'your_authentication_token_here' // Include the authentication token/session information
			];
		} else {
			$insertStatus = [
				"error" => "Booking failed",
				"message" => "Error: " . $sql . " " . $conn->error
			];
		}
	} else {
		$insertStatus = [
			"error" => "Incomplete form",
			"message" => "All form fields for Bookings table are required!"
		];
	}

	return $insertStatus;
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