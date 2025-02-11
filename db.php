<?php

$host = '127.0.0.1';       // Database host
$username = 'root';        // Database username
$password = '';            // Database password
$dbname = 'landmap';  // Database name

// Create a connection using MySQLi
$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}