<?php
session_start();
require_once '../config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../login.php");
    exit();
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'add_user') {
        // Add new user
        $fname = trim($_POST['fname']);
        $mname = trim($_POST['mname'] ?? '');
        $lname = trim($_POST['lname']);
        $username = trim($_POST['username']);
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
        $role_id = intval($_POST['role_id']);
        $office_id = !empty($_POST['office_id']) ? intval($_POST['office_id']) : null;

        // Validate username doesn't exist
        $check = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $_SESSION['error'] = "Username already exists!";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (fname, mname, lname, username, password, role_id, office_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssii", $fname, $mname, $lname, $username, $password, $role_id, $office_id);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "User added successfully!";
            } else {
                $_SESSION['error'] = "Error adding user: " . $conn->error;
            }
            $stmt->close();
        }
        $check->close();
    }

    header("Location: user_management.php");
    if ($stmt->execute()) {
        $_SESSION['success'] = "User account created successfully!";
    } else {
        $_SESSION['error'] = "Error creating user: " . $conn->error;
    }
}
exit();