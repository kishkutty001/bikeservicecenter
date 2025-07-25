<?php
include 'db.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name      = trim($_POST['name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = trim($_POST['password'] ?? '');
    $mobile    = trim($_POST['mobile'] ?? '');
    $user_type = trim($_POST['user_type'] ?? '');

    // 1. Empty field check
    if (empty($name) || empty($email) || empty($password) || empty($mobile) || empty($user_type)) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    // 2. Email format validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Invalid email format."]);
        exit;
    }

    // 3. Mobile number validation (Only digits, 10 to 15 characters)
    if (!preg_match('/^[0-9]{10,15}$/', $mobile)) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Invalid mobile number."]);
        exit;
    }

    // 4. Check if email already exists
    $check = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $check);

    if (mysqli_num_rows($result) > 0) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Email already registered."]);
    } else {
        $sql = "INSERT INTO users (name, email, password, mobile, user_type) 
                VALUES ('$name', '$email', '$password', '$mobile', '$user_type')";

        if (mysqli_query($conn, $sql)) {
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Registration successful."]);
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Database error: " . mysqli_error($conn)]);
        }
    }
}
?>
