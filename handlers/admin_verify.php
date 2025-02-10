<?php
// Extract the user_id from the URL
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Password</title>
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.0/dist/sweetalert2.min.css">
    <style>
        /* Custom styles for SweetAlert modal and input fields */
        .swal2-input {
            width: 80% !important;
            font-size: 16px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.0/dist/sweetalert2.min.js"></script>

<script>
    // Triggering SweetAlert notification for email verification success
    Swal.fire({
        title: 'Email is Verified!',
        text: 'Thank you for signing up. You can now create a password to secure your account.',
        icon: 'success',
        confirmButtonText: 'Create Password',
        confirmButtonColor: '#28a745',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Show SweetAlert for password creation with user_id included
            Swal.fire({
                title: 'Create Your Password',
                html: `
                    <form id="createPasswordForm">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" class="swal2-input" placeholder="Enter your password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirm Password</label>
                            <input type="password" id="confirmPassword" class="swal2-input" placeholder="Confirm your password" required>
                        </div>
                    </form>
                `,
                confirmButtonText: 'Create Password',
                confirmButtonColor: '#28a745',
                preConfirm: () => {
                    const password = document.getElementById('password').value;
                    const confirmPassword = document.getElementById('confirmPassword').value;

                    if (password !== confirmPassword) {
                        Swal.showValidationMessage('Passwords do not match!');
                        return false;
                    }

                    // Return password and user_id for AJAX request
                    return {
                        password: password,
                        confirmPassword: confirmPassword,
                        user_id: "<?php echo $user_id; ?>" // Inject user_id into the form
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const passwordData = result.value;

                    // Make an AJAX request to update the password
                    fetch('../backend/admin_createPass.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(passwordData),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Password Created!',
                                text: 'Your password has been successfully created. You can now log in.',
                                icon: 'success',
                                confirmButtonText: 'Go to Login',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                window.location.href = "../frontend/super_admin_login.php";
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.error,
                                icon: 'error',
                                confirmButtonText: 'Try Again',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'Try Again',
                            confirmButtonColor: '#dc3545'
                        });
                    });
                }
            });
        }
    });
</script>

</body>
</html>