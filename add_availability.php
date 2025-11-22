<?php
// api/tutors/add_availability.php

session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tutor') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$tutorId = $_SESSION['user_id'];

// Expect day_of_week, start_time, end_time from POST (can be form or JSON)
$dayOfWeek  = isset($_POST['day_of_week'])  ? intval($_POST['day_of_week'])  : null;
$startTime  = isset($_POST['start_time'])   ? $_POST['start_time']           : null;
$endTime    = isset($_POST['end_time'])     ? $_POST['end_time']             : null;

if ($dayOfWeek === null || $startTime === null || $endTime === null) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

if ($dayOfWeek < 0 || $dayOfWeek > 6) {
    echo json_encode(['success' => false, 'message' => 'Invalid day_of_week']);
    exit;
}

try {
    $pdo = getPDO();

    $stmt = $pdo->prepare("
        INSERT INTO tutor_availability (tutor_id, day_of_week, start_time, end_time, is_active)
        VALUES (:tutor_id, :day_of_week, :start_time, :end_time, 1)
    ");
    $stmt->execute([
        ':tutor_id'   => $tutorId,
        ':day_of_week'=> $dayOfWeek,
        ':start_time' => $startTime,
        ':end_time'   => $endTime
    ]);

    echo json_encode(['success' => true, 'message' => 'Availability added']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
