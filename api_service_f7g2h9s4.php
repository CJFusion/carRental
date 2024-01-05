<?php
require_once('./assets/dbConnect.php');
require_once('./assets/sqlPrepare.php');
require_once('./assets/sessionConfig.php');

global $data;

if (strpos($_SERVER['REQUEST_URI'], '/api/logout') === 0)
	require_once('./assets/logout.php');

if (!isAjaxOrFetch()) {
	http_response_code(400);
	echo json_encode([
		'error' => 'Bad Request',
		'message' => 'Direct request detected. Use the application as intended.'
	]);
	die();
}

$case = [
	'POST' => fn() => new POST($conn),
	'GET' => fn() => new GET($conn),
];

if (!array_key_exists($_SERVER['REQUEST_METHOD'], $case)) {
	http_response_code(405);
	$data = [
		'error' => 'Method Not Allowed',
		'message' => 'No such Method "' . $_SERVER['REQUEST_METHOD'] . '" available for use.'
	];
	die();
}

$requestObj = $case[$_SERVER['REQUEST_METHOD']]();
$requestObj->runRequest();
// if(!$obj->runRequest())

// echo json_encode($getObj->getData());
echo $requestObj->getJsonData();
$conn->close();
exit();

function isAjaxOrFetch()
{
	return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
		&& (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
			|| strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'fetch');
}

function isLoggedIn($userId = null): bool
{
	startSession();
	if (!isset($_SESSION['user']))
		return false;
	if ($userId !== null && $userId !== $_SESSION['userId'])
		return false;
	return true;
}

#region POST method class
class POST
{
	private $conn;
	private $data = [];
	private $endpoint;

	public function __construct($conn)
	{
		$this->endpoint = rtrim(str_replace('/api/', '', $_SERVER['REQUEST_URI']), '/');
		$this->conn = $conn;
	}

	public function getJsonData()
	{
		return json_encode($this->data);
	}

	public function getData()
	{
		return $this->data;
	}

	public function runRequest(): bool
	{

		$segment = explode("/", $this->endpoint);

		if (!isset($segment[0])) {
			http_response_code(400);
			$this->data = [
				'error' => 'Bad Request',
				'message' => 'No such POST "' . $_SERVER['REQUEST_URI'] . '" endpoint available.'
			];
			return false;
		}


		$case = [
			'Users' => fn() => $this->postUsers(strtolower($segment[1])),
			'Cars' => fn() => $this->postCars(),
			'Bookings' => fn() => $this->postBookings()
		];

		if (!array_key_exists($segment[0], $case)) {
			http_response_code(404);
			$this->data = [
				'error' => 'Invalid endpoint',
				'message' => 'Endpont "' . $_SERVER['REQUEST_URI'] . '" does not exist.'
			];
			return false;
		}

		$this->data = $case[$segment[0]]();
		if (isset($this->data['error']))
			return false;
		return true;
	}

	private function userExists($conn, $username): bool
	{
		$sql = "SELECT userId FROM Users WHERE BINARY username = ?";
		$stmtExec = executePreparedStatement($conn, $sql, "s", $username);
		if ($stmtExec->get_result()->num_rows > 0)
			return true;
		return false;
	}

	private function initializeSessionDetails($username, $userId, $userType)
	{
		if (!isLoggedIn($userId)) {
			destroySession();
			startSession();
		}

		$_SESSION['user'] = htmlspecialchars($username);
		$_SESSION['userId'] = htmlspecialchars($userId);
		$_SESSION['userType'] = htmlspecialchars($userType);
	}

	private function carExists($conn, $licenseNumber): bool
	{
		$sql = "SELECT carId FROM Cars WHERE BINARY licenseNumber = ?";
		$stmtExec = executePreparedStatement($conn, $sql, "s", $licenseNumber);
		if ($stmtExec->get_result()->num_rows > 0)
			return true;
		return false;
	}

	private function postCars()
	{
		$conn = $this->conn;
		startSession();

		// FIXME: 12. Check if forced logout is necessary or not when user is not logged in
		if (!isLoggedIn())
			require_once('./assets/logout.php');

		$userId = $_SESSION['userId'];

		$licenseNumber = $_POST['licenseNumber'];
		if ($this->carExists($conn, $licenseNumber)) {
			http_response_code(409);
			return [
				'error' => 'Car already exists',
				'message' => 'A car with the same license number already exists. Please add a different one.'
			];
		}

		$model = $_POST['model'];
		$capacity = $_POST['capacity'];
		$rentPerDay = $_POST['rentPerDay'];

		// Insert data into Cars Table
		$sql = "INSERT INTO Cars (agencyId, model, licenseNumber, capacity, rentPerDay) VALUES (?, ?, ?, ?, ?)";
		$stmtExec = executePreparedStatement($conn, $sql, "issss", $userId, $model, $licenseNumber, $capacity, $rentPerDay);

		if ($stmtExec->affected_rows === 1) {
			http_response_code(201);
			return ['message' => 'New car: "' . $licenseNumber . '" recorded into Cars table successfully'];
		}

		http_response_code(500);
		return [
			"error" => "Car addition failed",
			"message" => "Failed query: " . $sql . " " . $conn->error
		];
	}

