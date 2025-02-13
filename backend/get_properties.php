<?php
require '../db.php';

$query = "SELECT p.property_id, p.property_name, p.property_type, p.sale_price, p.sale_or_lease, 
                 p.latitude, p.longitude, p.property_location, p.land_area,
                 (SELECT image_name FROM property_images WHERE property_id = p.property_id LIMIT 1) AS image_name
          FROM properties p
          WHERE p.latitude IS NOT NULL AND p.longitude IS NOT NULL";

$result = $conn->query($query);
$properties = [];

while ($row = $result->fetch_assoc()) { 
    $properties[] = [
        'property_id' => $row['property_id'],
        'property_name' => $row['property_name'],
        'property_type' => $row['property_type'],
        'sale_price' => $row['sale_price'],
        'sale_or_lease' => $row['sale_or_lease'],
        'latitude' => $row['latitude'],
        'longitude' => $row['longitude'],
        'property_location' => $row['property_location'],
        'land_area' => $row['land_area'], 
        'image_name' => $row['image_name']
    ];
}

header('Content-Type: application/json');
echo json_encode($properties);
?>
