<?php
// config/db.php - Works on both Local (XAMPP) and Render
// This file automatically detects the environment and uses the correct database

// (Optional) Show errors while developing (disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if we're on Render (has DATABASE_URL or JAWSDB_URL)
$database_url = getenv('DATABASE_URL'); // PostgreSQL on Render
$jawsdb_url = getenv('JAWSDB_URL');     // MySQL on Render (alternative)

if ($database_url) {
    // Render PostgreSQL connection
    $url = parse_url($database_url);
    $host = $url['host'];
    $port = $url['port'] ?? 5432;
    $dbname = ltrim($url['path'], '/');
    $user = $url['user'];
    $pass = $url['pass'] ?? '';
    
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        http_response_code(500);
        die('Database connection failed: ' . $e->getMessage());
    }
    
} elseif ($jawsdb_url) {
    // Render MySQL via JawsDB
    $url = parse_url($jawsdb_url);
    $host = $url['host'];
    $port = $url['port'] ?? 3306;
    $dbname = ltrim($url['path'], '/');
    $user = $url['user'];
    $pass = $url['pass'] ?? '';
    $charset = 'utf8mb4';
    
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        http_response_code(500);
        die('Database connection failed: ' . $e->getMessage());
    }
    
} else {
    // Local XAMPP MySQL connection (development)
    $host    = 'localhost';
    $db      = 'aurak_tutoring';
    $user    = 'root';
    $pass    = '';
    $charset = 'utf8mb4';
    
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        http_response_code(500);
        die('Database connection failed: ' . $e->getMessage());
    }
}

/**
 * Backward-compatible helper function
 */
function getPDO() {
    global $pdo;
    return $pdo;
}