	private function postUsers($userType)
	{
		$conn = $this->conn;
		$username = $_POST['username'];
		if ($this->userExists($this->conn, $username)) {
			http_response_code(409);
			return [
				'error' => 'User already exists',
				'message' => 'The username is already in use. Please choose a different one.'
			];
		}

		$password = $_POST['password'];
		$email = $_POST['email'];

		$fullName = $_POST['fullName'];
		$phone = $_POST['phone'];
		$addressState = $_POST['addressState'];

		$passHash = password_hash($password, PASSWORD_DEFAULT);

		$sql = "INSERT INTO Users (username, password, email, userType) VALUES (?, ?, ?, ?)";
		$stmtExec = executePreparedStatement($conn, $sql, "ssss", $username, $passHash, $email, $userType);

		if ($stmtExec->affected_rows === 1) {
			$userId = $conn->insert_id;

			if ($userType == 'customer') {
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
				$this->initializeSessionDetails($username, $userId, $userType);

				http_response_code(201);
				return [
					'message' => 'New user: "' . $username . '" recorded into Users table successfully',
				];
			}

			http_response_code(500);
			return [
				"error" => ucfirst($userType) . " details creation failed",
				"message" => "Failed query: " . $sql . " " . $conn->error
			];
		}

		http_response_code(500);
		return [
			"error" => ucfirst($userType) . " account creation failed.",
			"message" => "Failed query: " . $sql . " " . $conn->error
		];
	}

	// Function to insert data into Cars Table

	private function bookingExists($conn, $carId, $newBookDate): DateTime|bool
	{
		$sql = "SELECT endDate FROM Bookings WHERE carId = ?";
		$stmtExec = executePreparedStatement($conn, $sql, "i", $carId);
		$sqlResult = $stmtExec->get_result();

		if ($sqlResult->num_rows > 0) {
			$newBookDate = new DateTime($newBookDate);
			while ($row = $sqlResult->fetch_assoc()) {
				$oldEndDate = new DateTime($row['endDate']);
				if ($oldEndDate >= $newBookDate)
					return $oldEndDate;
			}
		}
		return false;
	}

	// Function to insert data into Bookings Table
	private function postBookings()
	{
		$conn = $this->conn;

		// FIXME: 13. Check if forced logout is necessary or not when user is not logged in
		startSession();

		if (!isLoggedIn())
			require_once('./assets/logout.php');

		$customerId = $_SESSION['userId'];

		if (!isset($_POST['carId']) && !isset($_POST['startDate']) && !isset($_POST['endDate'])) {
			http_response_code(400);
			return [
				"error" => "Bad Request",
				"message" => "All form fields for Bookings table are required!"
			];
		}

		$carId = $_POST['carId'];
		$startDate = $_POST['bookDate'];
		$daysBooked = $_POST['daysBooked'];
		$endDate = date('Y-m-d', strtotime($startDate . ' + ' . $daysBooked . ' days'));

		if ($bookedTillDate = $this->bookingExists($conn, $carId, $startDate)) {
			http_response_code(409); // FIXME: Should this existing booking have an error assigned to it?
			return [
				"error" => "Booking conflict",
				"message" => "This car has already been booked till " . date_format($bookedTillDate, "d/m/Y")
			];
		}

		$sql = 'SELECT agencyId FROM Cars WHERE carId = ?';
		$stmtExec = executePreparedStatement($conn, $sql, "i", $carId);
		$sqlResult = $stmtExec->get_result();
		$agencyId = $sqlResult->fetch_assoc()["agencyId"];

		// Insert data into Bookings Table
		$sql = "INSERT INTO Bookings (carId, customerId, agencyId, bookDate, endDate) VALUES (?, ?, ?, ?, ?)";
		$stmtExec = executePreparedStatement($conn, $sql, "iiiss", $carId, $customerId, $agencyId, $startDate, $endDate);

		if ($stmtExec->affected_rows === 1) {
			http_response_code(201);
			return ['message' => 'New booking on carId: "' . $carId . '" recorded into Bookings table successfully'];
		}

		http_response_code(500);
		return [
			"error" => "Booking failed",
			"message" => "Failed query: " . $sql . " " . $conn->error
		];
	}
}
#endregion

#region GET method class
class GET
{
	private $conn;
	private $data = [];
	private $endpoint;

	public function __construct($conn)
	{
		$this->endpoint = rtrim(str_replace('/api/', '', $_SERVER['REQUEST_URI']), '/');
		$this->conn = $conn;
	}

	public function getJsonData()
	{
		return json_encode($this->data);
	}

	public function getData()
	{
		return $this->data;
	}

