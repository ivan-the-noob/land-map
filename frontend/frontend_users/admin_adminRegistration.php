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
    <title>Land Map | Admin Registration</title>
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
                    <h2 class="az-dashboard-title">Admin Registration</h2>
                    <p class="az-dashboard-text">Manage Admin Registration</p>
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
            <div class="row mt-4">
                <div class="main-box" id="admin-registration-form">
                        
                        
                        <!-- PIN Code Verification Form -->
                        <div id="pin-verification">
                            <div class="form-group">
                                <label for="adminPin">Enter Admin PIN Code</label>
                                <input type="password" class="form-control" id="adminPin" placeholder="Enter PIN code" required>
                                <span class="error-message" id="pin_error" style="color:red;"></span>
                            </div>
                            <button type="button" class="btn btn-primary" id="verifyPin">Verify PIN</button>
                        </div>

                        <!-- Admin Registration Form (initially hidden) -->
                        <form id="adminRegistrationForm" style="display:none;">
                            <div class="form-group">
                                <label>First name</label>
                                <input name="fname" type="text" class="form-control" placeholder="Enter first name" required>
                                <span class="error-message" id="admin_first_name_error" style="color:red;"></span>
                            </div>
                            <div class="form-group">
                                <label>Last name</label>
                                <input name="lname" type="text" class="form-control" placeholder="Enter last name" required>
                                <span class="error-message" id="admin_last_name_error" style="color:red;"></span>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input name="email" type="email" class="form-control" placeholder="Enter email" required>
                                <span class="error-message" id="admin_email_error" style="color:red;"></span>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input name="password" type="password" class="form-control" placeholder="Enter password" required>
                                <span class="error-message" id="admin_password_error" style="color:red;"></span>
                            </div>
                            <button name="admin_signup_btn" type="submit" class="btn btn-primary">
                                <span id="adminButtonText">Register Admin</span>
                                <span id="adminLoadingSpinner" style="display: none;" class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true"></span>
                            </button>
                        </form>
                    </div>
                                      
                                    </div>
                <!-- User List -->
                </div>
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

<script>

// PIN verification handling
        document.getElementById('verifyPin').addEventListener('click', function() {
            const pin = document.getElementById('adminPin').value;
            const pinError = document.getElementById('pin_error');
            
            // Replace '1234' with your actual PIN code or implement server-side verification
            if (pin === '1234') {
                document.getElementById('pin-verification').style.display = 'none';
                document.getElementById('adminRegistrationForm').style.display = 'block';
                pinError.textContent = '';
            } else {
                pinError.textContent = 'Invalid PIN code';
            }
        });

        document.getElementById('adminRegistrationForm').addEventListener('submit', function(event) {
        event.preventDefault();

        // Clear previous error messages
        document.getElementById('admin_first_name_error').textContent = '';
        document.getElementById('admin_last_name_error').textContent = '';
        document.getElementById('admin_email_error').textContent = '';
        document.getElementById('admin_password_error').textContent = '';

        // Show loading spinner
        document.getElementById('adminButtonText').style.display = 'none';
        document.getElementById('adminLoadingSpinner').style.display = 'inline-block';

        const formData = new FormData(this);

        // Send form data using AJAX
        fetch('../../backend/admin_registration.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())  // Read as text first
        .then(text => {
            console.log("Response Text:", text); // Log the raw response text for debugging
            
            // Hide loading spinner
            document.getElementById('adminLoadingSpinner').style.display = 'none';
            document.getElementById('adminButtonText').style.display = 'inline-block';

            // Check if the response contains an error message
            if (text.includes("Error:")) {
                // Show error in alert
                alert(text);  // Error message from PHP
            } else {
                // Success case
                alert(text);  // Success message from PHP

                // Optionally, show success modal if needed
                $('#successModal').modal('show');
                document.querySelector('.modal-message').textContent = text;

                // Reload the page after successful registration
                window.location.reload();
            }
        })
        
    });

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