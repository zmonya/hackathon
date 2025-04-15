<?php
session_start();
require_once '../config.php'; // Adjust this path as necessary

if (!isset($_SESSION['office_id']) || !isset($_GET['id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$feedback_id = intval($_GET['id']);

$query = "SELECT f.visit_date, f.age, f.sex, f.region, f.phone_number, 
                 f.service_availed, f.community, f.cc1, f.cc2, f.cc3, 
                 f.sqd0, f.sqd1, f.sqd2, f.sqd3, f.sqd4, f.sqd5, 
                 f.sqd6, f.sqd7, f.sqd8, f.comment_type, f.comments, 
                 f.submitted_at, o.office_name
          FROM feedback f
          JOIN offices o ON f.office_id = o.office_id
          WHERE f.feedback_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $feedback_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $feedback = $result->fetch_assoc();
    echo json_encode($feedback);
} else {
    echo json_encode(['error' => 'Feedback not found']);
}

$stmt->close();
?>