<?php
session_start();
require_once '../config.php';

// Set response type to JSON
header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if action and id are provided
if (!isset($_GET['action']) || !isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit();
}

$action = $_GET['action'];
$user_id = (int)$_GET['id'];
$response = ['success' => false, 'message' => ''];

try {
    if ($action === 'toggle_status') {
        // Get current status
        $stmt = $conn->prepare("SELECT is_active FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $new_status = $user['is_active'] ? 0 : 1;
            
            // Update the status
            $update_stmt = $conn->prepare("UPDATE users SET is_active = ? WHERE user_id = ?");
            $update_stmt->bind_param("ii", $new_status, $user_id);
            
            if ($update_stmt->execute()) {
                $response = [
                    'success' => true,
                    'message' => 'User status updated successfully!',
                    'new_status' => $new_status,
                    'status_text' => $new_status ? 'Active' : 'Inactive',
                    'btn_text' => $new_status ? 'Deactivate' : 'Activate'
                ];
            } else {
                $response['message'] = "Error updating user status";
            }
            $update_stmt->close();
        } else {
            $response['message'] = "User not found";
        }
        $stmt->close();
    } else {
        $response['message'] = "Invalid action";
    }
} catch (Exception $e) {
    $response['message'] = "Database error: " . $e->getMessage();
}

echo json_encode($response);
exit();
?>