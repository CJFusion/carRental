<?php
require_once(__DIR__ . '/sessionConfig.php');
require_once(dirname(__DIR__) . '/controllers/users.contr.php');

// Get the username and password from the form
$username = $_POST['username'];
$password = $_POST['password'];

$contr = new UsersController();
$contr->setMethod('GET');
$_POST['requireCode'] = true;
$contr->setEndpoint('/api/Users/ByUsername/' . htmlspecialchars(trim($username)));
$foundUser = $contr->processRequest();
if ($foundUser) {
	$data = $contr->getData();
	$row['userId'] = array_key_first($data['userId']);
	$row = array_merge($row, $data['userId'][$row['userId']]);
}

unset($_POST['requireCode']);
$contr->close();

if ($foundUser && password_verify($password, $row['password'])) {

	startSession();
	if (isset($_SESSION['user']) && $_SESSION['user'] !== $username) {
		destroySession();
		startSession();
	}

	$_SESSION['user'] = htmlspecialchars(trim($username));
	$_SESSION['userId'] = htmlspecialchars(trim($row['userId']));
	$_SESSION['userType'] = htmlspecialchars(trim($row['userType']));

	http_response_code(200);
	echo json_encode(['message' => 'Login successful']);
	exit;
}

http_response_code(401);
echo json_encode([
	'error' => 'Invalid credentials',
	'message' => 'The username or password is incorrect'
]);

if (session_status() === PHP_SESSION_ACTIVE)
	destroySession();

exit;
?>