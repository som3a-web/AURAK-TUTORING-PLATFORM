<?php
// api/tutors/update_session_schedule.php

session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

// Must be logged in as tutor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tutor') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$tutorId = $_SESSION['user_id'];

// Read POST data (can be form or AJAX)
$sessionId   = isset($_POST['session_id'])   ? intval($_POST['session_id']) : null;
$date        = isset($_POST['date'])         ? trim($_POST['date'])         : null; // YYYY-MM-DD
$time        = isset($_POST['time'])         ? trim($_POST['time'])         : null; // HH:MM
$location    = isset($_POST['location'])     ? trim($_POST['location'])     : null;
$meetingLink = isset($_POST['meeting_link']) ? trim($_POST['meeting_link']) : null;
$notes       = isset($_POST['notes'])        ? trim($_POST['notes'])        : null;

if (!$sessionId || !$date || !$time || !$location) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Build DATETIME: "2025-11-20 14:00:00"
$scheduledAt = $date . ' ' . $time . ':00';

try {
    $pdo = getPDO();

    // Optionally: check the session really belongs to this tutor
    $check = $pdo->prepare("
        SELECT id, status 
        FROM sessions 
        WHERE id = :id AND tutor_id = :tutor_id
    ");
    $check->execute([
        ':id'       => $sessionId,
        ':tutor_id' => $tutorId
    ]);
    $session = $check->fetch(PDO::FETCH_ASSOC);

    if (!$session) {
        echo json_encode(['success' => false, 'message' => 'Session not found']);
        exit;
    }

    // Update session schedule
    $stmt = $pdo->prepare("
        UPDATE sessions
        SET 
            scheduled_at = :scheduled_at,
            location     = :location,
            meeting_link = :meeting_link,
            notes        = :notes,
            status       = 'scheduled'
        WHERE id = :id AND tutor_id = :tutor_id
    ");

    $stmt->execute([
        ':scheduled_at' => $scheduledAt,
        ':location'     => $location,
        ':meeting_link' => $meetingLink,
        ':notes'        => $notes,
        ':id'           => $sessionId,
        ':tutor_id'     => $tutorId
    ]);

    echo json_encode(['success' => true, 'message' => 'Session scheduled successfully']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error while scheduling session'
    ]);
}
