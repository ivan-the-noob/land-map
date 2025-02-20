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

    <title>Land Map | My Listings</title>
    <link rel="icon" href="../../assets/images/logo.png" type="image/x-icon">

    <!-- vendor css -->
    <link href="../../assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../../assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
    <link href="../../assets/lib/typicons.font/typicons.css" rel="stylesheet">
    <link href="../../assets/lib/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmgygVeipMUsrtGeZPZ9UzXRmcVdheIqw&libraries=places"></script>
    <!-- Mapping Links -->
    <script src="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.umd.js"></script>
    <link href="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.css" rel="stylesheet" />

    <!-- azia CSS -->
    <link rel="stylesheet" href="../../assets/css/azia.css">    

<body>
    <div class="az-header">
        <?php require '../../partials/nav_user.php' ?>
    </div>

    <div class="az-content az-content-dashboard">
        <div class="container">
            <div class="az-content-body">
                <div class="az-dashboard-one-title">
                    <div>
                        <h2 class="az-dashboard-title">My Appointments</h2>
                        <p class="az-dashboard-text">View your scheduled land viewing appointments</p>
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

                <div class="tab-content mt-4">
                    <div id="dashboard" class="tab-pane active">
                        <div id="dashboard" class="tab-pane">
                            <!-- Post new land property -->
                             
                            <h3 class="mb-1 mr-5">My Inquiries Land Properties</h3>
                            <div class="property-list">
                                
                            <?php
                                require '../../db.php';

                                $user_id = $_SESSION['user_id']; 

                                $sql = "SELECT p.*, 
                                        u.fname, u.lname,
                                        ui.image_name AS user_image,
                                        iq.status AS inquiry_status,
                                        p.user_id AS agent_id, 
                                        (SELECT image_name FROM property_images WHERE property_id = p.property_id LIMIT 1) AS property_image
                                        FROM properties p
                                        LEFT JOIN users u ON p.user_id = u.user_id
                                        LEFT JOIN user_img ui ON u.user_id = ui.user_id
                                        LEFT JOIN inquire iq ON p.property_id = iq.property_id AND iq.user_id = ?
                                        WHERE iq.property_id IS NOT NULL
                                        ORDER BY iq.created_at DESC;
                                        "
                                ;

                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $user_id);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $imagePath = $row['property_image'] ? "../../assets/property_images/" . $row['property_image'] : "../../assets/images/default-property.jpg";
                                        $agentImage = $row['user_image'] ? "../../assets/profile_images/" . $row['user_image'] : "../../assets/profile_images/profile.jpg";
                                        $agentName = $row['fname'] . ' ' . $row['lname'];
                                        $inquiryStatus = $row['inquiry_status']; 

                                        // Fetch report_status for the agent
                                        $agent_id = $row['agent_id'];
                                        $query = "SELECT report_status FROM users WHERE user_id = ?";
                                        $stmt_report = $conn->prepare($query);
                                        $stmt_report->bind_param("i", $agent_id);
                                        $stmt_report->execute();
                                        $stmt_report->bind_result($report_status);
                                        $stmt_report->fetch();
                                        $stmt_report->close();

                                        // Set button class and text based on inquiry status
                                        if ($inquiryStatus === 'pending') {
                                            $buttonClass = 'btn-update';
                                            $buttonText = 'Inquiry Pending';
                                        } elseif ($inquiryStatus === 'accepted') {
                                            $buttonClass = 'btn-success';
                                            $buttonText = 'Inquiry Accepted';
                                        } elseif ($inquiryStatus === 'completed') {
                                            $buttonClass = 'btn-success';
                                            $buttonText = 'Sold';
                                        } else { 
                                            $buttonClass = 'btn-danger';
                                            $buttonText = 'Inquiry Declined';
                                        }
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
                        <?php if ($row['sale_or_lease']) { ?>
                            <span><i class="fas fa-home"> Land Type:</i> <?php echo htmlspecialchars($row['sale_or_lease']); ?></span>
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
                    <button class="btn-view text-now" onclick="viewDetails(<?php echo $row['property_id']; ?>)">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        <button class="<?= $buttonClass ?>">
                            <i class="fas fa-clock"></i> <?= $buttonText ?>
                        </button>
                        

                    

                      
                       <!-- Report Button -->
                       <?php
                           

                             if ($report_status == 0 && $inquiryStatus !== 'completed') {
                                // Show "Report Agent" button if not reported
                                echo '<button class="btn-delete report-btn" data-toggle="modal" data-target="#reportModal" data-agent-id="' . $agent_id . '">
                                        <i class="fas fa-trash"></i> Report Agent
                                    </button>';
                            } else {
                                // Show "Reported" button if already reported
                               
                            }
                        ?>

                      

                        <!-- Bootstrap Modal -->
                        <div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="reportModalLabel">Report Agent</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                    <p>You are reporting: <strong><?php echo $agentName; ?></strong></p>
                                        <form id="reportForm">
                                            <input type="hidden" name="agent_id" id="agent_id">
                                            <div class="mb-3">
                                                <label for="report_reason" class="form-label">Report Reason</label>
                                                <textarea class="form-control" id="report_reason" name="report_reason" rows="4" required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-danger d-flex mx-auto">Submit Report</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            $(document).ready(function () {
                                // Capture the agent ID when the modal is triggered
                                $('.report-btn').on('click', function () {
                                    var agentId = $(this).data('agent-id');
                                    $('#agent_id').val(agentId);
                                });

                                // Handle form submission via AJAX
                                $('#reportForm').on('submit', function (e) {
                                    e.preventDefault(); // Prevent default form submission

                                    $.ajax({
                                        url: '../../backend/submit_report.php',
                                        type: 'POST',
                                        data: $(this).serialize() + '&user_id=<?php echo $_SESSION['user_id']; ?>',
                                        dataType: 'json',
                                        success: function (response) {
                                            if (response.success) {
                                                alert("Report submitted successfully!");
                                                $('#reportModal').modal('hide'); // Hide modal
                                                location.reload(); // Reload the page
                                            } else {
                                                alert("Error: " + response.error);
                                            }
                                        },
                                        error: function () {
                                            alert("Something went wrong. Please try again.");
                                        }
                                    });
                                });
                            });
                        </script>


                    </div>

                    <?php if ($row['inquiry_status'] == 'accepted'): ?>
                        <div class="agent-info">
                           
                            <span><i class="fas fa-user"> Agent Name:</i> <?php echo ($agentName); ?></span>
                            <button class="btn-contact" 
                                onclick="openChatModal(
                                    <?= $row['property_id']; ?>, 
                                    <?= $row['agent_id']; ?>, 
                                    '<?= htmlspecialchars($row['property_name'], ENT_QUOTES); ?>', 
                                    '<?= htmlspecialchars($row['property_type'], ENT_QUOTES); ?>', 
                                    '<?= htmlspecialchars($row['property_location'], ENT_QUOTES); ?>', 
                                    '<?= htmlspecialchars($row['sale_or_lease'], ENT_QUOTES); ?>', 
                                    <?= $row['land_area']; ?>, 
                                    <?= $row['sale_price']; ?>,
                                    <?= $row['monthly_rent']; ?>
                                )">
                                <i class="fas fa-user"></i> Message Agent
                            </button>

                        </div>
                    <?php endif; ?>
                    <div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="chatModalLabel">Chat with <?php echo ($agentName); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Chat Header -->
                                    <div class="header-chat p-2 rounded mb-2" style=" position: absolute; padding: 15px; border: 1px solid #ddd; border-radius: 5px; margin: 5px;">
                                        <button class="close-btn" onclick="hideHeaderChat()">×</button>
                                        <p class="mb-1"><strong>Property Name:</strong> <span id="chatPropertyName"></span></p>
                                        <p class="mb-1"><strong>Property Type:</strong> <span id="chatPropertyType"></span></p>
                                        <p class="mb-1"><strong>Location:</strong> <span id="chatPropertyLocation"></span></p>
                                        <p class="mb-1"><strong>Sale/Lease:</strong> <span id="chatSaleOrLease"></span></p>
                                        <p class="mb-1"><strong>Land Area:</strong> <span id="chatLandArea"></span> sqm</p>
                                        <p class="mb-1"><strong id="chatPriceLabel">Price:</strong> ₱<span id="chatSalePrice"></span></p>
                                         <button class="btn btn-primary mt-2" onclick="viewPropertyImages()">View Images</button>
                                    </div>
                                    <!-- Chat Messages -->
                                    <div id="chatMessages" class="chat-box"></div>

                                    <!-- Chat Input -->
                                    <div class="input-group mt-3 d-flex gap-2">
                                        <input type="text" id="chatInput" class="form-control" placeholder="Type your message...">
                                        <button class="btn btn-primary" onclick="sendMessage()">Send</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                  

                    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="imageModalLabel">Property Images</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body text-center">
                                    <div id="propertyImagesContainer"></div>
                                </div>
                            </div>
                        </div>
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

