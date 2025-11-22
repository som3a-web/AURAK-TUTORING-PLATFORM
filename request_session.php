<?php
// api/student/request_session.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}



$studentId = $_SESSION['user_id'];

// 2) Read JSON body from fetch()
$data = json_decode(file_get_contents('php://input'), true) ?? [];

$tutorId      = $data['tutor_id']     ?? null;
$courseCode   = $data['course_code']  ?? null;
$meetingDate  = $data['meeting_date'] ?? null;  // e.g. 2025-11-20
$startTime    = $data['start_time']   ?? null;  // e.g. 14:00
$endTime      = $data['end_time']     ?? null;  // (optional, for reference)
$location     = $data['location']     ?? 'Online';
$notes        = $data['notes']        ?? '';

// simple validation
if (!$tutorId || !$meetingDate || !$startTime || !$courseCode) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields (tutor, date, time, course).'
    ]);
    exit;
}

// 3) Combine date + start time into scheduled_at (DATETIME)
$scheduledAt = date('Y-m-d H:i:s', strtotime("$meetingDate $startTime"));

try {
    // We will not use request_id or meeting_link for now -> set NULL
    $sql = "
        INSERT INTO sessions (
            student_id,
            tutor_id,
            course_code,
            request_id,
            scheduled_at,
            location,
            meeting_link,
            status,
            notes
        ) VALUES (
            :student_id,
            :tutor_id,
            :course_code,
            :request_id,
            :scheduled_at,
            :location,
            :meeting_link,
            :status,
            :notes
        )
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':student_id'   => $studentId,
        ':tutor_id'     => $tutorId,
        ':course_code'  => $courseCode,
        ':request_id'   => null,              // no linked public_request for now
        ':scheduled_at' => $scheduledAt,
        ':location'     => $location,
        ':meeting_link' => null,              // later you can fill this with Zoom/Teams link
        ':status'       => 'pending',         // initial session status
        ':notes'        => $notes
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Session request created'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'DB error: ' . $e->getMessage()
    ]);
}
