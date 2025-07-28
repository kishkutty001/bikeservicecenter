<?php
header('Content-Type: application/json');
include('db.php');



// Fetch all spare parts
$result = $conn->query("SELECT spare_name, spare_image, spare_price, spare_category, spare_unit FROM spare_parts");

$spares = [];

if ($result) {
    // Optional: base URL for images
    $base_url = 'http://localhost/path/to/uploads/'; // <-- change this to your real path

    while ($row = $result->fetch_assoc()) {
        $spares[] = [
            'spare_name'     => $row['spare_name'],
            'spare_image'    => $base_url . $row['spare_image'],
            'spare_price'    => $row['spare_price'],
            'spare_category' => $row['spare_category'],
            'spare_unit'     => $row['spare_unit']
        ];
    }

    echo json_encode(['status' => 'success', 'spares' => $spares]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch data']);
}

$conn->close();
?>
