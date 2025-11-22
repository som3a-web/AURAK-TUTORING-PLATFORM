<?php
// api/student/send_message.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/db.php';

// 1) Must be logged in as student
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

$sessionId  = $data['session_id']  ?? null;     // which tutoring session
$receiverId = $data['receiver_id'] ?? null;     // tutor id
$message    = trim($data['message'] ?? '');

if (!$sessionId || !$receiverId || $message === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields (session, receiver, message).'
    ]);
    exit;
}

try {
    // ⚠️ Adjust table/column names if your DB is slightly different
    $sql = "
        INSERT INTO messages (
            session_id,
            sender_id,
            receiver_id,
            message,
            created_at
        ) VALUES (
            :session_id,
            :sender_id,
            :receiver_id,
            :message,
            NOW()
        )
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':session_id'  => $sessionId,
        ':sender_id'   => $studentId,
        ':receiver_id' => $receiverId,
        ':message'     => $message
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Message sent'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'DB error: ' . $e->getMessage()
    ]);
}
