<?php
// /SE.PROJECT/api/admin/finalize_assignment.php

require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json; charset=utf-8');

// Read JSON body from fetch()
$input = json_decode(file_get_contents('php://input'), true) ?? [];

// 1) Basic validation
if (!isset($input['request_id'], $input['tutor_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Missing request_id or tutor_id'
    ]);
    exit;
}

$requestId = (int)$input['request_id'];
$tutorId   = (int)$input['tutor_id'];
$notes     = isset($input['notes']) ? trim($input['notes']) : '';

if ($requestId <= 0 || $tutorId <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request_id or tutor_id'
    ]);
    exit;
}

try {
    // 2) Start transaction
    $pdo->beginTransaction();

    // 3) Fetch the public request row
    $stmt = $pdo->prepare("
        SELECT *
        FROM public_requests
        WHERE id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $requestId]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        $pdo->rollBack();
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Public request not found'
        ]);
        exit;
    }

    // Get course_code from the request (adjust if your column name is different)
    $courseCode = $request['course_code'] ?? null;

    // Try to use a linked student_id if your table has that column
    $studentId = null;
    if (array_key_exists('student_id', $request) && $request['student_id'] !== null) {
        $studentId = (int)$request['student_id'];
    }

    // 4) Insert a new session
    $insert = $pdo->prepare("
        INSERT INTO sessions (
            student_id,
            tutor_id,
            course_code,
            request_id,
            status,
            notes
        ) VALUES (
            :student_id,
            :tutor_id,
            :course_code,
            :request_id,
            :status,
            :notes
        )
    ");

    $insert->execute([
        ':student_id' => $studentId,        // can be NULL
        ':tutor_id'   => $tutorId,
        ':course_code'=> $courseCode ?? 'UNKNOWN',
        ':request_id' => $requestId,
        ':status'     => 'pending',         // initial session status
        ':notes'      => $notes
    ]);

    $sessionId = $pdo->lastInsertId();

    // 5) Update public_requests: mark as Assigned and set assigned_tutor_id
    $update = $pdo->prepare("
        UPDATE public_requests
        SET status = :status,
            assigned_tutor_id = :tutor_id
        WHERE id = :id
    ");

    $update->execute([
        ':status'  => 'Assigned',   // must match ENUM value
        ':tutor_id'=> $tutorId,
        ':id'      => $requestId
    ]);

    // 6) Commit
    $pdo->commit();

    echo json_encode([
        'success'    => true,
        'message'    => 'Tutor assigned and session created successfully.',
        'session_id' => $sessionId
    ]);

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error during assignment.',
        'error'   => $e->getMessage() // for debugging; you can remove later
    ]);
}
