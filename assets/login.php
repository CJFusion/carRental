<?php

include 'dbConnect.php';
// include 'sqlPrepare.php';

// Get the username and password from the form
$username = $_POST['username'];
$password = $_POST['password'];

// $data['passHash'] = password_hash("user", PASSWORD_DEFAULT);
// // $data['verification'] = password_verify("user", '$2y$10$vwIOk3GV5yqlonugSY/lv.9K4nxvxXkLjY.mjuR.3fqxheIdVWHQW');
// echo json_encode($data);
// exit();


// $sql = "INSERT INTO Users (username, password, email) VALUES (?, ?, ?)";
// $types = "sss"; // Assuming all parameters are strings

// $username = "user32123";
// $password = "password32123";
// $email = "musers@example.com";

// // Call the function with the required parameters
// $result = executePreparedStatement($conn, $sql, $types, $username, $password, $email);

// // if ($result) {
// //     $response = "Data inserted successfully.";
// // } else {
// //     $response = "Error inserting data.";
// // }

// $data['results'] = $result->affected_rows;
// echo json_encode($data);
// exit();

$sql = "SELECT password FROM Users WHERE BINARY username = ?";
$sqlStmt = $conn->prepare($sql);
$sqlStmt->bind_param("s", $username);
$sqlStmt->execute();
$result = $sqlStmt->get_result();

if ($result->num_rows > 1) {
    $response = "Double accounts";
} 
else if ($result->num_rows == 1 && password_verify($password, $result->fetch_assoc()['password'])) {
    $response = "Login successful";
} 
else {
    $response = "Invalid username or password";
}

$data["loginStatus"] = $response;
echo json_encode($data);
$conn->close();
?>