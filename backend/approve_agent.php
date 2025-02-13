<?php
require '../db.php';
if (isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $conn->query("UPDATE users SET admin_verify = 1 WHERE user_id = '$userId'");
    echo json_encode(["success" => true]);
}
?>