.chat-message {
    display: flex;
    margin-bottom: 10px;
}

.user-message {
    justify-content: flex-end; 
}

.agent-message {
    justify-content: flex-start; 
}

.message-box {
    padding: 10px;
    border-radius: 10px;
    max-width: 70%;
    word-wrap: break-word;
}

.user-message .message-box {
    background-color: #007bff;
    color: white;
    text-align: right;
}

.agent-message .message-box {
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

.header-chat{
    background-color:#02C39A;
    padding: 20px;
    border-radius: 5px;
    color: #fff;
    font-weight: 500;
    width: 50%;
}

.close-btn {
    position: absolute;
    top: 10px;
    right: 15px;
    background: none;
    border: none;
    font-size: 20px;
    font-weight: bold;
    cursor: pointer;
    color: #888;
}

.close-btn:hover {
    color: #333;
}

</style>

<script>

let propertyId, agentId;

function openChatModal(propId, agtId, propertyName, propertyType, propertyLocation, saleOrLease, landArea, salePrice, monthlyRent) {
    console.log("Opening chat for:", { propId, agtId, propertyName, propertyType, propertyLocation, saleOrLease, landArea, salePrice, monthlyRent });

    propertyId = propId;
    agentId = agtId;

    // Update chat header details
    document.getElementById("chatPropertyName").innerText = propertyName;
    document.getElementById("chatPropertyType").innerText = propertyType;
    document.getElementById("chatPropertyLocation").innerText = propertyLocation;
    document.getElementById("chatSaleOrLease").innerText = saleOrLease;
    document.getElementById("chatLandArea").innerText = landArea;

    // Get price elements
    let priceLabel = document.getElementById("chatPriceLabel");
    let priceValue = document.getElementById("chatSalePrice");

    // Change text dynamically based on Sale/Lease
    if (saleOrLease.toLowerCase() === "lease") {
        priceLabel.innerText = "Monthly Rent:";
        priceValue.innerText = new Intl.NumberFormat().format(monthlyRent);
    } else {
        priceLabel.innerText = "Price:";
        priceValue.innerText = new Intl.NumberFormat().format(salePrice);
    }

    // Open the modal
    $('#chatModal').modal('show');

    // Load previous messages
    loadMessages();
}



// Auto-refresh messages every 3 seconds
setInterval(loadMessages, 3000);

function hideHeaderChat() {
    let headerChat = document.querySelector(".header-chat");
    headerChat.style.transition = "opacity 0.3s ease";
    headerChat.style.opacity = "0";

    setTimeout(() => {
        headerChat.style.display = "none";
        headerChat.style.opacity = "1";
    }, 300);
}

function loadMessages() {
    if (!propertyId || !agentId) return;  

    $.ajax({
        url: "../../backend/load_messages.php",
        type: "POST",
        data: { property_id: propertyId, agent_id: agentId },
        success: function(response) {
            $('#chatMessages').html(response);
            $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight); 
        }
    });
}


