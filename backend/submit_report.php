<?php
session_start();
include '../db.php';

header('Content-Type: application/json'); // Ensure JSON response

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "error" => "User not logged in."]);
        exit;
    }

    $user_id = $_SESSION['user_id']; // User reporting
    $inquirer_id = isset($_POST['inquirer_id']) ? intval($_POST['inquirer_id']) : 0;
    $report_reason = isset($_POST['report_reason']) ? trim($_POST['report_reason']) : '';

    if (empty($inquirer_id)) {
        echo json_encode(["success" => false, "error" => "Inquirer ID is missing."]);
        exit;
    }

    if (empty($report_reason)) {
        echo json_encode(["success" => false, "error" => "Report reason is required."]);
        exit;
    }

    // Fetch inquirer's user_id from users table
    $query = "SELECT user_id FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "SQL Prepare Error: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $inquirer_id);
    $stmt->execute();
    $stmt->bind_result($report_to);
    $stmt->fetch();
    $stmt->close();

    if (!$report_to) {
        echo json_encode(["success" => false, "error" => "Inquirer not found in the database."]);
        exit;
    }

    // Insert the report into the reports table
    $stmt = $conn->prepare("INSERT INTO reports (user_id, agent_id, report_reason, report_to, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iisi", $inquirer_id, $agent_id, $report_reason, $inquirer_id);
    

    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "SQL Prepare Error (Insert): " . $conn->error]);
        exit;
    }

    $stmt->bind_param("iisi", $user_id, $inquirer_id, $report_reason, $report_to);

    if ($stmt->execute()) {
        // Update report_status in users table
        $update_stmt = $conn->prepare("UPDATE users SET report_status = 1 WHERE user_id = ?");
        if ($update_stmt) {
            $update_stmt->bind_param("i", $report_to);
            $update_stmt->execute();
            $update_stmt->close();
        } else {
            echo json_encode(["success" => false, "error" => "SQL Prepare Error (Update): " . $conn->error]);
            exit;
        }

        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "SQL Execution Error: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
