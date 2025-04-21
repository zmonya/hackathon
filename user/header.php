<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PQA Client Satisfaction Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    
    <style>
            :root {
            --sidebar-width: 250px;
            --primary-color: #1a3c5e;
            --secondary-color: #f4f7fa;
            --accent-color: #d32f2f;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            display: flex;
            background-color: var(--secondary-color);
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--primary-color);
            color: white;
            padding: 20px 0;
            position: fixed;
        }

        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
            color: white;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .menu-item.active {
            background: rgba(255, 255, 255, 0.2);
            border-left: 4px solid var(--accent-color);
        }

        .menu-item i {
            margin-right: 10px;
            font-size: 18px;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .page-title {
            font-size: 22px;
            color: #333;
        }

        .user-profile {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            font-size: 14px;
        }

        /* Dashboard Cards */
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .card-title {
            font-size: 14px;
            color: #666;
        }

        .card-value {
            font-size: 22px;
            font-weight: bold;
            color: #333;
        }

        /* Charts Section */
        .chart-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .chart-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }

        .chart-title {
            margin-bottom: 15px;
            color: #333;
            font-size: 16px;
        }

        /* Feedback Section */
        .feedback-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 20px;
        }

        .chart-card {
            margin-bottom: 20px;
        }

        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .table th, .table td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .table th {
            background: var(--primary-color);
            color: white;
            font-size: 13px;
        }

        .table tr:hover {
            background: #f8f9fa;
        }

        .rating-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }

        .rating-badge.rating-5 { background: #2e7d32; } /* Dark green */
        .rating-badge.rating-4 { background: #4caf50; } /* Green */
        .rating-badge.rating-3 { background: #ffb300; color: #333; } /* Yellow */
        .rating-badge.rating-2 { background: #f57c00; } /* Orange */
        .rating-badge.rating-1 { background: #d32f2f; } /* Red */
        .rating-badge { background: #757575; } /* Default gray for N/A */

                .notification-icon {
            cursor: pointer;
            position: relative;
            transition: transform 0.3s;
        }

        .notification-icon:hover {
            transform: scale(1.1);
        }

        .notification-icon .badge {
            font-size: 0.6rem;
            padding: 3px 6px;
            position: absolute;
            top: -5px;
            right: -5px;
        }

        .list-group-item {
            border-left: 4px solid;
            border-left-color: var(--bs-gray-200);
            transition: all 0.2s;
            margin-bottom: 5px;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
            border-left-color: var(--bs-primary);
        }

        /* Color coding for ratings */
        .bg-success {
            background-color: #2e7d32 !important;
        }

        .bg-warning {
            background-color: #ffb300 !important;
            color: #000 !important;
        }

        .bg-danger {
            background-color: #d32f2f !important;
        }

        /* Service type badges */
        .badge-success {
            background-color: #2e7d32;
        }

        .badge-danger {
            background-color: #d32f2f;
        }
    </style>
</head>

        
    </div>
</body>
</html>