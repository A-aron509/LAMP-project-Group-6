<?php
require_once '../config/db.php';
require_once '../config/helpers.php';
setCORSHeaders();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['error' => 'Method not allowed']);
}
$body = getRequestBody();
$username = clean($body['username'] ?? '');
$email = clean($body['email'] ?? '');
$password = $body['password'] ?? '';
if (!$username || !$email || !$password) {
    respond(400, ['error' => 'username, email, and password are required']);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(400, ['error' => 'Invalid email address']);
}
if (strlen($password) < 8) {
    respond(400, ['error' => 'Password must be at least 8 characters']);
}
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
try {
    $db = getDB();
    $stmt = $db->prepare('INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :hash)');
    $stmt->execute([':username' => $username, ':email' => $email, ':hash' => $hash]);
    respond(201, ['message' => 'Account created successfully', 'user_id' => (int) $db->lastInsertId()]);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') { respond(409, ['error' => 'Username or email already in use']); }
    respond(500, ['error' => 'Registration failed']);
}
