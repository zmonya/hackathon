<?php
session_start();
require_once '../config.php';

// Check if user has admin role (role_id = 2)
if ($_SESSION['role_id'] != 2) {
    $_SESSION['error'] = "You don't have permission to perform this action.";
    header("Location: office_management.php");
    exit();
}

// Handle different actions
if (isset($_GET['action']) || isset($_POST['action'])) {
    $action = isset($_GET['action']) ? $_GET['action'] : $_POST['action'];
    
    switch ($action) {
        case 'create':
            createOffice();
            break;
        case 'update':
            updateOffice();
            break;
        case 'toggle_status':
            toggleOfficeStatus();
            break;
        default:
            $_SESSION['error'] = "Invalid action.";
            header("Location: office_management.php");
            exit();
    }
} else {
    $_SESSION['error'] = "No action specified.";
    header("Location: office_management.php");
    exit();
}

function createOffice() {
    global $conn;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error'] = "Invalid request method.";
        header("Location: office_management.php");
        exit();
    }
    
    $office_name = trim($_POST['office_name']);
    
    if (empty($office_name)) {
        $_SESSION['error'] = "Office name is required.";
        header("Location: office_management.php");
        exit();
    }
    
    // Check if office already exists
    $check_stmt = $conn->prepare("SELECT office_id FROM offices WHERE office_name = ?");
    $check_stmt->bind_param("s", $office_name);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        $_SESSION['error'] = "Office with this name already exists.";
        header("Location: office_management.php");
        exit();
    }
    
    $stmt = $conn->prepare("INSERT INTO offices (office_name, is_active) VALUES (?, 1)");
    $stmt->bind_param("s", $office_name);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Office created successfully!";
    } else {
        $_SESSION['error'] = "Error creating office: " . $conn->error;
    }
    
    header("Location: office_management.php");
    exit();
}

function updateOffice() {
    global $conn;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error'] = "Invalid request method.";
        header("Location: office_management.php");
        exit();
    }
    
    $office_id = intval($_POST['office_id']);
    $office_name = trim($_POST['office_name']);
    
    if (empty($office_name)) {
        $_SESSION['error'] = "Office name is required.";
        header("Location: office_management.php");
        exit();
    }
    
    // Check if office already exists (excluding current office)
    $check_stmt = $conn->prepare("SELECT office_id FROM offices WHERE office_name = ? AND office_id != ?");
    $check_stmt->bind_param("si", $office_name, $office_id);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        $_SESSION['error'] = "Another office with this name already exists.";
        header("Location: office_management.php");
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE offices SET office_name = ? WHERE office_id = ?");
    $stmt->bind_param("si", $office_name, $office_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Office updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating office: " . $conn->error;
    }
    
    header("Location: office_management.php");
    exit();
}

function toggleOfficeStatus() {
    global $conn;
    
    if (!isset($_GET['id'])) {
        $_SESSION['error'] = "Office ID is required.";
        header("Location: office_management.php");
        exit();
    }
    
    $office_id = intval($_GET['id']);
    
    // Get current status
    $stmt = $conn->prepare("SELECT is_active FROM offices WHERE office_id = ?");
    $stmt->bind_param("i", $office_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Office not found.";
        header("Location: office_management.php");
        exit();
    }
    
    $office = $result->fetch_assoc();
    $new_status = $office['is_active'] ? 0 : 1;
    
    // Update status
    $update_stmt = $conn->prepare("UPDATE offices SET is_active = ? WHERE office_id = ?");
    $update_stmt->bind_param("ii", $new_status, $office_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['success'] = "Office status updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating office status: " . $conn->error;
    }
    
    header("Location: office_management.php");
    exit();
}
?>