<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Land Map | Sign Up</title>
    <link rel="icon" href="../assets/images/logo.png" type="image/x-icon">
    <link href="../assets/lib/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/lib/ionicons/css/ionicons-core.min.css" rel="stylesheet">
    <link href="../assets/lib/typicons.font/typicons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/azia.css">

    <style>
        /* Back Button Styling */
        .back-button {
            position: absolute;
            top: 50px;
            left: 50px;
            text-decoration: none;
            color: #000;
            font-size: 1rem;
            font-weight: 500;
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, color 0.3s ease;
            z-index: 1000;
        }

        .back-button:hover {
            background-color: #006d77;
            color: #fff;
        }
    </style>
</head>

<body class="az-body">
    <div class="az-signup-wrapper">
        <div class="az-column-signup-left"
            style="background-image: url(); background-size: cover; background-position: center; padding: 20px;">
            <div>
                <h5
                    style="font-family: 'Roboto', sans-serif; font-size: 24px; font-weight: 600; color: #031a61; margin-bottom: 15px;">
                    Connect with Professional Land Agents</h5>
                <p
                    style="font-family: 'Open Sans', sans-serif; font-size: 16px; line-height: 1.6; color: #1b2e4b; margin-bottom: 20px;">
                    Find your perfect property by connecting directly with verified real estate agents. Get expert
                    guidance on land investments, property valuations, and market insights. Our platform enables
                    seamless communication between buyers and agents to help you make informed decisions.</p>
                <p style="font-family: 'Open Sans', sans-serif; font-size: 15px; color: #1b2e4b; margin-bottom: 8px;"><i
                        class="fas fa-check" style="color: #5b47fb; margin-right: 8px;"></i>Chat with agents in
                    real-time</p>
                <p style="font-family: 'Open Sans', sans-serif; font-size: 15px; color: #1b2e4b; margin-bottom: 8px;"><i
                        class="fas fa-check" style="color: #5b47fb; margin-right: 8px;"></i>Schedule property viewings
                </p>
                <p style="font-family: 'Open Sans', sans-serif; font-size: 15px; color: #1b2e4b; margin-bottom: 8px;"><i
                        class="fas fa-check" style="color: #5b47fb; margin-right: 8px;"></i>Get detailed property
                    information</p>
                <p style="font-family: 'Open Sans', sans-serif; font-size: 15px; color: #1b2e4b; margin-bottom: 25px;">
                    <i class="fas fa-check" style="color: #5b47fb; margin-right: 8px;"></i>Negotiate deals efficiently
                </p>
            </div>
        </div>

        <div class="az-column-signup" style="overflow: hidden;">
            <a href="../index.php">
                <img src="../assets/images/logo.png" alt="LandMap Logo" class="logo-img"
                    style="width: 5rem; margin-bottom: 1rem;">
            </a>
            <div class="az-signup-header">
                <h2>Sign up form</h2>

                <div class="d-flex justify-content-center" style="gap: 5px;">
                    <button id="signUpButton" class="btn-az-primary">Sign Up as User</button>
                    <button id="registrationButton"  class="btn-az-primary">Sign Up as Agent</button>
                </div>

                <form id="signUpForm" action="../backend/sign_up.php" method="POST">
                    <div class="form-group d-flex">
                        <div class="mr-2" style="flex: 1;">
                            <label>First Name</label>
                            <input name="first_name" type="text" class="form-control" placeholder="Enter your first name" style="text-transform: capitalize;">
                            <span class="error-message" id="first_name_error" style="color:red;"></span>
                        </div>
                        <div style="flex: 1;">
                            <label>Last Name</label>
                            <input name="last_name" type="text" class="form-control" placeholder="Enter your last name" style="text-transform: capitalize;">
                            <span class="error-message" id="last_name_error" style="color:red;"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Location</label>
                        <input name="location" type="text" class="form-control" placeholder="Enter your location" style="text-transform: capitalize;">
                        <span class="error-message" id="location_error" style="color:red;"></span>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input name="email" type="text" class="form-control" placeholder="Enter your email">
                        <span class="error-message" id="email_error" style="color:red;"></span>
                    </div>

                    <div class="form-group d-flex">
                        <div class="mr-2" style="flex: 1;">
                            <label>Password</label>
                            <input name="password" type="password" class="form-control" placeholder="Enter your password">
                            <span class="error-message" id="password_error" style="color:red;"></span>
                        </div>
                        <div style="flex: 1;">
                            <label>Confirm Password</label>
                            <input name="confirm_password" type="password" class="form-control" placeholder="Confirm your password">
                            <span class="error-message" id="confirm_password_error" style="color:red;"></span>
                        </div>
                    </div>

                    <div class="az-signup-footer" style="margin-bottom: 20px;">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="terms" name="terms" required>
                            <label class="custom-control-label" for="terms">I agree to the <a href="#"
                                    data-toggle="modal" data-target="#termsModal">Terms and Conditions</a></label>
                            <span class="error-message" id="terms_error" style="color:red;"></span>
                        </div>
                    </div>

                    <button name="sign_up.btn" type="submit" class="btn btn-az-primary btn-block"
                        style="margin-bottom: 20px;">
                        <span id="buttonText">Register</span>
                        <span id="loadingSpinner" style="display: none;"
                            class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true"></span>
                    </button>
                </form>

                <form id="registrationForm" enctype="multipart/form-data" style="display: none">
                    <div class="form-group d-flex">
                        <div class="mr-2" style="flex: 1;">
                            <label>First Name</label>
                            <input name="first_name" type="text" class="form-control" placeholder="Enter your first name" style="text-transform: capitalize;" required>
                            <span class="error-message" id="first_name_error" style="color:red;"></span>
                        </div>
                        <div style="flex: 1;">
                            <label>Last Name</label>
                            <input name="last_name" type="text" class="form-control" placeholder="Enter your last name" style="text-transform: capitalize;" required>
                            <span class="error-message" id="last_name_error" style="color:red;"></span>
                        </div>
                    </div>

                    <div class="form-group d-flex">
                        <div class="mr-2" style="flex: 1;">
                            <label>Location</label>
                            <input name="location" type="text" class="form-control" placeholder="Enter your location" style="text-transform: capitalize;" required>
                            <span class="error-message" id="location_error" style="color:red;"></span>
                        </div>
                        <div style="flex: 1;">
                            <label>Email</label>
                            <input name="email" type="email" class="form-control" placeholder="Enter your email" required>
                            <span class="error-message" id="email_error" style="color:red;"></span>
                        </div>
                    </div>

                    <div class="form-group d-flex">
                        <div class="mr-2" style="flex: 1;">
                            <label>Password</label>
                            <input name="password" type="password" class="form-control" placeholder="Enter your password" required>
                            <span class="error-message" id="password_error" style="color:red;"></span>
                        </div>
                        <div style="flex: 1;">
                            <label>Confirm Password</label>
                            <input name="confirm_password" type="password" class="form-control" placeholder="Confirm your password" required>
                            <span class="error-message" id="confirm_password_error" style="color:red;"></span>
                        </div>
                    </div>

                    <div class="form-group d-flex">
                        <div class="mr-2" style="flex: 1;">
                            <label>Mobile Number</label>
                            <input name="mobile" type="text" class="form-control" placeholder="Enter your mobile number" required>
                            <span class="error-message" id="mobile_error" style="color:red;"></span>
                        </div>
                        <div style="flex: 1;">
                            <label>Profile Image</label>
                            <input name="profile_image" type="file" class="form-control" required>
                            <span class="error-message" id="profile_image_error" style="color:red;"></span>
                        </div>
                    </div>

                    <div class="form-group d-flex">
                        <div class="mr-2" style="flex: 1;">
                            <label>Primary ID Type</label>
                            <select name="primary_id_type" class="form-control" required>
                                <option value="">Select Primary ID</option>
                                <option value="passport">PH Passport</option>
                                <option value="sss">SSS ID</option>
                                <option value="gsis">GSIS ID</option>
                                <option value="drivers_license">Driver's License</option>
                                <option value="nbi">NBI Clearance</option>
                                <option value="voters_id">Voter's ID</option>
                                <option value="voters_cert">Voter's Certificate</option>
                            </select>
                            <span class="error-message" id="primary_id_type_error" style="color:red;"></span>
                        </div>
                        <div style="flex: 1;">
                            <label>Primary ID Number</label>
                            <input name="primary_id_number" type="text" class="form-control" placeholder="Enter your primary ID number" required>
                            <span class="error-message" id="primary_id_number_error" style="color:red;"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Primary ID Image</label>
                        <input name="primary_id_image" type="file" class="form-control" required>
                        <span class="error-message" id="primary_id_image_error" style="color:red;"></span>
                    </div>

                    <div class="form-group d-flex">
                        <div class="mr-2" style="flex: 1;">
                            <label>Secondary ID Type</label>
                            <select id="secondary_id_type" name="secondary_id_type" class="form-control" required>
                                <option value="">Select Secondary ID</option>
                                <option value="philhealth">PhilHealth ID</option>
                                <option value="national">National ID</option>
                                <option value="postal">Postal ID (2015 onwards)</option>
                                <option value="company">Company ID</option>
                                <option value="other">Other ID</option>
                            </select>
                            <span class="error-message" id="secondary_id_type_error" style="color:red;"></span>
                        </div>
                        <div style="flex: 1;">
                            <label>Secondary ID Number</label>
                            <input name="secondary_id_number" type="text" class="form-control" placeholder="Enter your secondary ID number" required>
                            <span class="error-message" id="secondary_id_number_error" style="color:red;"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Secondary ID Image</label>
                        <input name="secondary_id_image" type="file" class="form-control" required>
                        <span class="error-message" id="secondary_id_image_error" style="color:red;"></span>
                    </div>

                    <div class="az-signup-footer" style="margin-bottom: 20px;">
                        <div class="custom-control custom-checkbox" style="cursor: pointer;">
                            <input type="checkbox" class="custom-control-input" id="terms" name="terms" required style="cursor: pointer;">
                            <label class="custom-control-label" for="terms" style="cursor: pointer;">I agree to the <a href="#"
                                    data-toggle="modal" data-target="#termsModal">Terms and Conditions</a></label>
                            <span class="error-message" id="terms_error" style="color:red;"></span>
                        </div>
                    </div>
                    
                    <button type="submit" id="submitBtn" class="btn btn-primary btn-block">Register</button>
                </form>

                



            </div>

            <div class="az-signup-footer" style="text-align: center;">
                <p>Already have an account? <a href="../frontend/sign_in.php">Sign In</a></p>
            </div>
        </div>

        <!-- Terms and Conditions Modal -->
        <div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h6>1. Acceptance of Terms</h6>
                        <p>By accessing and using this land selling platform, you accept and agree to be bound by the
                            terms and conditions outlined here.</p>

                        <h6>2. User Registration</h6>
                        <p>Users must provide accurate and complete information during registration. Property sellers
                            must verify their identity and ownership rights before listing properties.</p>

                        <h6>3. Property Listings</h6>
                        <p>All land property listings must include accurate details about location, size, zoning, title
                            status, and pricing. Supporting documents like titles and permits must be valid and current.
                        </p>

                        <h6>4. Transaction Guidelines</h6>
                        <p>All land transactions must comply with local real estate laws and regulations. We recommend
                            using licensed real estate professionals and legal counsel for transactions.</p>

                        <h6>5. Prohibited Activities</h6>
                        <p>Users shall not list properties they don't have rights to sell, misrepresent land details, or
                            engage in fraudulent pricing practices. Violation will result in account termination.</p>

                        <h6>6. Liability</h6>
                        <p>The platform serves as a listing service only and is not responsible for verifying property
                            details or mediating disputes between buyers and sellers.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content-popup">
                    <div class="modal-body text-center">
                        <!-- Checkmark Icon with Animation -->
                        <div class="checkmark-wrapper">
                            <i class="fas fa-check-circle checkmark-icon"></i>
                        </div>
                        <p class="modal-message">Account successfully created. A verification email has been sent to
                            your
                            email address.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-primary" id="redirectToSignIn">Go to Landing page</button>
                    </div>
                </div>
            </div>
        </div>


        <script src="../assets/lib/jquery/jquery.min.js"></script>
        <script src="../assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="../assets/lib/ionicons/ionicons.js"></script>
        <script src="../assets/js/azia.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const signUpButton = document.getElementById("signUpButton");
                const registrationButton = document.getElementById("registrationButton");
                const signUpForm = document.getElementById("signUpForm");
                const registrationForm = document.getElementById("registrationForm");

                signUpButton.addEventListener("click", function () {
                    signUpForm.style.display = "block";
                    registrationForm.style.display = "none";

                    signUpButton.classList.add("active-btn");
                    registrationButton.classList.remove("active-btn");
                });

                registrationButton.addEventListener("click", function () {
                    signUpForm.style.display = "none";
                    registrationForm.style.display = "block";

                    registrationButton.classList.add("active-btn");
                    signUpButton.classList.remove("active-btn");
                });
            });
        </script>

        <script>
            $(document).ready(function () {
                $('#signUpForm').on('submit', function (e) {
                    e.preventDefault();

                    // Clear previous errors
                    $('.error-message').text('');

                    // Show the loading spinner and hide the button text
                    $('#buttonText').hide();
                    $('#loadingSpinner').show();

                    // Get values of the inputs
                    var firstName = $('input[name="first_name"]').val().trim();
                    var lastName = $('input[name="last_name"]').val().trim();
                    var location = $('input[name="location"]').val().trim();
                    var email = $('input[name="email"]').val().trim();
                    var password = $('input[name="password"]').val().trim();
                    var confirmPassword = $('input[name="confirm_password"]').val().trim();

                    // Regular expression to check for numbers or special characters
                    var namePattern = /^[A-Za-z\s]+$/;
                    var locationPattern = /^[A-Za-z0-9\s,.-]+$/;
                    var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                    var passwordMinLength = 6; // Minimum password length

                    // Validate First Name
                    if (!firstName) {
                        $('#first_name_error').text('First name is required.');
                    } else if (!namePattern.test(firstName)) {
                        $('#first_name_error').text('First name should only contain letters and spaces.');
                    }

                    // Validate Last Name
                    if (!lastName) {
                        $('#last_name_error').text('Last name is required.');
                    } else if (!namePattern.test(lastName)) {
                        $('#last_name_error').text('Last name should only contain letters and spaces.');
                    }

                    // Validate location
                    if (!location) {
                        $('#location_error').text('Location is required.');
                    } else if (!locationPattern.test(location)) {
                        $('#location_error').text('Please enter your location.');
                    }

                    // Validate Email
                    if (!email) {
                        $('#email_error').text('Email is required.');
                    } else if (!emailPattern.test(email)) {
                        $('#email_error').text('Please enter a valid email address.');
                    }

                    // Validate Password
                    if (!password) {
                        $('#password_error').text('Password is required.');
                    } else if (password.length < passwordMinLength) {
                        $('#password_error').text('Password must be at least 6 characters.');
                    }

                    // Validate Confirm Password
                    if (!confirmPassword) {
                        $('#confirm_password_error').text('Please confirm your password.');
                    } else if (password !== confirmPassword) {
                        $('#confirm_password_error').text('Passwords do not match.');
                    }

                    // If there are validation errors, stop the form submission
                    if ($('#first_name_error').text() || $('#last_name_error').text() || $('#location_error').text() || $('#email_error').text() || $('#password_error').text() || $('#confirm_password_error').text()) {
                        // Hide loading spinner and show button text
                        $('#buttonText').show();
                        $('#loadingSpinner').hide();
                        return false;  // Prevent form submission
                    }

                    // Proceed with form submission via AJAX if no validation errors
                    var formData = $(this).serialize();

                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function (response) {
                            // Hide the loading spinner and show button text
                            $('#buttonText').show();
                            $('#loadingSpinner').hide();

                            if (response.success) {
                                // Show success message with SweetAlert2
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Account successfully created. A verification email has been sent to your email address.',
                                    icon: 'success',
                                    confirmButtonText: 'Redirecting to Gmail'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Redirect to Gmail after clicking "Ok"
                                        window.location.href = 'https://mail.google.com'; // Redirect to Gmail
                                    }
                                });
                            } else {
                                // Display validation errors using SweetAlert2
                                let errorMessage = 'There were errors with your submission:\n';
                                if (response.errors.first_name) {
                                    errorMessage += 'First name: ' + response.errors.first_name + '\n';
                                }
                                if (response.errors.last_name) {
                                    errorMessage += 'Last name: ' + response.errors.last_name + '\n';

                                }
                                if (response.errors.location) {
                                    errorMessage += 'Location: ' + response.errors.location + '\n';

                                }
                                if (response.errors.email) {
                                    errorMessage += 'Email: ' + response.errors.email + '\n';
                                }
                                if (response.errors.password) {
                                    errorMessage += 'Password: ' + response.errors.password + '\n';
                                }
                                if (response.errors.confirm_password) {
                                    errorMessage += 'Confirm password: ' + response.errors.confirm_password + '\n';
                                }

                                Swal.fire({
                                    title: 'Error!',
                                    text: errorMessage,
                                    icon: 'error',
                                    confirmButtonText: 'Try Again'
                                });
                            }
                        },
                        error: function () {
                            // Hide loading spinner and show button text if there's an error
                            $('#buttonText').show();
                            $('#loadingSpinner').hide();
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, please try again.',
                                icon: 'error',
                                confirmButtonText: 'Close'
                            });
                        }
                    });
                });
            });
        </script>

