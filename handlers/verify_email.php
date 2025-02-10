<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Example</title>
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
            background-image: linear-gradient(to bottom right, rgb(10, 153, 209), rgb(5, 76, 122));
            text-align: center;
            font-size: 16px;
        }

        .notification-container {
            border-radius: 1rem;
            padding: 2rem;
            background-color: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        .animated-check {
            color: #28a745; /* Green color */
            font-size: 90px;
            padding: 30px 0;
            animation: bounce 0.5s infinite alternate;
        }

        @keyframes bounce {
            from {
                transform: translateY(0);
            }
            to {
                transform: translateY(-10px);
            }
        }

        .b1 {
            background-color: #2b84be;
            box-shadow: 0px 4px #337095;
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
        <h3>Email is Verified</h3>
        <i class="fa fa-check-circle animated-check"></i>
        <p class="r3">Thank you for signing up in our website, you can now sign in</p>
        <div class="text-center mb-3">
            <a class="btn btn-primary w-50 rounded-pill b1" href="../frontend/sign_in.php">
               Sign in
            </a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>