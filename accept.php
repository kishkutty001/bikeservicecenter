<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit();
}

include('db.php');

$user_id = $_SESSION['user_id'];
$booking_id = $_POST['booking_id'] ?? '';
$mechanic_id = $_POST['mechanic_id'] ?? '';

// Check if the user is an admin
$stmt = $conn->prepare("SELECT user_type FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['user_type'] !== 'mechanic') {
    echo json_encode(["status" => "error", "message" => "Access denied. mechanic only."]);
    exit();
}

// Validate input
if (empty($booking_id) || empty($mechanic_id)) {
    echo json_encode(["status" => "error", "message" => "Booking ID and Mechanic ID are required."]);
    exit();
}

// Check if mechanic exists in  mechanic table
$check_mechanic = $conn->prepare("SELECT mechanic_id FROM  mechanic WHERE mechanic_id = ?");
$check_mechanic->bind_param("s", $mechanic_id);
$check_mechanic->execute();
$mechanic_result = $check_mechanic->get_result();

if ($mechanic_result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Invalid mechanic ID."]);
    exit();
}

// Update booking and assign mechanic
$update = $conn->prepare("UPDATE service_bookings SET status = 'accepted', mechanic_id = ? WHERE booking_id = ?");
$update->bind_param("si", $mechanic_id, $booking_id);

if ($update->execute()) {
    echo json_encode(["status" => "success", "message" => "Booking accepted and mechanic assigned."]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to accept booking. " . $conn->error]);
}

// Close all
$stmt->close();
$check_mechanic->close();
$update->close();
$conn->close();
?>
