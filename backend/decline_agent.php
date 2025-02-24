<?php
require '../db.php';

// Set response type to JSON
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "error" => "Invalid request method."]);
    exit;
}

// Validate user_id
if (!isset($_POST['user_id']) || empty($_POST['user_id'])) {
    echo json_encode(["success" => false, "error" => "User ID is required."]);
    exit;
}

$userId = intval($_POST['user_id']); // Ensure it's an integer

// Prepare and execute delete query
$deleteQuery = "DELETE FROM users WHERE user_id = ?";
$stmt = $conn->prepare($deleteQuery);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "SQL error: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Execution error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
