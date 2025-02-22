<?php
require '../db.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
        exit;
    }

    $user_id = $_SESSION['user_id']; // Retrieve the logged-in user's ID

    // Required fields except developer
    $requiredFields = ['propertyName', 'propertyType', 'saleOrLease', 'landArea', 'propertyDescription', 'latitude', 'longitude'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            echo json_encode(['status' => 'error', 'message' => "Missing required field: $field"]);
            exit;
        }
    }

    // Collect form data
    $propertyName = $_POST['propertyName'];
    $propertyLocation = $_POST['propertyLocation'];
    $propertyType = $_POST['propertyType'];
    $saleOrLease = $_POST['saleOrLease'];
    $landArea = $_POST['landArea'];
    $leaseDuration = $_POST['leaseDuration'] ?? null;
    $monthlyRent = $_POST['monthlyRent'] ?? null;
    $landCondition = $_POST['landCondition'] ?? null;
    $salePrice = $_POST['salePrice'] ?? null;
    $anotherInfo = $_POST['anotherInfo'] ?? null;
    $propertyDescription = $_POST['propertyDescription'];
    $developer = !empty(trim($_POST['developer'])) ? $_POST['developer'] : 'NO DEVELOPER';


    // Validate latitude & longitude
    if (!is_numeric($_POST['latitude']) || !is_numeric($_POST['longitude'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid latitude or longitude.']);
        exit;
    }

    $latitude = floatval($_POST['latitude']);
    $longitude = floatval($_POST['longitude']);

    // Insert property into the database
    $sql = "INSERT INTO properties (property_name, property_location, property_type, sale_or_lease, land_area, lease_duration, monthly_rent, land_condition, sale_price, another_info, property_description, latitude, longitude, user_id, developer) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('ssssisssssssdds', 
            $propertyName, $propertyLocation, $propertyType, $saleOrLease, 
            $landArea, $leaseDuration, $monthlyRent, $landCondition, 
            $salePrice, $anotherInfo, $propertyDescription, $latitude, $longitude, $user_id, $developer
        );

        if (!$stmt->execute()) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
            exit;
        }

        $propertyId = $stmt->insert_id;
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'SQL preparation error: ' . $conn->error]);
        exit;
    }

    // Insert notification
    $notifMessage = "Agent has added a new property: $propertyName";
    $notifSQL = "INSERT INTO notifications (agent_id, notification, created_at) VALUES (?, ?, NOW())";

    if ($notifStmt = $conn->prepare($notifSQL)) {
        $notifStmt->bind_param('is', $user_id, $notifMessage);
        $notifStmt->execute();
        $notifStmt->close();
    }

    // Handle image upload
    if (!empty($_FILES['images']['name'][0])) {
        $uploadDir = "../assets/property_images/";
        foreach ($_FILES['images']['name'] as $key => $imageName) {
            $imageTmp = $_FILES['images']['tmp_name'][$key];
            $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);
            $newImageName = uniqid() . "." . $imageExt;
            $targetFile = $uploadDir . $newImageName;

            if (move_uploaded_file($imageTmp, $targetFile)) {
                $imageSql = "INSERT INTO property_images (property_id, image_name) VALUES (?, ?)";
                if ($imageStmt = $conn->prepare($imageSql)) {
                    $imageStmt->bind_param('is', $propertyId, $newImageName);
                    if (!$imageStmt->execute()) {
                        echo json_encode(['status' => 'error', 'message' => 'Image DB error: ' . $imageStmt->error]);
                        exit;
                    }
                    $imageStmt->close();
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Image SQL error: ' . $conn->error]);
                    exit;
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => "Error uploading image: $imageName"]);
                exit;
            }
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Property successfully added!',
        'property_id' => $propertyId
    ]);
    exit;
}
?>
