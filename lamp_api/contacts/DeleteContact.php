<?php
// ============================================================
//  DeleteContact.php
//  DELETE /lamp_api/api/contacts/DeleteContact.php?id=1
//  Requires login session
// ============================================================

require_once '../../config/db.php';
require_once '../../config/helpers.php';

setCORSHeaders();

$userId = requireAuth();
$db     = getDB();

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    respond(405, ['error' => 'Method not allowed']);
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$id) {
    respond(400, ['error' => 'Contact ID required — use ?id=']);
}

$stmt = $db->prepare(
    'DELETE FROM contacts WHERE contact_id = :id AND user_id = :uid'
);
$stmt->execute([':id' => $id, ':uid' => $userId]);

if ($stmt->rowCount() === 0) {
    respond(404, ['error' => 'Contact not found']);
}

respond(200, ['message' => 'Contact deleted successfully']);
