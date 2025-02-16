<?php
session_start();
require_once '../db.php'; // Adjust the path as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "error" => "User not logged in."]);
        exit;
    }

    $user_id = $_POST['inquirer_id'] ?? null; // User being reported
    $report_reason = $_POST['report_reason'] ?? null;
    $agent_id = $_SESSION['user_id']; // The agent reporting
    $created_at = date("Y-m-d H:i:s");

    if (empty($user_id)) {
        echo json_encode(["success" => false, "error" => "User ID (inquirer) is missing."]);
        exit;
    }
    if (empty($report_reason)) {
        echo json_encode(["success" => false, "error" => "Report reason is required."]);
        exit;
    }

    // Insert report into the database
    $query = "INSERT INTO reports (user_id, agent_id, report_reason, created_at, report_to) 
              VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iissi", $user_id, $agent_id, $report_reason, $created_at, $user_id);

    if ($stmt->execute()) {
        // Update report_status_agent in the users table
        $updateQuery = "UPDATE users SET report_status_agent = 1 WHERE user_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("i", $user_id);

        if ($updateStmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to update report status.", "db_error" => $updateStmt->error]);
        }

        $updateStmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Failed to submit report.", "db_error" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request."]);
}
?>
