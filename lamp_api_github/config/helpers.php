<?php
// ============================================================
//  config/helpers.php — Shared utilities
// ============================================================

// ── CORS headers (adjust origin for your domain in production)
function setCORSHeaders(): void {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json; charset=UTF-8');

    // Pre-flight request — browsers send OPTIONS first
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

// ── Send JSON response and stop execution
function respond(int $status, array $data): void {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

// ── Parse JSON request body
function getRequestBody(): array {
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);
    return is_array($data) ? $data : [];
}

// ── Require a logged-in session; reject with 401 if not
function requireAuth(): int {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['user_id'])) {
        respond(401, ['error' => 'Unauthorized — please log in']);
    }
    return (int) $_SESSION['user_id'];
}

// ── Sanitize a string value from input
function clean(?string $value): string {
    return htmlspecialchars(trim($value ?? ''), ENT_QUOTES, 'UTF-8');
}
