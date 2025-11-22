<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// We are already inside /config, so just go to db.php directly
require_once __DIR__ . '/db.php';

try {
    $pdo = getPDO();  // or just use $pdo directly
    $stmt = $pdo->query("SELECT 1");
    $row = $stmt->fetch();
    echo "DB OK: connected";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
