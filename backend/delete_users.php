<?php
require_once '../db.php';

header('Content-Type: application/json'); // Ensure JSON response

if (isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];

    // **🔥 FORCE DELETE: Ignore Foreign Key Constraints 🔥**
    $conn->query("SET FOREIGN_KEY_CHECKS=0");

    try {
        // Delete all records related to this user
        $conn->query("DELETE FROM inquire WHERE user_id = $userId");
        $conn->query("DELETE FROM reports WHERE user_id = $userId");

        // 🚀 DELETE USER 🚀
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            throw new Exception($stmt->error);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }

    // **🔥 RE-ENABLE FOREIGN KEY CHECKS (Optional) 🔥**
    $conn->query("SET FOREIGN_KEY_CHECKS=1");

    $conn->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
}





?>
