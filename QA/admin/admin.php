<?php
require_once '../config.php';
session_start();

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    $_SESSION = []; // Clear session data
    session_destroy(); // Destroy session
    header("Location: ../login.php"); // Redirect to QA/login.php
    exit();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
} elseif ($_SESSION['role_id'] != 2) {
    // Forbidden for non-admins
    http_response_code(403);
    echo "<h1>403 Forbidden</h1><p>You do not have permission to access this page.</p>";
    exit();
}

// Dashboard Metrics
$total_feedback_query = "SELECT COUNT(*) as total FROM feedback";
$total_feedback_result = $conn->query($total_feedback_query);
$total_feedback = $total_feedback_result->fetch_assoc()['total'] ?? 0;

$avg_satisfaction_query = "
    SELECT AVG(CAST(sqd0 AS DECIMAL)) as avg_sqd0
    FROM feedback
    WHERE sqd0 != 'NA' AND sqd0 IN ('1', '2', '3', '4', '5')
";
$avg_satisfaction_result = $conn->query($avg_satisfaction_query);
$avg_satisfaction = $avg_satisfaction_result->fetch_assoc()['avg_sqd0'] ?? null;
$avg_satisfaction = $avg_satisfaction ? round($avg_satisfaction, 1) : 'N/A';

$response_rate_query = "
    SELECT 
        (SUM(CASE WHEN comments IS NOT NULL AND comments != '' THEN 1 ELSE 0 END) / COUNT(*)) * 100 as rate
    FROM feedback
";
$response_rate_result = $conn->query($response_rate_query);
$response_rate = $response_rate_result->fetch_assoc()['rate'] ?? 0;
$response_rate = $total_feedback ? round($response_rate) : 0;

$satisfaction_by_office_query = "
    SELECT 
        o.office_name, 
        AVG(CAST(f.sqd0 AS DECIMAL)) as avg_rating
    FROM feedback f
    JOIN offices o ON f.office_id = o.office_id
    WHERE f.sqd0 != 'NA' AND f.sqd0 IN ('1', '2', '3', '4', '5')
    GROUP BY o.office_id, o.office_name
    ORDER BY o.office_name
";
$satisfaction_by_office_result = $conn->query($satisfaction_by_office_query);
$office_labels = [];
$office_ratings = [];
while ($row = $satisfaction_by_office_result->fetch_assoc()) {
    $office_labels[] = $row['office_name'];
    $office_ratings[] = round($row['avg_rating'], 1);
}

$rating_breakdown_query = "
    SELECT 
        COALESCE(sqd0, 'NA') as rating,
        COUNT(*) as count
    FROM feedback
    GROUP BY sqd0
    ORDER BY 
        CASE 
            WHEN sqd0 = 'NA' THEN 6
            ELSE CAST(sqd0 AS INT)
        END DESC
