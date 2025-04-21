<?php
require_once '../config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>Please log in to view notifications.</p>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user information
$username_query = "SELECT username FROM users WHERE user_id = ?";
$username_stmt = $conn->prepare($username_query);
$username_stmt->bind_param('i', $user_id);
$username_stmt->execute();
$username_result = $username_stmt->get_result();
$username = $username_result->fetch_assoc()['username'] ?? 'User';

// Determine the avatar based on the first letter of the username
$avatar_initial = strtoupper(substr($username, 0, 1));

// Fetch unread notifications count
$notification_count_query = "SELECT COUNT(*) as count FROM notifications WHERE is_read = 0";
$notification_count_stmt = $conn->prepare($notification_count_query);
$notification_count_stmt->execute();
$notification_count_result = $notification_count_stmt->get_result();
$notification_count = $notification_count_result->fetch_assoc()['count'] ?? 0;

// Define page titles based on current page
$current_page = basename($_SERVER['PHP_SELF']);
$page_titles = [
    'dashboard.php' => 'Dashboard',
    'reports.php' => 'Reports',
    'feedback.php' => 'Feedbacks',
    'office_management.php' => 'Office Management',
    'user_management.php' => 'User Management'
];
$page_title = $page_titles[$current_page] ?? 'Dashboard';
?>

<div class="header">
    <h1 class="page-title"><?php echo htmlspecialchars($page_title); ?></h1>
    <div class="user-profile">
        <div class="notification-icon" style="position: relative; margin-right: 10px;">
            <i class="fas fa-bell" style="font-size: 20px;" data-bs-toggle="modal" data-bs-target="#notificationModal"></i>
            <?php if ($notification_count > 0): ?>
                <span class="badge bg-danger" style="position: absolute; top: -5px; right: -10px;">
                    <?php echo $notification_count; ?>
                </span>
            <?php endif; ?>
        </div>
        <div class="user-avatar">
            <?php echo htmlspecialchars($avatar_initial); ?>
        </div>
        <span><?php echo htmlspecialchars($username); ?></span>
    </div>
</div>
           <!-- Modal for Notifications -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Recent Feedback Notifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php
                // Fetch notifications with feedback details
                $notifications_query = "
                    SELECT n.notification_id, n.message, n.is_read, f.feedback_id, f.sqd_average, f.service_type, 
                           f.comments, f.submitted_at, o.office_name
                    FROM notifications n
                    JOIN feedback f ON n.feedback_id = f.feedback_id
                    JOIN offices o ON f.office_id = o.office_id
                    ORDER BY n.created_at DESC
                    LIMIT 10
                ";
                $notifications_result = $conn->query($notifications_query);

                if ($notifications_result && $notifications_result->num_rows > 0) {
                    echo '<div class="list-group">';
                    while ($row = $notifications_result->fetch_assoc()) {
                        $rating = $row['sqd_average'];
                        $service_type = $row['service_type'];
                        $comment = htmlspecialchars($row['comments'] ?? 'No comments provided');
                        $submitted = date('M j, Y g:i A', strtotime($row['submitted_at']));
                        $is_read = $row['is_read'];
                        $notification_id = $row['notification_id'];

                        // Determine badge color based on rating
                        $badge_class = 'bg-secondary';
                        if ($rating !== null) {
                            if ($rating >= 4) {
                                $badge_class = 'bg-success';
                            } elseif ($rating >= 3) {
                                $badge_class = 'bg-warning text-dark';
                            } else {
                                $badge_class = 'bg-danger';
                            }
                        }
                        ?>
                        <div class="list-group-item <?php echo $is_read ? '' : 'fw-bold'; ?>" data-notification-id="<?php echo $notification_id; ?>">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?php echo htmlspecialchars($row['office_name']); ?></h6>
                                <small><?php echo $submitted; ?></small>
                            </div>
                            <p class="mb-1"><?php echo htmlspecialchars($row['message']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <?php if ($rating !== null): ?>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        Rating: <?php echo number_format($rating, 1); ?>/5
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">No rating</span>
                                <?php endif; ?>
                                <span class="badge bg-<?php echo $service_type === 'Good' ? 'success' : 'danger'; ?>">
                                    <?php echo $service_type; ?>
                                </span>
                            </div>
                            <?php if (!empty($row['comments'])): ?>
                                <div class="mt-2">
                                    <small class="text-muted">Comment: <?php echo $comment; ?></small>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                    echo '</div>';
                } else {
                    echo '<div class="alert alert-info">No notifications found</div>';
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationModal = document.getElementById('notificationModal');
    
    notificationModal.addEventListener('shown.bs.modal', function() {
        // Get all unread notification IDs
        const unreadNotifications = document.querySelectorAll('.list-group-item.fw-bold');
        const notificationIds = Array.from(unreadNotifications).map(el => el.dataset.notificationId);
        
        if (notificationIds.length > 0) {
            // Send AJAX request to mark notifications as read
            fetch('mark_notifications_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ notification_ids: notificationIds }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI - remove bold styling and update badge count
                    unreadNotifications.forEach(el => el.classList.remove('fw-bold'));
                    
                    // Update notification badge count
                    const notificationBadge = document.querySelector('.notification-icon .badge');
                    if (notificationBadge) {
                        const currentCount = parseInt(notificationBadge.textContent);
                        const newCount = currentCount - notificationIds.length;
                        if (newCount > 0) {
                            notificationBadge.textContent = newCount;
                        } else {
                            notificationBadge.remove();
                        }
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
});
</script>
