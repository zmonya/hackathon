<?php
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

// Function to get full feedback details
function getFeedbackDetails($conn, $feedback_id) {
    $query = "
        SELECT 
            f.*, 
            o.office_name,
            DATE_FORMAT(f.visit_date, '%Y-%m-%d') as visit_date,
            DATE_FORMAT(f.submitted_at, '%Y-%m-%d %H:%i:%s') as submitted_at
        FROM feedback f
        LEFT JOIN offices o ON f.office_id = o.office_id
        WHERE f.feedback_id = ?
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return ['error' => 'Database query failed: ' . $conn->error];
    }
    
    $stmt->bind_param('i', $feedback_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['error' => 'Feedback not found'];
    }
    
    return $result->fetch_assoc();
}

// Check if we're requesting feedback details via AJAX
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_feedback' && isset($_GET['feedback_id'])) {
    $feedback_id = intval($_GET['feedback_id']);
    $feedback = getFeedbackDetails($conn, $feedback_id);
    
    if ($feedback) {
        // Map numeric values to their meanings
        $cc1_map = [
            '1' => 'I know what a CC is and I saw this office\'s CC',
            '2' => 'I know what a CC is but I did NOT see this office\'s CC',
            '3' => 'I learned of the CC only when I saw this office\'s CC',
            '4' => 'I do not know what a CC is and I did not see one'
        ];
        
        $feedback['cc1_text'] = isset($cc1_map[$feedback['cc1']]) ? $cc1_map[$feedback['cc1']] : $feedback['cc1'];
        
        header('Content-Type: application/json');
        echo json_encode($feedback);
        exit();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Feedback not found']);
        exit();
    }
}
?>

<div class="feedback-section">
    <div class="header">
        <h1 class="page-title">Client Feedback</h1>
        <div class="user-profile">
            <div class="user-avatar">QA</div>
            <span>QA Admin</span>
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
                                    <button class="btn btn-sm btn-primary view-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewFeedbackModal"
                                            data-feedback-id="<?php echo htmlspecialchars($row['feedback_id']); ?>">
                                        <i class="fas fa-eye"></i> View
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

<!-- View Feedback Modal -->
<div class="modal fade" id="viewFeedbackModal" tabindex="-1" aria-labelledby="viewFeedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewFeedbackModalLabel">Feedback Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody id="feedback-details">
                        <!-- Populated by JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Initialize all view buttons
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const feedbackId = btn.dataset.feedbackId;
            fetchFeedbackDetails(feedbackId);
        });
    });
});

function fetchFeedbackDetails(feedbackId) {
    // Show loading state
    const tbody = document.getElementById('feedback-details');
    tbody.innerHTML = `
        <tr>
            <td colspan="2" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Loading feedback details...</p>
            </td>
        </tr>
    `;

    // Fetch the details from the same page
    fetch(`?section=feedback&ajax=get_feedback&feedback_id=${feedbackId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            populateFeedbackModal(data);
        })
        .catch(error => {
            console.error('Error fetching feedback:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="2" class="text-danger">Error loading feedback details: ${error.message}</td>
                </tr>
            `;
        });
}

function populateFeedbackModal(data) {
    const tbody = document.getElementById('feedback-details');
    tbody.innerHTML = '';

    const addRow = (label, value) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <th style="width: 30%;">${label}</th>
            <td>${value || 'N/A'}</td>
        `;
        tbody.appendChild(row);
    };

    // Personal Information
    addRow('Feedback ID', data.feedback_id);
    addRow('Visit Date', data.visit_date);
    addRow('Age', data.age || 'N/A');
    addRow('Sex', data.sex || 'N/A');
    addRow('Region', data.region || 'N/A');
    addRow('Phone Number', data.phone_number || 'N/A');
    
    // Office Information
    addRow('Office', data.office_name || 'N/A');
    addRow('Service Availed', data.service_availed || 'N/A');
    addRow('Community', data.community || 'N/A');
    
    // Citizen's Charter
    addRow('CC1. Awareness of Citizen\'s Charter', data.cc1_text || 'N/A');
    addRow('CC2. Visibility of CC', data.cc2 || 'N/A');
    addRow('CC3. Helpfulness of CC', data.cc3 || 'N/A');
    
    // Service Quality Dimensions
    addRow('SQD0. Overall Satisfaction', formatRating(data.sqd0));
    addRow('SQD1. Responsiveness', formatRating(data.sqd1));
    addRow('SQD2. Reliability', formatRating(data.sqd2));
    addRow('SQD3. Access and Facilities', formatRating(data.sqd3));
    addRow('SQD4. Communication', formatRating(data.sqd4));
    addRow('SQD5. Costs', formatRating(data.sqd5));
    addRow('SQD6. Integrity', formatRating(data.sqd6));
    addRow('SQD7. Assurance', formatRating(data.sqd7));
    addRow('SQD8. Outcome', formatRating(data.sqd8));
    
    // Comments
    addRow('Comment Type', data.comment_type || 'N/A');
    addRow('Comments', data.comments || 'No comments provided');
    
    // Metadata
    addRow('Submitted At', data.submitted_at || 'N/A');
}

function formatRating(value) {
    if (!value || value === 'NA') return 'N/A';

    const ratingMap = {
        '1': '1 - Strongly Disagree',
        '2': '2 - Disagree',
        '3': '3 - Neutral',
        '4': '4 - Agree',
        '5': '5 - Strongly Agree'
    };

    return ratingMap[value] || value;
}
</script>