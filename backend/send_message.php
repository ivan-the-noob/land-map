<?php
require '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_id'], $_POST['agent_id'], $_POST['message'])) {
    $property_id = $_POST['property_id'];
    $user_id = $_SESSION['user_id'];
    $agent_id = $_POST['agent_id'];
    $message = trim($_POST['message']);

    if (empty($user_id)) {
        echo "error: user not logged in";
        exit;
    }


    $sql = "INSERT INTO messages (property_id, user_id, agent_id, message) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $property_id, $user_id, $agent_id, $message);

    if ($stmt->execute()) {
        $notification = "You have a new message from a user.";
        $role = "agent"; 

        $notif_sql = "INSERT INTO notifications (user_id, agent_id, notification, role) VALUES (?, ?, ?, ?)";
        $notif_stmt = $conn->prepare($notif_sql);
        $notif_stmt->bind_param("iiss", $user_id, $agent_id, $notification, $role);
        $notif_stmt->execute();

        echo "success";
    } else {
        echo "error";
    }
}
?>
