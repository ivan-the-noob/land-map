<?php

require '../db.php';

header('Content-Type: application/json');

$sql = "SELECT coordinates FROM properties WHERE coordinates IS NOT NULL";
$result = $conn->query($sql);

$coordinates = [];
if ($result->num_rows > 0) {
    // Fetch all the results and decode JSON from the database
    while($row = $result->fetch_assoc()) {
        $coords = json_decode($row['coordinates']);
        
        // Flatten the coordinate arrays into a single array of valid [longitude, latitude] pairs
        foreach ($coords as $coord) {
            if (is_array($coord) && count($coord) === 2) {
                $coordinates[] = $coord;
            } else {
                // Log invalid coordinate format
                error_log("Invalid coordinate format: " . json_encode($coord));
            }
        }
    }
} else {
    echo json_encode([]);
}

$conn->close();

// Output the coordinates as JSON
echo json_encode($coordinates);
?>
