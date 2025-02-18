<?php
require_once '../db.php'; // Adjust this path based on your structure

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST['user_id'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $is_verified = isset($_POST['is_verified']) ? 1 : 0; // Checkbox for active/inactive status

    $stmt = $conn->prepare("UPDATE users SET fname = ?, lname = ?, email = ?, is_verified = ? WHERE user_id = ?");
    $stmt->bind_param("sssii", $fname, $lname, $email, $is_verified, $user_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
