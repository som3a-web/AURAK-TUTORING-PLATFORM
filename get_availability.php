<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/db.php';

// Only tutors can view their availability
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
    $stmt = $pdo->prepare("
        SELECT id, day_of_week, start_time, end_time, is_active
        FROM tutor_availability
        WHERE tutor_id = :tutor_id
        ORDER BY day_of_week, start_time
    ");
    
    $stmt->execute([':tutor_id' => $tutorId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data'    => $rows
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);
}
