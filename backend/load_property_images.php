<?php
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_id'])) {
    $property_id = $_POST['property_id'];

    $sql = "SELECT image_name FROM property_images WHERE property_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $property_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<div class="d-flex flex-wrap justify-content-center">';
        while ($row = $result->fetch_assoc()) {
            $imagePath = "../../assets/property_images/" . $row['image_name'];
            echo '<img src="' . htmlspecialchars($imagePath) . '" class="img-thumbnail m-2" style="width: 200px; height: 150px;">';
        }
        echo '</div>';
    } else {
        echo '<p>No images found for this property.</p>';
    }
}
?>
