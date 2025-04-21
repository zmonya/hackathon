<?php
session_start();
include 'header.php';
include 'sidebar.php';
require_once '../config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>Please log in to view feedback.</p>";
    exit();
}

// Dashboard Metrics
// 1. Total Feedback
$total_feedback_query = "SELECT COUNT(*) as total FROM feedback";
$total_feedback_stmt = $conn->prepare($total_feedback_query);
$total_feedback_stmt->execute();
$total_feedback_result = $total_feedback_stmt->get_result();
$total_feedback = $total_feedback_result->fetch_assoc()['total'] ?? 0;

// 2. Avg. Satisfaction - Now using the pre-calculated sqd_average
$avg_satisfaction_query = "SELECT AVG(sqd_average) as overall_avg FROM feedback WHERE sqd_average IS NOT NULL";
$avg_satisfaction_stmt = $conn->prepare($avg_satisfaction_query);
$avg_satisfaction_stmt->execute();
$avg_satisfaction_result = $avg_satisfaction_stmt->get_result();
$avg_row = $avg_satisfaction_result->fetch_assoc();
$avg_satisfaction = 'N/A';
if ($avg_row && $avg_row['overall_avg'] !== null) {
    $avg_satisfaction = round($avg_row['overall_avg'], 1);
}

// 3. Response Rate (unchanged)
$response_rate_query = "
    SELECT 
        (SUM(CASE WHEN comments IS NOT NULL AND comments != '' THEN 1 ELSE 0 END) / COUNT(*)) * 100 as rate
    FROM feedback
";
$response_rate_stmt = $conn->prepare($response_rate_query);
$response_rate_stmt->execute();
$response_rate_result = $response_rate_stmt->get_result();
$response_rate = $response_rate_result->fetch_assoc()['rate'] ?? 0;
$response_rate = $total_feedback ? round($response_rate) : 0;

// 4. Satisfaction by Office - Now using sqd_average
$satisfaction_by_office_query = "
    SELECT 
        o.office_name, 
        AVG(f.sqd_average) as avg_rating
    FROM feedback f
    JOIN offices o ON f.office_id = o.office_id
    WHERE f.sqd_average IS NOT NULL
    GROUP BY o.office_id, o.office_name
    ORDER BY o.office_name
";
$satisfaction_by_office_stmt = $conn->prepare($satisfaction_by_office_query);
$satisfaction_by_office_stmt->execute();
$satisfaction_by_office_result = $satisfaction_by_office_stmt->get_result();
$office_labels = [];
$office_ratings = [];
while ($row = $satisfaction_by_office_result->fetch_assoc()) {
    $office_labels[] = $row['office_name'];
    $office_ratings[] = round($row['avg_rating'], 1);
}

