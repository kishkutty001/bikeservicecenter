<?php
session_start();
header('Content-Type: application/json');

// Admin access check
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in."]);
    exit();
}

include('db.php');

$admin_id = $_SESSION['user_id'];

// Verify admin
$stmt = $conn->prepare("SELECT user_type FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['user_type'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Access denied. Admins only."]);
    exit();
}

// Get and validate POST data
$name = $_POST['name'] ?? '';
$mobile = $_POST['mobile'] ?? '';
$user_experince = $_POST['user_experince'] ?? '';
$expertise = $_POST['expertise'] ?? '';

if (empty($name) || empty($mobile) || empty($user_experince) || empty($expertise)) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit();
}

// Insert mechanic
$insert = $conn->prepare("INSERT INTO mechanics (name, mobile, user_experince, expertise) VALUES (?, ?, ?, ?)");
$insert->bind_param("ssss", $name, $mobile, $user_experince, $expertise);

if ($insert->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Mechanic added successfully.",
        "mechanic_id" => $insert->insert_id
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to add mechanic: " . $insert->error]);
}

$stmt->close();
$insert->close();
$conn->close();
?>
