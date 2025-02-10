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
    <meta charset="utf-8"> <!-- Character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> <!-- Responsive design -->

    <title>Land Map | Control Panel</title> <!-- Page title -->
    <link rel="icon" href="../../assets/images/logo.png" type="image/x-icon"> <!-- Favicon -->

    <!-- vendor css -->
    <link href="../../assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet"> <!-- Font Awesome -->
    <link href="../../assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet"> <!-- Ionicons -->
    <link href="../../assets/lib/typicons.font/typicons.css" rel="stylesheet"> <!-- Typicons -->
    <link href="../../assets/lib/flag-icon-css/css/flag-icon.min.css" rel="stylesheet"> <!-- Flag Icons -->

    <!-- Mapping Links -->
    <script src="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.umd.js"></script> <!-- Maptiler SDK -->
    <link href="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.css" rel="stylesheet" />
    <!-- Maptiler CSS -->

    <!-- azia CSS -->
    <link rel="stylesheet" href="../../assets/css/azia.css"> <!-- Custom CSS -->
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
                        <h2 class="az-dashboard-title">Control Panel</h2> <!-- Dashboard title -->
                        <p class="az-dashboard-text">Super admin</p> <!-- Dashboard text -->
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
                        <a class="nav-link active" data-toggle="tab" href="#dashboard">Listed Properties</a>
                        <!-- Tab for listed properties -->
                        <a class="nav-link" data-toggle="tab" href="#create_property">Post New land property</a>
                        <!-- Tab for posting new property -->
                    </nav>

                    <nav class="nav">
                    </nav>
                </div>

                <div class="tab-content mt-4">
                    <div id="dashboard" class="tab-pane active">

                    </div>

                    <!-- PROPERTY CARD -->

                    <!-- CREATE PROPERTY LIST -->

                    <div id="create_property" class="tab-pane">
                        <div class="d-flex align-items-center mb-4">
                            <!-- Post new land property -->
                            <h3 class="mb-1 mr-5">Post New Land</h3> <!-- Title for posting new land -->

                            <!-- Tab-like Button -->
                        </div>
                    </div>
                    <!-- Cend of the land property form -->
                </div>
            </div>
        </div>
    </div>

    <div class="az-footer">
        <div class="container">
            <span class="text-muted d-block text-center">Copyright ©LoremIpsum 2024</span>
        </div>
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

    <!-- Success Modal create property -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content-popup">
                <div class="modal-body text-center">
                    <div class="checkmark-wrapper">
                        <i class="fas fa-check-circle checkmark-icon"></i>
                    </div>
                    <p class="modal-message">Property successfully added.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" id="closeModalBtn">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/lib/jquery/jquery.min.js"></script>
    <script src="../../assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/lib/ionicons/ionicons.js"></script>
    <script src="../../assets/js/azia.js"></script>

    <script src="../../assets/js/addedFunctions.js"></script>

    <script src="https://unpkg.com/maplibre-gl@latest/dist/maplibre-gl.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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

    <!--Label Changer in listing type, for sale or for lease-->
    <script>
        document.getElementById('saleOrLease').addEventListener('change', function () {
            const priceLabel = document.getElementById('priceLabel');
            const priceInput = document.getElementById('propertyPrice');

            if (this.value === 'lease') {
                priceLabel.textContent = 'Monthly Rate'; // Change label to "Monthly Rate"
                priceInput.placeholder = 'Enter monthly rate'; // Change placeholder
            } else if (this.value === 'sale') {
                priceLabel.textContent = 'Price'; // Change label back to "Price"
                priceInput.placeholder = 'Enter price'; // Change placeholder
            }
        });
    </script>

    <script>
        const timeElement = document.getElementById('time');
        if (timeElement) {
            timeElement.textContent = "New Time";
        }
    </script>


    <!--Image preview-->
    <script>
        let imageFiles = [];

        // Handle image preview
        document.getElementById('propertyImages').addEventListener('change', function (event) {
            const previewContainer = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('imagePreview');

            if (event.target.files.length > 0) {
                previewContainer.style.display = 'block'; // Show the preview box

                // Loop through selected files and add them to the preview section
                Array.from(event.target.files).forEach(file => {
                    imageFiles.push(file); // Add the file to the imageFiles array
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        const imgElement = document.createElement('img');
                        imgElement.src = e.target.result;
                        imgElement.classList.add('img-thumbnail', 'm-2');
                        imgElement.style.maxWidth = '150px';
                        imgElement.style.maxHeight = '150px';

                        // Create a delete button for the image
                        const deleteButton = document.createElement('button');
                        deleteButton.innerHTML = '&times;';
                        deleteButton.classList.add('btn', 'btn-danger', 'btn-sm', 'position-absolute', 'top-0', 'end-0');
                        deleteButton.style.zIndex = '10';
                        deleteButton.style.width = '30px'; // Set width for round button
                        deleteButton.style.height = '30px'; // Set height for round button
                        deleteButton.style.borderRadius = '50%'; // Make it circular
                        deleteButton.style.fontSize = '18px'; // Adjust font size
                        deleteButton.style.padding = '0'; // Remove padding
                        deleteButton.style.lineHeight = '30px'; // Vertically center the 'X'
                        deleteButton.style.textAlign = 'center'; // Center the 'X'

                        deleteButton.onclick = function () {
                            // Remove the image from the array and the preview
                            const index = imageFiles.indexOf(file);
                            if (index > -1) {
                                imageFiles.splice(index, 1); // Remove from array
                            }
                            imgElement.remove();
                            deleteButton.remove();

                            // If no images left, hide the preview container
                            if (imageFiles.length === 0) {
                                previewContainer.style.display = 'none';
                            }
                        };

                        // Wrapper div for the image and delete button
                        const wrapper = document.createElement('div');
                        wrapper.classList.add('position-relative');
                        wrapper.appendChild(imgElement);
                        wrapper.appendChild(deleteButton);

                        preview.appendChild(wrapper);
                    };

                    reader.readAsDataURL(file);
                });
            }
        });
    </script>

    <!--Time Update-->
    <script>
        // Function to update the current time every second
        function updateTime() {
            const timeElement = document.getElementById('currentTime');

            // Get the current time in Manila timezone
            const now = new Date().toLocaleString("en-US", {
                timeZone: "Asia/Manila"
            });

            // Format the time as hh:mm:ss AM/PM
            const options = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            const timeString = new Date(now).toLocaleTimeString('en-US', options);

            // Update the time on the page
            timeElement.textContent = timeString;
        }

        // Update the time every 1000 milliseconds (1 second)
        setInterval(updateTime, 1000);
    </script>

    <!--Di ko alam kay palacio-->
    <script>
        // Initialize maps
        const map1 = new maptilersdk.Map({
            container: 'map1',
            style: maptilersdk.MapStyle.STREETS,
            center: [121.0537, 14.5489], // Manila coordinates
            zoom: 13
        });

        const map2 = new maptilersdk.Map({
            container: 'map2',
            style: maptilersdk.MapStyle.STREETS,
            center: [121.0537, 14.5489],
            zoom: 13
        });

        const mapInput = new maptilersdk.Map({
            container: 'mapInput',
            style: maptilersdk.MapStyle.STREETS,
            center: [121.0537, 14.5489],
            zoom: 13
        });
    </script>

    <!--create property form part-->
    <script>
        document.getElementById('saleOrLease').addEventListener('change', function () {
            var saleForm = document.getElementById('saleForm');
            var leaseForm = document.getElementById('leaseForm');
            var leaseDuration = document.getElementById('leaseDuration');
            var landCondition = document.getElementById('landCondition');
            var anotherInfo = document.getElementById('anotherInfo');

            // Hide both forms initially
            saleForm.style.display = 'none';
            leaseForm.style.display = 'none';

            // Reset the required attribute and visibility for fields
            landCondition.required = false;
            anotherInfo.required = false;
            landCondition.style.display = 'none';
            anotherInfo.style.display = 'none';

            // Show the relevant form based on selection
            if (this.value === 'sale') {
                saleForm.style.display = 'block';
                leaseDuration.required = false; // Disable required for lease duration if sale is selected

                // Show landCondition and anotherInfo for sale
                landCondition.style.display = 'block';
                landCondition.required = true; // Enable required for landCondition when 'For Sale' is selected
                anotherInfo.style.display = 'block';
                anotherInfo.required = true; // Enable required for anotherInfo when 'For Sale' is selected

            } else if (this.value === 'lease') {
                leaseForm.style.display = 'block';
                leaseDuration.required = true; // Enable required for lease duration if lease is selected

                // Hide landCondition and anotherInfo for lease
                landCondition.style.display = 'none';
                anotherInfo.style.display = 'none';
            }
        });

        // Trigger the change event on page load to initialize the form visibility
        document.getElementById('saleOrLease').dispatchEvent(new Event('change'));
    </script>

    <!-- modal if data is sent and modal appear -->
    <script>
        document.getElementById('propertyForm').addEventListener('submit', async function (event) {
            event.preventDefault(); // Prevent form from refreshing the page

            // Show loading spinner and disable button
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const loadingSpinner = document.getElementById('loadingSpinner');

            submitBtn.disabled = true;
            btnText.textContent = "Submitting...";
            loadingSpinner.classList.remove("d-none");

            const formData = new FormData(this); // Get form data

            try {
                const response = await fetch('../../backend/add_property.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.status === "success") {
                    // Show success modal
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'), {
                        backdrop: 'static',
                        keyboard: false
                    });
                    successModal.show();
                } else {
                    alert("Error: " + result.message);
                }
            } catch (error) {
                console.error("Submission error:", error);
                alert("Something went wrong!");
            } finally {
                // Reset button after submission
                submitBtn.disabled = false;
                btnText.textContent = "Submit";
                loadingSpinner.classList.add("d-none");
            }
        });

        // Redirect button
        document.getElementById('closeModalBtn').addEventListener('click', function () {
            window.location.href = "agent_listing.php"; // Change this to your actual landing page
        });
    </script>

    <script>
        function updateDateTime() {
            const now = new Date(); // Get current date and time
            const dateOptions = { year: 'numeric', month: 'short', day: 'numeric' }; // Date options
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

</body>

</html>