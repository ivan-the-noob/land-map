<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['property_id'])) {
        die("Error: property_id is missing");
    }

    if (!isset($_SESSION['user_id'])) {
        die("Error: User not logged in");
    }

    $property_id = $_POST['property_id'];
    $user_id = $_SESSION['user_id'];

    // Check database connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare the SQL statement to insert into archive_table
    $sql = "INSERT INTO archive_table (property_id, user_id, add_list) VALUES (?, ?, 1)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ii", $property_id, $user_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        die("Execute failed: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} else {
    die("Invalid request method");
}
?>
