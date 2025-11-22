<?php
// api/student/get_upcoming_sessions.php

session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$studentId = $_SESSION['user_id'];

try {
    $pdo = getPDO();

    $sql = "
        SELECT 
            s.id,
            s.tutor_id,
            s.course_code,
            s.request_id,
            s.scheduled_at,
            s.location,
            s.meeting_link,
            s.status,
            s.notes,
            s.created_at,
            u.full_name AS tutor_name,
            u.email      AS tutor_email
        FROM sessions s
        LEFT JOIN users u ON s.tutor_id = u.id
        WHERE 
            s.student_id = :student_id
            AND s.scheduled_at IS NOT NULL
            AND s.status IN ('scheduled', 'confirmed', 'pending')
        ORDER BY s.scheduled_at ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':student_id' => $studentId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error while loading upcoming sessions'
    ]);
}

