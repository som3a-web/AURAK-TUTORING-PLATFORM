<?php
// api/tutors/delete_availability.php

session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tutor') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$tutorId = $_SESSION['user_id'];
$slotId  = isset($_POST['id']) ? intval($_POST['id']) : null;

if (!$slotId) {
    echo json_encode(['success' => false, 'message' => 'Missing id']);
    exit;
}

try {
    $pdo = getPDO();

    $stmt = $pdo->prepare("
        DELETE FROM tutor_availability
        WHERE id = :id AND tutor_id = :tutor_id
    ");
    $stmt->execute([
        ':id'       => $slotId,
        ':tutor_id' => $tutorId
    ]);

    echo json_encode(['success' => true, 'message' => 'Availability deleted']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
