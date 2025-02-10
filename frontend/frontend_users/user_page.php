<?php
session_start();

// Initialize a variable to store error message for modal
$show_modal = false;
$error_message = '';

// Check if the user is logged in
if (!isset($_SESSION['role_type'])) {
    // If not logged in, set flag and message for modal
    $show_modal = true;
    $error_message = 'You must be logged in to access this page.';
}

// Check if the user is an admin (if they are logged in)
elseif ($_SESSION['role_type'] !== 'user') {
    // If not admin, set flag and message for modal
    $show_modal = true;
    $error_message = 'You do not have the necessary permissions to access this page.';
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Land Map | USER </title>
  <link rel="icon" href="../../assets/images/logo.png" type="image/x-icon">

  <!-- vendor css -->
  <link href="../../assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../../assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
  <link href="../../assets/lib/typicons.font/typicons.css" rel="stylesheet">
  <link href="../../assets/lib/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">

  <!-- Mapping Links -->
  <script src="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.umd.js"></script>
  <link href="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.css" rel="stylesheet" />

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>

  <!-- azia CSS -->
  <link rel="stylesheet" href="../../assets/css/azia.css">
  <link rel="stylesheet" href="../../assets/css/profile.css">

  <style>
    /* Foggy effect for the entire screen */
    .modal-backdrop {
        backdrop-filter: blur(20px) brightness(0.8); /* Heavy blur with slight dimming */
        -webkit-backdrop-filter: blur(20px) brightness(0.8); /* Safari support */
        background-color: rgba(255, 255, 255, 0.4); /* Fog-like white overlay */
    }

    /* Modal content styling */
    .warning-modal-content {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .warning-icon {
        font-size: 50px;
        color: #dc3545;
    }

    .warning-btn-danger {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
    }

    .warning-btn-danger:hover {
        background-color: #c82333;
    }
</style>

</head>

<body>

<div class="az-header">
    <?php require '../../partials/nav_user.php' ?>
</div><!-- az-header -->

<div class="az-content az-content-dashboard">
    <div class="container">
        <div class="az-content-body">
            <div class="az-dashboard-one-title">
                <div>
                    <h2 class="az-dashboard-title">User Dashboard</h2>
                    <p class="az-dashboard-text">Explore and manage your property interests</p>
                </div>
                <!-- Time and Date -->
                <div class="az-content-header-right">
                        <div class="media">
                            <div class="media-body">
                                <label>Current Date</label>
                                <h6 id="current-date"></h6>
                            </div><!-- media-body -->
                        </div><!-- media -->
                        <div class="media">
                            <div class="media-body">
                                <label>Current Time</label>
                                <h6 id="current-time"></h6>
                            </div><!-- media-body -->
                        </div><!-- media -->
                        <div class="media">
                            <div class="media-body">
                                <label>Time Zone</label>
                                <h6>Philippine Time (PHT)</h6>
                            </div><!-- media-body -->
                        </div><!-- media -->
                    </div>
                    <script>
                        function updateDateTime() {
                            const now = new Date();
                            const dateOptions = { year: 'numeric', month: 'short', day: 'numeric' };
                            const timeOptions = { 
                                hour: '2-digit', 
                                minute: '2-digit', 
                                second: '2-digit', 
                                hour12: true,
                                timeZone: 'Asia/Manila'
                            };
                            
                            document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', dateOptions);
                            document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', timeOptions);
                        }

                        updateDateTime();
                        setInterval(updateDateTime, 1000);
                    </script>
                    <!-- Time and Date footer -->
            </div>
            <!-- az-dashboard-one-title -->

            <div class="az-dashboard-nav">
                <nav class="nav">
                    <a class="nav-link active" data-toggle="tab" href="#dashboard">My Listed Properties</a>
                    <a class="nav-link" data-toggle="tab" href="#message"></a>
                    <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'agent'): ?>
                    <a class="nav-link" data-toggle="tab" href="#user-activity">User Activity</a>
                    <?php endif; ?>
                </nav>
            </div>

            <div class="tab-content mt-4">
                <div id="property-list" class="tab-pane active">
                    <div class="row" id="landListings">

                    
                        <!-- Land listings will be rendered here -->
                    </div>
                </div>
                
                
                <!-- My Favorite Properties -->
                <div class="tab-content mt-4">
                    <div id="my-favorites" class="tab-pane active">
                        <div id="my-favorites" class="tab-pane">
                               <!-- Post new land property -->
                            <div class="property-list">
                                
    <?php
    require '../../db.php';

    $sql = "SELECT p.*, 
            u.fname, u.lname,
            ui.image_name as user_image,
            (SELECT image_name FROM property_images WHERE property_id = p.property_id LIMIT 1) AS property_image
            FROM properties p 
            LEFT JOIN users u ON p.user_id = u.user_id
            LEFT JOIN user_img ui ON u.user_id = ui.user_id";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $imagePath = $row['property_image'] ? "../../assets/property_images/" . $row['property_image'] : "../../assets/images/default-property.jpg";
            $agentName = $row['fname'] . ' ' . $row['lname'];
    ?>
            <div class="property-card">
                <div class="property-image">
                    <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($row['property_name']); ?>">
                    <div class="sale-badge">
                        <?php echo strtoupper($row['sale_or_lease']); ?>
                    </div>
                    <div class="location-badge">
                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['property_location']); ?>
                    </div>
                </div>

                <div class="property-content">
                    <h3 class="property-title">Property Name: <?php echo htmlspecialchars($row['property_name']); ?></h3>
                    
                    <?php if ($row['sale_or_lease'] == 'sale' && $row['sale_price'] > 0) { ?>
                        <div class="property-price">₱<?php echo number_format($row['sale_price'], 2); ?></div>
                    <?php } elseif ($row['sale_or_lease'] == 'lease' && $row['monthly_rent'] > 0) { ?>
                        <div class="property-price">₱<?php echo number_format($row['monthly_rent'], 2); ?>/monthly cost</div>
                    <?php } ?>

                    <div class="property-details">
                        <?php if ($row['land_area']) { ?>
                            <span><i class="fas fa-ruler-combined"> Land Area:</i> <?php echo number_format($row['land_area']); ?> sqm</span>
                        <?php } ?>
                        <?php if ($row['property_type']) { ?>
                            <span><i class="fas fa-home"> Land Type:</i> <?php echo htmlspecialchars($row['property_type']); ?></span>
                        <?php } ?>
                    </div>

                    <?php if ($row['property_description']) { ?>
                        <div class="property-description"><i class="fas fa-land"> Land Description:</i>
                            <?php echo substr(htmlspecialchars($row['property_description']), 0, 100) . '...'; ?>
                        </div>
                    <?php } ?>

                    <?php if ($row['land_condition']) { ?>
                        <div class="property-conditon">
                            <span><i class="fas fa-check-circle"></i> Land Condition: <?php echo ucfirst($row['land_condition']); ?></span>
                        </div>
                    <?php } ?>

                    <?php if ($row['another_info']) { ?><i class="fas fa-land"> Another Information:</i>
                        <div class="promo-badge">
                            <?php echo ucfirst($row['another_info']); ?>
                        </div>
                    <?php } ?>

                    <div class="admin-actions">
                    <button class="btn-view" onclick="viewDetails(<?php echo $row['property_id']; ?>)">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        <button class="btn-submit" onclick="submitProperty(<?php echo $row['property_id']; ?>)">
                            <i class="fas fa-plus"></i> Listed
                        </button>
                        <button class="btn-delete" onclick="deleteProperty(<?php echo $row['property_id']; ?>)">
                            <i class="fas fa-trash"></i> Archive
                        </button>
                    </div>

                    <div class="agent-info">
                        <?php if ($row['user_image']) { ?>
                            <img src="../../assets/images/profile/<?php echo $row['user_image']; ?>" alt="Agent">
                        <?php } ?>
                        <span><i class="fas fa-user"> Agent Name:</i> <?php echo htmlspecialchars($agentName); ?></span>
                        <button class="btn-contact" onclick="contactAgent(<?php echo $row['user_id']; ?>)">
                            <i class="fas fa-user"></i> Message Agent
                        </button>
                    </div>
                </div>
            </div>
    <?php
        }
    } else {
        echo '<div class="no-properties">
                <i class="fas fa-home"></i>
                <p>No properties found</p>
              </div>';
    }
    ?>
