<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = $_POST['property_id'] ?? null;

    if (!$property_id) {
        echo json_encode(["success" => false, "error" => "Property ID is missing."]);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get user_id from properties table
        $query_get_user = "SELECT user_id FROM properties WHERE property_id = ?";
        $stmt_get_user = $conn->prepare($query_get_user);
        $stmt_get_user->bind_param("i", $property_id);
        $stmt_get_user->execute();
        $result = $stmt_get_user->get_result();
        $user = $result->fetch_assoc();
        $stmt_get_user->close();

        if (!$user) {
            echo json_encode(["success" => false, "error" => "Property not found."]);
            exit;
        }

        $user_id = $user['user_id'];

        // Set disable_status back to 0
        $query_update = "UPDATE users SET disable_status = 0 WHERE user_id = ?";
        $stmt_update = $conn->prepare($query_update);
        $stmt_update->bind_param("i", $user_id);
        $stmt_update->execute();
        $stmt_update->close();

        // Delete reports related to this property
        $query_delete = "DELETE FROM report_properties WHERE property_id = ?";
        $stmt_delete = $conn->prepare($query_delete);
        $stmt_delete->bind_param("i", $property_id);
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
