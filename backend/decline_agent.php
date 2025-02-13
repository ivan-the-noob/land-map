<?php
require 'db.php';

if (isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $deleteQuery = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();
}
?>
