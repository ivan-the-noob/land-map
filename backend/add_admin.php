<?php
session_start();
require '../db.php'; // Database connection
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the user is logged in and is a super admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access!']);
    exit();
}

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);  // role will be 'full_access' or 'normal_access'

    // Validate input fields
    if (empty($fname) || empty($lname) || empty($email) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required!']);
        exit();
    }

    // Check if the email already exists
    $checkQuery = "SELECT id FROM admin WHERE email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists!']);
        exit();
    }
    $stmt->close();

    // Generate a unique verification code
    $verificationCode = bin2hex(random_bytes(16)); // 32-character random string

    // Insert the new user into the database with verification code and 'is_verified' set to 0
    $isVerified = 0;  // Set is_verified to 0 (unverified)
    
    $insertQuery = "INSERT INTO admin (fname, lname, email, role_type, verification_code, is_verified) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sssssi", $fname, $lname, $email, $role, $verificationCode, $isVerified);

    if ($stmt->execute()) {
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
        $mail->addAddress($email, $fname . ' ' . $lname);  // Combine first and last name

        // Set email content
        $mail->isHTML(true);
        $mail->Subject = 'Please verify your email address';
        $mail->Body = "
            <div style='font-family: Roboto, Arial, sans-serif; line-height: 1.6; color: #333; font-size: 16px;'>
                <h2 style='color: #2C3E50; font-size: 20px;'>Hi $fname $lname,</h2>
                <p style='font-size: 16px;'>Thank you for registering! Please click the button below to verify your email address:</p>
                <div style='margin: 20px 0;'>
                    <!-- Direct link to verification page -->
                    <a href='http://localhost:2000/backend/admin_verify_email.php?code=$verificationCode' 
                       style='display: inline-block; padding: 12px 20px; font-size: 16px; font-weight: bold; color: #fff; text-decoration: none; background-color: #28a745; border-radius: 5px;'>Verify Email</a>
                </div>
                <p style='font-size: 16px;'>If you did not register, please ignore this email.</p>
                <br>
                <p style='color: #999; font-size: 14px;'>This is an automated message. Please do not reply to this email.</p>
            </div>
        ";

        // Send the email and check for success
        if ($mail->send()) {
            // Return success message with email verification info
            echo json_encode(['success' => true, 'message' => "User added successfully. A verification email has been sent to the user's email address."]);
        } else {
            // In case of failure, return error response
            echo json_encode(['success' => false, 'message' => 'Error sending verification email. Please try again later.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding user!']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request!']);
}