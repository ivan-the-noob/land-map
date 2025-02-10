<?php
session_start();

// Destroy the session to log out the user
session_unset();  // Remove all session variables
session_destroy();  // Destroy the session

// Return a success response as JSON
echo json_encode(['success' => true]);

if (isset($_SESSION['user_id'])) {
    $update_status = "UPDATE users SET is_logged_in = 0 WHERE user_id = ?";
    $stmt = $conn->prepare($update_status);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}
?>
