<?php
require '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_id'])) {
    $property_id = $_POST['property_id'];


    $check_sql = "SELECT * FROM inquire WHERE property_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $property_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows == 0) {
        echo "error: no matching inquiry found";
        exit;
    }

    $sql = "DELETE FROM inquire WHERE property_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $property_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: delete failed";
    }
}
?>
