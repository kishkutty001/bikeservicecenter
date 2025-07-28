<?php
header('Content-Type: application/json');
include('db.php'); // your DB file

$id             = $_POST['id'] ?? null;
$spare_name     = $_POST['spare_name'] ?? null;
$spare_price    = $_POST['spare_price'] ?? null;
$spare_category = $_POST['spare_category'] ?? null;
$spare_unit     = $_POST['spare_unit'] ?? null;

if (!$id || !$spare_name || !$spare_price || !$spare_category || !$spare_unit) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

// Handle optional new image upload
$spare_image = null;
if (isset($_FILES['spare_image']) && $_FILES['spare_image']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['spare_image']['tmp_name'];
    $file_name = basename($_FILES['spare_image']['name']);
    $target_dir = 'uploads/'; // change as needed
    $target_file = $target_dir . uniqid('spare_', true) . '_' . $file_name;

    if (move_uploaded_file($file_tmp, $target_file)) {
        $spare_image = basename($target_file);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload image']);
        exit;
    }
}

// Build update query dynamically
if ($spare_image) {
    $stmt = $conn->prepare("UPDATE spare_parts SET spare_name=?, spare_price=?, spare_category=?, spare_unit=?, spare_image=? WHERE id=?");
    $stmt->bind_param("sdsssi", $spare_name, $spare_price, $spare_category, $spare_unit, $spare_image, $id);
} else {
    $stmt = $conn->prepare("UPDATE spare_parts SET spare_name=?, spare_price=?, spare_category=?, spare_unit=? WHERE id=?");
    $stmt->bind_param("sdssi", $spare_name, $spare_price, $spare_category, $spare_unit, $id);
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Spare part updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
