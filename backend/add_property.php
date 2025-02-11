<?php

require '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
        exit;
    }

    $user_id = $_SESSION['user_id']; // Retrieve the logged-in user's ID

    // Check if all required fields exist
    $requiredFields = ['propertyName', 'propertyLocation', 'propertyType', 'saleOrLease', 'landArea', 'propertyDescription', 'latitude', 'longitude'];
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
    $leaseDuration = isset($_POST['leaseDuration']) ? $_POST['leaseDuration'] : null;
    $monthlyRent = isset($_POST['monthlyRent']) ? $_POST['monthlyRent'] : null;
    $landCondition = isset($_POST['landCondition']) ? $_POST['landCondition'] : null;
    $salePrice = isset($_POST['salePrice']) ? $_POST['salePrice'] : null;
    $anotherInfo = isset($_POST['anotherInfo']) ? $_POST['anotherInfo'] : null;
    $propertyDescription = $_POST['propertyDescription'];

    // Latitude & Longitude validation
    if (!is_numeric($_POST['latitude']) || !is_numeric($_POST['longitude'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid latitude or longitude.']);
        exit;
    }

    $latitude = floatval($_POST['latitude']);
    $longitude = floatval($_POST['longitude']);

    // Set appropriate values based on listing type
    if ($saleOrLease === 'lease') {
        if (empty($leaseDuration) || !is_numeric($leaseDuration)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid lease duration.']);
            exit;
        }
    } elseif ($saleOrLease === 'sale') {
        if (empty($landCondition)) {
            echo json_encode(['status' => 'error', 'message' => 'Land condition is required for sale properties.']);
            exit;
        }
    }

    // Insert the property into the properties table
    $sql = "INSERT INTO properties (property_name, property_location, property_type, sale_or_lease, land_area, lease_duration, monthly_rent, land_condition, sale_price, another_info, property_description, latitude, longitude, user_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('ssssissdssssdd', 
            $propertyName, $propertyLocation, $propertyType, $saleOrLease, 
            $landArea, $leaseDuration, $monthlyRent, $landCondition, 
            $salePrice, $anotherInfo, $propertyDescription, $latitude, $longitude, $user_id
        );

        if ($stmt->execute()) {
            $propertyId = $stmt->insert_id;
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'SQL statement error: ' . $conn->error]);
        exit;
    }

    // Handle image upload
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $uploadDir = "../assets/property_images/";
        $uploadedImages = [];

        foreach ($_FILES['images']['name'] as $key => $imageName) {
            $imageTmp = $_FILES['images']['tmp_name'][$key];
            $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);
            $newImageName = uniqid() . "." . $imageExt;

            $targetFile = $uploadDir . $newImageName;

            if (move_uploaded_file($imageTmp, $targetFile)) {
                $uploadedImages[] = $newImageName;

                // Insert image record into property_images table
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

    // Success response
    echo json_encode([
        'status' => 'success',
        'message' => 'Property successfully added!',
        'property_id' => $propertyId
    ]);
    exit;
}
