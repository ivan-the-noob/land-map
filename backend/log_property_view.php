<?php
session_start();
require '../db.php';

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);
$response = ['success' => false];

// Check if user is logged in and property_id is provided
if (isset($_SESSION['user_id']) && isset($data['property_id'])) {
    $userId = $_SESSION['user_id'];
    $propertyId = $data['property_id'];
    
    try {
        // First check if this view already exists for today
        $checkSql = "SELECT view_id FROM property_views 
                    WHERE property_id = ? 
                    AND viewer_id = ? 
                    AND DATE(viewed_at) = CURDATE()";
        
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ii", $propertyId, $userId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        // If no view exists for today, insert new view
        if ($result->num_rows === 0) {
            $insertSql = "INSERT INTO property_views (property_id, viewer_id) VALUES (?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("ii", $propertyId, $userId);
            
            if ($insertStmt->execute()) {
                $response['success'] = true;
            }
        } else {
            // View already exists for today, still return success
            $response['success'] = true;
            $response['message'] = 'View already logged today';
        }
    } catch (Exception $e) {
        $response['error'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['error'] = 'Invalid request or user not logged in';
}

header('Content-Type: application/json');
echo json_encode($response); 