<?php
// /api/tutors/get_available_tutors.php
// Returns a list of approved tutors so admins (and other authenticated roles)
// can assign them to requests. Optional ?course= filter.

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Please log in first.'
    ]);
    exit;
}

$role = strtolower($_SESSION['role'] ?? '');
if (!in_array($role, ['admin', 'tutor', 'student'], true)) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access denied.'
    ]);
    exit;
}

$courseFilter = isset($_GET['course']) ? trim($_GET['course']) : '';

try {
    // First, try with tutor_details table
    try {
        $sql = "
            SELECT 
                u.id          AS tutor_id,
                u.full_name,
                u.email,
                COALESCE(td.subjects, '[]') AS subjects,
                td.bio,
                td.hourly_rate
            FROM users u
            LEFT JOIN tutor_details td ON td.user_id = u.id
            WHERE u.role = 'tutor'
              AND u.tutor_status = 'approved'
        ";

        $params = [];

        if ($courseFilter !== '') {
            $likeFilter = '%' . $courseFilter . '%';
            $sql .= " AND (COALESCE(td.subjects, '') LIKE :courseFilter OR u.full_name LIKE :nameFilter)";
            $params[':courseFilter'] = $likeFilter;
            $params[':nameFilter'] = $likeFilter;
        }

        $sql .= " ORDER BY u.full_name ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $joinError) {
        // If tutor_details table doesn't exist, fall back to users table only
        $sql = "
            SELECT 
                u.id          AS tutor_id,
                u.full_name,
                u.email,
                '[]' AS subjects,
                NULL AS bio,
                NULL AS hourly_rate
            FROM users u
            WHERE u.role = 'tutor'
              AND u.tutor_status = 'approved'
        ";

        $params = [];

        if ($courseFilter !== '') {
            $likeFilter = '%' . $courseFilter . '%';
            $sql .= " AND u.full_name LIKE :nameFilter";
            $params[':nameFilter'] = $likeFilter;
        }

        $sql .= " ORDER BY u.full_name ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        'success' => true,
        'data'    => $rows
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error while loading tutors.',
        'error'   => $e->getMessage()
    ]);
}
