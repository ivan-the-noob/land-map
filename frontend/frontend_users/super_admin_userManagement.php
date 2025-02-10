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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Land Map | Super Admin Dashboard</title>
    <link rel="icon" href="../../assets/images/logo.png" type="image/x-icon">

    <link href="../../assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../../assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
    <link href="../../assets/lib/typicons.font/typicons.css" rel="stylesheet">
    <link href="../../assets/lib/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
    <link href="../../assets/css/azia.css" rel="stylesheet">

    <style>
        /* Center the table data */
        table th,
        table td {
            text-align: center;
        }
    </style>

</head>

<body>
    <div class="az-header">
        <?php require '../../partials/super_admin_nav.php' ?> <!-- Include navigation for agent -->
    </div>

    <div class="az-content az-content-dashboard">
        <div class="container">
            <div class="az-content-body">
                <div class="az-dashboard-one-title">
                    <div>
                        <h2 class="az-dashboard-title">User Management</h2> <!-- Dashboard title -->
                        <p class="az-dashboard-text">text</p> <!-- Dashboard text -->
                    </div>
                    <div class="az-content-header-right">
                        <!-- Time and Date -->
                        <div class="az-content-header-right">
                            <div class="media">
                                <div class="media-body">
                                    <label>Current Date</label> <!-- Current date label -->
                                    <h6 id="current-date"></h6> <!-- Current date display -->
                                </div><!-- media-body -->
                            </div><!-- media -->
                            <div class="media">
                                <div class="media-body">
                                    <label>Current Time</label> <!-- Current time label -->
                                    <h6 id="current-time"></h6> <!-- Current time display -->
                                </div><!-- media-body -->
                            </div><!-- media -->
                            <div class="media">
                                <div class="media-body">
                                    <label>Time Zone</label> <!-- Time zone label -->
                                    <h6>Philippine Time (PHT)</h6> <!-- Time zone display -->
                                </div><!-- media-body -->
                            </div><!-- media -->
                        </div>
                    </div>
                </div>

                <div class="az-dashboard-nav">
                    <nav class="nav">
                        <!-- Tab for user list -->
                        <a class="nav-link active" data-toggle="tab" href="#admin_list">Admin List</a>
                        <!-- Agent List Tab -->
                        <a class="nav-link" data-toggle="tab" href="#agent_list">Agent List</a>
                        <!-- User List Tab -->
                        <a class="nav-link" data-toggle="tab" href="#user_list">User List</a>
                        <a class="nav-link" data-toggle="tab" href="#add_user">Add New User</a>
                    </nav>

                    <nav class="nav">
                    </nav>
                </div>

                <div class="tab-content mt-4">
                    <!-- USER LIST TAB -->
                    <div id="admin_list" class="tab-pane active">
                        <div class="card shadow-sm rounded">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col d-flex justify-content-between align-items-center">
                                        <h4 class="mb-0">Admin List</h4>
                                    </div>
                                </div>
                                <table class="table table-bordered table-striped mt-4">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Verified</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="userTableBody">
                                        <!-- Dynamic rows will be inserted here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                    <!-- Agent List Tab -->
                    <div id="agent_list" class="tab-pane">
                        <div class="card shadow-sm rounded">
                            <div class="card-body">
                                <h4 class="mb-4">Agent List</h4>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Verified</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="agentTableBody">
                                        <!-- Agent rows will be dynamically inserted here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- User List Tab -->
                    <div id="user_list" class="tab-pane">
                        <div class="card shadow-sm rounded">
                            <div class="card-body">
                                <h4 class="mb-4">User List</h4>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Verified</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="userTableBody">
                                        <!-- User rows will be dynamically inserted here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ADD NEW USER TAB -->
                    <div id="add_user" class="tab-pane">
                        <div class="d-flex align-items-center mb-4">
                            <h3 class="mb-1 mr-5">Add New User</h3>
                        </div>

                        <!-- Add New User Form -->
                        <div class="card shadow-sm rounded">
                            <div class="card-body">
                                <form id="addUserForm">
                                    <div class="mb-3">
                                        <label for="fname" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="fname" name="fname" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="lname" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lname" name="lname" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Role</label>
                                        <select class="form-control" id="role" name="role" required>
                                            <option value="full_access">Admin: Full access</option>
                                            <option value="normal_access">Admin: Normal access</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-success">Add User</button>
                                </form>
                            </div>
                        </div>

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

    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- User Creation Form -->
                    <form id="addUserForm">
                        <div class="mb-3">
                            <label for="fname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="fname" name="fname" required>
                        </div>
                        <div class="mb-3">
                            <label for="lname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lname" name="lname" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="full_access">Admin: Full access</option>
                                <option value="normal_access">Admin: Normal access</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Add User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/lib/jquery/jquery.min.js"></script>
    <script src="../../assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/lib/ionicons/ionicons.js"></script>
    <script src="../../assets/js/azia.js"></script>
    <!-- Add SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('signOutButton').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default action (if any) on the button click

            // Show SweetAlert confirmation
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
                    // Make an AJAX request to sign_out.php
                    $.ajax({
                        url: '../../backend/sign_out.php', // Path to your sign_out.php
                        type: 'POST',
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                // Redirect to the login page or home page after successful logout
                                window.location.href = '../../frontend/sign_in.php'; // Adjust the URL to your login page
                            } else {
                                // Handle any potential errors here (if needed)
                                Swal.fire({
                                    title: 'Error',
                                    text: 'There was an issue logging you out. Please try again.',
                                    icon: 'error'
                                });
                            }
                        },
                        error: function () {
                            // Handle error in the AJAX request
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
    </script>

    <script>
        function updateDateTime() {
            const now = new Date(); // Get current date and time
            const dateOptions = {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            }; // Date options
            const timeOptions = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true,
                timeZone: 'Asia/Manila' // Time zone
            };

            document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', dateOptions); // Update date
            document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', timeOptions); // Update time
        }

        updateDateTime(); // Initial call to update time
        setInterval(updateDateTime, 1000); // Update time every second
    </script>

    <script>
        $(document).ready(function () {
            $('#addUserForm').submit(function (e) {
                e.preventDefault(); // Prevent default form submission

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to add a new admin user!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, add user',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading spinner while processing
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait while we add the new admin user.',
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading(); // Show loading spinner
                            }
                        });

                        // Collect form data
                        let formData = $(this).serialize();

                        $.ajax({
                            url: '../../backend/add_admin.php', // Adjust path to your backend script
                            type: 'POST',
                            data: formData,
                            dataType: 'json',
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload(); // Reload page to reflect changes
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: response.message,
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function () {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Something went wrong! Please try again.',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>

<script>
    function loadAdminUsers() {
        fetch('../../backend/fetch_admin_user.php', { // Adjust the path to your PHP file
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Get the table body element
                    const userTableBody = document.getElementById('userTableBody');

                    // Clear any existing rows
                    userTableBody.innerHTML = '';

                    // Check if there are users
                    if (data.users.length === 0) {
                        // No users, display a message
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td colspan="6" class="text-center">No users available</td>
                        `;
                        userTableBody.appendChild(row);
                    } else {
                        // Populate the table with admin users
                        data.users.forEach(user => {
                            let verifiedStatus = '';
                            let verifiedClass = '';

                            // Set the status and class based on the verification status
                            if (user.is_verified == 1) {
                                verifiedStatus = 'Verified';
                                verifiedClass = 'text-success'; // Green
                            } else if (user.is_verified == 2) {
                                verifiedStatus = 'Semi Verified';
                                verifiedClass = 'text-warning'; // Yellow
                            } else {
                                verifiedStatus = 'Not Verified';
                                verifiedClass = 'text-danger'; // Red
                            }

                            // Check if the username is null and set it to "Not Set"
                            const username = user.username ? user.username : 'Not Set';

                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${username}</td>
                                <td>${user.email}</td>
                                <td>${user.role_type}</td>
                                <td>${user.control_lvl}</td>
                                <td class="${verifiedClass}">${verifiedStatus}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm" onclick="editUser(${user.id})">Edit</button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteUser(${user.id})">Delete</button>
                                </td>
                            `;
                            userTableBody.appendChild(row);
                        });
                    }
                } else {
                    console.error('Error fetching data:', data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching admin users:', error);
            });
    }

    // Call the function to load users when the page loads
    window.onload = loadAdminUsers;

    // Set an interval to reload the users every 3 seconds (3000 milliseconds)
    setInterval(loadAdminUsers, 3000);  // Update every 3 seconds
</script>


    <script>
    document.getElementById('addUserForm').addEventListener('submit', function(event) {
        event.preventDefault();

        // Get form data
        const formData = new FormData(this);

        // Example AJAX request (you can modify this to send the form data to your server)
        fetch('/path/to/your/submit/endpoint', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Handle the server response
            console.log('User added:', data);
            // Close the modal after successful submission
            const modal = bootstrap.Modal.getInstance(document.getElementById('createUserModal'));
            modal.hide();
            // You can update the user list or show a success message here
        })
        .catch(error => {
            // Handle error
            console.error('Error:', error);
        });
    });
</script>

</body>

</html>