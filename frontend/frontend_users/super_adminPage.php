<?php
session_start();
require '../../db.php';  // Make sure your DB connection is included

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../handlers/no_login.php");
    exit();
}

// Check if the user has 'super_admin' role
if ($_SESSION['role_type'] !== 'super_admin') {
    // If the user doesn't have 'super_admin' role, redirect them to the home page or error page
    header("Location: ../../handlers/no_login.php");
    exit();
}

// Get the user details from session
$user_id = $_SESSION['user_id'];  // Correct session variable for user id
$query = "SELECT * FROM admin WHERE id = '$user_id' LIMIT 1";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Set default image if img_name is empty
    if (empty($user['img_name'])) {
        $profile_image = '../../assets/profile_images/profile_default.png';  // Default image
    } else {
        $profile_image = '../../assets/profile_images/admin_profile_img/' . $user['img_name'];  // User's profile image
    }
} else {
    $profile_image = '../../assets/profile_images/profile_default.png';  // Fallback default image if user not found
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-90680653-2"></script>
    <script>
        window.dataLayer = window.dataLayer || []; // Initialize dataLayer

        function gtag() {
            dataLayer.push(arguments); // Push arguments to dataLayer
        }
        gtag('js', new Date()); // Log the current date
        gtag('config', 'UA-90680653-2'); // Configure Google Analytics
    </script>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Land Map | My Listings</title>
    <link rel="icon" href="../../assets/images/logo.png" type="image/x-icon">

    <!-- vendor css -->
    <link href="../../assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../../assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
    <link href="../../assets/lib/typicons.font/typicons.css" rel="stylesheet">
    <link href="../../assets/lib/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">

    <!-- Maptiler Links -->
    <script src="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.umd.js"></script>
    <link href="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.css" rel="stylesheet" />

    <!-- azia CSS -->
    <link rel="stylesheet" href="../../assets/css/azia.css">
</head>

<body>
    <div class="az-header">
        <?php require '../../partials/super_admin_nav.php' ?> <!-- Include navigation for superadmin -->
    </div>

    <div class="az-content az-content-dashboard">
        <div class="container">
            <div class="az-content-body">
                <div class="az-dashboard-one-title">
                    <div>
                        <h2 class="az-dashboard-title">Dashboard</h2>
                        <p class="az-dashboard-text">Super admin</p>
                    </div>
                    <div class="az-content-header-right">
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
                </div>

                <div class="az-dashboard-nav">
                    <nav class="nav">
                        <a class="nav-link active" data-toggle="tab" href="#dashboard">Listed Properties</a>
                        <a class="nav-link" data-toggle="tab" href="#create_property">Post New Land Property</a>
                    </nav>
                </div>

                <div class="tab-content mt-4">
                    <div id="dashboard" class="tab-pane active">
                        <!-- You can add dashboard content for managing properties here -->
                    </div>

                    <div id="create_property" class="tab-pane">
                        <div class="d-flex align-items-center mb-4">
                            <h3 class="mb-1 mr-5">Post New Land</h3>
                        </div>
                        <!-- Add property posting form here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="az-footer">
        <div class="container">
            <span class="text-muted d-block text-center">Copyright ©LoremIpsum 2024</span>
        </div>
    </div>

    <script src="../../assets/lib/jquery/jquery.min.js"></script>
    <script src="../../assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/lib/ionicons/ionicons.js"></script>
    <script src="../../assets/js/azia.js"></script>
    <script src="../../assets/js/addedFunctions.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- Date and Time Update Scripts -->
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

    <script>
        $(document).ready(function () {
            // Sign Out button click event
            $('#signOutButton').on('click', function (e) {
                e.preventDefault(); // Prevent the default form submit action

                // Show the SweetAlert confirmation
                Swal.fire({
                    title: 'Are you sure you want to sign out?',
                    text: "You will need to log in again to access your account.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, sign me out',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If confirmed, make an AJAX request to log out
                        $.ajax({
                            url: '../../backend/sign_out.php', // Path to your sign_out.php
                            type: 'POST',
                            dataType: 'json',
                            success: function (response) {
                                if (response.success) {
                                    // Redirect to the login page after successful logout
                                    window.location.href = '../../frontend/sign_in.php';
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'There was an issue logging you out. Please try again.',
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function () {
                                // Handle error in AJAX request
                                Swal.fire({
                                    title: 'Error',
                                    text: 'There was an issue logging you out. Please try again.',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>


</body>

</html>