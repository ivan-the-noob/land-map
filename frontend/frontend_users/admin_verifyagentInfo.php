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
              WHERE admin_verify = '0'";
    $result = $conn->query($query);
    
    if ($result) {
        $admin_verify = $result->fetch_all(MYSQLI_ASSOC);
        $total_verify = count($admin_verify);
    } else {
        throw new Exception("Query failed: " . $conn->error);
    }
} catch(Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
    $verify = [];
    $total_verify = 0;
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
                    <h2 class="az-dashboard-title">User List</h2>
                    <p class="az-dashboard-text">Manage Users/Client</p>
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
            
            <!-- Agent List -->
            <div class="rows mt-4">
                <div class="col-12">
                    <h4 class="text-center">Total Agent Information: <?php echo $total_verify; ?></h4>
                </div>
                
                <div class="main-box clearfix" id="verify-info">
                                        <h3>Verify Agent Information</h3>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>PRC File</th>
                                                        <th>DSHP File</th>
                                                        <th>PRC ID</th>
                                                        <th>DSHP ID</th>
                                                        <th class="text-center">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $limit = 5;
                                                        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
                                                        $offset = ($page - 1) * $limit;
                                                    
                                                        // Query to get total users count
                                                        $total_query = "SELECT COUNT(*) AS total FROM users WHERE role_type = 'user'";
                                                        $total_result = $conn->query($total_query);
                                                        $total_users = $total_result->fetch_assoc()['total'];
                                                        $total_pages = ceil($total_users / $limit);
                                                 $query = "SELECT user_id, fname, lname, prc_file, dshp_file, prc_id, dshp_id, information_status 
                                                 FROM users 
                                                 WHERE information_status IN (2)
                                                 AND prc_file IS NOT NULL 
                                                 AND dshp_file IS NOT NULL 
                                                 AND prc_id IS NOT NULL 
                                                 AND dshp_id IS NOT NULL
                                                LIMIT $limit OFFSET $offset";
                                       
                                       $result = $conn->query($query);
                                       

                                                    if ($result->num_rows > 0):
                                                        while ($user = $result->fetch_assoc()): ?>
                                                            <tr>
                                                                <td><?= htmlspecialchars($user['fname'] . ' ' . $user['lname']) ?></td>
                                                                <td>
                                                                <?php if (!empty($user['prc_file'])): ?>
                                                                    <img src="../../assets/agent_information/<?= htmlspecialchars($user['prc_file']) ?>" 
                                                                        alt="PRC File" 
                                                                        class="img-thumbnail zoomable-image" 
                                                                        data-toggle="modal" 
                                                                        data-target="#imageModal" 
                                                                        data-image="../../assets/agent_information/<?= htmlspecialchars($user['prc_file']) ?>"
                                                                        width="50" height="50">
                                                                <?php else: ?>
                                                                    <span class="text-muted">No file</span>
                                                                <?php endif; ?>
                                                            </td>

                                                            <td>
                                                                <?php if (!empty($user['dshp_file'])): ?>
                                                                    <img src="../../assets/agent_information/<?= htmlspecialchars($user['dshp_file']) ?>" 
                                                                        alt="DSHP File" 
                                                                        class="img-thumbnail zoomable-image" 
                                                                        data-toggle="modal" 
                                                                        data-target="#imageModal" 
                                                                        data-image="../../assets/agent_information/<?= htmlspecialchars($user['dshp_file']) ?>"
                                                                        width="50" height="50">
                                                                <?php else: ?>
                                                                    <span class="text-muted">No file</span>
                                                                <?php endif; ?>
                                                            </td>

                                                                <td><?= htmlspecialchars($user['prc_id']) ?></td>
                                                                <td><?= htmlspecialchars($user['dshp_id']) ?></td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-success btn-sm verify-user" data-user-id="<?= $user['user_id'] ?>" data-status="3">
                                                                        ✅
                                                                    </button>
                                                                    <button class="btn btn-danger btn-sm verify-users" data-user-id="<?= $user['user_id'] ?>" data-status="1">
                                                                        ❌
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        <?php endwhile;
                                                    else: ?>
                                                        <tr>
                                                            <td colspan="6" class="text-center">No pending verifications.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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
                                    <!-- Image Modal -->
                                    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="imageModalLabel">View Image</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img id="modalImage" src="" alt="Zoomed Image" class="img-fluid">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        $(document).ready(function() {
                                            $(".zoomable-image").click(function() {
                                                let imageUrl = $(this).data("image");
                                                $("#modalImage").attr("src", imageUrl);
                                            });
                                        });

                                    </script>


                                    <script>
                                 $(document).ready(function() {
                                $(".verify-user").click(function() {
                                    let userId = $(this).data("user-id");
                                    let status = $(this).data("status");

                                    $.ajax({
                                        url: "../../backend/update_verification_status.php",
                                        type: "POST",
                                        data: { user_id: userId, status: status },
                                        success: function(response) {
                                            try {
                                                console.log("Raw Response:", response);

                                                // Ensure response is an object before using
                                                if (typeof response !== "object") {
                                                    response = JSON.parse(response);
                                                }

                                                if (response.success) {
                                                    location.reload();
                                                } else {
                                                    alert("Error: " + response.message);
                                                }
                                            } catch (e) {
                                                console.error("JSON Parse Error:", e, response);
                                                alert("Server response is invalid. Check console.");
                                            }
                                        },
                                        error: function(xhr, status, error) {
                                            console.error("AJAX Error:", status, error, xhr.responseText);
                                            alert("AJAX request failed.");
                                        }
                                    });
                                });
                            });

                                    </script>
                                      <script>
                                 $(document).ready(function() {
                                $(".verify-users").click(function() {
                                    let userId = $(this).data("user-id");
                                    let status = $(this).data("status");

                                    $.ajax({
                                        url: "../../backend/update_verification_status.php",
                                        type: "POST",
                                        data: { user_id: userId, status: status },
                                        success: function(response) {
                                            try {
                                                console.log("Raw Response:", response);

                                                // Ensure response is an object before using
                                                if (typeof response !== "object") {
                                                    response = JSON.parse(response);
                                                }

                                                if (response.success) {
                                                    location.reload();
                                                } else {
                                                    alert("Error: " + response.message);
                                                }
                                            } catch (e) {
                                                console.error("JSON Parse Error:", e, response);
                                                alert("Server response is invalid. Check console.");
                                            }
                                        },
                                        error: function(xhr, status, error) {
                                            console.error("AJAX Error:", status, error, xhr.responseText);
                                            alert("AJAX request failed.");
                                        }
                                    });
                                });
                            });

                                    </script>
           
                
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
       document.addEventListener("DOMContentLoaded", function () {
    // Handle View Button Click
    document.querySelectorAll(".view-agent").forEach(button => {
        button.addEventListener("click", function () {
            let agent = JSON.parse(this.getAttribute("data-agent"));

            // Populate Modal Fields
            document.getElementById("agentProfileImg").src = "../../assets/profile_images/" + agent.profile;
            document.getElementById("agentName").textContent = agent.fname + " " + agent.lname;
            document.getElementById("agentEmail").textContent = agent.email;
            document.getElementById("agentLocation").textContent = agent.location;
            document.getElementById("agentMobile").textContent = agent.mobile;

            document.getElementById("agentPrimaryIDType").textContent = agent.primary_id_type;
            document.getElementById("agentPrimaryIDNumber").textContent = agent.primary_id_number;
            document.getElementById("agentPrimaryIDImg").src = "../../assets/agents/" + agent.primary_id_image;

            document.getElementById("agentSecondaryIDType").textContent = agent.secondary_id_type;
            document.getElementById("agentSecondaryIDNumber").textContent = agent.secondary_id_number;
            document.getElementById("agentSecondaryIDImg").src = "../../assets/agents/" + agent.secondary_id_image;
        });
    });

    // Handle Approve Agent
    document.querySelectorAll(".approve-agent").forEach(button => {
        button.addEventListener("click", function () {
            let agentId = this.getAttribute("data-id");

            if (confirm("Are you sure you want to approve this agent?")) {
                fetch("../../backend/approve_agent.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "user_id=" + agentId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Agent approved successfully!");
                        location.reload();
                    } else {
                        alert("Error approving agent.");
                    }
                });
            }
        });
    });

    // Handle Decline Agent
    document.querySelectorAll(".decline-agent").forEach(button => {
        button.addEventListener("click", function () {
            let agentId = this.getAttribute("data-id");

            if (confirm("Are you sure you want to decline this agent?")) {
                fetch("../../backend/decline_agent.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "user_id=" + agentId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Agent declined successfully!");
                        location.reload();
                    } else {
                        alert("Error declining agent.");
                    }
                });
            }
        });
    });
});


    </script>

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