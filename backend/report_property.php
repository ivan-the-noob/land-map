<?php
session_start();
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "error" => "User not logged in."]);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $property_id = isset($_POST['property_id']) ? intval($_POST['property_id']) : 0;
    $report_reason = isset($_POST['report_reason']) ? trim($_POST['report_reason']) : '';

    if (empty($property_id)) {
        echo json_encode(["success" => false, "error" => "Property ID is missing."]);
        exit;
    }

    if (empty($report_reason)) {
        echo json_encode(["success" => false, "error" => "Report reason is required."]);
        exit;
    }

    // Insert the report into the report_properties table
    $stmt = $conn->prepare("INSERT INTO report_properties (user_id, property_id, report_reason, created_at) VALUES (?, ?, ?, NOW())");

    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "SQL Prepare Error: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("iis", $user_id, $property_id, $report_reason);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "SQL Execution Error: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
