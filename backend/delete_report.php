<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_id = $_POST['report_id'] ?? null;

    if (!$report_id) {
        echo json_encode(["success" => false, "error" => "Report ID is missing."]);
        exit;
    }

    $query = "DELETE FROM reports WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $report_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to delete report."]);
    }

    $stmt->close();
    $conn->close();
}
?>
