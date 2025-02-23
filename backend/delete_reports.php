<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_id = $_POST['report_id'] ?? null;

    if (!$report_id) {
        echo json_encode(["success" => false, "error" => "Report ID is missing."]);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Check if the report exists
        $check_report = "SELECT id FROM reports WHERE id = ?";
        $stmt_check = $conn->prepare($check_report);
        $stmt_check->bind_param("i", $report_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $report = $result_check->fetch_assoc();
        $stmt_check->close();

        if (!$report) {
            echo json_encode(["success" => false, "error" => "Report not found."]);
            exit;
        }

        // Delete the report
        $delete_query = "DELETE FROM reports WHERE id = ?";
        $stmt_delete = $conn->prepare($delete_query);
        $stmt_delete->bind_param("i", $report_id);
        $stmt_delete->execute();
        $stmt_delete->close();

        // Commit transaction
        $conn->commit();

        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }

    $conn->close();
}
?>
