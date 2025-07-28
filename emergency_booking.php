<?php
session_start();
header('Content-Type: application/json');
include('db.php');



// Get POST data
$user_id = $_SESSION['user_id'] ?? null;
$location = $_POST['location'] ?? null;
$emergency_type = $_POST['emergency_type'] ?? null;

// Validate
if (!$user_id || !$location || !$emergency_type) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}


// Insert booking
$stmt = $conn->prepare("INSERT INTO emergency_bookings (user_id, location, emergency_type) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $location, $emergency_type);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Emergency booking created successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to create booking']);
}

$stmt->close();
$conn->close();
?>
