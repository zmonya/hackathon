<!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Feed Forward</h2>
            <h6 class="text-center">Admin</h6>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="menu-item active">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="reports.php" class="menu-item">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
            <a href="feedback.php" class="menu-item">
                <i class="fas fa-comment-alt"></i>
                <span>Feedbacks</span>
            </a>
            <a href="office_management.php" class="menu-item">
                <i class="fas fa-building"></i>
                <span>Office Management</span>
            </a>
            <a href="user_management.php" class="menu-item">
                <i class="fas fa-users-cog"></i>
                <span>User Management</span>
            </a>
            <a href="../logout.php" class="menu-item" onclick="return confirmLogout();">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <script>
            // Highlight active menu item based on current page
            document.addEventListener('DOMContentLoaded', () => {
                const currentPage = window.location.pathname.split('/').pop();
                const menuItems = document.querySelectorAll('.menu-item');
                
                menuItems.forEach(item => {
                    const href = item.getAttribute('href');
                    if (href && currentPage.includes(href.split('/').pop())) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                });
            });
        
            function confirmLogout() {
                return confirm("Are you sure you want to log out?");
            }   
    </script>