<?php
require '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_id'])) {
    $property_id = $_POST['property_id'];

    $check_sql = "SELECT * FROM inquire WHERE property_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $property_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows == 0) {
        echo "error: no matching inquiry found";
        exit;
    }

    $conn->begin_transaction();

    try {
        $sql_inquire = "DELETE FROM inquire WHERE property_id = ?";
        $stmt_inquire = $conn->prepare($sql_inquire);
        $stmt_inquire->bind_param("i", $property_id);
        $stmt_inquire->execute();

        $sql_messages = "DELETE FROM messages WHERE property_id = ?";
        $stmt_messages = $conn->prepare($sql_messages);
        $stmt_messages->bind_param("i", $property_id);
        $stmt_messages->execute();

        $conn->commit();
        
        echo "success";
    } catch (Exception $e) {
        $conn->rollback();
        echo "error: delete failed";
    }
}
?>
