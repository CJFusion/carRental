<?php
declare(strict_types=1);

require_once(dirname(__DIR__) . '/assets/dbConnect.php');
require_once(dirname(__DIR__) . '/assets/sessionConfig.php');
require_once(dirname(__DIR__) . '/assets/sqlPrepare.php');

class Model
{
	private $data = [];
	private mysqli|bool $conn;

	public function __construct()
	{
		$conn = msqliConnect();
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

	private function carExists(mysqli $conn, string $licenseNumber): bool
	{
		$sql = "SELECT carId FROM Cars WHERE licenseNumber = ?";
		$stmtExec = executePreparedStatement($conn, $sql, "s", $licenseNumber);
		if ($stmtExec->get_result()->num_rows > 0)
			return true;
		return false;
	}

	public function post(): bool
	{
		$conn = $this->conn;

		// FIXME: 12. Check if forced logout is necessary or not when user is not logged in
		if (!$this->isLoggedIn())
			require_once(dirname(__DIR__) . '/assets/logout.php');
		$userId = $_SESSION['userId'];

		$licenseNumber = $_POST['licenseNumber'];
		if ($this->carExists($conn, $licenseNumber)) {
			http_response_code(409);
			$this->data += [
				'error' => 'Car already exists',
				'message' => 'A car with the same license number already exists. Please add a different one.'
			];

			return false;
		}

		$model = $_POST['model'];
		$capacity = $_POST['capacity'];
		$rentPerDay = $_POST['rentPerDay'];

		// Insert data into Cars Table
		$sql = "INSERT INTO Cars (agencyId, capacity, rentPerDay, model, licenseNumber) VALUES (?, ?, ?, ?, ?)";
		$stmtExec = executePreparedStatement($conn, $sql, "iidss", $userId, $capacity, $rentPerDay, $model, $licenseNumber);

		if ($stmtExec->affected_rows === 1) {
			http_response_code(201);
			$this->data += ['message' => 'New car: "' . $licenseNumber . '" recorded into Cars table successfully'];

			return true;
		}

		http_response_code(500);
		$this->data += [
			"error" => "Car addition failed",
			"message" => "Failed query: " . $sql . " " . $conn->error
		];

		return false;
	}

	public function get(int|string|null $carId): bool
	{
		$conn = $this->conn;

		if ($carId === null) {
			$sql = "SELECT * from Cars";
			$sqlResult = $conn->query($sql);
		} else {
			$segment = explode('/', $carId);
			if (strtolower($segment[0]) === 'licensenumber') {
				$sql = "SELECT * from Cars WHERE licenseNumber = ?";
				$stmtExec = executePreparedStatement($conn, $sql, "s", strtoupper($segment[1]));
			} else {
				$sql = "SELECT * from Cars WHERE carId = ?";
				$stmtExec = executePreparedStatement($conn, $sql, "i", $carId);
			}
			$sqlResult = $stmtExec->get_result();
		}

		if ($sqlResult->num_rows < 1) {
			http_response_code(404);
			$this->data += [
				"error" => "Failed query: " . $sql . " " . $conn->error,
				"message" => ($carId === null) ? ('There are no cars listed in the Cars table.') : ('Car with id: "' . $carId . '" not found')
			];
			return false;
		}

		while ($row = $sqlResult->fetch_assoc())
			$this->data['availableCars']['agencyId'][$row['agencyId']]['carId'][$row['carId']]
				= array_diff_key($row, ['agencyId' => '', 'carId' => '']);

		http_response_code(200);
		$this->data += ['message' => 'Cars list fetched successfully.'];
		return true;
	}
}