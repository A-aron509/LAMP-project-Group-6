<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'db.php';

$inData = getRequestInfo();

$username = trim($inData["username"] ?? '');
$password = $inData["password"] ?? '';

// Validation
if (empty($username) || empty($password)) {
    returnWithError("Username and password are required");
}

if (strlen($username) < 3) {
    returnWithError("Username must be at least 3 characters");
}

if (strlen($password) < 6) {
    returnWithError("Password must be at least 6 characters");
}

// Check if username already exists
$stmt = $conn->prepare("SELECT id FROM Users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt->close();
    returnWithError("Username already taken");
}
$stmt->close();

// Hash password (this handles salting automatically)
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$stmt = $conn->prepare("INSERT INTO Users (username, password_hash) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $passwordHash);

if ($stmt->execute()) {
    $userId = $conn->insert_id;
    $stmt->close();
    $conn->close();
    returnWithInfo([
        'success' => true,
        'message' => 'Registration successful',
        'userId' => $userId
    ]);
} else {
    $stmt->close();
    $conn->close();
    returnWithError("Registration failed");
}
?>

