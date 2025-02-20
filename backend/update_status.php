<?php
require '../db.php';
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
ob_start(); // Start output buffering

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method.");
    }

    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not authenticated.");
    }

    $user_id = $_SESSION['user_id'];

    // Check if user exists
    $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
    if (!$check_stmt) {
        throw new Exception("SQL error in checking user existence: " . $conn->error);
    }
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows === 0) {
        throw new Exception("User not found in the database.");
    }

    // Update information_status
    $stmt = $conn->prepare("UPDATE users SET information_status = 1, updated_time = NOW() WHERE user_id = ?");
    if (!$stmt) {
        throw new Exception("SQL error in update query: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = "Information status updated successfully.";
        } else {
            throw new Exception("No changes were made. Status was already set to 1.");
        }
    } else {
        throw new Exception("Failed to update status: " . $stmt->error);
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

ob_end_clean(); // Prevent unexpected output before JSON response
echo json_encode($response);
exit;
?>
