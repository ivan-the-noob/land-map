<?php
session_start();
require '../../db.php';  // Make sure your DB connection is included

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../handlers/no_login.php");
    exit();
}

// Check if the user has 'super_admin' role
if ($_SESSION['role_type'] !== 'admin') {
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

// Fetch all admins for the Admin List tab
$adminQuery = "SELECT * FROM admin";
$adminResult = $conn->query($adminQuery);

// Close the connection after fetching data
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Land Map | Agents</title>
    <link rel="icon" href="../../assets/images/logo.png" type="image/x-icon">

    <!-- Bootstrap 5 CDN -->
    <link href="../../assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../../assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
    <link href="../../assets/lib/typicons.font/typicons.css" rel="stylesheet">
    <link href="../../assets/lib/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/azia.css">

    <style>
        /* Table styles */
        .table {
            text-align: center;
        }
    </style>
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
                        <h2 class="az-dashboard-title">Dashboard</h2>
                        <p class="az-dashboard-text">Super admin</p>
                    </div>
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

                <div class="az-dashboard-nav">
                    <nav class="nav">
                        <a class="nav-link active" data-bs-toggle="tab" href="#admin_list">Admin List</a>
                        <a class="nav-link" data-bs-toggle="tab" href="#agent_list">Agent List</a>
                        <a class="nav-link" data-bs-toggle="tab" href="#user_list">User List</a>
                        <a class="nav-link" data-bs-toggle="tab" href="#more">More</a>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Admin List Tab -->
                    <div class="tab-pane fade show active" id="admin_list">
                        <div class="d-flex justify-content-between w-100 mb-3">
                            <h3 class="mb-3">Admin List</h3>
                            <button class="btn btn-primary" id="addAdminButton" data-bs-toggle="modal"
                                data-bs-target="#addAdminModal">Create admin</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Username</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Permission</th>
                                        <th>Verification Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="admin-list-body">
                                    <!-- Admin rows will be dynamically added here by AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Agent List Tab -->
                    <div class="tab-pane fade" id="agent_list">
                        <!-- Agent content here -->
                    </div>

                    <!-- User List Tab -->
                    <div class="tab-pane fade" id="user_list">
                        <!-- User content here -->
                    </div>

                    <!-- More Tab -->
                    <div class="tab-pane fade" id="more">
                        <h3 class="mb-3">More</h3>
                        <p>Additional content can go here...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="az-footer ht-40">
        <div class="container ht-100p pd-t-0-f">
            <span class="text-muted d-block text-center">Copyright © LoremIpsum 2024</span>
        </div>
    </div>

    <!-- Add Admin Modal -->
    <div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAdminModalLabel">Add New Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addAdminForm" method="POST">
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" required>
                        </div>
                        <div class="form-group">
                            <label for="adminEmail">Email Address</label>
                            <input type="email" class="form-control" id="adminEmail" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="adminRole">Role</label>
                            <select class="form-control" id="adminRole" name="role_type" required>
                                <option value="admin">Admin</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary mt-3" id="confirmAddAdmin">Add Admin</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap 5 CDN Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/lib/jquery/jquery.min.js"></script>
    <script src="../../assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/lib/ionicons/ionicons.js"></script>
    <script src="../../assets/js/azia.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Update DateTime
        function updateDateTime() {
            const currentDate = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
            document.getElementById('current-date').textContent = currentDate.toLocaleDateString(undefined, options);
            document.getElementById('current-time').textContent = currentDate.toLocaleTimeString(undefined, timeOptions);
        }

        setInterval(updateDateTime, 1000);  // Update the time every second
    </script>

    <script>
        document.getElementById('confirmAddAdmin').addEventListener('click', function () {
            // Get form data
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const email = document.getElementById('adminEmail').value;
            const role = document.getElementById('adminRole').value;

            // Check if any required field is empty
            if (!firstName || !lastName || !email || !role) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please fill in all the fields.',
                });
                return;
            }

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to add ${firstName} ${lastName} as an admin with the email ${email}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, add it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show SweetAlert loading spinner while processing
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

                    // Send data to backend via AJAX
                    const formData = new FormData();
                    formData.append('fname', firstName);
                    formData.append('lname', lastName);
                    formData.append('email', email);
                    formData.append('role', role);

                    // Create an AJAX request
                    fetch('../../backend/add_admin.php', {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'  // Ensures session cookies are sent
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);  // Log the response to check what we're receiving

                            if (data.success) {
                                // Close the loading spinner and show success message
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: data.message,
                                }).then(() => {
                                    // Redirect to admin_userManagement.php after success
                                    window.location.href = 'admin_userManagement.php'; // Modify path if needed
                                });
                            } else {
                                // Close the loading spinner and show error message
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: data.message,
                                });
                            }
                        })
                        .catch(error => {
                            console.error("AJAX error:", error);  // Log any AJAX request errors

                            // Close the loading spinner and show error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Something went wrong, please try again later.',
                            });
                        });
                }
            });
        });


        function fetchAdminData() {
            fetch('../../backend/fetch_admin_user.php')
                .then(response => response.json())
                .then(data => {
                    const adminTableBody = document.querySelector('#admin-list-body');
                    adminTableBody.innerHTML = ''; // Clear the current table data

                    if (data.message) {
                        // If there's a message (no admins found), show it in the table
                        adminTableBody.innerHTML = `<tr><td colspan="6">${data.message}</td></tr>`;
                    } else if (data.admins && data.admins.length > 0) {
                        // If admins are found, display the rows
                        data.admins.forEach(admin => {
                            const row = document.createElement('tr');

                            row.innerHTML = `
                        <td>${admin.username}</td>
                        <td>${admin.full_name}</td>
                        <td>${admin.email}</td>
                        <td>${admin.control_level}</td>
                        <td><span class='badge ${admin.verification_class}'>${admin.verification_status}</span></td>
                        <td>
                            <button class='btn btn-outline-primary btn-sm'>Edit</button>
                            <button class='btn btn-outline-danger btn-sm'>Delete</button>
                        </td>
                    `;

                            adminTableBody.appendChild(row);
                        });
                    } else {
                        // If admins is empty, display no data found message
                        adminTableBody.innerHTML = '<tr><td colspan="6">No admins found.</td></tr>';
                    }
                })
                .catch(error => console.error('Error fetching admin data:', error));
        }

        // Fetch admin data when the page loads
        document.addEventListener('DOMContentLoaded', function () {
            fetchAdminData();

            // Set an interval to refresh the table every 10 seconds
            setInterval(fetchAdminData, 10000); // Update every 10 seconds
        });
    </script>

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

</body>

</html>