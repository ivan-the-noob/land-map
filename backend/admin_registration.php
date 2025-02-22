<?php
require '../db.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize variables
$role_type = 'admin'; // Set role to admin
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input data
    $first_name = $conn->real_escape_string(trim($_POST['fname']));
    $last_name = $conn->real_escape_string(trim($_POST['lname']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $conn->real_escape_string(trim($_POST['password']));

    // Validate first and last name
    if (empty($first_name)) {
        $errors['first_name'] = "First name is required.";
    }
    if (empty($last_name)) {
        $errors['last_name'] = "Last name is required.";
    }

    // Validate email
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email address.";
    }

    // Validate password
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters long.";
    }

    // Check if email already exists
    if (empty($errors)) {
        $email_check_query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = $conn->query($email_check_query);

        if ($result->num_rows > 0) {
            $errors['email'] = "Email already exists.";
        }
    }

    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Generate a verification token (if needed)
        $verification_code = bin2hex(random_bytes(16));

        // Insert into database
        $insert_query = "INSERT INTO users (role_type, fname, lname, email, password, verification_code, is_verified) 
                         VALUES ('$role_type', '$first_name', '$last_name', '$email', '$hashed_password', '$verification_code', 0)";

        if ($conn->query($insert_query) === TRUE) {
            // Send verification email
            $mail = new PHPMailer;

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'ioproperty041@gmail.com'; // Your email address
            $mail->Password = 'boumekrrtnkbbxgs';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('your-email@gmail.com', 'YourApp');
            $mail->addAddress($email, "$first_name $last_name");

            $mail->isHTML(true);
            $mail->Subject = 'Please verify your email address';
            $mail->Body = "Hi $first_name, please verify your email by clicking <a href='http://landmap.shop/verify?code=$verification_code'>here</a>.";

            if ($mail->send()) {
                // Successful registration and email sent
                echo "Admin registered successfully. Verification email sent.";
            } else {
                // Failed to send email
                echo "Error sending verification email.";
            }
        } else {
            // SQL insert failed
            echo "Error: " . $conn->error;
        }

    } else {
        foreach ($errors as $field => $error) {
            echo "$field: $error\n";
        }
    }
}

$conn->close();
?>
