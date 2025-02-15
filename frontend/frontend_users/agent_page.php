<?php
session_start();
require_once '../../db.php'; // Add database connection

// Assuming the login is successful
$_SESSION['login_success'] = true;

// Add this near the top of the file, after session_start()
$agent_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Unknown Agent';

// Initialize a variable to store error message for modal
$show_modal = false;
$error_message = '';

// Check if the user is logged in
if (!isset($_SESSION['role_type'])) {
    // If not logged in, set flag and message for modal
    $show_modal = true;
    $error_message = 'You must be logged in to access this page.';
} elseif ($_SESSION['role_type'] !== 'agent') {
    // Check if the user is an agent (if they are logged in)
    // If not agent, set flag and message for modal
    $show_modal = true;
    $error_message = 'You do not have the necessary permissions to access this page.';
}

// Get total properties count for the logged-in agent
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $countQuery = "SELECT COUNT(*) as total FROM properties WHERE user_id = ?";
    $stmt = $conn->prepare($countQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_properties = $result->fetch_assoc()['total'];
} else {
    $total_properties = 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-90680653-2"></script>
    <script>
        window.dataLayer = window.dataLayer || []; // Initialize dataLayer

        function gtag() {
            dataLayer.push(arguments); // Push arguments to dataLayer
        }
        gtag('js', new Date()); // Log the current date
        gtag('config', 'UA-90680653-2'); // Configure Google Analytics
    </script>

    <!-- Required meta tags -->
    <meta charset="utf-8"> <!-- Character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> <!-- Responsive design -->

    <title>Land Map | My Listings</title> <!-- Page title -->
    <link rel="icon" href="../../assets/images/logo.png" type="image/x-icon"> <!-- Favicon -->

    <!-- vendor css -->
    <link href="../../assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet"> <!-- Font Awesome -->
    <link href="../../assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet"> <!-- Ionicons -->
    <link href="../../assets/lib/typicons.font/typicons.css" rel="stylesheet"> <!-- Typicons -->
    <link href="../../assets/lib/flag-icon-css/css/flag-icon.min.css" rel="stylesheet"> <!-- Flag Icons -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmgygVeipMUsrtGeZPZ9UzXRmcVdheIqw&libraries=places"></script>

    <!-- Mapping Links -->
    <script src="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.umd.js"></script> <!-- Maptiler SDK -->
    <link href="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.css" rel="stylesheet" /> <!-- Maptiler CSS -->

    <!-- azia CSS -->
    <link rel="stylesheet" href="../../assets/css/azia.css"> <!-- Custom CSS -->
    </head>

  <!-- Blur Effect -->
  <style>
        /* Foggy effect for the entire screen */
        .modal-backdrop {
            backdrop-filter: blur(100px) brightness(200); /* Heavy blur with slight dimming */
            -webkit-backdrop-filter: blur(100px) brightness(400); /* Safari support */
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
    <!-- Blur Effect -->
    <div class="az-header">
        <?php require "../../partials/nav_agent.php" ?>
    </div>

<body>
    <!-- az-header-head -->

   

    <!-- az-header-tail -->

    <div class="az-content az-content-dashboard">
        <div class="container">
            <div class="az-content-body">
                <div class="az-dashboard-one-title">
                    <div>
                        <h2 class="az-dashboard-title">Agent: <?php echo htmlspecialchars($agent_name); ?></h2>
                        <p class="az-dashboard-text">
                            <span class="star-rating">
                                <i class="fa fa-star" style="color: gold;"></i>
                                <i class="fa fa-star" style="color: gold;"></i>
                                <i class="fa fa-star" style="color: gold;"></i>
                                <i class="fa fa-star-half" style="color: gold;"></i>
                                <i class="fa fa-star-o" style="color: gold;"></i>
                            </span>
                        </p>
                    </div>
                    <div class="az-content-header-right">
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
                                    <h6 id="currentTime"></h6>
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
                                document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-US', timeOptions);
                            }

                            updateDateTime();
                            setInterval(updateDateTime, 1000);
                        </script>
                        <!-- Time and Date footer -->
                    </div>
                </div>

                <div class="az-dashboard-nav">
                    <nav class="nav">
                        <a class="nav-link active" data-toggle="tab" href="#dashboard">Dashboard</a>
                        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'agent') : ?>
                            <a class="nav-link" data-toggle="tab" href="#user-activity">User Activity</a>
                        <?php endif; ?>
                    </nav>

                    <nav class="nav">
                    </nav>
                </div>

                <div class="tab-content mt-4">
                    <div id="dashboard" class="tab-pane active">
                        <?php if (isset($properties) && !empty($properties)) : ?>
                            <div class="property-list">
                                <?php foreach ($properties as $property) : ?>
                                    <div class="container mt-4">
                                        <div class="property-card">
                                            <div class="property-image">
                                                <?php if (!empty($property['images'])) : ?>
                                                    <img alt="Property image" height="300"
                                                         src="assets/uploads/<?php echo $property['images'][0]; ?>" width="500" />
                                                <?php endif; ?>
                                                <div class="badge-new">NEW</div>
                                                <div class="image-overlay">
                                                    <i class="fas fa-camera"></i> <?php echo count($property['images']); ?>
                                                </div>
                                            </div>
                                            <div class="property-details">
                                                <h3><?php echo htmlspecialchars($property['name']); ?></h3>
                                                <p><i class="fas fa-map-marker-alt"></i>
                                                    <?php echo htmlspecialchars($property['location']); ?></p>
                                                <p class="description">
                                                    <?php
                                                    $maxLength = 250;
                                                    $description = htmlspecialchars($property['description']);
                                                    echo strlen($description) > $maxLength ? substr($description, 0, $maxLength) . '...' : $description;
                                                    ?>
                                                </p>
                                                <div class="property-price">₱<?php echo number_format($property['price'], 2); ?>
                                                </div>
                                                <div class="property-meta">
                                                    <span><i class="fas fa-bed"></i>
                                                        <?php echo isset($property['amenities']['beds']) ? $property['amenities']['beds'] : 0; ?></span>
                                                    <span><i class="fas fa-ruler-combined"></i>
                                                        <?php echo htmlspecialchars($property['landArea']); ?> m²</span>
                                                    <span><i class="fas fa-expand-arrows-alt"></i>
                                                        <?php echo htmlspecialchars($property['floorArea']); ?> m²</span>
                                                </div>
                                                <div class="property-buttons">
                                                    <a href="/viewproperty?id=<?php echo $property['id']; ?>"><button
                                                            class="btn btn-primary">View</button></a>

                                                    <button class="btn btn-success">Chat</button>
                                                    <button class="btn btn-warning">Contact info</button>
                                                    <div class="property-agent">
                                                        <img alt="Agent profile picture" height="40"
                                                             src="https://storage.googleapis.com/a1aa/image/AHDaplfiAQyyPaSgBI50lHFgYCfOFjze4fvfS7oNbwfeniv2JA.jpg"
                                                             width="40" />
                                                        <div class="agent-info">
                                                            <span>Agent Name</span>
                                                            <span class="badge-semi-verified">SEMI VERIFIED</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <div id="dashboard" class="tab-pane">
                                <div class="row">
                                    <div class="col-md-3">
                                        <a href="agent_listing.php" class="text-decoration-none">
                                            <div class="card bg-primary text-white hover-card">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-home fa-2x mb-2"></i>
                                                    <h6 class="card-title">Total Land Properties</h6>
                                                    <h4><?php echo $total_properties; ?></h4>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="messages.php" class="text-decoration-none">
                                            <div class="card bg-success text-white hover-card">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-comments fa-2x mb-2"></i>
                                                    <h6 class="card-title">Active messages CRM</h6>
                                                    <h4>2</h4>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="active_listings.php" class="text-decoration-none">
                                            <div class="card bg-info text-white hover-card">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-list fa-2x mb-2"></i>
                                                    <h6 class="card-title">Active Listings</h6>
                                                    <h4>2</h4>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="inactive_listings.php" class="text-decoration-none">
                                            <div class="card bg-secondary text-white hover-card">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                                    <h6 class="card-title">Deactivated Listings</h6>
                                                    <h4>2</h4>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <style>
                                    .hover-card {
                                        transition: transform 0.2s, box-shadow 0.2s;
                                    }
                                    .hover-card:hover {
                                        transform: translateY(-5px);
                                        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                                        cursor: pointer;
                                    }
                                </style>

                                <?php if ($total_properties == 0) { ?>
                                    <div class="text-center mt-5">
                                        <div class="empty-state">
                                            <i class="fas fa-plus-circle fa-3x text-muted mb-2"></i>
                                            <h5>No Properties Listed</h5>
                                            <p class="text-muted small">Start by adding your first property</p>
                                            <button class="btn btn-primary mt-3" onclick="redirectToCreateProperty()">
                                                <i class="fas fa-plus"></i> Add New Land Property
                                            </button>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

            </div><!-- az-content-body -->
        </div>
    </div><!-- az-content -->


<!-- start of footer -->
<div class="modal-footer">
    </div>

    <div class="az-footer">
        <div class="container">
            <span class="text-muted d-block text-center">Copyright ©LoremIpsum 2024</span>
        </div><!-- container -->
    </div>


    <script src="../../assets/js/addedFunctions.js"></script>

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
            <div class="modal-content"> <!-- This is the white container -->
                <div class="modal-body text-center">
                    <!-- Custom Sign Out Icon with Animation -->
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
        // Show the sign-out confirmation modal when the Sign Out button is clicked
        document.getElementById('signOutButton').addEventListener('click', function() {
            $('#signOutModal').modal('show'); // Show the modal
        });

        // Confirm sign out (destroy session and redirect to login page)
        document.getElementById('confirmSignOutButton').addEventListener('click', function() {
            // Make a request to sign_out.php to destroy the session
            fetch('../../backend/sign_out.php', {
                method: 'GET'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // If sign out is successful, redirect to login page
                    window.location.href = '../../frontend/sign_in.php';
                } else {
                    alert('Error: Could not sign out.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>

    <script src="../../assets/lib/jquery/jquery.min.js"></script>
    <script src="../../assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/lib/ionicons/ionicons.js"></script>
    <script src="../../assets/lib/jquery.flot/jquery.flot.js"></script>
    <script src="../../assets/lib/jquery.flot/jquery.flot.resize.js"></script>
    <script src="../../assets/lib/chart.js/Chart.bundle.min.js"></script>
    <script src="../../assets/lib/peity/jquery.peity.min.js"></script>

    <script src="../../assets/js/azia.js"></script>
    <script src="../../assets/js/chart.flot.sampledata.js"></script>
    <script src="../../assets/js/dashboard.sampledata.js"></script>
    <script src="../../assets/js/jquery.cookie.js" type="text/javascript"></script>

    <script src="../../assets/js/addedFunctions.js"></script>

    <!-- Blur Effect -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const warningModal = document.getElementById("warningModal");
            const mainContent = document.getElementById("mainContent");

            // Add blur effect when modal is shown
            warningModal.addEventListener("show.bs.modal", function () {
                mainContent.classList.add("blur-background");
            });

            // Remove blur effect when modal is hidden
            warningModal.addEventListener("hidden.bs.modal", function () {
                mainContent.classList.remove("blur-background");
            });
        });
    </script>

    <script>
        // Only initialize the map if the container exists
        document.addEventListener('DOMContentLoaded', function() {
            const mapContainer = document.getElementById('agentPropertyMap');
            if (mapContainer) {
                maptilersdk.config.apiKey = 'gLXa6ihZF9HF7keYdTHC';
                const agentPropertyMap = new maptilersdk.Map({
                    container: 'agentPropertyMap',
                    style: maptilersdk.MapStyle.HYBRID,
                    geolocate: maptilersdk.GeolocationType.POINT,
                    zoom: 10,
                    maxZoom: 16.2
                });
            }
        });
    </script>

    <!-- Update event listeners to check for element existence -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // File input handler
            const fileInput = document.querySelector('.custom-file-input');
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const fileNames = Array.from(e.target.files).map(file => file.name);
                    const label = e.target.nextElementSibling;
                    label.classList.add('selected');
                    label.innerHTML = fileNames.length > 2 ? `${fileNames[0]}, ${fileNames[1]}, +${fileNames.length - 2} more` : fileNames.join(', ');
                });
            }

            // Map button handler
            const mapButton = document.getElementById('mapButton');
            if (mapButton) {
                mapButton.addEventListener('click', function() {
                    const mapContainer = document.getElementById('mapContainer');
                    const propertyList = document.querySelector('.property-list');
                    
                    if (mapContainer && propertyList) {
                        mapContainer.classList.toggle('open');
                        if (mapContainer.classList.contains('open')) {
                            propertyList.classList.add('one-column');
                        } else {
                            propertyList.classList.remove('one-column');
                        }
                    }
                });
            }

            // Sale/Lease handler
            const saleOrLease = document.getElementById('saleOrLease');
            if (saleOrLease) {
                saleOrLease.addEventListener('change', function() {
                    const priceLabel = document.getElementById('priceLabel');
                    const priceInput = document.getElementById('propertyPrice');
                    
                    if (priceLabel && priceInput) {
                        if (this.value === 'lease') {
                            priceLabel.textContent = 'Monthly Rate';
                            priceInput.placeholder = 'Enter monthly rate';
                        } else if (this.value === 'sale') {
                            priceLabel.textContent = 'Price';
                            priceInput.placeholder = 'Enter price';
                        }
                    }
                });
            }
        });
    </script>

    <!--Time Update-->
    <script>
        // Function to update the current time every second
        function updateTime() {
            const timeElement = document.getElementById('currentTime');
            if (timeElement) {  // Add null check
                // Get the current time in Manila timezone
                const now = new Date().toLocaleString("en-US", { timeZone: "Asia/Manila" });

                // Format the time as hh:mm:ss AM/PM
                const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
                const timeString = new Date(now).toLocaleTimeString('en-US', options);

                // Update the time on the page
                timeElement.textContent = timeString;
            }
        }

        // Update the time every 1000 milliseconds (1 second)
        setInterval(updateTime, 1000);
    </script>

</body>

</html>