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
              WHERE role_type = 'agent'";
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
elseif ($_SESSION['role_type'] !== 'agent') {
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
        .card{
            cursor: pointer;
        }

        .card:hover{
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }
    </style>
    <!-- Custom CSS footer-->
</head>

<body>

<div class="az-header">
    <?php require '../../partials/nav_agent.php' ?>
</div>



<div class="az-content">
    <div class="container">
        <div class="az-content-body">
            <div class="az-dashboard-one-title">
                <div>
                    <h2 class="az-dashboard-title">Developers</h2>
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

            <?php
require '../../db.php'; // Make sure your database connection is included

// Define the image path and default image
$image_path = "../../assets/developers/";
$default_image = "default.jpg";

// Developer images mapping
$developers = [
    "AYALA LAND" => "ayala.jpg",
    "SMDC" => "smdc.jpg",
    "VISTALAND" => "vistaland.jpg",
    "CAMELLA HOMES" => "camella.jpg",
    "PRO-FRIENDS" => "profriends.jpg",
    "DMCI" => "dm.jpg",
    "FILINVEST LAND" => "filinvest.jpg",
    "SM PRIME HOLDINGS" => "prime.jpg",
    "ROBINSON LAND CORPORATION" => "robinson.jpg",
    "FIDERAL LANDS" => "fideral.jpg",
    "CENTURY PROPERTIES GROUP" => "century.jpg"
];

// Fetch properties grouped by developers (case-insensitive)
$properties = [];
$query = "SELECT developer, property_name FROM properties";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $developer = strtoupper(trim($row['developer'])); // Normalize to uppercase
    $properties[$developer][] = $row['property_name']; // Store property names under each developer
}
?>

<div class="row">
    <?php foreach ($developers as $name => $image): ?>
        <div class="col-md-4">
            <div class="card m-1 rounded" style="width: 100%;" data-toggle="modal" data-target="#modal-<?= md5($name) ?>">
                <img src="<?= $image_path . $image ?>" class="card-img-top" style="height: 15vh; padding: 5px;" alt="<?= htmlspecialchars($name) ?>">
                <div class="card-body">
                    <h5 class="card-title text-center"><?= htmlspecialchars($name) ?></h5>
                </div>
            </div>
        </div>

        <!-- Bootstrap Modal -->
        <div class="modal fade" id="modal-<?= md5($name) ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel-<?= md5($name) ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel-<?= md5($name) ?>"><?= htmlspecialchars($name) ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <img src="<?= $image_path . $image ?>" class="img-fluid mb-3"  style="height: 15vh; padding: 5px;" alt="<?= htmlspecialchars($name) ?>">
                        </div>
                        <h6 class="text-center">Properties List</h6>
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Property Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $dev_key = strtoupper(trim($name)); // Normalize developer name to match DB
                                if (!empty($properties[$dev_key])): 
                                    $count = 1;
                                    foreach ($properties[$dev_key] as $property): ?>
                                        <tr>
                                            <td><?= $count++ ?></td>
                                            <td><?= htmlspecialchars($property) ?></td>
                                        </tr>
                                    <?php endforeach; 
                                else: ?>
                                    <tr>
                                        <td colspan="2" class="text-center">No properties found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
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