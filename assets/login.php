<?php

require_once('dbConnect.php');
require_once('sqlPrepare.php');
require_once('sessionConfig.php');

global $data;
// Get the username and password from the form
$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT userId, password, userType FROM Users WHERE BINARY username = ?";
$stmtExec = executePreparedStatement($conn, $sql, "s", $username);
$result = $stmtExec->get_result();

if ($result->num_rows > 1) {
	http_response_code(409);
	$response = [
		'error' => "Duplicate accounts",
		'message' => "More than one account exists for the same username."
	];
} else if ($result->num_rows == 1 && password_verify($password, $result->fetch_assoc()['password'])) {
	http_response_code(200);
	$response = [
		'message' => 'Login successful'
		// 'user_id' => '$userId', // Include any relevant user details like user ID
		// 'token' => 'create_authentication_token_here' // Include the authentication token/session information for skipping auth
	];

	startSession();
	if (isset($_SESSION['user']) && $_SESSION['user'] !== $username)
	// unsetSession(); // TODO: 2. Create logout function call to destroy session and then restart session
	{
		destroySession();
		startSession();
	}

	$result->data_seek(0);
	$row = $result->fetch_assoc();
	$_SESSION['user'] = htmlspecialchars($username);
	$_SESSION['userId'] = htmlspecialchars($row['userId']);
	$_SESSION['userType'] = htmlspecialchars($row['userType']);
} else {
	http_response_code(401);
	$response = [
		'error' => 'Invalid credentials',
		'message' => 'The username or password is incorrect'
	];
	
	if (session_status() === PHP_SESSION_ACTIVE) {
		session_unset();
		destroySession();
	}
}

$data += $response;
echo json_encode($data);
$conn->close();
?>