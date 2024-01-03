<?php
require_once('dbConnect.php');
require_once('sqlPrepare.php');
require_once('sessionConfig.php');

$currentUrl = '/assets/dbHandling.php';

if (!isAjaxOrFetch()) {
	http_response_code(400);
	echo json_encode([
		'error' => 'Bad Request',
		'message' => 'Direct request detected. Use the application as intended.'
	]);
	exit();
}

function isAjaxOrFetch()
{
	return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'fetch');
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitAction'])) {
	$submitAction = $_POST['submitAction'];

	$case = [
		'submitUsers' => fn() => insertUsers($conn),
		'submitAgencies' => fn() => insertUsers($conn),
		'submitCars' => fn() => insertCars($conn),
		'submitBookings' => fn() => insertBookings($conn)
	];

	if (array_key_exists($submitAction, $case))
		$data += $case[$submitAction]();
	else
		$data['formStatus'] = "Invalid action";
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && $_SERVER['REQUEST_URI'] == $currentUrl . '/Cars/') {
	global $data;

	$sql = "SELECT * from Cars";
	$sqlExec = $conn->query($sql);

	if ($sqlExec->num_rows > 0) {
		// Fetch each row from the result set
		while ($row = $sqlExec->fetch_assoc()) {
			$agencyId = $row['agencyId'];
			$carId = $row['carId'];
			unset($row['agencyId']);
			unset($row['carId']);

			if (!isset($data['availableCars']['agencyId'][$agencyId]))
				$data['availableCars']['agencyId'][$agencyId] = ['carId' => []];

			$data['availableCars']['agencyId'][$agencyId]['carId'] += [$carId => $row];
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
} elseif ($_SERVER["REQUEST_METHOD"] == "GET" && str_contains($_SERVER['REQUEST_URI'], $currentUrl . '/Bookings/Agency/')) {
	// expected path of request '/home/viewRentals.html


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
	if (empty($agencyId) & $agencyId != '0') {
		$data['message'] = 'not a get request for bookings with car id, but a fetch all'; // TODO: Include GET request for all cars from all agencies if required.
		echo json_encode($data);
		die();
	}

	if (session_status() === PHP_SESSION_NONE)
		startSession();
	// FIXME: 10. Check if forced logout when user is not logged in is necessary or not
	if (!isset($_SESSION['user'])) {
		require_once('logout.php');
	}

	$agencyId = filter_var($_SESSION['userId'], FILTER_SANITIZE_NUMBER_INT); // FIXME: Fix strange logic implemented here to retrieve all cars from the agency which has been booked

	$sql = 'SELECT * FROM Bookings WHERE agencyId = "' . $agencyId . '"';
	$sqlExec = $conn->query($sql);

	if ($sqlExec->num_rows > 0) {
		// Fetch each row from the result set
		while ($row = $sqlExec->fetch_assoc()) {
			$agencyId = $row['agencyId'];
			$carId = $row['carId'];

			$sql = 'SELECT username FROM Users WHERE BINARY userId = "' . $row['customerId'] . '"';
			$nestedSqlExec = $conn->query($sql);

			$row['customerName'] = $nestedSqlExec->fetch_assoc()['username'];

			$sql = 'SELECT model FROM Cars WHERE BINARY carId = "' . $row['carId'] . '"';
			$nestedSqlExec = $conn->query($sql);

			$row['model'] = $nestedSqlExec->fetch_assoc()['model'];

			$bookingId = $row['bookingId'];
			unset($row['agencyId']);
			unset($row['bookingId']);

			// if (!isset($data['availableCars']['agencyId'][$agencyId]))
			// 	$data['availableCars']['agencyId'][$agencyId] = ['carId' => []];

			// $data['availableCars']['agencyId'][$agencyId]['carId'] += [$carId => $row];


			// 			if (!isset($data['bookedCars']))
// 				$data['bookedCars'] = [];
			$carId = $row['carId'];
			unset($row['carId']);
			if (!isset($data['carId'][$carId][$bookingId]))
				$data['carId'][$carId][$bookingId] = [];
			$data['carId'][$carId]['model'] = $row['model'];
			unset($row['model']);
			$data['carId'][$carId][$bookingId] = $row;
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
} elseif ($_SERVER["REQUEST_METHOD"] == "GET" && $_SERVER['REQUEST_URI'] === $currentUrl . '/isCustomer') {
	// expected path of request '/home/rentCar.html'
	if (session_status() === PHP_SESSION_NONE)
		startSession();

	// FIXME: Add feature where if user is not logged in, display log in option on '/home/rentCar.html' ProfileOverlay as well as allow booking form fillup and on renting a car lead to login page
	// FIXME: 11. Check if forced logout when user is not logged in is necessary or not
	// if(!isset($_SESSION['user'])) {
	// require_once('logout.php');
	// }

	$userId = $_SESSION['userId'];

	// $sql = 'SELECT userType FROM USERS WHERE userId = ?';
	// $stmtExec = executePreparedStatement($conn, $sql, 'i', $userId);
	// $sqlResult = $stmtExec->fetch_assoc();

	// FIXME: Output response in JSON if necessary
	http_response_code(200);
	// if ($sqlResult['user_type'] === 'customer') {
	if ($_SESSION['userType'] === 'customer') {
		echo 'true';
		exit();
	}
	echo 'false';
	exit();
}

function userExists($conn, $username)
{
	$sql = "SELECT userId FROM Users WHERE BINARY username = ?";
	$stmtExec = executePreparedStatement($conn, $sql, "s", $username);
	if ($stmtExec->get_result()->num_rows > 0)
		return true;
	return false;
}

function initializeSessionDetails($username, $userId, $userType)
{
	if (session_status() === PHP_SESSION_NONE)
		startSession();

	if (isset($_SESSION['user']) && $_SESSION['user'] !== $username) {
		destroySession();
		startSession();
	}

	$_SESSION['user'] = $username;
	$_SESSION['userId'] = $userId;
	$_SESSION['userType'] = $userType;
}

// Function to insert data into Users Table
function insertUsers($conn)
{
	// expected request path '/signUp/registerCustomer.html' && '/signUp/registerAgency.html'

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
	$userType = $_POST['userType'];

	$fullName = $_POST['fullName'];
	$phone = $_POST['phone'];
	$addressState = $_POST['addressState'];

	$passHash = password_hash("$password", PASSWORD_DEFAULT);


	$sql = "INSERT INTO Users (username, password, email, userType) VALUES (?, ?, ?, ?)";
	$stmtExec = executePreparedStatement($conn, $sql, "ssss", $username, $passHash, $email, $userType);

	if ($stmtExec->affected_rows === 1) {
		$userId = $conn->insert_id;

		if ($_POST['userType'] == 'customer') {
			$dob = $_POST['dob'];
			$gender = $_POST['gender'];

			$sql = "INSERT INTO UserDetails(customerId, fullName, dob, gender, phone, addressState) 
                VALUES (?, ?, ?, ?, ?, ?)";
			$stmtExec = executePreparedStatement($conn, $sql, "isssss", $userId, $fullName, $dob, $gender, $phone, $addressState);
		} else {
			$agencyName = $_POST["agencyName"];

			$sql = "INSERT INTO AgencyDetails(agencyId, agencyName, fullName, phone, addressState) 
                VALUES (?, ?, ?, ?, ?)";
			$stmtExec = executePreparedStatement($conn, $sql, "issss", $userId, $agencyName, $fullName, $phone, $addressState);
		}
		if ($stmtExec->affected_rows === 1) {
			http_response_code(201);
			$insertStatus = [
				'message' => 'New user: "' . $username . '" recorded into Users table successfully',
				// 'userId' => '$userId', // Include any relevant user details like user ID
				// 'token' => 'your_authentication_token_here' // Include the authentication token/session information
			];

			initializeSessionDetails(htmlspecialchars($username), htmlspecialchars($userId), htmlspecialchars($userType));
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
	if (isset($_POST['agencyName']) && isset($_POST['agencyAddress']) && isset($_POST['contactDetails'])) {
		$agencyName = $_POST['agencyName'];
		$agencyAddress = $_POST['agencyAddress'];
		$contactDetails = $_POST['contactDetails'];

		// Insert data into Agencies Table
		$sqlAgencies = "INSERT INTO Agencies (agencyName, agencyAddress, contactDetails)
                    VALUES ('$agencyName', '$agencyAddress', '$contactDetails')";

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
	$sql = "SELECT carId FROM Cars WHERE BINARY licenseNumber = ?";
	$stmtExec = executePreparedStatement($conn, $sql, "s", $licenseNumber);
	if ($stmtExec->get_result()->num_rows > 0)
		return true;
	return false;
}

// Function to insert data into Cars Table
function insertCars($conn)
{
	//expected request path '/home/addCar.html'

	if (session_status() === PHP_SESSION_NONE)
		startSession();

	// FIXME: 12. Check if forced logout when user is not logged in is necessary or not
	if (!isset($_SESSION['user'])) {
		require_once('logout.php');
	}
	// expected request path '/home/addCar.html'
	$licenseNumber = $_POST['licenseNumber'];
	if (carExists($conn, $licenseNumber)) {
		http_response_code(409);
		return [
			'error' => 'Car already exists',
			'message' => 'A car with the same license number already exists. Please add a different one.'
		];
	}

	$userId = $_SESSION['userId'];
	$model = $_POST['model'];
	$capacity = $_POST['capacity'];
	$rentPerDay = $_POST['rentPerDay'];

	// Insert data into Cars Table
	$sql = "INSERT INTO Cars (agencyId, model, licenseNumber, capacity, rentPerDay) VALUES (?, ?, ?, ?, ?)";
	$stmtExec = executePreparedStatement($conn, $sql, "issss", $userId, $model, $licenseNumber, $capacity, $rentPerDay);

	if ($stmtExec->affected_rows === 1) {
		http_response_code(201);
		$insertStatus = [
			'message' => 'New car: "' . $licenseNumber . '" recorded into Cars table successfully',
			// 'userId' => '$userId', // Include any relevant user details like user ID
			// 'token' => 'your_authentication_token_here' // Include the authentication token/session information
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
	$sql = "SELECT endDate FROM Cars WHERE BINARY licenseNumber = ?";
	$stmtExec = executePreparedStatement($conn, $sql, "s", $licenseNumber);

	if ($stmtExec->get_result()->num_rows > 0) {
		$newBookDate = new DateTime($newBookDate);
		while ($row = $stmtExec->get_result()->fetch_assoc()) {
			$oldEndDate = new DateTime($row['endDate']);
			if ($oldEndDate >= $newBookDate)
				return true;
		}
	}
	return false;
}

// Function to insert data into Bookings Table
function insertBookings($conn)
{
	// expected request path '/home/rentCar.html'

	// FIXME: 13. Check if forced logout when user is not logged in is necessary or not
	if (session_status() === PHP_SESSION_NONE)
		startSession();

	if (!isset($_SESSION['user'])) {
		require_once('logout.php');
	}

	if (isset($_POST['carId'])) { // && isset($_POST['customer_id']) && isset($_POST['start_date']) && isset($_POST['end_date'])) {
		$carId = $_POST['carId'];
		$customerId = $_SESSION['userId'];
		$startDate = $_POST['bookDate'];

		$daysBooked = $_POST['daysBooked'];
		$endDate = date('Y-m-d', strtotime($startDate . ' + ' . $daysBooked . ' days'));

		$sql = 'SELECT agencyId FROM Cars WHERE carId = ?';
		$stmtExec = executePreparedStatement($conn, $sql, "i", $carId);
		$sqlResult = $stmtExec->get_result();
		$agencyId = $sqlResult->fetch_assoc()["agencyId"];

		// Insert data into Bookings Table
		$sql = "INSERT INTO Bookings (carId, customerId, agencyId, bookDate, endDate) VALUES (?, ?, ?, ?, ?)";
		$stmtExec = executePreparedStatement($conn, $sql, "iiiss", $carId, $customerId, $agencyId, $startDate, $endDate);

		if ($stmtExec->affected_rows === 1) {
			$insertStatus = [
				'message' => 'New booking on carId: "' . $carId . '" recorded into Bookings table successfully',
				// 'user_id' => '$userId', // Include any relevant user details like user ID
				// 'token' => 'your_authentication_token_here' // Include the authentication token/session information
			];
			// TODO: Check if response needs any additional data, if nt ignore
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