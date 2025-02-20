<?php
include '../db.php';

$users_per_page = 2;
$user_page = isset($_GET['user_page']) ? (int)$_GET['user_page'] : 1;
$user_offset = ($user_page - 1) * $users_per_page;

$query = "SELECT user_id, profile, fname, lname, role_type, is_verified, email FROM users WHERE role_type = 'user' LIMIT $users_per_page OFFSET $user_offset";
$result = $conn->query($query);

// Output only the user table (same as in `index.php`)
?>
