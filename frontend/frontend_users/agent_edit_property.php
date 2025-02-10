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
                            <h3 class="mb-1 mr-5">My Land Properties</h3> <!-- Title for posted properties -->
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
            WHERE p.user_id = ?
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

                    <div class="property-actions">
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
                    </div>

                    <div class="admin-actions">
                        <button class="btn-submit" onclick="submitProperty(<?php echo $row['property_id']; ?>)">
                            <i class="fas fa-check"></i> Submit
                        </button>
                        <button class="btn-update" onclick="updateProperty(<?php echo $row['property_id']; ?>)">
                            <i class="fas fa-edit"></i> Update
                        </button>
                        <button class="btn-delete" onclick="deleteProperty(<?php echo $row['property_id']; ?>)">
                            <i class="fas fa-trash"></i> Archive
                        </button>
                    </div>

                    <div class="agent-info">
                        <?php if ($row['user_image']) { ?>
                            <img src="../../assets/images/profile/<?php echo $row['user_image']; ?>" alt="Agent"> <!-- Agent image -->
                        <?php } ?>
                        <span><i class="fas fa-user"> Agent Name:</i> <?php echo htmlspecialchars($agentName); ?></span> <!-- Agent name -->
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
                    <div id="mapPanel" class="map-panel">
                        <div class="map-controls">
                            <button class="map-control-btn" onclick="toggleFullscreen()">
                                <i class="fas fa-expand"></i> <!-- Fullscreen button icon -->
                            </button>
                            <button class="map-control-btn" onclick="toggleMap()">
                                <i class="fas fa-times"></i> <!-- Close map button icon -->
                            </button>
                        </div>
                        <div id="agentPropertyMap" style="width: 100%; height: 100%;"></div> <!-- Map container -->
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
                            transition: transform 0.3s ease; /* Transition effect for icons */
                        }

                        .fullscreen .fa-expand {
                            transform: rotate(180deg); /* Rotate icon */
                        }

                        /* Add animation for property list */
                        .property-list {
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); /* Transition effect */
                            width: 100%; /* Full width */
                        }

                        .property-list.map-active {
                            width: 50%; /* Width when map is active */
                        }

                        .property-list.fullscreen-active {
                            opacity: 0; /* Fade out */
                            transform: scale(0.95); /* Scale down */
                            display: none; /* Hide */
                            transition: opacity 0.3s ease, transform 0.3s ease; /* Transition effect */
                        }

                        @media (max-width: 768px) {
                            .map-panel {
                                width: 100%; /* Full width on small screens */
                                right: -100%; /* Off-screen initially */
                            }

                            .property-list.map-active {
                                width: 0; /* Hide property list */
                                overflow: hidden; /* Hide overflow */
                            }
                        }
                    </style>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Initialize map
                            maptilersdk.config.apiKey = 'gLXa6ihZF9HF7keYdTHC'; // Set API key

                            const agentPropertyMap = new maptilersdk.Map({
                                container: 'agentPropertyMap', // Map container
                                style: maptilersdk.MapStyle.HYBRID, // Map style
                                geolocate: maptilersdk.GeolocationType.POINT, // Geolocation type
                                zoom: 10, // Initial zoom level
                                maxZoom: 16.2 // Max zoom level
                            });

                            // Fetch coordinates from the API
                            fetch('../../backend/coordinates.php')
                                .then(response => response.json()) // Parse JSON response
                                .then(coordinates => {
                                    // Check if the response is an array
                                    if (!Array.isArray(coordinates)) {
                                        console.error('Fetched data is not an array:', coordinates); // Log error
                                        return; // Exit function
                                    }

                                    // Add each coordinate as a marker
                                    coordinates.forEach(function(coord) {
                                        // Ensure that each coordinate array has exactly 2 values (longitude, latitude)
                                        if (coord.length !== 2) {
                                            console.error(`Invalid coordinate format: [${coord}]`); // Log error
                                            return; // Exit function
                                        }

                                        const [longitude, latitude] = coord; // Destructure coordinates

                                        // Check if the coordinate values are valid numbers
                                        if (isNaN(longitude) || isNaN(latitude)) {
                                            console.error(`Invalid coordinate: [${longitude}, ${latitude}]`); // Log error
                                        } else {
                                            new maptilersdk.Marker() // Create new marker
                                                .setLngLat([longitude, latitude]) // Set coordinates
                                                .addTo(agentPropertyMap); // Add marker to map
                                        }
                                    });
                                })
                                .catch(error => {
                                    console.error('Error fetching coordinates:', error); // Log error
                                });

                            window.toggleMap = function() {
                                const mapPanel = document.getElementById('mapPanel'); // Get map panel
                                const propertyList = document.querySelector('.property-list'); // Get property list

                                if (mapPanel && propertyList) {
                                    mapPanel.classList.toggle('active'); // Toggle map panel visibility
                                    propertyList.classList.toggle('map-active'); // Toggle property list visibility

                                    // If exiting fullscreen mode when closing
                                    if (mapPanel.classList.contains('fullscreen')) {
                                        mapPanel.classList.remove('fullscreen'); // Remove fullscreen class
                                        propertyList.classList.remove('fullscreen-active'); // Remove fullscreen class from property list
                                    }

                                    // Trigger a resize event to ensure the map renders correctly
                                    if (agentPropertyMap) {
                                        setTimeout(() => {
                                            agentPropertyMap.resize(); // Resize map
                                        }, 300);
                                    }
                                }
                            };

                            window.toggleFullscreen = function() {
                                const mapPanel = document.getElementById('mapPanel'); // Get map panel
                                const propertyList = document.querySelector('.property-list'); // Get property list
                                const fullscreenIcon = document.querySelector('.map-control-btn i.fa-expand, .map-control-btn i.fa-compress'); // Get fullscreen icon

                                if (mapPanel && propertyList) {
                                    mapPanel.classList.toggle('fullscreen'); // Toggle fullscreen class
                                    propertyList.classList.toggle('fullscreen-active'); // Toggle fullscreen class for property list

                                    // Toggle fullscreen icon
                                    if (fullscreenIcon) {
                                        if (mapPanel.classList.contains('fullscreen')) {
                                            fullscreenIcon.classList.remove('fa-expand'); // Remove expand icon
                                            fullscreenIcon.classList.add('fa-compress'); // Add compress icon
                                        } else {
                                            fullscreenIcon.classList.remove('fa-compress'); // Remove compress icon
                                            fullscreenIcon.classList.add('fa-expand'); // Add expand icon
                                        }
                                    }

                                    // Trigger a resize event to ensure the map renders correctly
                                    if (agentPropertyMap) {
                                        setTimeout(() => {
                                            agentPropertyMap.resize(); // Resize map
                                        }, 300);
                                    }
                                }
                            };

                            // Enable the map button after map style has loaded
                            agentPropertyMap.on('load', function() {
                                const mapButton = document.getElementById('mapButton'); // Get map button
                                if (mapButton) {
                                    mapButton.disabled = false; // Enable the button when the map is ready
                                }
                            });

                        });


                        document.addEventListener("DOMContentLoaded", function () {
                            const propertyType = document.getElementById("propertyType");
                            const sqmInput = document.getElementById("landArea");

                            // Define limits for each land type
                            const sqmLimits = {
                                "House and Lot": 500,
                                "Agricultural Farm": 10000, 
                                "Commercial Lot": 2000,
                                "Raw Land": 5000,
                                "Residential Land": 1000,
                                "Residential Farm": 3000,
                                "Memorial Lot": 50
                            };

                            propertyType.addEventListener("change", function () {
                                const selectedType = propertyType.value;
                                const maxSqm = sqmLimits[selectedType] || null;

                                if (maxSqm) {
                                    sqmInput.setAttribute("max", maxSqm);
                                    sqmInput.placeholder = `Max: ${maxSqm} sqm`;
                                } else {
                                    sqmInput.removeAttribute("max");
                                    sqmInput.placeholder = "Enter sqm";
                                }
                            });

                            sqmInput.addEventListener("input", function () {
                                const selectedType = propertyType.value;
                                const maxSqm = sqmLimits[selectedType];
                                if (maxSqm && sqmInput.value > maxSqm) {
                                    alert(`Maximum allowed sqm for ${selectedType} is ${maxSqm} sqm.`);
                                    sqmInput.value = maxSqm;
                                }
                            });
                        });


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
                                <div class="col-md-6 order-md-2">
                                    <div id="map" class="mb-3 position-relative">
                                        <label for="mapstyles" class="form-label">Select Map Style</label> <!-- Map style label -->
                                        <select name="mapstyles" id="mapstyles" class="form-select mapstyles-select"> <!-- Map styles dropdown -->
                                            <optgroup label="Map Styles">
                                                <option value="STREETS">Streets</option> <!-- Streets option -->
                                                <option value="STREETS.DARK">Streets Dark</option> <!-- Streets Dark option -->
                                                <option value="HYBRID" selected>Satellite</option> <!-- Satellite option -->
                                            </optgroup>
                                        </select>

                                        <div class="custom-button-container d-flex justify-content-center">
                                            <button id="undo-last" class="custom-btn custom-btn-secondary mx-2">Undo
                                                Last</button> <!-- Undo button -->
                                            <button id="clear-all" class="custom-btn custom-btn-danger mx-2">Clear
                                                All</button> <!-- Clear all button -->
                                        </div>
                                    </div>
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
                                    <div class="filter-item">
                                        <label for="propertyLocation">Location:</label>
                                        <select name="propertyLocation" class="form-control" id="propertyLocation">
                                            <option value="all">All Locations</option>
                                            <optgroup label="Cities">
                                                <option value="bacoor">Bacoor</option>
                                                <option value="cavite">Cavite City</option>
                                                <option value="dasmariñas">Dasmariñas</option>
                                                <option value="general trias">General Trias</option>
                                                <option value="imus">Imus</option>
                                                <option value="tagaytay">Tagaytay</option>
                                                <option value="trece martires">Trece Martires</option>
                                            </optgroup>
                                            
                                            <optgroup label="Municipalities">
                                                <option value="alfonso">Alfonso</option>
                                                <option value="amadeo">Amadeo</option>
                                                <option value="carmona">Carmona</option>
                                                <option value="gma">General Mariano Alvarez</option>
                                                <option value="indang">Indang</option>
                                                <option value="kawit">Kawit</option>
                                                <option value="magallanes">Magallanes</option>
                                                <option value="maragondon">Maragondon</option>
                                                <option value="mendez">Mendez</option>
                                                <option value="naic">Naic</option>
                                                <option value="noveleta">Noveleta</option>
                                                <option value="rosario">Rosario</option>
                                                <option value="silang">Silang</option>
                                                <option value="tanza">Tanza</option>
                                                <option value="ternate">Ternate</option>
                                            </optgroup>

                                            <optgroup label="Barangays - Cavite City">
                                                <option value="caridad">Caridad, Cavite City, Cavite</option>
                                                <option value="sta cruz">Sta. Cruz, Cavite City, Cavite</option>
                                                <option value="san roque">San Roque, Cavite City, Cavite</option>
                                                <option value="san antonio">San Antonio, Cavite City, Cavite</option>
                                                <option value="dalahican">Dalahican, Cavite City, Cavite</option>
                                                <option value="santa cruz">Santa Cruz, Cavite City, Cavite</option>
                                                <option value="san rafael">San Rafael, Cavite City, Cavite</option>
                                                <option value="paterno">Paterno, Cavite City, Cavite</option>
                                                <option value="san isidro">San Isidro, Cavite City, Cavite</option>
                                                <option value="narra">Narra, Cavite City, Cavite</option>
                                            </optgroup>
                                            
                                            <optgroup label="Barangays - Dasmariñas">
                                                <option value="burol">Burol, Dasmariñas, Cavite</option>
                                                <option value="paliparan">Paliparan, Dasmariñas, Cavite</option> 
                                                <option value="sabang">Sabang, Dasmariñas, Cavite</option>
                                                <option value="salawag">Salawag, Dasmariñas, Cavite</option>
                                                <option value="sampaloc">Sampaloc, Dasmariñas, Cavite</option>
                                                <option value="san agustin">San Agustin, Dasmariñas, Cavite</option>
                                                <option value="san jose">San Jose, Dasmariñas, Cavite</option>
                                                <option value="san luis">San Luis, Dasmariñas, Cavite</option>
                                                <option value="san simon">San Simon, Dasmariñas, Cavite</option>
                                                <option value="langkaan">Langkaan, Dasmariñas, Cavite</option>
                                                <option value="new brgy">San Andres, Dasmariñas, Cavite</option>
                                            </optgroup>

                                            <optgroup label="Barangays - General Trias">
                                                <option value="buenavista">Buenavista, General Trias, Cavite</option>
                                                <option value="manggahan">Manggahan, General Trias, Cavite</option>
                                                <option value="pasong kawayan">Pasong Kawayan, General Trias, Cavite</option>
                                                <option value="san-francisco">San Francisco, General Trias, Cavite</option>
                                                <option value="tejero">Tejero, General Trias, Cavite</option>
                                                <option value="biclatan">Biclatan, General Trias, Cavite</option>
                                                <option value="governor-ferrer">Governor Ferrer, General Trias, Cavite</option>
                                                <option value="navarro">Navarro, General Trias, Cavite</option>
                                                <option value="san-juan">San Juan, General Trias, Cavite</option>
                                                <option value="santa-clara">Santa Clara, General Trias, Cavite</option>
                                            </optgroup>

                                            <optgroup label="Barangays - Imus">
                                                <option value="anabu">Anabu, Imus, Cavite</option>
                                                <option value="bayan luma">Bayan Luma, Imus, Cavite</option>
                                                <option value="malagasang">Malagasang, Imus, Cavite</option>
                                                <option value="medicion">Medicion, Imus, Cavite</option>
                                                <option value="toclong">Toclong, Imus, Cavite</option>
                                                <option value="bukandala">Bukandala, Imus, Cavite</option>
                                                <option value="magdalo">Magdalo, Imus, Cavite</option>
                                                <option value="mariano espeleta">Mariano Espeleta, Imus, Cavite</option>
                                                <option value="pinagbuklod">Pinagbuklod, Imus, Cavite</option>
                                                <option value="tanzang luma">Tanzang Luma, Imus, Cavite</option>
                                            </optgroup>

                                            <optgroup label="Barangays - Tagaytay">
                                                <option value="asisan">Asisan, Tagaytay, Cavite</option>
                                                <option value="kaybagal">Kaybagal, Tagaytay, Cavite</option>
                                                <option value="mendez crossing">Mendez Crossing, Tagaytay, Cavite</option>
                                                <option value="patutong malaki">Patutong Malaki, Tagaytay, Cavite</option>
                                                <option value="sungay">Sungay, Tagaytay, Cavite</option>
                                                <option value="calabuso">Calabuso, Tagaytay, Cavite</option>
                                                <option value="francisco">Francisco</option>
                                                <option value="maharlika east">Maharlika East, Tagaytay, Cavite</option>
                                                <option value="maharlika west">Maharlika West, Tagaytay, Cavite</option>
                                                <option value="maitim">Maitim, Tagaytay, Cavite</option>
                                            </optgroup>

                                            <optgroup label="Barangays - Trece Martires">
                                                <option value="aguado">Aguado, Trece Martires, Cavite</option>
                                                <option value="cabezas">Cabezas, Trece Martires, Cavite</option>
                                                <option value="de ocampo">De Ocampo, Trece Martires, Cavite</option>
                                                <option value="inocencio">Inocencio, Trece Martires, Cavite</option>
                                                <option value="luciano">Luciano, Trece Martires, Cavite</option>
                                                <option value="conchu">Conchu, Trece Martires, Cavite</option>
                                                <option value="gregorio">Gregorio, Trece Martires, Cavite</option>
                                                <option value="lallana">Lallana, Trece Martires, Cavite</option>
                                                <option value="lapidario">Lapidario, Trece Martires, Cavite</option>
                                                <option value="perez">Perez, Trece Martires, Cavite</option>
                                            </optgroup>

                                            <optgroup label="Barangays - Alfonso">
                                                <option value="aguirre">Aguirre, Alfonso, Cavite</option>
                                                <option value="amuyong">Amuyong, Alfonso, Cavite</option>
                                                <option value="buck estate">Buck Estate, Alfonso, Cavite</option>
                                                <option value="esperanza">Esperanza, Alfonso, Cavite</option>
                                                <option value="kaybagal">Kaybagal, Alfonso, Cavite</option>
                                                <option value="luksuhin">Luksuhin, Alfonso, Cavite</option>
                                                <option value="mangas">Mangas, Alfonso, Cavite</option>
                                                <option value="marahan">Marahan, Alfonso, Cavite</option>
                                                <option value="pajo">Pajo, Alfonso, Cavite/option>
                                                <option value="sicat">Sicat, Alfonso, Cavite</option>
                                                <option value="taywanak">Taywanak, Alfonso, Cavite</option>
                                            </optgroup>

                                            <optgroup label="Barangays - Amadeo">
                                                <option value="barangay 1">Barangay I (Pob.), Amadeo, Cavite</option>
                                                <option value="barangay 2">Barangay II (Pob.), Amadeo, Cavite</option>
                                                <option value="barangay 3">Barangay III (Pob.), Amadeo, Cavite</option>
                                                <option value="barangay 4">Barangay IV (Pob.), Amadeo, Cavite</option>
                                                <option value="barangay 5">Barangay V (Pob.), Amadeo, Cavite</option>
                                                <option value="buho">Buho, Amadeo, Cavite</option>
                                                <option value="halang">Halang, Amadeo, Cavite</option>
                                                <option value="maymangga">Maymangga, Amadeo, Cavite</option>
                                                <option value="pangil">Pangil, Amadeo, Cavite</option>
                                                <option value="talon">Talon, Amadeo, Cavite</option>
                                            </optgroup>

                                            <optgroup label="Barangays - Carmona">
                                                <option value="bancal">Bancal, Carmona, Cavite</option>
                                                <option value="cabilang baybay">Cabilang Baybay, Carmona, Cavite</option>
                                                <option value="lantic">Lantic, Carmona, Cavite</option>
                                                <option value="mabuhay">Mabuhay, Carmona, Cavite</option>
                                                <option value="maduya">Maduya, Carmona, Cavite</option>
                                            </optgroup>

                                            <optgroup label="Barangays - GMA">
                                                <option value="aldiano santos">Aldiano Santos, GMA, Cavite</option>
                                                <option value="benjamin tirona">Benjamin Tirona, GMA, Cavite</option>
                                                <option value="francisco de castro">Francisco De Castro, GMA, Cavite</option>
                                                <option value="gavino maderan">Gavino Maderan, GMA, Cavite</option>
                                                <option value="jacinto lumbreras">Jacinto Lumbreras, GMA, Cavite</option>
                                            </optgroup>

                                            <optgroup label="Barangays - Indang">
                                                <option value="agus agus">Agus-agus, Indang, Cavite</option>
                                                <option value="alulod">Alulod, Indang, Cavite</option>
                                                <option value="banaba">Banaba, Indang, Cavite</option>
                                                <option value="carasuchi">Carasuchi, Indang, Cavite</option>
                                                <option value="daine">Daine, Indang, Cavite</option>
                                                <option value="guyam malaki">Guyam Malaki, Indang, Cavite</option>
                                                <option value="guyam munti">Guyam Munti, Indang, Cavite</option>
                                                <option value="kaytapos">Kaytapos, Indang, Cavite</option>
                                                <option value="limbon">Limbon, Indang, Cavite</option>
                                                <option value="lumampong">Lumampong, Indang, Cavite</option>
                                            </optgroup>

                                            <optgroup label="Barangays - Kawit">
                                                <option value="binakayan">Binakayan, kawit, Cavite</option>
                                                <option value="gahak">Gahak, kawit, Cavite</option>
                                                <option value="kaingen">Kaingen, kawit, Cavite</option>
                                                <option value="magdalo">Magdalo, kawit, Cavite</option>
                                                <option value="marulas">Marulas, kawit, Cavite</option>
                                                <option value="poblacion">Poblacion, kawit, Cavite</option>
                                                <option value="samala">Samala, kawit, Cavite</option>
                                                <option value="tabon">Tabon, kawit, Cavite</option>
                                            </optgroup>

                                            <optgroup label="Barangays - Magallanes">
                                                <option value="barangay 1">Barangay I, Magallanes, Cavite</option>
                                                <option value="barangay 2">Barangay II, Magallanes, Cavite</option>
                                                <option value="barangay 3">Barangay III, Magallanes, Cavite</option>
                                                <option value="barangay 4">Barangay IV, Magallanes, Cavite</option>
                                                <option value="barangay 5">Barangay V, Magallanes, Cavite</option>
                                            </optgroup>

                                            <optgroup label="Barangays - Maragondon">
                                                <option value="bucal">Bucal, Maragondon, Cavite</option>
                                                <option value="caingin">Caingin, Maragondon, Cavite</option>
                                                <option value="garita">Garita, Maragondon, Cavite</option>
                                                <option value="layong">Layong, Maragondon, Cavite</option>
                                                <option value="mabato">Mabato, Maragondon, Cavite</option>
                                                <option value="pantihan">Pantihan, Maragondon, Cavite</option>
                                                <option value="pinagsanhan">Pinagsanhan, Maragondon, Cavite</option>
                                                <option value="poblacion">Poblacion, Maragondon, Cavite</option>
                                            </optgroup>

                                            <optgroup label="Barangays - Mendez">
                                                <option value="anuling cerca">Anuling Cerca, Mendez, Cavite</option>
                                                <option value="anuling lejos">Anuling Lejos, Mendez, Cavite</option>
                                                <option value="arbisco">Arbisco, Mendez, Cavite</option>
                                                <option value="bukal">Bukal, Mendez, Cavite</option>
                                                <option value="galicia">Galicia, Mendez, Cavite</option>
                                                <option value="palocpoc">Palocpoc, Mendez, Cavite</option>
                                                <option value="poblacion">Poblacion, Mendez, Cavite</option>
                                            </optgroup>

                                            <optgroup label="Barangays - Naic">
                                                <option value="bagong karsada">Bagong Karsada, Naic, Cavite</option>
                                                <option value="bancaan">Bancaan, Naic, Cavite</option>
                                                <option value="bayan">Bayan, Naic, Cavite</option>
                                                <option value="bucana">Bucana, Naic, Cavite</option>
                                                <option value="kanluran">Kanluran, Naic, Cavite</option>
                                                <option value="labac">Labac, Naic, Cavite</option>
                                                <option value="malainen">Malainen, Naic, Cavite</option>
                                                <option value="munting-mapino">Munting Mapino, Naic, Cavite</option>
                                            </optgroup>

                                            <optgroup label="Barangays - Noveleta">
                                                <option value="magdiwang">Magdiwang, Noveleta, Cavite</option>
                                                <option value="poblacion">Poblacion, Noveleta, Cavite</option>
                                                <option value="salcedo">Salcedo, Noveleta, Cavite</option>
                                                <option value="san antonio">San Antonio, Noveleta, Cavite</option>
                                                <option value="san jose">San Jose, Noveleta, Cavite</option>
                                                <option value="san rafael">San Rafael, Noveleta, Cavite</option>
                                            </optgroup>

                                            <optgroup label="Barangays - Rosario">
                                                <option value="bagbag">Bagbag, Rosario, Cavite</option>
                                                <option value="kanluran">Kanluran, Rosario, Cavite</option>
                                                <option value="ligtong">Ligtong, Rosario, Cavite</option>
                                                <option value="muzon">Muzon, Rosario, Cavite</option>
                                                <option value="sapa">Sapa, Rosario, Cavite</option>
                                                <option value="silangan">Silangan, Rosario, Cavite</option>
                                                <option value="tejero">Tejero, Rosario, Cavite</option>
                                                <option value="wawa">Wawa, Rosario, Cavite</option>
                                            </optgroup>

                                            <optgroup label="Barangays - Ternate">
                                                <option value="bucana">Bucana, Ternate, Cavite</option>
                                                <option value="poblacion">Poblacion, Ternate, Cavite</option>
                                                <option value="pooc">Pooc, Ternate, Cavite</option>
                                                <option value="sapang">Sapang, Ternate, Cavite</option>
                                                <option value="san-jose">San Jose, Ternate, Cavite</option>
                                                <option value="san-juan">San Juan, Ternate, Cavite</option>
                                            </optgroup>
                                            <optgroup label="Barangays - Bacoor">
                                                <option value="alima">Alima, Bacoor, Cavite</option>
                                                <option value="aniban">Aniban, Bacoor, Cavite</option>
                                                <option value="bayanan">Bayanan, Bacoor, Cavite</option>
                                                <option value="daang-bukid">Daang Bukid, Bacoor, Cavite</option>
                                                <option value="digman">Digman, Bacoor, Cavite</option>
                                                <option value="dulong bayan">Dulong Bayan, Bacoor, Cavite</option>
                                                <option value="habay">Habay, Bacoor, Cavite</option>
                                                <option value="kaingin">Kaingin, Bacoor, Cavite</option>
                                                <option value="ligas">Ligas, Bacoor, Cavite</option>
                                                <option value="mabolo">Mabolo, Bacoor, Cavite</option>
                                                <option value="maliksi">Maliksi, Bacoor, Cavite</option>
                                                <option value="molino">Molino, Bacoor, Cavite</option>
                                                <option value="niog">Niog, Bacoor, Cavite</option>
                                                <option value="panapaan">Panapaan, Bacoor, Cavite</option>
                                                <option value="queens-row">Queens Row, Bacoor, Cavite</option>
                                                <option value="real">Real, Bacoor, Cavite</option>
                                                <option value="salinas">Salinas, Bacoor, Cavite</option>
                                                <option value="san nicolas">San Nicolas, Bacoor, Cavite</option>
                                                <option value="sineguelasan">Sineguelasan, Bacoor, Cavite</option>
                                                <option value="talaba">Talaba, Bacoor, Cavite</option>
                                                <option value="zapote">Zapote, Bacoor, Cavite</option>
                                            </optgroup>
                                            <optgroup label="Barangays - Tanza">
                                                <option value="Amaya I, Tanza, Cavite">Amaya I, Tanza, Cavite</option>
                                                <option value="Amaya II, Tanza, Cavite">Amaya II, Tanza, Cavite</option>
                                                <option value="Amaya III, Tanza, Cavite">Amaya III, Tanza, Cavite</option>
                                                <option value="Amaya IV, Tanza, Cavite">Amaya IV, Tanza, Cavite</option>
                                                <option value="Amaya V, Tanza, Cavite">Amaya V, Tanza, Cavite</option>
                                                <option value="Amaya VI, Tanza, Cavite">Amaya VI, Tanza, Cavite</option>
                                                <option value="Amaya VII, Tanza, Cavite">Amaya VII, Tanza, Cavite</option>
                                                <option value="Bagtas, Tanza, Cavite">Bagtas, Tanza, Cavite</option>
                                                <option value="Biga, Tanza, Cavite">Biga, Tanza, Cavite</option>
                                                <option value="Bunga, Tanza, Cavite">Bunga, Tanza, Cavite</option>
                                                <option value="Calibuyo, Tanza, Cavite">Calibuyo, Tanza, Cavite</option>
                                                <option value="Capipisa, Tanza, Cavite">Capipisa, Tanza, Cavite</option>
                                                <option value="Daang Amaya I, Tanza, Cavite">Daang Amaya I, Tanza, Cavite</option>
                                                <option value="Daang Amaya II, Tanza, Cavite">Daang Amaya II, Tanza, Cavite</option>
                                                <option value="Daang Amaya III, Tanza, Cavite">Daang Amaya III, Tanza, Cavite</option>
                                                <option value="Halayhay, Tanza, Cavite">Halayhay, Tanza, Cavite</option>
                                                <option value="Julugan I, Tanza, Cavite">Julugan I, Tanza, Cavite</option>
                                                <option value="Julugan II, Tanza, Cavite">Julugan II, Tanza, Cavite</option>
                                                <option value="Julugan III, Tanza, Cavite">Julugan III, Tanza, Cavite</option>
                                                <option value="Julugan IV, Tanza, Cavite">Julugan IV, Tanza, Cavite</option>
                                                <option value="Julugan V, Tanza, Cavite">Julugan V, Tanza, Cavite</option>
                                                <option value="Julugan VI, Tanza, Cavite">Julugan VI, Tanza, Cavite</option>
                                                <option value="Julugan VII, Tanza, Cavite">Julugan VII, Tanza, Cavite</option>
                                                <option value="Julugan VIII, Tanza, Cavite">Julugan VIII, Tanza, Cavite</option>
                                                <option value="Mulawin, Tanza, Cavite">Mulawin, Tanza, Cavite</option>
                                                <option value="Poblacion I, Tanza, Cavite">Poblacion I, Tanza, Cavite</option>
                                                <option value="Poblacion II, Tanza, Cavite">Poblacion II, Tanza, Cavite</option>
                                                <option value="Poblacion III, Tanza, Cavite">Poblacion III, Tanza, Cavite</option>
                                                <option value="Poblacion IV, Tanza, Cavite">Poblacion IV, Tanza, Cavite</option>
                                                <option value="Sahud Ulan, Tanza, Cavite">Sahud Ulan, Tanza, Cavite</option>
                                                <option value="Sanja Mayor, Tanza, Cavite">Sanja Mayor, Tanza, Cavite</option>
                                                <option value="Santol, Tanza, Cavite">Santol, Tanza, Cavite</option>
                                                <option value="Tres Cruses, Tanza, Cavite">Tres Cruses, Tanza, Cavite</option>
                                            </optgroup>     
                                            <optgroup label="Barangays - Silang">
                                                <option value="adlas">Adlas, Silang, Cavite</option>
                                                <option value="balite 1">Balite I, Silang, Cavite</option>
                                                <option value="balite 2">Balite II, Silang, Cavite</option>
                                                <option value="balubad">Balubad, Silang, Cavite</option>
                                                <option value="banaba">Banaba, Silang, Cavite</option>
                                                <option value="banaybanay">Banaybanay, Silang, Cavite</option>
                                                <option value="biga 1">Biga I, Silang, Cavite</option>
                                                <option value="biga 2">Biga II, Silang, Cavite</option>
                                                <option value="biluso">Biluso, Silang, Cavite</option>
                                                <option value="buho">Buho, Silang, Cavite</option>
                                                <option value="bulihan">Bulihan, Silang, Cavite</option>
                                                <option value="carmen">Carmen, Silang, Cavite</option>
                                                <option value="hoyo">Hoyo, Silang, Cavite</option>
                                                <option value="ipil">Ipil, Silang, Cavite</option>
                                                <option value="kalubkob">Kalubkob, Silang, Cavite</option>
                                                <option value="kaong">Kaong, Silang, Cavite</option>
                                                <option value="lalaan 1">Lalaan I, Silang, Cavite</option>
                                                <option value="lalaan 2">Lalaan II, Silang, Cavite</option>
                                                <option value="lucsuhin">Lucsuhin, Silang, Cavite</option>
                                                <option value="lumil">Lumil, Silang, Cavite</option>
                                                <option value="maguyam">Maguyam, Silang, Cavite</option>
                                                <option value="malabag">Malabag, Silang, Cavite</option>
                                                <option value="malaking tatyao">Malaking Tatyao, Silang, Cavite</option>
                                                <option value="mangas 1">Mangas I, Silang, Cavite</option>
                                                <option value="mangas 2">Mangas II, Silang, Cavite</option>
                                                <option value="munting ilog">Munting Ilog, Silang, Cavite</option>
                                                <option value="narra 1">Narra I, Silang, Cavite</option>
                                                <option value="narra 2">Narra II, Silang, Cavite</option>
                                                <option value="pasong langka">Pasong Langka, Silang, Cavite</option>
                                                <option value="pooc 1">Pooc I, Silang, Cavite</option>
                                                <option value="pooc 2">Pooc II, Silang, Cavite</option>
                                                <option value="puting kahoy">Puting Kahoy, Silang, Cavite</option>
                                                <option value="sabutan">Sabutan, Silang, Cavite</option>
                                                <option value="san vicente 1">San Vicente I, Silang, Cavite</option>
                                                <option value="san vicente 2">San Vicente II, Silang, Cavite</option>
                                                <option value="santol">Santol, Silang, Cavite</option>
                                                <option value="tartaria">Tartaria, Silang, Cavite</option>
                                                <option value="tibig">Tibig, Silang, Cavite</option>
                                                <option value="tubuan 1">Tubuan I, Silang, Cavite</option>
                                                <option value="tubuan 2">Tubuan II, Silang, Cavite</option>
                                                <option value="tubuan 3">Tubuan III, Silang, Cavite</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    <script>
                                        document.getElementById('propertyLocation').addEventListener('change', function() {
                                            const selectedLocation = this.value;
                                            const barangayOptions = document.querySelectorAll('optgroup[label^="Barangays"]');
                                            
                                            barangayOptions.forEach(optgroup => {
                                                optgroup.style.display = 'none';
                                                if (optgroup.label.toLowerCase().includes(selectedLocation)) {
                                                    optgroup.style.display = 'block';
                                                }
                                            });
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
                                                        <option value="short term">Short term less than 1 year</option>
                                                        <option value="long term">Long term more than 1 year</option>
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

    <!--Di ko alam kay palacio-->
    <script>
        // Initialize maps
        const map1 = new maptilersdk.Map({
            container: 'map1',
            style: maptilersdk.MapStyle.STREETS,
            center: [121.0537, 14.5489], // Manila coordinates
            zoom: 13
        });

        const map2 = new maptilersdk.Map({
            container: 'map2',
            style: maptilersdk.MapStyle.STREETS,
            center: [121.0537, 14.5489],
            zoom: 13
        });

        const mapInput = new maptilersdk.Map({
            container: 'mapInput',
            style: maptilersdk.MapStyle.STREETS,
            center: [121.0537, 14.5489],
            zoom: 13
        });
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
            event.preventDefault(); // Prevent form from refreshing the page

            // Show loading spinner and disable button
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const loadingSpinner = document.getElementById('loadingSpinner');

            submitBtn.disabled = true;
            btnText.textContent = "Submitting...";
            loadingSpinner.classList.remove("d-none");

            const formData = new FormData(this); // Get form data

            try {
                const response = await fetch('../../backend/add_property.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.status === "success") {
                    // Show success modal
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
                // Reset button after submission
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

</body>

</html>