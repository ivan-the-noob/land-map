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

// Check if the user is an agent (if they are logged in)
elseif ($_SESSION['role_type'] !== 'agent') {
    // If not agent, set flag and message for modal
    $show_modal = true;
    $error_message = 'You do not have the necessary permissions to access this page.';
}

if (!isset($_SESSION['user_id']) && isset($user['user_id'])) {
    $_SESSION['user_id'] = $user['user_id'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-90680653-2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'UA-90680653-2');
    </script>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Land Map | Land Properties</title>
    <link rel="icon" href="../../assets/images/logo.png" type="image/x-icon">

    <!-- vendor css -->
    <link href="../../assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../../assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
    <link href="../../assets/lib/typicons.font/typicons.css" rel="stylesheet">
    <link href="../../assets/lib/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">

    <!-- Mapping Links -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmgygVeipMUsrtGeZPZ9UzXRmcVdheIqw&libraries=places"></script>
    <script src="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.umd.js"></script>
    <link href="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.css" rel="stylesheet" />

    <!-- azia CSS -->
    <link rel="stylesheet" href="../../assets/css/azia.css">
    <style>
        .land-card {
            border: 1px solid #ddd;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
        }

        .land-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
        }

        .map-container {
            height: 200px;
            margin: 10px 0;
        }

        .client-messages {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>

    <style>
        .land-card {
            border: 1px solid #ddd;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
        }

        .land-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
        }

        .map-container {
            height: 200px;
            margin: 10px 0;
        }

        .client-messages {
            max-height: 300px;
            overflow-y: auto;
        }

        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
        }

        .filter-item {
            flex: 1;
            min-width: 200px;
        }

        .features-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 10px 0;
        }

        .feature-tag {
            background: #e9ecef;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
        }
    </style>

    <style>
        .new-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ff4757;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            animation: pulse 2s infinite;
        }

        .property-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

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

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
    </style>

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

.chat-message {
    display: flex;
    margin-bottom: 10px;
}

.user-message {
    justify-content: flex-start; 
}

.agent-message {
    justify-content: flex-end; 
}

.message-box {
    padding: 10px;
    border-radius: 10px;
    max-width: 70%;
    word-wrap: break-word;
}

.agent-message .message-box {
    background-color: #007bff;
    color: white;
    text-align: right;
}

.user-message .message-box {
    background-color: #f1f1f1;
    color: black;
    text-align: left;
}

.chat-box {
    height: 400px;
    overflow-y: auto;
    border: 1px solid #ddd; 
    padding: 10px;
    background-color: #f9f9f9;
    border-radius: 10px;
}


</style>
</head>

