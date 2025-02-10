<?php
session_start();
require '../../db.php';  // Make sure your DB connection is included

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../handlers/no_login.php");
    exit();
}

// Check if the user has 'admin' role
if ($_SESSION['role_type'] !== 'admin') {
    // If the user doesn't have 'admin' role, redirect them to the home page or error page
    header("Location: ../../handlers/no_login.php");
    exit();
}

// Get the user details from session
$user_id = $_SESSION['user_id'];  // Correct session variable for user id
$query = "SELECT * FROM admin WHERE id = '$user_id' LIMIT 1";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Set the is_verified in session
    $_SESSION['is_verified'] = $user['is_verified'];  // Save is_verified in session

    // Check if the user's verification status is 2 (needs profile update)
    if ($user['is_verified'] == 2) {
        // Set session flag to show profile picture update notification
        $_SESSION['update_profile_picture'] = true;
    }

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
    <title>Profile | Admin</title>
    <link rel="icon" href="../../assets/images/logo.png" type="image/x-icon">

    <link href="../../assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../../assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
    <link href="../../assets/lib/typicons.font/typicons.css" rel="stylesheet">
    <link href="../../assets/lib/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../../assets/css/azia.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <div class="az-header">
        <?php require '../../partials/nav_admin.php'; ?>
    </div>

    <!-- Profile 1 - Bootstrap Brain Component -->
    <section class="bg-light py-3 py-md-5 py-xl-8">

        <div class="container">
            <div class="row gy-4 gy-lg-0">
                <div class="col-12 col-lg-4 col-xl-3">
                    <div class="row gy-4">
                        <div class="col-12">
                            <div class="card widget-card border-light shadow-sm">
                                <div class="card-header text-bg-primary text-center">
                                    <strong>Welcome, <?php echo $_SESSION['full_name'] ?></strong>
                                </div>
                                <div class="card-body text-center">
                                    <!-- Profile Image -->
                                    <div class="mb-3">
                                        <img src="<?php echo $profile_image; ?>" class="img-fluid rounded-circle w-50"
                                            alt="User Image">
                                    </div>
                                    <!-- Name and Role -->
                                    <h5 class="mb-1"><?php echo $_SESSION['user_name'] ?></h5>
                                    <p class="text-secondary mb-4"><?php echo $_SESSION['role_type'] ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card widget-card border-light shadow-sm">
                                <div class="card-header text-bg-primary">Social Accounts</div>
                                <div class="card-body">
                                    <a href="#!" class="d-inline-block bg-dark link-light lh-1 p-2 rounded">
                                        <i class="bi bi-youtube"></i>
                                    </a>
                                    <a href="#!" class="d-inline-block bg-dark link-light lh-1 p-2 rounded">
                                        <i class="bi bi-twitter-x"></i>
                                    </a>
                                    <a href="#!" class="d-inline-block bg-dark link-light lh-1 p-2 rounded">
                                        <i class="bi bi-facebook"></i>
                                    </a>
                                    <a href="#!" class="d-inline-block bg-dark link-light lh-1 p-2 rounded">
                                        <i class="bi bi-linkedin"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card widget-card border-light shadow-sm">
                                <div class="card-header text-bg-primary">About Me</div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush mb-0">
                                        <li class="list-group-item">
                                            <h6 class="mb-1">
                                                <span class="bii bi-mortarboard-fill me-2"></span>
                                                Education
                                            </h6>
                                            <span>M.S Computer Science</span>
                                        </li>
                                        <li class="list-group-item">
                                            <h6 class="mb-1">
                                                <span class="bii bi-geo-alt-fill me-2"></span>
                                                Location
                                            </h6>
                                            <span>Mountain View, California</span>
                                        </li>
                                        <li class="list-group-item">
                                            <h6 class="mb-1">
                                                <span class="bii bi-building-fill-gear me-2"></span>
                                                Company
                                            </h6>
                                            <span>GitHub Inc</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card widget-card border-light shadow-sm">
                                <div class="card-header text-bg-primary">Skills</div>
                                <div class="card-body">
                                    <span class="badge text-bg-primary">HTML</span>
                                    <span class="badge text-bg-primary">SCSS</span>
                                    <span class="badge text-bg-primary">Javascript</span>
                                    <span class="badge text-bg-primary">React</span>
                                    <span class="badge text-bg-primary">Vue</span>
                                    <span class="badge text-bg-primary">Angular</span>
                                    <span class="badge text-bg-primary">UI</span>
                                    <span class="badge text-bg-primary">UX</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-8 col-xl-9">
                    <div class="card widget-card border-light shadow-sm">
                        <div class="card-body p-4">
                            <ul class="nav nav-tabs" id="profileTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab"
                                        data-bs-target="#overview-tab-pane" type="button" role="tab"
                                        aria-controls="overview-tab-pane" aria-selected="true">Overview</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#profile-tab-pane" type="button" role="tab"
                                        aria-controls="profile-tab-pane" aria-selected="false">Profile</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="password-tab" data-bs-toggle="tab"
                                        data-bs-target="#password-tab-pane" type="button" role="tab"
                                        aria-controls="password-tab-pane" aria-selected="false">Change Password</button>
                                </li>
                            </ul>
                            <div class="tab-content pt-4" id="profileTabContent">
                                <div class="tab-pane fade show active" id="overview-tab-pane" role="tabpanel"
                                    aria-labelledby="overview-tab" tabindex="0">

                                    <!-- About Section -->
                                    <div class="mb-4">
                                        <h5 class="mb-3">About</h5>
                                        <p class="lead mb-3">
                                            Ethan Leo is a seasoned and results-driven Project Manager who brings
                                            experience and expertise to project management.
                                            With a proven track record of successfully delivering complex projects on
                                            time and within budget, Ethan Leo is the go-to professional
                                            for organizations seeking efficient and effective project leadership.
                                        </p>
                                    </div>

                                    <!-- Profile Section -->
                                    <div class="card shadow-sm rounded-3">
                                        <div class="card-header bg-secondary text-white">
                                            <h5 class="mb-0">Profile</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-0">
                                                <div class="col-12 col-md-4">
                                                    <div class="p-3 border-end">
                                                        <strong>First Name</strong>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-8">
                                                    <div class="p-3">
                                                        Ethan
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="p-3 border-end">
                                                        <strong>Last Name</strong>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-8">
                                                    <div class="p-3">
                                                        Leo
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="p-3 border-end">
                                                        <strong>Education</strong>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-8">
                                                    <div class="p-3">
                                                        M.S Computer Science
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="p-3 border-end">
                                                        <strong>Address</strong>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-8">
                                                    <div class="p-3">
                                                        Mountain View, California
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="p-3 border-end">
                                                        <strong>Country</strong>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-8">
                                                    <div class="p-3">
                                                        United States
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="p-3 border-end">
                                                        <strong>Job</strong>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-8">
                                                    <div class="p-3">
                                                        Project Manager
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="p-3 border-end">
                                                        <strong>Company</strong>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-8">
                                                    <div class="p-3">
                                                        GitHub Inc
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="p-3 border-end">
                                                        <strong>Phone</strong>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-8">
                                                    <div class="p-3">
                                                        +1 (248) 679-8745
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="p-3 border-end">
                                                        <strong>Email</strong>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-8">
                                                    <div class="p-3">
                                                        leo@example.com
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel"
                                    aria-labelledby="profile-tab" tabindex="0">
                                    <div class="card shadow-sm rounded-3">
                                        <div class="card-header bg-light border-0">
                                            <h5 class="mb-0">Profile Settings</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="#!" class="row gy-3 gy-xxl-4">
                                                <!-- Profile Image Section -->
                                                <div class="col-12 mb-4">
                                                    <label class="form-label">Profile Image</label>
                                                    <div class="d-flex flex-column align-items-center mb-4">
                                                        <img src="<?php echo $profile_image; ?>"
                                                            class="img-fluid rounded-circle mb-3"
                                                            style="max-width: 100px; height: 100px;" alt="User Image">
                                                        <div class="d-flex gap-3">
                                                            <a href="#!" class="btn btn-outline-primary btn-sm"
                                                                title="Upload Image">
                                                                <i class="bi bi-upload"></i> Upload
                                                            </a>
                                                            <a href="#!" class="btn btn-outline-danger btn-sm"
                                                                title="Delete Image">
                                                                <i class="bi bi-trash"></i> Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Personal Info Section -->
                                                <div class="col-12 mb-4">
                                                    <h6 class="text-muted mb-3">Personal Information</h6>
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <label for="inputuser_namr" class="form-label">User
                                                                Name</label>
                                                            <input type="text" class="form-control" id="inputUserName"
                                                                placeholder="<?php echo !empty($user['user_name']) ? $user['user_name'] : 'Not set'; ?>">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Company and Job Section -->
                                                <div class="col-12 mb-4">
                                                    <h6 class="text-muted mb-3">Job Information</h6>
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <label for="inputCompany" class="form-label">Company</label>
                                                            <input type="text" class="form-control" id="inputCompany"
                                                                placeholder="<?php echo !empty($user['company']) ? $user['company'] : 'Not set'; ?>">
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label for="inputJob" class="form-label">Job</label>
                                                            <input type="text" class="form-control" id="inputJob"
                                                                placeholder="<?php echo !empty($user['job']) ? $user['job'] : 'Not set'; ?>">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Contact Info Section -->
                                                <div class="col-12 mb-4">
                                                    <h6 class="text-muted mb-3">Contact Information</h6>
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <label for="inputPhone" class="form-label">Phone
                                                                Number</label>
                                                            <input type="tel" class="form-control" id="inputPhone"
                                                                placeholder="<?php echo !empty($user['phone_number']) ? $user['phone_number'] : 'Not set'; ?>">
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label for="inputEmail" class="form-label">Email
                                                                Address</label>
                                                            <input type="email" class="form-control" id="inputEmail"
                                                                placeholder="<?php echo !empty($user['email']) ? $user['email'] : 'Not set'; ?>">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Address Section -->
                                                <div class="col-12 mb-4">
                                                    <h6 class="text-muted mb-3">Address</h6>
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <label for="inputAddress" class="form-label">Address</label>
                                                            <input type="text" class="form-control" id="inputAddress"
                                                                placeholeder="<?php echo !empty($user['address']) ? $user['address'] : 'Not set'; ?>"
                                                                placeholder="e.g Manila, Philippines">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Save Button -->
                                                <div class="col-12 text-center mt-4">
                                                    <button type="submit" class="btn btn-primary btn-lg px-4 py-2">Save
                                                        Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="password-tab-pane" role="tabpanel"
                                    aria-labelledby="password-tab" tabindex="0">
                                    <form action="#!">
                                        <div class="row gy-3 gy-xxl-4">
                                            <div class="col-12">
                                                <label for="currentPassword" class="form-label">Current Password</label>
                                                <input type="password" class="form-control" id="currentPassword">
                                            </div>
                                            <div class="col-12">
                                                <label for="newPassword" class="form-label">New Password</label>
                                                <input type="password" class="form-control" id="newPassword">
                                            </div>
                                            <div class="col-12">
                                                <label for="confirmPassword" class="form-label">Confirm Password</label>
                                                <input type="password" class="form-control" id="confirmPassword">
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">Change Password</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <div class="az-footer">
        <div class="container">
            <span class="text-muted d-block text-center">Copyright ©LoremIpsum 2024</span>
        </div>
    </div>

    <!-- Modernized Edit Profile Modal -->
    <!-- Your modal code goes here -->

    <script src="../../assets/lib/jquery/jquery.min.js"></script>
    <script src="../../assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/lib/ionicons/ionicons.js"></script>
    <script src="../../assets/js/azia.js"></script>
    <script src="../../assets/js/addedFunctions.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    

</body>

</html>