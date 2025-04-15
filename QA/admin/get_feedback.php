<?php
session_start();
require_once '../config.php';

// Check if user is authorized (either admin or matching office_id)
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Feedback ID not provided']);
    exit();
}

$feedback_id = intval($_GET['id']);

// Get feedback details with office name
$query = "SELECT f.visit_date, f.age, f.sex, f.region, f.phone_number, 
                 f.service_availed, f.community, f.cc1, f.cc2, f.cc3, 
                 f.sqd0, f.sqd1, f.sqd2, f.sqd3, f.sqd4, f.sqd5, 
                 f.sqd6, f.sqd7, f.sqd8, f.comment_type, f.comments, 
                 f.submitted_at, o.office_name
          FROM feedback f
          LEFT JOIN offices o ON f.office_id = o.office_id
          WHERE f.feedback_id = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $conn->error]);
    exit();
}

$stmt->bind_param("i", $feedback_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $feedback = $result->fetch_assoc();
    
    // Map CC1 value to meaningful text
    $cc1_map = [
        '1' => 'I know what a CC is and I saw this office\'s CC',
        '2' => 'I know what a CC is but I did NOT see this office\'s CC',
        '3' => 'I learned of the CC only when I saw this office\'s CC',
        '4' => 'I do not know what a CC is and I did not see one'
    ];
    
    $feedback['cc1_text'] = isset($cc1_map[$feedback['cc1']]) ? $cc1_map[$feedback['cc1']] : $feedback['cc1'];
    
    // Format dates
    $feedback['visit_date'] = date('Y-m-d', strtotime($feedback['visit_date']));
    $feedback['submitted_at'] = date('Y-m-d H:i:s', strtotime($feedback['submitted_at']));
    
    header('Content-Type: application/json');
    echo json_encode($feedback);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Feedback not found']);
}

$stmt->close();
$conn->close();
?>