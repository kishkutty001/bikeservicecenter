<?php
header('Content-Type: application/json');
include('db.php'); // your db connection

// Get id from POST or GET
$id = $_POST['id'] ?? $_GET['id'] ?? null;

// Get fields from POST
$mechanic_name   = $_POST['mechanic_name'] ?? null;
$email_address   = $_POST['email_address'] ?? null;
$phone_number    = $_POST['phone_number'] ?? null;
$mechanic_id     = $_POST['mechanic_id'] ?? null;
$experience      = $_POST['experience'] ?? null;
$specialization  = $_POST['specialization'] ?? null;
$licence_number  = $_POST['licence_number'] ?? null;
$address         = $_POST['address'] ?? null;

// Validate required fields
if (
    empty($id) || empty($mechanic_name) || empty($email_address) || empty($phone_number) ||
    empty($mechanic_id) || empty($experience) || empty($specialization) || empty($licence_number) || empty($address)
) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

// Handle optional mechanic image upload
$mechanic_image = null;
if (isset($_FILES['mechanic_image']) && $_FILES['mechanic_image']['error'] === UPLOAD_ERR_OK) {
    $file_tmp  = $_FILES['mechanic_image']['tmp_name'];
    $file_name = basename($_FILES['mechanic_image']['name']);
    $target_dir = 'uploads/'; // make sure this folder exists & is writable
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    $target_file = $target_dir . uniqid('mech_', true) . '_' . $file_name;

    if (move_uploaded_file($file_tmp, $target_file)) {
        $mechanic_image = basename($target_file);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload mechanic image']);
        exit;
    }
}

// Update query: include mechanic_image if uploaded
if ($mechanic_image) {
    $stmt = $conn->prepare(
        "UPDATE mechanic 
         SET mechanic_name=?, email_address=?, phone_number=?, mechanic_id=?, experience=?, specialization=?, licence_number=?, address=?, mechanic_image=? 
         WHERE id=?"
    );
    $stmt->bind_param(
        "sssssssssi",
        $mechanic_name, $email_address, $phone_number, $mechanic_id,
        $experience, $specialization, $licence_number, $address, $mechanic_image, $id
    );
} else {
    $stmt = $conn->prepare(
        "UPDATE mechanic 
         SET mechanic_name=?, email_address=?, phone_number=?, mechanic_id=?, experience=?, specialization=?, licence_number=?, address=? 
         WHERE id=?"
    );
    $stmt->bind_param(
        "ssssssssi",
        $mechanic_name, $email_address, $phone_number, $mechanic_id,
        $experience, $specialization, $licence_number, $address, $id
    );
}

// Execute and respond
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Mechanic updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
