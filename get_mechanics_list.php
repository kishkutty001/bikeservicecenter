<?php
header('Content-Type: application/json');
include('db.php');  // make sure this connects to your DB

// Optional: base URL for images
$base_url = 'http://localhost/path/to/uploads/'; // change to your real folder

// Fetch all mechanics
$sql = "SELECT mechanic_name, email_address, phone_number, mechanic_id, experience, specialization, licence_number, mechanic_image FROM mechanic";
$result = $conn->query($sql);

$mechanics = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $mechanics[] = [
            'mechanic_name'    => $row['mechanic_name'],
            'email_address'    => $row['email_address'],
            'phone_number'     => $row['phone_number'],
            'employee_id'      => $row['mechanic_id'],
            'experience'       => $row['experience'],
            'specialization'   => $row['specialization'],
            'licence_number'   => $row['licence_number'],
            'mechanic_image'   => $row['mechanic_image'] 
                ? $base_url . $row['mechanic_image'] 
                : null
        ];
    }

    echo json_encode(['status' => 'success', 'mechanics' => $mechanics]);
} else {
    echo json_encode(['status' => 'success', 'message' => 'No mechanics found', 'mechanics' => []]);
}

$conn->close();
?>
