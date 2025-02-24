<?php
require '../db.php';
session_start();

date_default_timezone_set('Asia/Manila'); // Set timezone to Philippine Time
$created_at = date('Y-m-d H:i:s'); // Get current date-time in 24-hour format (MySQL standard)

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_id'], $_POST['agent_id'], $_POST['message'])) {
    $property_id = $_POST['property_id'];
    $user_id = $_SESSION['user_id'];
    $agent_id = $_POST['agent_id'];
    $message = trim($_POST['message']);

    if (empty($user_id)) {
        echo "error: user not logged in";
        exit;
    }

    // Insert message with PHP-generated timestamp
    $sql = "INSERT INTO messages (property_id, user_id, agent_id, message, created_at) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiss", $property_id, $user_id, $agent_id, $message, $created_at);

    if ($stmt->execute()) {
        $notification = "You have a new message from a user.";
        $role = "agent"; 

        // Insert notification with PHP-generated timestamp
        $notif_sql = "INSERT INTO notifications (user_id, agent_id, notification, role, created_at) VALUES (?, ?, ?, ?, ?)";
        $notif_stmt = $conn->prepare($notif_sql);
        $notif_stmt->bind_param("iisss", $user_id, $agent_id, $notification, $role, $created_at);
        $notif_stmt->execute();

        echo "success";
    } else {
        echo "error";
    }
}
?>
