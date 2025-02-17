<?php
session_start();
// Assuming the login is successful
$_SESSION['login_success'] = true;

// Initialize a variable to store error message for modal
$show_modal = false;
$error_message = '';

// Check if the user is logged in
if (!isset($_SESSION['role_type'])) {
    // If not logged in, set flag and message for modal
    $show_modal = true;
    $error_message = 'You must be logged in to access this page.';
}

// Check if the user is an admin (if they are logged in)
elseif ($_SESSION['role_type'] !== 'admin') {
    // If not admin, set flag and message for modal
    $show_modal = true;
    $error_message = 'You do not have the necessary permissions to access this page.';
}

require '../../db.php';

// Fetch total users
$sql_users = "SELECT COUNT(*) as total FROM users";
$result_users = $conn->query($sql_users);
$totalUsers = $result_users->fetch_assoc()['total'];

// Fetch total agents
$sql_agents = "SELECT COUNT(*) as total FROM users WHERE role_type = 'agent'";
$result_agents = $conn->query($sql_agents);
$totalAgents = $result_agents->fetch_assoc()['total'];

// Fetch total properties
$sql_properties = "SELECT COUNT(*) as total FROM properties";
$result_properties = $conn->query($sql_properties);
$totalProperties = $result_properties->fetch_assoc()['total'];

// Calculate property growth (comparing to last month)
$sql_growth = "SELECT 
    COALESCE(
        ((COUNT(*) - LAG(COUNT(*)) OVER (ORDER BY MONTH(created_at))) / 
        NULLIF(LAG(COUNT(*)) OVER (ORDER BY MONTH(created_at)), 0) * 100
    ), 0) as growth
    FROM properties 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 2 MONTH)
    GROUP BY MONTH(created_at)
    ORDER BY MONTH(created_at) DESC 
    LIMIT 1";
$result_growth = $conn->query($sql_growth);
$propertyGrowth = $result_growth ? ($result_growth->fetch_assoc()['growth'] ?? 0) : 0;

// Check if property_views table exists and fetch total views
$totalViews = 0;
$result = $conn->query("SHOW TABLES LIKE 'property_views'");
if ($result->num_rows > 0) {
    $sql_views = "SELECT COUNT(*) as total FROM property_views";
    $result_views = $conn->query($sql_views);
    $totalViews = $result_views->fetch_assoc()['total'];
}

// Fetch top properties (without view counts if table doesn't exist)
$sql_top = "SELECT p.property_id, 
            p.property_name,
            p.property_location,
            0 as views,
            0 as growth
            FROM properties p
            ORDER BY p.created_at DESC
            LIMIT 5";
$result_top = $conn->query($sql_top);
$topProperties = [];
while($row = $result_top->fetch_assoc()) {
    $topProperties[] = $row;
}

// Calculate user type percentages
$sql_buyers = "SELECT COUNT(*) as total FROM users WHERE role_type = 'user'";
$result_buyers = $conn->query($sql_buyers);
$totalBuyers = $result_buyers->fetch_assoc()['total'];

$sql_viewers = "SELECT COUNT(*) as total FROM users WHERE role_type = 'viewer'";
$result_viewers = $conn->query($sql_viewers);
$totalViewers = $result_viewers->fetch_assoc()['total'];

