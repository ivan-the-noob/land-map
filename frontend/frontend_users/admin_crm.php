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
    <title>Land Map | CRM</title>
    <link rel="icon" href="../../assets/images/logo.png" type="image/x-icon">

    <!-- Vendor CSS -->
    <link href="../../assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../../assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
    <link href="../../assets/lib/typicons.font/typicons.css" rel="stylesheet">
    <link href="../../assets/lib/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
    
<!-- Required Scripts -->
<script src="../../assets/lib/jquery/jquery.min.js"></script>
<script src="../../assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/lib/ionicons/ionicons.js"></script>
<script src="../../assets/js/azia.js"></script>

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
                    <h2 class="az-dashboard-title">ADMIN CRM</h2>
                    <p class="az-dashboard-text"></p>
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
            <div class="crm">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                    <tr>
                        <th class="text-white">Name</th>
                        <th class="text-white">Land Type</th>
                        <th class="text-white">Status</th>
                        <th class="text-white">Location</th>
                        <th class="text-white">Land Area</th>
                        <th class="text-white">List Type</th>
                        <th class="text-white">Price </th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                        require '../../db.php'; 
                        
                        $query = "
                        SELECT 
                            CONCAT(COALESCE(u.fname, ''), ' ', COALESCE(u.lname, '')) AS Name,
                            COALESCE(p.property_type, 'N/A') AS LandType,
                            COALESCE(i.status, 'N/A') AS Status,
                            p.sale_or_lease,
                            p.property_location,
                            p.land_area,
                            CASE 
                                WHEN p.sale_or_lease = 'lease' THEN p.monthly_rent
                                WHEN p.sale_or_lease = 'sale' THEN p.sale_price
                                ELSE 'N/A' 
                            END AS PriceOrRent,
                            i.created_at
                        FROM inquire i
                        LEFT JOIN users u ON i.user_id = u.user_id
                        LEFT JOIN properties p ON i.property_id = p.property_id
                        ORDER BY i.created_at DESC
                    ";
                    

                        $result = mysqli_query($conn, $query);

                        if ($result) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                        <td>{$row['Name']}</td>
                                        <td>{$row['LandType']}</td>
                                        <td>{$row['Status']}</td>
                                        <td>{$row['property_location']}</td>
                                        <td>{$row['land_area']}</td>
                                        <td>{$row['sale_or_lease']}</td>
                                        <td>{$row['PriceOrRent']}</td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No data found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
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
    

    <script>
    $(document).ready(function () {
        console.log("jQuery Loaded:", $.fn.jquery); // Debugging
        console.log("Bootstrap Modal Loaded:", typeof $.fn.modal); // Debugging

        // Show the sign-out confirmation modal
        $('#signOutButton').on('click', function () {
            $('#signOutModal').modal('show'); // Show the modal
        });

        // Confirm sign out (destroy session and redirect to login page)
        $('#confirmSignOutButton').on('click', function () {
            fetch('../../backend/sign_out.php', { method: 'GET' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '../../index.php';
                    } else {
                        alert('Error: Could not sign out.');
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });
</script>

<script>
    jQuery.noConflict();
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

    

    <!--Signout process--->
   

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