";
$rating_breakdown_result = $conn->query($rating_breakdown_query);
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PQA Client Satisfaction Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #1a3c5e;
            --secondary-color: #f4f7fa;
            --accent-color: #d32f2f;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { display: flex; background-color: var(--secondary-color); }
        .sidebar { width: var(--sidebar-width); height: 100vh; background: var(--primary-color); color: white; padding: 20px 0; position: fixed; z-index: 1000; }
        .sidebar-header { padding: 0 20px 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .sidebar-menu { padding: 20px 0; }
        .menu-item { padding: 12px 20px; display: flex; align-items: center; cursor: pointer; transition: background 0.3s; user-select: none; color: white; text-decoration: none; }
        .menu-item:hover { background: rgba(255, 255, 255, 0.1); }
        .menu-item.active { background: rgba(255, 255, 255, 0.2); border-left: 4px solid var(--accent-color); }
        .menu-item i { margin-right: 10px; font-size: 18px; }
        .main-content { margin-left: var(--sidebar-width); width: calc(100% - var(--sidebar-width)); padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .page-title { font-size: 22px; color: #333; }
        .user-profile { display: flex; align-items: center; }
        .user-avatar { width: 35px; height: 35px; border-radius: 50%; background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; margin-right: 8px; font-size: 14px; }
        .card-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 15px; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .card-title { font-size: 14px; color: #666; }
        .card-value { font-size: 22px; font-weight: bold; color: #333; }
        .chart-container { display: grid; grid-template-columns: 2fr 1fr; gap: 15px; margin-bottom: 20px; }
        .chart-card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 15px; }
        .chart-title { margin-bottom: 15px; color: #333; font-size: 16px; }
        .table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
        .table th, .table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #eee; }
        .table th { background: var(--primary-color); color: white; font-size: 13px; }
        .table tr:hover { background: #f8f9fa; }
        .rating-badge { padding: 3px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .rating-5 { background: #2e7d32; color: white; }
        .rating-4 { background: #4caf50; color: white; }
        .rating-3 { background: #ffb300; color: #333; }
        .rating-2 { background: #f57c00; color: white; }
        .rating-1 { background: #d32f2f; color: white; }
        .dashboard-section, .reports-section, .feedback-section, .user-management-section { display: none; }
        .dashboard-section.active, .reports-section.active, .feedback-section.active, .user-management-section.active { display: block; }
        .modal-content { border-radius: 8px; padding: 20px; }
        .modal-header { background: var(--primary-color); color: white; border-bottom: none; }
        .modal-footer { border-top: none; }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Feedback System</h2>
        </div>
        <div class="sidebar-menu">
            <div class="menu-item active" onclick="showSection('dashboard')">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </div>
            <div class="menu-item" onclick="showSection('reports')">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </div>
            <div class="menu-item" onclick="showSection('feedback')">
                <i class="fas fa-comment-alt"></i>
                <span>Feedback</span>
            </div>
            <div class="menu-item" onclick="showSection('user-management')">
                <i class="fas fa-users-cog"></i>
                <span>User Management</span>
            </div>
            <a href="?logout=1" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Dashboard Section -->
        <div class="dashboard-section active">
            <div class="header">
                <h1 class="page-title">Client Satisfaction Overview</h1>
                <div class="user-profile">
                    <div class="user-avatar">QA</div>
                    <span>QA Admin</span>
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
                    <h3 class="chart-title">Rating Breakdown</h3>
                    <canvas id="ratingChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Reports Section -->
        <?php include 'report.php'; ?>

        <!-- Feedback Section -->
        <?php include 'feedback.php'; ?>

        <!-- User Management Section -->
        <div class="user-management-section">
            <?php include 'user_management_partial.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Pass PHP data to JavaScript
        const officeLabels = <?php echo json_encode($office_labels); ?>;
        const officeRatings = <?php echo json_encode($office_ratings); ?>;
        const ratingLabels = <?php echo json_encode($rating_labels); ?>;
        const ratingCounts = <?php echo json_encode($rating_counts); ?>;
        const ratingColors = <?php echo json_encode(array_values($rating_colors)); ?>;

        // Initialize Charts
        document.addEventListener('DOMContentLoaded', () => {
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
                    plugins: { legend: { position: 'bottom' } }
                }
            });

            showSection('dashboard');
        });

        function showSection(section) {
            const sections = document.querySelectorAll('.dashboard-section, .reports-section, .feedback-section, .user-management-section');
            sections.forEach(el => el.classList.remove('active'));
            const targetSection = document.querySelector(`.${section}-section`);
            if (targetSection) {
                targetSection.classList.add('active');
            } else {
                console.error(`Section ${section}-section not found`);
            }

            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => item.classList.remove('active'));
            const targetMenu = document.querySelector(`.menu-item[onclick="showSection('${section}')"]`);
            if (targetMenu) {
                targetMenu.classList.add('active');
            } else {
                console.error(`Menu item for ${section} not found`);
            }
        }

        document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', () => {
                console.log('Menu item clicked:', item.textContent);
            });
        });
    </script>
</body>
</html>