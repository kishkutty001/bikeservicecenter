<?php
session_start();
header('Content-Type: application/json');
include('db.php'); // include your DB connection

// Step 1: Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized: Please log in first."]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Step 2: Check if user is admin or mechanic
$stmt = $conn->prepare("SELECT user_type FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_type = $user['user_type'];
    
    if ($user_type !== 'admin' && $user_type !== 'mechanic') {
        echo json_encode(["status" => "error", "message" => "Access denied: Only admin and mechanic users can view bookings."]);
        exit();
    }
} else {
    echo json_encode(["status" => "error", "message" => "User not found."]);
    exit();
}

// Step 3: Fetch service bookings
$query = "SELECT booking_id, user_id, username, mobile, service_type, location, vehicle_type, vehicle_model, 
          issue_type, message, status, mechanic_id, booking_date, service_complete 
          FROM service_bookings 
          ORDER BY created_at ASC";

$result = $conn->query($query);

$bookings = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    echo json_encode(["status" => "success", "bookings" => $bookings]);
} else {
    echo json_encode(["status" => "success", "bookings" => [], "message" => "No bookings found."]);
}

$stmt->close();
$conn->close();
?>
