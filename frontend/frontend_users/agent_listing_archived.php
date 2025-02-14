<?php
session_start(); // Start the session

// Initialize a variable to store error message for modal
$show_modal = false; // Flag to show modal
$error_message = ''; // Variable to hold error message

// Check if the user is logged in
if (!isset($_SESSION['role_type'])) {
    // If not logged in, set flag and message for modal
    $show_modal = true; // Show modal
    $error_message = 'You must be logged in to access this page.'; // Error message for not logged in
} elseif ($_SESSION['role_type'] !== 'agent') {
    // If not agent, set flag and message for modal
    $show_modal = true; // Show modal
    $error_message = 'You do not have the necessary permissions to access this page.'; // Error message for permission issue
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
    <style>
        /* Modal Styles */
        .modal-content {
            max-width: 600px; /* Max width for modal */
            margin: auto; /* Center modal */
            border-radius: 12px; /* Rounded corners */
        }

        .modal-body {
            padding: 20px; /* Padding for modal body */
        }

        .modal-section-title {
            font-size: 1.2rem; /* Font size for section title */
            color: #333; /* Text color */
            margin-bottom: 15px; /* Bottom margin */
            padding-bottom: 8px; /* Padding below title */
            border-bottom: 2px solid #eee; /* Bottom border */
        }

        /* Property Details Grid */
        .details-grid {
            display: grid; /* Use grid layout */
            grid-template-columns: repeat(2, 1fr); /* Two columns */
            gap: 15px; /* Gap between items */
            margin-bottom: 20px; /* Bottom margin */
        }

        .detail-item {
            display: flex; /* Flex layout */
            align-items: start; /* Align items to start */
            gap: 10px; /* Gap between items */
            padding: 8px; /* Padding */
            background: #f8f9fa; /* Background color */
            border-radius: 6px; /* Rounded corners */
        }

        .detail-item i {
            color: #666; /* Icon color */
            min-width: 20px; /* Minimum width for icon */
            margin-top: 4px; /* Top margin */
        }

        .detail-item p {
            margin: 0; /* Remove margin */
            font-size: 0.9rem; /* Font size */
            line-height: 1.4; /* Line height */
        }

        /* Description Box */
        .description-box {
            display: flex; /* Flex layout */
            gap: 10px; /* Gap between items */
            padding: 12px; /* Padding */
            background: #f8f9fa; /* Background color */
            border-radius: 6px; /* Rounded corners */
            margin-bottom: 20px; /* Bottom margin */
        }

        .description-box p {
            margin: 0; /* Remove margin */
            font-size: 0.9rem; /* Font size */
            line-height: 1.5; /* Line height */
        }

        /* Agent Section */
        .agent-profile {
            display: flex; /* Flex layout */
            align-items: center; /* Align items to center */
            gap: 20px; /* Gap between items */
            padding: 15px; /* Padding */
            background: #f8f9fa; /* Background color */
            border-radius: 6px; /* Rounded corners */
        }

        .agent-image img {
            width: 80px; /* Image width */
            height: 80px; /* Image height */
            border-radius: 50%; /* Circular image */
            object-fit: cover; /* Cover image */
            border: 2px solid #fff; /* White border */
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Shadow effect */
        }

        .agent-info p {
            margin: 5px 0; /* Margin for agent info */
            font-size: 0.9rem; /* Font size */
        }

        .land-card {
            border: 1px solid #ddd; /* Border */
            margin-bottom: 20px; /* Bottom margin */
            padding: 15px; /* Padding */
            border-radius: 8px; /* Rounded corners */
        }

        .land-image {
            width: 100%; /* Full width */
            height: 200px; /* Fixed height */
            object-fit: cover; /* Cover image */
            border-radius: 4px; /* Rounded corners */
        }

        .map-container {
            height: 200px; /* Fixed height */
            margin: 10px 0; /* Margin */
        }

        .client-messages {
            max-height: 300px; /* Max height */
            overflow-y: auto; /* Scroll if overflow */
        }

        /* Updated NEW badge design to match sale style */
        .new-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background:rgb(244, 2, 23);
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 2;
            animation: newBadgeAnimation 2s infinite;
        }

        @keyframes newBadgeAnimation {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.9;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Hover effect */
        .new-badge:hover {
            animation-play-state: paused;
            background: linear-gradient(rgb(248, 3, 23));
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
        }

        /* Ensure proper positioning */
        .property-image {
            position: relative;
            overflow: visible !important;
        }

        /* Match the sale badge style if it exists */
        .sale-badge, .new-badge {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
    </style>

    <style>
        .land-card {
            border: 1px solid #ddd; /* Border */
            margin-bottom: 20px; /* Bottom margin */
            padding: 15px; /* Padding */
            border-radius: 8px; /* Rounded corners */
        }

        .land-image {
            width: 100%; /* Full width */
            height: 200px; /* Fixed height */
            object-fit: cover; /* Cover image */
            border-radius: 4px; /* Rounded corners */
        }

        .map-container {
            height: 200px; /* Fixed height */
            margin: 10px 0; /* Margin */
        }

        .client-messages {
            max-height: 300px; /* Max height */
            overflow-y: auto; /* Scroll if overflow */
        }

        .filter-section {
            background: #f8f9fa; /* Background color */
            padding: 20px; /* Padding */
            border-radius: 8px; /* Rounded corners */
            margin-bottom: 20px; /* Bottom margin */
        }

        .filter-row {
            display: flex; /* Flex layout */
            flex-wrap: wrap; /* Wrap items */
            gap: 15px; /* Gap between items */
            margin-bottom: 15px; /* Bottom margin */
        }

        .filter-item {
            flex: 1; /* Flex grow */
            min-width: 200px; /* Minimum width */
        }

        .features-list {
            display: flex; /* Flex layout */
            flex-wrap: wrap; /* Wrap items */
            gap: 10px; /* Gap between items */
            margin: 10px 0; /* Margin */
        }

        .feature-tag {
            background: #e9ecef; /* Background color */
            padding: 5px 10px; /* Padding */
            border-radius: 15px; /* Rounded corners */
            font-size: 0.9em; /* Font size */
        }

        #map {
        height: 500px;
        width: 100%;
        }
        input {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <div class="az-header">
        <?php require '../../partials/nav_agent.php' ?> <!-- Include navigation for agent -->
    </div>

    <div class="az-content az-content-dashboard">
        <div class="container">
            <div class="az-content-body">
                <div class="az-dashboard-one-title">
                    <div>
                        <h2 class="az-dashboard-title">My Land Listings</h2> <!-- Dashboard title -->
                        <p class="az-dashboard-text">Manage your land property listings</p> <!-- Dashboard text -->
                    </div>
                    <div class="az-content-header-right">
                        <!-- Time and Date -->
                    <div class="az-content-header-right">
                        <div class="media">
                            <div class="media-body">
                                <label>Current Date</label> <!-- Current date label -->
                                <h6 id="current-date"></h6> <!-- Current date display -->
                            </div><!-- media-body -->
                        </div><!-- media -->
                        <div class="media">
                            <div class="media-body">
                                <label>Current Time</label> <!-- Current time label -->
                                <h6 id="current-time"></h6> <!-- Current time display -->
                            </div><!-- media-body -->
                        </div><!-- media -->
                        <div class="media">
                            <div class="media-body">
                                <label>Time Zone</label> <!-- Time zone label -->
                                <h6>Philippine Time (PHT)</h6> <!-- Time zone display -->
                            </div><!-- media-body -->
                        </div><!-- media -->
                    </div>
                    <script>
                        function updateDateTime() {
                            const now = new Date(); // Get current date and time
                            const dateOptions = { year: 'numeric', month: 'short', day: 'numeric' }; // Date options
                            const timeOptions = { 
                                hour: '2-digit', 
                                minute: '2-digit', 
                                second: '2-digit', 
                                hour12: true,
                                timeZone: 'Asia/Manila' // Time zone
                            };
                            
                            document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', dateOptions); // Update date
                            document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', timeOptions); // Update time
                        }

                        updateDateTime(); // Initial call to update time
                        setInterval(updateDateTime, 1000);
                    </script>
                    <!-- Time and Date footer -->
                    </div>
                </div>

                <div class="az-dashboard-nav">
                    <nav class="nav">
                        <a class="nav-link active" data-toggle="tab" href="#dashboard">Listed Properties</a> <!-- Tab for listed properties -->
                        <a class="nav-link" data-toggle="tab" href="#create_property">Post New land property</a> <!-- Tab for posting new property -->
                    </nav>

                    <nav class="nav">
                    </nav>
                </div>

                <div class="tab-content mt-4">
                    <div id="dashboard" class="tab-pane active">
                        <div id="dashboard" class="tab-pane">
                            <!-- Post new land property -->
                            <h3 class="mb-1 mr-5">My Archived Land Properties</h3> 
                            <div class="d-flex justify-content-end" style="padding-right: 10%">
                                <a href="agent_listing.php"><button class="btn btn-success mb-2">Unarchived Properties</button></a>
                            </div>
                            <div class="property-list">
                                
    <?php
    require '../../db.php'; // Include database connection

    // Fetch properties only for the logged-in agent
    $userId = $_SESSION['user_id']; // Assuming user_id is stored in session
    $sql = "SELECT p.*, 
            u.fname, u.lname,
            ui.image_name as user_image,
            (SELECT image_name FROM property_images WHERE property_id = p.property_id LIMIT 1) AS property_image,
            DATE_FORMAT(p.created_at, '%M %d, %Y') as added_date,
            DATE_FORMAT(p.created_at, '%h:%i %p') as added_time,
            TIMESTAMPDIFF(HOUR, p.created_at, NOW()) as hours_since_created,
            0 as view_count,  /* Temporary default value until table is created */
            0 as message_count  /* Temporary default value until table is created */
            FROM properties p 
            LEFT JOIN users u ON p.user_id = u.user_id
            LEFT JOIN user_img ui ON u.user_id = ui.user_id
            WHERE p.user_id = ? AND p.is_archive = 1
            ORDER BY p.property_id DESC";

    $stmt = $conn->prepare($sql); // Prepare SQL statement
    $stmt->bind_param("i", $userId); // Bind parameters
    $stmt->execute(); // Execute statement
    $result = $stmt->get_result(); // Get result

    if ($result->num_rows > 0) { // Check if properties exist
        while ($row = $result->fetch_assoc()) { // Fetch each property
            $imagePath = $row['property_image'] ? "../../assets/property_images/" . $row['property_image'] : "../../assets/images/default-property.jpg"; // Set image path
            $agentName = $row['fname'] . ' ' . $row['lname']; // Get agent name
            $isNew = $row['hours_since_created'] <= 24;
    ?>
            <div class="property-card">
                <div class="property-image">
                    <?php if ($isNew) { ?>
                        <div class="new-badge">NEW</div>
                    <?php } ?>
                    <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($row['property_name']); ?>"> <!-- Property image -->
                    <div class="sale-badge">
                        <?php echo $row['sale_or_lease'] == 'sale' ? 'FOR SALE' : 'FOR LEASE'; ?> <!-- Sale or lease badge -->
                    </div>
                    <div class="location-badge">
                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['property_location']); ?> <!-- Location badge -->
                    </div>
                </div>

                <div class="property-content">
                    <h3 class="property-title">Property Name: <?php echo htmlspecialchars($row['property_name']); ?></h3> <!-- Property title -->
                    
                    <?php if ($row['sale_or_lease'] == 'sale' && $row['sale_price'] > 0) { ?>
                        <div class="property-price">₱ <?php echo number_format($row['sale_price'], 2); ?></div> <!-- Sale price -->
                    <?php } elseif ($row['sale_or_lease'] == 'lease' && $row['monthly_rent'] > 0) { ?>
                        <div class="property-price">₱ <?php echo number_format($row['monthly_rent'], 2); ?> /monthly cost</div> <!-- Monthly rent -->
                    <?php } ?>

                    <!-- Add Property Stats Section -->
                    <?php if (isset($_SESSION['role_type']) && $_SESSION['role_type'] === 'agent') { ?>
                        <div class="property-stats">
                            <div class="stat-item">
                                <i class="fas fa-eye"></i>
                                <span><?php echo number_format($row['view_count'] ?? 0); ?> Views</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-envelope"></i>
                                <span><?php echo number_format($row['message_count'] ?? 0); ?> Messages</span>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="property-details">
                        <?php if ($row['land_area']) { ?>
                            <span><i class="fas fa-ruler-combined"> Land Area:</i> <?php echo number_format($row['land_area']); ?> sqm</span> <!-- Land area -->
                        <?php } ?>
                        <?php if ($row['property_type']) { ?>
                            <span><i class="fas fa-home"> Land Type:</i> <?php echo htmlspecialchars($row['property_type']); ?></span> <!-- Land type -->
                        <?php } ?>
                        <?php if ($row['sale_or_lease']) { ?>
                            <span><i class="fas fa-tag">Lease Type: </i> <?php echo htmlspecialchars($row['sale_or_lease']); ?></span> <!-- Land type -->
                        <?php } ?>
                        <?php if (!empty($row['land_condition'])) { ?>
                            <span><i class="fas fa-check-circle"></i> Land Condition: <?php echo htmlspecialchars($row['land_condition']); ?></span> 
                        <?php } ?>
                        <?php if (!empty($row['lease_duration']) && $row['sale_or_lease'] === 'lease') { ?>
                            <span><i class="fas fa-file-contract"></i> Lease Term: <?php echo htmlspecialchars($row['lease_duration']); ?></span> 
                        <?php } ?>



                    </div>

                    <?php if ($row['property_description']) { ?>
                        <div class="property-description"><i class="fas fa-land"> Land Description:</i>
                            <?php echo substr(htmlspecialchars($row['property_description']), 0, 100) . '...'; ?> <!-- Property description -->
                        </div>
                    <?php } ?>

                    <?php if ($row['another_info']) { ?><i class="fas fa-land"> Another Information:</i>
                        <div class="promo-badge">
                            <?php echo ucfirst($row['another_info']); ?>
                        </div>
                    <?php } ?>

                    <?php if ($row['land_condition']) { ?><i class="fas fa-land"> Land Condition:</i>
                        <div class="property-conditon">
                            <?php echo ucfirst($row['land_condition']); ?>
                        </div>
                    <?php } ?>

                    <div class="property-actions">
                    <?php if ($row['added_date'] && $row['added_time']) { ?>
                        <div class="property-timestamp">
                            <i class="fas fa-clock"></i>
                            <span>Added on <?php echo htmlspecialchars($row['added_date']); ?> at <?php echo htmlspecialchars($row['added_time']); ?></span>
                        </div>
                    <?php } ?>
                    </div>

                    <style>
                        .property-timestamp {
                            font-size: 0.9rem;
                            color: #666;
                            margin-top: 10px;
                            padding-top: 10px;
                            border-top: 1px solid #eee;
                            display: flex;
                            align-items: center;
                            gap: 5px;
                        }
                        
                        .property-timestamp i {
                            color: #999;
                        }
                    </style>

                    <div class="property-actions d-flex justify-content-center">
                        <button class="btn-view" onclick="openModal(<?php echo $row['property_id']; ?>)">
                            <i class="fas fa-eye"></i> View More Details
                        </button>

                        <!-- Modal -->
                        <div id="propertyModal<?php echo $row['property_id']; ?>" class="modal">
                            <div class="modal-content">
                                <span class="close" onclick="closeModal(<?php echo $row['property_id']; ?>)">&times;</span>
                                
                                <div class="modal-header">
                                    <h2><?php echo htmlspecialchars($row['property_name']); ?></h2> <!-- Modal header -->
                                </div>

                                <div class="modal-body">
                                    <div class="property-details-section">
                                        <h4 class="modal-section-title">Property Details</h4> <!-- Property details title -->
                                        <div class="details-grid">
                                            <div class="detail-item">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <p><strong>Location:</strong> <?php echo htmlspecialchars($row['property_location']); ?></p> <!-- Property location -->
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-tag"></i>
                                                <p><strong>Price:</strong> ₱<?php echo number_format($row['sale_or_lease'] == 'sale' ? $row['sale_price'] : $row['monthly_rent'], 2); ?></p> <!-- Property price -->
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-ruler-combined"></i>
                                                <p><strong>Land Area:</strong> <?php echo number_format($row['land_area']); ?> sqm</p> <!-- Land area -->
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-home"></i>
                                                <p><strong>Land Type:</strong> <?php echo htmlspecialchars($row['property_type']); ?></p> <!-- Land type -->
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-info-circle"></i>
                                                <p><strong>Land Condition:</strong> <?php echo !empty($row['land_condition']) ? ucfirst($row['land_condition']) : 'N/A'; ?></p> <!-- Land condition -->
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-info-circle"></i>
                                                <p><strong>Lease Type</strong> <?php echo !empty($row['sale_or_lease']) ? ucfirst($row['sale_or_lease']) : 'N/A'; ?></p> 
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-plus-circle"></i>
                                                <p><strong>Additional Info:</strong> <?php echo !empty($row['another_info']) ? ucfirst($row['another_info']) : 'N/A'; ?></p> <!-- Additional information -->
                                            </div>
                                        </div>
                                        
                                        <div class="description-box">
                                            <i class="fas fa-file-alt"></i>
                                            <p><strong>Description:</strong> <?php echo htmlspecialchars($row['property_description']); ?></p> <!-- Property description -->
                                        </div>
                                    </div>

                                    <div class="agent-details-section">
                                        <h4 class="modal-section-title">Agent Information</h4> <!-- Agent information title -->
                                        <div class="agent-profile">
                                            <div class="agent-image">
                                                <?php if ($row['user_image']) { ?>
                                                    <img src="../../assets/images/profile/<?php echo $row['user_image']; ?>" alt="Agent Photo"> <!-- Agent image -->
                                                <?php } else { ?>
                                                    <img src="../../assets/images/default-avatar.png" alt="Default Agent Photo"> <!-- Default agent image -->
                                                <?php } ?>
                                            </div>
                                            <div class="agent-info">
                                                <p><strong>Name:</strong> <?php echo htmlspecialchars($agentName); ?></p> <!-- Agent name -->
                                                <p><strong>Contact:</strong> <?php echo htmlspecialchars($row['contact_number'] ?? 'Not provided'); ?></p> <!-- Agent contact -->
                                                <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email'] ?? 'Not provided'); ?></p> <!-- Agent email -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button class="btn-contact" onclick="contactAgent(<?php echo $row['user_id']; ?>)">
                                        <i class="fas fa-envelope"></i> Contact Agent
                                    </button>
                                </div>
                            </div>
                        </div>

                        <script>
                            function openModal(propertyId) {
                                document.getElementById('propertyModal' + propertyId).style.display = "block"; // Open modal
                            }

                            function closeModal(propertyId) {
                                document.getElementById('propertyModal' + propertyId).style.display = "none"; // Close modal
                            }
                        </script>
        

                  

                      <!-- Archive Button -->
                    <button class="btn btn-danger archive-btn" 
                            data-toggle="modal" 
                            data-target="#archiveModal" 
                            data-property-id="<?php echo $row['property_id']; ?>">
                        <i class="fas fa-trash"></i> UnArchive
                    </button>

                    <!-- Archive Confirmation Modal -->
                    <div class="modal fade" id="archiveModal" tabindex="-1" role="dialog" aria-labelledby="archiveModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="archiveModalLabel">Confirm Archive</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to unarchive this property?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-danger" id="confirmArchive">Archive</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                    <script>
                        $(document).ready(function () {
                        var propertyId; // Variable to store property ID

                        // Capture property ID when modal is triggered
                        $('#archiveModal').on('show.bs.modal', function (event) {
                            var button = $(event.relatedTarget); // Button that triggered the modal
                            propertyId = button.data('property-id'); // Get property ID
                            console.log("Selected property ID:", propertyId); // Debugging
                        });

                        // Handle archive confirmation
                        $('#confirmArchive').on('click', function () {
                            if (!propertyId) {
                                alert('Property ID is missing.');
                                return;
                            }

                            $.ajax({
                                url: '../../backend/unarchive_property.php',
                                type: 'POST',
                                data: { property_id: propertyId },
                                success: function (response) {
                                    console.log("Server Response:", response); // Debug response
                                    if (response.trim() === "Success") {
                                        $('#archiveModal').modal('hide'); // Close the modal
                                        
                                        // Delay reload to prevent continuous alert loop
                                        setTimeout(function () {
                                            location.reload();
                                        }, 500); // Reload after 0.5 seconds (adjust as needed)
                                    } else {
                                        alert('Error: ' + response); // Show exact error
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.error("AJAX Error:", status, error);
                                    alert('Error archiving property.');
                                }
                            });
                        });

                        // Ensure modal is completely hidden before triggering reload
                        $('#archiveModal').on('hidden.bs.modal', function () {
                            propertyId = null; // Reset property ID
                        });
                    });

                    </script>



                    </div>

                   
                </div>
            </div>

            
    <?php
        }
    } else {
        echo '<div class="no-properties">
                <i class="fas fa-home"></i>
                <p>No properties found</p> <!-- No properties message -->
              </div>';
    }
    ?>
</div>

<style>
.property-list {
    display: grid; /* Use grid layout */
    grid-template-columns: repeat(auto-fill, minmax(500px, 1fr)); /* Responsive columns */
    gap: 20px; /* Gap between items */
    padding: 20px; /* Padding */
}

.property-card {
    border: 1px solid #e0e0e0; /* Border */
    border-radius: 8px; /* Rounded corners */
    overflow: hidden; /* Hide overflow */
    background: white; /* Background color */
    margin-bottom: 20px; /* Bottom margin */
    box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Shadow effect */
}

.property-image {
    position: relative; /* Position relative for absolute children */
    height: 200px; /* Fixed height */
    overflow: hidden; /* Hide overflow */
}

.property-image img {
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    object-fit: cover; /* Cover image */
}

.sale-badge {
    position: absolute; /* Position absolute */
    top: 10px; /* Top position */
    left: 10px; /* Left position */
    background: #4CAF50; /* Background color */
    color: white; /* Text color */
    padding: 5px 10px; /* Padding */
    border-radius: 4px; /* Rounded corners */
    font-size: 12px; /* Font size */
    font-weight: bold; /* Bold text */
}

.location-badge {
    position: absolute; /* Position absolute */
    bottom: 10px; /* Bottom position */
    left: 10px; /* Left position */
    background: rgba(0,0,0,0.7); /* Background color */
    color: white; /* Text color */
    padding: 5px 10px; /* Padding */
    border-radius: 4px; /* Rounded corners */
    font-size: 12px; /* Font size */
}

.property-content {
    padding: 15px; /* Padding */
}

.property-title {
    font-size: 18px; /* Font size */
    font-weight: bold; /* Bold text */
    margin-bottom: 10px; /* Bottom margin */
}

.property-price {
    font-size: 20px; /* Font size */
    font-weight: bold; /* Bold text */
    color: #4CAF50; /* Text color */
    margin-bottom: 10px; /* Bottom margin */
}

.property-details {
    display: flex; /* Flex layout */
    gap: 15px; /* Gap between items */
    margin-bottom: 10px; /* Bottom margin */
    color: black; /* Text color */
    font-size: 14px; /* Font size */
}

.property-details span {
    display: flex; /* Flex layout */
    align-items: center; /* Align items to center */
    gap: 5px; /* Gap between items */
}

.property-description {
    font-size: 14px; /* Font size */
    color: black; /* Text color */
    margin-bottom: 15px; /* Bottom margin */
}

.property-condition {
    display: inline-block; /* Inline block */
    background: #FFC107; /* Background color */
    color: white; /* Text color */
    padding: 3px 8px; /* Padding */
    border-radius: 4px; /* Rounded corners */
    font-size: 12px; /* Font size */
    margin-bottom: 15px; /* Bottom margin */
}

.promo-badge {
    display: inline-block; /* Inline block */
    background: #FFC107; /* Background color */
    color: white; /* Text color */
    padding: 3px 8px; /* Padding */
    border-radius: 4px; /* Rounded corners */
    font-size: 12px; /* Font size */
    margin-bottom: 15px; /* Bottom margin */
}

.property-conditon {
    display: inline-block; /* Inline block */
    background:rgb(237, 55, 5); /* Background color */
    color: white; /* Text color */
    padding: 3px 8px; /* Padding */
    border-radius: 4px; /* Rounded corners */
    font-size: 12px; /* Font size */
    margin-bottom: 15px; /* Bottom margin */
}

.property-actions, .admin-actions {
    display: flex; /* Flex layout */
    gap: 10px; /* Gap between items */
    margin-bottom: 15px; /* Bottom margin */
}

.admin-actions {
    padding-top: 10px; /* Top padding */
    border-top: 1px solid #eee; /* Top border */
}

.btn-view, .btn-contact, .btn-submit, .btn-update, .btn-delete {
    padding: 8px 15px; /* Padding */
    border: none; /* No border */
    border-radius: 4px; /* Rounded corners */
    cursor: pointer; /* Pointer cursor */
    font-size: 14px; /* Font size */
    display: flex; /* Flex layout */
    align-items: center; /* Align items to center */
    gap: 5px; /* Gap between items */
}

.btn-view {
    background: #4CAF50; /* Background color */
    color: white; /* Text color */
}

.btn-contact {
    background: white; /* Background color */
    color: #4CAF50; /* Text color */
    border: 1px solid #4CAF50; /* Border */
}

.btn-submit {
    background: #2196F3; /* Background color */
    color: white; /* Text color */
}

.btn-update {
    background: #FFC107; /* Background color */
    color: white; /* Text color */
}

.btn-delete {
    background: #f44336; /* Background color */
    color: white; /* Text color */
}

.btn-view:hover, .btn-submit:hover, .btn-update:hover, .btn-delete:hover {
    opacity: 0.9; /* Hover effect */
}

.btn-contact:hover {
    background: #4CAF50; /* Background color on hover */
    color: white; /* Text color on hover */
}

.agent-info {
    display: flex; /* Flex layout */
    align-items: center; /* Align items to center */
    gap: 10px; /* Gap between items */
    font-size: 14px; /* Font size */
    color: #666; /* Text color */
    margin-top: 10px; /* Top margin */
    padding-top: 10px; /* Top padding */
    border-top: 1px solid #eee; /* Top border */
}

.agent-info img {
    width: 30px; /* Image width */
    height: 30px; /* Image height */
    border-radius: 50%; /* Circular image */
    object-fit: cover; /* Cover image */
}

@media (max-width: 768px) {
    .property-list {
        grid-template-columns: 1fr; /* One column layout */
    }
    
    .property-card {
        margin: 10px; /* Margin */
    }

    .property-actions, .admin-actions {
        flex-direction: column; /* Column layout */
    }
}

.card{
    border: none;
    padding: 20px;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3);
    border-radius: 5px;
}
</style>

<script>
function submitProperty(propertyId) {
    if(confirm('Are you sure you want to submit this property?')) {
        // Add your submit logic here
        console.log('Submitting property:', propertyId); // Log submission
    }
}

function updateProperty(propertyId) {
    window.location.href = 'agent_edit_property.php?id=' + propertyId; // Redirect to edit property
}

function deleteProperty(propertyId) {
    if(confirm('Are you sure you want to delete this property?')) {
        // Add your delete logic here
        console.log('Deleting property:', propertyId); // Log deletion
    }
}

function viewDetails(propertyId) {
    // Add your view details logic here
    console.log('Viewing details for property:', propertyId); // Log view details
}

function contactAgent(userId) {
    // Add your contact agent logic here
    console.log('Contacting agent:', userId); // Log contact agent
}
</script>
                        </div>
                    </div>

                    <!-- PROPERTY CARD -->

                    <!-- Add the floating button -->
                    <button id="mapButton" class="floating-map-btn" onclick="toggleMap()">
                        <i class="fas fa-map-marker-alt"></i> <!-- Map button icon -->
                    </button>

                    <!-- Add the map panel -->
                    <div id="mapPanel" class="map-panel" style="margin-top: 65px;">
                        <div class="map-controls" style="margin-top: 50px;">
                        <button class="map-control-btn" onclick="toggleFullscreen()">
                            <i class="fas fa-expand"></i> 
                            
                            <button class="map-control-btn" onclick="toggleMap()">
                                <i class="fas fa-times"></i> <!-- Close map button icon -->
                            </button>
                        </div>
                        <div id="agentPropertyMaps" style="height: 100vh;"></div> <!-- Map container -->
                    </div>

                    <style>
                        .floating-map-btn {
                            position: fixed; /* Fixed position */
                            bottom: 30px; /* Bottom position */
                            right: 30px; /* Right position */
                            background: rgba(255, 255, 255, 0.9); /* Background color */
                            color: #666; /* Text color */
                            border: 1px solid #ddd; /* Border */
                            border-radius: 50%; /* Circular button */
                            width: 45px; /* Button width */
                            height: 45px; /* Button height */
                            display: flex; /* Flex layout */
                            align-items: center; /* Align items to center */
                            justify-content: center; /* Center content */
                            cursor: pointer; /* Pointer cursor */
                            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Shadow effect */
                            z-index: 1000; /* Z-index */
                            transition: all 0.3s ease; /* Transition effect */
                        }

                        .map-panel {
                            position: fixed; /* Fixed position */
                            top: 0; /* Top position */
                            right: -50%; /* Off-screen initially */
                            width: 50%; /* Width */
                            height: 100vh; /* Full height */
                            background: white; /* Background color */
                            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1); /* Shadow effect */
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); /* Transition effect */
                            z-index: 999; /* Z-index */
                        }

                        .map-panel.active {
                            right: 0; /* Slide in */
                        }

                        .map-panel.fullscreen {
                            width: 100% !important; /* Fullscreen width */
                            height: 100vh !important; /* Fullscreen height */
                            right: 0; /* Slide in */
                            top: 0; /* Top position */
                            z-index: 1001; /* Z-index */
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); /* Transition effect */
                        }

                        .map-controls {
                            position: absolute; /* Absolute position */
                            top: 10px; /* Top position */
                            left: 10px; /* Left position */
                            display: flex; /* Flex layout */
                            gap: 10px; /* Gap between items */
                            z-index: 1002; /* Z-index */
                        }

                        .map-control-btn {
                            background: white; /* Background color */
                            border: none; /* No border */
                            border-radius: 4px; /* Rounded corners */
                            width: 32px; /* Button width */
                            height: 32px; /* Button height */
                            display: flex; /* Flex layout */
                            align-items: center; /* Align items to center */
                            justify-content: center; /* Center content */
                            cursor: pointer; /* Pointer cursor */
                            box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Shadow effect */
                            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); /* Transition effect */
                        }

                        .map-control-btn:hover {
                            background: #f5f5f5; /* Background color on hover */
                            transform: translateY(-2px); /* Hover effect */
                            box-shadow: 0 3px 6px rgba(0,0,0,0.15); /* Shadow effect on hover */
                        }

                        .map-control-btn i {
                            transition: transform 0.3s ease; /* Transition effect for icon */
                        }

                        .map-control-btn:active i {
                            transform: scale(0.9); /* Scale down on click */
                        }

                        /* Add animation for fullscreen icon */
                        .fa-expand, .fa-compress {
                            transition: transform 0.3s ease;
                        }

                        .fullscreen .fa-expand {
                            transform: rotate(180deg); 
                        }

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

                        .gm-ui-hover-effect{
                            display: none;
                        }
                    </style>


