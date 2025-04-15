<?php
require_once 'config.php';

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging: Output POST data
    var_dump($_POST); // Check the submitted data for debugging

    // Validate and sanitize input data
    $visit_date = $conn->real_escape_string($_POST['visit_date']);
    $age = isset($_POST['age']) ? intval($_POST['age']) : NULL;
    $sex = $conn->real_escape_string($_POST['sex']);
    $region = isset($_POST['region']) ? $conn->real_escape_string($_POST['region']) : NULL;
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    
    $office_id = intval($_POST['office_id']);
    $service_availed = $conn->real_escape_string($_POST['service_availed']);
    $community = $conn->real_escape_string($_POST['community']);
    
    // Citizen's Charter feedback
    $cc1 = isset($_POST['cc1']) ? $conn->real_escape_string($_POST['cc1']) : NULL;
    $cc2 = isset($_POST['cc2']) ? $conn->real_escape_string($_POST['cc2']) : NULL;
    $cc3 = isset($_POST['cc3']) ? $conn->real_escape_string($_POST['cc3']) : NULL;
    
    // Service Quality Dimensions
    $sqd0 = $conn->real_escape_string($_POST['sqd0']);
    $sqd1 = $conn->real_escape_string($_POST['sqd1']);
    $sqd2 = $conn->real_escape_string($_POST['sqd2']);
    $sqd3 = $conn->real_escape_string($_POST['sqd3']);
    $sqd4 = $conn->real_escape_string($_POST['sqd4']);
    $sqd5 = $conn->real_escape_string($_POST['sqd5']);
    $sqd6 = $conn->real_escape_string($_POST['sqd6']);
    $sqd7 = $conn->real_escape_string($_POST['sqd7']);
    $sqd8 = $conn->real_escape_string($_POST['sqd8']);
    
    // Comments
    $comment_type = $conn->real_escape_string($_POST['comment_type']);
    $comments = isset($_POST['comments']) ? $conn->real_escape_string($_POST['comments']) : NULL;
    
    // Prepare SQL statement
    $sql = "INSERT INTO feedback (
        visit_date, age, sex, region, phone_number,
        office_id, service_availed, community,
        cc1, cc2, cc3,
        sqd0, sqd1, sqd2, sqd3, sqd4, sqd5, sqd6, sqd7, sqd8,
        comment_type, comments
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    
    $stmt->bind_param(
        "sisssissssssssssssssss",
        $visit_date, $age, $sex, $region, $phone_number,
        $office_id, $service_availed, $community,
        $cc1, $cc2, $cc3,
        $sqd0, $sqd1, $sqd2, $sqd3, $sqd4, $sqd5, $sqd6, $sqd7, $sqd8,
        $comment_type, $comments
    );
    
    // Execute the statement
    if ($stmt->execute()) {
        // Success - redirect to thank you page
        header("Location: thank_you.php");
        exit();
    } else {
        // Error handling
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
} else {
    // Not a POST request, redirect to form
    header("Location: form.php");
    exit();
}
?>