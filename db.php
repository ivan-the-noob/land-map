<?php

// $host = '127.0.0.1';       // Database host
// $username = 'u173282149_landshop';        // Database username
// $password = '#Joshua23';            // Database password
// $dbname = 'u173282149_landshop';  // Database name

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