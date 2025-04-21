<?php
session_start();
include 'header.php';
include 'sidebar.php';
require_once '../config.php';

// Check if user is admin or has permission
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../login.php");
    exit();
}

// Fetch users from database
$users_query = "
    SELECT 
        u.user_id,
        u.fname,
        u.mname,
        u.lname,
        u.username,
        u.role_id,
        u.is_active,
        r.role_name,
        u.office_id,
        o.office_name
    FROM 
        users u
    LEFT JOIN 
        roles r ON u.role_id = r.role_id
    LEFT JOIN 
        offices o ON u.office_id = o.office_id
    ORDER BY 
        u.lname, u.fname
";
$users = $conn->query($users_query);

// Check for query errors
if (!$users) {
    die("Database error: " . $conn->error);
}
?>
<div class="main-content">
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
    
    <div class="chart-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="chart-title mb-0">Users</h3>
            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-plus me-2"></i>Add User
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        
                        <th class="text-center">First Name</th>
                        <th class="text-center">Middle Name</th>
                        <th class="text-center">Last Name</th>
                        <th class="text-center">Username</th>
                        <th class="text-center">Role</th>
                        <th class="text-center">Office</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            
                            <td class="text-center"><?php echo htmlspecialchars($user['fname']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($user['mname'] ?? ''); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($user['lname']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($user['role_name']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($user['office_name'] ?? 'N/A'); ?></td>
                           
                            <td class="text-center">
                                <span class="badge status-badge bg-<?= $user['is_active'] ? 'success' : 'danger' ?>">
                                    <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            
                            <td class="text-center">
                                
                                    <button class="btn btn-sm btn-primary edit-user-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editUserModal"
                                        data-user-id="<?= $user['user_id'] ?>"
                                        data-fname="<?= htmlspecialchars($user['fname']) ?>"
                                        data-mname="<?= htmlspecialchars($user['mname'] ?? '') ?>"
                                        data-lname="<?= htmlspecialchars($user['lname']) ?>"
                                        data-username="<?= htmlspecialchars($user['username']) ?>"
                                        data-role-id="<?= $user['role_id'] ?>"
                                        data-office-id="<?= $user['office_id'] ?? '' ?>"
                                        data-is-active="<?= $user['is_active'] ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    
                                    <a href="process_user.php?action=toggle_status&id=<?= $user['user_id'] ?>" 
                                       class="btn btn-sm btn-<?= $user['is_active'] ? 'danger' : 'success' ?>">
                                        <i class="fas <?= $user['is_active'] ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
                                        <?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>
                                    </a>
                            
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addUserModalLabel">Create User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="process_user.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_user">
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="fname" class="form-label">First Name *</label>
                            <input type="text" class="form-control" id="fname" name="fname" required>
                        </div>
                        <div class="col-md-4">
                            <label for="mname" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="mname" name="mname">
                        </div>
                        <div class="col-md-4">
                            <label for="lname" class="form-label">Last Name *</label>
                            <input type="text" class="form-control" id="lname" name="lname" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username *</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="8">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="role_id" class="form-label">Role *</label>
                            <select class="form-select" id="role_id" name="role_id" required>
                                <option value="">Select Role</option>
                                <?php
                                $roles = $conn->query("SELECT role_id, role_name FROM roles");
                                while ($row = $roles->fetch_assoc()) {
                                    echo '<option value="'.$row['role_id'].'" data-role-name="'.$row['role_name'].'">'.$row['role_name'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6" id="officeField">
                            <label for="office_id" class="form-label">Office</label>
                            <select class="form-select" id="office_id" name="office_id" required>
                                <option value="">Select Office</option>
                                <?php
                                $offices = $conn->query("SELECT office_id, office_name FROM offices");
                                while ($row = $offices->fetch_assoc()) {
                                    echo '<option value="'.$row['office_id'].'">'.$row['office_name'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="process_user.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit_user">
                    <input type="hidden" id="edit_user_id" name="user_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="edit_fname" class="form-label">First Name *</label>
                            <input type="text" class="form-control" id="edit_fname" name="fname" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_mname" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="edit_mname" name="mname">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_lname" class="form-label">Last Name *</label>
                            <input type="text" class="form-control" id="edit_lname" name="lname" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_username" class="form-label">Username *</label>
                            <input type="text" class="form-control" id="edit_username" name="username" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_password" class="form-label">Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" id="edit_password" name="password" minlength="8">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_role_id" class="form-label">Role *</label>
                            <select class="form-select" id="edit_role_id" name="role_id" required>
                                <option value="">Select Role</option>
                                <?php
                                $roles = $conn->query("SELECT role_id, role_name FROM roles");
                                while ($row = $roles->fetch_assoc()) {
                                    echo '<option value="'.$row['role_id'].'" data-role-name="'.strtolower($row['role_name']).'">'.$row['role_name'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6" id="editOfficeField">
                            <label for="edit_office_id" class="form-label">Office</label>
                            <select class="form-select" id="edit_office_id" name="office_id" required>
                                <option value="">Select Office</option>
                                <?php
                                $offices = $conn->query("SELECT office_id, office_name FROM offices");
                                while ($row = $offices->fetch_assoc()) {
                                    echo '<option value="'.$row['office_id'].'">'.$row['office_name'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.table').DataTable({
        "paging": true, // Enable pagination
        "searching": true, // Enable search
        "ordering": true, // Enable sorting
        "info": true // Show info
    });
});

// Initialize event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.edit-user-btn').forEach(button => {
        button.addEventListener('click', function() {
            // Get all data attributes
            const userId = this.getAttribute('data-user-id');
            const fname = this.getAttribute('data-fname');
            const mname = this.getAttribute('data-mname');
            const lname = this.getAttribute('data-lname');
            const username = this.getAttribute('data-username');
            const roleId = this.getAttribute('data-role-id');
            const officeId = this.getAttribute('data-office-id');

            // Populate form fields
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('edit_fname').value = fname;
            document.getElementById('edit_mname').value = mname;
            document.getElementById('edit_lname').value = lname;
            document.getElementById('edit_username').value = username;
            
            // Set selected role
            const roleSelect = document.getElementById('edit_role_id');
            for (let i = 0; i < roleSelect.options.length; i++) {
                if (roleSelect.options[i].value == roleId) {
                    roleSelect.selectedIndex = i;
                    break;
                }
            }
            
            // Set selected office if exists
            if (officeId) {
                const officeSelect = document.getElementById('edit_office_id');
                for (let i = 0; i < officeSelect.options.length; i++) {
                    if (officeSelect.options[i].value == officeId) {
                        officeSelect.selectedIndex = i;
                        break;
                    }
                }
            }
            
            // Trigger office field visibility update based on initial role
            toggleEditOfficeField();
        });
    });
    
function showAlert(message, type = 'success') {
    // Remove any existing alerts
    const existingAlerts = document.querySelectorAll('.alert-dismissible');
    existingAlerts.forEach(alert => alert.remove());

    // Create alert HTML
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    // Insert alert at the top of main-content
    const mainContent = document.querySelector('.main-content');
    if (mainContent) {
        mainContent.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-remove alert after 5 seconds
        setTimeout(() => {
            const newAlert = document.querySelector('.alert-dismissible');
            if (newAlert) {
                newAlert.remove();
            }
        }, 5000);
    }
}

// Toggle status handler
document.querySelectorAll('[href*="toggle_status"]').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const url = this.getAttribute('href');
        const actionText = this.textContent.trim();
        
        if (confirm(`Are you sure you want to ${actionText} this account?`)) {
            fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update the UI
                    const row = this.closest('tr');
                    
                    // Update status badge
                    const badge = row.querySelector('.status-badge');
                    badge.className = `badge status-badge bg-${data.new_status ? 'success' : 'danger'}`;
                    badge.textContent = data.status_text;
                    
                    // Update button
                    this.className = `btn btn-sm btn-${data.new_status ? 'danger' : 'success'}`;
                    this.innerHTML = `<i class="fas ${data.new_status ? 'fa-user-slash' : 'fa-user-check'}"></i> ${data.btn_text}`;
                    
                    // Update href
                    const newUrl = url.replace(
                        /action=toggle_status/,
                        `action=toggle_status`
                    );
                    this.setAttribute('href', newUrl);
                    
                    // Show success message
                    showAlert(data.message, 'success');
                } else {
                    showAlert(data.message || 'Error updating status', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred while updating the status', 'danger');
            });
        }
    });
});
});
</script>