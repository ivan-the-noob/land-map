<?php
    require '../db.php';
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_id'])) {
        $property_id = $_POST['property_id'];

        $sql = "UPDATE inquire SET status = 'accepted' WHERE property_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $property_id);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
    }
?>
