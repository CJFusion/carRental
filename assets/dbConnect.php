<?php

$servername = "localhost";
$username = "id21674326_marakrentals";
$password = "rentals@Y23";
$database = "id21674326_rentalservices";

$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    $response = "Connection failed";
}

$response = "Connection successful";

$data["connStatus"] = $response;
unset($response);

?>