</div>

<style>
.property-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(500px, 1fr));
    gap: 20px;
    padding: 20px;
}

.property-card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    background: white;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.property-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.property-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.sale-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #4CAF50;
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.location-badge {
    position: absolute;
    bottom: 10px;
    left: 10px;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
}

.property-content {
    padding: 15px;
}

.property-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
}

.property-price {
    font-size: 20px;
    font-weight: bold;
    color: #4CAF50;
    margin-bottom: 10px;
}

.property-details {
    display: flex;
    gap: 15px;
    margin-bottom: 10px;
    color: #666;
    font-size: 14px;
}

.property-details span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.property-description {
    font-size: 14px;
    color: #666;
    margin-bottom: 15px;
}

.property-condition {
    display: inline-block;
    background: #FFC107;
    color: white;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 12px;
    margin-bottom: 15px;
}

.promo-badge {
    display: inline-block;
    background: #FFC107;
    color: white;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 12px;
    margin-bottom: 15px;
}

.property-actions, .admin-actions {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.admin-actions {
    padding-top: 10px;
    border-top: 1px solid #eee;
}

.btn-view, .btn-contact, .btn-submit, .btn-update, .btn-delete {
    padding: 8px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.btn-view {
    background: #4CAF50;
    color: white;
}

.btn-contact {
    background: white;
    color: #4CAF50;
    border: 1px solid #4CAF50;
}

.btn-submit {
    background: #2196F3;
    color: white;
}

.btn-update {
    background: #FFC107;
    color: white;
}

.btn-delete {
    background: #f44336;
    color: white;
}

.btn-view:hover, .btn-submit:hover, .btn-update:hover, .btn-delete:hover {
    opacity: 0.9;
}

.btn-contact:hover {
    background: #4CAF50;
    color: white;
}

.agent-info {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    color: #666;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #eee;
}

.agent-info img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
}

@media (max-width: 768px) {
    .property-list {
        grid-template-columns: 1fr;
    }
    
    .property-card {
        margin: 10px;
    }

    .property-actions, .admin-actions {
        flex-direction: column;
    }
}
</style>

<script>
function submitProperty(propertyId) {
    if(confirm('Are you sure you want to submit this property?')) {
        // Add your submit logic here
        console.log('Submitting property:', propertyId);
    }
}

function updateProperty(propertyId) {
    window.location.href = 'edit_property.php?id=' + propertyId;
}

function deleteProperty(propertyId) {
    if(confirm('Are you sure you want to archive this property?')) {
        // Make AJAX call to archive the property
        fetch('../../backend/archive_property.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                property_id: propertyId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert('Property archived successfully');
                // Refresh the page to update the property list
                location.reload();
            } else {
                // Show error message
                alert('Error archiving property: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error archiving property. Please try again.');
        });
    }
}

