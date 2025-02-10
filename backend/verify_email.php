<?php
require '../db.php';

if (isset($_GET['code'])) {
    $verification_code = $conn->real_escape_string($_GET['code']);

    // Check if the verification code exists in the database
    $query = "SELECT * FROM users WHERE verification_code = '$verification_code' LIMIT 1";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Mark the user as verified by setting verification_code to NULL and is_verified to 1
        $update_query = "UPDATE users SET verification_code = NULL, is_verified = 1 WHERE verification_code = '$verification_code'";

        if ($conn->query($update_query) === TRUE) {
            header('Location: ../handlers/verify_email.php');
        } else {
            echo "Error verifying your email. Please try again later.";
        }
    } else {
        header('Location: ../handlers/invalid_token.php');
    }
} else {
    echo "No verification code provided.";
}

$conn->close();