<?php
session_start();
require_once '../config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    $_SESSION['error'] = "You don't have permission to perform this action.";
    header("Location: ../login.php");
    exit();
}


$action = $_POST['action'];


    if ($action === 'edit_user') {
        // Handle user edit
        $user_id = intval($_POST['user_id']);
        $fname = trim($_POST['fname']);
        $mname = trim($_POST['mname'] ?? '');
        $lname = trim($_POST['lname']);
        $username = trim($_POST['username']);
        $role_id = intval($_POST['role_id']);
        
        // Basic validation
        if (empty($fname) || empty($lname) || empty($username) || empty($role_id)) {
            throw new Exception("All required fields must be filled.");
        }

        // Check if username already exists (excluding current user)
        $check_username = $conn->prepare("SELECT user_id FROM users WHERE username = ? AND user_id != ?");
        $check_username->bind_param("si", $username, $user_id);
        $check_username->execute();
        $check_username->store_result();
        
        if ($check_username->num_rows > 0) {
            throw new Exception("Username already exists.");
        }

        // Handle office_id - clear if role is admin (role_id 2)
        $office_id = ($role_id == 2) ? null : (isset($_POST['office_id']) ? intval($_POST['office_id']) : null);

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

        // Execute the query
        if (!$stmt->execute()) {
            throw new Exception("Error updating user: " . $stmt->error);
        }

        $_SESSION['success'] = "User updated successfully!";
    }

// Redirect back to users page
header("Location: user_management.php");
exit();
?>