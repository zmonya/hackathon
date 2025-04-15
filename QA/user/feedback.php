<?php
session_start(); // Start the session to access session variables

include 'header.php';
include 'sidebar.php';
require_once '../config.php'; // Adjusted path

$feedbacks = [];

// Check if the user is logged in and has an office ID
if (!isset($_SESSION['office_id'])) {
    echo "<p>Please log in to view feedback.</p>";
    exit(); // Stop further execution
}

$user_office_id = $_SESSION['office_id']; // Get the office ID from the session

// Update the query to use the correct column name
$query = "SELECT f.visit_date, 
                 (COALESCE(f.sqd0, 0) + COALESCE(f.sqd1, 0) + COALESCE(f.sqd2, 0) + COALESCE(f.sqd3, 0) + COALESCE(f.sqd4, 0) + COALESCE(f.sqd5, 0) + COALESCE(f.sqd6, 0) + COALESCE(f.sqd7, 0) + COALESCE(f.sqd8, 0)) / 
                 (CASE WHEN f.sqd0 IS NOT NULL THEN 1 ELSE 0 END +
                  CASE WHEN f.sqd1 IS NOT NULL THEN 1 ELSE 0 END +
                  CASE WHEN f.sqd2 IS NOT NULL THEN 1 ELSE 0 END +
                  CASE WHEN f.sqd3 IS NOT NULL THEN 1 ELSE 0 END +
                  CASE WHEN f.sqd4 IS NOT NULL THEN 1 ELSE 0 END +
                  CASE WHEN f.sqd5 IS NOT NULL THEN 1 ELSE 0 END +
                  CASE WHEN f.sqd6 IS NOT NULL THEN 1 ELSE 0 END +
                  CASE WHEN f.sqd7 IS NOT NULL THEN 1 ELSE 0 END +
                  CASE WHEN f.sqd8 IS NOT NULL THEN 1 ELSE 0 END) AS overall_rating,
                 f.comments,
                 f.feedback_id,
                 f.service_availed,
                 f.comment_type,
                 o.office_name
          FROM feedback f
          JOIN offices o ON f.office_id = o.office_id  -- Replace with your actual column name
          WHERE f.office_id = ? 
          ORDER BY f.visit_date DESC"; // Filter by office_id and sort by visit_date

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_office_id); // Bind the office ID parameter
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $feedbacks[] = $row;
}
$stmt->close();
?>

