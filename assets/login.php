<?php

include 'dbConnect.php';
include 'sqlPrepare.php';

// Get the username and password from the form
$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT password FROM Users WHERE BINARY username = ?";
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
		'message' => 'Login successful',
		'user_id' => '$userId', // Include any relevant user details like user ID
		'token' => 'your_authentication_token_here' // Include the authentication token/session information
	];
} else {
	http_response_code(401);
	$response = [
		'error' => 'Invalid credentials',
		'message' => 'The username or password is incorrect'
	];
}

$data += $response;
echo json_encode($data);
$conn->close();
?>