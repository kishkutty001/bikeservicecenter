<?php
session_start();
header('Content-Type: application/json');
include('db.php');
// Get data from POST
$user_email = $_SESSION['user_email'] ?? null;
$new_password = $_POST['new_password'] ?? null;

if (!$user_email || !$new_password) {
    echo json_encode(['status' => 'error', 'message' => 'Email and new password are required']);
    exit;
}

// Check if OTP was verified before reset
if (!isset($_SESSION['forgot_password_otp'])) {
    echo json_encode(['status' => 'error', 'message' => 'OTP verification required']);
    exit;
}

// TODO: update password in your database as plain text (not secure)
// Example using mysqli (replace credentials and table/column names)


$stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
$stmt->bind_param("ss", $new_password, $user_email);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
    // Clear OTP from session
    unset($_SESSION['forgot_password_otp']);
    unset($_SESSION['otp_generated_time']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update password']);
}

$stmt->close();
$conn->close();
?>
