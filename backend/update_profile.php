<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized request!";
    exit;
}

$user_id = $_SESSION['user_id'];
$fname = trim($_POST['fname']);
$lname = trim($_POST['lname']);

// Check if fname and lname are not empty
if (empty($fname) || empty($lname)) {
    echo "First name and last name are required!";
    exit;
}

// Update first name and last name
$updateName = "UPDATE users SET fname = ?, lname = ? WHERE user_id = ?";
$stmt = $conn->prepare($updateName);
$stmt->bind_param("ssi", $fname, $lname, $user_id);
if (!$stmt->execute()) {
    echo "Error updating profile!";
    exit;
}

// Handle profile image upload
if (!empty($_FILES['profile']['name'])) {
    $uploadDir = '../assets/profile_images/';
    $fileName = time() . '_' . basename($_FILES['profile']['name']);
    $uploadPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['profile']['tmp_name'], $uploadPath)) {
        // Update profile image in database
        $updateImage = "UPDATE users SET profile = ? WHERE user_id = ?";
        $stmt = $conn->prepare($updateImage);
        $stmt->bind_param("si", $fileName, $user_id);
        $stmt->execute();
    } else {
        echo "Error uploading image!";
        exit;
    }
}

echo "Profile updated successfully!";
$conn->close();
?>
