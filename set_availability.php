<?php
// api/tutors/set_availability.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

// include DB – this gives us $pdo
require_once __DIR__ . '/../../config/db.php';

// ---------- AUTH CHECK ----------
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'No session vars',
        'session' => $_SESSION
    ]);
    exit;
}

if ($_SESSION['role'] !== 'tutor') {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Wrong role (need tutor)',
        'session' => $_SESSION
    ]);
    exit;
}

$tutorId = $_SESSION['user_id'];

// ---------- READ INPUT (supports $_POST and raw body) ----------
$data = $_POST;

// If $_POST is empty, parse raw body (URL-encoded)
if (empty($data)) {
    $raw = file_get_contents('php://input');
    $parsed = [];
    parse_str($raw, $parsed);
    if (!empty($parsed)) {
        $data = $parsed;
    }
}

$dayOfWeekRaw = $data['day_of_week'] ?? null;
$startRaw     = $data['start_time']  ?? null;
$endRaw       = $data['end_time']    ?? null;

if ($dayOfWeekRaw === null || $startRaw === null || $endRaw === null) {
    echo json_encode([
        'success'  => false,
        'message'  => 'Missing fields',
        'received' => $data
    ]);
    exit;
}

$dayOfWeek = (int)$dayOfWeekRaw;
if ($dayOfWeek < 0 || $dayOfWeek > 6) {
    echo json_encode(['success' => false, 'message' => 'Invalid day_of_week']);
    exit;
}

// Convert time to MySQL format HH:MM:SS
$startTime = date('H:i:s', strtotime($startRaw));
$endTime   = date('H:i:s', strtotime($endRaw));

if (!$startTime || !$endTime) {
    echo json_encode(['success' => false, 'message' => 'Invalid time format']);
    exit;
}

// ---------- INSERT INTO DB ----------
try {
    // $pdo comes from config/db.php
    $sql = "
        INSERT INTO tutor_availability (tutor_id, day_of_week, start_time, end_time, is_active)
        VALUES (:tutor_id, :day_of_week, :start_time, :end_time, 1)
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':tutor_id'    => $tutorId,
        ':day_of_week' => $dayOfWeek,
        ':start_time'  => $startTime,
        ':end_time'    => $endTime
    ]);

    echo json_encode(['success' => true, 'message' => 'Availability saved']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
