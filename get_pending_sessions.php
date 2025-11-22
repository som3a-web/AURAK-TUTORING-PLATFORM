<?php
// api/tutors/get_pending_sessions.php

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

try {
    $pdo = getPDO();   // same helper as other API files

    $stmt = $pdo->prepare("
        SELECT
            id,
            student_id,
            course_code,
            scheduled_at,
            notes,
            status
        FROM sessions
        WHERE tutor_id = :tid
          AND status = 'pending'
        ORDER BY created_at DESC
    ");
    $stmt->execute([':tid' => $tutorId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data'    => $rows
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error',
        'error'   => $e->getMessage()
    ]);
}
