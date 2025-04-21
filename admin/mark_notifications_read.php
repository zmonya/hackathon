<?php
require_once '../config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get the notification IDs from POST data
$input = json_decode(file_get_contents('php://input'), true);
$notificationIds = $input['notification_ids'] ?? [];

if (empty($notificationIds)) {
    echo json_encode(['success' => false, 'message' => 'No notification IDs provided']);
    exit();
}

// Prepare the IDs for the query
$placeholders = implode(',', array_fill(0, count($notificationIds), '?'));
$types = str_repeat('i', count($notificationIds));

// Update the notifications
$query = "UPDATE notifications SET is_read = 1 WHERE notification_id IN ($placeholders)";
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$notificationIds);
$success = $stmt->execute();

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Notifications marked as read']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update notifications']);
}
?>