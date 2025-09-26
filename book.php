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

// Define amount mapping based on service_type and issue_type
$pricing = [
    'Repair' => [
        'Engine Issue'       => 1500,
        'Brake Issue'        => 800,
        'Electrical Issue'   => 1000
    ],
    'Towing' => [
        'Accident'           => 2500,
        'Breakdown'          => 2000
    ],
    'Spare Parts' => [
        'Battery'            => 3000,
        'Tyre'               => 2500
    ],
    'Emergency Service' => [
        'Night Breakdown'    => 3500,
        'Accident Emergency' => 4000,
        'Flat Tyre Emergency'=> 3000
    ]
];


// Default amount
$base_amount = 0;

// Lookup amount
if (isset($pricing[$service_type][$issue_type])) {
    $base_amount = $pricing[$service_type][$issue_type];
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid service type or issue type for pricing."
    ]);
    exit();
}

// Add service charge
$service_charge = 200; // You can set your desired service charge here
$total_amount = $base_amount + $service_charge;

// Prepare SQL statement with total amount
$stmt = $conn->prepare("INSERT INTO service_bookings 
    (user_id, username, mobile, service_type, location, vehicle_type, vehicle_model, issue_type, message, booking_date, amount)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "isssssssssd", 
    $user_id, $username, $mobile, $service_type, $location, $vehicle_type, $vehicle_model, $issue_type, $message, $booking_date, $total_amount
);

// Execute and return response
if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Service booked successfully.",
        "booking_id" => $stmt->insert_id,
        "base_amount" => $base_amount,
        "service_charge" => $service_charge,
        "total_amount" => $total_amount
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
