<?php
require '../db.php'; // Adjust this to your actual database connection file

header('Content-Type: application/json'); // Ensure response is JSON

if (!isset($_POST['image_id']) || !isset($_POST['image_name'])) {
    echo json_encode(["success" => false, "error" => "Missing required parameters."]);
    exit;
}

$imageId = $_POST['image_id'];
$imageName = $_POST['image_name'];

if (!is_numeric($imageId)) {
    echo json_encode(["success" => false, "error" => "Invalid image ID format."]);
    exit;
}

// Path to the image file
$filePath = "../../assets/property_images/" . $imageName;

// Prepare and execute the delete query
$query = "DELETE FROM property_images WHERE image_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "Database error: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $imageId);

if ($stmt->execute()) {
    // Check if file exists before attempting to delete
    if (file_exists($filePath)) {
        if (!unlink($filePath)) {
            echo json_encode(["success" => false, "error" => "Failed to delete image file from server."]);
            exit;
        }
    }
    echo json_encode(["success" => true, "message" => "Image deleted successfully."]);
} else {
    echo json_encode(["success" => false, "error" => "Failed to delete from database."]);
}

$stmt->close();
$conn->close();
?>
