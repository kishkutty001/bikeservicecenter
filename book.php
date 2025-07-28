<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Unauthorized: Please log in first."
    ]);
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['user_name'] ?? '';

include('db.php');

// Get and sanitize POST data
$mobile        = $_POST['mobile'] ?? '';
$service_type  = $_POST['service_type'] ?? '';
$location      = $_POST['location'] ?? '';
$vehicle_type  = $_POST['vehicle_type'] ?? '';
$vehicle_model = $_POST['vehicle_model'] ?? '';
$issue_type    = $_POST['issue_type'] ?? '';
$message       = $_POST['message'] ?? '';
$booking_date  = date('Y-m-d H:i:s'); // current datetime

// Validate required fields
if (empty($username) || empty($mobile) || empty($service_type) || empty($location) || empty($vehicle_type) || empty($vehicle_model) || empty($issue_type)) {
    echo json_encode([
        "status" => "error",
        "message" => "All fields except 'message' are required."
    ]);
    exit();
}

// Prepare SQL statement
$stmt = $conn->prepare("INSERT INTO service_bookings 
    (user_id, username, mobile, service_type, location, vehicle_type, vehicle_model, issue_type, message, booking_date)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "isssssssss", 
    $user_id, $username, $mobile, $service_type, $location, $vehicle_type, $vehicle_model, $issue_type, $message, $booking_date
);

// Execute and return response
if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Service booked successfully.",
        "booking_id" => $stmt->insert_id
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to book service: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
