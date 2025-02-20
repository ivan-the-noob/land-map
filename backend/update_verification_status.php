<?php
require '../db.php';
session_start();

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

    $admin_id = $_SESSION['user_id'];
    $user_id = $_POST['user_id'] ?? null;
    $status = $_POST['status'] ?? null;

    if (!$user_id || !in_array($status, [1, 3])) {
        throw new Exception("Invalid request parameters.");
    }

    $stmt = $conn->prepare("UPDATE users SET information_status = ?, updated_time = NOW() WHERE user_id = ?");
    if (!$stmt) {
        throw new Exception("SQL error: " . $conn->error);
    }

    $stmt->bind_param("ii", $status, $user_id);
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "User status updated.";
    } else {
        throw new Exception("Failed to update status.");
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Ensure no extra output before JSON response
ob_end_clean();
echo json_encode($response);
exit;
?>
