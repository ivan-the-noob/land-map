<?php
require '../db.php'; // Database connection

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
        // Use a prepared statement to securely fetch the user data
        $query = "SELECT * FROM admin WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email); // "s" denotes the parameter type (string)
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();  // Fetch user info

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Successful login
                session_start();
                $_SESSION['email'] = $user['email'];  // Store the email in session
                $_SESSION['user_id'] = $user['id'];  // Store the user ID in session
                $_SESSION['username'] = $user['username'];  // Store the username in session
                $_SESSION['role_type'] = $user['role_type'];  // Store role type (admin or super_admin)

                // Determine the redirect page based on role
                if ($user['role_type'] === 'super_admin') {
                    echo json_encode([
                        'success' => true,
                        'redirect' => 'frontend_users/super_adminPage.php',  // Redirect to super admin page
                        'username' => $_SESSION['username']  // Send username in the response
                    ]);
                } else if ($user['role_type'] === 'admin') {
                    echo json_encode([
                        'success' => true,
                        'redirect' => 'frontend_users/admin_page.php',  // Redirect to admin page
                        'username' => $_SESSION['username']  // Send username in the response
                    ]);
                }
            } else {
                // Invalid password
                $errors['general'] = "Incorrect email or password.";
                echo json_encode(['success' => false, 'errors' => $errors]);
            }
        } else {
            // No user found with the provided email
            $errors['general'] = "No account found with this email.";
            echo json_encode(['success' => false, 'errors' => $errors]);
        }
        $stmt->close(); // Close the prepared statement
    } else {
        // Return validation errors
        echo json_encode(['success' => false, 'errors' => $errors]);
    }
}

$conn->close();
?>