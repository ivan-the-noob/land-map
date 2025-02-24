<?php
require '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_id'])) {
    $property_id = $_POST['property_id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update inquire table
        $sql1 = "UPDATE inquire SET status = 'completed' WHERE property_id = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("i", $property_id);
        $stmt1->execute();

        // Update properties table
        $sql2 = "UPDATE properties SET property_status = 1 WHERE property_id = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $property_id);
        $stmt2->execute();

        // Delete messages related to the property_id
        $sql3 = "DELETE FROM messages WHERE property_id = ?";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("i", $property_id);
        $stmt3->execute();

        // Commit transaction
        $conn->commit();
        echo "success";
    } catch (Exception $e) {
        // Rollback in case of an error
        $conn->rollback();
        echo "error: " . $e->getMessage();
    }
}
?>
