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

// Check if the user is an admin
$stmt = $conn->prepare("SELECT user_type FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['user_type'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Access denied. Admins only."]);
    exit();
}

if (empty($booking_id)) {
    echo json_encode(["status" => "error", "message" => "Booking ID is required."]);
    exit();
}

// Update booking status
$update = $conn->prepare("UPDATE service_bookings SET status = 'rejected' WHERE booking_id = ?");
$update->bind_param("i", $booking_id);

if ($update->execute()) {
    echo json_encode(["status" => "success", "message" => "Booking rejected."]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to reject booking."]);
}

$stmt->close();
$update->close();
$conn->close();
?>
