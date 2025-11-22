<?php
// /api/user/profile.php
// GET  -> returns the authenticated user's profile
// PUT  -> updates editable profile fields (full_name, email, password)

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Not authenticated.']);
    exit;
}

$userId = (int) $_SESSION['user_id'];

function fetchUser(PDO $pdo, int $userId): array {
    $stmt = $pdo->prepare("
        SELECT id, full_name, email, role, tutor_status
        FROM users
        WHERE id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['message' => 'User not found.']);
        exit;
    }

    return $user;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    echo json_encode(fetchUser($pdo, $userId));
    exit;
}

if ($method === 'PUT') {
    $payload = json_decode(file_get_contents('php://input'), true);
    if (!is_array($payload)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid JSON payload.']);
        exit;
    }

    $fields = [];
    $params = [':id' => $userId];

    if (array_key_exists('full_name', $payload)) {
        $fullName = trim($payload['full_name'] ?? '');
        if (mb_strlen($fullName) < 2) {
            http_response_code(422);
            echo json_encode(['message' => 'Full name must be at least 2 characters.']);
            exit;
        }
        $fields[] = 'full_name = :full_name';
        $params[':full_name'] = $fullName;
    }

    if (array_key_exists('email', $payload)) {
        $email = trim($payload['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode(['message' => 'Invalid email address.']);
            exit;
        }

        $check = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id <> :id LIMIT 1");
        $check->execute([':email' => $email, ':id' => $userId]);
        if ($check->fetch(PDO::FETCH_ASSOC)) {
            http_response_code(409);
            echo json_encode(['message' => 'That email is already in use.']);
            exit;
        }

        $fields[] = 'email = :email';
        $params[':email'] = $email;
    }

    if (array_key_exists('password', $payload) && $payload['password'] !== '') {
        $password = (string) $payload['password'];
        if (strlen($password) < 6) {
            http_response_code(422);
            echo json_encode(['message' => 'Password must be at least 6 characters.']);
            exit;
        }
        $fields[] = 'password_hash = :password_hash';
        $params[':password_hash'] = password_hash($password, PASSWORD_DEFAULT);
    }

    if (empty($fields)) {
        http_response_code(400);
        echo json_encode(['message' => 'No valid fields to update.']);
        exit;
    }

    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $updatedUser = fetchUser($pdo, $userId);
    $_SESSION['full_name'] = $updatedUser['full_name'];

    echo json_encode([
        'success' => true,
        'user'    => $updatedUser
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['message' => 'Method not allowed.']);

