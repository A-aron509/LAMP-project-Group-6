<?php
require_once '../config/db.php';
require_once '../config/helpers.php';

setCORSHeaders();

$userId = requireAuth();
$db     = getDB();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['error' => 'Method not allowed']);
}

$body = getRequestBody();
$firstName = clean($body['first_name'] ?? '');
$lastName  = clean($body['last_name']  ?? '');
$email     = clean($body['email']      ?? '');
$phone     = clean($body['phone']      ?? '');
$notes     = clean($body['notes']      ?? '');

if (!$firstName || !$lastName) {
    respond(400, ['error' => 'first_name and last_name are required']);
}

if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(400, ['error' => 'Invalid email address']);
}

$stmt = $db->prepare('INSERT INTO contacts (user_id, first_name, last_name, email, phone, notes) VALUES (:uid, :fn, :ln, :email, :phone, :notes)');
$stmt->execute([
    ':uid'   => $userId,
    ':fn'    => $firstName,
    ':ln'    => $lastName,
    ':email' => $email ?: null,
    ':phone' => $phone ?: null,
    ':notes' => $notes ?: null,
]);

respond(201, ['message' => 'Contact added successfully', 'contact_id' => (int) $db->lastInsertId()]);
