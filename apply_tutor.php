<?php
// /SE.PROJECT/api/user/apply_tutor.php

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../../config/db.php'; // $pdo

    $input = json_decode(file_get_contents('php://input'), true);

    $userId          = isset($input['user_id']) ? (int)$input['user_id'] : 0;
    $major           = trim($input['major'] ?? '');
    $gpa             = $input['gpa'] ?? null;
    $coursesToTutor  = trim($input['courses_to_tutor'] ?? '');
    $motivationEssay = trim($input['motivation_essay'] ?? '');

    if (!$userId || $major === '' || $coursesToTutor === '' || $motivationEssay === '') {
        throw new Exception('Missing required fields.');
    }

    // (Optional) check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = :uid LIMIT 1");
    $stmt->execute([':uid' => $userId]);
    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception('User not found.');
    }

    // If a pending application already exists, update it instead of inserting duplicate
    $stmt = $pdo->prepare("
        SELECT id FROM tutor_applications 
        WHERE user_id = :uid AND status = 'pending'
        LIMIT 1
    ");
    $stmt->execute([':uid' => $userId]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $stmt = $pdo->prepare("
            UPDATE tutor_applications
            SET major = :major,
                gpa = :gpa,
                courses_to_tutor = :courses,
                motivation_essay = :essay,
                applied_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $stmt->execute([
            ':major'   => $major,
            ':gpa'     => $gpa,
            ':courses' => $coursesToTutor,
            ':essay'   => $motivationEssay,
            ':id'      => $existing['id']
        ]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO tutor_applications 
                (user_id, major, gpa, courses_to_tutor, motivation_essay, status)
            VALUES 
                (:uid, :major, :gpa, :courses, :essay, 'pending')
        ");
        $stmt->execute([
            ':uid'     => $userId,
            ':major'   => $major,
            ':gpa'     => $gpa,
            ':courses' => $coursesToTutor,
            ':essay'   => $motivationEssay
        ]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Your tutor application has been submitted and is pending review.'
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error in apply_tutor.php: ' . $e->getMessage()
    ]);
}
