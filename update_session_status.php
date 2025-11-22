<?php
// api/tutors/update_session_status.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/db.php';

// Must be logged in as tutor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tutor') {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

$tutorId = $_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true) ?? [];

$sessionId   = $data['session_id']   ?? null;
$action      = $data['action']       ?? null;   // "approve" | "reject"
$meetingLink = $data['meeting_link'] ?? null;   // optional
$scheduledAt = $data['scheduled_at'] ?? null;   // optional new datetime

if (!$sessionId || !in_array($action, ['approve','reject'], true)) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing or invalid data'
    ]);
    exit;
}

// Map frontend actions to ENUM values in sessions.status
// approve  -> confirmed
// reject   -> cancelled
$status = ($action === 'approve') ? 'confirmed' : 'cancelled';

try {
    // Build dynamic SQL depending on what we want to update
    $fields = ['status = :status'];
    $params = [
        ':status'     => $status,
        ':session_id' => $sessionId,
        ':tutor_id'   => $tutorId
    ];

    if ($meetingLink !== null && $meetingLink !== '') {
        $fields[] = 'meeting_link = :meeting_link';
        $params[':meeting_link'] = $meetingLink;
    }

    if ($scheduledAt !== null && $scheduledAt !== '') {
        $fields[] = 'scheduled_at = :scheduled_at';
        $params[':scheduled_at'] = date('Y-m-d H:i:s', strtotime($scheduledAt));
    }

    $sql = "UPDATE sessions
            SET " . implode(', ', $fields) . "
            WHERE id = :session_id AND tutor_id = :tutor_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    if ($stmt->rowCount() === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Session not found or not yours'
        ]);
        exit;
    }

    echo json_encode([
        'success'    => true,
        'message'    => 'Session status updated',
        'new_status' => $status
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'DB error: ' . $e->getMessage()
    ]);
}
