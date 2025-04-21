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

// 2. Average Satisfaction (using sqd_average)
$avg_satisfaction_query = "
    SELECT AVG(sqd_average) as overall_avg
    FROM feedback
    WHERE office_id = ? 
    AND sqd_average IS NOT NULL
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

// 4. Monthly Satisfaction Trend (using sqd_average)
$monthly_trend_query = "
    SELECT 
        DATE_FORMAT(visit_date, '%Y-%m') as month,
        AVG(sqd_average) as avg_rating
    FROM feedback
    WHERE office_id = ?
    AND sqd_average IS NOT NULL
    GROUP BY YEAR(visit_date), MONTH(visit_date)
    ORDER BY YEAR(visit_date), MONTH(visit_date)
";
$monthly_trend_stmt = $conn->prepare($monthly_trend_query);
$monthly_trend_stmt->bind_param('i', $user_office_id);
$monthly_trend_stmt->execute();
$monthly_trend_result = $monthly_trend_stmt->get_result();

$month_labels = [];
$month_ratings = [];
while ($row = $monthly_trend_result->fetch_assoc()) {
    $month_labels[] = $row['month'];
    $month_ratings[] = round($row['avg_rating'], 1);
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
            <?php include 'notification.php'; ?>

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
                    <h3 class="chart-title">Monthly Satisfaction Trend</h3>
                    <canvas id="trendChart"></canvas>
                </div>
                <div class="chart-card">
                    <h3 class="chart-title">Rating Breakdown</h3>
                    <canvas id="ratingChart"></canvas>
                </div>
            </div>
        </div>

        <script>
            // Pass PHP data to JavaScript
            const monthLabels = <?php echo json_encode($month_labels); ?>;
            const monthRatings = <?php echo json_encode($month_ratings); ?>;
            const ratingLabels = <?php echo json_encode($rating_labels); ?>;
            const ratingCounts = <?php echo json_encode($rating_counts); ?>;
            const ratingColors = <?php echo json_encode(array_values($rating_colors)); ?>;

            // Initialize Charts
            document.addEventListener('DOMContentLoaded', () => {
                // Monthly Trend Chart
                const trendCtx = document.getElementById('trendChart').getContext('2d');
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: monthLabels.length ? monthLabels : ['No Data'],
                        datasets: [{
                            label: 'Average Satisfaction',
                            data: monthRatings.length ? monthRatings : [0],
                            backgroundColor: 'rgba(26, 60, 94, 0.2)',
                            borderColor: 'rgba(26, 60, 94, 1)',
                            borderWidth: 2,
                            tension: 0.1,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 5,
                                title: {
                                    display: true,
                                    text: 'Satisfaction Rating'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
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