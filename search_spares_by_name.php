<?php
header('Content-Type: application/json');
include('db.php');

// Get search text from POST or GET
$search_text = $_POST['spare_name'] ?? $_GET['spare_name'] ?? null;

if (!$search_text) {
    echo json_encode(['status' => 'error', 'message' => 'spare_name parameter is required']);
    exit;
}

// Prepare SQL query with LIKE for partial match
$stmt = $conn->prepare("SELECT spare_name, spare_image, spare_price, spare_category, spare_unit FROM spare_parts WHERE spare_name LIKE ?");
$like = "%" . $search_text . "%";
$stmt->bind_param("s", $like);
$stmt->execute();

$result = $stmt->get_result();

$spares = [];
// Optional: base URL for images
$base_url = 'http://localhost/path/to/uploads/'; // change this to your actual uploads folder URL

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $spares[] = [
            'spare_name'     => $row['spare_name'],
            'spare_image'    => $base_url . $row['spare_image'],
            'spare_price'    => $row['spare_price'],
            'spare_category' => $row['spare_category'],
            'spare_unit'     => $row['spare_unit']
        ];
    }

    if (empty($spares)) {
        echo json_encode(['status' => 'success', 'message' => 'No data found', 'spares' => []]);
    } else {
        echo json_encode(['status' => 'success', 'spares' => $spares]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch data']);
}

$stmt->close();
$conn->close();
?>
