<?php
require '../db.php'; 

$query = "UPDATE notifications SET is_seen = 1 WHERE is_seen = 0";
mysqli_query($conn, $query);
?>
