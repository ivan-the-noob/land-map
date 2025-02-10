<?php
require '../db.php';

if (isset($_GET['code'])) {
    // Retrieve and sanitize the verification code from the URL
    $verificationCode = $conn->real_escape_string($_GET['code']);

    // Query the database to check if the verification code exists for any admin
    $checkQuery = "SELECT id, fname, lname, email FROM admin WHERE verification_code = ? LIMIT 1";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $verificationCode);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Fetch the user details
        $stmt->bind_result($userId, $fname, $lname, $email);
        $stmt->fetch();

        // Update the user's status to verified (set is_verified = 1 and verification_code to NULL)
        $updateQuery = "UPDATE admin SET verification_code = NULL, is_verified = 1 WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("i", $userId);

        if ($updateStmt->execute()) {
            // Redirect to a success page with the user_id in the URL
            header('Location: ../handlers/admin_verify.php?user_id=' . $userId);
            exit();
        } else {
            echo "There was an error verifying your email. Please try again later.";
        }

        $updateStmt->close();
    } else {
        // If no matching record is found, show invalid token message
        header('Location: ../handlers/invalid_token.php');
        exit();
    }

    $stmt->close();
} else {
    // If no verification code is provided in the URL
    echo "Verification code not provided.";
}

$conn->close();
?>