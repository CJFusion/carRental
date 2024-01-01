<?php
function executePreparedStatement($conn, $sql, $types, ...$params)
{
	global $data;
	$stmt = $conn->prepare($sql);

	if (!$stmt) {
		$data["stmtPreparation"] = "Error preparing statement: " . $conn->error;
		return false;
	}

	$bindParams = array_merge([$types], $params);

	$bindParamsRefs = [];
	foreach ($bindParams as $key => $value) {
		$bindParamsRefs[$key] = &$bindParams[$key];
	}

	call_user_func_array([$stmt, 'bind_param'], $bindParamsRefs);

	$stmt->execute();

	return $stmt;

}




function executePreparedStatement_placeholder($conn, $sql, $params)
{
	global $data;
	$stmt = $conn->prepare($sql);

	if (!$stmt) {
		$data["stmtPreparation"] = "Error preparing statement: " . $conn->error;
		return false;
	}

	foreach ($params as $key => &$value) {
		$stmt->bindParam($key, $value);
	}

	$stmt->execute();

	return $stmt;
}


// Sample data for placeholders

// $keys = array(':username', ':password', ':email');
// $values = array('john_doe', 'password123', 'john@example.com');
// $params = array_combine($keys, $values);

// // Prepare the SQL statement
// $sql = "INSERT INTO Users (" . implode(', ', array_map(function($key) { return substr($key, 1); }, $keys)) . ")
//         VALUES (" . implode(', ', $keys) . ")";

// // Execute prepared statement with placeholders and data
// $result = executePreparedStatement_placeholder($conn, $sql, $params);

// if ($result) {
// 	echo "Statement executed successfully!";
// } else {
// 	echo "Error executing statement.";
// }


?>