<?php
require '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_id'])) {
    $property_id = $_POST['property_id'];

    // Delete from archive_table where property_id matches
    $sql = "DELETE FROM archive_table WHERE property_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo "error: prepare failed";
        exit;
    }

    $stmt->bind_param("i", $property_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "success";
        } else {
            error_log("Delete executed, but no rows were affected.");
            echo "error: no rows affected";
        }
    } else {
        error_log("Execution failed: " . $stmt->error);
        echo "error: execution failed";
    }

    $stmt->close();
    $conn->close();
}
?>