<script>
    window.map = null;
    window.allowedBounds = null;
    let infoWindows = []; // Store all InfoWindows
    let showInfo = false; // Track InfoWindow visibility

    function initMap() {
        const caviteCenter = { lat: 14.2794, lng: 120.8786 };

        window.allowedBounds = new google.maps.LatLngBounds(
            { lat: 14.1325, lng: 120.6750 },
            { lat: 14.5050, lng: 121.0000 }
        );

        window.map = new google.maps.Map(document.getElementById("agentPropertyMaps"), { 
            center: caviteCenter,
            zoom: 12,
            restriction: {
                latLngBounds: window.allowedBounds,
                strictBounds: true
            },
            mapTypeControl: true // Enable map/satellite toggle
        });

        fetch('../../backend/get_properties.php')
            .then(response => response.json())
            .then(properties => {
                if (!Array.isArray(properties)) {
                    console.error("Invalid data format:", properties);
                    return;
                }
     
                properties.forEach(property => {
                    const { latitude, longitude, property_name, property_type, sale_price, sale_or_lease } = property;

                    if (!latitude || !longitude || isNaN(latitude) || isNaN(longitude)) {
                        console.warn(`Skipping property: ${property_name} (Invalid coordinates)`);
                        return;
                    }

                    const propertyLocation = new google.maps.LatLng(parseFloat(latitude), parseFloat(longitude));

                    if (!window.allowedBounds.contains(propertyLocation)) {
                        console.warn(`Skipping property: ${property_name} (Out of Cavite bounds)`);
                        return;
                    }

                    // Determine the correct status (For Sale / For Lease)
                    let statusText = "N/A";
                    if (sale_or_lease) {
                        statusText = sale_or_lease.toLowerCase() === 'lease' ? 'For Lease' :
                                     sale_or_lease.toLowerCase() === 'sale' ? 'For Sale' : 'N/A';
                    }

                    // Create a marker
                    const marker = new google.maps.Marker({
                        position: propertyLocation,
                        map: window.map,
                        title: property_name
                    });

                    // Create an InfoWindow without a close button
                    const infoWindow = new google.maps.InfoWindow({
                        content: `<div style="white-space: nowrap;">
                                    <img src="../../assets/property_images/${property.image_name}" alt="${property.property_name}" style="width: 100%; height: 100px; object-fit: cover; border-radius: 5px; margin-bottom: 10px;"><br>
                                    <strong>${property_name}</strong><br>
                                    <b>Type:</b> ${property_type || 'N/A'}<br>
                                    <b>Status:</b> ${statusText}<br>
                                    <b>Price:</b> ₱${sale_price ? parseInt(sale_price).toLocaleString("en-PH") : 'N/A'}
                                </div>`,
                        disableAutoPan: true // Prevents auto-panning when opened
                    });

                    // Remove or hide the close button from the InfoWindow when it's opened
                    google.maps.event.addListener(infoWindow, 'domready', function () {
                        // Target all close buttons and hide them
                        const closeButtons = document.querySelectorAll('.gm-ui-hover-effect');
                        closeButtons.forEach(button => {
                            button.style.display = 'none'; // Hide each close button
                        });
                    });

                    // Store InfoWindow for toggling
                    infoWindows.push({ marker, infoWindow });

                    // Open InfoWindow only if "Show Info" is enabled
                    if (showInfo) {
                        infoWindow.open(window.map, marker);
                    }

                    // Open InfoWindow when marker is clicked
                    marker.addListener("click", () => {
                        if (showInfo) {
                            infoWindow.close(); // Close the InfoWindow if it's currently open
                        } else {
                            infoWindow.open(window.map, marker); // Open the InfoWindow
                        }
                    });
                });
            })
            .catch(error => console.error("Error fetching properties:", error));

        // Add "Show Info" toggle button next to Maps/Satellite toggle
        const showInfoControl = document.createElement("button");
        showInfoControl.textContent = "Show All";
        showInfoControl.classList.add("show-info-btn");

        // Apply styles
        showInfoControl.style.fontSize = "14px"; // Bigger text
        showInfoControl.style.fontWeight = "bold";
        showInfoControl.style.margin = "8px"; // Adjust spacing
        showInfoControl.style.padding = "12px 20px"; // Bigger button
        showInfoControl.style.background = "#fff"; // White background
        showInfoControl.style.border = "1px solid #ccc"; // Border
        showInfoControl.style.cursor = "pointer";
        showInfoControl.style.borderRadius = "5px"; // Rounded corners
        showInfoControl.style.boxShadow = "0 2px 4px rgba(0,0,0,0.2)"; // Add slight shadow

        showInfoControl.addEventListener("click", () => {
            showInfo = !showInfo; // Toggle state
            infoWindows.forEach(({ marker, infoWindow }) => {
                if (showInfo) {
                    infoWindow.open(window.map, marker); // Open all InfoWindows
                } else {
                    infoWindow.close(); // Close all InfoWindows
                }
            });

            // Toggle the button text between "Show All" and "Hide All"
            if (showInfo) {
                showInfoControl.textContent = "Hide All";
            } else {
                showInfoControl.textContent = "Show All";
            }
        });

        // Add the button to the map, positioning it on the left near Map/Satellite toggle
        window.map.controls[google.maps.ControlPosition.TOP_LEFT].push(showInfoControl);
    }

    // ✅ Ensures `initMap()` runs correctly
    google.maps.event.addDomListener(window, 'load', initMap);

    window.toggleMap = function() {
        const mapPanel = document.getElementById('mapPanel');
        if (mapPanel) {
            mapPanel.classList.toggle('active'); 
        }

        if (window.map) {
            setTimeout(() => {
                google.maps.event.trigger(window.map, 'resize');
            }, 300);
        }
    };

    window.toggleFullscreen = function() {
        const mapPanel = document.getElementById('mapPanel');
        const fullscreenIcon = document.querySelector('.map-control-btn i.fa-expand, .map-control-btn i.fa-compress');

        if (mapPanel) {
            mapPanel.classList.toggle('fullscreen'); 

            if (fullscreenIcon) {
                if (mapPanel.classList.contains('fullscreen')) {
                    fullscreenIcon.classList.remove('fa-expand');
                    fullscreenIcon.classList.add('fa-compress');
                } else {
                    fullscreenIcon.classList.remove('fa-compress');
                    fullscreenIcon.classList.add('fa-expand');
                }
            }

            if (window.map) {
                setTimeout(() => {
                    google.maps.event.trigger(window.map, 'resize');
                }, 300);
            }
        }
    };
