<?php
session_start();
require_once '../config.php';

$feedback_id = intval($_GET['id']);
$user_office_id = $_SESSION['office_id'];

$query = "SELECT f.visit_date, f.age, f.sex, f.region, f.phone_number, 
                 f.service_availed, f.community, f.cc1, f.cc2, f.cc3, 
                 f.sqd0, f.sqd1, f.sqd2, f.sqd3, f.sqd4, f.sqd5, 
                 f.sqd6, f.sqd7, f.sqd8, f.sqd_average, f.service_type, f.comments, 
                 o.office_name
          FROM feedback f
          JOIN offices o ON f.office_id = o.office_id
          WHERE f.feedback_id = ? AND f.office_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $feedback_id, $user_office_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $feedback = $result->fetch_assoc();
    
    // Convert numeric values to proper types
    $feedback['age'] = $feedback['age'] !== null ? (int)$feedback['age'] : null;
    $feedback['sqd_average'] = $feedback['sqd_average'] !== null ? (float)$feedback['sqd_average'] : null;
    
    // Convert all NULL values to empty strings for consistent display
    foreach ($feedback as $key => $value) {
        if ($value === null) {
            $feedback[$key] = '';
        }
    }
    
    echo json_encode($feedback);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Feedback not found or not authorized']);
}

$stmt->close();
?>