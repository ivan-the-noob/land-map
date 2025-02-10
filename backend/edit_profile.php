<?php
session_start();
require '../db.php';  // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Check if the user has 'super_admin' role
if ($_SESSION['role_type'] !== 'super_admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Get the user ID from session
$user_id = $_SESSION['user_id'];

// Initialize variables to hold user data (username, email, profile image)
$username = $email = $profile_image = '';

// Get the current user's details
$query = "SELECT * FROM admin WHERE id = '$user_id' LIMIT 1";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Check if a new profile image is uploaded
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
        // Get file details
        $file_tmp_name = $_FILES['profileImage']['tmp_name'];
        $file_name = $_FILES['profileImage']['name'];
        $file_size = $_FILES['profileImage']['size'];
        $file_type = $_FILES['profileImage']['type'];

        // Allowed file types (images)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        // Validate file type and size
        if (!in_array($file_type, $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, and GIF are allowed.']);
            exit();
        }

        // Generate unique file name
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $unique_file_name = uniqid('profile_', true) . '.' . $file_extension;
        $target_directory = '../assets/profile_images/admin_profile_img/';
        $target_file = $target_directory . $unique_file_name;

        // Check if there is an old image to delete
        if (!empty($user['img_name'])) {
            // Old image path
            $old_image_path = $target_directory . $user['img_name'];
            
            // Delete the old image if it exists
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
        }

        // Move uploaded file to target directory
        if (!move_uploaded_file($file_tmp_name, $target_file)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload the image.']);
            exit();
        }

        // Set profile image filename
        $profile_image = $unique_file_name;
    }
}

// If username or email is provided, update them along with profile image (if available)
$query = "UPDATE admin SET ";
$updates = [];

if (!empty($_POST['username'])) {
    $username = $conn->real_escape_string(trim($_POST['username']));
    $updates[] = "username = '$username'";
}
if (!empty($_POST['email'])) {
    $email = $conn->real_escape_string(trim($_POST['email']));
    $updates[] = "email = '$email'";
}
if (!empty($profile_image)) {
    $updates[] = "img_name = '$profile_image'";
}

// Only update if there are changes
if (count($updates) > 0) {
    $query .= implode(", ", $updates);
    $query .= " WHERE id = '$user_id'";

    if ($conn->query($query)) {
        // If the update is successful, update session variables if needed
        if (!empty($username)) {
            $_SESSION['user_name'] = $username;
        }
        if (!empty($email)) {
            $_SESSION['email'] = $email;
        }

        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No data to update.']);
}

$conn->close();
?>