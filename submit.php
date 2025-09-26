<?php
header('Content-Type: application/json');

require 'db.php';

// Check if all required fields are set
$required_fields = ['user_id', 'service_type', 'bike_model', 'issue_type', 'service_address'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field])) {
        echo json_encode(['status' => 'error', 'message' => "Required field '{$field}' is missing."]);
        exit();
    }
}

// Retrieve data from POST request, using null coalescing for optional fields
$user_id = $_POST['user_id'];
$service_type = $_POST['service_type'];
$bike_model = $_POST['bike_model'];
$other_bike_model = $_POST['other_bike_model'] ?? null;
$issue_type = $_POST['issue_type'];
$other_issue_type = $_POST['other_issue_type'] ?? null;
$additional_details = $_POST['additional_details'] ?? null;
$service_address = $_POST['service_address'];
$preferred_date = $_POST['preferred_date'] ?? null;
$preferred_time = $_POST['preferred_time'] ?? null;

// Prepare and bind the SQL statement to prevent SQL injection
$stmt = $conn->prepare(
    "INSERT INTO service_requests (user_id, service_type, bike_model, other_bike_model, issue_type, other_issue_type, additional_details, service_address, preferred_date, preferred_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare the statement: ' . $conn->error]);
    exit();
}

// Bind parameters (i for integer, s for string)
$stmt->bind_param(
    "isssssssss",
    $user_id,
    $service_type,
    $bike_model,
    $other_bike_model,
    $issue_type,
    $other_issue_type,
    $additional_details,
    $service_address,
    $preferred_date,
    $preferred_time
);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Service request submitted successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to submit service request: ' . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>

