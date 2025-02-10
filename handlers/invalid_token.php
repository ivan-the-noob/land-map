<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invalid Token Notification</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
    <div class="notification-container text-center">
        <h3>Invalid</h3>
        <i class="fa fa-exclamation-circle animated-warning"></i>
        <p class="r3">Invalid verification link or token expired.</p>
        <div class="text-center mb-3">
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
