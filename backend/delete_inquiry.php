<?php
require '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inquiry_id'], $_POST['cancel_reason'])) {
    $inquiry_id = intval($_POST['inquiry_id']);
    $cancel_reason = trim($_POST['cancel_reason']);

    if (!$inquiry_id || empty($cancel_reason)) {
        echo "error: Invalid inquiry ID or missing cancel reason.";
        exit;
    }

    // Check if inquiry exists
    $check_sql = "SELECT * FROM inquire WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $inquiry_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows === 0) {
        echo "error: No matching inquiry found.";
        exit;
    }

    $conn->begin_transaction();

    try {
        // Update the inquiry status and add cancel reason
        $sql_inquire = "UPDATE inquire SET status = 'cancelled', cancel_reason = ? WHERE id = ?";
        $stmt_inquire = $conn->prepare($sql_inquire);
        $stmt_inquire->bind_param("si", $cancel_reason, $inquiry_id);
        $stmt_inquire->execute();

        $conn->commit();
        echo "success";
    } catch (Exception $e) {
        $conn->rollback();
        echo "error: update failed";
    }
}
?>
