<?php

function startSession()
{
	ini_set("session.use_only_cookies", 1);
	ini_set("session.use_strict_mode", 1);

	session_set_cookie_params([
		'lifetime' => 1800,
		'domain' => $_SERVER['SERVER_NAME'],
		'path' => '/',
		'secure' => true,
		'httponly' => true,
		'samesite' => 'Strict',
	]);

	session_start();
	regenerateSession();
}

function regenerateSession()
{
	if (!isset($_SESSION['lastRegeneration'])) {
		regenerateSessionId();
	} else {
		$interval = 60 * 30;
		if (time() - $_SESSION['lastRegeneration'] >= $interval) {
			regenerateSessionId();
		}
	}
}

function regenerateSessionId()
{
	session_regenerate_id(true);
	$_SESSION['lastRegeneration'] = time();
}

function destroySession()
{
	session_unset();
	session_destroy();
}

function unsetSession()
{
	$temp = $_SESSION['lastRegeneration'];
	session_unset();
	$_SESSION['lastRegeneration'] = $temp;
}
?>