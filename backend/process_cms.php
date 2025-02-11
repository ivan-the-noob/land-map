<?php
require '../db.php';

function uploadFile($file, $directory) {
    if ($file['error'] == UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $uniqueName = uniqid() . '.' . $ext;
        $uploadPath = $directory . $uniqueName;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $uniqueName;
        }
    }
    return null;
}

$uploadDir = '../assets/images/cms/';

$query = "SELECT COUNT(*) AS count FROM cms";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$cmsExists = ($row['count'] > 0);

if ($cmsExists) {
    $query = "SELECT * FROM cms LIMIT 1";
    $result = $conn->query($query);
    $cms = $result->fetch_assoc();
} else {
    $cms = [];
}

$logo = (!empty($_FILES['logo']['name'])) ? uploadFile($_FILES['logo'], $uploadDir) : ($cms['logo'] ?? NULL);
$img = (!empty($_FILES['img']['name'])) ? uploadFile($_FILES['img'], $uploadDir) : ($cms['img'] ?? NULL);

$text = $_POST['text'] ?? NULL;
$animation_text = $_POST['animation_text'] ?? NULL;
$background_color = $_POST['background_color'] ?? NULL;
$land_services = $_POST['land_services'] ?? NULL;
$font_family = $_POST['font_family'] ?? NULL;
$font_style = $_POST['font_style'] ?? NULL;
$font_size = $_POST['font_size'] ?? NULL;
$about_page = $_POST['about_page'] ?? NULL;
$contact_email = $_POST['contact_email'] ?? NULL;
$contact_number = $_POST['contact_number'] ?? NULL;
$contact_location = $_POST['contact_location'] ?? NULL;

if ($cmsExists) {
    $query = "UPDATE cms SET 
        logo = ?, text = ?, animation_text = ?, background_color = ?, img = ?, land_services = ?, 
        font_family = ?, font_style = ?, font_size = ?, about_page = ?, 
        contact_email = ?, contact_number = ?, contact_location = ?
        WHERE id = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssssssss", $logo, $text, $animation_text, $background_color, $img, $land_services, 
        $font_family, $font_style, $font_size, $about_page, $contact_email, $contact_number, $contact_location);
    $action = "updated";
} else {
    $query = "INSERT INTO cms (logo, text, animation_text, background_color, img, land_services, 
              font_family, font_style, font_size, about_page, contact_email, contact_number, contact_location) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssssssss", $logo, $text, $animation_text, $background_color, $img, $land_services, 
        $font_family, $font_style, $font_size, $about_page, $contact_email, $contact_number, $contact_location);
    $action = "inserted";
}

if ($stmt->execute()) {
    echo "CMS $action successfully!";
} else {
    echo "Error updating CMS.";
}
?>
