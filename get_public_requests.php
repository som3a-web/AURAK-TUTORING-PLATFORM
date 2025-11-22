<?php
// SE.PROJECT/api/admin/get_public_requests.php

header('Content-Type: application/json');
// NOTE: Ensure your PDO connection file path is correct
require '../../config/db.php'; 

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed. Use GET."]);
    exit();
}

try {
    // Select all pending public requests
    $sql = "SELECT id, student_name, student_email, course_code, topic_details, request_date 
            FROM public_requests 
            WHERE status = 'Pending'
            ORDER BY request_date DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode(["success" => true, "data" => $requests]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database error retrieving requests.", "error" => $e->getMessage()]);
}
?>