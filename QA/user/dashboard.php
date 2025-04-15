<?php
session_start();
include 'header.php';
include 'sidebar.php';
require_once '../config.php';

// Check if user is logged in
if (!isset($_SESSION['office_id'])) {
    echo "<p>Please log in to view feedback.</p>";
    exit();
}

$user_office_id = $_SESSION['office_id'] ?? null;

// 1. Total Feedback Count
$total_feedback_query = "SELECT COUNT(*) as total FROM feedback WHERE office_id = ?";
$total_feedback_stmt = $conn->prepare($total_feedback_query);
$total_feedback_stmt->bind_param('i', $user_office_id);
$total_feedback_stmt->execute();
$total_feedback_result = $total_feedback_stmt->get_result();
$total_feedback = $total_feedback_result->fetch_assoc()['total'] ?? 0;

// 2. Average Satisfaction (using only sqd0 to match the office chart)
$avg_satisfaction_query = "
    SELECT AVG(CAST(sqd0 AS DECIMAL)) as overall_avg
    FROM feedback
    WHERE office_id = ? 
    AND sqd0 != 'NA' 
    AND sqd0 IN ('1', '2', '3', '4', '5')
";
$avg_satisfaction_stmt = $conn->prepare($avg_satisfaction_query);
$avg_satisfaction_stmt->bind_param('i', $user_office_id);
$avg_satisfaction_stmt->execute();
$avg_satisfaction_result = $avg_satisfaction_stmt->get_result();
$avg_satisfaction = $avg_satisfaction_result->fetch_assoc()['overall_avg'] ?? null;
$avg_satisfaction = $avg_satisfaction ? round($avg_satisfaction, 1) : 'N/A';

// 3. Response Rate
$response_rate_query = "
    SELECT 
        (SUM(CASE WHEN comments IS NOT NULL AND comments != '' THEN 1 ELSE 0 END) / COUNT(*)) * 100 as rate
    FROM feedback
    WHERE office_id = ?
";
$response_rate_stmt = $conn->prepare($response_rate_query);
$response_rate_stmt->bind_param('i', $user_office_id);
$response_rate_stmt->execute();
$response_rate_result = $response_rate_stmt->get_result();
$response_rate = $response_rate_result->fetch_assoc()['rate'] ?? 0;
$response_rate = $total_feedback ? round($response_rate) : 0;

// 4. Satisfaction by Office (using sqd0 only - original design)
$satisfaction_by_office_query = "
    SELECT 
        o.office_name, 
        AVG(CAST(f.sqd0 AS DECIMAL)) as avg_rating
    FROM feedback f
    JOIN offices o ON f.office_id = o.office_id
    WHERE f.sqd0 != 'NA' 
    AND f.sqd0 IN ('1', '2', '3', '4', '5')
    AND f.office_id = ?
    GROUP BY o.office_id, o.office_name
    ORDER BY o.office_name
";
$satisfaction_by_office_stmt = $conn->prepare($satisfaction_by_office_query);
$satisfaction_by_office_stmt->bind_param('i', $user_office_id);
$satisfaction_by_office_stmt->execute();
$satisfaction_by_office_result = $satisfaction_by_office_stmt->get_result();
$office_labels = [];
$office_ratings = [];
while ($row = $satisfaction_by_office_result->fetch_assoc()) {
    $office_labels[] = $row['office_name'];
    $office_ratings[] = round($row['avg_rating'], 1);
}

// 5. Rating Breakdown (all questions)
$rating_breakdown_query = "
    SELECT 
        rating,
        COUNT(*) as count
    FROM (
        SELECT sqd0 as rating FROM feedback WHERE office_id = ?
        UNION ALL SELECT sqd1 FROM feedback WHERE office_id = ?
        UNION ALL SELECT sqd2 FROM feedback WHERE office_id = ?
        UNION ALL SELECT sqd3 FROM feedback WHERE office_id = ?
        UNION ALL SELECT sqd4 FROM feedback WHERE office_id = ?
        UNION ALL SELECT sqd5 FROM feedback WHERE office_id = ?
        UNION ALL SELECT sqd6 FROM feedback WHERE office_id = ?
        UNION ALL SELECT sqd7 FROM feedback WHERE office_id = ?
        UNION ALL SELECT sqd8 FROM feedback WHERE office_id = ?
    ) all_ratings
    WHERE rating IN ('1', '2', '3', '4', '5', 'NA')
    GROUP BY rating
    ORDER BY 
        CASE 
            WHEN rating = 'NA' THEN 6
            ELSE CAST(rating AS INT)
        END DESC
