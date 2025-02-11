<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "You must be logged in to inquire."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["property_id"])) {
    $user_id = $_SESSION['user_id'];
    $property_id = intval($_POST["property_id"]);

    $checkQuery = $conn->prepare("SELECT * FROM inquire WHERE user_id = ? AND property_id = ?");
    $checkQuery->bind_param("ii", $user_id, $property_id);
    $checkQuery->execute();
    $result = $checkQuery->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "You have already inquired about this property."]);
        exit;
    }

    $query = $conn->prepare("INSERT INTO inquire (user_id, property_id, status) VALUES (?, ?, 'pending')");
    $query->bind_param("ii", $user_id, $property_id);

    if ($query->execute()) {
        echo json_encode(["status" => "success", "message" => "Inquiry submitted successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to submit inquiry."]);
    }
}
?>
