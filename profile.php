<?php
session_start();
header('Content-Type: application/json');
include('db.php'); // adjust path to your DB connection

// Step 1: Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized: Please log in first."]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Step 2: Fetch user details
$stmt = $conn->prepare("SELECT id, name, email, mobile, user_type FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Step 3: Build response based on user_type
    $profile = [
        "id"        => $user['id'],
        "name"      => $user['name'],
        "email"     => $user['email'],
        "mobile"    => $user['mobile'],
        "user_type" => $user['user_type'],
    ];

    // Optional: if mechanic, get extra mechanic details
    if ($user['user_type'] === 'mechanic') {
        $stmt2 = $conn->prepare("SELECT mechanic_id, experience, specialization, licence_number, address, mechanic_image 
                                 FROM mechanic WHERE email_address = ?");
        $stmt2->bind_param("s", $user['email']);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($result2 && $result2->num_rows > 0) {
            $mechanicData = $result2->fetch_assoc();
            // merge mechanic data into profile
            $profile = array_merge($profile, $mechanicData);
        }
        $stmt2->close();
    }

    echo json_encode(["status" => "success", "profile" => $profile]);

} else {
    echo json_encode(["status" => "error", "message" => "User not found."]);
}

$stmt->close();
$conn->close();
?>
