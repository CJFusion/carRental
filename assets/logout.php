<?php
require_once('sessionConfig.php');

startSession();
destroySession();

http_response_code(200);
header('Location: https://' . $_SERVER['SERVER_NAME']);

if (isAjaxOrFetch())
	echo json_encode(['message' => 'Logged out successfully.']);

exit();

function isAjaxOrFetch()
{
	return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
		&& (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
			|| strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'fetch');
}

?>