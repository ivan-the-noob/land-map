<?php

require '../db.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$role_type = 'user';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $conn->real_escape_string(trim($_POST['first_name']));
    $last_name = $conn->real_escape_string(trim($_POST['last_name']));
    $location = $conn->real_escape_string(trim($_POST['location']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $conn->real_escape_string(trim($_POST['password']));
    $confirm_password = $conn->real_escape_string(trim($_POST['confirm_password']));

    // validation
    $name_check_query = "SELECT * FROM users WHERE fname = '$first_name' AND lname = '$last_name' LIMIT 1";
    $result = $conn->query($name_check_query);

    if ($result->num_rows > 0) {
        die(json_encode(["success" => false, "errors" => ["first_name" => "This name combination already exists."]]));
    }

    if (empty($first_name)) {
        $errors['first_name'] = "First name is required.";
    } elseif (!preg_match("/^[A-Za-z\s]+$/", $first_name)) {
        $errors['first_name'] = "First name should only contain letters and spaces.";
    }
    
    if (empty($last_name)) {
        $errors['last_name'] = "Last name is required.";
    } elseif (!preg_match("/^[A-Za-z\s]+$/", $last_name)) {
        $errors['last_name'] = "Last name should only contain letters and spaces.";
    }

    if (empty($location)) {
        $errors['location'] = "Location is required.";
    } elseif (!preg_match("/^[A-Za-z\s,]+$/", $location)) {
        $errors['location'] = "Location should only contain letters, spaces and commas.";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email address.";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters long.";
    } elseif (!preg_match("/[A-Z]/", $password)) {
        $errors['password'] = "Password must contain at least one uppercase letter.";
    } elseif (!preg_match("/[a-z]/", $password)) {
        $errors['password'] = "Password must contain at least one lowercase letter.";
    } elseif (!preg_match("/[0-9]/", $password)) {
        $errors['password'] = "Password must contain at least one number.";
    } elseif (!preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $password)) {
        $errors['password'] = "Password must contain at least one special character (!@#$%^&*(),.?\":{}|<>).";
    }

    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Confirm password is required.";
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    // If Email Already Exists
    if (empty($errors)) {
        $email_check_query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = $conn->query($email_check_query);

        if ($result->num_rows > 0) {
            $errors['email'] = "Email already exists.";
        }
    }

    if (empty($errors)) {
        // Hash Password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Generate Unique Verification Token
        $verification_code = bin2hex(random_bytes(16)); // 32-character random string

        // Insert into database
        $insert_query = "INSERT INTO users (role_type, fname, lname, mobile, location, email, password, verification_code, is_verified) 
                         VALUES ('$role_type', '$first_name', '$last_name', mobile,  '$location','$email', '$hashed_password', '$verification_code', 0)";

        if ($conn->query($insert_query) === TRUE) {
            // Send the verification email using PHPMailer
            $mail = new PHPMailer;

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'ioproperty041@gmail.com'; // Your email address
            $mail->Password = 'boumekrrtnkbbxgs'; // Your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587; // SMTP port

            // Set the sender and recipient
            $mail->setFrom('ioproperty041@gmail.com', 'Property IO');
            $mail->addAddress($email, "$first_name $last_name");

            // Set email content
            $mail->isHTML(true);
            $mail->Subject = 'Please verify your email address';
            $mail->Body = "
                <div style='font-family: Roboto, Arial, sans-serif; line-height: 1.6; color: #333; font-size: 16px;'>
                    <h2 style='color: #2C3E50; font-size: 20px;'>Hi $first_name,</h2>
                    <p style='font-size: 16px;'>Thank you for registering in landMap! Please click the button below to verify your email address:</p>
                    <div style='margin: 20px 0;'>
                        <a href='http://landmap.shop/backend/verify_email.php?code=$verification_code' style='display: inline-block; padding: 12px 20px; font-size: 16px; font-weight: bold; color: #fff; text-decoration: none; background-color: #28a745; border-radius: 5px;'>Verify Email</a>
                    </div>
                    <p style='font-size: 16px;'>If you did not register, please ignore this email.</p>
                    <br>
                    <p style='color: #999; font-size: 14px;'>This is an automated message. Please do not reply to this email.</p>
                </div>";
            // Send the email and check for success
            if ($mail->send()) {
                // Return success message with email verification info
                echo json_encode(['success' => true, 'message' => "Account successfully created. A verification email has been sent to your email address."]);
            } else {
                // In case of failure, return error response
                echo json_encode(['success' => false, 'errors' => ['general' => "Error sending verification email. Please try again later."]]);
            }
        } else {
            $errors['general'] = "Error: " . $conn->error;
            echo json_encode(['success' => false, 'errors' => $errors]);
        }
    } else {
        echo json_encode(['success' => false, 'errors' => $errors]);
    }
}

$conn->close();
