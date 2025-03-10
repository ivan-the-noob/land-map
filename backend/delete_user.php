<?php
require_once '../db.php';

header('Content-Type: application/json'); // Ensure JSON response

if (isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete related records first
        $conn->query("DELETE FROM inquire WHERE user_id = $userId");
        $conn->query("DELETE FROM reports WHERE user_id = $userId");

        // Now delete from users
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);

        if ($stmt->execute()) {
            $conn->commit();
            echo json_encode(["success" => true]); // Send success JSON
        } else {
            throw new Exception($stmt->error);
        }

        $stmt->close();
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "error" => $e->getMessage()]); // Send error JSON
    }

    $conn->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request"]); // Handle missing user_id
}


?>
