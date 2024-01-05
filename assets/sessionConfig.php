<?php

// Set session configuration using ini_set
ini_set("session.use_only_cookies", 1);
ini_set("session.use_strict_mode", 1);

// Function to start or resume a session
function startSession()
{
	// Check if the session is not already active
	if (session_status() === PHP_SESSION_NONE) {

		// Set strict cookie parameters
		session_set_cookie_params([
			'lifetime' => 1800,
			'domain' => $_SERVER['SERVER_NAME'],
			'path' => '/',
			'secure' => true,
			'httponly' => true,
			'samesite' => 'Strict',
		]);

		session_start();
	}

	// Regenerate session ID if needed
	regenerateSession();
}

// Function to regenerate the session ID periodically
function regenerateSession()
{
	if (!isset($_SESSION['lastRegeneration'])) {
		regenerateSessionId();
	} else {
		$interval = 60 * 30; // 30 minutes
		if (time() - $_SESSION['lastRegeneration'] >= $interval) {
			regenerateSessionId();
		}
	}
}

// Function to regenerate the session ID
function regenerateSessionId()
{
	session_regenerate_id(true);
	$_SESSION['lastRegeneration'] = time();
}

// Function to destroy the session
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