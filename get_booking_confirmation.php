<?php
session_start();
header('Content-Type: application/json');
include('db.php'); // your DB connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Unauthorized: Please log in first."
    ]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Prepare SQL: join users and service_bookings on user_id
$stmt = $conn->prepare("
    SELECT 
        sb.status,
        u.name,
        sb.service_type,
        sb.booking_date,
        sb.location
    FROM service_bookings sb
    INNER JOIN users u ON sb.user_id = u.id
    WHERE sb.user_id = ?
    ORDER BY sb.booking_date DESC
");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $bookings = [];

    while ($row = $result->fetch_assoc()) {
        $bookings[] = [
            "status"        => $row['status'],
            "username"      => $row['name'],
            "service_type"  => $row['service_type'],
            "booking_date"  => $row['booking_date'],
            "location"      => $row['location']
        ];
    }

    if (empty($bookings)) {
        echo json_encode([
            "status" => "success",
            "message" => "No booking confirmations found.",
            "bookings" => []
        ]);
    } else {
        echo json_encode([
            "status" => "success",
            "bookings" => $bookings
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to fetch booking confirmations: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
