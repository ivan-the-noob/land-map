<?php
require '../../db.php'; // Include database connection

$propertyId = 57; // Change this to test with another property ID

// Debugging: Display fetched image names
echo "<h3>Debugging: Checking Images for Property ID: $propertyId</h3>";

$imgQuery = "SELECT image_name FROM property_images WHERE property_id = ?";
$imgStmt = $conn->prepare($imgQuery);
$imgStmt->bind_param("i", $propertyId);
$imgStmt->execute();
$imgResult = $imgStmt->get_result();

// Debugging: Check if rows are fetched
if ($imgResult->num_rows > 0) {
    echo "<p>Images found: " . $imgResult->num_rows . "</p>";
} else {
    echo "<p>No images found for this property.</p>";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Property Images</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h3>Existing Images for Property ID: <?php echo $propertyId; ?></h3>
        <div class="row">
            <?php
            if ($imgResult->num_rows > 0) {
                while ($imgRow = $imgResult->fetch_assoc()) {
                    $imgPath = "../../assets/property_images/" . $imgRow['image_name'];
                    
                    // Debugging: Show fetched image names
                    echo "<p>Image Found: " . $imgRow['image_name'] . "</p>";
            ?>
                    <div class="col-md-3 mb-2">
                        <div class="card">
                            <img src="<?php echo htmlspecialchars($imgPath); ?>" class="card-img-top img-fluid" style="height: 150px; object-fit: cover;">
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p>No images available for this property.</p>";
            }
            ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
