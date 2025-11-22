<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);

    $userId = (int)($input['user_id'] ?? 0);
    $action = strtolower($input['action'] ?? '');

    if (!$userId || !in_array($action, ['approve','reject'])) {
        throw new Exception("Invalid request.");
    }

    if ($action === 'approve') {

        // 1) Mark application approved
        $pdo->prepare("
            UPDATE tutor_applications
            SET status = 'approved'
            WHERE user_id = ?
        ")->execute([$userId]);

        // 2) Update user's role + tutor status
        $pdo->prepare("
            UPDATE users
            SET role = 'tutor',
                tutor_status = 'approved'
            WHERE id = ?
        ")->execute([$userId]);

        echo json_encode([
            "success" => true,
            "message" => "Tutor approved successfully"
        ]);
        exit;
    }

    if ($action === 'reject') {

        // 1) Mark rejected
        $pdo->prepare("
            UPDATE tutor_applications
            SET status = 'rejected'
            WHERE user_id = ?
        ")->execute([$userId]);

        // 2) Only update tutor_status
        $pdo->prepare("
            UPDATE users
            SET tutor_status = 'rejected'
            WHERE id = ?
        ")->execute([$userId]);

        echo json_encode([
            "success" => true,
            "message" => "Tutor application rejected"
        ]);
        exit;
    }

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Server error: " . $e->getMessage()
    ]);
}
