<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require 'db.php';

// FIX: The 'rating' column has been removed from the SELECT statement
$sql = "SELECT id, full_name, phone, specialization, experience, license_number, document_image_path FROM mechanics";

$result = $conn->query($sql);

$mechanics = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Build the full image URL
        $imageUrl = null;
        if (!empty($row['document_image_path'])) {
            // This assumes your PHP script is in a subfolder and 'uploads' is parallel to it.
            // Adjust the path if your structure is different.
            $imageUrl = "https://94b8m8gq-80.inc1.devtunnels.ms/repo/bikeservicecenter/" . $row['document_image_path'];
        }
        $row['imageUrl'] = $imageUrl;
        unset($row['document_image_path']); // Remove original path for cleaner JSON
        $mechanics[] = $row;
    }
}

echo json_encode($mechanics);

$conn->close();
?>

