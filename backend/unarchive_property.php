<?php
require '../db.php'; // Ensure this file properly initializes $conn (MySQLi connection)

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_id'])) {
    $property_id = intval($_POST['property_id']); // Convert to integer for security

    // Debugging: Log received property_id
    error_log("Received property_id: " . $property_id);

    // Check if the property exists
    $checkSql = "SELECT * FROM properties WHERE property_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $property_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows === 0) {
        error_log("Error: Property ID does not exist.");
        echo "Error: Property ID does not exist.";
        exit;
    }

    // Update property
    $sql = "UPDATE properties SET is_archive = 0 WHERE property_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $property_id);

    if ($stmt->execute()) {
        error_log("Query executed successfully: UPDATE properties SET is_archive = 1 WHERE property_id = " . $property_id);
        echo "Success";
    } else {
        error_log("SQL Error: " . $stmt->error);
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    error_log("Invalid request");
    echo "Invalid request";
}
?>
