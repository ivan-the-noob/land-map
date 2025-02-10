<?php
require '../db.php';
session_start();

// Check if user is admin
if (!isset($_SESSION['role_type']) || $_SESSION['role_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (isset($_POST['report_id']) && isset($_POST['status'])) {
    $report_id = intval($_POST['report_id']);
    $status = $conn->real_escape_string($_POST['status']);
    
    $sql = "UPDATE reports SET status = ?, updated_at = NOW() WHERE report_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $status, $report_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
} 