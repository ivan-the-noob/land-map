<?php
session_start();
require '../db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);
$propertyId = $data['property_id'] ?? null;

if (!$propertyId) {
    echo json_encode(['success' => false, 'message' => 'Property ID is required']);
    exit;
}

try {
    // Update the property status to archived
    $sql = "UPDATE properties SET status = 'archived' WHERE property_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $propertyId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}

$conn->close();
?>