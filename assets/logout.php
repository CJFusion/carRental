<?php
require_once(__DIR__ . '/sessionConfig.php');

startSession();
destroySession();

http_response_code(200);
header('Location: https://' . $_SERVER['HTTP_HOST'] . '/home/login.html');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'fetch'))
	echo json_encode(['message' => 'Logged out successfully.']);

exit();
?>