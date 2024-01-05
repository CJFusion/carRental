<?php
function executePreparedStatement($conn, $sql, ...$params)
{
	global $data;
	$params = array_map(function($element) {return is_string($element) ? trim($element) : $element;
	}, $params);
	$stmt = $conn->prepare($sql);

	if (!$stmt) {
		$data["error"] = "Error preparing statement";
		$data["message"] = $conn->error;
		return false;
	}

    $bindMethod = $stmt->bind_param(...$params);
    if (!$bindMethod) {
		$data["error"] = "Error binding parameters";
		$data["message"] = $stmt->error;
        return false;
    }

	$stmt->execute();

	return $stmt;
}
?>