<body>
    <div class="az-header">
        <?php require '../../partials/nav_agent.php' ?>
    </div>

    <div class="az-content az-content-dashboard">
        <div class="container">
            <div class="az-content-body">
                <div class="az-dashboard-one-title">
                    <div>
                        <h2 class="az-dashboard-title">View land Properties</h2>
                        <p class="az-dashboard-text">Find your dream land property</p>
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
                <div class="az-content az-content-dashboard">
                    <div class="container">
                        <div class="az-content-body">
            
                <!-- Add this before the Filters Section -->
                

                
    
                <button onclick="window.location.href='agent_chat.php'" class="btn btn-primary m-3">Chat Agent</button>
                <div class="tab-content mt-4">
                    <div id="dashboard" class="tab-pane active">
                        <div id="dashboard" class="tab-pane">
                            <h3 class="mb-1 mr-5">All Land Properties</h3>
                            <div class="property-list">
                                
                            <?php
                                require '../../db.php';
                                $agent_id = $_SESSION['user_id']; 

                                $sql = "SELECT iq.status AS iq_status, p.*, 
                                            u_inq.fname AS inquirer_fname, u_inq.lname AS inquirer_lname, 
                                            iq.user_id AS inquirer_id,  -- Alias for the inquirer user_id
                                            p.user_id AS agent_id,      -- Alias for the agent user_id
                                            ui.image_name AS user_image,
                                            (SELECT image_name FROM property_images WHERE property_id = p.property_id LIMIT 1) AS property_image 
                                        FROM inquire iq
                                        INNER JOIN properties p ON iq.property_id = p.property_id
                                        INNER JOIN users u_agent ON p.user_id = u_agent.user_id  
                                        INNER JOIN users u_inq ON iq.user_id = u_inq.user_id     
                                        LEFT JOIN user_img ui ON u_inq.user_id = ui.user_id      
                                        WHERE p.user_id = ?";  // Filter properties by agent_id
                    
                    

                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $agent_id);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $inquiryStatus = $row['iq_status']; 
                                    $propertyName = $row['property_name'];
                                    $inquirerName = $row['inquirer_fname'] . ' ' . $row['inquirer_lname']; 

                                    $imagePath = !empty($row['property_image']) ? "../../assets/property_images/" . $row['property_image'] : "../../assets/images/default-property.jpg";        
                            ?>

            <div class="property-card">
                <div class="property-image">
                <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($row['property_name']); ?>">
                    <div class="sale-badge">
                        <?php echo strtoupper($row['sale_or_lease']); ?>
                    </div>
                    <div class="location-badge">
                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['property_location']); ?>
                    </div>
                </div>

                <div class="property-content">
                    <h3><strong>Inquirer:</strong> <?php echo htmlspecialchars($inquirerName); ?></h3>
                    <h3 class="property-title text-muted">Property Name: <?php echo htmlspecialchars($row['property_name']); ?></h3>
                    
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

                    <div class="admin-actions d-flex justify-content-center">
                        <button class="btn-view" onclick="viewDetails(<?php echo $row['property_id']; ?>)">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        <?php if ($inquiryStatus === 'pending') { ?>
                        <button class="btn-success" onclick="openAcceptModal(<?= $row['property_id']; ?>)">
                            <i class="fas fa-check"></i> Accept Inquiry
                        </button>        
                        <?php } elseif ($inquiryStatus === 'accepted') { ?>
                            <button class="btn-primary" disabled>
                                <i class="fas fa-hourglass-half"></i> On-going
                            </button>
                        <?php } else { ?>
                            <button class="btn-danger" disabled>
                                <i class="fas fa-times"></i> Inquiry Declined
                            </button>
                        <?php } ?>
                        
                        <?php if ($inquiryStatus !== 'cancelled') { ?>
                            <button class="btn-delete" onclick="openDeleteModal(<?= htmlspecialchars($row['property_id']); ?>)">
                                <i class="fas fa-trash"></i> Cancel Inquiry
                            </button>
                            <?php } else { ?>
                                <button class="btn btn-secondary" disabled>
                                <i class="fas fa-ban"></i> Cancelled
                            </button>
                        <?php } ?>


                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
                        function openDeleteModal(propertyId) {
                            $("#confirmDeleteBtn").attr("data-property-id", propertyId);
                            $("#deleteInquiryModal").modal("show");
                        }

                        $(document).ready(function () {
                            $("#confirmDeleteBtn").click(function () {
                                let propertyId = $(this).attr("data-property-id");

                                $.ajax({
                                    url: "../../backend/delete_inquiry.php",
                                    type: "POST",
                                    data: { property_id: propertyId },
                                    success: function (response) {
                                        if (response.trim() === "success") {
                                            alert("Inquiry cancelled successfully!");
                                            location.reload(); // Reload page to reflect changes
                                        } else {
                                            alert("Error: " + response);
                                        }
                                    },
                                    error: function () {
                                        alert("Failed to cancel inquiry. Please try again.");
                                    }
                                });
                            });
                        });
                        </script>

                        </div>
                       

                       <!-- Chat Modal -->
                       <div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="chatModalLabel">Chat with User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="chatMessages" class="chat-box"></div>
                                        <div class="input-group mt-3">
                                            <input type="text" id="chatInput" class="form-control" placeholder="Type your message...">
                                            <button class="btn btn-primary" onclick="sendMessage()">Send</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="modal fade" id="deleteInquiryModal" tabindex="-1" aria-labelledby="deleteInquiryModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteInquiryModalLabel">Confirm Cancellation</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to cancel this inquiry?
                                    </div>
                                    <div class="modal-footer">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true"><div class="btn btn-secondary">NO</div></span>
                                    </button>

                                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Cancel Inquiry</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="modal fade" id="acceptInquiryModal" tabindex="-1" aria-labelledby="acceptInquiryModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="acceptInquiryModalLabel">Confirm Acceptance</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to accept this inquiry?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-success" id="confirmAcceptBtn">Yes, Accept</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
                            let selectedPropertyId = null;

                            function openAcceptModal(propertyId) {
                                selectedPropertyId = propertyId;
                                $('#acceptInquiryModal').modal('show');
                            }

                            $(document).ready(function () {
                                $('#confirmAcceptBtn').on('click', function () {
                                    if (selectedPropertyId) {
                                        $.ajax({
                                            url: '../../backend/accept_inquiry.php',
                                            type: 'POST',
                                            data: { property_id: selectedPropertyId },
                                            success: function (response) {
                                                if (response.trim() === "success") {
                                                    alert("Inquiry accepted successfully!");
                                                    location.reload(); 
                                                } else {
                                                    alert("Failed to accept inquiry.");
                                                }
                                            },
                                            error: function () {
                                                alert("An error occurred.");
                                            }
                                        });
                                        $('#acceptInquiryModal').modal('hide');
                                    }
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
                <p>Empty Inquire</p>
              </div>';
    }
    ?>
</div>



<script>
let propertyId, agentId, inquirerId; // Define variables globally

function openChatModal(propId, agentIdFromPHP, inqId) {
    console.log('Opening chat modal with:', { propId, agentIdFromPHP, inqId });

    propertyId = propId;
    agentId = agentIdFromPHP;  // Set the agent's ID from PHP to the global variable
    inquirerId = inqId;  // This is the inquirer (user) ID
    $('#chatModal').modal('show'); // Open modal
    loadMessages(); // Load previous messages
}

// This function will load the chat messages from the database
function loadMessages() {
    console.log('Loading messages for property:', propertyId, 'user:', inquirerId, 'agent:', agentId);

    $.ajax({
        url: "../../backend/load_messages.php",
        type: "POST",
        data: { 
            property_id: propertyId, 
            user_id: inquirerId,  // Inquirer ID (user)
            agent_id: agentId     // Agent ID (from session)
        },
        success: function(response) {
            console.log("Messages loaded:", response); // Log response from server
            $('#chatMessages').html(response); // Display chat messages
        },
        error: function(xhr, status, error) {
            console.log("Error loading messages:", status, error); // Log any error in the request
        }
    });
}

setInterval(loadMessages, 2000);

// Google Maps function for modal
function initMap(latitude, longitude) {
    const propertyLocation = { lat: latitude, lng: longitude };

    // Initialize Google Map
    const map = new google.maps.Map(document.getElementById("modalMap"), {
        zoom: 15,
        center: propertyLocation,
    });

    // Add marker for property location
    const marker = new google.maps.Marker({
        position: propertyLocation,
        map: map,
    });
}

// Function to fetch property details and populate the modal
function viewDetails(propertyId) {
    // Fetch property details from the server
    fetch(`../../backend/get_property_details.php?property_id=${propertyId}`)
        .then(response => response.json())
        .then(data => {
            // Populate modal with property details
            document.getElementById('modalPropertyName').textContent = data.property_name;
            document.getElementById('modalPropertyType').textContent = data.property_type;
            document.getElementById('modalSaleType').textContent = data.sale_or_lease.toUpperCase();
            document.getElementById('modalLocation').textContent = data.property_location;
            document.getElementById('modalLandArea').textContent = data.land_area;
            document.getElementById('modalLandCondition').textContent = data.land_condition || 'N/A';
            document.getElementById('modalDescription').textContent = data.property_description;

            // Set price based on sale or lease type
            const price = data.sale_or_lease === 'sale' 
                ? `₱${Number(data.sale_price).toLocaleString()}`
                : `₱${Number(data.monthly_rent).toLocaleString()}/month`;
            document.getElementById('modalPrice').textContent = price;

            // Populate features
            const featuresContainer = document.getElementById('modalFeatures');
            featuresContainer.innerHTML = ''; // Clear existing features
            if (data.features) {
                const features = data.features.split(',');
                features.forEach(feature => {
                    const featureElement = document.createElement('div');
                    featureElement.className = 'feature-item';
                    featureElement.innerHTML = `
                        <i class="fas fa-check"></i>
                        ${feature.trim()}
                    `;
                    featuresContainer.appendChild(featureElement);
                });
            }

            // Populate image carousel
            const carouselInner = document.querySelector('.carousel-inner');
            carouselInner.innerHTML = ''; // Clear existing images
            if (data.images && data.images.length > 0) {
                data.images.forEach((image, index) => {
                    const carouselItem = document.createElement('div');
                    carouselItem.className = `carousel-item ${index === 0 ? 'active' : ''}`;
                    carouselItem.innerHTML = `
                        <img src="../../assets/property_images/${image}" class="d-block w-100" alt="Property Image">
                    `;
                    carouselInner.appendChild(carouselItem);
                });
            }

            // Initialize Google Map with property location
            if (data.latitude && data.longitude) {
    const latitude = parseFloat(data.latitude);
    const longitude = parseFloat(data.longitude);

    // Check if the values are valid numbers
    if (!isNaN(latitude) && !isNaN(longitude)) {
        const propertyMap = new google.maps.Map(document.getElementById('modalGoogleMap'), {
            center: { lat: latitude, lng: longitude },
            zoom: 15
        });

        // Add marker for property location
        new google.maps.Marker({
            position: { lat: latitude, lng: longitude },
            map: propertyMap
        });
    } else {
        console.error("Invalid latitude or longitude values:", data.latitude, data.longitude);
    }
} else {
    console.error("Latitude or Longitude is missing");
}

            // Set agent information
            document.getElementById('modalAgentName').textContent = `${data.agent_fname} ${data.agent_lname}`;
            document.getElementById('modalAgentImage').src = data.agent_image 
                ? `../../assets/images/profile/${data.agent_image}`
                : '../../assets/images/default-profile.jpg';
            
            // Get the actions container
            const actionsContainer = document.getElementById('agentActions');
            
            // Clear previous actions
            actionsContainer.innerHTML = '';
            
            // Check if the property belongs to the logged-in user
            if (data.user_id == <?php echo $_SESSION['user_id']; ?>) {
                // Show edit and archive buttons for owner
                actionsContainer.innerHTML = `
                    <button class="btn btn-warning btn-sm mr-2" onclick="editProperty(${propertyId})">
                        <i class="fas fa-edit"></i> Edit Property
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="archiveProperty(${propertyId})">
                        <i class="fas fa-archive"></i> Archive Property
                    </button>
                `;
            } else {
                // Show contact button for non-owners
                actionsContainer.innerHTML = `
                    <button class="btn btn-primary btn-sm" onclick="contactAgent(${data.user_id})">
                        <i class="fas fa-envelope"></i> Contact Agent
                    </button>
                `;
            }

            // Show the modal
            $('#propertyDetailsModal').modal('show');
        })
        .catch(error => {
            console.error('Error fetching property details:', error);
            alert('Error loading property details. Please try again.');
        });
}

// Function to contact agent
function contactAgent(userId) {
    // Add your contact agent logic here
    console.log('Contacting agent:', userId);
}

// Update the current time every second
function updateTime() {
    const timeElement = document.getElementById('current-time');
    const now = new Date().toLocaleString("en-US", {
        timeZone: "Asia/Manila",
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    });
    timeElement.textContent = now;
}

// Update time immediately and then every second
updateTime();
setInterval(updateTime, 1000);
</script>

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

<!-- start of footer -->
<div class="modal-footer">
    </div>

    <div class="az-footer">
        <div class="container">
            <span class="text-muted d-block text-center">Copyright ©LoremIpsum 2024</span>
        </div>
    </div>
    <!-- End of Footer -->
                                   
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

    <!-- Property Details Modal -->
    <div class="modal fade" id="propertyDetailsModal" tabindex="-1" role="dialog" aria-labelledby="propertyDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="propertyDetailsModalLabel">Property Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Image Carousel -->
                    <div id="propertyImageCarousel" class="carousel slide mb-4" data-ride="carousel">
                        <div class="carousel-inner">
                            <!-- Images will be dynamically added here -->
                        </div>
                        <a class="carousel-control-prev" href="#propertyImageCarousel" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#propertyImageCarousel" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>

                    <!-- Property Information -->
                    <div class="property-info">
                        <h3 id="modalPropertyName"></h3>
                        <div class="property-meta">
                            <span class="badge badge-primary" id="modalPropertyType"></span>
                            <span class="badge badge-info" id="modalSaleType"></span>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h5>Basic Information</h5>
                                <ul class="list-unstyled">
                                    <li><strong>Location:</strong> <span id="modalLocation"></span></li>
                                    <li><strong>Land Area:</strong> <span id="modalLandArea"></span> sqm</li>
                                    <li><strong>Price:</strong> <span id="modalPrice"></span></li>
                                    <li><strong>Land Condition:</strong> <span id="modalLandCondition"></span></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5>Features</h5>
                                <div id="modalFeatures" class="features-list">
                                    <!-- Features will be dynamically added here -->
                                </div>
                            </div>
                        </div>

                        <div class="description-section mt-4">
                            <h5>Description</h5>
                            <p id="modalDescription"></p>
                        </div>

                        <!-- Property Location Map -->
                        <div class="mt-4">
                            <h5>Property Location</h5>
                            <div id="modalGoogleMap" style="height: 300px;"></div>
                        </div>

                        <!-- Agent Information -->
                        <div class="agent-info mt-4">
                            <h5>Agent Information</h5>
                            <div class="d-flex align-items-center">
                                <img id="modalAgentImage" src="" alt="Agent" class="rounded-circle mr-3" style="width: 60px; height: 60px; object-fit: cover;">
                                <div>
                                    <h6 id="modalAgentName"></h6>
                                    <div id="agentActions">
                                        <!-- Buttons will be dynamically populated based on ownership -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Restrict Property Modal -->
    <div class="modal fade" id="restrictModal" tabindex="-1" role="dialog" aria-labelledby="restrictModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="restrictModalLabel">Restrict Property</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group" id="restrictDaysGroup">
                        <label for="restrictDays">Number of Days to Restrict:</label>
                        <input type="number" class="form-control" id="restrictDays" min="1" max="365" placeholder="Enter number of days">
                        <small class="text-muted">Property will be hidden from users for the specified number of days.</small>
                    </div>
                    <div id="unrestrictMessage" style="display: none;">
                        <p>Are you sure you want to remove the restriction from this property?</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmRestrictBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/lib/jquery/jquery.min.js"></script>
    <script src="../../assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/lib/ionicons/ionicons.js"></script>
    <script src="../../assets/js/azia.js"></script>

    <script src="../../assets/js/addedFunctions.js"></script>



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
                                imageFiles.splice(index, 1);
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
                window.location.href = '../../frontend/sign_in.php'; // Redirect to the login page
            });
        });
    </script>

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
                        window.location.href = '../../frontend/sign_in.php'; // Adjust the login page URL as needed
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
            const timeElement = document.getElementById('current-time');

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

    <!-- Function to check and remove expired new badges -->
    <script>
    function checkNewBadges() {
        const newBadges = document.querySelectorAll('.new-badge');
        const now = new Date();
        
        newBadges.forEach(badge => {
            const createdDate = new Date(badge.dataset.created);
            const daysDifference = Math.floor((now - createdDate) / (1000 * 60 * 60 * 24));
            
            if (daysDifference >= 7) {
                badge.remove();
            }
        });
    }

    // Check badges when page loads
    document.addEventListener('DOMContentLoaded', checkNewBadges);

    // Optionally check periodically (every minute) for real-time updates
    setInterval(checkNewBadges, 60000);
    </script>

    <!-- New functions for edit and archive -->
    <script>
    function editProperty(propertyId) {
        // Redirect to edit property page
        window.location.href = `edit_property.php?id=${propertyId}`;
    }

    function archiveProperty(propertyId) {
        if (confirm('Are you sure you want to archive this property? This action cannot be undone.')) {
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
                    // Reload the page to remove the archived property
                    location.reload();
                } else {
                    alert('Failed to archive property: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while archiving the property');
            });
        }
    }
    </script>

    <!-- Add this JavaScript before the closing body tag -->
    <script>
    let currentPropertyId = null;
    let currentStatus = null;

    function showRestrictModal(propertyId, status) {
        currentPropertyId = propertyId;
        currentStatus = status;
        
        const restrictDaysGroup = document.getElementById('restrictDaysGroup');
        const unrestrictMessage = document.getElementById('unrestrictMessage');
        const modalTitle = document.getElementById('restrictModalLabel');
        
        if (status === 'restricted') {
            restrictDaysGroup.style.display = 'none';
            unrestrictMessage.style.display = 'block';
            modalTitle.textContent = 'Unrestrict Property';
        } else {
            restrictDaysGroup.style.display = 'block';
            unrestrictMessage.style.display = 'none';
            modalTitle.textContent = 'Restrict Property';
        }
        
        $('#restrictModal').modal('show');
    }

    document.getElementById('confirmRestrictBtn').addEventListener('click', function() {
        if (currentStatus === 'restricted') {
            togglePropertyStatus(currentPropertyId, currentStatus);
        } else {
            const days = document.getElementById('restrictDays').value;
            if (!days || days < 1) {
                alert('Please enter a valid number of days');
                return;
            }
            togglePropertyStatus(currentPropertyId, currentStatus, days);
        }
        $('#restrictModal').modal('hide');
    });
    </script>

    <script>
    function togglePropertyStatus(propertyId, currentStatus, days = null) {
        const data = {
            property_id: propertyId,
            current_status: currentStatus
        };
        
        if (days) {
            data.days = days;
        }

        fetch('../../backend/toggle_property_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to show updated status
                location.reload();
            } else {
                alert('Failed to update property status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the property status');
        });
    }
    </script>

    <!-- Add this JavaScript before the closing body tag -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const propertyCards = document.querySelectorAll('.property-card');
            
            propertyCards.forEach(card => {
                const propertyName = card.querySelector('.property-title').textContent.toLowerCase();
                const propertyLocation = card.querySelector('.location-badge').textContent.toLowerCase();
                const propertyType = card.querySelector('.property-details').textContent.toLowerCase();
                const propertyDescription = card.querySelector('.property-description') ? 
                                          card.querySelector('.property-description').textContent.toLowerCase() : '';
                
                // Check if any of the property details match the search term
                const matches = propertyName.includes(searchTerm) ||
                              propertyLocation.includes(searchTerm) ||
                              propertyType.includes(searchTerm) ||
                              propertyDescription.includes(searchTerm);
                
                // Show/hide the card based on search match
                card.style.display = matches ? 'block' : 'none';
            });
        });
    });
    </script>

    <style>
    /* Add these styles to your existing CSS */
    .search-section {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .search-section .input-group {
        max-width: 600px;
        margin: 0 auto;
    }

    .search-section input {
        border-right: none;
        padding-left: 15px;
    }

    .search-section .btn {
        border-left: none;
        background: white;
    }

    .search-section .btn:hover {
        background: #f8f9fa;
    }

    .search-section .fa-search {
        color: #6c757d;
    }

    /* Add animation for search results */
    .property-card {
        transition: all 0.3s ease-in-out;
    }

    .property-card:not([style*="display: none"]) {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>

    <!-- Add this JavaScript before the closing body tag -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get filter elements
        const saleTypeFilter = document.getElementById('saleTypeFilter');
        const leaseTermFilter = document.getElementById('leaseTermFilter');
        const monthlyRental = document.getElementById('monthlyRental');
        const landCondition = document.getElementById('landCondition');
        const landContract = document.getElementById('landContract');
        const landTypeFilter = document.getElementById('landTypeFilter');
        const locationFilter = document.getElementById('locationFilter');
        const minPrice = document.getElementById('minPrice');
        const maxPrice = document.getElementById('maxPrice');
        const minArea = document.getElementById('minArea');
        const maxArea = document.getElementById('maxArea');
        const featureCheckboxes = document.querySelectorAll('input[name="features"]');
        const additionalInfoCheckboxes = document.querySelectorAll('input[name="additionalInfo"]');

        // Apply filters button click handler
        document.getElementById('applyFilters').addEventListener('click', function() {
            const propertyCards = document.querySelectorAll('.property-card');
            
            propertyCards.forEach(card => {
                let showCard = true;

                // Sale Type Filter
                if (saleTypeFilter.value !== 'all') {
                    const saleType = card.querySelector('.sale-badge').textContent.toLowerCase();
                    if (saleTypeFilter.value === 'sale' && !saleType.includes('sale')) showCard = false;
                    if (saleTypeFilter.value === 'lease' && !saleType.includes('lease')) showCard = false;
                }

                // Lease Term Filter (if applicable)
                if (saleTypeFilter.value === 'lease' && leaseTermFilter.value !== 'all') {
                    const leaseTerm = card.querySelector('.lease-term')?.textContent.toLowerCase();
                    if (leaseTermFilter.value === 'short' && !leaseTerm?.includes('less than 1 year')) showCard = false;
                    if (leaseTermFilter.value === 'long' && !leaseTerm?.includes('more than 1 year')) showCard = false;
                }

                // Monthly Rental Filter (if applicable)
                if (saleTypeFilter.value === 'lease' && monthlyRental.value) {
                    const rental = parseFloat(card.querySelector('.property-price')?.textContent.replace(/[^0-9.]/g, '')) || 0;
                    const filterRental = parseFloat(monthlyRental.value.replace(/,/g, '')) || 0;
                    if (rental > filterRental) showCard = false;
                }

                // Land Condition Filter (if applicable)
                if (saleTypeFilter.value === 'sale' && landCondition.value !== 'all') {
                    const condition = card.querySelector('.property-conditon')?.textContent.toLowerCase();
                    if (!condition?.includes(landCondition.value.toLowerCase())) showCard = false;
                }

                // Land Contract Price Filter (if applicable)
                if (saleTypeFilter.value === 'sale' && landContract.value) {
                    const price = parseFloat(card.querySelector('.property-price')?.textContent.replace(/[^0-9.]/g, '')) || 0;
                    const filterPrice = parseFloat(landContract.value.replace(/,/g, '')) || 0;
                    if (price > filterPrice) showCard = false;
                }

                // Land Type Filter
                if (landTypeFilter.value !== 'all') {
                    const landType = card.querySelector('.property-details')?.textContent.toLowerCase();
                    if (!landType?.includes(landTypeFilter.value.toLowerCase())) showCard = false;
                }

                // Location Filter
                if (locationFilter.value !== 'all') {
                    const location = card.querySelector('.location-badge')?.textContent.toLowerCase();
                    if (!location?.includes(locationFilter.value.toLowerCase())) showCard = false;
                }

                // Price Range Filter
                const price = parseFloat(card.querySelector('.property-price')?.textContent.replace(/[^0-9.]/g, '')) || 0;
                if (minPrice.value && price < parseFloat(minPrice.value)) showCard = false;
                if (maxPrice.value && price > parseFloat(maxPrice.value)) showCard = false;

                // Area Range Filter
                const area = parseFloat(card.querySelector('.property-details')?.textContent.match(/\d+/)?.[0]) || 0;
                if (minArea.value && area < parseFloat(minArea.value)) showCard = false;
                if (maxArea.value && area > parseFloat(maxArea.value)) showCard = false;

                // Features Filter
                const selectedFeatures = Array.from(featureCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);
                if (selectedFeatures.length > 0) {
                    const features = card.querySelector('.features-list')?.textContent.toLowerCase() || '';
                    if (!selectedFeatures.every(feature => features.includes(feature.toLowerCase()))) {
                        showCard = false;
                    }
                }

                // Additional Info Filter
                const selectedInfo = Array.from(additionalInfoCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);
                if (selectedInfo.length > 0) {
                    const additionalInfo = card.querySelector('.promo-badge')?.textContent.toLowerCase() || '';
                    if (!selectedInfo.every(info => additionalInfo.includes(info.toLowerCase()))) {
                        showCard = false;
                    }
                }

                // Show/hide card based on all filters
                card.style.display = showCard ? 'block' : 'none';
            });
        });

        // Reset filters button click handler
        document.getElementById('resetFilters').addEventListener('click', function() {
            // Reset all filter values
            saleTypeFilter.value = 'all';
            leaseTermFilter.value = 'all';
            monthlyRental.value = '';
            landCondition.value = 'all';
            landContract.value = '';
            landTypeFilter.value = 'all';
            locationFilter.value = 'all';
            minPrice.value = '';
            maxPrice.value = '';
            minArea.value = '';
            maxArea.value = '';
            
            // Uncheck all checkboxes
            featureCheckboxes.forEach(cb => cb.checked = false);
            additionalInfoCheckboxes.forEach(cb => cb.checked = false);

            // Show all property cards
            document.querySelectorAll('.property-card').forEach(card => {
                card.style.display = 'block';
            });

            // Reset sale/lease specific options visibility
            document.querySelectorAll('.sale-options').forEach(option => {
                option.style.display = 'none';
            });
            document.querySelectorAll('.lease-options').forEach(option => {
                option.style.display = 'none';
            });
        });
    });
    </script>

    <!-- Add this script section after the filter section -->
    <script>
    function handleSearch() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const propertyCards = document.querySelectorAll('.property-card');

        propertyCards.forEach(card => {
            const propertyName = card.querySelector('.property-title').textContent.toLowerCase();
            const propertyType = card.querySelector('.property-details').textContent.toLowerCase();
            const propertyLocation = card.querySelector('.location-badge').textContent.toLowerCase();
            
            const matchesSearch = propertyName.includes(searchInput) || 
                                propertyType.includes(searchInput) || 
                                propertyLocation.includes(searchInput);
            
            card.style.display = matchesSearch ? 'block' : 'none';
        });
    }

    function applyFilters() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const saleType = document.getElementById('saleTypeFilter').value;
        const landType = document.getElementById('landTypeFilter').value;
        const location = document.getElementById('propertyLocation').value;
        const minPrice = document.getElementById('minPrice').value;
        const maxPrice = document.getElementById('maxPrice').value;
        const minArea = document.getElementById('minArea').value;
        const maxArea = document.getElementById('maxArea').value;
        
        const propertyCards = document.querySelectorAll('.property-card');

        propertyCards.forEach(card => {
            let showCard = true;
            
            // Search text filter
            const propertyName = card.querySelector('.property-title').textContent.toLowerCase();
            const propertyType = card.querySelector('.property-details').textContent.toLowerCase();
            const propertyLocation = card.querySelector('.location-badge').textContent.toLowerCase();
            
            if (searchInput && !propertyName.includes(searchInput) && 
                !propertyType.includes(searchInput) && 
                !propertyLocation.includes(searchInput)) {
                showCard = false;
            }

            // Sale type filter
            if (saleType !== 'all') {
                const cardSaleType = card.querySelector('.sale-badge').textContent.toLowerCase();
                if (!cardSaleType.includes(saleType)) {
                    showCard = false;
                }
            }

            // Land type filter
            if (landType !== 'all') {
                const cardLandType = card.querySelector('.property-details').textContent.toLowerCase();
                if (!cardLandType.includes(landType)) {
                    showCard = false;
                }
            }

            // Location filter
            if (location !== 'all') {
                const cardLocation = card.querySelector('.location-badge').textContent.toLowerCase();
                if (!cardLocation.includes(location.toLowerCase())) {
                    showCard = false;
                }
            }

            // Price range filter
            const priceText = card.querySelector('.property-price').textContent;
            const price = parseFloat(priceText.replace(/[^0-9.]/g, ''));
            if ((minPrice && price < minPrice) || (maxPrice && price > maxPrice)) {
                showCard = false;
            }

            // Area range filter
            const areaText = card.querySelector('.property-details').textContent;
            const areaMatch = areaText.match(/(\d+)\s*sqm/);
            if (areaMatch) {
                const area = parseFloat(areaMatch[1]);
                if ((minArea && area < minArea) || (maxArea && area > maxArea)) {
                    showCard = false;
                }
            }

            card.style.display = showCard ? 'block' : 'none';
        });
    }

    function resetFilters() {
        // Reset all filter inputs
        document.getElementById('searchInput').value = '';
        document.getElementById('saleTypeFilter').value = 'all';
        document.getElementById('landTypeFilter').value = 'all';
        document.getElementById('propertyLocation').value = 'all';
        document.getElementById('minPrice').value = '';
        document.getElementById('maxPrice').value = '';
        document.getElementById('minArea').value = '';
        document.getElementById('maxArea').value = '';
        
        // Reset checkboxes
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });

        // Show all property cards
        document.querySelectorAll('.property-card').forEach(card => {
            card.style.display = 'block';
        });
    }

    // Add event listener for search input
    document.getElementById('searchInput').addEventListener('keyup', handleSearch);
    </script>

</body>

</html>
