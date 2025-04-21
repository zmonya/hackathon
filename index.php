<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) { // Change 'user_id' to the appropriate session variable for your app
    header("Location: login.php"); // Redirect to login.php
    exit(); // Stop further execution
}

// Continue with the rest of your code...
?>