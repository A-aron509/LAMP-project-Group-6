<?php
require_once '../config/db.php';
require_once '../config/helpers.php';

setCORSHeaders();

$userId = requireAuth();
$db     = getDB();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    respond(405, ['error' => 'Method not allowed']);
}

$search = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($search === '') {
    respond(400, ['error' => 'Search term required — use ?q=searchterm']);
}

$like = '%' . $search . '%';

$stmt = $db->prepare(
    'SELECT contact_id, first_name, last_name, email, phone, notes, created_at
     FROM contacts
     WHERE user_id = :uid
       AND (
         first_name LIKE :q1
         OR last_name  LIKE :q2
         OR email      LIKE :q3
         OR phone      LIKE :q4
         OR CONCAT(first_name, \' \', last_name) LIKE :q5
       )
     ORDER BY first_name, last_name'
);

$stmt->execute([
    ':uid' => $userId,
    ':q1'  => $like,
    ':q2'  => $like,
    ':q3'  => $like,
    ':q4'  => $like,
    ':q5'  => $like,
]);

respond(200, $stmt->fetchAll());
