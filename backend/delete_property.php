<?php
require '../db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_id'])) {
    $propertyId = $_POST['property_id'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM properties WHERE property_id = ?");
    $stmt->bind_param("i", $propertyId);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
