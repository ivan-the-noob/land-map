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

    <title>Land Map | Admin Control</title>
    <link rel="icon" href="../../assets/images/logo.png" type="image/x-icon">

    <!-- vendor css -->
    <link href="../../assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../../assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
    <link href="../../assets/lib/typicons.font/typicons.css" rel="stylesheet">
    <link href="../../assets/lib/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">

    <!-- Mapping Links -->
    <script src="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.umd.js"></script>
    <link href="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.css" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />

    <!-- azia CSS -->
    <link rel="stylesheet" href="../../assets/css/azia.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">

</head>

<body>

    <div class="az-header">
        <?php require '../../partials/nav_admin.php'; ?>
    </div><!-- az-header -->

    <div class="az-content pd-y-20 pd-lg-y-30 pd-xl-y-40">
        <div class="container">
            <div class="az-content-left az-content-left-components">
                <div class="component-item">
                    <label>Users management</label>
                    <nav class="nav flex-column">
                        <a href="#" class="nav-link active" id="userListLink" onclick="showUserTable(event)">User List</a>
                        <a href="#" class="nav-link" id="agentListLink" onclick="showAgentTable(event)">Agent List</a>
                        <a href="#" class="nav-link" id="agentListLink" onclick="showAgentVerification(event)">Agent Verification</a>
                        <a href="#" class="nav-link" id="agentRegistrationLink" onclick="showRegistration(event)">Agent Registration</a>
                        <a href="#" class="nav-link" id="adminRegistrationLink" onclick="showAdminRegistration(event)">Admin Registration</a>
                    </nav>
                    <label>Website Edit</label>
                    <nav class="nav flex-column">
                        <a href="#" class="nav-link" id="websitedesignListLink" onclick="showWebsiteDesign(event)">Website Design</a>
                    </nav>
                </div><!-- component-item -->
            </div><!-- az-content-left -->

            <div class="container">
                <div class="az-content-body pd-lg-l-40 d-flex flex-column" style="width: 1000px;">
                    <!-- User Tables Section -->
                    <div class="main-box clearfix" id="user-tables">
                        <div class="table-responsive">
                            <table class="table table-striped user-list">
                                <tbody>
                                    <?php
                                    require '../../db.php'; // Include your database connection file
                                    
                                    // Fetch all users
                                    $query = "SELECT * FROM users WHERE role_type = 'user'";
                                    $result = $conn->query($query);
                                    ?>
                                    <div class="main-box clearfix" id="user-tables">
                                        <h3>User List</h3>
                                        <div class="table-responsive">
                                            <table class="table table-striped user-list">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>User</th>
                                                        <th>Role</th>
                                                        <th class="text-center">Status</th>
                                                        <th>Email</th>
                                                        <th class="text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($result->num_rows > 0): 
                                                        while ($user = $result->fetch_assoc()): ?>
                                                            <tr>
                                                                <td>
                                                                    <img src="<?= htmlspecialchars($user['avatar']) ?>" alt=""
                                                                        style="width: 50px; height: 50px; border-radius: 50%;" class="mr-2">
                                                                    <a href="#"
                                                                        class="user-link"><?= htmlspecialchars($user['fname'] . ' ' . $user['lname']) ?></a>
                                                                </td>
                                                                <td><?= htmlspecialchars($user['role_type']) ?></td>
                                                                <td class="text-center">
                                                                    <span
                                                                        class="badge badge-<?= $user['is_verified'] == 1 ? 'success' : 'secondary' ?>">
                                                                        <?= $user['is_verified'] == 1 ? 'Active' : 'Inactive' ?>
                                                                    </span>
                                                                </td>
                                                                <td><a
                                                                        href="mailto:<?= htmlspecialchars($user['email']) ?>"><?= htmlspecialchars($user['email']) ?></a>
                                                                </td>
                                                                <td class="text-center">
                                                                    <a href="#" class="table-link"><i class="fas fa-search-plus"></i></a>
                                                                    <a href="#" class="table-link"><i class="fas fa-pencil-alt"></i></a>
                                                                    <a href="#" class="table-link text-danger"><i
                                                                            class="fas fa-trash-alt"></i></a>
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
                                        </div>
                                        <nav aria-label="Page navigation">
                                            <ul class="pagination">
                                                <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                                                <li class="page-item"><a class="page-link" href="#">1</a></li>
                                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                            </ul>
                                        </nav>
                                    </div><!-- main-box -->

                                    <!-- Agent Tables Section -->
                                    <div class="main-box clearfix" id="agent-tables" style="display:none;">
                                        <h3>Agent List</h3>
                                        <div class="table-responsive">
                                            <table class="table table-striped agent-list">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Agent</th>
                                                        <th>Role</th>
                                                        <th class="text-center">Status</th>
                                                        <th>Email</th>
                                                        <th class="text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $query = "SELECT * FROM users WHERE role_type = 'agent'"; // Fetch all agents
                                                    $result = $conn->query($query);

                                                    if ($result->num_rows > 0):
                                                        while ($agent = $result->fetch_assoc()): ?>
                                                            <tr>
                                                                <td>
                                                                    <img src="<?= htmlspecialchars($agent['avatar']) ?>" alt=""
                                                                        style="width: 50px; height: 50px; border-radius: 50%;" class="mr-2">
                                                                    <a href="#"
                                                                        class="user-link"><?= htmlspecialchars($agent['fname'] . ' ' . $agent['lname']) ?></a>
                                                                </td>
                                                                <td><?= htmlspecialchars($agent['role_type']) ?></td>
                                                                <td class="text-center">
                                                                    <span
                                                                        class="badge badge-<?= $agent['is_verified'] == 1 ? 'success' : 'secondary' ?>">
                                                                        <?= $agent['is_verified'] == 1 ? 'Active' : 'Inactive' ?>
                                                                    </span>
                                                                </td>
                                                                <td><a
                                                                        href="mailto:<?= htmlspecialchars($agent['email']) ?>"><?= htmlspecialchars($agent['email']) ?></a>
                                                                </td>
                                                                <td class="text-center">
                                                                    <a href="#" class="table-link"><i class="fas fa-search-plus"></i></a>
                                                                    <a href="#" class="table-link"><i class="fas fa-pencil-alt"></i></a>
                                                                    <a href="#" class="table-link text-danger"><i
                                                                            class="fas fa-trash-alt"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php endwhile;
                                                    else: ?>
                                                        <tr>
                                                            <td colspan="5" class="text-center">No agents found.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <nav aria-label="Page navigation">
                                            <ul class="pagination">
                                                <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                                                <li class="page-item"><a class="page-link" href="#">1</a></li>
                                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                            </ul>
                                        </nav>
                                    </div><!-- main-box -->

                                    <div class="main-box clearfix" id="agent-verification" style="display:none;">
                                        <h3>Agent Verification</h3>
                                        <div class="table-responsive">
                                            <table class="table table-striped agent-list">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Agent</th>
                                                        <th>Role</th>
                                                        <th class="text-center">Status</th>
                                                        <th>Email</th>
                                                        <th class="text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $query = "SELECT * FROM users WHERE role_type = 'agent' AND admin_verify = 0";
                                                    $result = $conn->query($query);

                                                    if ($result->num_rows > 0):
                                                        while ($agent = $result->fetch_assoc()): ?>
                                                            <tr>
                                                                <td>
                                                                    <img src="../../assets/profile_images/<?= htmlspecialchars($agent['profile']) ?>" alt=""
                                                                        style="width: 50px; height: 50px; border-radius: 50%;" class="mr-2">
                                                                    <a href="#" class="user-link text-dark"><?= htmlspecialchars($agent['fname'] . ' ' . $agent['lname']) ?></a>
                                                                </td>
                                                                <td><?= htmlspecialchars($agent['role_type']) ?></td>
                                                                <td class="text-center">
                                                                    <span class="badge badge-<?= $agent['admin_verify'] == 1 ? 'success' : 'secondary' ?>">
                                                                        <?= $agent['admin_verify'] == 1 ? 'Verified' : 'Not Verified' ?>
                                                                    </span>
                                                                </td>
                                                                <td><a href="mailto:<?= htmlspecialchars($agent['email']) ?>" class="text-dark"><?= htmlspecialchars($agent['email']) ?></a></td>
                                                                <td class="text-center">
                                                                    <!-- View Button (Opens Modal) -->
                                                                    <button class="btn btn-info btn-sm view-agent" data-toggle="modal" 
                                                                        data-target="#viewAgentModal" 
                                                                        data-agent='<?= json_encode($agent) ?>'>
                                                                        <i class="fas fa-eye"></i> View
                                                                    </button>

                                                                    <!-- Accept Button (Approve Agent) -->
                                                                    <button class="btn btn-success btn-sm approve-agent" data-id="<?= $agent['user_id'] ?>">
                                                                        <i class="fas fa-check"></i> Accept
                                                                    </button>

                                                                    <!-- Decline Button (Reject Agent) -->
                                                                    <button class="btn btn-danger btn-sm decline-agent" data-id="<?= $agent['user_id'] ?>">
                                                                        <i class="fas fa-times"></i> Decline
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        <?php endwhile;
                                                    else: ?>
                                                        <tr>
                                                            <td colspan="5" class="text-center">No agents found.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                        <nav aria-label="Page navigation">
                                            <ul class="pagination">
                                                <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                                                <li class="page-item"><a class="page-link" href="#">1</a></li>
                                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                            </ul>
                                        </nav>
                                    </div>

                                    <div class="modal fade" id="viewAgentModal" tabindex="-1" role="dialog" aria-labelledby="viewAgentModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="viewAgentModalLabel">Agent Details</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="text-center">
                                                        <img id="agentProfileImg" src="" alt="Agent Image" class="rounded-circle" style="width: 100px; height: 100px;">
                                                    </div>
                                                    <p><strong>Name:</strong> <span id="agentName"></span></p>
                                                    <p><strong>Email:</strong> <span id="agentEmail"></span></p>
                                                    <p><strong>Location:</strong> <span id="agentLocation"></span></p>
                                                    <p><strong>Mobile:</strong> <span id="agentMobile"></span></p>
                                                    <hr>
                                                    <h6>Primary ID</h6>
                                                    <p><strong>Type:</strong> <span id="agentPrimaryIDType"></span></p>
                                                    <p><strong>Number:</strong> <span id="agentPrimaryIDNumber"></span></p>
                                                    <div class="text-center">
                                                        <img id="agentPrimaryIDImg" src="" alt="Primary ID Image" class="img-fluid" style="max-width: 200px;">
                                                    </div>
                                                    <hr>
                                                    <h6>Secondary ID</h6>
                                                    <p><strong>Type:</strong> <span id="agentSecondaryIDType"></span></p>
                                                    <p><strong>Number:</strong> <span id="agentSecondaryIDNumber"></span></p>
                                                    <div class="text-center">
                                                        <img id="agentSecondaryIDImg" src="" alt="Secondary ID Image" class="img-fluid" style="max-width: 200px;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Agent Registration Form Section -->
                                    <div class="main-box" id="registration-form" style="display:none;">
                                        <h3>Agent Registration</h3>
                                        <form action="../../backend/agent_registration.php" method="POST" id="agentRegistrationForm" enctype="multipart/form-data">
                                            <div class="row">
                                                <!-- Left Column -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="agentName">First name</label>
                                                        <input name="first_name" type="text" class="form-control" placeholder="Enter first name" required>
                                                        <span class="error-message" id="first_name_error" style="color:red;"></span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="agentEmail">Email</label>
                                                        <input name="email" type="email" class="form-control" placeholder="Enter email" required>
                                                        <span class="error-message" id="email_error" style="color:red;"></span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="agentLocation">Location</label>
                                                        <input name="location" type="text" class="form-control" placeholder="Enter City/Province" required>
                                                        <span class="error-message" id="location_error" style="color:red;"></span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Valid IDs select up to 2</label>
                                                        <div class="row">
                                                            <!-- Primary ID Column -->
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="primary_id_type">Primary ID (Select one)</label>
                                                                    <select id="primary_id_type" name="primary_id_type" class="form-control" required>
                                                                        <option value="">Select Primary ID</option>
                                                                        <option value="passport">PH Passport</option>
                                                                        <option value="sss">SSS ID</option>
                                                                        <option value="gsis">GSIS ID</option>
                                                                        <option value="drivers_license">Driver's License</option>
                                                                        <option value="nbi">NBI Clearance</option>
                                                                        <option value="voters_id">Voter's ID</option>
                                                                        <option value="voters_cert">Voter's Certificate</option>
                                                                    </select>
                                                                    <input name="primary_id_number" type="text" class="form-control mt-2" placeholder="Enter ID Number" required>
                                                                    <input name="primary_id_image" type="file" class="form-control mt-2" accept="image/*" required>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Secondary ID Column -->
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="secondary_id_type">Secondary ID (Select one)</label>
                                                                    <select id="secondary_id_type" name="secondary_id_type" class="form-control" required>
                                                                        <option value="">Select Secondary ID</option>
                                                                        <option value="philhealth">PhilHealth ID</option>
                                                                        <option value="national">National ID</option>
                                                                        <option value="postal">Postal ID (2015 onwards)</option>
                                                                        <option value="company">Company ID</option>
                                                                    </select>
                                                                    <input name="secondary_id_number" type="text" class="form-control mt-2" placeholder="Enter ID Number" required>
                                                                    <input name="secondary_id_image" type="file" class="form-control mt-2" accept="image/*" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <small class="form-text text-muted">Please upload clear scanned copies or photos of your IDs</small>
                                                        <span class="error-message" id="valid_ids_error" style="color:red;"></span>
                                                    </div>
                                                </div>

                                                <!-- Right Column -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="agentName">Last name</label>
                                                        <input name="last_name" type="text" class="form-control" placeholder="Enter last name" required>
                                                        <span class="error-message" id="last_name_error" style="color:red;"></span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="agentMobile">Mobile Number</label>
                                                        <input name="mobile" type="tel" class="form-control" placeholder="Enter mobile number (e.g., +63 912 345 6789)" required pattern="^\+63[0-9]{10}$" inputmode="numeric" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode === 43">
                                                        <span class="error-message" id="mobile_error" style="color:red;"></span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="agentImage">Profile Image</label>
                                                        <input name="profile_image" type="file" class="form-control" accept="image/*" required>
                                                        <span class="error-message" id="profile_image_error" style="color:red;"></span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="agentPassword">Password</label>
                                                        <div class="input-group">
                                                            <input name="password" type="password" class="form-control" placeholder="Enter Password" required>
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary" type="button" id="togglePassword" onclick="togglePasswordVisibility('password')">Show</button>
                                                            </div>
                                                        </div>
                                                        <span class="error-message" id="password_error" style="color:red;"></span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="confirmPassword">Confirm Password</label>
                                                        <div class="input-group">
                                                            <input name="confirm_password" type="password" class="form-control" placeholder="Confirm Password" required>
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword" onclick="togglePasswordVisibility('confirm_password')">Show</button>
                                                            </div>
                                                        </div>
                                                        <span class="error-message" id="confirm_password_error" style="color:red;"></span>
                                                    </div>
                                                    <script>
                                                        function togglePasswordVisibility(inputName) {
                                                            const inputField = document.querySelector(`input[name="${inputName}"]`);
                                                            const button = document.getElementById(`toggle${inputName.charAt(0).toUpperCase() + inputName.slice(1)}`);
                                                            inputField.type = inputField.type === 'password' ? 'text' : 'password';
                                                            button.textContent = inputField.type === 'password' ? 'Show' : 'Hide';
                                                        }
                                                    </script>
                                                </div>
                                            </div>

                                            <!-- Submit Button (Full Width) -->
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <button name="sign_up.btn" type="submit" class="btn btn-primary btn-block">
                                                        <span id="buttonText">Register agent</span>
                                                        <span id="loadingSpinner" style="display: none;" class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                 
                  
                                      

                    <div class="main-box" id="website-design" style="display:none;">
                    <div class="button-container d-flex justify-content-end">
                        <!-- Reload Page button -->
                        <button id="reload-page-button" class="btn btn-primary">Reload Page</button>
                    </div>
                        <div class="iframe-container" style="overflow: hidden;">
                            <!-- Iframe to display the webpage -->
                            <iframe id="website-viewer" src="../../index.php" width="100%" height="800px" frameborder="0"></iframe>
                        </div>
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
                    <div class="main-box" id="admin-registration-form" style="display:none;">
                        <h3>Admin Registration</h3>
                        
                        <!-- PIN Code Verification Form -->
                        <div id="pin-verification">
                            <div class="form-group">
                                <label for="adminPin">Enter Admin PIN Code</label>
                                <input type="password" class="form-control" id="adminPin" placeholder="Enter PIN code" required>
                                <span class="error-message" id="pin_error" style="color:red;"></span>
                            </div>
                            <button type="button" class="btn btn-primary" id="verifyPin">Verify PIN</button>
                        </div>

                        <!-- Admin Registration Form (initially hidden) -->
                        <form action="../../backend/admin_registration.php" method="POST" id="adminRegistrationForm" style="display:none;">
                            <div class="form-group">
                                <label>First name</label>
                                <input name="first_name" type="text" class="form-control" placeholder="Enter first name" required>
                                <span class="error-message" id="admin_first_name_error" style="color:red;"></span>
                            </div>
                            <div class="form-group">
                                <label>Last name</label>
                                <input name="last_name" type="text" class="form-control" placeholder="Enter last name" required>
                                <span class="error-message" id="admin_last_name_error" style="color:red;"></span>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input name="email" type="email" class="form-control" placeholder="Enter email" required>
                                <span class="error-message" id="admin_email_error" style="color:red;"></span>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input name="password" type="password" class="form-control" placeholder="Enter password" required>
                                <span class="error-message" id="admin_password_error" style="color:red;"></span>
                            </div>
                            <button name="admin_signup_btn" type="submit" class="btn btn-primary">
                                <span id="adminButtonText">Register Admin</span>
                                <span id="adminLoadingSpinner" style="display: none;" class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div><!-- az-content-body -->
        </div><!-- container -->

    </div><!-- az-content -->

    <div class="az-footer ht-40">
        <div class="container ht-100p pd-t-0-f">
            <span class="text-muted d-block text-center">Copyright ©LoremIpsum 2024</span>
        </div><!-- container -->
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content-popup">
                <div class="modal-body text-center">
                    <!-- Checkmark Icon with Animation -->
                    <div class="checkmark-wrapper">
                        <i class="fas fa-check-circle checkmark-icon"></i>
                    </div>
                    <p class="modal-message">Account successfully created. A verification email has been sent to agent account.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" id="okButton">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/lib/jquery/jquery.min.js"></script>
    <script src="../../assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/lib/ionicons/ionicons.js"></script>
    <script src="../../assets/lib/jquery.flot/jquery.flot.js"></script>
    <script src="../../assets/lib/jquery.flot/jquery.flot.resize.js"></script>
    <script src="../../assets/lib/chart.js/Chart.bundle.min.js"></script>
    <script src="../../assets/lib/peity/jquery.peity.min.js"></script>

    <script src="../../assets/js/azia.js"></script>
    <script src="../../assets/js/chart.flot.sampledata.js"></script>
    <script src="../../assets/js/dashboard.sampledata.js"></script>
    <script src="../../assets/js/jquery.cookie.js" type="text/javascript"></script>

    <script src="../../assets/js/addedFunctions.js"></script>

    <script>
        maptilersdk.config.apiKey = 'gLXa6ihZF9HF7keYdTHC';

        const userPropertyMap = new maptilersdk.Map({
            container: 'userPropertyMap',
            style: maptilersdk.MapStyle.HYBRID,
            geolocate: maptilersdk.GeolocationType.POINT,
            zoom: 10,
            maxZoom: 16.2
        });

        const agentPropertyMap = new maptilersdk.Map({
            container: 'agentPropertyMap',
            style: maptilersdk.MapStyle.HYBRID,
            geolocate: maptilersdk.GeolocationType.POINT,
            zoom: 10,
            maxZoom: 16.2
        });

        const properties = <?php echo json_encode($properties); ?>;

        properties.forEach(property => {
            // Extract coordinates (an array of [longitude, latitude] pairs for the polygon)
            const coordinates = property.coordinates;

            if (Array.isArray(coordinates)) {
                // Loop over the coordinates array to add a marker for each pair
                coordinates.forEach(coord => {
                    if (Array.isArray(coord) && coord.length === 2) {
                        const lng = coord[0];  // Longitude
                        const lat = coord[1];  // Latitude

                        // Create a marker at the property coordinates
                        const marker = new maptilersdk.Marker()
                            .setLngLat([lng, lat])  // Set the position of the marker
                            .addTo(agentPropertyMap);  // Add marker to the map
                    } else {
                        console.warn("Invalid coordinate format:", coord);
                    }
                });
            } else {
                console.warn("Coordinates are not an array:", coordinates);
            }
        });
    </script>

    <script>
        document.getElementById('saveAmenities').addEventListener('click', function () {
            const selectedAmenities = [];
            const checkboxes = document.querySelectorAll('.modal-body input[type="checkbox"]:checked');
            checkboxes.forEach(checkbox => {
                selectedAmenities.push(checkbox.value);
            });

            // Display selected amenities
            const selectedAmenitiesDiv = document.getElementById('selectedAmenities');
            selectedAmenitiesDiv.innerHTML = selectedAmenities.length > 0
                ? 'Selected Amenities: ' + selectedAmenities.join(', ')
                : 'No amenities selected';

            // Close the modal
            $('#amenityModal').modal('hide');
        });
    </script>

    <script>
        // Update the label on file selection
        document.querySelector('.custom-file-input').addEventListener('change', function (e) {
            const fileNames = Array.from(e.target.files).map(file => file.name);
            const label = e.target.nextElementSibling;
            label.classList.add('selected');
            label.innerHTML = fileNames.length > 2 ? `${fileNames[0]}, ${fileNames[1]}, +${fileNames.length - 2} more` : fileNames.join(', ');
        });
    </script>

    <script>
      function showUserTable(event) {
        event.preventDefault(); // Prevent default anchor behavior
        document.getElementById('user-tables').style.display = 'block';
        document.getElementById('agent-tables').style.display = 'none';
        document.getElementById('registration-form').style.display = 'none';
        document.getElementById('website-design').style.display = 'none';
        document.getElementById('agent-verification').style.display = 'none';
        setActiveLink(event.currentTarget.id);
    }

    function showAgentTable(event) {
        event.preventDefault(); // Prevent default anchor behavior
        document.getElementById('user-tables').style.display = 'none';
        document.getElementById('agent-tables').style.display = 'block';
        document.getElementById('registration-form').style.display = 'none';
        document.getElementById('website-design').style.display = 'none';
        document.getElementById('agent-verification').style.display = 'none';
        setActiveLink(event.currentTarget.id);
    }

    function showRegistration(event) {
        event.preventDefault(); // Prevent default anchor behavior
        document.getElementById('user-tables').style.display = 'none';
        document.getElementById('agent-tables').style.display = 'none';
        document.getElementById('registration-form').style.display = 'block';
        document.getElementById('website-design').style.display = 'none';
        document.getElementById('agent-verification').style.display = 'none';
        setActiveLink(event.currentTarget.id);
    }

    function showWebsiteDesign(event) {
        event.preventDefault(); // Prevent default anchor behavior
        document.getElementById('user-tables').style.display = 'none';
        document.getElementById('agent-tables').style.display = 'none';
        document.getElementById('registration-form').style.display = 'none';
        document.getElementById('website-design').style.display = 'block';
        document.getElementById('agent-verification').style.display = 'none';
        setActiveLink(event.currentTarget.id);
    }

    function showAgentVerification(event) {
        event.preventDefault(); // Prevent default anchor behavior
        document.getElementById('user-tables').style.display = 'none';
        document.getElementById('agent-tables').style.display = 'none';
        document.getElementById('registration-form').style.display = 'none';
        document.getElementById('website-design').style.display = 'none';
        document.getElementById('agent-verification').style.display = 'block';
        setActiveLink(event.currentTarget.id);
    }

    function setActiveLink(activeId) {
        const links = ['userListLink', 'agentListLink', 'agentRegistrationLink', 'websitedesignListLink', 'agentVerificationLink'];
        links.forEach(link => {
            const element = document.getElementById(link);
            if (element) {
                if (link === activeId) {
                    element.classList.add('active');
                } else {
                    element.classList.remove('active');
                }
            }
        });
    }

    </script>

    <script>
        document.getElementById('mapButton').addEventListener('click', function () {
            var mapContainer = document.getElementById('mapContainer');
            var propertyList = document.querySelector('.property-list');

            // Toggle the map container visibility
            mapContainer.classList.toggle('open');

            // Adjust the property list layout: switch to 1 column when the map is shown
            if (mapContainer.classList.contains('open')) {
                propertyList.classList.add('one-column');
            } else {
                propertyList.classList.remove('one-column');
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get all the buttons
            const buttons = document.querySelectorAll('.btn-group .btn');

            // Form sections for different property types
            const landForm = document.getElementById('landForm');
            const singleAttachedHouseForm = document.getElementById('singleAttachedHouseForm');
            const singleDetachedHouseForm = document.getElementById('singleDetachedHouseForm');
            const rowhouseForm = document.getElementById('rowhouseForm');
            const apartmentForm = document.getElementById('apartmentForm');
            const villaForm = document.getElementById('villaForm');

            // Function to hide all forms and disable their fields
            function hideAllForms() {
                // Get all form sections
                const forms = [landForm, singleAttachedHouseForm, singleDetachedHouseForm, rowhouseForm, apartmentForm, villaForm];

                // Loop through all forms
                forms.forEach(form => {
                    // Disable all input fields within the form
                    const inputs = form.querySelectorAll('input, textarea, select');
                    inputs.forEach(input => {
                        input.disabled = true;
                    });
                    // Hide the form
                    form.style.display = 'none';
                });
            }

            // Function to show the selected form and enable its fields
            function showForm(form) {
                // Hide all forms first and disable their fields
                hideAllForms();

                // Show the selected form and enable its fields
                form.style.display = 'block';
                const inputs = form.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    input.disabled = false;  // Enable the fields of the currently visible form
                });
            }

            // Add event listeners to each button
            buttons.forEach(button => {
                button.addEventListener('click', function () {
                    // Remove the "active" class from all buttons
                    buttons.forEach(btn => btn.classList.remove('active'));

                    // Add the "active" class to the clicked button
                    this.classList.add('active');

                    // Show the corresponding form based on the clicked button
                    if (this.id === 'landBtn') {
                        showForm(landForm);
                    } else if (this.id === 'singleAttachedHouseBtn') {
                        showForm(singleAttachedHouseForm);
                    } else if (this.id === 'singleDetachedHouseBtn') {
                        showForm(singleDetachedHouseForm);
                    } else if (this.id === 'rowhouseBtn') {
                        showForm(rowhouseForm);
                    } else if (this.id === 'apartmentBtn') {
                        showForm(apartmentForm);
                    } else if (this.id === 'villaBtn') {
                        showForm(villaForm);
                    }
                });
            });

            // Initially, show the Land form
            showForm(landForm);
        });
    </script>

    <script>
        // Function to reload the iframe
        function reloadWebsite() {
            var iframe = document.getElementById('website-viewer');
            iframe.contentWindow.location.reload(); // Reload the page in the iframe
        }

        // Event listener for the reload button
        document.getElementById('reload-page-button').addEventListener('click', reloadWebsite);

        document.getElementById('website-viewer').style.transform = 'scale(0.8)';
        document.getElementById('website-viewer').style.transformOrigin = 'top left';
        document.getElementById('website-viewer').style.width = '125%';  // Increase width to compensate for zoom

        // Handle Form Submit for Appearance Customization
        document.getElementById('appearance-form').addEventListener('submit', function (e) {
            e.preventDefault();

            const backgroundColor = document.getElementById('background-color').value;
            const primaryColor = document.getElementById('primary-color').value;
            const secondaryColor = document.getElementById('secondary-color').value;
            const fontFamily = document.getElementById('font-family').value;
            const fontSize = document.getElementById('font-size').value;
            const layoutWidth = document.getElementById('layout-width').value;
            const spacing = document.getElementById('spacing').value;

            const iframe = document.getElementById('website-viewer');
            const iframeDocument = iframe.contentDocument || iframe.contentWindow.document;

            // Apply changes to iframe styles
            iframeDocument.body.style.backgroundColor = backgroundColor;
            iframeDocument.body.style.fontFamily = fontFamily;
            iframeDocument.body.style.fontSize = fontSize + 'px';
            iframeDocument.body.style.margin = spacing + 'px';

            // Apply layout width
            iframeDocument.documentElement.style.maxWidth = layoutWidth + 'px';

            // Apply primary and secondary colors (just for demo purposes)
            iframeDocument.querySelectorAll('a').forEach(link => {
                link.style.color = primaryColor;
            });

            // Apply additional styling (for example, change button background)
            iframeDocument.querySelectorAll('button').forEach(button => {
                button.style.backgroundColor = primaryColor;
            });

            alert("Changes applied to the iframe");
        });
    </script>

    <!--registration/loading-->
    <script>
        document.getElementById('agentRegistrationForm').addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent form from submitting the traditional way

            // Hide error messages before submitting the form
            document.getElementById('first_name_error').textContent = '';
            document.getElementById('last_name_error').textContent = '';
            document.getElementById('email_error').textContent = '';
            document.getElementById('password_error').textContent = '';

            // Show loading spinner
            document.getElementById('buttonText').style.display = 'none';
            document.getElementById('loadingSpinner').style.display = 'inline-block';

            const formData = new FormData(this);

            // Send form data using AJAX
            fetch('../../backend/agent_registration.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    // Hide loading spinner
                    document.getElementById('loadingSpinner').style.display = 'none';
                    document.getElementById('buttonText').style.display = 'inline-block';

                    if (data.success) {
                        // Show the success modal
                        $('#successModal').modal('show');
                    } else {
                        // Show validation errors if any
                        for (const key in data.errors) {
                            if (data.errors.hasOwnProperty(key)) {
                                const errorElement = document.getElementById(`${key}_error`);
                                if (errorElement) {
                                    errorElement.textContent = data.errors[key];
                                }
                            }
                        }
                    }
                })
                .catch(error => {
                    // Handle unexpected errors
                    console.error('Error:', error);
                    document.getElementById('loadingSpinner').style.display = 'none';
                    document.getElementById('buttonText').style.display = 'inline-block';
                });
        });

        // Redirect or close the modal when OK button is clicked
        document.getElementById('okButton').addEventListener('click', function () {
            $('#successModal').modal('hide');

            window.location.href = '../../frontend/frontend_users/admin_control.php';
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navTypeSelector = document.getElementById('navTypeSelector');
            
            function loadNavigationItems(navType) {
                fetch(`/api/navigation.php?type=${navType}`)
                    .then(response => response.json())
                    .then(items => {
                        const container = document.getElementById('navigationItems');
                        container.innerHTML = '';
                        
                        items.forEach(item => {
                            const itemElement = createNavigationItemElement(item);
                            container.appendChild(itemElement);
                        });
                    });
            }
            
            navTypeSelector.addEventListener('change', (e) => {
                loadNavigationItems(e.target.value);
            });
            
            // Initial load
            loadNavigationItems(navTypeSelector.value);
        });

        // Add navigation item handling
        document.getElementById('addNavItem').addEventListener('click', () => {
            document.getElementById('navItemId').value = '';
            document.getElementById('navItemForm').reset();
            $('#navItemModal').modal('show');
        });

        document.getElementById('saveNavItem').addEventListener('click', async () => {
            const formData = new FormData(document.getElementById('navItemForm'));
            formData.append('nav_type', document.getElementById('navTypeSelector').value);
            
            try {
                const response = await fetch('/api/navigation.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    $('#navItemModal').modal('hide');
                    loadNavigationItems(document.getElementById('navTypeSelector').value);
                }
            } catch (error) {
                console.error('Error saving navigation item:', error);
            }
        });
    </script>

    <script>
        function showAdminRegistration(event) {
            event.preventDefault();
            document.getElementById('user-tables').style.display = 'none';
            document.getElementById('agent-tables').style.display = 'none';
            document.getElementById('registration-form').style.display = 'none';
            document.getElementById('website-design').style.display = 'none';
            document.getElementById('admin-registration-form').style.display = 'block';
            setActiveLink(event.currentTarget.id);
        }

        // PIN verification handling
        document.getElementById('verifyPin').addEventListener('click', function() {
            const pin = document.getElementById('adminPin').value;
            const pinError = document.getElementById('pin_error');
            
            // Replace '1234' with your actual PIN code or implement server-side verification
            if (pin === '1234') {
                document.getElementById('pin-verification').style.display = 'none';
                document.getElementById('adminRegistrationForm').style.display = 'block';
                pinError.textContent = '';
            } else {
                pinError.textContent = 'Invalid PIN code';
            }
        });

        // Admin registration form handling
        document.getElementById('adminRegistrationForm').addEventListener('submit', function(event) {
            event.preventDefault();

            // Hide error messages
            document.getElementById('admin_first_name_error').textContent = '';
            document.getElementById('admin_last_name_error').textContent = '';
            document.getElementById('admin_email_error').textContent = '';
            document.getElementById('admin_password_error').textContent = '';

            // Show loading spinner
            document.getElementById('adminButtonText').style.display = 'none';
            document.getElementById('adminLoadingSpinner').style.display = 'inline-block';

            const formData = new FormData(this);

            // Send form data using AJAX
            fetch('../../backend/admin_registration.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading spinner
                document.getElementById('adminLoadingSpinner').style.display = 'none';
                document.getElementById('adminButtonText').style.display = 'inline-block';

                if (data.success) {
                    // Show success modal
                    $('#successModal').modal('show');
                    // Update success message for admin registration
                    document.querySelector('.modal-message').textContent = 'Admin account successfully created.';
                } else {
                    // Show validation errors
                    for (const key in data.errors) {
                        if (data.errors.hasOwnProperty(key)) {
                            const errorElement = document.getElementById(`admin_${key}_error`);
                            if (errorElement) {
                                errorElement.textContent = data.errors[key];
                            }
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('adminLoadingSpinner').style.display = 'none';
                document.getElementById('adminButtonText').style.display = 'inline-block';
            });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
       document.addEventListener("DOMContentLoaded", function () {
    // Handle View Button Click
    document.querySelectorAll(".view-agent").forEach(button => {
        button.addEventListener("click", function () {
            let agent = JSON.parse(this.getAttribute("data-agent"));

            // Populate Modal Fields
            document.getElementById("agentProfileImg").src = "../../assets/profile_images/" + agent.profile;
            document.getElementById("agentName").textContent = agent.fname + " " + agent.lname;
            document.getElementById("agentEmail").textContent = agent.email;
            document.getElementById("agentLocation").textContent = agent.location;
            document.getElementById("agentMobile").textContent = agent.mobile;

            document.getElementById("agentPrimaryIDType").textContent = agent.primary_id_type;
            document.getElementById("agentPrimaryIDNumber").textContent = agent.primary_id_number;
            document.getElementById("agentPrimaryIDImg").src = "../../assets/agents/" + agent.primary_id_image;

            document.getElementById("agentSecondaryIDType").textContent = agent.secondary_id_type;
            document.getElementById("agentSecondaryIDNumber").textContent = agent.secondary_id_number;
            document.getElementById("agentSecondaryIDImg").src = "../../assets/agents/" + agent.secondary_id_image;
        });
    });

    // Handle Approve Agent
    document.querySelectorAll(".approve-agent").forEach(button => {
        button.addEventListener("click", function () {
            let agentId = this.getAttribute("data-id");

            if (confirm("Are you sure you want to approve this agent?")) {
                fetch("../../backend/approve_agent.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "user_id=" + agentId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Agent approved successfully!");
                        location.reload();
                    } else {
                        alert("Error approving agent.");
                    }
                });
            }
        });
    });

    // Handle Decline Agent
    document.querySelectorAll(".decline-agent").forEach(button => {
        button.addEventListener("click", function () {
            let agentId = this.getAttribute("data-id");

            if (confirm("Are you sure you want to decline this agent?")) {
                fetch("../../backend/decline_agent.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "user_id=" + agentId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Agent declined successfully!");
                        location.reload();
                    } else {
                        alert("Error declining agent.");
                    }
                });
            }
        });
    });
});


    </script>

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
    <script>
        document.getElementById("website-viewer").onload = function() {
            let iframeDoc = document.getElementById("website-viewer").contentWindow.document;

            let buttons = iframeDoc.querySelectorAll("button");
            buttons.forEach(button => {
                button.disabled = true;
                button.style.pointerEvents = "none"; 
                button.style.opacity = "0.5";
            });

            let links = iframeDoc.querySelectorAll("a");
            links.forEach(link => {
                link.removeAttribute("href");
                link.style.pointerEvents = "none";
                link.style.color = "gray";
                link.style.textDecoration = "none"; 
            });
        };
    </script>


    <script>
    document.addEventListener("DOMContentLoaded", function() {
        let textInput = document.getElementById("background_color");
        let colorPicker = document.getElementById("colorPicker");

        colorPicker.addEventListener("input", function() {
            textInput.value = this.value;
        });
    });
    </script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        let fontFamily = document.getElementById("fontFamily");
        let fontStyle = document.getElementById("fontStyle");
        let fontSize = document.getElementById("fontSize");
        let fontSizeValue = document.getElementById("fontSizeValue");
        let fontPreview = document.getElementById("fontPreview");

        function updatePreview() {
            fontPreview.style.fontFamily = fontFamily.value;
            fontPreview.style.fontSize = fontSize.value + "px";
            fontSizeValue.textContent = fontSize.value + "px";

            let selectedStyle = fontStyle.value;
            if (selectedStyle === "bold") {
                fontPreview.style.fontWeight = "bold";
                fontPreview.style.fontStyle = "normal";
            } else if (selectedStyle === "italic") {
                fontPreview.style.fontWeight = "normal";
                fontPreview.style.fontStyle = "italic";
            } else if (selectedStyle === "bold italic") {
                fontPreview.style.fontWeight = "bold";
                fontPreview.style.fontStyle = "italic";
            } else {
                fontPreview.style.fontWeight = "normal";
                fontPreview.style.fontStyle = "normal";
            }
        }

        fontFamily.addEventListener("change", updatePreview);
        fontStyle.addEventListener("change", updatePreview);
        fontSize.addEventListener("input", updatePreview);

        updatePreview();
    });
    </script>



</body>

</html>