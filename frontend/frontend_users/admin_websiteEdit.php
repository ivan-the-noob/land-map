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
            <!-- User List -->
            <div class="row mt-4">
            <div class="main-box" id="website-design" style="display:none;">
                    <div class="button-container d-flex justify-content-end">
                        <!-- Reload Page button -->
                        <button id="reload-page-button" class="btn btn-primary">Reload Page</button>
                    </div>
                        
                        <div class="iframe-container" style="overflow: hidden;">
                                <!-- Iframe to display the webpage -->
                                <iframe id="website-viewer" src="../../index.php" width="100%" height="800px" frameborder="0"></iframe>
                            </div>

                            <script>
                                document.getElementById("website-viewer").onload = function() {
                                    var iframe = document.getElementById("website-viewer").contentWindow.document;
                                    var links = iframe.querySelectorAll("a");

                                    links.forEach(function(link) {
                                        link.addEventListener("click", function(event) {
                                            event.preventDefault(); // Prevent the default action
                                            link.style.pointerEvents = "none"; // Disable clicking
                                            link.style.color = "gray"; // Optional: Change color to indicate disabled state
                                        });
                                    });
                                };
                            </script>

                        <!-- Customization Form -->
                        <?php
                            $query = "SELECT * FROM cms LIMIT 1";
                            $result = $conn->query($query);
                            $cms = $result->fetch_assoc();
                        ?>
                     
                        <div class="appearance-settings-container">
                            <h2>EDIT CMS</h2>
                            <div class="card p-4 shadow col-md-12">
                                <form id="cmsForm" enctype="multipart/form-data">
                                    <div class="row">
                                    <div class="col-md-12 mb-3">
                                            <label class="form-label">Font Preview:</label>
                                            <div id="fontPreview" class="p-3 border rounded" style="font-family: <?= $cms['font_family'] ?? 'Arial'; ?>; font-size: <?= $cms['font_size'] ?? '16px'; ?>; font-style: <?= $cms['font_style'] ?? 'normal'; ?>;">
                                                This is a live preview of your font selection.
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Font Family:</label>
                                            <select name="font_family" id="fontFamily" class="form-control">
                                                <option value="Arial" <?= ($cms['font_family'] ?? '') == "Arial" ? "selected" : ""; ?>>Arial</option>
                                                <option value="Verdana" <?= ($cms['font_family'] ?? '') == "Verdana" ? "selected" : ""; ?>>Verdana</option>
                                                <option value="Times New Roman" <?= ($cms['font_family'] ?? '') == "Times New Roman" ? "selected" : ""; ?>>Times New Roman</option>
                                                <option value="Courier New" <?= ($cms['font_family'] ?? '') == "Courier New" ? "selected" : ""; ?>>Courier New</option>
                                                <option value="Georgia" <?= ($cms['font_family'] ?? '') == "Georgia" ? "selected" : ""; ?>>Georgia</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Font Style:</label>
                                            <select name="font_style" id="fontStyle" class="form-control">
                                                <option value="normal" <?= ($cms['font_style'] ?? '') == "normal" ? "selected" : ""; ?>>Normal</option>
                                                <option value="italic" <?= ($cms['font_style'] ?? '') == "italic" ? "selected" : ""; ?>>Italic</option>
                                                <option value="bold" <?= ($cms['font_style'] ?? '') == "bold" ? "selected" : ""; ?>>Bold</option>
                                                <option value="bold italic" <?= ($cms['font_style'] ?? '') == "bold italic" ? "selected" : ""; ?>>Bold Italic</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Font Size: <span id="fontSizeValue"><?= $cms['font_size'] ?? '16px'; ?></span></label>
                                            <input type="range" name="font_size" id="fontSize" class="form-range" min="12" max="100" step="1" value="<?= intval($cms['font_size'] ?? 16); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Logo:</label>
                                            <input type="file" name="logo" class="form-control">
                                            <?php if (!empty($cms['logo'])): ?>
                                                <img src="../../assets/images/cms/<?= htmlspecialchars($cms['logo']); ?>" class="mt-2 img-thumbnail" width="100">
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Image:</label>
                                            <input type="file" name="img" class="form-control">
                                            <?php if (!empty($cms['img'])): ?>
                                                <img src="../../assets/images/cms/<?= htmlspecialchars($cms['img']); ?>" class="mt-2 img-thumbnail" width="100">
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Text:</label>
                                            <input type="text" name="text" class="form-control" value="<?= htmlspecialchars($cms['text'] ?? ''); ?>">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Animation Text:</label>
                                            <input type="text" name="animation_text" class="form-control" value="<?= htmlspecialchars($cms['animation_text'] ?? ''); ?>">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Background Color:</label>
                                            <div class="input-group">
                                                <input type="text" id="background_color" name="background_color" class="form-control" value="<?= htmlspecialchars($cms['background_color'] ?? '#808080'); ?>">
                                                <input type="color" id="colorPicker" class="form-control form-control-color" value="<?= htmlspecialchars($cms['background_color'] ?? '#808080'); ?>" style="width: 50px; border: none; cursor: pointer;">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Land Services:</label>
                                            <textarea name="land_services" class="form-control" rows="3"><?= htmlspecialchars($cms['land_services'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="col-md-12">                                                       
                                            <div id="responseMessage" class="w-25 fw-bold"></div>
                                        </div>
                                       

                                        <hr>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">About Page:</label>
                                            <textarea name="about_page" class="form-control" rows="3" ><?= htmlspecialchars($cms['about_page'] ?? ''); ?></textarea>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Contact Email:</label>
                                            <input type="email" name="contact_email" class="form-control" value="<?= htmlspecialchars($cms['contact_email'] ?? ''); ?>" >
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Contact Number:</label>
                                            <input type="text" name="contact_number" class="form-control" value="<?= htmlspecialchars($cms['contact_number'] ?? ''); ?>" >
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Contact Location:</label>
                                            <input type="text" name="contact_location" class="form-control" value="<?= htmlspecialchars($cms['contact_location'] ?? ''); ?>" >
                                        </div>

                                        <div class="col-md-12 d-flex justify-content-center mx-auto">
                                               <div id="responseMessage"></div>
                                            <button type="submit" class="btn btn-primary w-50">Update</button>
                                        </div>
                                    </div>
                                </form>

                                <script>
    $(document).ready(function() {
        $("#cmsForm").on("submit", function(e) {
            e.preventDefault(); 

            var formData = new FormData(this);

            $.ajax({
                url: "../../backend/process_cms.php", 
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $("#responseMessage").html('<div class="alert alert-info">Updating...</div>').fadeIn();
                },
                success: function(response) {
                    console.log("Server Response: ", response);
                    $("#responseMessage").html('<div class="alert alert-success">' + response + '</div>').fadeIn().delay(3000).fadeOut();
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", xhr.responseText);
                    $("#responseMessage").html('<div class="alert alert-danger">An error occurred: ' + error + '</div>').fadeIn().delay(3000).fadeOut();
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

                    <div class="main-box" id="navigation-management" style="display:none;">
                        <h3>Navigation Management</h3>
                        <div class="nav-type-selector mb-4">
                            <select id="navTypeSelector" class="form-control">
                                <option value="landing">Landing Navigation</option>
                                <option value="user">User Navigation</option>
                                <option value="agent">Agent Navigation</option>
                                <option value="home">Home Navigation</option>
                            </select>
                        </div>

                        <div class="nav-items-container">
                            <div id="navigationItems" class="list-group">
                                <!-- Navigation items will be loaded here dynamically -->
                            </div>
                            
                            <button class="btn btn-primary mt-3" id="addNavItem">
                                <i class="fas fa-plus"></i> Add Navigation Item
                            </button>
                        </div>

                        <!-- Add/Edit Navigation Item Modal -->
                        <div class="modal fade" id="navItemModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Navigation Item</h5>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="navItemForm">
                                            <input type="hidden" id="navItemId">
                                            <div class="form-group">
                                                <label>Label</label>
                                                <input type="text" class="form-control" id="navLabel" required>
                                            </div>
                                            <div class="form-group">
                                                <label>URL</label>
                                                <input type="text" class="form-control" id="navUrl" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Icon (Font Awesome or Typicons class)</label>
                                                <input type="text" class="form-control" id="navIcon">
                                            </div>
                                            <div class="form-group">
                                                <label>Parent Menu</label>
                                                <select class="form-control" id="navParent">
                                                    <option value="">None</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Order Position</label>
                                                <input type="number" class="form-control" id="navOrder" min="0">
                                            </div>
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="navActive">
                                                    <label class="custom-control-label" for="navActive">Active</label>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary" id="saveNavItem">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Registration Form Section -->
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

    <!-- Add this script before the closing body tag -->
    <script>
    function showSection(sectionId) {
        // Hide all sections first
        document.getElementById('website-design').style.display = 'none';
        document.getElementById('navigation-management').style.display = 'none';
        
        // Show the selected section
        document.getElementById(sectionId).style.display = 'block';
    }

    // Show website design section by default
    document.addEventListener('DOMContentLoaded', function() {
        showSection('website-design');
    });
    </script>

    

</body>
</html>