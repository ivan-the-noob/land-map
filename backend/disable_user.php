<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;

    if (!$user_id) {
        echo json_encode(["success" => false, "error" => "User ID is missing."]);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Increment disable_status in users table
        $query1 = "UPDATE users SET disable_status = disable_status + 1 WHERE user_id = ?";
        $stmt1 = $conn->prepare($query1);
        $stmt1->bind_param("i", $user_id);
        $stmt1->execute();
        $stmt1->close();

        // Delete all reports related to this user in report_properties
        $query2 = "DELETE FROM report_properties WHERE property_id IN (SELECT property_id FROM properties WHERE user_id = ?)";
        $stmt2 = $conn->prepare($query2);
        $stmt2->bind_param("i", $user_id);
        $stmt2->execute();
        $stmt2->close();

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
