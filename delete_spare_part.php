<?php
header('Content-Type: application/json');
include('db.php');

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID is required']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM spare_parts WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Spare part deleted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Delete failed: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
