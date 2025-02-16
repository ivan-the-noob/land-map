<?php
require '../db.php';


if (isset($_GET['property_id'])) {
    $property_id = mysqli_real_escape_string($conn, $_GET['property_id']);
    
    $sql = "SELECT p.*, 
            u.fname AS agent_fname, 
            u.lname AS agent_lname,
            u.profile AS agent_image,  -- Get profile image from users table
            GROUP_CONCAT(pi.image_name) AS images
            FROM properties p
            LEFT JOIN users u ON p.user_id = u.user_id
            LEFT JOIN property_images pi ON p.property_id = pi.property_id
            WHERE p.property_id = ?
            GROUP BY p.property_id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $property_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $property = $result->fetch_assoc();
        
        // Convert images string to array
        $property['images'] = $property['images'] ? explode(',', $property['images']) : [];

        // Check if the agent has a profile image
        $agentImage = !empty($property['agent_image']) ? $property['agent_image'] : 'default.jpg';

        // Add missing fields to JSON response
        $property_data = [
            'property_name' => $property['property_name'] ?? 'N/A',
            'property_type' => $property['property_type'] ?? 'N/A',
            'sale_or_lease' => $property['sale_or_lease'] ?? 'N/A',
            'property_location' => $property['property_location'] ?? 'N/A',
            'land_area' => $property['land_area'] ?? 'N/A',
            'monthly_rent' => isset($property['monthly_rent']) ? (float)$property['monthly_rent'] : 0.00,
            'sale_price' => isset($property['sale_price']) ? (float)$property['sale_price'] : 0.00,
            'land_condition' => $property['land_condition'] ?? 'N/A',
            'lease_duration' => $property['lease_duration'] ?? 'N/A',
            'agent_fname' => $property['agent_fname'] ?? '',
            'agent_lname' => $property['agent_lname'] ?? '',
            'agent_name' => trim(($property['agent_fname'] ?? '') . ' ' . ($property['agent_lname'] ?? '')),
            'property_description' => $property['property_description'] ?? 'N/A',
            'latitude' => $property['latitude'] ?? 'N/A',
            'longitude' => $property['longitude'] ?? 'N/A',
            'images' => $property['images'],
            'agent_image' => $agentImage  // Ensure correct profile image retrieval
        ];
        
        echo json_encode($property_data);
    } else {
        echo json_encode(['error' => 'Property not found']);
    }
} else {
    echo json_encode(['error' => 'No property ID provided']);
}
?>
