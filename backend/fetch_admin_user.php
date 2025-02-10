<?php
// fetch_admins.php

session_start();
require '../db.php';

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role_type'] !== 'admin') {
    echo json_encode(["error" => "Unauthorized access"]);
    exit();
}

// Fetch all admins from the database
$adminQuery = "SELECT * FROM admin";
$adminResult = $conn->query($adminQuery);

$admins = [];

if ($adminResult->num_rows > 0) {
    while ($admin = $adminResult->fetch_assoc()) {
        // Check if fields are empty or null, if so, set them to "Not Set"
        $username = !empty($admin['username']) ? $admin['username'] : 'Not Set';
        $fullName = (!empty($admin['fname']) && !empty($admin['lname'])) ? $admin['fname'] . ' ' . $admin['lname'] : 'Not Set';
        $email = !empty($admin['email']) ? $admin['email'] : 'Not Set';

        // Check verification status
        $verificationStatus = ($admin['is_verified'] == 1) ? 'Verified' : ($admin['is_verified'] == 2 ? 'Semi-verified' : 'Not Verified');
        $verificationClass = '';

        if ($verificationStatus === 'Verified') {
            $verificationClass = 'badge-success';  
        } elseif ($verificationStatus === 'Semi-verified') {
            $verificationClass = 'badge-warning';  
        } else {
            $verificationClass = 'badge-danger';  
        }

        // Control level
        $controlLevel = ($admin['control_lvl'] == 1) ? 'Full Access' : 'Normal Access';

        $admins[] = [
            'username' => $username,
            'full_name' => $fullName,
            'email' => $email,
            'control_level' => $controlLevel,
            'verification_status' => $verificationStatus,
            'verification_class' => $verificationClass,
        ];
    }
}

$conn->close();

// If no admins found, return a message in the JSON response
if (empty($admins)) {
    echo json_encode(['admins' => [], 'message' => 'No admins found.']);
} else {
    // Return the admin data in JSON format
    echo json_encode(['admins' => $admins]);
}
?>