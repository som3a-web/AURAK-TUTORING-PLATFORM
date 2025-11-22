<?php
// SE.PROJECT/api/requests/submit_public_request.php

header('Content-Type: application/json');
// Make sure this path is correct relative to the 'requests' folder:
require '../../config/db.php'; // Use require to stop execution if DB connection fails

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

// Basic validation
if (empty($data->student_name) || empty($data->student_email) || empty($data->course_code)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing required contact or course information."]);
    exit();
}

$name = $data->student_name;
$email = $data->student_email;
$course = $data->course_code;
$topic = $data->topic_details ?? '';

try {
    // NOTE: Using PDO syntax ($pdo->prepare and execute with array)
    $sql = "INSERT INTO public_requests 
                (student_name, student_email, course_code, topic_details, status) 
            VALUES 
                (:name, :email, :course, :topic, 'Pending')";
                
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters to prevent SQL injection
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':course', $course);
    $stmt->bindParam(':topic', $topic);
    
    $stmt->execute();
    
    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Your tutoring request has been received. We will contact you soon."]);

} catch (PDOException $e) {
    // PDO errors are now specifically caught and displayed
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Database error. Please check your table structure.", 
        "error" => $e->getMessage()
    ]);
}

// No need to close PDO connection manually

?>