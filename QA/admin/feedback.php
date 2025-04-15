<?php
session_start();
include 'header.php';
include 'sidebar.php';
require_once '../config.php';

// Fetch all offices for dropdown
$offices_query = "SELECT office_id, office_name FROM offices ORDER BY office_name";
$offices_result = $conn->query($offices_query);

// Get selected office_id from GET, default to 'all'
$selected_office = isset($_GET['office_id']) ? $_GET['office_id'] : 'all';
$where_clause = '';
$params = [];
$types = '';

if ($selected_office !== 'all') {
    $where_clause = 'WHERE f.office_id = ?';
    $params[] = intval($selected_office);
    $types = 'i';
}

// Fetch feedback data
$query = "
    SELECT f.feedback_id, f.visit_date, o.office_name, f.service_availed, f.comment_type, f.comments,
           f.sqd0, f.sqd1, f.sqd2, f.sqd3, f.sqd4, f.sqd5, f.sqd6, f.sqd7, f.sqd8
    FROM feedback f
    LEFT JOIN offices o ON f.office_id = o.office_id
    $where_clause
    ORDER BY f.visit_date DESC
    LIMIT 10
";

if ($where_clause) {
    $stmt = $conn->prepare($query);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}

// Calculate average rating
function calculateRating($row) {
    $ratings = [
        $row['sqd0'], $row['sqd1'], $row['sqd2'], $row['sqd3'],
        $row['sqd4'], $row['sqd5'], $row['sqd6'], $row['sqd7'], $row['sqd8']
    ];
    $validRatings = array_filter($ratings, function($val) {
        return $val !== 'NA' && is_numeric($val);
    });
    if (empty($validRatings)) {
        return 'N/A';
    }
    $avg = array_sum($validRatings) / count($validRatings);
    return round($avg, 1);
}
?>

<div class="main-content">
    <div class="header">
        <h1 class="page-title">Feedbacks</h1>
        <div class="user-profile">
            <div class="notification-icon" style="position: relative; margin-right: 10px;">
                <i class="fas fa-bell" style="font-size: 20px;"></i>
            </div>
            <div class="user-avatar">AD</div>
            <span>Admin</span>
        </div>
    </div>

    <div class="chart-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="chart-title">Recent Feedback</h3>
            <form method="GET" action="?section=feedback" class="d-inline">
                <select name="office_id" class="form-select form-select-sm" style="width: 200px;" onchange="this.form.submit()">
                    <option value="all" <?php echo $selected_office === 'all' ? 'selected' : ''; ?>>All Offices</option>
                    <?php while ($office = $offices_result->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($office['office_id']); ?>"
                                <?php echo $selected_office === (string)$office['office_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($office['office_name']); ?>
                        </option>
                    <?php endwhile; ?>
                    <?php $offices_result->data_seek(0); ?>
                </select>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Office</th>
                        <th>Service Availed</th>
                        <th>Rating</th>
                        <th>Comment Type</th>
                        <th>Comment</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php
                            $rating = calculateRating($row);
                            $ratingClass = $rating !== 'N/A' ? 'rating-' . ceil($rating) : '';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['visit_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['office_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['service_availed'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php if ($rating !== 'N/A'): ?>
                                        <span class="rating-badge <?php echo $ratingClass; ?>">
                                            <?php echo htmlspecialchars($rating); ?>
                                        </span>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['comment_type'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['comments'] ?? 'No comment'); ?></td>
                                <td>
                                    <button type="button" class="btn btn-primary view-btn" 
                                            data-feedback-id="<?php echo htmlspecialchars($row['feedback_id']); ?>" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#feedbackModal">
                                        View 
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No feedback available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
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
            console.log(data); // For debugging
            if (data.error) {
                console.error(data.error);
                return;
            }

            // Populate the modal fields with the retrieved data
            document.getElementById('modal-visit-date').innerText = data.visit_date || 'N/A';
            document.getElementById('modal-age').innerText = data.age || 'N/A';
            document.getElementById('modal-sex').innerText = data.sex || 'N/A';
            document.getElementById('modal-region').innerText = data.region || 'N/A';
            document.getElementById('modal-phone-number').innerText = data.phone_number || 'N/A';
            document.getElementById('modal-office-name').innerText = data.office_name || 'N/A';
            document.getElementById('modal-service-availed').innerText = data.service_availed || 'N/A';
            document.getElementById('modal-community').innerText = data.community || 'N/A';
            document.getElementById('modal-cc1').innerText = data.cc1 || 'N/A';
            document.getElementById('modal-cc2').innerText = data.cc2 || 'N/A';
            document.getElementById('modal-cc3').innerText = data.cc3 || 'N/A';
            document.getElementById('modal-sqd0').innerText = data.sqd0 || 'N/A';
            document.getElementById('modal-sqd1').innerText = data.sqd1 || 'N/A';
            document.getElementById('modal-sqd2').innerText = data.sqd2 || 'N/A';
            document.getElementById('modal-sqd3').innerText = data.sqd3 || 'N/A';
            document.getElementById('modal-sqd4').innerText = data.sqd4 || 'N/A';
            document.getElementById('modal-sqd5').innerText = data.sqd5 || 'N/A';
            document.getElementById('modal-sqd6').innerText = data.sqd6 || 'N/A';
            document.getElementById('modal-sqd7').innerText = data.sqd7 || 'N/A';
            document.getElementById('modal-sqd8').innerText = data.sqd8 || 'N/A';
            document.getElementById('modal-comments').innerText = data.comments || 'No comment';
            document.getElementById('modal-comment-type').innerText = data.comment_type || 'N/A';

            // Show the modal
            $('#feedbackModal').modal('show');
        })
        .catch(error => console.error('Error fetching feedback:', error));
}
</script>