<?php
// ============================================================
//  api/contacts/index.php  — Contacts CRUD + Search
//
//  GET    /api/contacts          — list all contacts for user
//  GET    /api/contacts?q=term   — partial search (server-side)
//  GET    /api/contacts?id=1     — get single contact
//  POST   /api/contacts          — create contact
//  PUT    /api/contacts?id=1     — update contact
//  DELETE /api/contacts?id=1     — delete contact
// ============================================================

require_once '../../config/db.php';
require_once '../../config/helpers.php';

setCORSHeaders();

// All contact routes require a logged-in user
$userId = requireAuth();
$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// ── Route by HTTP method ─────────────────────────────────────
switch ($method) {

    // ── GET: search or list or single ────────────────────────
    case 'GET':
        $id      = isset($_GET['id']) ? (int) $_GET['id'] : null;
        $search  = isset($_GET['q'])  ? trim($_GET['q'])  : null;

        // Single contact by ID
        if ($id) {
            $stmt = $db->prepare(
                'SELECT contact_id, first_name, last_name, email, phone, notes, created_at, updated_at
                 FROM contacts
                 WHERE contact_id = :id AND user_id = :uid
                 LIMIT 1'
            );
            $stmt->execute([':id' => $id, ':uid' => $userId]);
            $contact = $stmt->fetch();

            if (!$contact) {
                respond(404, ['error' => 'Contact not found']);
            }
            respond(200, $contact);
        }

        // Search (partial match on name, email, phone)
        if ($search !== null && $search !== '') {
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
        }

        // List all contacts (no search term)
        $stmt = $db->prepare(
            'SELECT contact_id, first_name, last_name, email, phone, notes, created_at
             FROM contacts
             WHERE user_id = :uid
             ORDER BY first_name, last_name'
        );
        $stmt->execute([':uid' => $userId]);
        respond(200, $stmt->fetchAll());
        break;

    // ── POST: create contact ─────────────────────────────────
    case 'POST':
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
            'INSERT INTO contacts (user_id, first_name, last_name, email, phone, notes)
             VALUES (:uid, :fn, :ln, :email, :phone, :notes)'
        );
        $stmt->execute([
            ':uid'   => $userId,
            ':fn'    => $firstName,
            ':ln'    => $lastName,
            ':email' => $email  ?: null,
            ':phone' => $phone  ?: null,
            ':notes' => $notes  ?: null,
        ]);

        respond(201, [
            'message'    => 'Contact created',
            'contact_id' => (int) $db->lastInsertId(),
        ]);
        break;

    // ── PUT: update contact ───────────────────────────────────
    case 'PUT':
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!$id) {
            respond(400, ['error' => 'Contact ID is required — use ?id=']);
        }

        // Confirm contact belongs to this user
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
            ':email' => $email  ?: null,
            ':phone' => $phone  ?: null,
            ':notes' => $notes  ?: null,
            ':id'    => $id,
            ':uid'   => $userId,
        ]);

        respond(200, ['message' => 'Contact updated']);
        break;

    // ── DELETE: delete contact ────────────────────────────────
    case 'DELETE':
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!$id) {
            respond(400, ['error' => 'Contact ID is required — use ?id=']);
        }

        $stmt = $db->prepare(
            'DELETE FROM contacts WHERE contact_id = :id AND user_id = :uid'
        );
        $stmt->execute([':id' => $id, ':uid' => $userId]);

        if ($stmt->rowCount() === 0) {
            respond(404, ['error' => 'Contact not found']);
        }

        respond(200, ['message' => 'Contact deleted']);
        break;

    default:
        respond(405, ['error' => 'Method not allowed']);
}
