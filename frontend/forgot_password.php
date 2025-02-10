<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Land Map | Forgot Password</title>
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

</head>

<body class="az-body">

<div class="az-signin-wrapper">
    <div class="az-card-signin">
        <a href="../index.php">
            <img src="../assets/images/logo.png" alt="LandMap Logo" class="logo-img" style="width: 5rem; margin-bottom: 1rem;">
        </a>
        <div class="az-signin-header">
            <h2>Forgot Password?</h2>
            <p>Enter your email to reset your password</p>
            <form method="POST" id="forgot-password-form" action="../backend/forgot_password.php">
                <div id="general-error" class="text-danger" style="display: none; font-size: 14px; margin-bottom: 10px;">
                    <!-- This will display error messages -->
                </div>
                <div id="success-message" class="text-success" style="display: none; font-size: 14px; margin-bottom: 10px;">
                    <!-- This will display success messages -->
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input name="user_email" type="email" class="form-control" placeholder="Enter your email">
                </div><!-- form-group -->
                <button type="submit" class="btn btn-az-primary btn-block" style="margin-bottom: 20px;">
                    <span id="buttonText">Reset Password</span>
                    <span id="loadingSpinner" style="display: none;" class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true"></span>
                </button>

            </form>
        </div><!-- az-signin-header -->
        <div class="az-signin-footer" style="text-align: center;">
            <p>Remember your password? <a href="../frontend/sign_in.php">Sign In</a></p>
            <p>Don't have an account? <a href="../frontend/sign_up.php">Sign up</a></p>
        </div><!-- az-signin-footer -->
    </div><!-- az-card-signin -->
</div><!-- az-signin-wrapper -->

    <script src="../assets/lib/jquery/jquery.min.js"></script>
    <script src="../assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/lib/ionicons/ionicons.js"></script>

    <script src="../assets/js/azia.js"></script>

    <script>
        $(document).ready(function () {
            $('#forgot-password-form').on('submit', function (event) {
                event.preventDefault();

                // Clear previous messages
                $('#general-error').hide().text('');
                $('#success-message').hide().text('');

                // Show loading spinner
                $('#buttonText').hide();
                $('#loadingSpinner').show();

                var email = $("input[name='user_email']").val().trim();
                var isValid = true;
                var errorMessage = '';

                // Check email validation
                if (!email) {
                    errorMessage = 'Email is required.';
                    isValid = false;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    errorMessage = 'Please enter a valid email address.';
                    isValid = false;
                }

                // Display error message if validation fails
                if (!isValid) {
                    $('#general-error').text(errorMessage).show();
                    $('#buttonText').show();
                    $('#loadingSpinner').hide();
                    return;
                }

                // If valid, proceed with AJAX request to backend
                $.ajax({
                    url: "../backend/forgot_password.php",
                    type: "POST",
                    data: { user_email: email },
                    success: function (response) {
                        var result = JSON.parse(response);

                        if (result.success) {
                            $('#success-message').text('Password reset instructions have been sent to your email.').show();
                            $("input[name='user_email']").val('');
                        } else {
                            $('#general-error').text(result.message || 'An error occurred. Please try again.').show();
                        }
                    },
                    error: function () {
                        $('#general-error').text('An unexpected error occurred. Please try again later.').show();
                    },
                    complete: function() {
                        // Hide loading spinner and show button text
                        $('#buttonText').show();
                        $('#loadingSpinner').hide();
                    }
                });
            });
        });
    </script>

</body>

</html>