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
    <title>Land Map | Agent Registration</title>
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
                    <h2 class="az-dashboard-title">Agent Registration</h2>
                    <p class="az-dashboard-text">Manage Agent Registration</p>
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
                <!-- Agent Registration Form Section -->
                <div class="main-box" id="registration-form">
                    <h3>Agent Registration</h3>
                    <form action="../../backend/agent_registration.php" method="POST" id="agentRegistrationForm" enctype="multipart/form-data">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">First name</label>
                                    <input name="first_name" id="first_name" type="text" class="form-control" placeholder="Enter first name" required>
                                    <span class="error-message" id="first_name_error" style="color:red;"></span>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input name="email" id="email" type="email" class="form-control" placeholder="Enter email" required>
                                    <span class="error-message" id="email_error" style="color:red;"></span>
                                </div>
                                <div class="form-group">
                                    <label for="location">Location</label>
                                    <input name="location" id="location" type="text" class="form-control" placeholder="Enter City/Province" required>
                                    <span class="error-message" id="location_error" style="color:red;"></span>
                                </div>
                                <div class="form-group">
                                    <label>Valid IDs (select up to 2)</label>
                                    <div class="row">
                                        <!-- Primary ID Column -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="primary_id_type">Primary ID (Select one)</label>
                                                <select id="primary_id_type" name="primary_id_type" class="form-control" required>
                                                    <option value="">Select Primary ID</option>
                                                    <option value="passport">PH Passport</option>
                                                    <option value="sss">SSS ID</option>
                                                    <option value="gsis">GSIS ID</option>
                                                    <option value="drivers_license">Driver's License</option>
                                                    <option value="nbi">NBI Clearance</option>
                                                    <option value="voters_id">Voter's ID</option>
                                                    <option value="voters_cert">Voter's Certificate</option>
                                                </select>
                                                <input name="primary_id_number" id="primary_id_number" type="text" class="form-control mt-2" placeholder="Enter ID Number" required>
                                                <input name="primary_id_image" id="primary_id_image" type="file" class="form-control mt-2" accept="image/*" required>
                                            </div>
                                        </div>
                                        
                                        <!-- Secondary ID Column -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="secondary_id_type">Secondary ID (Select one)</label>
                                                <select id="secondary_id_type" name="secondary_id_type" class="form-control" required>
                                                    <option value="">Select Secondary ID</option>
                                                    <option value="philhealth">PhilHealth ID</option>
                                                    <option value="national">National ID</option>
                                                    <option value="postal">Postal ID (2015 onwards)</option>
                                                    <option value="company">Company ID</option>
                                                    <option value="otherid">Other ID</option>
                                                </select>
                                                <input name="secondary_id_number" id="secondary_id_number" type="text" class="form-control mt-2" placeholder="Enter ID Number" required>
                                                <input name="secondary_id_image" id="secondary_id_image" type="file" class="form-control mt-2" accept="image/*" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <small class="form-text text-muted">Please upload clear scanned copies or photos of your IDs</small>
                                    <span class="error-message" id="valid_ids_error" style="color:red;"></span>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">Last name</label>
                                    <input name="last_name" id="last_name" type="text" class="form-control" placeholder="Enter last name" required>
                                    <span class="error-message" id="last_name_error" style="color:red;"></span>
                                </div>
                                <div class="form-group">
                                    <label for="mobile">Mobile Number</label>
                                    <input name="mobile" id="mobile" type="tel" class="form-control" placeholder="Enter mobile number (e.g., +63 912 345 6789)" required pattern="^\+63[0-9]{10}$" inputmode="numeric">
                                    <span class="error-message" id="mobile_error" style="color:red;"></span>
                                </div>
                                <div class="form-group">
                                    <label for="profile_image">Profile Image</label>
                                    <input name="profile_image" id="profile_image" type="file" class="form-control" accept="image/*" required>
                                    <span class="error-message" id="profile_image_error" style="color:red;"></span>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <div class="input-group">
                                        <input name="password" id="password" type="password" class="form-control" placeholder="Enter Password" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">Show</button>
                                        </div>
                                    </div>
                                    <span class="error-message" id="password_error" style="color:red;"></span>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Confirm Password</label>
                                    <div class="input-group">
                                        <input name="confirm_password" id="confirm_password" type="password" class="form-control" placeholder="Confirm Password" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">Show</button>
                                        </div>
                                    </div>
                                    <span class="error-message" id="confirm_password_error" style="color:red;"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button (Full Width) -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <span id="buttonText">Register agent</span>
                                    <span id="loadingSpinner" style="display: none;" class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- User List -->
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
    document.getElementById('agentRegistrationForm').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent form from submitting the traditional way

        // Hide error messages before submitting the form
        document.getElementById('first_name_error').textContent = '';
        document.getElementById('last_name_error').textContent = '';
        document.getElementById('email_error').textContent = '';
        document.getElementById('password_error').textContent = '';

        // Show loading spinner
        document.getElementById('buttonText').style.display = 'none';
        document.getElementById('loadingSpinner').style.display = 'inline-block';

        const formData = new FormData(this);

        // Send form data using AJAX
        fetch('../../backend/agent_registration.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text()) // Use text() first to debug raw output
        .then(data => {
            console.log("Raw Response:", data); // Log full response to check if it's valid JSON
            try {
                let jsonData = JSON.parse(data);
                console.log("Parsed JSON:", jsonData);

                // Hide loading spinner
                document.getElementById('loadingSpinner').style.display = 'none';
                document.getElementById('buttonText').style.display = 'inline-block';

                if (jsonData.success) {
                    // Show the success modal
                    $('#successModal').modal('show');
                } else {
                    // Show validation errors if any
                    for (const key in jsonData.errors) {
                        const errorElement = document.getElementById(`${key}_error`);
                        if (errorElement) {
                            errorElement.textContent = jsonData.errors[key];
                        }
                    }
                }
            } catch (error) {
                console.error("JSON Parse Error:", error, data);
                alert("An error occurred while processing your request. Check the console for details.");
            }
        })
        .catch(error => {
            // Handle unexpected errors
            console.error('Fetch Error:', error);
            alert("A network error occurred. Please try again.");
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('buttonText').style.display = 'inline-block';
        });
    });

    // Redirect or close the modal when OK button is clicked
    document.getElementById('okButton').addEventListener('click', function () {
        $('#successModal').modal('hide');
        window.location.href = '../../frontend/frontend_users/admin_control.php';
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