<?php
// /SE.PROJECT/api/admin/pending_tutors.php

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../../config/db.php'; // $pdo

    // We join tutor_applications with users to get name + email
    // Your users table has full_name; we split it into first_name / last_name
    $sql = "
        SELECT 
            ta.user_id,
            ta.major,
            ta.gpa,
            ta.courses_to_tutor,
            ta.motivation_essay,
            ta.applied_at,
            u.full_name,
            u.email,
            SUBSTRING_INDEX(u.full_name, ' ', 1) AS first_name,
            TRIM(
                SUBSTRING(
                    u.full_name,
                    LENGTH(SUBSTRING_INDEX(u.full_name, ' ', 1)) + 1
                )
            ) AS last_name
        FROM tutor_applications ta
        JOIN users u ON ta.user_id = u.id
        WHERE ta.status = 'pending'
        ORDER BY ta.applied_at DESC
    ";

    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data'    => $rows
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error in pending_tutors.php: ' . $e->getMessage()
    ]);
}
