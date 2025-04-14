<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_GET['feedback_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No feedback ID provided']);
    exit;
}

$feedback_id = intval($_GET['feedback_id']);
$query = "
    SELECT 
        f.feedback_id, 
        DATE_FORMAT(f.visit_date, '%Y-%m-%d') as visit_date,
        f.age, f.sex, f.region, f.phone_number,
        f.office_id, o.office_name, f.service_availed, f.community,
        f.cc1, f.cc2, f.cc3,
        f.sqd0, f.sqd1, f.sqd2, f.sqd3, f.sqd4, f.sqd5, f.sqd6, f.sqd7, f.sqd8,
        f.comment_type, f.comments,
        DATE_FORMAT(f.submitted_at, '%Y-%m-%d %H:%i:%s') as submitted_at
    FROM feedback f
    LEFT JOIN offices o ON f.office_id = o.office_id
    WHERE f.feedback_id = ?
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Query preparation failed']);
    exit;
}
$stmt->bind_param('i', $feedback_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Map cc1
    $cc1_map = [
        '1' => 'I know what a CC is and I saw this office\'s CC',
        '2' => 'I know what a CC is but I did NOT see this office\'s CC',
        '3' => 'I learned of the CC only when I saw this office\'s CC',
        '4' => 'I do not know what a CC is and I did not see one'
    ];
    $row['cc1_text'] = $cc1_map[$row['cc1']] ?? $row['cc1'] ?? 'N/A';
    echo json_encode($row);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Feedback not found']);
}

$stmt->close();
$conn->close();
?>