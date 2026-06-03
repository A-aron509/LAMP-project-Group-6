<?php
// ============================================================
//  EditContact.php
//  PUT /lamp_api/api/contacts/EditContact.php?id=1
//  Body: { "first_name", "last_name", "email", "phone", "notes" }
//  Requires login session
// ============================================================

require_once '../../config/db.php';
require_once '../../config/helpers.php';

setCORSHeaders();

$userId = requireAuth();
$db     = getDB();

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    respond(405, ['error' => 'Method not allowed']);
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$id) {
    respond(400, ['error' => 'Contact ID required — use ?id=']);
}

// Make sure contact belongs to this user
$check = $db->prepare(
    'SELECT contact_id FROM contacts WHERE contact_id = :id AND user_id = :uid LIMIT 1'
);
$check->execute([':id' => $id, ':uid' => $userId]);
if (!$check->fetch()) {
    respond(404, ['error' => 'Contact not found']);
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

$stmt = $db->prepare(
    'UPDATE contacts
     SET first_name = :fn,
         last_name  = :ln,
         email      = :email,
         phone      = :phone,
         notes      = :notes
     WHERE contact_id = :id AND user_id = :uid'
);
$stmt->execute([
    ':fn'    => $firstName,
    ':ln'    => $lastName,
    ':email' => $email ?: null,
    ':phone' => $phone ?: null,
    ':notes' => $notes ?: null,
    ':id'    => $id,
    ':uid'   => $userId,
]);

respond(200, ['message' => 'Contact updated successfully']);
