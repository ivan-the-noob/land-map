<?php
// Assuming you have a database connection file included here
include('../db.php'); // Update with the path to your DB connection file

session_start(); // Assuming the user is logged in and session is active
$user_id = $_SESSION['user_id']; // Assuming the user ID is stored in the session

// Handle image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
    $upload_dir = '../assets/profile_images/admin_profile_img/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create directory if not exists
    }

    $image = $_FILES['image'];
    $image_name = basename($image['name']);
    $image_tmp = $image['tmp_name'];
    $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array(strtolower($image_extension), $allowed_extensions)) {
        // Generate a unique filename
        $new_image_name = uniqid() . '.' . $image_extension;
        $image_path = $upload_dir . $new_image_name;

        if (move_uploaded_file($image_tmp, $image_path)) {
            // Update the profile image in the database
            $stmt = $db->prepare("UPDATE admin SET img_name = ? WHERE id = ?");
            $stmt->bind_param('si', $image_path, $user_id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Profile image updated successfully', 'image_url' => $image_path]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update profile image']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid image type']);
    }
}

// Handle form submission to update user information
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_name = $_POST['user_name'];
    $company = $_POST['company'];
    $job = $_POST['job'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    // Update the user's information in the database
    $stmt = $db->prepare("UPDATE admin SET username = ?, company = ?, job = ?, phone_number = ?, email = ?, address = ? WHERE id = ?");
    $stmt->bind_param('ssssssi', $user_name, $company, $job, $phone_number, $email, $address, $user_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
    }
}

// Handle deleting profile image
if (isset($_POST['delete_image']) && $_POST['delete_image'] == 'true') {
    // Get the current image path from the database
    $stmt = $db->prepare("SELECT img_name FROM admin WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $current_image = $row['profile_image'];

    if ($current_image && file_exists($current_image)) {
        unlink($current_image); // Delete the current image file
    }

    // Update the database to remove the image
    $stmt = $db->prepare("UPDATE users SET profile_image = NULL WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Profile image deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete profile image']);
    }
}
?>