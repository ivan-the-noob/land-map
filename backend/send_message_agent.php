<?php
require '../db.php';
session_start();

// Check if the necessary POST data is available
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_id'], $_POST['user_id'], $_POST['agent_id'], $_POST['message'])) {
    
    // Get data from POST and session
    $property_id = $_POST['property_id'];  // Property ID
    $user_id = $_POST['user_id'];          // Inquirer (user) ID from POST data (user who inquired)
    $agent_id = $_SESSION['user_id'];      // Agent ID from session
    $message = trim($_POST['message']);    // The message to be sent
    $role_type = $_SESSION['role_type'];  // Ensure the role is valid (user or agent)

    // Check if user is logged in
    if (empty($user_id) || empty($role_type)) {
        echo "error: user not logged in";
        exit;
    }

    // Insert message into the database
    $sql = "INSERT INTO messages (property_id, user_id, agent_id, message, role_type) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiss", $property_id, $user_id, $agent_id, $message, $role_type);

    // Check if the insertion was successful
    if ($stmt->execute()) {
        echo "success"; // Message sent successfully
    } else {
        echo "error"; // Error during insertion
    }
}
?>