</script>

                    
                    <!-- CREATE PROPERTY LIST -->

                    <div id="create_property" class="tab-pane">
                        <div class="d-flex align-items-center mb-4">
                            <!-- Post new land property -->
                            <h3 class="mb-1 mr-5">Post New Land</h3> <!-- Title for posting new land -->

                            <!-- Tab-like Button -->
                        </div>
                        <form id="propertyForm" method="POST" enctype="multipart/form-data"> <!-- Property form -->
                            <div class="row">
                                <div class="col-md-6 order-md-2 card">
                                
                                <div id="search-container" class="mb-3">
                                    <input id="searchBox" type="text" class="form-control" placeholder="Search location">
                                </div>                              
                                <div id="map"></div>

                                <!-- Hidden Inputs for Lat & Lng -->
                                <input type="hidden" id="latitude" name="latitude">
                                <input type="hidden" id="longitude" name="longitude">



                                </div>

                                <div class="col-md-6 order-md-1">
                                    <!-- Property Type Dropdown (styled like the Property Name input) -->
                                    <div class="form-group">
                                        <label for="propertyType">Land Type</label> <!-- Land type label -->
                                        <select name="propertyType" class="form-control" id="propertyType"> <!-- Land type dropdown -->
                                            <option value="" selected>Select Land Type</option> <!-- Default option -->
                                            <option value="House and Lot">House and Lot</option> <!-- House and Lot option -->
                                            <option value="Agricultural Farm">Agricultural Farm</option> <!-- Agricultural Farm option -->
                                            <option value="Commercial Lot">Commercial Lot</option> <!-- Commercial Lot option -->
                                            <option value="Raw Land">Raw Land</option> <!-- Raw Land option -->
                                            <option value="Residential Land">Residential Land</option> <!-- Residential Land option -->
                                            <option value="Residential Farm">Residential Farm</option> <!-- Residential Farm option -->
                                            <option value="Memorial Lot">Memorial Lot</option> <!-- Memorial Lot option -->
                                        </select>
                                    </div>

                                    <!-- Property Name -->
                                    <div class="form-group">
                                        <label for="propertyName">Property Name</label> <!-- Property name label -->
                                        <input name="propertyName" type="text" class="form-control" id="propertyName"
                                            placeholder="Enter Property Name" required
                                            oninput="this.value = this.value.replace(/\b\w/g, l => l.toUpperCase())"> <!-- Property name input with auto-uppercase -->
                                    </div>

                                    <!-- Property Location -->
                                    
                                    <script>
                                        document.getElementById('propertyLocation').addEventListener('change', function() {
                                            const selectedValue = this.value;
                                            const selectedCity = selectedValue.split(',')[0]; // Get city name before comma
                                            const barangayOptions = document.querySelectorAll('optgroup[label^="Barangays"]');
                                            
                                            barangayOptions.forEach(optgroup => {
                                                optgroup.style.display = 'none';
                                                const cityInLabel = optgroup.label.split('-')[1].trim(); // Get city name after dash
                                                if (cityInLabel.toLowerCase().includes(selectedCity.toLowerCase())) {
                                                    optgroup.style.display = 'block';
                                                }
                                            });

                                            // Reset to first option if a city/municipality is selected
                                            if (selectedValue !== 'all') {
                                                this.value = selectedValue;
                                            }
                                        });
                                    </script>

                                    <!-- Listing Type and Land Area -->
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="saleOrLease">Listing Type</label> <!-- Listing type label -->
                                            <select name="saleOrLease" class="form-control" id="saleOrLease" required> <!-- Listing type dropdown -->
                                                <option value="">Select Type</option> <!-- Default option -->
                                                <option value="sale">For Sale</option> <!-- For Sale option -->
                                                <option value="lease">For Lease</option> <!-- For Lease option -->
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="landArea">Land Area (sqm.)</label>
                                            <input name="landArea" type="number" class="form-control" id="landArea"
                                                placeholder="Enter land area in sqm" required>
                                            <small id="landAreaValidationMessage" class="text-danger"></small>
                                        </div>

                                        <script>
                                            document.getElementById('landArea').addEventListener('input', function(e) {
                                                // Remove any dashes from input
                                                this.value = this.value.replace(/-/g, '');
                                                
                                                const message = document.getElementById('landAreaValidationMessage');
                                                // 1 hectare = 10,000 square meters
                                                // 1000 hectares = 10,000,000 square meters
                                                const maxArea = 10000000; // 1000 hectares in square meters
                                                
                                                if (this.value > maxArea) {
                                                    message.textContent = "Land area cannot exceed 1000 hectares (10,000,000 sqm)";
                                                    this.value = maxArea;
                                                } else if (this.value < 0) {
                                                    message.textContent = "Land area cannot be negative";
                                                    this.value = 0;
                                                } else {
                                                    message.textContent = "";
                                                }
                                            });
                                        </script>

                                        <div id="leaseForm" class="hidden">
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="leaseDuration" 
                                                        class="block text-gray-700 font-bold mb-2">Lease
                                                        Term</label>
                                                    <select name="leaseDuration" class="form-control" id="leaseDuration"
                                                        required>
                                                        <option value="select">Select Term Type</option>
                                                        <option value="short_term">Short term less than 1 year</option>
                                                        <option value="long_term">Long term more than 1 year</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="monthlyRent"
                                                        class="block text-gray-700 font-bold mb-2">Monthly Rental
                                                        Cost</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">₱</span>
                                                        </div>
                                                        <input type="number" id="monthlyRent" name="monthlyRent"
                                                            class="form-control" placeholder="Enter land monthly rent">
                                                    </div>
                                                    <small id="rentValidationMessage" class="text-danger"></small>
                                                </div>
                                            </div>
                                            <script>
                                                document.getElementById('leaseDuration').addEventListener('change', function() {
                                                    const monthlyRent = document.getElementById('monthlyRent');
                                                    const message = document.getElementById('rentValidationMessage');
                                                    
                                                    if (this.value === 'select') {
                                                        monthlyRent.value = '';
                                                        monthlyRent.disabled = true;
                                                        message.textContent = '';
                                                    } else if (this.value === 'short term') {
                                                        monthlyRent.disabled = false;
                                                        monthlyRent.max = "1000000";
                                                        message.textContent = "Maximum rental cost for short term is ₱1,000,000"; 
                                                    } else if (this.value === 'long term') {
                                                        monthlyRent.disabled = false;
                                                        monthlyRent.max = "1000000000";
                                                        message.textContent = "Maximum rental cost for long term is ₱1,000,000,000";
                                                    }
                                                });

                                                document.getElementById('monthlyRent').addEventListener('input', function(e) {
                                                    // Remove any dashes from input
                                                    this.value = this.value.replace(/-/g, '');
                                                    
                                                    const leaseDuration = document.getElementById('leaseDuration').value;
                                                    const message = document.getElementById('rentValidationMessage');
                                                    
                                                    if (leaseDuration === 'select') {
                                                        this.value = '';
                                                        message.textContent = "Please select a lease term first";
                                                    } else if (this.value < 0) {
                                                        message.textContent = "Monthly rent cannot be negative";
                                                        this.value = 0;
                                                    } else if (leaseDuration === 'short term' && this.value > 1000000) {
                                                        message.textContent = "Amount exceeds maximum limit for short term (₱1,000,000)";
                                                        this.value = 1000000;
                                                    } else if (leaseDuration === 'long term' && this.value > 1000000000) {
                                                        message.textContent = "Amount exceeds maximum limit for long term (₱1,000,000,000)";
                                                        this.value = 1000000000;
                                                    } else {
                                                        message.textContent = "";
                                                    }
                                                });
                                            </script>

                                        </div>
                                        <div id="saleForm" class="hidden">
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="landCondition"
                                                        class="block text-gray-700 font-bold mb-2">Land
                                                        condition</label> <!-- Land condition label -->
                                                    <select name="landCondition" class="form-control" id="landCondition"
                                                        required> <!-- Land condition dropdown -->
                                                        <option value="" selected>Select Land Condition</option> <!-- Default option -->
                                                        <option value="resale">Resale</option> <!-- Resale option -->
                                                        <option value="foreClose">Foreclose/Acquired Assets</option> <!-- Foreclose option -->
                                                        <option value="pasalo">Pasalo/Assumed Balance</option> <!-- Pasalo option -->
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="salePrice"
                                                        class="block text-gray-700 font-bold mb-2">Price</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">₱</span>
                                                        </div>
                                                        <input type="number" id="salePrice" name="salePrice"
                                                            class="form-control" placeholder="Enter sale price">
                                                    </div>
                                                    <small id="priceValidationMessage" class="text-danger"></small>
                                                </div>
                                                <script>
                                                    document.getElementById('salePrice').addEventListener('input', function() {
                                                        const message = document.getElementById('priceValidationMessage');
                                                        const maxPrice = 1000000000; // 1 billion
                                                        
                                                        if (this.value > maxPrice) {
                                                            message.textContent = "Price cannot exceed ₱1,000,000,000";
                                                            this.value = maxPrice;
                                                        } else if (this.value < 0) {
                                                            message.textContent = "Price cannot be negative";
                                                            this.value = 0;
                                                        } else {
                                                            message.textContent = "";
                                                        }
                                                    });
                                                </script>
                                                
                                                <div class="form-group col-md-6">
                                                    <label for="anotherInfo"
                                                        class="block text-gray-700 font-bold mb-2">Another
                                                        Information</label>
                                                    <select name="anotherInfo" class="form-control" id="anotherInfo"
                                                        required>
                                                        <option value="" selected>All Types</option>
                                                        <option value="cleanTitle">Clean Title</option>
                                                        <option value="DisPromo">Discounted/Promo</option>
                                                        <option value="pagibig">Pag-IBIG Accredited</option>
                                                        <option value="fsbo">For Sale by Owner</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="propertyDescription">Description</label>
                                        <textarea name="propertyDescription" class="form-control"
                                            id="propertyDescription" rows="3" placeholder="Enter description"
                                            required
                                            oninput="this.value = this.value.replace(/(?:^|\.\s+)([a-z])/g, function(match, letter) { return match.toUpperCase(); })"></textarea>
                                    </div>

                                    <!-- Upload Images -->
                                    <div class="form-group">
                                        <label for="propertyImages">Upload Images</label>
                                        <div class="custom-file">
                                            <input type="file" name="images[]" class="custom-file-input"
                                                id="propertyImages" multiple accept="image/*">
                                            <label class="custom-file-label" for="propertyImages">Choose images</label>
                                        </div>
                                        <small class="form-text text-muted">Allowed files: JPG, PNG, JPEG, GIF, and
                                            other image formats size: 5 mb</small>
                                    </div>

                                    <!-- Preview Images Section -->
                                    <div id="imagePreviewContainer" class="border rounded mt-3 p-2" style="display: none;">
                                        <h5 class="mb-3">Uploaded Images</h5>
                                        <div class="alert alert-info" role="alert">
                                            <i class="fas fa-info-circle"></i> 
                                            <strong>Tip:</strong> Drag images to reorder them. First image will be the main land property image.
                                        </div>
                                        <div id="imagePreview" class="d-flex flex-wrap gap-3">
                                            <!-- Image previews will appear here -->
                                        </div>
                                    </div>

                                    <style>
                                        .draggable {
                                            cursor: move;
                                            transition: transform 0.2s;
                                        }
                                        
                                        .draggable.dragging {
                                            opacity: 0.5;
                                            transform: scale(0.95);
                                        }

                                        .draggable:hover {
                                            transform: translateY(-2px);
                                            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                                        }

                                        .drag-over {
                                            border: 2px dashed #4CAF50;
                                        }

                                        #imagePreview {
                                            min-height: 150px;
                                            padding: 10px;
                                            border-radius: 4px;
                                        }

                                        .img-order-number {
                                            position: absolute;
                                            top: -10px;
                                            left: -10px;
                                            background: #4CAF50;
                                            color: white;
                                            width: 24px;
                                            height: 24px;
                                            border-radius: 50%;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            font-size: 12px;
                                            font-weight: bold;
                                            z-index: 11;
                                        }
                                    </style>

                                    <script>
                                        const MAX_IMAGES = 10; // Maximum number of images allowed

                                        // Modify the file input change handler
                                        document.getElementById('propertyImages').addEventListener('change', function(event) {
                                            const currentImageCount = document.querySelectorAll('#imagePreview .draggable').length;
                                            const remainingSlots = MAX_IMAGES - currentImageCount;
                                            const selectedFiles = Array.from(event.target.files);

                                            if (selectedFiles.length > remainingSlots) {
                                                alert(`You can only add ${remainingSlots} more image(s). Maximum limit is ${MAX_IMAGES} images.`);
                                                // Reset the file input
                                                this.value = '';
                                                return;
                                            }
                                        });

                                        function updateImageOrder() {
                                            const images = document.querySelectorAll('#imagePreview .draggable');
                                            images.forEach((img, index) => {
                                                // Update or create order number element
                                                let orderNumber = img.querySelector('.img-order-number');
                                                if (!orderNumber) {
                                                    orderNumber = document.createElement('div');
                                                    orderNumber.className = 'img-order-number';
                                                    img.appendChild(orderNumber);
                                                }
                                                orderNumber.textContent = index + 1;
                                                
                                                // Make number 1 red for front image
                                                if (index === 0) {
                                                    orderNumber.style.backgroundColor = '#ff0000'; // Red background
                                                } else {
                                                    orderNumber.style.backgroundColor = '#4CAF50'; // Reset to default green
                                                }
                                            });
                                        }

                                        function handleDragStart(e) {
                                            this.classList.add('dragging');
                                            e.dataTransfer.effectAllowed = 'move';
                                            e.dataTransfer.setData('text/plain', Array.from(this.parentNode.children).indexOf(this));
                                        }

                                        function handleDragOver(e) {
                                            e.preventDefault();
                                            const draggable = document.querySelector('.dragging');
                                            const container = document.getElementById('imagePreview');
                                            const afterElement = getDragAfterElement(container, e.clientY);
                                            
                                            if (afterElement == null) {
                                                container.appendChild(draggable);
                                            } else {
                                                container.insertBefore(draggable, afterElement);
                                            }
                                        }

                                        function getDragAfterElement(container, y) {
                                            const draggableElements = [...container.querySelectorAll('.draggable:not(.dragging)')];

                                            return draggableElements.reduce((closest, child) => {
                                                const box = child.getBoundingClientRect();
                                                const offset = y - box.top - box.height / 2;
                                                
                                                if (offset < 0 && offset > closest.offset) {
                                                    return { offset: offset, element: child };
                                                } else {
                                                    return closest;
                                                }
                                            }, { offset: Number.NEGATIVE_INFINITY }).element;
                                        }

                                        function handleDragEnd() {
                                            this.classList.remove('dragging');
                                            updateImageOrder();
                                        }

                                        // Add event listeners to container
                                        const container = document.getElementById('imagePreview');
                                        container.addEventListener('dragover', handleDragOver);
                                        
                                        // Observe DOM changes to add event listeners to new elements
                                        const observer = new MutationObserver((mutations) => {
                                            mutations.forEach((mutation) => {
                                                mutation.addedNodes.forEach((node) => {
                                                    if (node.classList && node.classList.contains('draggable')) {
                                                        node.addEventListener('dragstart', handleDragStart);
                                                        node.addEventListener('dragend', handleDragEnd);
                                                    }
                                                });
                                            });
                                            updateImageOrder();
                                        });

                                        observer.observe(container, { 
                                            childList: true 
                                        });
                                    </script>
                                     <script>
                                    let map;
                                    let marker;
                                    let allowedBounds;

                                    function initMap() {
                                        const caviteCenter = { lat: 14.2794, lng: 120.8786 }; // Center of Cavite

                                        // Define boundaries for Cavite
                                        allowedBounds = new google.maps.LatLngBounds(
                                            { lat: 14.1325, lng: 120.6750 }, // Southwest (Ternate)
                                            { lat: 14.5050, lng: 121.0000 }  // Northeast (Bacoor)
                                        );

                                        // Initialize the map
                                        map = new google.maps.Map(document.getElementById("map"), {
                                            center: caviteCenter,
                                            zoom: 12,
                                            restriction: {
                                                latLngBounds: allowedBounds,
                                                strictBounds: true
                                            }
                                        });

                                        // Initialize marker
                                        marker = new google.maps.Marker({
                                            position: caviteCenter,
                                            map: map,
                                            draggable: true
                                        });

                                        function updateLatLng(lat, lng) {
                                            document.getElementById("latitude").value = lat;
                                            document.getElementById("longitude").value = lng;
                                        }

                                        // Restrict marker movement within Cavite
                                        marker.addListener("dragend", () => {
                                            let newPos = marker.getPosition();
                                            if (allowedBounds.contains(newPos)) {
                                                updateLatLng(newPos.lat(), newPos.lng());
                                            } else {
                                                marker.setPosition(caviteCenter); // Reset marker if out of bounds
                                            }
                                        });

                                        // Click event to place marker (inside bounds only)
                                        map.addListener("click", (event) => {
                                            let clickedLocation = event.latLng;
                                            if (allowedBounds.contains(clickedLocation)) {
                                                marker.setPosition(clickedLocation);
                                                updateLatLng(clickedLocation.lat(), clickedLocation.lng());
                                            }
                                        });

                                        // Search Box with Restriction to Cavite
                                        const searchBox = new google.maps.places.Autocomplete(document.getElementById("searchBox"), {
                                            componentRestrictions: { country: "PH" }, // Restrict to the Philippines
                                            bounds: allowedBounds, // Bias results to Cavite
                                            strictBounds: true // Force results inside Cavite
                                        });

                                        searchBox.addListener("place_changed", function () {
                                            let place = searchBox.getPlace();
                                            if (!place.geometry) return;

                                            // Ensure the selected place is inside Cavite
                                            let placeAddress = place.formatted_address || "";
                                            if (!placeAddress.includes("Cavite")) {
                                                document.getElementById("searchBox").value = ""; // Clear input if outside Cavite
                                                return;
                                            }

                                            // Move map and marker to the searched location
                                            map.setCenter(place.geometry.location);
                                            map.setZoom(14);
                                            marker.setPosition(place.geometry.location);
                                            updateLatLng(place.geometry.location.lat(), place.geometry.location.lng());
                                        });

                                        // Get and update user's live location
                                        if (navigator.geolocation) {
                                            navigator.geolocation.watchPosition(
                                                (position) => {
                                                    let userLocation = new google.maps.LatLng(
                                                        position.coords.latitude,
                                                        position.coords.longitude
                                                    );

                                                    if (allowedBounds.contains(userLocation)) {
                                                        // Move marker to user location
                                                        marker.setPosition(userLocation);
                                                        updateLatLng(userLocation.lat(), userLocation.lng());
                                                        map.setCenter(userLocation);
                                                    }
                                                },
                                                (error) => console.warn("Error in getting location: ", error),
                                                {
                                                    enableHighAccuracy: true,
                                                    maximumAge: 0
                                                }
                                            );
                                        } else {
                                            console.warn("Geolocation is not supported by this browser.");
                                        }

                                        // Prevent users from panning outside Cavite
                                        google.maps.event.addListener(map, 'dragend', function () {
                                            if (!allowedBounds.contains(map.getCenter())) {
                                                map.setCenter(caviteCenter);
                                            }
                                        });
                                    }

                                    window.onload = initMap;
                                </script>
                                </div>
                            </div>

                            <input type="hidden" name="coordinates" id="coordinates" value="">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span id="btnText">Submit</span>
                                <span id="loadingSpinner" class="spinner-border spinner-border-sm d-none" role="status"
                                    aria-hidden="true"></span>
                            </button>
                        </form>
                    </div>
                    <!-- Cend of the land property form -->
                </div>
            </div>
        </div>
    </div>

    <div class="az-footer">
        <div class="container">
            <span class="text-muted d-block text-center">Copyright ©LoremIpsum 2024</span>
        </div>
    </div>

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

    <!-- Success Modal create property -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content-popup">
                <div class="modal-body text-center">
                    <div class="checkmark-wrapper">
                        <i class="fas fa-check-circle checkmark-icon"></i>
                    </div>
                    <p class="modal-message">Property successfully added.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" id="closeModalBtn">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/lib/jquery/jquery.min.js"></script>
    <script src="../../assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/lib/ionicons/ionicons.js"></script>
    <script src="../../assets/js/azia.js"></script>

    <script src="../../assets/js/addedFunctions.js"></script>

    <script src="https://unpkg.com/maplibre-gl@latest/dist/maplibre-gl.js"></script>


    <script>
        // Update the label on file selection
        document.querySelector('.custom-file-input').addEventListener('change', function(e) {
            const fileNames = Array.from(e.target.files).map(file => file.name);
            const label = e.target.nextElementSibling;
            label.classList.add('selected');
            label.innerHTML = fileNames.length > 2 ? `${fileNames[0]}, ${fileNames[1]}, +${fileNames.length - 2} more` : fileNames.join(', ');
        });
    </script>

    <script>
        document.getElementById('mapButton').addEventListener('click', function() {
            var mapContainer = document.getElementById('mapContainer');
            var propertyList = document.querySelector('.property-list');

            // Toggle the map container visibility
            mapContainer.classList.toggle('open');

            // Adjust the property list layout: switch to 1 column when the map is shown
            if (mapContainer.classList.contains('open')) {
                propertyList.classList.add('one-column');
            } else {
                propertyList.classList.remove('one-column');
            }
        });
    </script>

    <!--Label Changer in listing type, for sale or for lease-->
    <script>
        document.getElementById('saleOrLease').addEventListener('change', function() {
            const priceLabel = document.getElementById('priceLabel');
            const priceInput = document.getElementById('propertyPrice');

            if (this.value === 'lease') {
                priceLabel.textContent = 'Monthly Rate'; // Change label to "Monthly Rate"
                priceInput.placeholder = 'Enter monthly rate'; // Change placeholder
            } else if (this.value === 'sale') {
                priceLabel.textContent = 'Price'; // Change label back to "Price"
                priceInput.placeholder = 'Enter price'; // Change placeholder
            }
        });
    </script>
    
    <script>
    const timeElement = document.getElementById('time');
        if (timeElement) {
            timeElement.textContent = "New Time";
        }
    </script>


    <!--Image preview-->
    <script>
        let imageFiles = [];

        document.getElementById('propertyImages').addEventListener('change', function(event) {
            const previewContainer = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('imagePreview');

            if (event.target.files.length > 0) {
                previewContainer.style.display = 'block';

                Array.from(event.target.files).forEach(file => {
                    imageFiles.push(file);
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const imgElement = document.createElement('img');
                        imgElement.src = e.target.result;
                        imgElement.classList.add('img-thumbnail', 'm-2');
                        imgElement.style.maxWidth = '150px';
                        imgElement.style.maxHeight = '150px';

                        // Set attributes for drag-and-drop
                        const wrapper = document.createElement('div');
                        wrapper.classList.add('position-relative', 'draggable');
                        wrapper.setAttribute('draggable', 'true');
                        wrapper.style.cursor = 'grab';

                        const deleteButton = document.createElement('button');
                        deleteButton.innerHTML = '&times;';
                        deleteButton.classList.add('btn', 'btn-danger', 'btn-sm', 'position-absolute', 'top-0', 'end-0');
                        deleteButton.style.zIndex = '10';
                        deleteButton.style.width = '30px';
                        deleteButton.style.height = '30px';
                        deleteButton.style.borderRadius = '50%';
                        deleteButton.style.fontSize = '18px';
                        deleteButton.style.padding = '0';
                        deleteButton.style.lineHeight = '30px';
                        deleteButton.style.textAlign = 'center';

                        deleteButton.onclick = function() {
                            const index = imageFiles.indexOf(file);
                            if (index > -1) {
                                imageFiles.splice(index, 1);
                            }
                            wrapper.remove();

                            if (imageFiles.length === 0) {
                                previewContainer.style.display = 'none';
                            }
                        };

                        wrapper.appendChild(imgElement);
                        wrapper.appendChild(deleteButton);
                        preview.appendChild(wrapper);

                        // Drag-and-drop functionality
                        wrapper.addEventListener('dragstart', function(event) {
                            event.dataTransfer.setData('text/plain', imageFiles.indexOf(file));
                            wrapper.style.opacity = '0.5';
                        });

                        wrapper.addEventListener('dragover', function(event) {
                            event.preventDefault();
                        });

                        wrapper.addEventListener('drop', function(event) {
                            event.preventDefault();
                            const draggedIndex = event.dataTransfer.getData('text/plain');
                            const targetIndex = imageFiles.indexOf(file);

                            if (draggedIndex !== targetIndex) {
                                const draggedFile = imageFiles[draggedIndex];
                                imageFiles.splice(draggedIndex, 1);
                                imageFiles.splice(targetIndex, 0, draggedFile);

                                // Reorder elements in the DOM
                                const draggedElement = preview.children[draggedIndex];
                                preview.insertBefore(draggedElement, wrapper);
                            }
                        });

                        wrapper.addEventListener('dragend', function() {
                            wrapper.style.opacity = '1';
                        });
                    };

                    reader.readAsDataURL(file);
                });
            }
        });
    </script>


    <!--Unauthorized modal-->
    <script>
        $(document).ready(function() {
            var showModal = <?php echo $show_modal ? 'true' : 'false'; ?>;
            var errorMessage = <?php echo json_encode($error_message); ?>;

            if (showModal) {
                $('#warningMessage').text(errorMessage); // Set the error message dynamically
                $('#warningModal').modal({
                    backdrop: 'static', // Prevent closing when clicking outside
                    keyboard: false // Prevent closing when pressing the escape key
                });
                $('#warningModal').modal('show'); // Show the modal
            }

            // Close the modal and redirect to login when the "Sign In" button is clicked
            $('#warningCloseButton').click(function() {
                $('#warningModal').modal('hide');
                window.location.href = '../../frontend/sign_in.php'; // Redirect to the login page
            });
        });
    </script>

    <!--Signout process--->
    <script>
        // Show the sign-out confirmation modal when the Sign Out button is clicked
        document.getElementById('signOutButton').addEventListener('click', function() {
            $('#signOutModal').modal('show');
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



    <!--Time Update-->
    <script>
        // Function to update the current time every second
        function updateTime() {
            const timeElement = document.getElementById('currentTime');

            // Get the current time in Manila timezone
            const now = new Date().toLocaleString("en-US", {
                timeZone: "Asia/Manila"
            });

            // Format the time as hh:mm:ss AM/PM
            const options = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            const timeString = new Date(now).toLocaleTimeString('en-US', options);

            // Update the time on the page
            timeElement.textContent = timeString;
        }

        // Update the time every 1000 milliseconds (1 second)
        setInterval(updateTime, 1000);
    </script>

  

    <!--create property form part-->
    <script>
        document.getElementById('saleOrLease').addEventListener('change', function() {
            var saleForm = document.getElementById('saleForm');
            var leaseForm = document.getElementById('leaseForm');
            var leaseDuration = document.getElementById('leaseDuration');
            var landCondition = document.getElementById('landCondition');
            var anotherInfo = document.getElementById('anotherInfo');

            // Hide both forms initially
            saleForm.style.display = 'none';
            leaseForm.style.display = 'none';

            // Reset the required attribute and visibility for fields
            landCondition.required = false;
            anotherInfo.required = false;
            landCondition.style.display = 'none';
            anotherInfo.style.display = 'none';

            // Show the relevant form based on selection
            if (this.value === 'sale') {
                saleForm.style.display = 'block';
                leaseDuration.required = false; // Disable required for lease duration if sale is selected

                // Show landCondition and anotherInfo for sale
                landCondition.style.display = 'block';
                landCondition.required = true; // Enable required for landCondition when 'For Sale' is selected
                anotherInfo.style.display = 'block';
                anotherInfo.required = true; // Enable required for anotherInfo when 'For Sale' is selected

            } else if (this.value === 'lease') {
                leaseForm.style.display = 'block';
                leaseDuration.required = true; // Enable required for lease duration if lease is selected

                // Hide landCondition and anotherInfo for lease
                landCondition.style.display = 'none';
                anotherInfo.style.display = 'none';
            }
        });

        // Trigger the change event on page load to initialize the form visibility
        document.getElementById('saleOrLease').dispatchEvent(new Event('change'));
    </script>

    <!-- modal if data is sent and modal appear -->
    <script>
       document.getElementById('propertyForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const loadingSpinner = document.getElementById('loadingSpinner');

    submitBtn.disabled = true;
    btnText.textContent = "Submitting...";
    loadingSpinner.classList.remove("d-none");

    const formData = new FormData(this);

    // Ensure latitude & longitude are included
    const latitude = document.getElementById('latitude').value;
    const longitude = document.getElementById('longitude').value;

    if (!latitude || !longitude) {
        alert("Please select a location on the map.");
        submitBtn.disabled = false;
        btnText.textContent = "Submit";
        loadingSpinner.classList.add("d-none");
        return;
    }

    formData.append('latitude', latitude);
    formData.append('longitude', longitude);

    // Add lease duration and land condition to formData
    const saleOrLease = document.getElementById('saleOrLease').value;
    if (saleOrLease === 'lease') {
        const leaseDuration = document.getElementById('leaseDuration').value;
        formData.append('leaseDuration', leaseDuration);
    } else if (saleOrLease === 'sale') {
        const landCondition = document.getElementById('landCondition').value;
        formData.append('landCondition', landCondition);
    }

    try {
        const response = await fetch('../../backend/add_property.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === "success") {
            const successModal = new bootstrap.Modal(document.getElementById('successModal'), {
                backdrop: 'static',
                keyboard: false
            });
            successModal.show();
        } else {
            alert("Error: " + result.message);
        }
    } catch (error) {
        console.error("Submission error:", error);
        alert("Something went wrong!");
    } finally {
        submitBtn.disabled = false;
        btnText.textContent = "Submit";
        loadingSpinner.classList.add("d-none");
    }
});

// Redirect button
document.getElementById('closeModalBtn').addEventListener('click', function() {
    window.location.href = "agent_listing.php"; // Change this to your actual landing page
});

    </script>

<script>
function updateProperty(propertyId) {
    let form = document.querySelector(`#updateForm${propertyId}`);
    let formData = new FormData(form);

    fetch('../../backend/update_properties.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        console.log("Raw Response:", text);

        try {
            let data = JSON.parse(text);
            if (data.status === 'success') {
                alert('Property updated successfully!');
                location.reload();
            } else {
                alert('Error updating property: ' + data.message);
            }
        } catch (error) {
            console.error("JSON Parse Error:", error);
            console.error("Response Received:", text);
            alert("An unexpected error occurred. Please check the console for details.");
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        alert('An unexpected error occurred. Please try again.');
    });
}

</script>

</body>

</html>