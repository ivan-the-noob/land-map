<?php
require '../db.php';
session_start();

// Set time zone for MySQL session to Philippine Time
$conn->query("SET time_zone = '+08:00'");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_id'], $_POST['agent_id'], $_POST['message'])) {
    $property_id = $_POST['property_id'];
    $user_id = $_SESSION['user_id'];
    $agent_id = $_POST['agent_id'];
    $message = trim($_POST['message']);

    if (empty($user_id)) {
        echo "error: user not logged in";
        exit;
    }

    // Insert message into the database
    $sql = "INSERT INTO messages (property_id, user_id, agent_id, message, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $property_id, $user_id, $agent_id, $message);

    if ($stmt->execute()) {
        $notification = "You have a new message from a user.";
        $role = "agent"; 

        // Insert notification
        $notif_sql = "INSERT INTO notifications (user_id, agent_id, notification, role, created_at) VALUES (?, ?, ?, ?, NOW())";
        $notif_stmt = $conn->prepare($notif_sql);
        $notif_stmt->bind_param("iiss", $user_id, $agent_id, $notification, $role);
        $notif_stmt->execute();

        echo "success";
    } else {
        echo "error";
    }
}
?>
