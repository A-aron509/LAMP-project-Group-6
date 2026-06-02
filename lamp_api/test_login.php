<?php
require_once 'config/db.php';

$db = getDB();
$stmt = $db->prepare('SELECT password_hash FROM users WHERE email = :email LIMIT 1');
$stmt->execute([':email' => 'test@test.com']);
$user = $stmt->fetch();

echo "Hash from DB: " . $user['password_hash'] . "<br>";
echo "Verify result: " . (password_verify('password123', $user['password_hash']) ? 'TRUE' : 'FALSE');
?>