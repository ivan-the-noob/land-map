<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_id'])) {
    $property_id = $_POST['property_id'];
    $user_id = $_SESSION['user_id']; // Ensure user is logged in

    // Prepare the SQL statement to insert into archive_table
    $sql = "INSERT INTO archive_table (property_id, user_id, add_list) VALUES (?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $property_id, $user_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>
