<?php
function executePreparedStatement($conn, $sql, $types, ...$params) {
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
    
    
    if ($stmt->affected_rows > 0) {
        $data["stmtPreparation"] = "Successful";
        return $data;
    } else {
        $data["stmtPreparation"] = "Error executing statement: " . $stmt->error;
        return false;
    }
}
?>