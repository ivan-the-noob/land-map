<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


require '../db.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$role_type = 'agent';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $conn->real_escape_string(trim($_POST['first_name']));
    $last_name = $conn->real_escape_string(trim($_POST['last_name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $conn->real_escape_string(trim($_POST['password']));
    $mobile = $conn->real_escape_string(trim($_POST['mobile']));
    $location = $conn->real_escape_string(trim($_POST['location']));
    $primary_id_type = $conn->real_escape_string(trim($_POST['primary_id_type']));
    $primary_id_number = $conn->real_escape_string(trim($_POST['primary_id_number']));
    $secondary_id_type = $conn->real_escape_string(trim($_POST['secondary_id_type']));
    $secondary_id_number = $conn->real_escape_string(trim($_POST['secondary_id_number']));

    // Check for existing name combination
    $name_check_query = "SELECT * FROM users WHERE fname = '$first_name' AND lname = '$last_name' LIMIT 1";
    $result = $conn->query($name_check_query);

    if ($result->num_rows > 0) {
        die(json_encode(["success" => false, "errors" => ["first_name" => "This name combination already exists."]]));
    }

    // Validation
    if (empty($first_name) || !preg_match("/^[A-Za-z\s]+$/", $first_name)) {
        $errors['first_name'] = "Invalid first name.";
    }
    if (empty($last_name) || !preg_match("/^[A-Za-z\s]+$/", $last_name)) {
        $errors['last_name'] = "Invalid last name.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email address.";
    }
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    }
    if (empty($mobile)) {
        $errors['mobile'] = "Mobile number is required.";
    }
    if (empty($location)) {
        $errors['location'] = "Location is required.";
    }

    // Check if email already exists
    if (empty($errors)) {
        $email_check_query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = $conn->query($email_check_query);
        if ($result->num_rows > 0) {
            $errors['email'] = "Email already exists.";
        }
    }

    // File upload handling function
    function uploadFile($file, $destinationFolder) {
        if (!empty($file['name'])) {
            $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
            $uniqueName = uniqid(time() . "_", true) . "." . $fileExt;
            $targetFile = $destinationFolder . $uniqueName;

            // Create directory if not exists
            if (!is_dir($destinationFolder)) {
                mkdir($destinationFolder, 0777, true);
            }

            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                return $uniqueName; // Save only the filename in DB
            }
        }
        return null;
    }

    $uploadDir = '../assets/agents/';
    $uploadProfile = '../assets/profile_images/';

    $profile_image = uploadFile($_FILES['profile_image'], $uploadProfile);
    $primary_id_image = uploadFile($_FILES['primary_id_image'], $uploadDir);
    $secondary_id_image = uploadFile($_FILES['secondary_id_image'], $uploadDir);

    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Generate verification code
        $verification_code = bin2hex(random_bytes(16));

        // Insert into database
        $insert_query = "INSERT INTO users (role_type, fname, lname, email, password, mobile, location, profile, primary_id_type, primary_id_number, primary_id_image, secondary_id_type, secondary_id_number, secondary_id_image, verification_code, is_verified, admin_verify) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0)";

        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sssssssssssssss", $role_type, $first_name, $last_name, $email, $hashed_password, $mobile, $location, $profile_image, $primary_id_type, $primary_id_number, $primary_id_image, $secondary_id_type, $secondary_id_number, $secondary_id_image, $verification_code);

        if ($stmt->execute()) {
            // Send verification email
            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ioproperty041@gmail.com';
            $mail->Password = 'boumekrrtnkbbxgs';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('ioproperty041@gmail.com', 'Property IO');
            $mail->addAddress($email, "$first_name $last_name");

            $mail->isHTML(true);
            $mail->Subject = 'Please verify your email address';
            $base_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/landmap";
            $verification_link = "$base_url/backend/verify_email.php?code=$verification_code";
            
            $mail->Body = "
                <div style='font-family: Roboto, Arial, sans-serif; line-height: 1.6; color: #333; font-size: 16px;'>
                    <h2 style='color: #2C3E50; font-size: 20px;'>Hi $first_name,</h2>
                    <p style='font-size: 16px;'>Thank you for registering! Please click the button below to verify your email address:</p>
                    <div style='margin: 20px 0;'>
                        <a href='$verification_link' style='display: inline-block; padding: 12px 20px; font-size: 16px; font-weight: bold; color: #fff; text-decoration: none; background-color: #28a745; border-radius: 5px;'>Verify Email</a>
                    </div>
                    <p style='font-size: 16px;'>If you did not register, please ignore this email.</p>
                    <br>
                    <p style='color: #999; font-size: 14px;'>This is an automated message. Please do not reply to this email.</p>
                </div>";
            

            if ($mail->send()) {
                echo json_encode(['success' => true, 'message' => "Account successfully created. A verification email has been sent."]);
            } else {
                echo json_encode(['success' => false, 'errors' => ['general' => "Error sending verification email."]]);
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
