<?php
declare(strict_types=1);

require_once(dirname(__DIR__) . '/assets/dbConnect.php');
require_once(dirname(__DIR__) . '/assets/sessionConfig.php');
require_once(dirname(__DIR__) . '/assets/sqlPrepare.php');

class UsersModel
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

	public function setConn(mysqli $conn): void
	{
		$this->conn->close();
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

	private function usernameExists(mysqli $conn, string $username): bool
	{
		$sql = "SELECT userId FROM Users WHERE BINARY username = ?";
		$stmtExec = executePreparedStatement($conn, $sql, "s", $username);
		if ($stmtExec->get_result()->num_rows > 0)
			return true;
		return false;
	}

	private function initializeSessionDetails(string $username, int $userId, string $userType): void
	{
		if (!$this->isLoggedIn($userId)) {
			destroySession();
			startSession();
		}

		$_SESSION['user'] = htmlspecialchars(trim($username));
		$_SESSION['userId'] = $userId;
		$_SESSION['userType'] = htmlspecialchars(trim($userType));
	}

	// 	Supports the following endpoints:
	// 		/api/Users/Agency	=> To post a single user under Agency category
	// 		/api/Users/Customer	=> To post a single user under Customer category
	public function post(string $userType): bool
	{
		$conn = $this->conn;
		$username = $_POST['username'];
		if ($this->usernameExists($conn, $username)) {
			http_response_code(409);
			$this->data['error'] = 'User already exists';
			$this->data['message'] = 'The username is already in use. Please choose a different one.';

			return false;
		}

		$password = $_POST['password'];
		$email = $_POST['email'];

		$fullName = $_POST['fullName'];
		$phone = $_POST['phone'];
		$addressState = $_POST['addressState'];

		$passHash = password_hash($password, PASSWORD_DEFAULT);
		
		$conn->begin_transaction();
		$sql = "INSERT INTO Users (username, password, email, userType) VALUES (?, ?, ?, ?)";
		$stmtExec = executePreparedStatement($conn, $sql, "ssss", $username, $passHash, $email, $userType);

		if ($stmtExec->affected_rows !== 1) {
			$conn->rollback();

			http_response_code(500);
			$this->data["error"] = "Failed query: " . $sql . " " . $conn->error;
			$this->data["message"] = ucfirst($userType) . " account creation failed.";

			return false;
		}

		$userId = $conn->insert_id;

		if (strtolower($userType) == 'customer') {
			$dob = $_POST['dob'];
			$gender = $_POST['gender'];

			$sql = "INSERT INTO UserDetails(customerId, phone, fullName, dob, gender, addressState) 
                VALUES (?, ?, ?, ?, ?, ?)";
			$stmtExec = executePreparedStatement($conn, $sql, "iissss", $userId, $phone, $fullName, $dob, $gender, $addressState);
		} else {
			$agencyName = $_POST["agencyName"];

			$sql = "INSERT INTO AgencyDetails(agencyId, phone, agencyName, fullName, addressState) 
                VALUES (?, ?, ?, ?, ?)";
			$stmtExec = executePreparedStatement($conn, $sql, "iisss", $userId, $phone, $agencyName, $fullName, $addressState);
		}

		if ($stmtExec->affected_rows !== 1) {
			$conn->rollback();

			http_response_code(500);
			$this->data["error"] = "Failed query: " . $sql . " " . $conn->error;
			$this->data["message"] = ucfirst($userType) . " details creation failed";

			return false;
		}

		$conn->commit();
		$this->initializeSessionDetails($username, $userId, $userType);

		http_response_code(201);
		$this->data['message'] = 'New user: "' . $username . '" created successfully';

		return true;
	}


	private function getAllUsers($conn): bool
	{
		$customerSql = "SELECT * FROM Users INNER JOIN UserDetails ON Users.userId = UserDetails.customerId";
		$customerSqlResult = $conn->query($customerSql);

		$agencySql = "SELECT * FROM Users INNER JOIN AgencyDetails ON Users.userId = AgencyDetails.agencyId;";
		$agencySqlResult = $conn->query($agencySql);

		if ($customerSqlResult->num_rows < 1 && $agencySqlResult->num_rows < 1) {
			http_response_code(404);
			$this->data["error"] = "Failed queries: $customerSql \n\t $agencySql \n " . $conn->error;
			$this->data["message"] = 'There are no users reqistered';
			return false;
		}

		if ($customerSqlResult->num_rows > 0)
			if (isset($_POST['requireCode']) && $_POST['requireCode'])
				while ($row = $customerSqlResult->fetch_assoc())
					$this->data['customerId'][$row['userId']] = array_intersect_key($row, ['username' => '', 'password' => '', 'userType' => '']);
			else
				while ($row = $customerSqlResult->fetch_assoc())
					$this->data['customerId'][$row['userId']] = array_diff_key($row, ['userId' => '', 'password' => '', 'agencyId' => '', 'customerId' => '']);

		if ($agencySqlResult->num_rows > 0)
			if (isset($_POST['requireCode']) && $_POST['requireCode'])
				while ($row = $agencySqlResult->fetch_assoc())
					$this->data['agencyId'][$row['userId']] = array_intersect_key($row, ['username' => '', 'password' => '', 'userType' => '']);
			else
				while ($row = $agencySqlResult->fetch_assoc())
					$this->data['agencyId'][$row['userId']] = array_diff_key($row, ['userId' => '', 'password' => '', 'agencyId' => '', 'customerId' => '']);

		unset($_POST['requireCode']);

		http_response_code(200);
		$this->data['message'] = 'Fetched all users data successfully';
		return true;
	}

	// 	Supports the following endpoints:
	// 		/api/Users			=> To get all users
	// 		/api/Users/0		=> To get a single user who's currently logged in
	// 		/api/Users/{int}	=> To get a single user by userId
	// 		/api/Users/ByUsername				=> To get current logged in user by their username
	// 		/api/Users/ByUsername/{string}		=> To get a user by their username
	public function get(int|string|null $userId): bool
	{
		$conn = $this->conn;

		if ($userId === null)
			return $this->getAllUsers($conn);

		$sql = "SELECT * FROM Users WHERE userId = ?";
		$types = "i";

		$case = [
			'byusername' =>
				function (&$userId, &$sql, &$types) {
					$sql = "SELECT * FROM Users WHERE BINARY username = ?";
					$types = "s";

					$username = explode('/', $userId)[1] ?? '';
					if (strlen((string) $username) > 0) {
						$userId = $username;
						return;
					}

					if (!$this->isLoggedIn())
						require_once(dirname(__DIR__) . '/assets/logout.php');
					$userId = $_SESSION['user'];
				},

			'0' =>
				function (&$userId) {
					if (!$this->isLoggedIn())
						require_once(dirname(__DIR__) . '/assets/logout.php');
					$userId = $_SESSION['userId'];
				}
		];

		$segment = explode('/', trim(strtolower($userId), '/'))[0];
		if (array_key_exists($segment, $case))
			$case[$segment]($userId, $sql, $types);

		$stmtExec = executePreparedStatement($conn, $sql, $types, $userId);
		$sqlResult = $stmtExec->get_result();

		if ($sqlResult->num_rows > 0) {
			if (isset($_POST['requireCode']) && $_POST['requireCode'])
				while ($row = $sqlResult->fetch_assoc())
					$this->data['userId'][$row['userId']] = array_intersect_key($row, ['username' => '', 'password' => '', 'userType' => '']);
			else
				while ($row = $sqlResult->fetch_assoc())
					$this->data['userId'][$row['userId']] = array_diff_key($row, ['userId' => '', 'password' => '', 'agencyId' => '', 'customerId' => '']);

			unset($_POST['requireCode']);

			// The above loops would only execute once, as the Users table is expected to have only one record for each userId, its redundant
			$userId = array_key_first($this->data['userId']);
			$userType = $this->data['userId'][$userId]['userType'];
			$table = ($userType == "customer") ? "User" : "Agency";
			$sql = "SELECT * FROM " . $table . "Details WHERE " . $userType . "Id = \"$userId\"";
			$sqlResult = $conn->query($sql);

			$this->data['userId'][$userId] = array_merge($this->data['userId'][$userId], array_diff_key($sqlResult->fetch_assoc(), ['customerId' => '']));

			http_response_code(200);
			$this->data['message'] = "Fetched user's data successfully";
			return true;
		}

		http_response_code(404);
		$this->data["error"] = "Failed query: " . $sql . " " . $conn->error;
		$this->data["message"] = 'User with name/id: "' . $userId . '" not found';
		return false;
	}
}
?>