let isSending = false; // Prevent multiple sends

function sendMessage() {
    let chatInput = $('#chatInput');
    let message = chatInput.val().trim();

    if (message === '' || isSending) return; // Prevent sending empty messages or multiple clicks

    isSending = true; // Lock sending
    $('#sendButton').prop('disabled', true); // Disable button to prevent spam clicks

    $.ajax({
        url: "../../backend/send_message.php",
        type: "POST",
        data: { property_id: propertyId, agent_id: agentId, message: message },
        success: function(response) {
            chatInput.val('');  
            loadMessages(); 
        },
        error: function(xhr, status, error) {
            console.log("Error sending message:", status, error);
        },
        complete: function() {
            isSending = false; // Unlock sending
            $('#sendButton').prop('disabled', false); // Re-enable button
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("chatInput").addEventListener("keydown", function (event) {
        if (event.key === "Enter" && !event.shiftKey) { 
            event.preventDefault();
            sendMessage();
        }
    });
});

function viewPropertyImages() {
    console.log("Fetching images for Property ID:", propertyId);

    $.ajax({
        url: "../../backend/load_property_images.php", // PHP script to fetch images
        type: "POST",
        data: { property_id: propertyId },
        success: function(response) {
            console.log("Images loaded:", response);
            $('#propertyImagesContainer').html(response);
            $('#imageModal').modal('show'); // Show modal after loading images
        },
        error: function(xhr, status, error) {
            console.log("Error loading images:", status, error);
        }
    });
}



function submitProperty(propertyId) {
    if(confirm('Are you sure you want to submit this property?')) {
        console.log('Submitting property:', propertyId);
    }
}

function updateProperty(propertyId) {
    window.location.href = 'edit_property.php?id=' + propertyId;
}

function deleteProperty(propertyId) {
    if (confirm('Are you sure you want to cancel this inquiry?')) {
        console.log('Cancelling inquiry for property:', propertyId);

        // Make an AJAX request to update the status to 'cancelled'
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../../backend/cancel_inquiry.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Handle the response (you can show a success message or update the UI)
                alert("Inquiry has been cancelled.");
                location.reload(); // Reload to reflect the changes
            }
        };
        xhr.send("property_id=" + propertyId);
    }
}