	public function runRequest(): bool
	{

		$segment = explode("/", $this->endpoint);

		if (!isset($segment[0])) {
			http_response_code(400);
			$this->data = [
				'error' => 'Bad Request',
				'message' => 'No such GET "' . $_SERVER['REQUEST_URI'] . '" endpoint available.'
			];
			return false;
		}

		$case = [
			'Cars' => fn() => $this->getCars(isset($segment[1]) ? $segment[1] : null),
			'Bookings' => fn() => $this->getBookings(isset($segment[1]) ? array_slice($segment, 1) : [null]),
			'isCustomer' => fn() => $this->isCustomer(isset($segment[1]) ? $segment[1] : null)
		];

		if (!array_key_exists($segment[0], $case)) {
			http_response_code(404);
			$this->data = [
				'error' => 'Invalid endpoint',
				'message' => 'Endpont "' . $_SERVER['REQUEST_URI'] . '" does not exist.'
			];
			return false;
		}

		$this->data = $case[$segment[0]]();
		if (isset($this->data['error']))
			return false;
		return true;
	}

	private function getCars($carId)
	{
		if ($carId === null) {
			$sql = "SELECT * from Cars";
			$sqlResult = $this->conn->query($sql);
		} else {
			$sql = "SELECT * from Cars WHERE carId = ?";
			$stmtExec = executePreparedStatement($this->conn, $sql, "i", $carId);
			$sqlResult = $stmtExec->get_result();
		}

		if ($sqlResult->num_rows > 0) {
			// Fetch each row from the result set
			while ($row = $sqlResult->fetch_assoc())
				$data['availableCars']['agencyId'][$row['agencyId']]['carId'][$row['carId']]
					= array_diff_key($row, ['agencyId' => '', 'carId' => '']);

			http_response_code(200);
			$data += ['message' => 'Car list fetched successfully.'];
			return $data;
		}

		http_response_code(404);
		return [
			"error" => "There are no cars listed in the Cars table.",
			"message" => "Failed query: " . $sql . " " . $this->conn->error
		];
	}

	private function getBookings(array $segment)
	{
		// expected path of request '/home/viewRentals.html

		// $data['url'] = $_SERVER['SERVER_NAME']; // returns with just the domain name
		// $data['url'] = $_SERVER['HTTP_HOST']; // returns domain name and port as well
		// $data['url'] = $_SERVER['HTTP_REFERER']; // returns the entire url from which request was made

		if ($segment[0] === null) {
			$sql = "SELECT * from Bookings";
			$sqlResult = $this->conn->query($sql);
		} else if ($segment[0] === "Agency") {

			if (isset($segment[1]))
				$agencyId = $segment[1];
			else {
				startSession();
				// FIXME: 10. Check if forced logout is necessary or not when user is not logged in
				if (!isLoggedIn())
					require_once('./assets/logout.php');

				$agencyId = $_SESSION['userId'];
			}

			$sql = "SELECT * from Bookings WHERE agencyId = ?";
			$stmtExec = executePreparedStatement($this->conn, $sql, "i", $agencyId);
			$sqlResult = $stmtExec->get_result();
		}

		if ($sqlResult->num_rows > 0) {
			// Fetch each row from the result set
			while ($row = $sqlResult->fetch_assoc()) {
				$sql = 'SELECT username FROM Users WHERE BINARY userId = "' . $row['customerId'] . '"';
				$nestedSqlResult = $this->conn->query($sql);
				$row['customerName'] = $nestedSqlResult->fetch_assoc()['username'];

				$sql = 'SELECT model FROM Cars WHERE BINARY carId = "' . $row['carId'] . '"';
				$nestedSqlResult = $this->conn->query($sql);

				$data['carId'][$row['carId']]['model'] = $nestedSqlResult->fetch_assoc()['model'];
				$data['carId'][$row['carId']][$row['bookingId']]
					= array_diff_key($row, ['carId' => '', 'bookingId' => '', 'agencyId' => '']);
			}

			http_response_code(200);
			$data += ['message' => 'All booked cars list' . (isset($segment[1]) ? ' under agencyId: "' . $agencyId . '"' : '') . ' fetched successfully.'];
			return $data;
		}

		http_response_code(404);
		return [
			"error" => 'There are no cars booked' . (isset($segment[1]) ? ' under agencyId: "' . $agencyId . '"' : '') . ' in the Bookings table.',
			"message" => "Failed query: " . $sql . " " . $this->conn->error
		];
	}

	private function isCustomer($userId): array|bool
	{
		// expected path of request '/home/rentCar.html'
		$conn = $this->conn;

		if ($userId === null) {
			startSession();
			// FIXME: 14. Check if forced logout is necessary or not when user is not logged in
			if (!isLoggedIn())
				require_once('./assets/logout.php');

			$userType = $_SESSION['userType'];
		} else {
			// TODO: Create getUser method if needed
			$sql = 'SELECT userType FROM Users WHERE userId = ?';
			$stmtExec = executePreparedStatement($this->conn, $sql, 'i', $userId);
			$sqlResult = $stmtExec->get_result();

			if ($sqlResult->num_rows !== 1) {
				http_response_code(404);
				return [
					"error" => "User not found",
					"message" => "No such user with userId: \"" . $userId . "\" found.\n" . ($conn->error)
				];
			}

			$userType = $sqlResult->fetch_assoc()['userType'];
		}

		http_response_code(200);
		if ($userType === 'customer')
			return ["bool" => true];

		return ["bool" => false];
	}
}
#endregion
?>