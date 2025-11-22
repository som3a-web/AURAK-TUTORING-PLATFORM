<?php
session_start();  // ✅ create / resume PHP session

require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json; charset=utf-8');

// Read JSON body
$data = json_decode(file_get_contents("php://input"), true) ?? [];

// Basic validation
if (empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode([
        'ok'    => false,
        'error' => 'Missing email or password'
    ]);
    exit;
}

$email    = trim($data['email']);
$password = (string)$data['password'];

// Fetch user by email
$stmt = $pdo->prepare("
    SELECT id, full_name, email, password_hash, role, tutor_status
    FROM users
    WHERE email = ?
    LIMIT 1
");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verify password and return full user info
if ($user && password_verify($password, $user['password_hash'])) {

    $_SESSION['user_id']      = (int)$user['id'];
    $_SESSION['role']         = strtolower($user['role']);  // always 'student' / 'tutor' / 'admin'
    $_SESSION['full_name']    = $user['full_name'];
    $_SESSION['tutor_status'] = $user['tutor_status'];
    

echo json_encode([
    'ok'      => true,
    'message' => 'Login successful',
    'user'    => [
        'id'           => (int)$user['id'],
        'full_name'    => $user['full_name'],
        'email'        => $user['email'],
        'role'         => strtolower($user['role']),   // 👈
        'tutor_status' => $user['tutor_status'],
    ]
]);

} else {
    http_response_code(401);
    echo json_encode([
        'ok'    => false,
        'error' => 'Invalid credentials'
    ]);
}