function viewDetails(propertyId) {
    console.log('Viewing details for property:', propertyId);
}

function contactAgent(userId) {
    console.log('Contacting agent:', userId);
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

                        /* Hides the InfoWindow close button */
                        .gm-ui-hover-effect {
                            display: none !important;
                        }

                    </style>


                    <script>
                       window.map = null;
                        window.allowedBounds = null;
                        let infoWindows = [];
                        let showInfo = true;

                        function initMap() {
    const caviteCenter = { lat: 14.2794, lng: 120.8786 };

    window.allowedBounds = new google.maps.LatLngBounds(
        { lat: 14.1325, lng: 120.6750 },
        { lat: 14.5050, lng: 121.0000 }
    );

    window.map = new google.maps.Map(document.getElementById("agentPropertyMaps"), { 
        center: caviteCenter,
        zoom: 10,
        mapTypeId: google.maps.MapTypeId.SATELLITE,
        restriction: {
            latLngBounds: window.allowedBounds,
            strictBounds: true
        },
        mapTypeControl: true
    });

    const streetView = window.map.getStreetView();

    fetch('../../backend/get_properties.php')
    .then(response => response.json())
    .then(properties => {
        if (!Array.isArray(properties)) {
            console.error("Invalid data format:", properties);
            return;
        }

        properties.forEach(property => {
            const { latitude, longitude, property_name, property_type, sale_price, monthly_rent, sale_or_lease, image_name, property_location, land_area } = property;

            if (!latitude || !longitude || isNaN(latitude) || isNaN(longitude)) {
                console.warn(`Skipping property: ${property_name} (Invalid coordinates)`);
                return;
            }

            const propertyLocation = new google.maps.LatLng(parseFloat(latitude), parseFloat(longitude));

            if (!window.allowedBounds.contains(propertyLocation)) {
                console.warn(`Skipping property: ${property_name} (Out of Cavite bounds)`);
                return;
            }

            let statusText = sale_or_lease ? (sale_or_lease.toLowerCase() === 'lease' ? 'For Lease' :
                                            sale_or_lease.toLowerCase() === 'sale' ? 'For Sale' : 'N/A') : 'N/A';

            let imageUrl = image_name ? `../../assets/property_images/${image_name}` : 'https://via.placeholder.com/150';

            const homeIcon = {
                url: "../../assets/images/land.png", 
                scaledSize: new google.maps.Size(40, 40), 
                origin: new google.maps.Point(0, 0), 
                anchor: new google.maps.Point(20, 40) 
            };

            const marker = new google.maps.Marker({
                position: propertyLocation,
                map: window.map,
                title: property_name,
                icon: homeIcon
            });

            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="white-space: nowrap; text-align: center;">
                        <img src="${imageUrl}" alt="${property_name}" style="width: 100%; height: 100px; object-fit: cover; border-radius: 5px;"><br>
                        <strong>${property_name}</strong><br>
                        <b>Location:</b> ${property_location || 'N/A'}<br>
                        <b>Type:</b> ${property_type || 'N/A'}<br>
                        <b>Status:</b> ${statusText}<br>
                        <div class="d-flex justify-content-center mx-auto align-items-center">
                        <b>Price:</b> ₱${sale_price ? parseInt(sale_price).toLocaleString("en-PH") : 'N/A'}
                        | ${land_area ? land_area + " sqm" : 'N/A'}</div>
                    </div>`,
                disableAutoPan: true
            });

            infoWindows.push({ marker, infoWindow });

            marker.addListener("click", () => {
                infoWindows.forEach(({ infoWindow }) => infoWindow.close()); 
                infoWindow.open(window.map, marker);

                // Move to Street View and place marker inside it
                streetView.setPosition(propertyLocation);
                streetView.setVisible(true);
            });
        });
    })
    .catch(error => console.error("Error fetching properties:", error));

    const showInfoControl = document.createElement("button");
    showInfoControl.textContent = "Show all info";
    showInfoControl.classList.add("show-info-btn");

    showInfoControl.style.fontSize = "14px"; 
    showInfoControl.style.fontWeight = "bold";
    showInfoControl.style.margin = "8px"; 
    showInfoControl.style.padding = "12px 20px"; 
    showInfoControl.style.background = "#fff";
    showInfoControl.style.border = "1px solid #ccc"; 
    showInfoControl.style.cursor = "pointer";
    showInfoControl.style.borderRadius = "5px";
    showInfoControl.style.boxShadow = "0 2px 4px rgba(0,0,0,0.2)"; 

    showInfoControl.addEventListener("click", () => {
        showInfo = !showInfo;
        infoWindows.forEach(({ marker, infoWindow }) => {
            if (showInfo) {
                infoWindow.open(window.map, marker);
            } else {
                infoWindow.close();
            }
        });
    });

    window.map.controls[google.maps.ControlPosition.TOP_LEFT].push(showInfoControl);
}

google.maps.event.addDomListener(window, 'load', initMap);


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
                            <h3 class="mb-1 mr-5">Post New Land</h3>

                            <!-- Tab-like Button -->
                        </div>
                        <form id="propertyForm" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 order-md-2">
                                    <div id="map" class="mb-3 position-relative">
                                        <label for="mapstyles" class="form-label">Select Map Style</label>
                                        <select name="mapstyles" id="mapstyles" class="form-select mapstyles-select">
                                            <optgroup label="Map Styles">
                                                <option value="STREETS">Streets</option>
                                                <option value="STREETS.DARK">Streets Dark</option>
                                                <option value="HYBRID" selected>Satellite</option>
                                            </optgroup>
                                        </select>

                                        <div class="custom-button-container d-flex justify-content-center">
                                            <button id="undo-last" class="custom-btn custom-btn-secondary mx-2">Undo
                                                Last</button>
                                            <button id="clear-all" class="custom-btn custom-btn-danger mx-2">Clear
                                                All</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 order-md-1">
                                    <!-- Property Type Dropdown (styled like the Property Name input) -->
                                    <div class="form-group">
                                        <label for="propertyType">Land Type</label>
                                        <select name="propertyType" class="form-control" id="propertyType">
                                            <option value="" selected>Select Land Type</option>
                                            <option value="Agricultural Farm">Agricultural Farm</option>
                                            <option value="Commercial Lot">Commercial Lot</option>
                                            <option value="Raw Land">Raw Land</option>
                                            <option value="Residential Land">Residential Land</option>
                                            <option value="Residential Farm">Residential Farm</option>
                                            <option value="Memorial Lot">Memorial Lot</option>
                                        </select>
                                    </div>

                                    <!-- Property Name -->
                                    <div class="form-group">
                                        <label for="propertyName">Property Name</label>
                                        <input name="propertyName" type="text" class="form-control" id="propertyName"
                                            placeholder="Enter property name" required>
                                    </div>

                                    <!-- Property Location -->
                                    <div class="form-group">
                                        <label for="propertyLocation">Location</label>
                                        <input name="propertyLocation" type="text" class="form-control"
                                            id="propertyLocation" placeholder="Enter baranggay, city, province" required>
                                    </div>

                                    <!-- Listing Type and Land Area -->
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="saleOrLease">Listing Type</label>
                                            <select name="saleOrLease" class="form-control" id="saleOrLease" required>
                                                <option value="">--</option>
                                                <option value="sale">For Sale</option>
                                                <option value="lease">For Lease</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="landArea">Land Area (sqm.)</label>
                                            <input name="landArea" type="number" class="form-control" id="landArea"
                                                placeholder="Enter land area in sqm" required>
                                        </div>
                                        <div id="leaseForm" class="hidden">
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="leaseDuration"
                                                        class="block text-gray-700 font-bold mb-2">Lease
                                                        Term</label>
                                                    <select name="leaseDuration" class="form-control" id="leaseDuration"
                                                        required>
                                                        <option value=""></option>
                                                        <option value="Short Term">Short term less than 1 year
                                                        </option>
                                                        <option value="Long Term">Long term more than 1 year</option>
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
                                                </div>
                                            </div>
                                        </div>
                                        <div id="saleForm" class="hidden">
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="landCondition"
                                                        class="block text-gray-700 font-bold mb-2">Land
                                                        condition</label>
                                                    <select name="landCondition" class="form-control" id="landCondition"
                                                        required>
                                                        <option value="" selected>All Types</option>
                                                        <option value="Resale">Resale</option>
                                                        <option value="foreClose">Foreclose/Acquired Assets</option>
                                                        <option value="pasalo">Pasalo/Assumed Balance</option>
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
                                                </div>
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
                                            required></textarea>
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
                                            other image formats</small>
                                    </div>

                                    <!-- Preview Images Section -->
                                    <div id="imagePreviewContainer" class="border rounded mt-3 p-2"
                                        style="display: none;">
                                        <h5 class="mb-3">Uploaded Images</h5>
                                        <div id="imagePreview" class="d-flex flex-wrap gap-3">
                                            <!-- Image previews will appear here -->
                                        </div>
                                    </div>
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