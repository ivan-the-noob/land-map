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
                    <h2 class="az-dashboard-title">Agents List</h2>
                    <p class="az-dashboard-text">Manage Agents</p>
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
            <div class="row mt-4">
                <div class="col-12">
                    <h4 class="text-center">Total Agents: <?php echo $total_agents; ?></h4>
                </div>
                
                <div class="main-box clearfix" id="user-tables">
                    <div class="table-responsive d-flex justify-content-center">
                        <table class="table table-striped user-list mx-auto">
                            <thead class="thead-light">
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th class="text-center">Status</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Location</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                              $limit = 5; // Number of agents per page
                              $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                              $offset = ($page - 1) * $limit;
                              
                              // Fetch total number of agents
                              $total_query = "SELECT COUNT(*) as total FROM users WHERE role_type = 'agent'";
                              $total_result = $conn->query($total_query);
                              $total_agents = $total_result->fetch_assoc()['total'];
                              $total_pages = ceil($total_agents / $limit);
                              
                              // Query to fetch paginated agents
                              $query = "SELECT user_id, profile, fname, lname, role_type, is_verified, email, mobile, location 
                                        FROM users 
                                        WHERE role_type = 'agent' 
                                        LIMIT $limit OFFSET $offset";
                              $result = $conn->query($query);
                                if ($result->num_rows > 0): 
                                    while ($user = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <img src="../../assets/profile_images/<?= htmlspecialchars($user['profile']) ?>" alt="" style="width: 50px; height: 50px; border-radius: 50%;" class="mr-2">
                                                <a href="#" class="user-link text-dark"><?= htmlspecialchars($user['fname'] . ' ' . $user['lname']) ?></a>
                                            </td>
                                            <td><?= htmlspecialchars($user['role_type']) ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-<?= $user['is_verified'] == 1 ? 'success' : 'secondary' ?>">
                                                    <?= $user['is_verified'] == 1 ? 'Active' : 'Inactive' ?>
                                                </span>
                                            </td>
                                            <td><a class="text-dark" href="mailto:<?= htmlspecialchars($user['email']) ?>"><?= htmlspecialchars($user['email']) ?></a></td>
                                            <td><?= htmlspecialchars($user['mobile']) ?></td>
                                            <td><?= htmlspecialchars($user['location']) ?></td>
                                            <td class="text-center">
                                                <!-- Edit Button -->
                                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editUserModal<?= $user['user_id'] ?>">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                
                                                <!-- Edit Modal -->
                                                <div class="modal fade" id="editUserModal<?= $user['user_id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Agent</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form class="editUserForm">
                                                                    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">

                                                                    <div class="form-group">
                                                                        <label>First Name</label>
                                                                        <input type="text" class="form-control" name="fname" value="<?= htmlspecialchars($user['fname']) ?>">
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label>Last Name</label>
                                                                        <input type="text" class="form-control" name="lname" value="<?= htmlspecialchars($user['lname']) ?>">
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label>Email</label>
                                                                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                                                                    </div>

                                                                    
                                                                    <div class="form-group">
                                                                        <label>Mobile</label>
                                                                        <input type="text" class="form-control" name="mobile" value="<?= htmlspecialchars($user['mobile']) ?>">
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label>Location</label>
                                                                        <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($user['location']) ?>">
                                                                    </div>

                                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Delete Button -->
                                                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteUserModal<?= $user['user_id'] ?>">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>

                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteUserModal<?= $user['user_id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Confirm Deletion</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">Are you sure you want to delete this user?</div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                <button type="button" class="btn btn-danger confirmUserDelete" data-user_id="<?= $user['user_id'] ?>">Delete</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; 
                                else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No users found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <script>
                        $(document).ready(function () {
                            $('.editUserForm').submit(function (e) {
                                e.preventDefault();
                                var form = $(this);
                                $.ajax({
                                    url: '../../backend/update_user.php',
                                    type : 'POST',
                                    data: form.serialize(),
                                    success: function (response) {
                                        if (response.trim() === 'success') {
                                            alert('User updated successfully!');
                                            $('.modal').modal('hide');
                                            setTimeout(function () {
                                                location.reload(true);
                                            }, 500);
                                        } else {
                                            alert('Update failed: ' + response);
                                        }
                                    },
                                    error: function () {
                                        alert('An error occurred while updating.');
                                    }
                                });
                            });

                            $('.confirmUserDelete').click(function () {
                                var userId = $(this).data('user_id');
                                $.ajax({
                                    url: '../../backend/delete_user.php',
                                    type: 'POST',
                                    data: { user_id: userId },
                                    success: function (response) {
                                        if (response.trim() === 'success') {
                                            alert('User deleted successfully!');
                                            $('.modal').modal('hide');
                                            setTimeout(function () {
                                                location.reload(true);
                                            }, 500);
                                        } else {
                                            alert('Delete failed: ' + response);
                                        }
                                    },
                                    error: function () {
                                        alert('An error occurred while deleting.');
                                    }
                                });
                            });
                        });
                        </script>
                    </div>
                </div>
                <!-- Agent List -->
                </div>
                <!-- page navigation -->
                <nav aria-label="Page navigation">
                    <ul class="pagination d-flex justify-content-end">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
                <!-- Page navigation -->

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