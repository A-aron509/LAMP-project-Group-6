<?php
// ============================================================
//  api/auth/logout.php
//  POST /api/auth/logout
// ============================================================

require_once '../../config/helpers.php';

setCORSHeaders();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = [];
session_destroy();

respond(200, ['message' => 'Logged out successfully']);
