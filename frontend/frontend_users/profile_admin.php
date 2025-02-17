<?php
session_start();

// Initialize a variable to store error message for modal
$show_modal = false;
$error_message = '';

// Add database connection
require_once '../../db.php';

// Get the logged-in agent's information
$agent_fname = isset($_SESSION['fname']) ? $_SESSION['fname'] : 'Unknown';
$agent_lname = isset($_SESSION['lname']) ? $_SESSION['lname'] : 'Unknown';
$agent_location = isset($_SESSION['location']) ? $_SESSION['location'] : 'Unknown location';

// Query to get all agents with their last activity timestamp
try {
    $query = "SELECT *, 
              CASE 
                WHEN last_activity >= NOW() - INTERVAL 5 MINUTE THEN 'online'
                WHEN last_activity >= NOW() - INTERVAL 15 MINUTE THEN 'away'
                ELSE 'offline'
              END as current_status 
              FROM users 
              WHERE role_type = 'admin'";
    $result = $conn->query($query);
    
    if ($result) {
        $agents = $result->fetch_all(MYSQLI_ASSOC);
        $total_agents = count($agents);
    } else {
        throw new Exception("Query failed: " . $conn->error);
    }
} catch(Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
    $agents = [];
    $total_agents = 0;
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
elseif ($_SESSION['role_type'] !== 'user') {
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
    <title>Land Map | Profile</title>
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

<?php
// Fetch user details from the 'users' table
$user_id = $_SESSION['user_id'];

$query = "SELECT fname, lname, profile FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Set default profile image if none exists
$profileImage = !empty($user['profile']) ? "../../assets/profile_images/" . $user['profile'] : "../img/faces/default.jpg";
?>

<div class="az-content">
    <div class="container">
        <div class="az-content-body">
            <div class="az-dashboard-one-title">
                <div>
                    <h2 class="az-dashboard-title">Profile</h2>
                    <p class="az-dashboard-text">Update Information</p>
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

            <!-- Profile Section -->
            <div class="col-md-6 d-flex flex-column justify-content-center mx-auto">
            <div class="card">
                <div class="card-body text-center">
                    <h4>Profile Information</h4>
                    
                    <!-- Profile Image -->
                    <img id="profile-img" src="<?= $profileImage ?>" alt="Profile Image" class="rounded-circle" width="150" height="150">
                    <h4 class="mt-2"><?= htmlspecialchars($user['fname'] . ' ' . $user['lname']) ?></h4>
                    
                    <form id="profile-form" enctype="multipart/form-data" class="mt-3">
                      <!-- Profile Image Upload -->
                      <input type="file" name="profile" id="profile-input" class="form-control" accept="image/*">

                      <!-- First Name -->
                      <input type="text" name="fname" id="fname" class="form-control mt-2" 
                            value="<?= htmlspecialchars($user['fname']) ?>" placeholder="First Name">

                      <!-- Last Name -->
                      <input type="text" name="lname" id="lname" class="form-control mt-2" 
                            value="<?= htmlspecialchars($user['lname']) ?>" placeholder="Last Name">

                      <!-- Submit Button -->
                      <button type="submit" class="btn btn-primary mt-2">Update Profile</button>

                      <!-- Status Message -->
                      <div id="status-message" class="mt-2"></div>
                  </form>

                  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                  <script>
                 $(document).ready(function() {
                $("#profile-form").submit(function(e) {
                    e.preventDefault();

                    let formData = new FormData(this);

                    $.ajax({
                        url: "../../backend/update_profile.php",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        beforeSend: function() {
                            $("#status-message").html('<span class="text-info">Updating...</span>');
                        },
                        success: function(response) {
                            if (response.includes("successfully")) {
                                $("#status-message").html('<span class="text-success">' + response + '</span>');
                            } else {
                                $("#status-message").html('<span class="text-danger">' + response + '</span>');
                            }
                        },
                        error: function() {
                            $("#status-message").html('<span class="text-danger">Error updating profile.</span>');
                        }
                    });
                });
            });

                  </script>

                </div>
            </div>

            <!-- Change Password Section -->
            <div class="card mt-4">
                <div class="card-body">
                    <h3>Change Password</h3>
                    <form id="password-form">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Update Password</button>

                    <!-- Status Message -->
                    <div id="password-status" class="mt-2"></div>
                </form>

                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                $(document).ready(function() {
                    $("#password-form").submit(function(e) {
                        e.preventDefault(); 

                        let formData = $(this).serialize();

                        $.ajax({
                            url: "../../backend/user_changepassword.php",
                            type: "POST",
                            data: formData,
                            beforeSend: function() {
                                $("#password-status").html('<span class="text-info">Updating...</span>');
                            },
                            success: function(response) {
                                if (response.includes("successfully")) {
                                    $("#password-status").html('<span class="text-success">' + response + '</span>');
                                    $("#password-form")[0].reset();
                                } else {
                                    $("#password-status").html('<span class="text-danger">' + response + '</span>');
                                }
                            },
                            error: function() {
                                $("#password-status").html('<span class="text-danger">Error updating password.</span>');
                            }
                        });
                    });
                });
                </script>

                </div>
            </div>

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

<!-- Agent Modal Template -->
<?php foreach ($agents as $index => $agent): ?>
    <div class="modal fade" id="agentModal<?php echo $index; ?>" tabindex="-1" role="dialog" aria-labelledby="agentModalLabel<?php echo $index; ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agentModalLabel<?php echo $index; ?>">Agent Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <img src="../../assets/images/person_<?php echo ($index % 5) + 1; ?>.jpg" alt="Agent Photo" class="img-fluid rounded-circle" style="width: 150px; height: 150px;">
                    </div>
                    <div class="agent-details">
                        <h4 class="text-center mb-4">
                            <?php 
                                $fullName = htmlspecialchars($agent['fname'] . ' ' . $agent['lname']); 
                                echo $fullName;
                            ?>
                        </h4>
                        <div class="row">
                            <div class="col-md-12">
                                <p><strong><i class="fas fa-envelope"></i> Email:</strong> 
                                    <?php echo htmlspecialchars($agent['email']); ?>
                                </p>
                                <p><strong><i class="fas fa-phone"></i> Phone:</strong> 
                                    <?php echo isset($agent['phone']) ? htmlspecialchars($agent['phone']) : 'Not provided'; ?>
                                </p>
                                <p><strong><i class="fas fa-map-marker-alt"></i> Location:</strong> 
                                    <?php echo isset($agent['location']) ? htmlspecialchars($agent['location']) : 'Tanza, Cavite'; ?>
                                </p>
                                <p><strong><i class="fa fa-id-card"></i> PRC:</strong> 
                                    <?php echo isset($agent['prc']) ? htmlspecialchars($agent['prc']) : 'Not provided'; ?>
                                </p>
                                <p><strong><i class="fa fa-address-card"></i> DHSP:</strong> 
                                    <?php echo isset($agent['dshp']) ? htmlspecialchars($agent['dshp']) : 'Not provided'; ?>
                                </p>
                                <p><strong><i class="fas fa-briefcase"></i> Role:</strong> 
                                    <?php echo ucfirst(htmlspecialchars($agent['role_type'])); ?>
                                </p>
                                <!-- Add more fields as available in your database -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <a href="agent_profile.php?id=<?php echo $agent['id']; ?>" class="btn btn-primary">View Full Profile</a>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

</body>
</html>