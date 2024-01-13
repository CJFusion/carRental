<?php
declare(strict_types=1);

require_once(dirname(__DIR__) . '/assets/dbConnect.php');
require_once(dirname(__DIR__) . '/assets/sessionConfig.php');
require_once(dirname(__DIR__) . '/assets/sqlPrepare.php');

class BookingsModel
{
	private array $data = [];
	private mysqli|bool $conn;

	public function __construct()
	{
		$conn = mysqliConnect();
		if (!$conn) {
			http_response_code(500);
			echo json_encode(["error" => "MySql connection failed", "message" => $conn->error]);
			die();
		}
		$this->data['connStatus'] = "MySql connection successful";
		$this->conn = $conn;
	}

	public function getData(): array
	{
		return $this->data;
	}

	public function close(): void
	{
		$this->conn->close();
	}

	private function isLoggedIn(int $userId = null): bool
	{
		startSession();
		if (!isset($_SESSION['user']))
			return false;
		if ($userId !== null && $userId !== $_SESSION['userId'])
			return false;
		return true;
	}

	private function bookingExists(mysqli $conn, int $carId, string $newBookDate): DateTime|bool
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

	// 	Supports the following endpoints:
	// 		/api/Bookings			=> To post a booking uner current logged in user
	public function post(): bool
	{
		$conn = $this->conn;

		// FIXME: 13. Check if forced logout is necessary or not when user is not logged in
		if (!$this->isLoggedIn())
			require_once(dirname(__DIR__) . '/assets/logout.php');

		$customerId = $_SESSION['userId'];

		if (!isset($_POST['carId']) && !isset($_POST['startDate']) && !isset($_POST['endDate'])) {
			http_response_code(400);
			$this->data["error"] = "Bad Request";
			$this->data["message"] = "All form fields for Bookings table are required!";

			return false;
		}

		$carId = (int) $_POST['carId'];
		$startDate = $_POST['bookDate'];
		$daysBooked = $_POST['daysBooked'];
		$endDate = date('Y-m-d', strtotime($startDate . ' + ' . $daysBooked . ' days'));

		if ($startDate <= date('Y-m-d')) {
			http_response_code(400); // FIXME: Should this inaccurate booking have an error assigned to it?
			$this->data["error"] = "Bad request";
			$this->data["message"] = "Booking for today or a past date is not possible.";

			return false;
		}

		if ($bookedTillDate = $this->bookingExists($conn, $carId, $startDate)) {
			http_response_code(409); // FIXME: Should this existing booking have an error assigned to it?
			$this->data["error"] = "Booking conflict";
			$this->data["message"] = "This car has already been booked till " . date_format($bookedTillDate, "d/m/Y");

			return false;
		}

		$sql = 'SELECT agencyId, licenseNumber FROM Cars WHERE carId = ?';
		$stmtExec = executePreparedStatement($conn, $sql, "i", $carId);
		$sqlResult = $stmtExec->get_result();
		$row = $sqlResult->fetch_assoc();
		$agencyId = $row["agencyId"];
		$licenseNumber = $row["licenseNumber"];

		// Insert data into Bookings Table
		$sql = "INSERT INTO Bookings (carId, customerId, agencyId, bookDate, endDate) VALUES (?, ?, ?, ?, ?)";
		$stmtExec = executePreparedStatement($conn, $sql, "iiiss", $carId, $customerId, $agencyId, $startDate, $endDate);

		if ($stmtExec->affected_rows === 1) {
			http_response_code(201);
			$this->data['message'] = "New booking on carId: \"$carId\" with license number: \"$licenseNumber\" successful";

			return true;
		}

		http_response_code(500);
		$this->data["error"] = "Failed query: " . $sql . " " . $conn->error;
		$this->data["message"] = "Booking failed";

		return false;
	}

	// 	Supports the following endpoints:
	// 		/api/Bookings			=> To get all bookings
	// 		/api/Bookings/{int}		=> To get a booking by its bookingsId
	// 		/api/Bookings/Agency/{int}		=> To get all bookings made under agencyId 
	// 		/api/Bookings/Customer/{int}	=> To get all bookings made under customerId
	public function get(int|string|null $bookingId): bool
	{
		$conn = $this->conn;

		if ($bookingId === null) {
			$sql = "SELECT * from Bookings";
			$sqlResult = $conn->query($sql);
		} else {
			$segment = explode('/', $bookingId);
			$index = 0;
			$searchField = 'bookingId';

			if (strtolower($segment[0]) === 'agency' || strtolower($segment[0]) === 'customer') {
				if (!isset($segment[1])) {
					// FIXME: 10. Check if forced logout is necessary or not when user is not logged in
					if (!$this->isLoggedIn())
						require_once(dirname(__DIR__) . '/assets/logout.php');
					$segment[1] = $_SESSION['userId'];
				}

				$index = 1;
				$searchField = $segment[0] . 'Id';
			}

			$sql = "SELECT * from Bookings WHERE " . $searchField . " = ?";
			$stmtExec = executePreparedStatement($conn, $sql, "i", $segment[$index]);
			$sqlResult = $stmtExec->get_result();
		}

		if ($sqlResult->num_rows > 0) {
			// Fetch each row from the result set
			while ($row = $sqlResult->fetch_assoc()) {
				$sql = 'SELECT username FROM Users WHERE BINARY userId = "' . $row['customerId'] . '"';
				$nestedSqlResult = $conn->query($sql);
				$row['customerName'] = $nestedSqlResult->fetch_assoc()['username'];

				$sql = 'SELECT model FROM Cars WHERE BINARY carId = "' . $row['carId'] . '"';
				$nestedSqlResult = $conn->query($sql);

				$this->data['carId'][$row['carId']]['model'] = $nestedSqlResult->fetch_assoc()['model'];
				$this->data['carId'][$row['carId']][$row['bookingId']]
					= array_diff_key($row, ['carId' => '', 'bookingId' => '', 'agencyId' => '']);
			}

			http_response_code(200);
			$this->data['message'] = 'All booked cars list' . (isset($segment[1]) ? (' under ' . $segment[0] . 'Id: "' . $segment[1] . '"') : '') . ' fetched successfully.';

			return true;
		}

		http_response_code(404);
		$this->data["error"] = "Failed query: " . $sql . " " . $conn->error;
		$this->data["message"] = 'There are no cars booked' . (isset($segment[1]) ? (' under ' . $segment[0] . 'Id: "' . $segment[1] . '"') : '') . ' in the Bookings table.';

		return false;
	}
}