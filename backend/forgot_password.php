<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
// Enable error logging to a file
ini_set('log_errors', 1);
ini_set('error_log', '../logs/php-error.log');

require_once '../config/database.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure database connection is established
try {
    if (!isset($pdo)) {
        error_log('Attempting database connection...');
        // Assuming database.php defines these variables
        if (!isset($host) || !isset($dbname) || !isset($username) || !isset($password)) {
            error_log('Database configuration variables are not properly set');
            throw new Exception('Database configuration error');
        }
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        error_log('Database connection successful');
    }
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
} catch (Exception $e) {
    error_log('Configuration error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Configuration error occurred']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = array('success' => false, 'message' => '');
    
    if (isset($_POST['new_password']) && isset($_POST['token'])) {
        // Handle password reset submission
        try {
            $new_password = $_POST['new_password'];
            $token = $_POST['token'];

            // Verify token is valid and not expired
            $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW() LIMIT 1");
            $stmt->execute([$token]);
            $user = $stmt->fetch();

            if (!$user) {
                $response['message'] = 'Invalid or expired reset token.';
                echo json_encode($response);
                exit;
            }

            // Update password and clear reset token
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
            $stmt->execute([$hashed_password, $user['id']]);

            $response['success'] = true;
            $response['message'] = 'Password has been successfully reset.';

        } catch (Exception $e) {
            error_log('Password reset error: ' . $e->getMessage());
            $response['message'] = 'An error occurred while resetting password.';
        }

        echo json_encode($response);
        exit;
    }
    
    // Handle forgot password request
    $email = isset($_POST['user_email']) ? trim($_POST['user_email']) : '';
    
    if (empty($email)) {
        $response['message'] = 'Email is required.';
        echo json_encode($response);
        exit;
    }

    try {
        // Check if email exists in database
        $stmt = $pdo->prepare("SELECT id, fname FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            $response['message'] = 'No account found with this email address.';
            echo json_encode($response);
            exit;
        }

        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Store reset token in database
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
        $stmt->execute([$token, $expiry, $email]);

        // Create reset password link
        $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/frontend/reset_password.php?token=" . $token;

        // Initialize PHPMailer
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ioproperty041@gmail.com';
        $mail->Password = 'boumekrrtnkbbxgs';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Set sender and recipient
        $mail->setFrom('ioproperty041@gmail.com', 'Property IO');
        $mail->addAddress($email, $user['fname']);

        // Set email content
        $mail->isHTML(true);
        $mail->Subject = "Password Reset Request";
        $mail->Body = "
            <div style='font-family: Roboto, Arial, sans-serif; line-height: 1.6; color: #333; font-size: 16px;'>
                <h2 style='color: #2C3E50; font-size: 20px;'>Hi " . htmlspecialchars($user['fname']) . ",</h2>
                <p style='font-size: 16px;'>You recently requested to reset your password. Click the button below to reset it:</p>
                <div style='margin: 20px 0;'>
                    <a href='$resetLink' style='display: inline-block; padding: 12px 20px; font-size: 16px; font-weight: bold; color: #fff; text-decoration: none; background-color: #28a745; border-radius: 5px;'>Reset Password</a>
                </div>
                <p style='font-size: 16px;'>This link will expire in 1 hour.</p>
                <p style='font-size: 16px;'>If you did not request a password reset, please ignore this email.</p>
                <br>
                <p style='color: #999; font-size: 14px;'>This is an automated message. Please do not reply to this email.</p>
            </div>";

        // Send email
        if($mail->send()) {
            $response['success'] = true;
            $response['message'] = 'Password reset instructions have been sent to your email.';
        } else {
            $response['message'] = 'Failed to send reset email. Please try again.';
        }

    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        $response['message'] = 'Database error occurred. Please try again.';
    } catch (Exception $e) {
        error_log('Email error: ' . $e->getMessage());
        $response['message'] = 'An error occurred sending the email. Please try again.';
    }

    echo json_encode($response);
    exit;
}

// Handle GET request with token
if (isset($_GET['token'])) {
    try {
        $token = $_GET['token'];
        
        // Verify token exists and is not expired
        $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW() LIMIT 1");
        $stmt->execute([$token]);
        
        if (!$stmt->fetch()) {
            header('Location: ../handlers/invalid_reset.php');
            exit;
        }
        
        // Token is valid, redirect to reset password form
        header('Location: ../frontend/reset_password.php?token=' . $token);
        exit;
        
    } catch (Exception $e) {
        error_log('Token verification error: ' . $e->getMessage());
        header('Location: ../handlers/invalid_reset.php');
        exit;
    }
}
?>