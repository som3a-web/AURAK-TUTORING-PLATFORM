<?php
// api/student/get_messages.php

session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$studentId = $_SESSION['user_id'];
$sessionId = $_GET['session_id'] ?? null;

if (!$sessionId) {
    echo json_encode(['success' => false, 'message' => 'Session ID required']);
    exit;
}

try {
    $pdo = getPDO();
    
    // Verify the session belongs to this student
    $checkStmt = $pdo->prepare("SELECT id, tutor_id FROM sessions WHERE id = :session_id AND student_id = :student_id");
    $checkStmt->execute([':session_id' => $sessionId, ':student_id' => $studentId]);
    $session = $checkStmt->fetch();
    
    if (!$session) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Session not found or access denied']);
        exit;
    }
    
    // Get all messages for this session
    $sql = "
        SELECT 
            m.id,
            m.sender_id,
            m.receiver_id,
            m.message,
            m.created_at,
            u.full_name AS sender_name
        FROM messages m
        LEFT JOIN users u ON m.sender_id = u.id
        WHERE m.session_id = :session_id
        ORDER BY m.created_at ASC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':session_id' => $sessionId]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $messages,
        'tutor_id' => $session['tutor_id']
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

