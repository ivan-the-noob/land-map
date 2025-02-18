<?php
require '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inquiry_id'])) {
    $inquiry_id = intval($_POST['inquiry_id']);

    if (!$inquiry_id) {
        echo "error: invalid inquiry ID";
        exit;
    }

    // Check if inquiry exists
    $check_sql = "SELECT * FROM inquire WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $inquiry_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows === 0) {
        echo "error: no matching inquiry found";
        exit;
    }

    $conn->begin_transaction();

    try {
        // Update the inquiry status instead of deleting
        $sql_inquire = "UPDATE inquire SET status = 'cancelled' WHERE id = ?";
        $stmt_inquire = $conn->prepare($sql_inquire);
        $stmt_inquire->bind_param("i", $inquiry_id);
        $stmt_inquire->execute();

        // Optional: Delete related messages (if required)
        $sql_messages = "DELETE FROM messages WHERE property_id = ?";
        $stmt_messages = $conn->prepare($sql_messages);
        $stmt_messages->bind_param("i", $property_id);
        $stmt_messages->execute();

        $conn->commit();
        echo "success";
    } catch (Exception $e) {
        $conn->rollback();
        echo "error: update failed";
    }
}
?>
