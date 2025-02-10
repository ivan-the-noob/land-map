<?php
require '../db.php';
session_start();

// Check if user is admin
if (!isset($_SESSION['role_type']) || $_SESSION['role_type'] !== 'admin') {
    header('Location: ../frontend/sign_in.php');
    exit;
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="reports_' . date('Y-m-d') . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Add CSV headers
fputcsv($output, ['Report ID', 'Reporter', 'Reported User', 'Report Type', 'Description', 'Date', 'Status']);

// Fetch and write data
$sql = "SELECT r.*, 
        u1.username as reporter_name,
        u2.username as reported_user_name
        FROM reports r
        LEFT JOIN users u1 ON r.reporter_id = u1.user_id
        LEFT JOIN users u2 ON r.reported_user_id = u2.user_id
        ORDER BY r.created_at DESC";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['report_id'],
        $row['reporter_name'],
        $row['reported_user_name'],
        $row['report_type'],
        $row['description'],
        $row['created_at'],
        $row['status']
    ]);
}

fclose($output); 