<?php
// ✅ No spaces or newlines before this line
header('Content-Type: application/json; charset=UTF-8');

// ✅ Disable error display (to avoid HTML in JSON)
ini_set('display_errors', 0);
error_reporting(0);

session_start();
require 'db.php'; // Ensure this file also has no output/whitespace

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // ✅ Validate input
    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Email and password are required."]);
        exit;
    }

    // ✅ Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, name, email, user_type,mobile FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $_SESSION['user_id']   = $row['id'];
        $_SESSION['user_name'] = $row['name'];
        $_SESSION['user_type'] = $row['user_type'];
        $_SESSION['mobile'] = $row['mobile'];

        http_response_code(200);
        echo json_encode([
            "status"  => "success",
            "message" => "Login successful.",
            "data"    => $row
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed."]);
}
?>
