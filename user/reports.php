<?php
session_start();
include 'header.php';
include 'sidebar.php';
require_once '../config.php';

// Fetch the logged-in user's office ID
$user_office_id = $_SESSION['office_id'] ?? null;

// Get selected period and format from GET
$selected_period = $_GET['period'] ?? 'monthly';
$selected_format = $_GET['format'] ?? 'csv';

// Validate period
$valid_periods = ['daily', 'weekly', 'monthly', 'yearly'];
if (!in_array($selected_period, $valid_periods)) {
    $selected_period = 'monthly';
}

// Validate format
$valid_formats = ['csv', 'pdf'];
if (!in_array($selected_format, $valid_formats)) {
    $selected_format = 'csv';
}

// Get office name for the user
$selected_office_name = 'Unknown Office';
if ($user_office_id) {
    $office_name_query = "SELECT office_name FROM offices WHERE office_id = ?";
    $office_name_stmt = $conn->prepare($office_name_query);
    $office_name_stmt->bind_param('i', $user_office_id);
    $office_name_stmt->execute();
    $office_name_result = $office_name_stmt->get_result();
    if ($office_name_result->num_rows > 0) {
        $selected_office_name = $office_name_result->fetch_assoc()['office_name'];
    }
    $office_name_stmt->close();
}

// Build query using the existing sqd_average from database
$where_clause = 'WHERE f.office_id = ?';
$params = [$user_office_id];
$types = 'i';
$group_by = '';
$period_format = '';

switch ($selected_period) {
    case 'daily':
        $group_by = "DATE(f.visit_date)";
        $period_format = "DATE_FORMAT(f.visit_date, '%Y-%m-%d') AS period";
        break;
    case 'weekly':
        // Corrected weekly grouping - uses ISO week year and week
        $group_by = "YEARWEEK(f.visit_date, 3)"; // Mode 3: ISO week (Monday as first day, week 1 is first week with 4+ days)
        $period_format = "CONCAT(
            DATE_FORMAT(DATE_SUB(f.visit_date, INTERVAL WEEKDAY(f.visit_date) DAY), '%Y-%m-%d'), 
            ' - ', 
            DATE_FORMAT(DATE_ADD(DATE_SUB(f.visit_date, INTERVAL WEEKDAY(f.visit_date) DAY), INTERVAL 6 DAY), '%Y-%m-%d')
        ) AS period";
        break;
    case 'monthly':
        $group_by = "YEAR(f.visit_date), MONTH(f.visit_date)";
        $period_format = "DATE_FORMAT(f.visit_date, '%b %Y') AS period";
        break;
    case 'yearly':
        $group_by = "YEAR(f.visit_date)";
        $period_format = "YEAR(f.visit_date) AS period";
        break;
}

$query = "
    SELECT 
        o.office_name,
        $period_format,
        AVG(f.sqd_average) AS avg_rating,
        COUNT(f.feedback_id) AS responses
    FROM feedback f
    LEFT JOIN offices o ON f.office_id = o.office_id
    $where_clause
    GROUP BY o.office_id, o.office_name" . ($group_by ? ", $group_by" : "") . "
    ORDER BY o.office_name, MAX(f.visit_date) DESC
";

try {
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    error_log("SQL Error in report.php: " . $e->getMessage());
    $result = null;
}
?>

