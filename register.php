<?php
require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['full_name'], $data['email'], $data['password'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Missing fields']);
    exit;
}

$full_name = trim($data['full_name']);
$email = trim($data['email']);
$password = password_hash($data['password'], PASSWORD_DEFAULT);
$role = $data['role'] ?? 'student';

try {
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$full_name, $email, $password, $role]);

    echo json_encode(['ok' => true, 'message' => 'Registration successful']);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(['ok' => false, 'error' => 'Email already exists']);
    } else {
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    }
}
?>
