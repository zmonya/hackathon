<?php
require_once '../config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get the notification IDs and office ID from POST data
$input = json_decode(file_get_contents('php://input'), true);
$notificationIds = $input['notification_ids'] ?? [];
$office_id = $input['office_id'] ?? null;

if (empty($notificationIds) {
    echo json_encode(['success' => false, 'message' => 'No notification IDs provided']);
    exit();
}

if (!$office_id) {
    echo json_encode(['success' => false, 'message' => 'Office ID not provided']);
    exit();
}

// Prepare the IDs for the query
$placeholders = implode(',', array_fill(0, count($notificationIds), '?'));
$types = str_repeat('i', count($notificationIds));

// Update only notifications that belong to feedback from the user's office
$query = "UPDATE notifications n
          JOIN feedback f ON n.feedback_id = f.feedback_id
          SET n.is_read = 1 
          WHERE n.notification_id IN ($placeholders)
          AND f.office_id = ?";
$stmt = $conn->prepare($query);

// Bind both notification IDs and office ID
$params = array_merge($notificationIds, [$office_id]);
$types .= 'i'; // Add one more integer for office_id
$stmt->bind_param($types, ...$params);

$success = $stmt->execute();

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Notifications marked as read']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update notifications']);
}
?>