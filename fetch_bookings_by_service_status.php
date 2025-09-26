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
    
    if ($user_type !== 'mechanic') {
        echo json_encode(["status" => "error", "message" => "Access denied: Only  mechanic users can view bookings."]);
        exit();
    }
} else {
    echo json_encode(["status" => "error", "message" => "User not found."]);
    exit();
}

// Step 3: Get requested service_complete value from POST or GET
$service_status = $_POST['service_complete'] ?? $_GET['service_complete'] ?? 'pending';

// Validate: allow only 'pending' or 'completed'
if ($service_status !== 'pending' && $service_status !== 'completed') {
    echo json_encode(["status" => "error", "message" => "Invalid service status filter. Allowed: pending or completed."]);
    exit();
}

// Step 4: Fetch bookings with this service_complete status
$query = "SELECT booking_id, user_id, username, mobile, service_type, location, vehicle_type, vehicle_model, 
          issue_type, message, status, mechanic_id, booking_date, service_complete,amount 
          FROM service_bookings 
          WHERE service_complete = ?
          ORDER BY created_at ASC";

$stmt2 = $conn->prepare($query);
$stmt2->bind_param("s", $service_status);
$stmt2->execute();
$result2 = $stmt2->get_result();

$bookings = [];
if ($result2 && $result2->num_rows > 0) {
    while ($row = $result2->fetch_assoc()) {
        $bookings[] = $row;
    }
    echo json_encode(["status" => "success", "service_complete" => $service_status, "bookings" => $bookings]);
} else {
    echo json_encode(["status" => "success", "service_complete" => $service_status, "bookings" => [], "message" => "No bookings found."]);
}

$stmt->close();
$stmt2->close();
$conn->close();
?>
