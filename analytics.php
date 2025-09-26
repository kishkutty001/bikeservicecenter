<?php
header("Content-Type: application/json");

// Database connection
$conn = new mysqli("localhost", "root", "", "bikeservice");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Example queries (replace with real logic)
$new_signups = 156;
$daily_active = 456;
$weekly_active = 892;
$monthly_active = 1247;
$avg_session = "8.5 mins";
$total_users = 1247;
$active_users = 892;

echo json_encode([
    "new_signups" => $new_signups,
    "daily_active" => $daily_active,
    "weekly_active" => $weekly_active,
    "monthly_active" => $monthly_active,
    "avg_session" => $avg_session,
    "total_users" => $total_users,
    "active_users" => $active_users
]);

$conn->close();
?>
