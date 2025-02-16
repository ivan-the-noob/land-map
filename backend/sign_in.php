<?php

require '../db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string(trim($_POST['user_email']));
    $password = $conn->real_escape_string(trim($_POST['password']));

    // Validate email
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email address.";
    }

    // Validate password
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    }

    // If no errors, proceed with authentication
    if (empty($errors)) {
        $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Check if the account is disabled
            if ($user['disable_status'] >= 3) {
                $errors['general'] = "Your account has been disabled.";
                echo json_encode(['success' => false, 'errors' => $errors]);
                exit; // Stop execution
            }

            // Verify password
            if (password_verify($password, $user['password'])) {
                if ($user['is_verified'] == 1) {
                    if ($user['admin_verify'] == 1) {
                        // Successful login
                        session_start();
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['user_name'] = $user['fname'] . ' ' . $user['lname'];
                        $_SESSION['role_type'] = $user['role_type'];  // Store the role type in the session
            
                        // Check the role type and redirect
                        if ($user['role_type'] == 'admin') {
                            echo json_encode(['success' => true, 'redirect' => '../frontend/frontend_users/admin_page.php']);
                        } elseif ($user['role_type'] == 'agent') {
                            echo json_encode(['success' => true, 'redirect' => '../frontend/frontend_users/agent_page.php']);
                        } elseif ($user['role_type'] == 'user') {
                            echo json_encode(['success' => true, 'redirect' => '../frontend/frontend_users/user_listing.php']);
                        } else {
                            echo json_encode(['success' => false, 'errors' => ['general' => 'Invalid role type.']]);
                        }
                    } else {
                        // Admin verification pending
                        $errors['general'] = "Please wait for admin to verify your account.";
                        echo json_encode(['success' => false, 'errors' => $errors]);
                    }
                } else {
                    // Account not verified
                    $errors['general'] = "Please verify your email address before signing in.";
                    echo json_encode(['success' => false, 'errors' => $errors]);
                }
            } else {
                // Invalid password
                $errors['general'] = "Incorrect email or password.";
                echo json_encode(['success' => false, 'errors' => $errors]);
            }
            
        } else {
            // User not found
            $errors['general'] = "No account found with this email.";
            echo json_encode(['success' => false, 'errors' => $errors]);
        }
    } else {
        // Return validation errors
        echo json_encode(['success' => false, 'errors' => $errors]);
    }
}

$conn->close();
