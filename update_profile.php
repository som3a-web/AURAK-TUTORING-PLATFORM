<?php
// api/tutors/update_profile.php

header('Content-Type: application/json');
include_once '../../config/db.php'; // Adjust path as needed

// --- 1. Basic Setup and Security Checks ---

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed. Use POST."]);
    exit();
}

// **SECURITY CHECK:** Verify the user is logged in AND is an 'Approved Tutor'
// You would use session data or a JWT check here.
// For example, retrieve the user ID from the session/token:
// $logged_in_user_id = getUserIdFromSession(); 
// if (!$logged_in_user_id) { ... security fail }

$data = json_decode(file_get_contents("php://input"));

if (empty($data->user_id) || empty($data->bio) || empty($data->subjects)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing required fields (user_id, bio, or subjects)."]);
    exit();
}

$user_id = $data->user_id;
$bio = $data->bio;
// Convert the subjects array/list into a JSON string for storage
$subjects_json = json_encode($data->subjects); 
$rate = $data->hourly_rate ?? 0.00; // Use a default if rate is not submitted

// --- 2. Database Insertion/Update Logic ---

try {
    // UPSERT (Update or Insert): Check if the profile already exists.
    
    // Check for existing profile
    $stmt_check = $conn->prepare("SELECT user_id FROM tutor_details WHERE user_id = ?");
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // Profile exists, so UPDATE
        $sql = "UPDATE tutor_details SET bio = ?, subjects = ?, hourly_rate = ?, last_updated = NOW() WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdi", $bio, $subjects_json, $rate, $user_id);
    } else {
        // Profile does NOT exist, so INSERT
        $sql = "INSERT INTO tutor_details (user_id, bio, subjects, hourly_rate, last_updated) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issd", $user_id, $bio, $subjects_json, $rate);
    }

    $stmt->execute();

    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Tutor profile updated successfully."]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Profile update failed.", "error" => $e->getMessage()]);
}

$conn->close();
?>