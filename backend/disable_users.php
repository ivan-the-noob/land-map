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
        // Ensure the user exists
        $check_user = "SELECT user_id, disable_status FROM users WHERE user_id = ?";
        $stmt_check = $conn->prepare($check_user);
        $stmt_check->bind_param("i", $user_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $user = $result_check->fetch_assoc();
        $stmt_check->close();

        if (!$user) {
            echo json_encode(["success" => false, "error" => "User not found."]);
            exit;
        }

        // Increment disable_status but limit to 3 max
        $query1 = "UPDATE users SET disable_status = LEAST(disable_status + 1, 3) WHERE user_id = ?";
        $stmt1 = $conn->prepare($query1);
        $stmt1->bind_param("i", $user_id);
        $stmt1->execute();
        $stmt1->close();

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
