<?php
session_start();
header('Content-Type: application/json');

// Load Composer's autoloader
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Get email from session (from your flow)
$user_email = $_SESSION['user_email'] ?? null;

if (!$user_email) {
    echo json_encode(['status' => 'error', 'message' => 'Email is required']);
    exit;
}

// Generate OTP
$otp = rand(100000, 999999);

// Save to session
$_SESSION['forgot_password_otp'] = $otp;
$_SESSION['otp_generated_time'] = time();

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';             // SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'kishkutty001@gmail.com';   // ✅ Your Gmail
    $mail->Password = 'djko qknn gimj xofm
';      // ✅ Gmail App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('your_email@gmail.com', 'Bike Service Center');
    $mail->addAddress($user_email);            // User's email

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Your Password Reset OTP';
    $mail->Body    = "Hello,<br><br>Your OTP for password reset is: <b>$otp</b><br><br>It is valid for 10 minutes.";

    $mail->send();
    echo json_encode(['status' => 'success', 'message' => 'OTP sent successfully']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
}
?>
