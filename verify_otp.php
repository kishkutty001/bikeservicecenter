<?php
session_start();

header('Content-Type: application/json');

// Get entered OTP
$entered_otp = $_POST['otp'] ?? null;

if (!$entered_otp) {
    echo json_encode(['status' => 'error', 'message' => 'OTP is required']);
    exit;
}

// Check if OTP is still valid (10 minutes)
if (time() - $_SESSION['otp_generated_time'] <= 600) {
    if ($entered_otp == $_SESSION['forgot_password_otp']) {
        echo json_encode(['status' => 'success', 'message' => 'OTP verified']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid OTP']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'OTP expired']);
}
?>