// 5. Rating Breakdown
$rating_breakdown_query = "
    SELECT 
        rating,
        COUNT(*) as count
    FROM (
        SELECT sqd0 as rating FROM feedback
        UNION ALL SELECT sqd1 FROM feedback
        UNION ALL SELECT sqd2 FROM feedback
        UNION ALL SELECT sqd3 FROM feedback
        UNION ALL SELECT sqd4 FROM feedback
        UNION ALL SELECT sqd5 FROM feedback
        UNION ALL SELECT sqd6 FROM feedback
        UNION ALL SELECT sqd7 FROM feedback
        UNION ALL SELECT sqd8 FROM feedback
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

// 6. Monthly Satisfaction Trend - Now using sqd_average
$selected_office = $_GET['trend_office_id'] ?? 'all';
$trend_where_clause = '';
$trend_params = [];
$trend_types = '';
if ($selected_office !== 'all') {
    $trend_where_clause = 'WHERE f.office_id = ?';
    $trend_params[] = intval($selected_office);
    $trend_types = 'i';
}

$monthly_trend_query = "
    SELECT 
        o.office_name,
        DATE_FORMAT(f.visit_date, '%Y-%m') as month,
        AVG(f.sqd_average) as avg_rating
    FROM feedback f
    JOIN offices o ON f.office_id = o.office_id
    $trend_where_clause
    GROUP BY o.office_id, o.office_name, YEAR(f.visit_date), MONTH(f.visit_date)
    ORDER BY o.office_name, YEAR(f.visit_date), MONTH(f.visit_date)
";

if ($trend_where_clause) {
    $monthly_trend_stmt = $conn->prepare($monthly_trend_query);
    $monthly_trend_stmt->bind_param($trend_types, ...$trend_params);
    $monthly_trend_stmt->execute();
    $monthly_trend_result = $monthly_trend_stmt->get_result();
} else {
    $monthly_trend_result = $conn->query($monthly_trend_query);
}

// Process trend data
$trend_data = [];
$all_months = [];
if ($monthly_trend_result) {
    while ($row = $monthly_trend_result->fetch_assoc()) {
        $office = $row['office_name'];
        $month = $row['month'];
        $rating = $row['avg_rating'] ? round($row['avg_rating'], 1) : null;
        if (!isset($trend_data[$office])) {
            $trend_data[$office] = [];
        }
        $trend_data[$office][$month] = $rating;
        $all_months[$month] = true;
    }
}

// Sort month labels
$month_labels = array_keys($all_months);
sort($month_labels);

// Fetch offices for dropdown
$offices_query = "SELECT office_id, office_name FROM offices ORDER BY office_name";
$offices_result = $conn->query($offices_query);
?>

<body>
    <div class="main-content">
        <div class="dashboard-section active">

            <!-- Success/Error Message Display -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

            
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
                    <h3 class="chart-title">Satisfaction by Office</h3>
                    <canvas id="satisfactionChart"></canvas>
                </div>
                <div class="chart-card">
                    <h3 class="chart-title">Rating Breakdown (All Questions)</h3>
                    <canvas id="ratingChart"></canvas>
                </div>
                <div class="chart-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="chart-title">Monthly Satisfaction Trend</h3>
                        <form method="GET" action="?section=dashboard" class="d-inline">
                            <select name="trend_office_id" class="form-select form-select-sm" style="width: 200px;" onchange="this.form.submit()">
                                <option value="all" <?php echo $selected_office === 'all' ? 'selected' : ''; ?>>All Offices</option>
                                <?php while ($office = $offices_result->fetch_assoc()): ?>
                                    <option value="<?php echo htmlspecialchars($office['office_id']); ?>"
                                            <?php echo $selected_office === (string)$office['office_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($office['office_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </form>
                    </div>
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        const officeLabels = <?php echo json_encode($office_labels); ?>;
        const officeRatings = <?php echo json_encode($office_ratings); ?>;
        const ratingLabels = <?php echo json_encode($rating_labels); ?>;
        const ratingCounts = <?php echo json_encode($rating_counts); ?>;
        const ratingColors = <?php echo json_encode(array_values($rating_colors)); ?>;
        const trendData = <?php echo json_encode($trend_data); ?>;
        const monthLabels = <?php echo json_encode($month_labels); ?>;
        const selectedOffice = '<?php echo $selected_office; ?>';

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
                    scales: { y: { beginAtZero: true, max: 5 } },
                    plugins: { legend: { display: false } }
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

            // Monthly Trend Chart
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            const colors = [
                'rgba(26, 60, 94, 0.7)',  // Blue
                'rgba(46, 125, 50, 0.7)', // Green
                'rgba(211, 47, 47, 0.7)', // Red
                'rgba(255, 179, 0, 0.7)', // Amber
                'rgba(123, 31, 162, 0.7)' // Purple
            ];

            let datasets = [];
            if (selectedOffice === 'all') {
                let colorIndex = 0;
                Object.keys(trendData).sort().forEach(office => {
                    const data = monthLabels.map(month => trendData[office][month] || null);
                    datasets.push({
                        label: office,
                        data: data,
                        borderColor: colors[colorIndex % colors.length],
                        backgroundColor: colors[colorIndex % colors.length].replace('0.7', '0.2'),
                        fill: false,
                        tension: 0.1
                    });
                    colorIndex++;
                });
            } else {
                const officeName = Object.keys(trendData)[0] || 'Selected Office';
                const data = monthLabels.map(month => trendData[officeName][month] || null);
                datasets.push({
                    label: officeName,
                    data: data,
                    borderColor: colors[0],
                    backgroundColor: colors[0].replace('0.7', '0.2'),
                    fill: false,
                    tension: 0.1
                });
            }

            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: monthLabels.length ? monthLabels : ['No Data'],
                    datasets: datasets.length ? datasets : [{
                        label: 'No Data',
                        data: [0],
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
                            title: { display: true, text: 'Satisfaction Rating' }
                        },
                        x: {
                            title: { display: true, text: 'Month' }
                        }
                    },
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    return value ? `${context.dataset.label}: ${value}` : `${context.dataset.label}: No Data`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>

