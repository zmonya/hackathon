<?php
session_start();
require_once '../config.php';

// Set default headers
header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    $_SESSION['error'] = "You don't have permission to perform this action.";
    header("Location: ../login.php");
    exit();
}

// Process requests based on method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    handlePostRequest();
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    handleGetRequest();
}

function handlePostRequest() {
    global $conn;
    
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'add_user':
                handleAddUser();
                // If we got here, it was successful
                $_SESSION['success'] = "User created successfully!";
                header("Location: user_management.php");
                break;
                
            case 'edit_user':
                $_SESSION['success'] = "User updated successfully!";
                header("Location: user_management.php");
                handleEditUser();
                break;
                
            default:
                throw new Exception("Invalid action");
        }
          
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: user_management.php");
    }
    
    exit();
}

function handleGetRequest() {
    global $conn;
    
    $action = $_GET['action'] ?? '';
    $user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    try {
        if ($action === 'toggle_status') {
            toggleUserStatus($user_id);
        } else {
            throw new Exception("Invalid action");
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    }
}

function handleAddUser() {
    global $conn;
    
    // Validate required fields
    $required = ['fname', 'lname', 'username', 'password', 'role_id'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Validate password length
    if (strlen(trim($_POST['password'])) < 8) {
        throw new Exception("Password must be at least 8 characters long");
    }

    // Validate password length if provided
    if (!empty($_POST['password']) && strlen(trim($_POST['password'])) < 8) {
        throw new Exception("Password must be at least 8 characters long");
    }
    
    // Sanitize inputs
    $fname = trim($_POST['fname']);
    $mname = trim($_POST['mname'] ?? '');
    $lname = trim($_POST['lname']);
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role_id = intval($_POST['role_id']);
    $office_id = !empty($_POST['office_id']) ? intval($_POST['office_id']) : null;

    // Check if username exists
    $check = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        throw new Exception("Username already exists!");
    }
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (fname, mname, lname, username, password, role_id, office_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssii", $fname, $mname, $lname, $username, $password, $role_id, $office_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error adding user: " . $conn->error);
    }
    
    $stmt->close();
    $check->close();
}

function handleEditUser() {
    global $conn;
    
    // Validate required fields
    $required = ['user_id', 'fname', 'lname', 'username', 'role_id'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }
        if (!empty($_POST['password']) && strlen(trim($_POST['password'])) < 8) {
        throw new Exception("Password must be at least 8 characters long");
    }
    
    // Sanitize inputs
    $user_id = intval($_POST['user_id']);
    $fname = trim($_POST['fname']);
    $mname = trim($_POST['mname'] ?? '');
    $lname = trim($_POST['lname']);
    $username = trim($_POST['username']);
    $role_id = intval($_POST['role_id']);
    $office_id = !empty($_POST['office_id']) ? intval($_POST['office_id']) : null;

    // Check if username exists (excluding current user)
    $check = $conn->prepare("SELECT user_id FROM users WHERE username = ? AND user_id != ?");
    $check->bind_param("si", $username, $user_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        throw new Exception("Username already exists!");
    }

    // Prepare the update query
    if (!empty($_POST['password'])) {
        // Update with password
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET fname=?, mname=?, lname=?, username=?, password=?, role_id=?, office_id=? WHERE user_id=?");
        $stmt->bind_param("sssssiii", $fname, $mname, $lname, $username, $password, $role_id, $office_id, $user_id);
    } else {
        // Update without password
        $stmt = $conn->prepare("UPDATE users SET fname=?, mname=?, lname=?, username=?, role_id=?, office_id=? WHERE user_id=?");
        $stmt->bind_param("ssssiii", $fname, $mname, $lname, $username, $role_id, $office_id, $user_id);
    }

    if (!$stmt->execute()) {
        throw new Exception("Error updating user: " . $stmt->error);
    }
    
    $stmt->close();
    $check->close();
}

function toggleUserStatus($user_id) {
    global $conn;
    
    // Get current status
    $stmt = $conn->prepare("SELECT is_active FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) {
        throw new Exception("User not found");
    }
    
    $user = $result->fetch_assoc();
    $new_status = $user['is_active'] ? 0 : 1;
    
    // Update the status
    $update_stmt = $conn->prepare("UPDATE users SET is_active = ? WHERE user_id = ?");
    $update_stmt->bind_param("ii", $new_status, $user_id);
    
    if (!$update_stmt->execute()) {
        throw new Exception("Error updating user status");
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'User status updated successfully!',
        'new_status' => $new_status,
        'status_text' => $new_status ? 'Active' : 'Inactive',
        'btn_text' => $new_status ? 'Deactivate' : 'Activate'
    ]);
    
    $update_stmt->close();
    $stmt->close();
}