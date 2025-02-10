<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role_type'] !== 'agent') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

try {
    // Get form data
    $property_id = $_POST['property_id'];
    $property_name = $_POST['property_name'];
    $property_location = $_POST['property_location'];
    $sale_or_lease = $_POST['sale_or_lease'];
    $land_area = $_POST['land_area'];
    $property_description = $_POST['property_description'];

    // Validate land area
    if ($land_area > 10000000) { // 1000 hectares in square meters
        throw new Exception("Land area cannot exceed 1000 hectares (10,000,000 sqm)");
    }

    // Prepare base SQL query
    $sql = "UPDATE properties SET 
            property_name = ?,
            property_location = ?,
            sale_or_lease = ?,
            land_area = ?,
            property_description = ?";
    $params = [$property_name, $property_location, $sale_or_lease, $land_area, $property_description];
    $types = "sssds"; // string, string, string, decimal, string

    // Add sale-specific fields
    if ($sale_or_lease === 'sale') {
        $sale_price = $_POST['sale_price'];
        $land_condition = $_POST['land_condition'];
        $another_info = $_POST['another_info'];

        // Validate sale price
        if ($sale_price > 1000000000) {
            throw new Exception("Sale price cannot exceed ₱1,000,000,000");
        }

        $sql .= ", sale_price = ?, land_condition = ?, another_info = ?, 
                  monthly_rent = NULL, lease_duration = NULL";
        $params[] = $sale_price;
        $params[] = $land_condition;
        $params[] = $another_info;
        $types .= "dss"; // decimal, string, string
    } else {
        // Add lease-specific fields
        $monthly_rent = $_POST['monthly_rent'];
        $lease_duration = $_POST['lease_duration'];

        // Validate monthly rent based on lease duration
        if ($lease_duration === 'short term' && $monthly_rent > 1000000) {
            throw new Exception("Monthly rent cannot exceed ₱1,000,000 for short term lease");
        } elseif ($lease_duration === 'long term' && $monthly_rent > 1000000000) {
            throw new Exception("Monthly rent cannot exceed ₱1,000,000,000 for long term lease");
        }

        $sql .= ", monthly_rent = ?, lease_duration = ?, 
                  sale_price = NULL, land_condition = NULL, another_info = NULL";
        $params[] = $monthly_rent;
        $params[] = $lease_duration;
        $types .= "ds"; // decimal, string
    }

    // Add WHERE clause
    $sql .= " WHERE property_id = ? AND user_id = ?";
    $params[] = $property_id;
    $params[] = $_SESSION['user_id'];
    $types .= "ii"; // integer, integer

    // Prepare and execute the statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Property updated successfully'
        ]);
    } else {
        // Check if the property exists and belongs to the user
        $check_sql = "SELECT property_id FROM properties WHERE property_id = ? AND user_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $property_id, $_SESSION['user_id']);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows === 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Property not found or you do not have permission to edit it'
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'message' => 'No changes were made to the property'
            ]);
        }
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?> 