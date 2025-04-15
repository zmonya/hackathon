<?php
session_start(); // Start the session

// Destroy all session variables
$_SESSION = []; // Clear session data
session_destroy(); // Destroy the session

// Redirect to the login page
header("Location: login.php"); // Adjust this path as needed
exit(); // Ensure no further code is executed
?>