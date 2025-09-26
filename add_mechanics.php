<?php
header('Content-Type: application/json');

// --- IMPORTANT: CONFIGURE YOUR DATABASE CONNECTION HERE ---
require 'db.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

// --- HANDLE FILE UPLOAD ---
$document_image_path = null;
if (isset($_FILES['document_image']) && $_FILES['document_image']['error'] == 0) {
    $target_dir = "uploads/"; // Make sure this directory exists and is writable
    $file_extension = strtolower(pathinfo($_FILES["document_image"]["name"], PATHINFO_EXTENSION));
    $unique_file_name = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $unique_file_name;

    // Basic validation (you can add more checks for size, type, etc.)
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_extension, $allowed_types)) {
        echo json_encode(['status' => 'error', 'message' => 'Only JPG, JPEG, PNG & GIF files are allowed.']);
        exit();
    }

    if (move_uploaded_file($_FILES["document_image"]["tmp_name"], $target_file)) {
        $document_image_path = $target_file;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Sorry, there was an error uploading your file.']);
        exit();
    }
}

// --- GET TEXT DATA ---
$full_name = $_POST['full_name'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$address = $_POST['address'] ?? '';
$specialization = $_POST['specialization'] ?? '';
$experience = $_POST['experience'] ?? 0;
$license_number = $_POST['license_number'] ?? '';

// --- PREPARE AND BIND SQL STATEMENT ---
$stmt = $conn->prepare("INSERT INTO mechanics (full_name, phone, email, address, specialization, experience, license_number, document_image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
    exit();
}

$stmt->bind_param("sssssiss", $full_name, $phone, $email, $address, $specialization, $experience, $license_number, $document_image_path);

// --- EXECUTE AND RESPOND ---
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'New mechanic added successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add mechanic: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