<script>
    $('#registrationForm').on('submit', function (e) {
        e.preventDefault();

        // Clear previous errors only for this form
        $('#registrationForm .error-message').text('');

        // Disable button and show loading state
        $('#registrationForm #submitBtn').prop('disabled', true).text('Registering...');

        // Get input values from registrationForm only
        var firstName = $('#registrationForm input[name="first_name"]').val().trim();
        var lastName = $('#registrationForm input[name="last_name"]').val().trim();
        var email = $('#registrationForm input[name="email"]').val().trim();
        var password = $('#registrationForm input[name="password"]').val().trim();
        var confirmPassword = $('#registrationForm input[name="confirm_password"]').val().trim();
        var mobile = $('#registrationForm input[name="mobile"]').val().trim();
        var location = $('#registrationForm input[name="location"]').val().trim();
        var primaryIdType = $('#registrationForm select[name="primary_id_type"]').val();
        var primaryIdNumber = $('#registrationForm input[name="primary_id_number"]').val().trim();
        var secondaryIdType = $('#registrationForm select[name="secondary_id_type"]').val();
        var secondaryIdNumber = $('#registrationForm input[name="secondary_id_number"]').val().trim();

        // Validation
        var mobilePattern = /^[0-9]{10,15}$/;
        var hasError = false;

        function showError(field, message) {
            $('#registrationForm #' + field + '_error').text(message);
            hasError = true;
        }

        if (!firstName) showError('first_name', 'First name is required.');
        if (!lastName) showError('last_name', 'Last name is required.');
        if (!email) showError('email', 'Email is required.');
        if (!password) showError('password', 'Password is required.');
        if (password !== confirmPassword) showError('confirm_password', 'Passwords do not match.');
        if (!mobile.match(mobilePattern)) showError('mobile', 'Invalid mobile number.');
        if (!location) showError('location', 'Location is required.');
        if (!primaryIdType) showError('primary_id_type', 'Primary ID type is required.');
        if (!primaryIdNumber) showError('primary_id_number', 'Primary ID number is required.');
        if (!secondaryIdType) showError('secondary_id_type', 'Secondary ID type is required.');
        if (!secondaryIdNumber) showError('secondary_id_number', 'Secondary ID number is required.');

        if (hasError) {
            $('#registrationForm #submitBtn').prop('disabled', false).text('Register');
            return false;
        }

        // Send AJAX request for registration
        $.ajax({
            url: '../backend/register_agent.php', 
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                $('#registrationForm #submitBtn').prop('disabled', false).text('Register');
                if (response.success) {
                    Swal.fire('Info', 'Registered. Check your email and please wait for admin verification.', 'info');
                } else {
                    console.error(response); // Log response to browser console
                    Swal.fire('Error!', response.message || 'Something went wrong!', 'error');
                }
            },
            error: function (xhr, status, error) {
                $('#registrationForm #submitBtn').prop('disabled', false).text('Register');
                console.error(xhr.responseText); // Log full server response
                Swal.fire('Error!', 'Something went wrong. Check the console for details.', 'error');
            }
        });
    });


</script>


</body>

</html>