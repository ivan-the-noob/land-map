<?php
require '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Get the property_id of the current inquiry
    $sql = "SELECT property_id FROM inquire WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($property_id);
    $stmt->fetch();
    $stmt->close();

    if ($property_id) {
        // Check if there's already an accepted inquiry for this property_id
        $sql = "SELECT COUNT(*) FROM inquire WHERE property_id = ? AND status = 'accepted'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $property_id);
        $stmt->execute();
        $stmt->bind_result($existingCount);
        $stmt->fetch();
        $stmt->close();

        if ($existingCount > 0) {
            echo "You have existing clients, you can only talk to one person per property.";
        } else {
            // Update inquiry status to accepted
            $sql = "UPDATE inquire SET status = 'accepted' WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo "success";
            } else {
                echo "error";
            }
        }
    } else {
        echo "Invalid inquiry.";
    }
}
?>
