<?php
require '../db.php';
session_start();

header('Content-Type: application/json'); // Ensure JSON response

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        $response['message'] = "User not authenticated.";
        echo json_encode($response);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $position = trim($_POST['position']);
    $prc_id = trim($_POST['prc_id']);
    $dshp_id = trim($_POST['dshp_id']);

    // Validate required fields
    if (empty($position) || empty($prc_id) || empty($dshp_id)) {
        $response['message'] = "All fields are required.";
        echo json_encode($response);
        exit;
    }

    // File Upload Directory
    $uploadDir = __DIR__ . "../../assets/agent_information/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create folder if it doesn't exist
    }

    // Handle File Uploads
    $prc_file = !empty($_FILES['prc_file']['name']) ? time() . '_' . basename($_FILES['prc_file']['name']) : null;
    $dshp_file = !empty($_FILES['dshp_file']['name']) ? time() . '_' . basename($_FILES['dshp_file']['name']) : null;

    if ($prc_file) {
        $prcFilePath = $uploadDir . $prc_file;
        if (!move_uploaded_file($_FILES['prc_file']['tmp_name'], $prcFilePath)) {
            $response['message'] = "Failed to upload PRC file.";
            echo json_encode($response);
            exit;
        }
    }

    if ($dshp_file) {
        $dshpFilePath = $uploadDir . $dshp_file;
        if (!move_uploaded_file($_FILES['dshp_file']['tmp_name'], $dshpFilePath)) {
            $response['message'] = "Failed to upload DSHP file.";
            echo json_encode($response);
            exit;
        }
    }

    // Prepare SQL statement
    $sql = "UPDATE users 
            SET position = ?, 
                prc_id = ?, 
                dshp_id = ?, 
                prc_file = COALESCE(?, prc_file), 
                dshp_file = COALESCE(?, dshp_file), 
                information_status = 2
            WHERE user_id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $response['message'] = "SQL error: " . $conn->error;
        echo json_encode($response);
        exit;
    }

    $stmt->bind_param("sssssi", $position, $prc_id, $dshp_id, $prc_file, $dshp_file, $user_id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Information updated successfully.";
    } else {
        $response['message'] = "Database error: " . $stmt->error;
    }

    echo json_encode($response);
}
?>
