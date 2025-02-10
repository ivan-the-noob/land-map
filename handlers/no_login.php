<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Required</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        html, body {
            height: 100%;
        }

        body {
            display: grid;
            place-items: center;
            width: 100%;
            height: 100vh;
            background-image: linear-gradient(to bottom right, rgb(255, 50, 50), rgb(200, 20, 20));
            text-align: center;
            font-size: 16px;
        }

        .notification-container {
            border-radius: 1rem;
            padding: 2rem;
            background-color: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        .animated-warning {
            color: #dc3545; /* Red color */
            font-size: 90px;
            padding: 30px 0;
            animation: shake 0.5s infinite alternate;
        }

        @keyframes shake {
            from {
                transform: translateX(0);
            }
            to {
                transform: translateX(-10px);
            }
        }

        .b1 {
            background-color: #dc3545;
            box-shadow: 0px 4px #c82333;
            font-size: 17px;
        }

        .r3 {
            color: #c1c1c1;
            font-weight: 500;
        }

        a, a:hover {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <script>
        // Trigger SweetAlert on page load
        window.onload = function() {
            Swal.fire({
                icon: 'error',
                title: 'Login Required',
                text: 'You need to log in to access this page.',
                confirmButtonText: 'Log in',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                allowOutsideClick: false // Prevent closing by clicking outside
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to login page (adjust URL as needed)
                    window.location.href = '../frontend/super_admin_login.php'; 
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // Redirect to the landing page (adjust URL as needed)
                    window.location.href = '../../'; // Change this to your landing page URL
                }
            });
        }
    </script>


</body>
</html>