<?php
// Fetch the POST data
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['password']) && isset($data['user_id'])) {
    $password = $data['password'];
    $user_id = $data['user_id'];

    // Hash the password securely
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Create your DB connection
    require '../db.php';

    // Update the password in the database for the given user_id
    $updateQuery = "UPDATE admin SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('si', $hashedPassword, $user_id);

    if ($stmt->execute()) {
        // Respond with success
        echo json_encode(['success' => true]);
    } else {
        // Handle error
        echo json_encode(['success' => false, 'error' => 'Could not update the password. Please try again.']);
    }

    $stmt->close();
} else {
    // Invalid input
    echo json_encode(['success' => false, 'error' => 'Missing required data.']);
}

$conn->close();
?>