<div class="main-content">
    <?php include 'notification.php'; ?>

    <div class="chart-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="chart-title">Performance Reports</h3>
            <div class="d-flex gap-3">
                <form method="GET" action="?section=reports" class="d-inline">
                    <select name="period" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                        <option value="daily" <?php echo $selected_period === 'daily' ? 'selected' : ''; ?>>Daily</option>
                        <option value="weekly" <?php echo $selected_period === 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                        <option value="monthly" <?php echo $selected_period === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                        <option value="yearly" <?php echo $selected_period === 'yearly' ? 'selected' : ''; ?>>Yearly</option>
                    </select>
                </form>
                <form id="download-form" class="d-inline">
                    <select name="format" id="download-format" class="form-select form-select-sm" style="width: 150px;" onchange="downloadReport(this.value)">
                        <option value="" selected hidden>Download</option>
                        <option value="csv">CSV</option>
                        <option value="pdf">PDF</option>
                    </select>

                </form>
            </div>
        </div>
        <div>
            <table class="table table-striped table-hover" id="reports-table">
                <thead>
                    <tr>
                        <th class="text-center">Office</th>
                        <th class="text-center">Period</th>
                        <th class="text-center">Avg. Rating</th>
                        <th class="text-center">Responses</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                
                                <td class="text-center"><?php echo htmlspecialchars($row['office_name'] ?? 'N/A'); ?></td>
                                <td class="text-center" data-sort="<?php 
                                    $period = $row['period'] ?? '';
                                    if ($period === 'N/A') {
                                        echo '0';
                                    } elseif ($selected_period === 'weekly') {
                                        // For weekly periods formatted as "YYYY-MM-DD - YYYY-MM-DD"
                                        $dates = explode(' - ', $period);
                                        echo strtotime($dates[0] ?? ''); // Use start date of the week
                                    } elseif ($selected_period === 'monthly') {
                                        echo strtotime($period); // Works for "Jan 2023" format
                                    } elseif ($selected_period === 'yearly' && is_numeric($period)) {
                                        echo strtotime("$period-01-01");
                                    } else {
                                        // Default for daily or other formats
                                        echo strtotime($period);
                                    }
                                ?>">
                                    <?php echo htmlspecialchars($period); ?>
                                </td>
                                <td class="text-center">
                                    <?php 
                                    $rating = $row['avg_rating'] ? number_format(round($row['avg_rating'], 1), 1) : 'N/A';
                                    $ratingClass = is_numeric($rating) ? 'rating-' . floor($rating) : '';
                                    ?>
                                    <span class="rating-badge <?php echo $ratingClass; ?>">
                                        <?php echo $rating; ?>
                                    </span>
                                </td>
                                <td class="text-center"><?php echo number_format($row['responses']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No data available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
    $(document).ready(function() {
        $('.table').DataTable({
            "paging": true,
            "order": [[0, "desc"]], 
            "searching": true,
            "ordering": true,
            "info": true
        });
    });

    function downloadReport(format) {
    const table = document.getElementById('reports-table');
    const rows = table.querySelectorAll('tbody tr');
    const periodType = '<?php echo $selected_period; ?>';
    const officeName = '<?php echo addslashes($selected_office_name); ?>';
    const capitalizedPeriod = periodType.charAt(0).toUpperCase() + periodType.slice(1);
    const title = `Satisfaction Report - ${capitalizedPeriod} - ${officeName}`;
    const filename = `satisfaction_report_${periodType}_${officeName.replace(/\s+/g, '_')}.${format}`;

    if (format === 'csv') {
        let csvContent = `"${title}"\n\nOffice,Period,Avg. Rating,Responses\n`;

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length >= 4) {
                const office = cells[0].textContent.trim();
                let period = cells[1].textContent.trim();
                const avgRating = cells[2].textContent.trim();
                const responses = cells[3].textContent.trim();

                // Format period for Excel compatibility
                if (periodType === 'monthly') {
                    const [monthStr, year] = period.split(' ');
                    const monthMap = {
                        'Jan': '01', 'Feb': '02', 'Mar': '03', 'Apr': '04',
                        'May': '05', 'Jun': '06', 'Jul': '07', 'Aug': '08',
                        'Sep': '09', 'Oct': '10', 'Nov': '11', 'Dec': '12'
                    };
                    period = `${year}-${monthMap[monthStr] || '01'}`;
                } else if (periodType === 'weekly') {
                    period = period.replace(/ - /g, ' to ');
                }

                const escapeCsv = (str) => `"${str.replace(/"/g, '""')}"`;
                const rowData = [
                    escapeCsv(office),
                    escapeCsv(period),
                    escapeCsv(avgRating),
                    escapeCsv(responses)
                ].join(',');

                csvContent += rowData + '\n';
            }
        });

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    } else if (format === 'pdf') {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Add title (centered)
        doc.setFontSize(16);
        const pageWidth = doc.internal.pageSize.getWidth();
        const textWidth = doc.getTextWidth(title);
        const xOffset = (pageWidth - textWidth) / 2;
        doc.text(title, xOffset, 20);

        // Add table
        const tableData = [];
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length >= 4) {
                tableData.push([
                    cells[0].textContent.trim(),
                    cells[1].textContent.trim(),
                    cells[2].textContent.trim(),
                    cells[3].textContent.trim()
                ]);
            }
        });

        doc.autoTable({
            head: [['Office', 'Period', 'Avg. Rating', 'Responses']],
            body: tableData.length ? tableData : [['No data available', '', '', '']],
            startY: 30,
            theme: 'striped',
            headStyles: { fillColor: [26, 60, 94] },
            styles: { fontSize: 10 }
        });

        doc.save(filename);
    }
}
</script>
