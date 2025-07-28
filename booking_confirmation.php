<?php
session_start();
header('Content-Type: application/json');
include('db.php'); // your db connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$user_stmt = $conn->prepare("SELECT id, name, email, mobile FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}
$user = $user_result->fetch_assoc();

// Fetch service bookings for this user
$booking_stmt = $conn->prepare("SELECT booking_id, service_type, status, booking_date, mechanic_id FROM service_bookings WHERE user_id = ?");
$booking_stmt->bind_param("i", $user_id);
$booking_stmt->execute();
$booking_result = $booking_stmt->get_result();

$bookings = [];
while ($row = $booking_result->fetch_assoc()) {
    $bookings[] = $row;
}

// Return combined response
echo json_encode([
    'status' => 'success',
    'user' => $user,
    'bookings' => $bookings
]);

$user_stmt->close();
$booking_stmt->close();
$conn->close();
?>
