<?php
// create_admin.php  (TEMPORARY SCRIPT – delete after use)

require_once __DIR__ . '/../../config/db.php'; 
// ^ adjust the path if this file is not in api/auth

// 1) CHOOSE ADMIN DETAILS
$full_name = 'Ismaail Al Lahham123';
$email     = 'admin@example.com';      // <--- change to real admin email
$password  = 'Admin123!';              // <--- choose a strong password

// 2) HASH PASSWORD
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// 3) INSERT INTO users TABLE AS ADMIN
$stmt = $pdo->prepare("
    INSERT INTO users (full_name, email, password_hash, role, tutor_status)
    VALUES (?, ?, ?, 'admin', NULL)
");

try {
    $stmt->execute([$full_name, $email, $password_hash]);
    echo "✅ Admin user created successfully!<br>";
    echo "Email: {$email}<br>";
    echo "Password: {$password}<br>";
} catch (PDOException $e) {
    echo "❌ Error creating admin: " . $e->getMessage();
}
