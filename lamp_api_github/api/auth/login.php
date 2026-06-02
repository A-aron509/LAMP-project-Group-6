<?php
// ============================================================
//  api/auth/login.php
//  POST /api/auth/login
//  Body: { "email": "", "password": "" }
// ============================================================

require_once '../../config/db.php';
require_once '../../config/helpers.php';

setCORSHeaders();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['error' => 'Method not allowed']);
}

$body = getRequestBody();

$email    = clean($body['email']    ?? '');
$password =        $body['password'] ?? '';

// ── Validation ───────────────────────────────────────────────
if (!$email || !$password) {
    respond(400, ['error' => 'email and password are required']);
}

// ── Look up user ─────────────────────────────────────────────
$db   = getDB();
$stmt = $db->prepare('SELECT user_id, username, password_hash FROM users WHERE email = :email LIMIT 1');
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

// ── Verify password (timing-safe) ───────────────────────────
if (!$user || !password_verify($password, $user['password_hash'])) {
    respond(401, ['error' => 'Invalid email or password']);
}

// ── Start session ────────────────────────────────────────────
session_regenerate_id(true); // prevent session fixation
$_SESSION['user_id']  = $user['user_id'];
$_SESSION['username'] = $user['username'];

respond(200, [
    'message'  => 'Login successful',
    'user_id'  => $user['user_id'],
    'username' => $user['username'],
]);
