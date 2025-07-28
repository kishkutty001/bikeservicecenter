<?php
header('Content-Type: application/json');
include('db.php'); // your db connection file

// Get mechanic id from POST or GET
$id = $_POST['id'] ?? $_GET['id'] ?? null;

// Validate id
if (empty($id)) {
    echo json_encode(['status' => 'error', 'message' => 'Mechanic id is required']);
    exit;
}

// Prepare and execute delete query
$stmt = $conn->prepare("DELETE FROM mechanic WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Mechanic deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Mechanic not found or already deleted']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Delete failed: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
