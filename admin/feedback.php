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
    SELECT f.feedback_id, f.visit_date, o.office_name, f.service_availed, f.service_type, f.comments,
           f.sqd0, f.sqd1, f.sqd2, f.sqd3, f.sqd4, f.sqd5, f.sqd6, f.sqd7, f.sqd8,
           f.sqd_average, f.community
    FROM feedback f
    LEFT JOIN offices o ON f.office_id = o.office_id
    $where_clause
    ORDER BY f.feedback_id DESC
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
?>

<div class="main-content">
    <?php include 'notification.php'; ?>

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
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th class="text-center">Date</th>
                        <th class="text-center">Office</th>
                        <th class="text-center">Service Availed</th>
                        <th class="text-center">Community</th>
                        <th class="text-center">Rating</th>
                        <th class="text-center">Service Type</th>
                        <th class="text-center">Comment</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php
                            $rating = $row['sqd_average'];
                            $ratingClass = $rating !== null ? 'rating-' . ceil($rating) : '';
                            ?>
                            <tr>
                                <td class="text-center"><?php echo htmlspecialchars($row['visit_date']); ?></td>
                                <td class="text-center"><?php echo htmlspecialchars($row['office_name'] ?? 'N/A'); ?></td>
                                <td class="text-center"><?php echo htmlspecialchars($row['service_availed'] ?? 'N/A'); ?></td>
                                <td class="text-center"><?php echo htmlspecialchars($row['community'] ?? 'N/A'); ?></td>
                                <?php 
                                $rating = $row['sqd_average'];
                                if ($rating !== null && is_numeric($rating)) {
                                    $ratingClass = 'rating-' . min(5, max(1, floor($rating))); // Round to nearest whole number
                                } else {
                                    $ratingClass = '';
                                }
                                ?>
                                <td class="text-center">
                                    <?php if ($rating !== null && is_numeric($rating)): ?>
                                        <span class="rating-badge <?php echo $ratingClass; ?>">
                                            <?php echo number_format($rating, 1); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="rating-badge">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?php echo htmlspecialchars($row['service_type'] ?? 'N/A'); ?></td>
                                <td class="text-center">
                                    <?php echo htmlspecialchars(!empty($row['comments']) ? $row['comments'] : 'No comment'); ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary view-btn" 
                                            data-feedback-id="<?php echo htmlspecialchars($row['feedback_id']); ?>" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#feedbackModal">
                                            <i class="fas fa-eye"></i>
                                        View 
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">No feedback available.</td>
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
                <p><strong>Average Rating:</strong> <span id="modal-sqd-average"></span></p>
                <p><strong>Service Type:</strong> <span id="modal-service-type"></span></p>
                <p><strong>Comments:</strong> <span id="modal-comments"></span></p>
                <p><strong>Phone Number:</strong> <span id="modal-phone-number"></span></p>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.table').DataTable({
        "order": [[0, "desc"]], 
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true

    });
});

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
            console.log(data);
            if (data.error) {
                console.error(data.error);
                return;
            }

            // Populate the modal fields
            document.getElementById('modal-visit-date').innerText = data.visit_date || 'N/A';
            document.getElementById('modal-age').innerText = data.age || 'N/A';
            document.getElementById('modal-sex').innerText = data.sex || 'N/A';
            document.getElementById('modal-region').innerText = data.region || 'N/A';
            
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
            document.getElementById('modal-sqd-average').innerText = data.sqd_average ? number_format(data.sqd_average, 1) : 'N/A';
            document.getElementById('modal-service-type').innerText = data.service_type || 'N/A';
            document.getElementById('modal-comments').innerText = data.comments || 'No comment';
            document.getElementById('modal-phone-number').innerText = data.phone_number || 'N/A';

            $('#feedbackModal').modal('show');
        })
        .catch(error => console.error('Error fetching feedback:', error));
}

function number_format(number, decimals) {
    return parseFloat(number).toFixed(decimals);
}
</script>