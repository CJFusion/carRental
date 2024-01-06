<?php

if (strpos($_SERVER['REQUEST_URI'], '/api/logout') === 0)
	require_once(__DIR__ . '/assets/logout.php');

if (strpos($_SERVER['REQUEST_URI'], '/api/login') === 0)
	require_once(__DIR__ . '/assets/login.php');

if (!isAjaxOrFetch()) {
	http_response_code(400);
	echo json_encode([
		'error' => 'Bad Request',
		'message' => 'Direct request detected. Use the application as intended.'
	]);
	die();
}

if (!isset($_SERVER['REQUEST_METHOD'])) {
	http_response_code(418);
	echo json_encode([
		'error' => "I'm a teapot",
		"message" => "The server refuses to brew coffee because it is, permanently, a teapot."
	]);
} else
	fulfilRequest();

exit;

function isAjaxOrFetch()
{
	return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
		&& (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
			|| strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'fetch');
}

function fulfilRequest()
{
	// $data['url'] = $_SERVER['SERVER_NAME']; // returns with just the domain name
	// $data['url'] = $_SERVER['HTTP_HOST']; // returns domain name and port as well
	// $data['url'] = $_SERVER['HTTP_REFERER']; // returns the entire url from which request was made

	$endpoint = trim(str_replace('/api', '', $_SERVER['REQUEST_URI']), '/');
	$segment = explode("/", $endpoint);

	if (!isset($segment[0]) && $segment[0] !== '') {
		http_response_code(400);
		echo json_encode([
			'error' => 'Invalid endpoint',
			'message' => 'Endpont "' . $_SERVER['REQUEST_URI'] . '" does not exist.'
		]);
		die();
	}

	// All available controllers are added in this array
	$case = ['Users', 'Cars', 'Bookings'];

	if (!in_array($segment[0], $case)) {
		http_response_code(404);
		echo json_encode([
			'error' => 'Invalid endpoint',
			'message' => 'Endpont "' . $_SERVER['REQUEST_URI'] . '" does not exist.'
		]);
		die();
	}

	$contr = fetchController(strtolower($segment[0]));
	$contr->processRequest();
	echo $contr->getJsonData();
	$contr->close();
}

function fetchController($name)
{
	$file = __DIR__ . '/controllers/' . $name . '.contr.php';
	if (!file_exists($file)) {
		http_response_code(404);
		echo json_encode([
			'error' => 'Required resource',
			'message' => 'Required controller file "' . $name . '.php" does not exist.'
		]);
		die();
	}
	require_once($file);

	return new Controller();
}
?>