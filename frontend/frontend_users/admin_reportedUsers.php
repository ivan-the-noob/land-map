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
              FROM report 
              WHERE user_id, agent_id = '0'";
    $result = $conn->query($query);
    
    if ($result) {
        $report = $result->fetch_all(MYSQLI_ASSOC);
        $total_report = count($report);
    } else {
        throw new Exception("Query failed: " . $conn->error);
    }
} catch(Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
    $report = [];
    $total_report = 0;
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
                    <h2 class="az-dashboard-title">Reported User List</h2>
                    <p class="az-dashboard-text">Manage Reported Users/Client</p>
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
                    <h4 class="text-center">Total Users Reports: <?php echo $total_report; ?></h4>
                </div>
                
                <div class="main-box clearfix" id="reports-section">
                                        <h3>Reported Reported</h3>
                                        <div class="table-responsive">
                                            <table class="table table-striped reports-list">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>User Name</th>
                                                        <th>Agent Name</th>
                                                        <th>Report Reason</th>
                                                        <th>Report To (Role)</th>
                                                        <th>Status</th>
                                                        <th class="text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                   $limit = 5; // Number of reports per page
                                                   $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
                                                   $offset = ($page - 1) * $limit;
                                                   
                                                   // Query to count total reports
                                                   $total_query = "SELECT COUNT(*) AS total FROM reports";
                                                   $total_result = $conn->query($total_query);
                                                   $total_reports = $total_result->fetch_assoc()['total'];
                                                   $total_pages = ceil($total_reports / $limit);
                                                   
                                                   // Fetch paginated reports
                                                   $query = "SELECT r.*, 
                                                   u1.fname AS user_fname, u1.lname AS user_lname, 
                                                   u2.fname AS agent_fname, u2.lname AS agent_lname, 
                                                   u3.role_type AS report_to_role, u3.disable_status, u3.user_id AS reported_user_id
                                                    FROM reports r
                                                    LEFT JOIN users u1 ON r.user_id = u1.user_id
                                                    LEFT JOIN users u2 ON r.agent_id = u2.user_id
                                                    LEFT JOIN users u3 ON r.report_to = u3.user_id
                                                    LIMIT $limit OFFSET $offset";
                                       
                                                   
                                                   $result = $conn->query($query);

                                                    if ($result->num_rows > 0):
                                                        while ($report = $result->fetch_assoc()): ?>
                                                            <tr>
                                                                <td>
                                                                    <a href="#" class="text-dark">
                                                                        <?= htmlspecialchars($report['user_fname'] . ' ' . $report['user_lname']) ?>
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <?= htmlspecialchars($report['agent_fname'] . ' ' . $report['agent_lname']) ?>
                                                                </td>
                                                                <td><?= htmlspecialchars($report['report_reason']) ?></td>
                                                                <td><?= htmlspecialchars($report['report_to_role']) ?></td>
                                                                <td class="text-center">
                                                                    <?php
                                                                    $disableStatus = $report['disable_status'];
 
                                                                    if ($disableStatus == 0) {
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
                                                                    <button class="btn btn-success btn-sm disable-user" data-user-id="<?= $report['reported_user_id'] ?>">
                                                                        <i class="fas fa-check"></i>
                                                                    </button>

                                                                    <!-- Delete Report -->
                                                                    <button class="btn btn-danger btn-sm delete-report" data-report-id="<?= $report['id'] ?>">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        <?php endwhile;
                                                    else: ?>
                                                        <tr>
                                                            <td colspan="6" class="text-center">No reports found.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <script>
                                        $(document).ready(function () {
                                        // Handle image click to open zoom modal
                                       

                                        // Disable user (Increment disable_status)
                                        $('.disable-user').on('click', function () {
                                            var userId = $(this).data('user-id');

                                            $.ajax({
                                                url: '../../backend/disable_users.php',
                                                type: 'POST',
                                                data: { user_id: userId },
                                                dataType: 'json',
                                                success: function (response) {
                                                    if (response.success) {
                                                        alert("User disabled successfully!");
                                                        location.reload();
                                                    } else {
                                                        alert("Error: " + response.error);
                                                    }
                                                }
                                            });
                                        });

                                        // Delete report
                                        $('.delete-report').on('click', function () {
                                            var reportId = $(this).data('report-id');

                                            $.ajax({
                                                url: '../../backend/delete_reports.php',
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
                
<div class="az-footer ht-40">
        <div class="container ht-100p pd-t-0-f">
            <span class="text-muted d-block text-center">Copyright ©LoremIpsum 2024</span>
        </div><!-- container -->
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