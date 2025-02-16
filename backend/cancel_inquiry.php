<?php
    require '../db.php';

    if (isset($_POST['property_id'])) {
        $propertyId = $_POST['property_id'];

        $sql = "UPDATE inquire SET status = 'cancelled' WHERE property_id = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $propertyId); 
            if ($stmt->execute()) {
                echo "Inquiry has been cancelled successfully.";
            } else {
                echo "Error cancelling inquiry.";
            }
            $stmt->close();
        } else {
            echo "Failed to prepare the statement.";
        }

        $conn->close();
    }
?>