$totalUsers = $totalAgents + $totalBuyers + $totalViewers;
$agentPercentage = ($totalUsers > 0) ? round(($totalAgents / $totalUsers) * 100) : 0;
$buyerPercentage = ($totalUsers > 0) ? round(($totalBuyers / $totalUsers) * 100) : 0;
$viewerPercentage = ($totalUsers > 0) ? round(($totalViewers / $totalUsers) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-90680653-2"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'UA-90680653-2');
  </script>

  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Land Map | ADMIN</title>
  <link rel="icon" href="../../assets/images/logo.png" type="image/x-icon">

  <!-- vendor css -->
  <link href="../../assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../../assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
  <link href="../../assets/lib/typicons.font/typicons.css" rel="stylesheet">
  <link href="../../assets/lib/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">

  <!-- Mapping Links -->
  <script src="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.umd.js"></script>
  <link href="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.css" rel="stylesheet" />

  <!--di pa sure kung buburahin-->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>

  <!-- azia CSS -->
  <link rel="stylesheet" href="../../assets/css/azia.css">
  <link rel="stylesheet" href="../../assets/css/profile.css">

  <style>
    /* Foggy effect for the entire screen */
    .modal-backdrop {
        backdrop-filter: blur(100px) brightness(200); 
        -webkit-backdrop-filter: blur(100px) brightness(400);
        background-color: rgba(255, 255, 255, 0.4);
    }

    /* Modal content styling */
    .warning-modal-content {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .warning-icon {
        font-size: 50px;
        color: #dc3545;
    }

    .warning-btn-danger {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
    }

    .warning-btn-danger:hover {
        background-color: #c82333;
    }
</style>

</head>

<body>

<div class="az-header">
    <?php require '../../partials/nav_admin.php' ?>
</div><!-- az-header -->

<div class="az-content az-content-dashboard">
    <div class="container">
        <div class="az-content-body">
            <div class="az-dashboard-one-title">
                <div>
                    <h2 class="az-dashboard-title">Admin Dashboard</h2>
                    <p class="az-dashboard-text">Explore and manage your users and lands properties</p>
                </div>
                <!-- Time and Date -->
                <div class="az-content-header-right">
                        <div class="media">
                            <div class="media-body">
                                <label>Current Date</label>
                                <h6 id="current-date"></h6>
                            </div><!-- media-body -->
                        </div><!-- media -->
                        <div class="media">
                            <div class="media-body">
                                <label>Current Time</label>
                                <h6 id="current-time"></h6>
                            </div><!-- media-body -->
                        </div><!-- media -->
                        <div class="media">
                            <div class="media-body">
                                <label>Time Zone</label>
                                <h6>Philippine Time (PHT)</h6>
                            </div><!-- media-body -->
                        </div><!-- media -->
                    </div>
                    <script>
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
                    <!-- Time and Date footer -->


            </div><!-- az-dashboard-one-title -->

            <div class="az-dashboard-nav">
                <nav class="nav">
                    <a class="nav-link active" data-toggle="tab" href="#overview">Overview</a>
                    <a class="nav-link" data-toggle="tab" href="#landTypes">Land Types</a>
                    <a class="nav-link" data-toggle="tab" href="#reports">Reports</a>
                    <a class="nav-link" data-toggle="tab" href="#reports"></a>
                </nav>
            </div>

            <!-- Add Tab Content Container -->
            <div class="tab-content">
                <!-- Overview Tab (existing content) -->
                <div class="tab-pane fade show active" id="overview">
                    <div class="row row-sm mg-b-20">
                        <div class="col-lg-7 ht-lg-100p">
                            <div class="card card-dashboard-one">
                                <div class="card-header">
                                    <div>
                                        <h6 class="card-title">Land Property Analytics</h6>
                                        <p class="card-text">Overview of land property listings and user engagement</p>
                                    </div>
                                    <div class="btn-group">
                                        <button class="btn active">Day</button>
                                        <button class="btn">Week</button>
                                        <button class="btn">Month</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="card-body-top">
                                        <div>
                                            <label class="mg-b-0">Total Users</label>
                                            <h2><?php echo $totalUsers; ?></h2>
                                        </div>
                                        <div>
                                            <label class="mg-b-0">Total Agents</label>
                                            <h2><?php echo $totalAgents; ?></h2>
                                        </div>
                                        <div>
                                            <label class="mg-b-0">Property Views</label>
                                            <h2><?php echo $totalViews; ?></h2>
                                        </div>
                                        <div>
                                            <label class="mg-b-0">Listed Properties</label>
                                            <h2><?php echo $totalProperties; ?></h2>
                                        </div>
                                    </div>
                                    <div class="flot-chart-wrapper">
                                        <div id="flotChart" class="flot-chart"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5 mg-t-20 mg-lg-t-0">
                            <div class="row row-sm">
                                <div class="col-sm-6">
                                    <div class="card card-dashboard-two">
                                        <div class="card-header">
                                            <h6><?php echo $propertyGrowth; ?>% <i class="icon ion-md-trending-up tx-success"></i></h6>
                                            <p>Property Growth</p>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-wrapper">
                                                <div id="flotChart1" class="flot-chart"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 mg-t-20 mg-sm-t-0">
                                    <div class="card card-dashboard-two">
                                        <div class="card-header">
                                            <h6><?php echo $totalAgents; ?> <i class="icon ion-md-trending-up tx-success"></i></h6>
                                            <p>Active Agents</p>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-wrapper">
                                                <div id="flotChart2" class="flot-chart"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mg-t-20">
                                    <div class="card card-dashboard-three">
                                        <div class="card-header">
                                            <p>Land Type Distribution</p>
                                            <h6><?php echo $totalProperties; ?> Properties</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart"><canvas id="landTypeChart"></canvas></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row row-sm mg-b-20">
                        <div class="col-lg-4">
                            <div class="card card-dashboard-pageviews">
                                <div class="card-header">
                                    <h6 class="card-title">Most Viewed Properties</h6>
                                    <p class="card-text">Top performing property listings</p>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($topProperties)): ?>
                                        <?php foreach($topProperties as $property): ?>
                                        <div class="az-list-item">
                                            <div>
                                                <h6><?php echo htmlspecialchars($property['property_name']); ?></h6>
                                                <span><?php echo htmlspecialchars($property['property_location']); ?></span>
                                            </div>
                                            <div>
                                                <h6 class="tx-primary"><?php echo $property['views']; ?></h6>
                                                <span><?php echo $property['growth']; ?>%</span>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p>No properties available.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 mg-t-20 mg-lg-t-0">
                            <div class="card card-dashboard-four">
                                <div class="card-header">
                                    <h6 class="card-title">User Activity by Type</h6>
                                </div>
                                <div class="card-body row">
                                    <div class="col-md-6 d-flex align-items-center">
                                        <div class="chart"><canvas id="userActivityChart"></canvas></div>
                                    </div>
                                    <div class="col-md-6 col-lg-5 mg-lg-l-auto mg-t-20 mg-md-t-0">
                                        <div class="az-traffic-detail-item">
                                            <div>
                                                <span>Agents</span>
                                                <span><?php echo $totalAgents; ?> <span>(<?php echo $agentPercentage; ?>%)</span></span>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar bg-purple" style="width: <?php echo $agentPercentage; ?>%" role="progressbar"></div>
                                            </div>
                                        </div>
                                        <div class="az-traffic-detail-item">
                                            <div>
                                                <span>Buyers</span>
                                                <span><?php echo $totalBuyers; ?> <span>(<?php echo $buyerPercentage; ?>%)</span></span>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar bg-primary" style="width: <?php echo $buyerPercentage; ?>%" role="progressbar"></div>
                                            </div>
                                        </div>
                                        <div class="az-traffic-detail-item">
                                            <div>
                                                <span>Viewers</span>
                                                <span><?php echo $totalViewers; ?> <span>(<?php echo $viewerPercentage; ?>%)</span></span>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar bg-info" style="width: <?php echo $viewerPercentage; ?>%" role="progressbar"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Land Types Tab -->
                <div class="tab-pane fade" id="landTypes">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Land Types Management</h6>
                        </div>
                        <div class="card-body">
                            <!-- Add your land types content here -->
                            <p>Land types management content will go here.</p>
                        </div>
                    </div>
                </div>

                <!-- Users Tab -->
                <div class="tab-pane fade" id="users">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">User Management</h6>
                        </div>
                        <div class="card-body">
                            <!-- Add your users content here -->
                            <p>User management content will go here.</p>
                        </div>
                    </div>
                </div>

                <!-- Reports Tab -->
                <div class="tab-pane fade" id="reports">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Reports</h6>
                        </div>
                        <div class="card-body">
                            <!-- Add your reports content here -->
                            <p>Reports content will go here.</p>
                        </div>
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

<script src="../../assets/js/addedFunctions.js"></script>


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

<div class="modal-footer">
    </div>

    <div class="az-footer">
        <div class="container">
            <span class="text-muted d-block text-center">Copyright ©LoremIpsum 2024</span>
        </div>
    </div>
    <!-- End of Footer -->


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
        $(document).ready(function() {
            // Show the sign-out confirmation modal when the Sign Out button is clicked
            $('#signOutButton').on('click', function() {
                $('#signOutModal').modal('show');
            });

            // Confirm sign out (destroy session and redirect to login page)
            $('#confirmSignOutButton').on('click', function() {
                // Make a request to sign_out.php to destroy the session
                $.ajax({
                    url: '../../backend/sign_out.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            window.location.href = '../../index.php';
                        } else {
                            alert('Error: Could not sign out.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('Error: Could not sign out.');
                    }
                });
            });
        });
    </script>
    <!-- full -->




        <!-- Blur Effect -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const warningModal = document.getElementById("warningModal");
    const mainContent = document.getElementById("mainContent");

    // Add blur effect when modal is shown
    warningModal.addEventListener("show.bs.modal", function () {
        mainContent.classList.add("blur-background");
    });

    // Remove blur effect when modal is hidden
    warningModal.addEventListener("hidden.bs.modal", function () {
        mainContent.classList.remove("blur-background");
    });
});
</script>

<script>
$(document).ready(function() {
    window.updateStatus = function(reportId, status) {
        if (confirm('Are you sure you want to mark this report as ' + status + '?')) {
            $.ajax({
                url: '../../backend/update_report_status.php',
                method: 'POST',
                data: {
                    report_id: reportId,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error updating report status');
                    }
                },
                error: function() {
                    alert('Error updating report status');
                }
            });
        }
    };

    window.exportToCSV = function() {
        window.location.href = '../../backend/export_reports.php';
    };
});
</script>

</body>

</html>