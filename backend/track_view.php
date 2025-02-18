<?php
session_start();
require '../db.php'; // Adjust the path based on your structure

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_id'])) {
    $user_id = $_SESSION['user_id'];
    $property_id = intval($_POST['property_id']);
    $created_at = date('Y-m-d H:i:s');

    if (!$property_id) {
        echo json_encode(["error" => "Invalid property ID"]);
        exit;
    }

    try {
        // Check if the view entry already exists
        $check_sql = "SELECT id FROM views WHERE user_id = ? AND property_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $user_id, $property_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows === 0) {
            // Insert the new view record
            $insert_sql = "INSERT INTO views (user_id, property_id, created_at) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("iis", $user_id, $property_id, $created_at);
            $insert_stmt->execute();
        }

        echo json_encode(["success" => "View recorded"]);
    } catch (Exception $e) {
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Invalid request"]);
}

?>
