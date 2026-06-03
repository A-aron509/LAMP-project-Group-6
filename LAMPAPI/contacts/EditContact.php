<?php
require_once '../config/db.php';
require_once '../config/helpers.php';

setCORSHeaders();

$userId = requireAuth();
$db     = getDB();

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    respond(405, ['error' => 'Method not allowed']);
}

$contactId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$contactId) {
    respond(400, ['error' => 'Contact ID required — use ?id=1']);
}

$body      = getRequestBody();
$firstName = clean($body['first_name'] ?? '');
$lastName  = clean($body['last_name']  ?? '');
$email     = clean($body['email']      ?? '');
$phone     = clean($body['phone']      ?? '');
$notes     = clean($body['notes']      ?? '');

if (!$firstName || !$lastName) {
    respond(400, ['error' => 'first_name and last_name are required']);
}

$stmt = $db->prepare(
    'UPDATE contacts SET first_name=:fn, last_name=:ln, email=:email, phone=:phone, notes=:notes
     WHERE contact_id=:cid AND user_id=:uid'
);
$stmt->execute([
    ':fn'    => $firstName,
    ':ln'    => $lastName,
    ':email' => $email ?: null,
    ':phone' => $phone ?: null,
    ':notes' => $notes ?: null,
    ':cid'   => $contactId,
    ':uid'   => $userId,
]);

if ($stmt->rowCount() === 0) {
    respond(404, ['error' => 'Contact not found or not yours']);
}

respond(200, ['message' => 'Contact updated successfully']);