<div class="main-content">
    <div class="header">
                <h1 class="page-title">Feedbacks</h1>
                <div class="user-profile">
                    <div class="notification-icon" style="position: relative; margin-right: 10px;">
                        <i class="fas fa-bell" style="font-size: 20px;"></i>
                    </div>
                    <div class="user-avatar">U</div>
                    <span>User</span>
                </div>
            </div>

    <div class="chart-card">
        <h3 class="chart-title">Recent Feedback</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Office</th>
                    <th>Date</th>
                    <th>Rating</th>
                    <th>Service Availed</th>
                    <th>Comment Type</th>
                    <th>Comment</th>
                    
                     
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feedbacks as $feedback): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($feedback['office_name']); ?></td>
                        <td><?php echo htmlspecialchars($feedback['visit_date']); ?></td>
                        <td>
                            <span class="rating-badge rating-<?php echo round($feedback['overall_rating']); ?>">
                                <?php echo htmlspecialchars(number_format($feedback['overall_rating'], 1)); ?> <!-- Display decimal rating -->
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($feedback['service_availed']); ?></td>
                        <td><?php echo htmlspecialchars($feedback['comment_type']); ?></td>
                        <td><?php echo htmlspecialchars($feedback['comments']); ?></td>
                        
                         
                        <td>
                           
    <button type="button" class="btn btn-primary view-btn" data-feedback-id="<?php echo htmlspecialchars($feedback['feedback_id']); ?>" data-bs-toggle="modal" data-bs-target="#feedbackModal">
        View 
    </button>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: blue; color: white;">
                <h5 class="modal-title" id="feedbackModalLabel">Feedback Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Date of Visit:</strong> <span id="modal-visit-date"></span></p>
                <p><strong>Age:</strong> <span id="modal-age"></span></p>
                <p><strong>Sex:</strong> <span id="modal-sex"></span></p>
                <p><strong>Region:</strong> <span id="modal-region"></span></p>
                <p><strong>Office:</strong> <span id="modal-office-name"></span></p>
                <p><strong>Service Availed:</strong> <span id="modal-service-availed"></span></p>
                <p><strong>Community:</strong> <span id="modal-community"></span></p>
                <p><strong>CC1:</strong> <span id="modal-cc1"></span></p>
                <p><strong>CC2:</strong> <span id="modal-cc2"></span></p>
                <p><strong>CC3:</strong> <span id="modal-cc3"></span></p>
                <p><strong>SQD0:</strong> <span id="modal-sqd0"></span></p>
                <p><strong>SQD1:</strong> <span id="modal-sqd1"></span></p>
                <p><strong>SQD2:</strong> <span id="modal-sqd2"></span></p>
                <p><strong>SQD3:</strong> <span id="modal-sqd3"></span></p>
                <p><strong>SQD4:</strong> <span id="modal-sqd4"></span></p>
                <p><strong>SQD5:</strong> <span id="modal-sqd5"></span></p>
                <p><strong>SQD6:</strong> <span id="modal-sqd6"></span></p>
                <p><strong>SQD7:</strong> <span id="modal-sqd7"></span></p>
                <p><strong>SQD8:</strong> <span id="modal-sqd8"></span></p>
                <p><strong>Comment Type:</strong> <span id="modal-comment-type"></span></p>
                <p><strong>Comments:</strong> <span id="modal-comments"></span></p>
                <p><strong>Phone Number:</strong> <span id="modal-phone-number"></span></p>
            </div>
        </div>
    </div>
</div>

    <!-- Bootstrap JS Bundle with Popper -->
    
</body>
</html>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewButtons = document.querySelectorAll('.view-btn');

    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const feedbackId = this.getAttribute('data-feedback-id');
            fetchFeedbackData(feedbackId);
        });
    });
});

function fetchFeedbackData(feedbackId) {
    fetch('get_feedback.php?id=' + feedbackId)
        .then(response => response.json())
        .then(data => {
            console.log(data); // Add this line to debug the response
            if (data.error) {
                console.error(data.error);
                return;
            }

            // Populate the modal fields with the retrieved data
            document.getElementById('modal-visit-date').innerText = data.visit_date;
            document.getElementById('modal-age').innerText = data.age;
            document.getElementById('modal-sex').innerText = data.sex;
            document.getElementById('modal-region').innerText = data.region;
            document.getElementById('modal-phone-number').innerText = data.phone_number;
            document.getElementById('modal-office-name').innerText = data.office_name; // Check if this is populated
            document.getElementById('modal-service-availed').innerText = data.service_availed;
            document.getElementById('modal-community').innerText = data.community;
            document.getElementById('modal-cc1').innerText = data.cc1;
            document.getElementById('modal-cc2').innerText = data.cc2;
            document.getElementById('modal-cc3').innerText = data.cc3;
            document.getElementById('modal-sqd0').innerText = data.sqd0;
            document.getElementById('modal-sqd1').innerText = data.sqd1;
            document.getElementById('modal-sqd2').innerText = data.sqd2;
            document.getElementById('modal-sqd3').innerText = data.sqd3;
            document.getElementById('modal-sqd4').innerText = data.sqd4;
            document.getElementById('modal-sqd5').innerText = data.sqd5;
            document.getElementById('modal-sqd6').innerText = data.sqd6;
            document.getElementById('modal-sqd7').innerText = data.sqd7;
            document.getElementById('modal-sqd8').innerText = data.sqd8;
            document.getElementById('modal-comments').innerText = data.comments;
            document.getElementById('modal-comment-type').innerText = data.comment_type;

            // Show the modal
            $('#feedbackModal').modal('show');
        })
        .catch(error => console.error('Error fetching feedback:', error));
}
</script>