";
$rating_breakdown_stmt = $conn->prepare($rating_breakdown_query);
$rating_breakdown_stmt->bind_param('iiiiiiiii', 
    $user_office_id, $user_office_id, $user_office_id, 
    $user_office_id, $user_office_id, $user_office_id,
    $user_office_id, $user_office_id, $user_office_id
);
$rating_breakdown_stmt->execute();
$rating_breakdown_result = $rating_breakdown_stmt->get_result();

$rating_labels = [];
$rating_counts = [];
$rating_colors = [
    '5' => '#2e7d32',
    '4' => '#4caf50',
    '3' => '#ffb300',
    '2' => '#f57c00',
    '1' => '#d32f2f',
    'NA' => '#757575'
];

while ($row = $rating_breakdown_result->fetch_assoc()) {
    $rating = $row['rating'];
    $rating_labels[] = $rating === 'NA' ? 'N/A' : "$rating Star" . ($rating == '1' ? '' : 's');
    $rating_counts[] = $row['count'];
}
?>

<body>
    <!-- Main Content -->
    <div class="main-content">
        <!-- Dashboard Section -->
        <div class="dashboard-section active">
            <div class="header">
                <h1 class="page-title">Dashboard</h1>
                <div class="user-profile">
                    <div class="notification-icon" style="position: relative; margin-right: 10px;">
                        <i class="fas fa-bell" style="font-size: 20px;"></i>
                    </div>
                    <div class="user-avatar">U</div>
                    <span>User</span>
                </div>
            </div>

            <div class="card-container">
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Total Feedback</span>
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-value"><?php echo number_format($total_feedback); ?></div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Avg. Satisfaction</span>
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="card-value"><?php echo $avg_satisfaction; ?></div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Response Rate</span>
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="card-value"><?php echo $response_rate; ?>%</div>
                </div>
            </div>

            <div class="chart-container">
                <div class="chart-card">
                    <h3 class="chart-title">Satisfaction by Office</h3>
                    <canvas id="satisfactionChart"></canvas>
                </div>
                <div class="chart-card">
                    <h3 class="chart-title">Rating Breakdown (All Questions)</h3>
                    <canvas id="ratingChart"></canvas>
                </div>
            </div>
        </div>

        <script>
            // Pass PHP data to JavaScript
            const officeLabels = <?php echo json_encode($office_labels); ?>;
            const officeRatings = <?php echo json_encode($office_ratings); ?>;
            const ratingLabels = <?php echo json_encode($rating_labels); ?>;
            const ratingCounts = <?php echo json_encode($rating_counts); ?>;
            const ratingColors = <?php echo json_encode(array_values($rating_colors)); ?>;

            // Initialize Charts (original design)
            document.addEventListener('DOMContentLoaded', () => {
                // Satisfaction Chart
                const satisfactionCtx = document.getElementById('satisfactionChart').getContext('2d');
                new Chart(satisfactionCtx, {
                    type: 'bar',
                    data: {
                        labels: officeLabels.length ? officeLabels : ['No Data'],
                        datasets: [{
                            label: 'Average Rating',
                            data: officeRatings.length ? officeRatings : [0],
                            backgroundColor: 'rgba(26, 60, 94, 0.7)',
                            borderColor: 'rgba(26, 60, 94, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: { beginAtZero: true, max: 5 }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });

                // Rating Breakdown Chart
                const ratingCtx = document.getElementById('ratingChart').getContext('2d');
                new Chart(ratingCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ratingLabels.length ? ratingLabels : ['No Data'],
                        datasets: [{
                            data: ratingCounts.length ? ratingCounts : [1],
                            backgroundColor: ratingCounts.length ? ratingColors : ['#ccc'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((value / total) * 100);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    </div>
</body>
</html>