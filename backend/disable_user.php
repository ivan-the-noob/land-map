<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;

    if (!$user_id) {
        echo json_encode(["success" => false, "error" => "User ID is missing."]);
        exit;
    }

    $query = "UPDATE users SET disable_status = disable_status + 1 WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to update status."]);
    }

    $stmt->close();
    $conn->close();
}
?>
