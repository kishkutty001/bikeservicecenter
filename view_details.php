<?php
// Set headers to return JSON and allow cross-origin requests
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// --- Database Connection ---
require 'db.php';

// --- Fetch Data ---
// Check if the 'id' parameter is provided in the URL (e.g., .../get_request_details.php?id=1)
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(["error" => "A valid request ID is required."]);
    exit();
}

$requestId = (int)$_GET['id'];

// SQL query to join service_requests and users tables
// This fetches all details for a specific request ID
$sql = "SELECT 
            sr.id, 
            sr.user_id, 
            sr.service_type, 
            sr.bike_model, 
            sr.other_bike_model, 
            sr.issue_type, 
            sr.other_issue_type, 
            sr.additional_details, 
            sr.service_address, 
            sr.preferred_date, 
            sr.preferred_time, 
            sr.status, 
            sr.request_date,
            u.name AS user_name,
            u.mobile AS user_mobile,
            u.email AS user_email
        FROM 
            service_requests sr
        JOIN 
            users u ON sr.user_id = u.id
        WHERE 
            sr.id = ?";

// Use a prepared statement to prevent SQL injection
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $requestId);
$stmt->execute();
$result = $stmt->get_result();

$response = [];

if ($result->num_rows > 0) {
    // Fetch the single row of data
    $response = $result->fetch_assoc();
} else {
    $response["error"] = "Request not found.";
}

// Return the data as JSON
echo json_encode($response);

// Close connections
$stmt->close();
$conn->close();
?>