<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input data
    $visit_date = $conn->real_escape_string($_POST['visit_date']);
    $age = isset($_POST['age']) ? intval($_POST['age']) : NULL;
    $sex = $conn->real_escape_string($_POST['sex']);
    $region = isset($_POST['region']) ? $conn->real_escape_string($_POST['region']) : NULL;
    $phone_number = isset($_POST['phone_number']) && !empty(trim($_POST['phone_number'])) 
    ? $conn->real_escape_string($_POST['phone_number']) 
    : NULL;
    $office_id = intval($_POST['office_id']);
    $service_availed = $conn->real_escape_string($_POST['service_availed']);
    $community = $conn->real_escape_string($_POST['community']);
    
    // Citizen's Charter feedback
    $cc1 = isset($_POST['cc1']) ? $conn->real_escape_string($_POST['cc1']) : NULL;
    $cc2 = isset($_POST['cc2']) ? $conn->real_escape_string($_POST['cc2']) : NULL;
    $cc3 = isset($_POST['cc3']) ? $conn->real_escape_string($_POST['cc3']) : NULL;
    
    // Service Quality Dimensions
    $sqd_values = [
        $_POST['sqd0'],
        $_POST['sqd1'],
        $_POST['sqd2'],
        $_POST['sqd3'],
        $_POST['sqd4'],
        $_POST['sqd5'],
        $_POST['sqd6'],
        $_POST['sqd7'],
        $_POST['sqd8']
    ];
    
    // Calculate average excluding NA values
    $sum = 0;
    $count = 0;
    
    foreach ($sqd_values as $value) {
        if ($value !== 'NA' && is_numeric($value)) {
            $value = (float)$value;
            if ($value >= 1 && $value <= 5) {
                $sum += $value;
                $count++;
            }
        }
    }
    
    // Calculate as float value
    $sqd_average = ($count > 0) ? ($sum / $count) : NULL;
    
    // Sanitize individual SQD values
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
    $service_type = $conn->real_escape_string($_POST['service_type']);
    $comments = isset($_POST['comments']) ? $conn->real_escape_string($_POST['comments']) : NULL;
    
    // Prepare SQL statement for feedback
    $sql = "INSERT INTO feedback (
        visit_date, age, sex, region, phone_number,
        office_id, service_availed, community,
        cc1, cc2, cc3,
        sqd0, sqd1, sqd2, sqd3, sqd4, sqd5, sqd6, sqd7, sqd8,
        sqd_average, service_type, comments
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    // Bind parameters
    $stmt->bind_param(
        "sisssissssssssssssssdss",
        $visit_date, $age, $sex, $region, $phone_number,
        $office_id, $service_availed, $community,
        $cc1, $cc2, $cc3,
        $sqd0, $sqd1, $sqd2, $sqd3, $sqd4, $sqd5, $sqd6, $sqd7, $sqd8,
        $sqd_average, 
        $service_type, $comments
    );
    
    // Execute the statement
    if ($stmt->execute()) {
        // After successfully inserting the feedback:
        $feedback_id = $conn->insert_id;

        // Get the office name
        $office_name = '';
        $office_query = "SELECT office_name FROM offices WHERE office_id = ?";
        $office_stmt = $conn->prepare($office_query);
        $office_stmt->bind_param("i", $office_id);
        $office_stmt->execute();
        $office_result = $office_stmt->get_result();
        if ($office_row = $office_result->fetch_assoc()) {
            $office_name = $office_row['office_name'];
        }
        $office_stmt->close();

        // Create a notification message with office name
        $notification_message = "New feedback received for $office_name";

        // Create a notification for users in the same office
        $notification_query = "INSERT INTO notifications (feedback_id, message) VALUES (?, ?)";
        $notification_stmt = $conn->prepare($notification_query);
        $notification_stmt->bind_param("is", $feedback_id, $notification_message);
        $notification_stmt->execute();
        $notification_stmt->close();

        header("Location: thank_you.php");
        exit();
    } else {
        // Enhanced error reporting
        error_log("Database error: " . $stmt->error);
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: form.php");
    exit();
}