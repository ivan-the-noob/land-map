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
    $role_type = $_SESSION['role_type'];   // Ensure the role is valid (user or agent)

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
        // Insert notification for the user
        $notification_msg = "You have a new message from an agent.";
        $notif_sql = "INSERT INTO notifications (user_id, agent_id, notification) VALUES (?, ?, ?)";
        $notif_stmt = $conn->prepare($notif_sql);
        $notif_stmt->bind_param("iis", $user_id, $agent_id, $notification_msg);
        $notif_stmt->execute();

        echo "success"; // Message and notification inserted successfully
    } else {
        echo "error"; // Error during insertion
    }
}
?>
