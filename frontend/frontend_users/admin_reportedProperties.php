<?php
session_start();

// Initialize a variable to store error message for modal
$show_modal = false;
$error_message = '';

// Add database connection
require_once '../../db.php';

// Query to get all agents with their last activity timestamp
try {
    $query = "SELECT *, 
              CASE 
                WHEN last_activity >= NOW() - INTERVAL 5 MINUTE THEN 'online'
                WHEN last_activity >= NOW() - INTERVAL 15 MINUTE THEN 'away'
                ELSE 'offline'
              END as current_status 
              FROM users 
              WHERE role_type = 'user'";
    $result = $conn->query($query);
    
    if ($result) {
        $users = $result->fetch_all(MYSQLI_ASSOC);
        $total_users = count($users);
    } else {
        throw new Exception("Query failed: " . $conn->error);
    }
} catch(Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
    $users = [];
    $total_users = 0;
}

// Update current user's last activity
if (isset($_SESSION['user_id'])) {
    $update_activity = "UPDATE users SET last_activity = NOW() WHERE user_id = ?";
    $stmt = $conn->prepare($update_activity);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

// Check if the user is logged in
if (!isset($_SESSION['role_type'])) {
    // If not logged in, set flag and message for modal
    $show_modal = true;
    $error_message = 'You must be logged in to access this page.';
}

// Check if the user is an user (if they are logged in)
elseif ($_SESSION['role_type'] !== 'admin') {
    // If not user, set flag and message for modal
    $show_modal = true;
    $error_message = 'You do not have the necessary permissions to access this page.';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Land Map | User List</title>
    <link rel="icon" href="../../assets/images/logo.png" type="image/x-icon">

    <!-- Vendor CSS -->
    <link href="../../assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../../assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
    <link href="../../assets/lib/typicons.font/typicons.css" rel="stylesheet">
    <link href="../../assets/lib/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/azia.css">
    <style>
        .agent-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #17c9a3;
        }
        
        .agent-box:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .agent-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .agent-location {
            color: #666;
            margin-bottom: 10px;
        }

        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .status-online {
            background-color: #28a745;
        }

        .status-offline {
            background-color: #dc3545;
        }

        .status-away {
            background-color: #ffc107;
        }

        .modal-content {
            border-radius: 15px;
        }
        
        .agent-details p {
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .agent-details i {
            margin-right: 10px;
            color: #17c9a3;
        }
        
        .modal-footer .btn-primary {
            background-color: #17c9a3;
            border-color: #17c9a3;
        }
        
        .modal-footer .btn-primary:hover {
            background-color: #14b391;
            border-color: #14b391;
        }
    </style>
    <!-- Custom CSS footer-->
</head>

<body>

<div class="az-header">
    <?php require '../../partials/nav_admin.php' ?>
</div>

<div class="az-content">
    <div class="container">
        <div class="az-content-body">
            <div class="az-dashboard-one-title">
                <div>
                    <h2 class="az-dashboard-title">Reported Property List</h2>
                    <p class="az-dashboard-text">Manage Reported Properties</p>
                </div>
                
                <!-- Time and Date -->
                <div class="az-content-header-right">
                    <div class="media">
                        <div class="media-body">
                            <label>Current Date</label>
                            <h6 id="current-date"></h6>
                        </div>
                    </div>
                    <div class="media">
                        <div class="media-body">
                            <label>Current Time</label>
                            <h6 id="current-time"></h6>
                        </div>
                    </div>
                    <div class="media">
                        <div class="media-body">
                            <label>Time Zone</label>
                            <h6>Philippine Time (PHT)</h6>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end time and date -->
            
            <!-- User List -->
             
            <div class="rows mt-4">
                <div class="col-12">
                    <h4 class="text-center">Total Reported Properties: <?php echo $total_users; ?></h4>
                </div>
                <div class="main-box clearfix" id="reports-section">
                <h3>Reported Properties</h3>
                                            <div class="table-responsive">
                                                <table class="table table-striped reports-list">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>User</th>
                                                            <th>Property</th> <!-- Updated from Property ID to Property (Image) -->
                                                            <th>Agent Name</th>
                                                            <th>Report Reason</th>
                                                            <th>Status</th>
                                                            <th class="text-center">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $limit = 5; // Number of properties per page
                                                        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
                                                        $offset = ($page - 1) * $limit;

                                                        // Query to count total properties
                                                        $total_query = "SELECT COUNT(*) AS total FROM report_properties";
                                                        $total_result = $conn->query($total_query);
                                                        $total_properties = $total_result->fetch_assoc()['total'];
                                                        $total_pages = ceil($total_properties / $limit);

                                                        // Fetch reported properties
                                                        $query = "SELECT * FROM report_properties LIMIT $limit OFFSET $offset";
                                                        $result = $conn->query($query);

                                                        if ($result->num_rows > 0):
                                                            while ($report = $result->fetch_assoc()):
                                                                $user_id = intval($report['user_id']);
                                                                $property_id = intval($report['property_id']);

                                                                // Fetch user (reporter) details
                                                                $userQuery = "SELECT fname, lname FROM users WHERE user_id = $user_id";
                                                                $userResult = $conn->query($userQuery);
                                                                $user = $userResult->fetch_assoc() ?? ['fname' => 'Unknown', 'lname' => 'User'];

                                                                // Fetch agent (property owner) details
                                                                $property_id = $report['property_id']; 

                                                                $agentQuery = "SELECT u.fname, u.lname, u.disable_status 
                                                                            FROM properties p 
                                                                            JOIN users u ON p.user_id = u.user_id 
                                                                            WHERE p.property_id = $property_id";

                                                                $agentResult = $conn->query($agentQuery);

                                                                if ($agentResult->num_rows > 0) {
                                                                    $agent = $agentResult->fetch_assoc();
                                                                } else {
                                                                    $agent = ['fname' => 'Unknown', 'lname' => 'Agent', 'disable_status' => null];
                                                                }


                                                                // Fetch property image
                                                                $imageQuery = "SELECT image_name FROM property_images WHERE property_id = $property_id LIMIT 1";
                                                                $imageResult = $conn->query($imageQuery);
                                                                $image = $imageResult->fetch_assoc();
                                                                $imagePath = !empty($image['image_name']) ? "../../assets/property_images/" . htmlspecialchars($image['image_name']) : "../../assets/no-image.jpg";
                                                        ?>
                                                                <tr>
                                                                    <td><?= htmlspecialchars($user['fname'] . ' ' . $user['lname']) ?></td>
                                                                    <td class="text-center">
                                                                        <a href="#" data-toggle="modal" data-target="#zoomModal" data-image="<?= $imagePath ?>">
                                                                            <img src="<?= $imagePath ?>" alt="Property Image" style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;">
                                                                        </a>
                                                                    </td>
                                                                    <td><?= htmlspecialchars($agent['fname'] . ' ' . $agent['lname']) ?></td>
                                                                    <td><?= htmlspecialchars($report['report_reason']) ?></td>
                                                                    <td class="text-center">
                                                                        <?php
                                                                        $disableStatus = $agent['disable_status'] ?? null;

                                                                        if ($disableStatus === null) {
                                                                            echo '<span class="badge badge-secondary">Unknown</span>';
                                                                        } elseif ($disableStatus == 0) {
                                                                            echo '<span class="badge badge-success">Active</span>';
                                                                        } elseif ($disableStatus == 1) {
                                                                            echo '<span class="badge badge-warning">Active 1 Warning</span>';
                                                                        } elseif ($disableStatus == 2) {
                                                                            echo '<span class="badge badge-warning">Active 2 Warnings</span>';
                                                                        } else {
                                                                            echo '<span class="badge badge-danger">Disabled</span>';
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <!-- Approve (Disable) -->
                                                                        <button class="btn btn-success btn-sm disable-user" data-property-id="<?= $report['property_id'] ?>">
                                                                            <i class="fas fa-check"></i>
                                                                        </button>

                                                                        <!-- Delete Report -->
                                                                        <button class="btn btn-danger btn-sm delete-report" data-report-id="<?= $report['id'] ?>">
                                                                            <i class="fas fa-times"></i>
                                                                        </button>

                                                                        <!-- Enable Property -->
                                                                        <button class="btn btn-warning text-white btn-sm enable-property" data-property-id="<?= $report['property_id'] ?>">
                                                                            Enable
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                        <?php endwhile;
                                                        else: ?>
                                                            <tr>
                                                                <td colspan="6" class="text-center">No reported properties found.</td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>     
                                    <script>
                                        $(document).ready(function () {
                                        // Handle image click to open zoom modal

                                       

                                        $('.disable-user').on('click', function () {
                                            var propertyId = $(this).data('property-id'); // Get property_id
                                            var userId = $(this).data('user-id'); // Get user_id (assuming it's set in the button)

                                            if (!confirm("Are you sure you want to disable this agent?")) {
                                                return;
                                            }

                                            $.ajax({
                                                url: '../../backend/disable_user.php',
                                                type: 'POST',
                                                data: { property_id: propertyId, user_id: userId }, // Send both property_id and user_id
                                                dataType: 'json',
                                                success: function (response) {
                                                    if (response.success) {
                                                        alert("Agent disabled successfully!");
                                                        location.reload();
                                                    } else {
                                                        alert("Error: " + response.error);
                                                    }
                                                },
                                                error: function () {
                                                    alert("AJAX request failed.");
                                                }
                                            });
                                        });

                                        $('.enable-property').on('click', function () {
                                        var propertyId = $(this).data('property-id'); // Get property_id

                                        if (!confirm("Are you sure you want to enable this property?")) {
                                            return;
                                        }

                                        $.ajax({
                                            url: '../../backend/enable_property.php', // New PHP file to handle enabling
                                            type: 'POST',
                                            data: { property_id: propertyId }, // Send property_id
                                            dataType: 'json',
                                            success: function (response) {
                                                if (response.success) {
                                                    alert("Property enabled successfully!");
                                                    location.reload();
                                                } else {
                                                    alert("Error: " + response.error);
                                                }
                                            },
                                            error: function () {
                                                alert("AJAX request failed.");
                                            }
                                        });
                                    });





                                        // Delete report
                                        $('.delete-report').on('click', function () {
                                            var reportId = $(this).data('report-id');

                                            $.ajax({
                                                url: '../../backend/delete_report.php',
                                                type: 'POST',
                                                data: { report_id: reportId },
                                                dataType: 'json',
                                                success: function (response) {
                                                    if (response.success) {
                                                        alert("Report deleted successfully!");
                                                        location.reload();
                                                    } else {
                                                        alert("Error: " + response.error);
                                                    }
                                                }
                                            });
                                        });
                                    });

                                        </script>
                                        </div>
                <!-- User List -->
                 
                </div>
                <!-- page navigation -->
                <nav aria-label="Page navigation">
                    <ul class="pagination d-flex justify-content-end">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= max(1, $page - 1) ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= min($total_pages, $page + 1) ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <!-- page navigation -->
                


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

<!-- Required Scripts -->
<script src="../../assets/lib/jquery/jquery.min.js"></script>
<script src="../../assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/lib/ionicons/ionicons.js"></script>
<script src="../../assets/js/azia.js"></script>

<script>
function viewAgentLands(agentId) {
    // Redirect to agent's land listings page
    window.location.href = 'agent_lands.php?id=' + agentId;
}

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
                window.location.href = '../../index.php';  // Redirect to the login page
            });
        });
    </script>

    <!--Signout process--->
    <script>
        // Show the sign-out confirmation modal when the Sign Out button is clicked
        document.getElementById('signOutButton').addEventListener('click', function () {
            $('#signOutModal').modal('show');  // Show the modal
        });

        // Confirm sign out (destroy session and redirect to login page)
        document.getElementById('confirmSignOutButton').addEventListener('click', function () {
            // Make a request to sign_out.php to destroy the session
            fetch('../../backend/sign_out.php', {
                method: 'GET'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // If sign out is successful, redirect to login page
                        window.location.href = '../../index.php'; // Redirect to login page
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
            const now = new Date().toLocaleString("en-US", { timeZone: "Asia/Manila" });

            // Format the time as hh:mm:ss AM/PM
            const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            const timeString = new Date(now).toLocaleTimeString('en-US', options);

            // Update the time on the page
            timeElement.textContent = timeString;
        }

        // Update the time every 1000 milliseconds (1 second)
        setInterval(updateTime, 1000);
    </script>



</body>
</html>