<?php
session_start();
require '../db.php';

if (isset($_POST['property_id']) && isset($_POST['cancel_reason'])) {
    $propertyId = intval($_POST['property_id']);
    $cancelReason = trim($_POST['cancel_reason']);

    // Update the inquire table without cancelled_by
    $sql = "UPDATE inquire SET status = 'cancelled', cancel_reason = ? WHERE property_id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("si", $cancelReason, $propertyId);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Error cancelling inquiry."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Failed to prepare statement."]);
    }

    $conn->close();
} else {
    echo json_encode(["success" => false, "error" => "Missing required fields."]);
}
?>