function viewDetails(propertyId) {
    // Add your view details logic here
    console.log('Viewing details for property:', propertyId);
}

function contactAgent(userId) {
    // Add your contact agent logic here
    console.log('Contacting agent:', userId);
}
</script>
                        </div>
                    </div>

                    <!-- PROPERTY CARD -->

                    <!-- Add the floating button -->
<button id="mapButton" class="floating-map-btn" onclick="toggleMap()">
    <i class="fas fa-map-marker-alt"></i>
</button>

<!-- Add the map panel -->
<div id="mapPanel" class="map-panel">
    <div class="map-controls">
        <button class="map-control-btn" onclick="toggleFullscreen()">
            <i class="fas fa-expand"></i>
        </button>
        <button class="map-control-btn" onclick="toggleMap()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div id="agentPropertyMap" style="width: 100%; height: 100%;"></div>
</div>

<style>
.floating-map-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: rgba(255, 255, 255, 0.9);
    color: #666;
    border: 1px solid #ddd;
    border-radius: 50%;
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    transition: all 0.3s ease;
}

.map-panel {
    position: fixed;
    top: 0;
    right: -50%;
    width: 50%;
    height: 100vh;
    background: white;
    box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 999;
}

.map-panel.active {
    right: 0;
}

.map-panel.fullscreen {
    width: 100% !important;
    height: 100vh !important;
    right: 0;
    top: 0;
    z-index: 1001;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.map-controls {
    position: absolute;
    top: 10px;
    left: 10px;
    display: flex;
    gap: 10px;
    z-index: 1002;
}

.map-control-btn {
    background: white;
    border: none;
    border-radius: 4px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.map-control-btn:hover {
    background: #f5f5f5;
    transform: translateY(-2px);
    box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}

.map-control-btn i {
    transition: transform 0.3s ease;
}

.map-control-btn:active i {
    transform: scale(0.9);
}

/* Add animation for fullscreen icon */
.fa-expand, .fa-compress {
    transition: transform 0.3s ease;
}

.fullscreen .fa-expand {
    transform: rotate(180deg);
}

/* Add animation for property list */
.property-list {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    width: 100%;
}

.property-list.map-active {
    width: 50%;
}

.property-list.fullscreen-active {
    opacity: 0;
    transform: scale(0.95);
    display: none;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

@media (max-width: 768px) {
    .map-panel {
        width: 100%;
        right: -100%;
    }

    .property-list.map-active {
        width: 0;
        overflow: hidden;
    }
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    maptilersdk.config.apiKey = 'gLXa6ihZF9HF7keYdTHC';

    const agentPropertyMap = new maptilersdk.Map({
        container: 'agentPropertyMap',
        style: maptilersdk.MapStyle.HYBRID,
        geolocate: maptilersdk.GeolocationType.POINT,
        zoom: 10,
        maxZoom: 16.2
    });

    // Fetch coordinates from the API
    fetch('../../backend/coordinates.php')
        .then(response => response.json())
        .then(coordinates => {
            // Check if the response is an array
            if (!Array.isArray(coordinates)) {
                console.error('Fetched data is not an array:', coordinates);
                return;
            }

            // Add each coordinate as a marker
            coordinates.forEach(function(coord) {
                // Ensure that each coordinate array has exactly 2 values (longitude, latitude)
                if (coord.length !== 2) {
                    console.error(`Invalid coordinate format: [${coord}]`);
                    return;
                }

                const [longitude, latitude] = coord;

                // Check if the coordinate values are valid numbers
                if (isNaN(longitude) || isNaN(latitude)) {
                    console.error(`Invalid coordinate: [${longitude}, ${latitude}]`);
                } else {
                    new maptilersdk.Marker()
                        .setLngLat([longitude, latitude])
                        .addTo(agentPropertyMap);
                }
            });
        })
        .catch(error => {
            console.error('Error fetching coordinates:', error);
        });

    window.toggleMap = function() {
        const mapPanel = document.getElementById('mapPanel');
        const propertyList = document.querySelector('.property-list');

        if (mapPanel && propertyList) {
            mapPanel.classList.toggle('active');
            propertyList.classList.toggle('map-active');

            // If exiting fullscreen mode when closing
            if (mapPanel.classList.contains('fullscreen')) {
                mapPanel.classList.remove('fullscreen');
                propertyList.classList.remove('fullscreen-active');
            }

            // Trigger a resize event to ensure the map renders correctly
            if (agentPropertyMap) {
                setTimeout(() => {
                    agentPropertyMap.resize();
                }, 300);
            }
        }
    };

    window.toggleFullscreen = function() {
        const mapPanel = document.getElementById('mapPanel');
        const propertyList = document.querySelector('.property-list');
        const fullscreenIcon = document.querySelector('.map-control-btn i.fa-expand, .map-control-btn i.fa-compress');

        if (mapPanel && propertyList) {
            mapPanel.classList.toggle('fullscreen');
            propertyList.classList.toggle('fullscreen-active');

            // Toggle fullscreen icon
            if (fullscreenIcon) {
                if (mapPanel.classList.contains('fullscreen')) {
                    fullscreenIcon.classList.remove('fa-expand');
                    fullscreenIcon.classList.add('fa-compress');
                } else {
                    fullscreenIcon.classList.remove('fa-compress');
                    fullscreenIcon.classList.add('fa-expand');
                }
            }

            // Trigger a resize event to ensure the map renders correctly
            if (agentPropertyMap) {
                setTimeout(() => {
                    agentPropertyMap.resize();
                }, 300);
            }
        }
    };

    // Enable the map button after map style has loaded
    agentPropertyMap.on('load', function() {
        const mapButton = document.getElementById('mapButton');
        if (mapButton) {
            mapButton.disabled = false;
        }
    });

});

// This is for controlling the map button state
const mapButton = document.getElementById('mapButton');
if (mapButton) {
    mapButton.disabled = false; // Enable the button when the map is ready
}

const mapPanel = document.getElementById('mapPanel');
if (mapPanel) {
    mapPanel.classList.toggle('active');
}

const propertyList = document.querySelector('.property-list');
if (propertyList) {
    propertyList.classList.toggle('map-active');
}

</script>

                <!-- Add Land Modal -->
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="redirectToCreateProperty()">
                        <i class="fas fa-plus"></i> Schedule Appointment
                    </button>
                </div>

                <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'agent'): ?>
                <div id="user-activity" class="tab-pane">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">User Activity Tracking</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Last Active</th>
                                            <th>Viewed Properties</th>
                                            <th>Favorites</th>
                                            <th>Inquiries</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="userActivityTableBody">
                                        <!-- User activity will be populated dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div id="mapContainer" class="map-container">
                <div id="userPropertyMap"></div>
            </div>

            <button id="mapButton" class="btn">
                <i class="fas fa-map-marker-alt"></i>
            </button>

        </div><!-- az-content-body -->
    </div>
</div><!-- az-content -->

<!-- Required Scripts -->
<script src="../../assets/lib/jquery/jquery.min.js"></script>
<script src="../../assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/lib/ionicons/ionicons.js"></script>
<script src="../../assets/js/azia.js"></script>

<!-- Unauthorized Access Modal -->
<div class="modal fade" id="warningModal" tabindex="-1" role="dialog" aria-labelledby="warningModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="warning-modal-content">
            <div class="modal-body text-center">
                <!-- Custom Warning Icon with Animation -->
                <div class="warning-icon-wrapper">
                    <i class="fas fa-exclamation-circle warning-icon"></i>
                </div>
                <p class="warning-modal-message" id="warningMessage">You do not have permission to view this page.
                </p>
            </div>
            <div class="warning-modal-footer justify-content-center">
                <button type="button" class="btn warning-btn-danger" id="warningCloseButton">Sign In</button>
            </div>
        </div>
    </div>
</div>

    <!--Unauthorized modal-->
    <script>
        $(document).ready(function () {
            var showModal = <?php echo $show_modal ? 'true' : 'false'; ?>;
            var errorMessage = <?php echo json_encode($error_message); ?>;

            if (showModal) {
                $('#warningMessage').text(errorMessage); // Set the error message dynamically
                $('#warningModal').modal({
                    backdrop: 'static',  // Prevent closing when clicking outside
                    keyboard: false      // Prevent closing when pressing the escape key
                });
                $('#warningModal').modal('show'); // Show the modal
            }

            // Close the modal and redirect to login when the "Sign In" button is clicked
            $('#warningCloseButton').click(function () {
                $('#warningModal').modal('hide');
                window.location.href = '../../frontend/sign_in.php';  // Redirect to the login page
            });
        });
    </script>
    
    <!-- Sign Out Confirmation Modal -->
    <div class="modal fade" id="signOutModal" tabindex="-1" role="dialog" aria-labelledby="signOutModalLabel"
            aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="signout-icon-wrapper">
                        <i class="fas fa-sign-out-alt signout-icon"></i>
                    </div>
                    <p class="signout-modal-message">Are you sure you want to sign out?</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmSignOutButton">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!--Signout process--->
    <script>
        $(document).ready(function() {
            // Show the sign-out confirmation modal when the Sign Out button is clicked
            $('#signOutButton').on('click', function() {
                $('#signOutModal').modal('show');
            });

            // Confirm sign out (destroy session and redirect to login page)
            $('#confirmSignOutButton').on('click', function() {
                // Make a request to sign_out.php to destroy the session
                $.ajax({
                    url: '../../backend/sign_out.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            window.location.href = '../../index.php';
                        } else {
                            alert('Error: Could not sign out.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('Error: Could not sign out.');
                    }
                });
            });
        });
    </script>

<script>
// Initialize empty landListings array
const landListings = [];

// Fix for time update script
function updateTime() {
    const timeElement = document.getElementById('current-time');
    const dateElement = document.getElementById('current-date');
    
    if (!timeElement || !dateElement) {
        console.error('Time/date elements not found');
        return;
    }

    const now = new Date();
    const dateOptions = { year: 'numeric', month: 'short', day: 'numeric' };
    const timeOptions = { 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit', 
        hour12: true,
        timeZone: 'Asia/Manila'
    };
    
    dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
    timeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
}

// Fix for MapTiler integration
const apiKey = 'REPLACE_WITH_ACTUAL_MAPTILER_API_KEY'; // Replace this with your actual MapTiler API key
maptilersdk.config.apiKey = apiKey;

function renderListings(listings) {
    const container = document.getElementById('landListings');
    if (!container) {
        console.error('Land listings container not found');
        return;
    }
    
    container.innerHTML = '';
    
    listings.forEach((listing, index) => {
        const listingHtml = `
            <div class="col-md-6 col-lg-4 land-item" data-type="${listing.type}">
                <div class="land-card">
                    <div class="${listing.saleType === 'sale' ? 'sale-tag' : 'lease-tag'}">
                        ${listing.saleType === 'sale' ? 'For Sale' : 'For Lease'}
                    </div>
                    <img src="${listing.image}" class="land-image" alt="Land Image">
                    <h4 class="mt-3">${listing.propertyName}</h4>
                    <div class="land-details">
                        <p><i class="fas fa-map-marker-alt"></i> Location: ${listing.location}</p>
                        <p><i class="fas fa-tag"></i> Price: ₱${listing.price.toLocaleString()}</p>
                        <p><i class="fas fa-ruler-combined"></i> Area: ${listing.area} sqm</p>
                        <p><i class="fas fa-info-circle"></i> Description: ${listing.description}</p>
                        ${listing.saleType === 'sale' ? 
                            `<p><i class="fas fa-home"></i> Land Condition: ${listing.landCondition}</p>` :
                            `<p><i class="fas fa-clock"></i> Lease Term: ${listing.leaseTerm}</p>`
                        }
                        <div class="features-list">
                            ${listing.features ? listing.features.map(feature => 
                                `<span class="feature-tag"><i class="fas fa-check"></i> ${feature}</span>`
                            ).join('') : ''}
                        </div>
                        <div class="map-container" id="map${index}"></div>
                        <div class="mt-3">
                            <button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#viewModal${index}">View</button>
                            <button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#messageModal${index}">Chat Agent</button>
                            <button class="btn btn-primary btn-xs" onclick="addToList(${index})"><i class="fas fa-star"></i>Favorites</button>
                        </div>
                    </div>
                </div>
            </div>`;
        container.innerHTML += listingHtml;

        // Add error handling for map initialization
        try {
            if (!apiKey || apiKey === 'REPLACE_WITH_ACTUAL_MAPTILER_API_KEY') {
                throw new Error('Invalid MapTiler API key');
            }

            const map = new maptilersdk.Map({
                container: `map${index}`,
                style: maptilersdk.MapStyle.STREETS,
                center: [121.0537, 14.5489], // Philippines coordinates
                zoom: 13
            });

            // Add marker
            new maptilersdk.Marker()
                .setLngLat([121.0537, 14.5489])
                .addTo(map);

        } catch (error) {
            console.error(`Error initializing map ${index}:`, error);
            const mapContainer = document.getElementById(`map${index}`);
            if (mapContainer) {
                mapContainer.innerHTML = 
                    '<div class="alert alert-warning">Map loading failed. Please ensure a valid MapTiler API key is configured.</div>';
            }
        }
    });
}

// Add tracking functionality for agents
$(document).ready(function() {
    $('.track-user').click(function() {
        const userId = $(this).data('user-id');
        // Implement user tracking logic here
        console.log('Tracking user:', userId);
        // Make AJAX call to track user activity
    });

    // Initial render of listings
    renderListings(landListings);
    
    // Start time updates
    updateTime();
    setInterval(updateTime, 1000);
});

function viewUserProfile(userId) {
    // Implement view user profile logic here
    window.location.href = 'user_profile.php?id=' + userId;
}

function viewAgentProfile(agentId) {
    // Implement view agent profile logic here
    window.location.href = 'agent_profile.php?id=' + agentId;
}

function redirectToCreateProperty() {
    window.location.href = 'user_appointment.php';
}

function addToList(index) {
    // Implement add to favorites logic here
    console.log('Adding listing to favorites:', index);
    // Make AJAX call to add to favorites
}

function removeFromFavorites(index) {
    // Implement remove from favorites logic here
    console.log('Removing listing from favorites:', index);
    // Make AJAX call to remove from favorites
}
</script>

</body>
</html>