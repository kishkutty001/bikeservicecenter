<?php
header("Content-Type: application/json");

require 'db.php';

// --- Fetch Only Required Data ---
$sql = "SELECT user_id,id,bike_model, issue_type, service_address, status FROM service_requests ORDER BY request_date DESC";
$result = $conn->query($sql);

$requests = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
    echo json_encode($requests);
} else {
    echo json_encode([]);
}

$conn->close();
?>