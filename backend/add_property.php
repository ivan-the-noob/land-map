<?php

require '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();
    $user_id = $_SESSION['user_id']; // Retrieve the logged-in user's ID

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
    $coordinates = $_POST['coordinates'];

    // Initialize variables
    $leaseDuration = null;
    $landCondition = null;
    
    // Set appropriate values based on listing type
    if ($saleOrLease === 'lease') {
        $leaseDuration = $_POST['leaseDuration'];
    } else if ($saleOrLease === 'sale') {
        $landCondition = $_POST['landCondition'];
    }

    // Insert the property into the properties table
    $sql = "INSERT INTO properties (property_name, property_location, property_type, sale_or_lease, land_area, lease_duration, monthly_rent, land_condition, sale_price, another_info, property_description, coordinates, user_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('ssssissdsssss', $propertyName, $propertyLocation, $propertyType, $saleOrLease, $landArea, $leaseDuration, $monthlyRent, $landCondition, $salePrice, $anotherInfo, $propertyDescription, $coordinates, $user_id);
        $stmt->execute();
        $propertyId = $stmt->insert_id; // Get the inserted property ID
        $stmt->close();

        // Handle image upload
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $uploadDir = "../assets/property_images/";
            $uploadedImages = [];

            foreach ($_FILES['images']['name'] as $key => $imageName) {
                $imageTmp = $_FILES['images']['tmp_name'][$key];
                $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);
                $newImageName = uniqid() . "." . $imageExt; // Unique filename

                $targetFile = $uploadDir . $newImageName;

                if (move_uploaded_file($imageTmp, $targetFile)) {
                    $uploadedImages[] = $newImageName;

                    // Insert image record into property_images table
                    $imageSql = "INSERT INTO property_images (property_id, image_name) VALUES (?, ?)";
                    if ($imageStmt = $conn->prepare($imageSql)) {
                        $imageStmt->bind_param('is', $propertyId, $newImageName);
                        $imageStmt->execute();
                        $imageStmt->close();
                    }
                }
            }
        }

        // Success response
        echo json_encode(['status' => 'success', 'message' => 'Property successfully added!', 'property_id' => $propertyId]);
    } else {
        // Error response
        echo json_encode(['status' => 'error', 'message' => 'Failed to add property.']);
    }

    exit; // Make sure no further output is sent
}

/*
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $propertyName = isset($_POST['propertyName']) ? $_POST['propertyName'] : '';
    $propertyLocation = isset($_POST['propertyLocation']) ? $_POST['propertyLocation'] : '';
    $saleOrLease = isset($_POST['saleOrLease']) ? $_POST['saleOrLease'] : '';
    $landArea = isset($_POST['landArea']) ? $_POST['landArea'] : '';
    $leaseDuration = isset($_POST['leaseDuration']) ? $_POST['leaseDuration'] : '';
    $monthlyRent = isset($_POST['monthlyRent']) ? $_POST['monthlyRent'] : '';
    $landCondition = isset($_POST['landCondition']) ? $_POST['landCondition'] : '';
    $salePrice = isset($_POST['salePrice']) ? $_POST['salePrice'] : '';
    $anotherInfo = isset($_POST['anotherInfo']) ? $_POST['anotherInfo'] : '';
    $propertyDescription = isset($_POST['propertyDescription']) ? $_POST['propertyDescription'] : '';
    $coordinates = isset($_POST['coordinates']) ? $_POST['coordinates'] : '';
    
    //kuha nya image
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $uploadedImage = [];
        $images = $_FILES['images'];
        
        foreach ($images['name'] as $key => $imageName) {
            $imageTmp = $images['tmp_name'][$key];
            $imageType = $images['type'][$key];
            
            
            $target = "../assets/uploads/";
            $targetFile = $target . basename($imageName);
            
            if (move_uploaded_file($imageTmp, $targetFile)) {
                $uploadedImage[] = $targetFile;
            } else {
                $uploadedImage[] = "Error uploading image: $imageName";
            }
        }
    } else {
        $uploadedImage = "No images uploaded.";
    }

    echo "<h2>Form Data Received:</h2>";
    echo "<strong>Property Name:</strong> " . htmlspecialchars($propertyName) . "<br>";
    echo "<strong>Property Location:</strong> " . htmlspecialchars($propertyLocation) . "<br>";
    echo "<strong>Listing Type:</strong> " . htmlspecialchars($saleOrLease) . "<br>";
    echo "<strong>Land Area (sqm):</strong> " . htmlspecialchars($landArea) . "<br>";

    if ($saleOrLease == 'lease') {
        echo "<strong>Lease Duration:</strong> " . htmlspecialchars($leaseDuration) . "<br>";
        echo "<strong>Monthly Rent:</strong> ₱" . htmlspecialchars($monthlyRent) . "<br>";
    } elseif ($saleOrLease == 'sale') {
        echo "<strong>Land Condition:</strong> " . htmlspecialchars($landCondition) . "<br>";
        echo "<strong>Sale Price:</strong> ₱" . htmlspecialchars($salePrice) . "<br>";
        echo "<strong>Another Information:</strong> " . htmlspecialchars($anotherInfo) . "<br>";
    }

    echo "<strong>Property Description:</strong> " . htmlspecialchars($propertyDescription) . "<br>";
    echo "<strong>Coordinates:</strong> " . htmlspecialchars($coordinates) . "<br>";

    echo "<h3>Uploaded Images:</h3>";
    if (is_array($uploadedImage)) {
        foreach ($uploadedImage as $image) {
            echo "<img src='" . $image . "' alt='Property Image' width='150px' /><br>";
        }
    } else {
        echo $uploadedImage . "<br>";
    }
}
    */