<?php
require '../db.php';

if (isset($_GET['property_id'])) {
    $property_id = mysqli_real_escape_string($conn, $_GET['property_id']);
    
    $sql = "SELECT p.*, 
            u.fname as agent_fname, u.lname as agent_lname,
            ui.image_name as agent_image,
            GROUP_CONCAT(pi.image_name) as images
            FROM properties p
            LEFT JOIN users u ON p.user_id = u.user_id
            LEFT JOIN user_img ui ON u.user_id = ui.user_id
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
        
        echo json_encode($property);
    } else {
        echo json_encode(['error' => 'Property not found']);
    }
} else {
    echo json_encode(['error' => 'No property ID provided']);
}
?> 