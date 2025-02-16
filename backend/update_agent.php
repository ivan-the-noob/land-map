<?php
require_once '../db.php'; // Adjust this path to your database connection file

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST['user_id'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $location = $_POST['location'];


    // Update query
    $stmt = $conn->prepare("UPDATE users SET fname = ?, lname = ?, email = ?, mobile = ?, location = ? WHERE user_id = ?");
    $stmt->bind_param("sssssi", $fname, $lname, $email, $mobile, $location, $user_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
