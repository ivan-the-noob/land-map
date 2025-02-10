<?php
session_start();
require '../../db.php';

// Add your session checks here (similar to admin_properties.php)

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Include your existing head content -->
    <title>Land Map | Archived Properties</title>
</head>
<body>
    <!-- Include your header/navigation -->
    
    <div class="az-content az-content-dashboard">
        <div class="container">
            <div class="az-content-body">
                <div class="az-dashboard-one-title">
                    <div>
                        <h2 class="az-dashboard-title">Archived Properties</h2>
                        <p class="az-dashboard-text">View and manage archived land properties</p>
                    </div>
                </div>

                <div class="row">
                    <?php
                    $sql = "SELECT p.*, 
                            u.fname, u.lname,
                            (SELECT image_name FROM property_images WHERE property_id = p.property_id LIMIT 1) AS property_image
                            FROM properties p 
                            LEFT JOIN users u ON p.user_id = u.user_id
                            WHERE p.status = 'archived'
                            ORDER BY p.updated_at DESC";
                    
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $imagePath = $row['property_image'] ? "../../assets/property_images/" . $row['property_image'] : "../../assets/images/default-property.jpg";
                    ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card mb-4">
                                <img src="<?php echo $imagePath; ?>" class="card-img-top" alt="Property Image">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($row['property_name']); ?></h5>
                                    <p class="card-text">
                                        <small class="text-muted">Archived on: <?php echo date('M d, Y', strtotime($row['updated_at'])); ?></small>
                                    </p>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-primary btn-sm" onclick="viewDetails(<?php echo $row['property_id']; ?>)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button class="btn btn-success btn-sm" onclick="restoreProperty(<?php echo $row['property_id']; ?>)">
                                            <i class="fas fa-undo"></i> Restore
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteProperty(<?php echo $row['property_id']; ?>)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                        }
                    } else {
                        echo '<div class="col-12"><div class="alert alert-info">No archived properties found.</div></div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add necessary scripts -->
    <script>
    function restoreProperty(propertyId) {
        if (confirm('Are you sure you want to restore this property?')) {
            fetch('../../backend/restore_property.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ property_id: propertyId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to restore property: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while restoring the property');
            });
        }
    }

    function deleteProperty(propertyId) {
        if (confirm('Are you sure you want to permanently delete this property? This action cannot be undone.')) {
            fetch('../../backend/delete_property.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ property_id: propertyId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to delete property: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the property');
            });
        }
    }
    </script>
</body>
</html> 