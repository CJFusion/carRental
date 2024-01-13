<?php

function mysqliConnect()
{
	$servername = "localhost";
	$username = "id21674326_marakrentals";
	$password = "rentals@Y23";
	$database = "id21674326_rentalservices";

	return mysqli_connect($servername, $username, $password, $database);
}

$conn = mysqliConnect();
// Check connection
if (!$conn) {
	$response = "Connection failed";
}
$response = "Connection successful";

$GLOBALS['data'] = ["connStatus" => $response];
unset($response);
?>