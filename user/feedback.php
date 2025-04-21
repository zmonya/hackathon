<?php
session_start();
include 'header.php';
include 'sidebar.php';
require_once '../config.php';

$feedbacks = [];

if (!isset($_SESSION['office_id'])) {
    echo "<p>Please log in to view feedback.</p>";
    include 'footer.php';
    exit();
}

$user_office_id = $_SESSION['office_id'];

$query = "SELECT f.feedback_id, f.visit_date, o.office_name, f.service_availed, 
                 f.service_type, f.comments, f.sqd_average, f.community,
                 f.age, f.sex, f.region, f.phone_number,
                 f.cc1, f.cc2, f.cc3,
                 f.sqd0, f.sqd1, f.sqd2, f.sqd3, f.sqd4, 
                 f.sqd5, f.sqd6, f.sqd7, f.sqd8
          FROM feedback f
          JOIN offices o ON f.office_id = o.office_id
          WHERE f.office_id = ? 
          ORDER BY f.feedback_id DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_office_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $feedbacks[] = $row;
}
$stmt->close();
?>

<div class="main-content">
    <?php include 'notification.php'; ?>

    <div class="chart-card">
        <h3 class="chart-title">Recent Feedback</h3>
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
                    <?php foreach ($feedbacks as $feedback): ?>
                        <tr>
                            <td class="text-center"><?php echo htmlspecialchars($feedback['visit_date']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($feedback['office_name']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($feedback['service_availed']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($feedback['community'] ?? 'N/A'); ?></td>
                            
                            <td class="text-center">
                                <?php if ($feedback['sqd_average'] !== null): ?>
                                    <span class="rating-badge rating-<?php echo floor($feedback['sqd_average']); ?>">
                                        <?php echo htmlspecialchars(number_format($feedback['sqd_average'], 1)); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="rating-badge">N/A</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="text-center"><?php echo htmlspecialchars($feedback['service_type'] ?? 'N/A'); ?></td>
                            <td class="text-center">
                                <?php 
                                $comment = trim($feedback['comments'] ?? '');
                                echo htmlspecialchars($comment !== '' ? $comment : 'No comment');
                                ?>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-primary view-btn" 
                                        data-feedback-id="<?php echo htmlspecialchars($feedback['feedback_id']); ?>" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#feedbackModal">
                                    <i class="fas fa-eye"></i> View 
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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
                <p><strong>Community:</strong> <span id="modal-community"></span></p>
                <p><strong>Office:</strong> <span id="modal-office-name"></span></p>
                <p><strong>Service Availed:</strong> <span id="modal-service-availed"></span></p>
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
    // Initialize DataTable with proper callbacks
    var table = $('.table').DataTable({
        "order": [[0, "desc"]],
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "drawCallback": function(settings) {
            // Reattach click handlers after each table redraw (including pagination)
            $('.view-btn').off('click').on('click', function() {
                const feedbackId = $(this).data('feedback-id');
                fetchFeedbackData(feedbackId);
            });
        }
    });

    // Initial attachment of click handlers
    $('.view-btn').on('click', function() {
        const feedbackId = $(this).data('feedback-id');
        fetchFeedbackData(feedbackId);
    });
});

function fetchFeedbackData(feedbackId) {
    $.ajax({
        url: 'get_feedback.php?id=' + feedbackId,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                console.error(data.error);
                alert('Error loading feedback details');
                return;
            }

            // Basic Information
            $('#modal-visit-date').text(data.visit_date || 'N/A');
            $('#modal-age').text(data.age || 'N/A');
            $('#modal-sex').text(data.sex || 'N/A');
            $('#modal-region').text(data.region || 'N/A');
            $('#modal-community').text(data.community || 'N/A');
            $('#modal-phone-number').text(data.phone_number || 'N/A');
            
            // Office Information
            $('#modal-office-name').text(data.office_name || 'N/A');
            $('#modal-service-availed').text(data.service_availed || 'N/A');
            $('#modal-service-type').text(data.service_type || 'N/A');
            $('#modal-sqd-average').text(data.sqd_average ? data.sqd_average.toFixed(1) : 'N/A');
            
            // Citizen's Charter
            $('#modal-cc1').text(data.cc1 || 'N/A');
            $('#modal-cc2').text(data.cc2 || 'N/A');
            $('#modal-cc3').text(data.cc3 || 'N/A');
            
            // Service Quality Dimensions
            $('#modal-sqd0').text(data.sqd0 || 'N/A');
            $('#modal-sqd1').text(data.sqd1 || 'N/A');
            $('#modal-sqd2').text(data.sqd2 || 'N/A');
            $('#modal-sqd3').text(data.sqd3 || 'N/A');
            $('#modal-sqd4').text(data.sqd4 || 'N/A');
            $('#modal-sqd5').text(data.sqd5 || 'N/A');
            $('#modal-sqd6').text(data.sqd6 || 'N/A');
            $('#modal-sqd7').text(data.sqd7 || 'N/A');
            $('#modal-sqd8').text(data.sqd8 || 'N/A');
            
            // Comments
            $('#modal-comments').text(data.comments || 'No comment');
            
            $('#feedbackModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('Error fetching feedback:', error);
            alert('Error loading feedback details');
        }
    });
}
</script>