<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Land Map | Sign In</title>
    <link rel="icon" href="../assets/images/logo.png" type="image/x-icon">

    <!-- LINKS -->
    <link href="../assets/lib/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/lib/ionicons/css/ionicons-core.min.css" rel="stylesheet">
    <link href="../assets/lib/typicons.font/typicons.css" rel="stylesheet">

    <script type="module" src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app.js"></script>
    <script type="module" src="https://www.gstatic.com/firebasejs/9.22.2/firebase-auth.js"></script>
    <script type="module" src="https://www.gstatic.com/firebasejs/9.22.2/firebase-database.js"></script>

    <!-- azia CSS -->
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

    <div class="az-signin-wrapper">
        <a href="../index.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <div class="az-card-signin">
            <a href="../index.php">
                <img src="../assets/images/logo.png" alt="LandMap Logo" class="logo-img"
                    style="width: 5rem; margin-bottom: 1rem;">
            </a>
            <div class="az-signin-header">
                <h2>Welcome to LandMap</h2>
                <p>Your trusted platform for land transactions</p>
                <form method="POST" id="signin-form" action="../backend/sign_in.php">
                    <div id="attempts-left" class="text-danger" style="display: none;">
                        <p>You have made too many login attempts. Please wait <span id="timer">60</span>s to log in.</p>
                    </div>
                    <div id="general-error" class="text-danger"
                        style="display: none; font-size: 14px; margin-bottom: 10px;">
                        <!-- This will display error messages -->
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input name="user_email" type="text" class="form-control" placeholder="Enter your email">
                    </div><!-- form-group -->
                    <div class="form-group">
                        <label>Password</label>
                        <input name="password" type="password" class="form-control" placeholder="Enter your password">
                    </div><!-- form-group -->
                    <button type="submit" class="btn btn-az-primary btn-block" style="margin-bottom: 20px;"
                        id="submit-btn">
                        <span id="button-text">Login</span>
                        <div id="loading-spinner" class="spinner-border spinner-border-sm text-light"
                            style="display: none;" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </button>
                </form>
            </div><!-- az-signin-header -->
            <div class="az-signin-footer" style="text-align: center;">
                <p><a href="../frontend/forgot_password.php" style="margin: 20px 0;">Forgot password?</a></p>
                <p>Don't have an account? <a href="../frontend/sign_up.php">Sign up</a></p>
            </div><!-- az-signin-footer -->
        </div><!-- az-card-signin -->
    </div><!-- az-signin-wrapper -->

    <script src="../assets/lib/jquery/jquery.min.js"></script>
    <script src="../assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/lib/ionicons/ionicons.js"></script>
    <!-- Add SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="../assets/js/azia.js"></script>

    <script>
        $(document).ready(function () {
    var maxAttempts = 3; // Max number of login attempts
    var attemptsLeft = maxAttempts;
    var lockoutTime = 60; // Lockout time in seconds (5 minutes)
    var lockoutTimerInterval = null;

    // Retrieve lockout end time from localStorage if it exists
    var lockoutEndTime = localStorage.getItem('lockoutEndTime');
    var currentTime = Date.now();

    if (lockoutEndTime && currentTime < lockoutEndTime) {
        // Lockout is still active
        lockoutTime = Math.ceil((lockoutEndTime - currentTime) / 1000); // Calculate remaining lockout time
        if (lockoutTime > 0) {
            $('#attempts-left').show();
            $('#timer').text(lockoutTime); // Show the remaining time immediately
            startLockoutTimer(); // Start the countdown timer
        }
    } else {
        // Reset lockout if expired or not set
        localStorage.removeItem('lockoutEndTime');
    }

    $('#signin-form').on('submit', function (event) {
        event.preventDefault();

        // Clear previous error messages
        $('#general-error').hide().text('');
        $('#attempts-left').hide();

        var email = $("input[name='user_email']").val().trim();
        var password = $("input[name='password']").val().trim();
        var isValid = true;
        var errorMessage = '';

        // Check email and password validation
        if (!email && !password) {
            errorMessage = 'Email and password are required.';
            isValid = false;
        } else if (!email || !password) {
            errorMessage = 'Invalid email or password.';
            isValid = false;
        }

        // If the user is locked out, prevent further submission
        if (attemptsLeft <= 0) {
            $('#attempts-left').show();
            startLockoutTimer();
            return; // Stop form submission
        }

        // Display error message if validation fails
        if (!isValid) {
            $('#general-error').text(errorMessage).show();
            return; // Stop form submission
        }

        // Show spinner and hide button text
        $('#button-text').hide();
        $('#loading-spinner').show();

        // If valid, proceed with AJAX request to backend
        $.ajax({
            url: "../backend/sign_in.php",
            type: "POST",
            data: { user_email: email, password: password },
            success: function (response) {
                var result = JSON.parse(response);

                if (result.success) {
                    // If login is successful, show SweetAlert popup
                    Swal.fire({
                        title: 'Welcome ' + result.user_name, // Using the user's name here
                        text: 'You successfully logged in!',
                        icon: 'success',
                        confirmButtonText: 'Proceed'
                    }).then(() => {
                        // Redirect after SweetAlert is confirmed
                        window.location.href = result.redirect;
                    });
                } else {
                    // If login fails, decrease the attempts count
                    attemptsLeft--;

                    // Show the general error message only if the attempts are not exhausted
                    if (attemptsLeft > 0) {
                        var backendError = result.errors && result.errors.general ? result.errors.general : 'Invalid email or password.';
                        $('#general-error').text(backendError).show();
                    }

                    // If attempts are exhausted, lock the user out and start the countdown timer
                    if (attemptsLeft <= 0) {
                        $('#attempts-left').show();
                        startLockoutTimer();
                    }
                }
            },
            error: function () {
                // Handle unexpected server error
                $('#general-error').text('An unexpected error occurred. Please try again later.').show();
            },
            complete: function () {
                // Hide spinner and restore button text after request completes
                $('#loading-spinner').hide();
                $('#button-text').show();
            }
        });
    });

    // Function to start the lockout countdown timer
    function startLockoutTimer() {
        // Disable the form and button during the lockout period
        $('#signin-form input, #signin-form button').prop('disabled', true);

        // Countdown timer function
        lockoutTimerInterval = setInterval(function () {
            lockoutTime--;
            $('#timer').text(lockoutTime);

            if (lockoutTime <= 0) {
                // Lockout time is over, re-enable the form and reset the attempts
                clearInterval(lockoutTimerInterval);
                $('#signin-form input, #signin-form button').prop('disabled', false);
                attemptsLeft = maxAttempts; // Reset attempts
                $('#attempts-left').hide();
                localStorage.removeItem('lockoutEndTime'); // Remove the lockout end time from localStorage
            } else {
                // Store the lockout end time in localStorage
                localStorage.setItem('lockoutEndTime', Date.now() + lockoutTime * 1000);
            }
        }, 1000); // Update the timer every second
    }
});

    </script>

</body>

</html>
