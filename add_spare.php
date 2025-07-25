<?php
include 'db.php';
session_start();
header('Content-Type: application/json');

// Ensure user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
}

// Get data
$name        = trim($_POST['name'] ?? '');
$price       = trim($_POST['price'] ?? '');
$description = trim($_POST['description'] ?? '');
$quantity    = trim($_POST['quantity'] ?? '');
$added_by    = $_SESSION['user_id'];

// Validation
if (empty($name) || empty($price) || empty($quantity)) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Name, price, and quantity are required."]);
    exit;
}

if (!is_numeric($price) || $price < 0) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Invalid price."]);
    exit;
}

if (!ctype_digit($quantity)) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Quantity must be an integer."]);
    exit;
}

// Insert query
$sql = "INSERT INTO spare_parts (name, price, description, quantity, added_by)
        VALUES ('$name', '$price', '$description', '$quantity', '$added_by')";

if (mysqli_query($conn, $sql)) {
    http_response_code(200);
    echo json_encode(["status" => "success", "message" => "Spare part added successfully."]);
} else {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Database error: " . mysqli_error($conn)]);
}
?>
