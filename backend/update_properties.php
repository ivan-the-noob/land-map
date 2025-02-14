<?php
session_start();
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $property_id = $_POST['property_id'];
    $property_name = $_POST['propertyName']; 
    $property_type = $_POST['propertyType']; 
    $sale_or_lease = $_POST['saleOrLease']; 
    $land_area = $_POST['landArea']; 
    $land_condition = $_POST['landCondition']; 
    $sale_price = isset($_POST['price']) ? $_POST['price'] : NULL; 
    $lease_duration = isset($_POST['leaseDuration']) ? $_POST['leaseDuration'] : NULL;
    $monthly_rent = isset($_POST['monthlyRentalCost']) ? $_POST['monthlyRentalCost'] : NULL;
    $property_description = $_POST['description']; 

    $sql = "UPDATE properties 
            SET property_name = ?, 
                property_type = ?, 
                sale_or_lease = ?, 
                land_area = ?, 
                land_condition = ?, 
                sale_price = ?, 
                lease_duration = ?, 
                monthly_rent = ?, 
                property_description = ? 
            WHERE property_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdssdssi", $property_name, $property_type, $sale_or_lease, $land_area, $land_condition, 
                      $sale_price, $lease_duration, $monthly_rent, $property_description, $property_id);

    if ($stmt->execute()) {
        $agent_id = $_SESSION['user_id'];
        $message = "$property_name properties has been updated.";

        $notif_sql = "INSERT INTO notifications (agent_id, notification) VALUES (?, ?)";
        $notif_stmt = $conn->prepare($notif_sql);
        $notif_stmt->bind_param("is", $agent_id, $message);
        $notif_stmt->execute();
        $notif_stmt->close();

        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update property."]);
    }

    $stmt->close();
    $conn->close();
}
?>
