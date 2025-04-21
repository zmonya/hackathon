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

// Fetch offices
$offices_query = "
    SELECT 
        office_id,
        office_name,
        is_active
    FROM 
        offices
    ORDER BY 
        office_name
";

$offices = $conn->query($offices_query);

// Check for query errors
if (!$offices) {
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
            <h3 class="chart-title mb-0">Offices</h3>
            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addOfficeModal">
                <i class="fas fa-plus me-2"></i>Add office
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th class="text-center">Office Name</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($office = $offices->fetch_assoc()): ?>
                        <tr>
                            <td class="text-center"><?php echo htmlspecialchars($office['office_name']); ?></td>
                            <td class="text-center">
                                <span class="badge status-badge bg-<?= $office['is_active'] ? 'success' : 'danger' ?>">
                                    <?= $office['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary edit-office" 
                                        data-id="<?= $office['office_id'] ?>"
                                        data-name="<?= htmlspecialchars($office['office_name']) ?>"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editOfficeModal">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <!-- In the table row where the deactivate/activate button appears -->
<a href="process_office.php?action=toggle_status&id=<?= $office['office_id'] ?>" 
   class="btn btn-sm btn-<?= $office['is_active'] ? 'danger' : 'success' ?> toggle-status"
   data-name="<?= htmlspecialchars($office['office_name']) ?>"
   data-status="<?= $office['is_active'] ? 'active' : 'inactive' ?>">
    <i class="fas <?= $office['is_active'] ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
    <?= $office['is_active'] ? 'Deactivate' : 'Activate' ?>
</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Office Modal -->
<div class="modal fade" id="addOfficeModal" tabindex="-1" aria-labelledby="addOfficeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addOfficeModalLabel">Create Office</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="process_office.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label for="office_name" class="form-label">Office Name</label>
                        <input type="text" class="form-control" id="office_name" name="office_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Office Modal -->
<div class="modal fade" id="editOfficeModal" tabindex="-1" aria-labelledby="editOfficeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editOfficeModalLabel">Edit Office</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="process_office.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="office_id" id="edit_office_id">
                    <div class="mb-3">
                        <label for="edit_office_name" class="form-label">Office Name</label>
                        <input type="text" class="form-control" id="edit_office_name" name="office_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
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
// Script to handle edit modal data population
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-office');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const officeId = this.getAttribute('data-id');
            const officeName = this.getAttribute('data-name');
            
            document.getElementById('edit_office_id').value = officeId;
            document.getElementById('edit_office_name').value = officeName;
        });
    });
});

// Add confirmation for status toggle (native version)
document.addEventListener('DOMContentLoaded', function() {
    const toggleButtons = document.querySelectorAll('.toggle-status');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const officeName = this.getAttribute('data-name');
            const currentStatus = this.getAttribute('data-status');
            const actionText = currentStatus === 'active' ? 'deactivate' : 'activate';
            
            if (confirm(`Are you sure you want to ${actionText} this office?`)) {
                window.location.href = this.getAttribute('href');
            }
        });
    });
});
</script>