<?php
header('Content-Type: application/json');
include('db.php'); // include your DB connection

// Get data from POST
$mechanic_name   = $_POST['mechanic_name'] ?? null;
$email_address   = $_POST['email_address'] ?? null;
$phone_number    = $_POST['phone_number'] ?? null;
$mechanic_id     = $_POST['mechanic_id'] ?? null;
$experience      = $_POST['experience'] ?? null;
$specialization  = $_POST['specialization'] ?? null;
$licence_number  = $_POST['licence_number'] ?? null;
$address         = $_POST['address'] ?? null;

// Validate required fields
if (!$mechanic_name || !$email_address || !$phone_number || !$mechanic_id || !$experience || !$specialization || !$licence_number || !$address) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

// Handle image upload if present
$mechanic_image = null;
if (isset($_FILES['mechanic_image']) && $_FILES['mechanic_image']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['mechanic_image']['tmp_name'];
    $file_name = basename($_FILES['mechanic_image']['name']);
    $target_dir = 'uploads/'; // change as needed
    $target_file = $target_dir . uniqid('mech_', true) . '_' . $file_name;

    if (move_uploaded_file($file_tmp, $target_file)) {
        $mechanic_image = basename($target_file); // save only the file name in DB
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload mechanic image']);
        exit;
    }
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO mechanic 
    (mechanic_name, email_address, phone_number, mechanic_id, experience, specialization, licence_number, address, mechanic_image) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "sssssssss", 
    $mechanic_name, 
    $email_address, 
    $phone_number, 
    $mechanic_id, 
    $experience, 
    $specialization, 
    $licence_number, 
    $address, 
    $mechanic_image
);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Mechanic added successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add mechanic. ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
