<?php
session_start(); // Start the session
require_once '../../db.php'; // Add database connection

// Initialize a variable to store error message for modal
$show_modal = false; // Flag to show modal
$error_message = ''; // Variable to hold error message

// Add this near the top of the file, after session_start()
$agent_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Unknown Agent';

// Check if the user is logged in
if (!isset($_SESSION['role_type'])) {
    // If not logged in, set flag and message for modal
    $show_modal = true; // Show modal
    $error_message = 'You must be logged in to access this page.'; // Error message for not logged in
} elseif ($_SESSION['role_type'] !== 'user') {
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

    <title>Land Map | Brokers</title> <!-- Page title -->
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
        <?php require '../../partials/nav_user.php' ?> <!-- Include navigation for agent -->
    </div>

    <div class="az-content az-content-dashboard">
        <div class="container">
            <div class="az-content-body">
                <div class="az-dashboard-one-title">
                    <div>
                        <h2 class="az-dashboard-title">Brokers</h2> <!-- Dashboard title -->
                        <p class="az-dashboard-text">View Agent Brokens </p> <!-- Dashboard text -->
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

            

                <div class="tab-content mt-4">
                    <div id="dashboard" class="tab-pane active">
                        <div id="dashboard" class="tab-pane">
                            <!-- Post new land property -->
                            
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

    ?>
           
    
</div>
<?php
    include '../../db.php'; 

    $agent_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Fetch agent details
    $agent_query = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $agent_query->bind_param("i", $agent_id);
    $agent_query->execute();
    $agent_result = $agent_query->get_result();
    $agent = $agent_result->fetch_assoc();

    // Fetch properties with images for this agent
    $properties_query = $conn->prepare("
    SELECT p.*, pi.image_name 
    FROM properties p
    LEFT JOIN property_images pi ON p.property_id = pi.property_id
    WHERE p.user_id = ?
    ");
    $properties_query->bind_param("i", $agent_id);
    $properties_query->execute();
    $properties_result = $properties_query->get_result();

?>

<div class="agent-details">
<div class="card-body text-center">
                  
                    <img src="../../assets/profile_images/<?php echo !empty($agent['profile']) ? htmlspecialchars($agent['profile']) : '../../assets/images/default-profile.jpg'; ?>" style="width: 150px; height: 150px; border-radius: 50%;">
                    <h3 class="mt-3"><?php echo htmlspecialchars($agent['fname'] . ' ' . $agent['lname']); ?></h3>
                    <p><strong><i class="fas fa-envelope"></i> Email:</strong> <?php echo htmlspecialchars($agent['email']); ?></p>
                    <p><strong><i class="fas fa-phone"></i> Phone:</strong> <?php echo !empty($agent['mobile']) ? htmlspecialchars($agent['mobile']) : 'Not provided'; ?></p>
                    <p><strong><i class="fas fa-map-marker-alt"></i> Location:</strong> <?php echo !empty($agent['location']) ? htmlspecialchars($agent['location']) : 'Tanza, Cavite'; ?></p>
                    <p><strong><i class="fa fa-id-card"></i> PRC:</strong> <?php echo !empty($agent['prc_id']) ? htmlspecialchars($agent['prc_id']) : 'Not provided'; ?></p>
                    <p><strong><i class="fa fa-address-card"></i> DHSP:</strong> <?php echo !empty($agent['dshp_id']) ? htmlspecialchars($agent['dshp_id']) : 'Not provided'; ?></p>
                    <p><strong><i class="fas fa-briefcase"></i> Role:</strong> <?php echo ucfirst(htmlspecialchars($agent['role_type'])); ?></p>
                </div>
</div>

<!-- Property Listings -->
<h3>Properties Listed by <?php echo htmlspecialchars($agent['fname']); ?></h3>
<div class="property-listings">
<div class="container">
    <div class="row">
        <?php while ($row = $properties_result->fetch_assoc()) : ?>
            <div class="col-md-5 mb-4 m-4"> <!-- Adjust column width if needed -->
                <div class="property-card">
                    <div class="property-image">
                        <?php 
                            $imagePath = !empty($row['image_name']) ? "../../assets/property_images/" . $row['image_name'] : "../../assets/property_images/default.jpg";
                        ?>
                        <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($row['property_name']); ?>">
                        <div class="sale-badge">
                            <?php echo $row['sale_or_lease'] == 'sale' ? 'FOR SALE' : 'FOR LEASE'; ?>
                        </div>
                        <div class="location-badge">
                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['property_location']); ?>
                        </div>
                    </div>
                    
                    <div class="property-content">
                         
                     
                    <?php if ($row['sale_or_lease'] == 'sale' && $row['sale_price']) { ?>
                        <div class="property-price">₱<?php echo number_format($row['sale_price'], 2); ?>/contract price</div>
                    <?php } elseif ($row['sale_or_lease'] == 'lease' && $row['monthly_rent'] > 0) { ?>
                        <div class="property-price">₱<?php echo number_format($row['monthly_rent'], 2); ?>/monthly cost</div>
                    <?php } ?>


                        <h3 class="property-title">Property Name: <?php echo htmlspecialchars($row['property_name']); ?></h3>
                        
                        <?php if ($row['sale_or_lease'] == 'sale' && $row['sale_price']) { ?>
                            <div class="property-price">₱ <?php echo number_format($row['sale_price'], 2); ?></div>
                        <?php } elseif ($row['sale_or_lease'] == 'lease' && $row['monthly_rent'] > 0) { ?>
                            <div class="property-price">₱ <?php echo number_format($row['monthly_rent'], 2); ?> /monthly cost</div>
                        <?php } ?>
                        
                        <div class="property-details">
                            <?php if ($row['land_area']) { ?>
                                <span><i class="fas fa-ruler-combined"></i> Land Area: <?php echo number_format($row['land_area']); ?> sqm</span>
                            <?php } ?>
                            <?php if ($row['property_type']) { ?>
                                <span><i class="fas fa-home"></i> Land Type: <?php echo htmlspecialchars($row['property_type']); ?></span>
                            <?php } ?>
                            <?php if ($row['sale_or_lease']) { ?>
                            <span><i class="fas fa-tag">Lease Type: </i> <?php echo htmlspecialchars($row['sale_or_lease']); ?></span> <!-- Land type -->
                        <?php } ?>
                        </div>
                        
                        <?php if ($row['property_description']) { ?>
                            <div class="property-description">
                                <i class="fas fa-file-alt"></i> Land Description: <?php echo substr(htmlspecialchars($row['property_description']), 0, 100) . '...'; ?>
                            </div>
                        <?php } ?>
                        
                        <?php if ($row['another_info']) { ?>
                            <div class="promo-badge">
                                <i class="fas fa-info-circle"></i> Another Information: <?php echo ucfirst($row['another_info']); ?>
                            </div>
                        <?php } ?>
                        
                        <?php if ($row['land_condition']) { ?>
                            <div class="property-condition">
                                <i class="fas fa-leaf"></i> Land Condition: <?php echo ucfirst($row['land_condition']); ?>
                            </div>
                        <?php } ?>
                        
                        <div class="property-actions">
                        <button class="btn btn-danger btn-sm" onclick="openAddToListModal(<?php echo $row['property_id']; ?>)">
                            <i class="fas fa-archive"></i> Add to List
                        </button>
                        <div class="modal fade" id="addToListModal" tabindex="-1" aria-labelledby="addToListModalLabel">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addToListModalLabel">Confirm Listing</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to list this property?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                                        <button type="button" class="btn btn-success" id="confirmAddToListBtn">Yes, List</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
                            let selectedPropertyId = null;

                            function openAddToListModal(propertyId) {
                                selectedPropertyId = propertyId;
                                $('#addToListModal').modal('show');
                            }

                            $(document).ready(function () {
                                $('#confirmAddToListBtn').on('click', function () {
                                    if (selectedPropertyId) {
                                        $.ajax({
                                            url: '../../backend/update_add_list.php',
                                            type: 'POST',
                                            data: { property_id: selectedPropertyId },
                                            success: function (response) {
                                                console.log("Server Response:", response); 
                                                if (response.trim() === "success") {
                                                    alert("Property listed successfully!");
                                                    location.reload(); 
                                                } else {
                                                    alert("Failed to list property: " + response); 
                                                }
                                            },

                                            error: function () {
                                                alert("An error occurred.");
                                            }
                                        });
                                        $('#addToListModal').modal('hide');
                                    }
                                });
                            });
                        </script>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

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
    window.location.href = 'edit_property.php?id=' + propertyId; // Redirect to edit property
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
                                zoom: 10,
        mapTypeId: google.maps.MapTypeId.SATELLITE, // Initial zoom level
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
                    </script>
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

        // Handle image preview
        document.getElementById('propertyImages').addEventListener('change', function(event) {
            const previewContainer = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('imagePreview');

            if (event.target.files.length > 0) {
                previewContainer.style.display = 'block'; // Show the preview box

                // Loop through selected files and add them to the preview section
                Array.from(event.target.files).forEach(file => {
                    imageFiles.push(file); // Add the file to the imageFiles array
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const imgElement = document.createElement('img');
                        imgElement.src = e.target.result;
                        imgElement.classList.add('img-thumbnail', 'm-2');
                        imgElement.style.maxWidth = '150px';
                        imgElement.style.maxHeight = '150px';

                        // Create a delete button for the image
                        const deleteButton = document.createElement('button');
                        deleteButton.innerHTML = '&times;';
                        deleteButton.classList.add('btn', 'btn-danger', 'btn-sm', 'position-absolute', 'top-0', 'end-0');
                        deleteButton.style.zIndex = '10';
                        deleteButton.style.width = '30px'; // Set width for round button
                        deleteButton.style.height = '30px'; // Set height for round button
                        deleteButton.style.borderRadius = '50%'; // Make it circular
                        deleteButton.style.fontSize = '18px'; // Adjust font size
                        deleteButton.style.padding = '0'; // Remove padding
                        deleteButton.style.lineHeight = '30px'; // Vertically center the 'X'
                        deleteButton.style.textAlign = 'center'; // Center the 'X'

                        deleteButton.onclick = function() {
                            // Remove the image from the array and the preview
                            const index = imageFiles.indexOf(file);
                            if (index > -1) {
                                imageFiles.splice(index, 1); // Remove from array
                            }
                            imgElement.remove();
                            deleteButton.remove();

                            // If no images left, hide the preview container
                            if (imageFiles.length === 0) {
                                previewContainer.style.display = 'none';
                            }
                        };

                        // Wrapper div for the image and delete button
                        const wrapper = document.createElement('div');
                        wrapper.classList.add('position-relative');
                        wrapper.appendChild(imgElement);
                        wrapper.appendChild(deleteButton);

                        preview.appendChild(wrapper);
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
                window.location.href = '../../index.php'; // Redirect to the login page
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
                    window.location.href = '../../index.php';
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