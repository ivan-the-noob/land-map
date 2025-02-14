<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized request!";
    exit;
}

$user_id = $_SESSION['user_id'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

// Validate new passwords match
if ($new_password !== $confirm_password) {
    echo "New passwords do not match!";
    exit;
}

// Fetch current password hash from database
$query = "SELECT password FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($stored_password);
$stmt->fetch();
$stmt->close();

// Verify current password
if (!password_verify($current_password, $stored_password)) {
    echo "Current password is incorrect!";
    exit;
}

// Hash new password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update password in database
$updateQuery = "UPDATE users SET password = ? WHERE user_id = ?";
$stmt = $conn->prepare($updateQuery);
$stmt->bind_param("si", $hashed_password, $user_id);
if ($stmt->execute()) {
    echo "Password updated successfully!";
} else {
    echo "Error updating password!";
}

$conn->close();
?>
