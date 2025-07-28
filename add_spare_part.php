<?php
header('Content-Type: application/json');
include('db.php');



// Get POST data
$spare_name = $_POST['spare_name'] ?? null;
$spare_price = $_POST['spare_price'] ?? null;
$spare_category = $_POST['spare_category'] ?? null;
$spare_unit = $_POST['spare_unit'] ?? null;

// Validate required fields
if (!$spare_name || !$spare_price || !$spare_category || !$spare_unit || !isset($_FILES['spare_image'])) {
    echo json_encode(['status' => 'error', 'message' => 'All fields including image are required']);
    exit;
}

// Handle image upload
$target_dir = "uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$image_name = uniqid() . "_" . basename($_FILES["spare_image"]["name"]);
$target_file = $target_dir . $image_name;

if (move_uploaded_file($_FILES["spare_image"]["tmp_name"], $target_file)) {
    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO spare_parts (spare_name, spare_image, spare_price, spare_category, spare_unit) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $spare_name, $image_name, $spare_price, $spare_category, $spare_unit);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Spare part added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to insert into database']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to upload image']);
}

$conn->close();
?>
