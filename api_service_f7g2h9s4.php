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

	$contr = fetchController();
	$contr->processRequest();
	echo $contr->getJsonData();
	$contr->close();
}

function fetchController()
{
	$endpoint = trim(str_replace('/api', '', $_SERVER['REQUEST_URI']), '/');
	$segment = explode("/", $endpoint);
	$name = strtolower($segment[0]);

	if (!isset($name) && $name !== '') {
		http_response_code(400);
		echo json_encode([
			'error' => 'Invalid endpoint',
			'message' => 'Endpont "' . $_SERVER['REQUEST_URI'] . '" does not exist.'
		]);
		die();
	}

	// All available controllers are added in this array
	$case = [
		'users' => fn() => new UsersController(),
		'cars' => fn() => new CarsController(),
		'bookings' => fn() => new BookingsController(),
		'images' => fn() => new ImagesController()
	];

	if (!array_key_exists(strtolower($name), $case)) {
		http_response_code(404);
		echo json_encode([
			'error' => 'Invalid endpoint',
			'message' => 'Endpont "' . $_SERVER['REQUEST_URI'] . '" does not exist.'
		]);
		die();
	}

	$file = __DIR__ . '/controllers/' . $name . '.contr.php';
	if (!file_exists($file)) {
		http_response_code(404);
		echo json_encode([
			'error' => 'Required resource',
			'message' => 'Required controller file "' . ucfirst($name) . '.php" does not exist.'
		]);
		die();
	}
	require_once($file);

	return $case[$name]();
}
?>