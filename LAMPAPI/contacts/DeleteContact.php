<?php
require_once '../config/db.php';
require_once '../config/helpers.php';

setCORSHeaders();

$userId = requireAuth();
$db     = getDB();

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    respond(405, ['error' => 'Method not allowed']);
}

$contactId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$contactId) {
    respond(400, ['error' => 'Contact ID required — use ?id=1']);
}

$stmt = $db->prepare(
    'DELETE FROM contacts WHERE contact_id = :cid AND user_id = :uid'
);
$stmt->execute([':cid' => $contactId, ':uid' => $userId]);

if ($stmt->rowCount() === 0) {
    respond(404, ['error' => 'Contact not found or not yours']);
}

respond(200, ['message' => 'Contact deleted successfully']);
