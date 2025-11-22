<?php
// SE.PROJECT/api/admin/manage_public_request.php

header('Content-Type: application/json');
require '../../config/db.php'; // Ensure your PDO connection file path is correct

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed. Use POST."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (empty($data->request_id) || empty($data->action)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing request ID or action."]);
    exit();
}

$requestId = $data->request_id;
$action = $data->action; // 'assign' or 'close'

// Determine the new status
$newStatus = ($action === 'assign') ? 'Assigned' : 'Closed';

try {
    $sql = "UPDATE public_requests SET status = :status 
            WHERE id = :id AND status = 'Pending'"; // Only update if still pending
            
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':status', $newStatus);
    $stmt->bindParam(':id', $requestId);
    
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("Request not found or already processed.");
    }
    
    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Request ID {$requestId} status updated to {$newStatus}."]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Database error.", 
        "error" => $e->getMessage()
    ]);
}
?>