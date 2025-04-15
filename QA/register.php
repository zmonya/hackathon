<?php
require_once 'config.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = trim($_POST['fname']);
    $mname = trim($_POST['mname']);
    $lname = trim($_POST['lname']);
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role_id = $_POST['role_id'];
    $office_id = isset($_POST['office_id']) && $_POST['office_id'] !== '' ? $_POST['office_id'] : null;

    // Check if the username already exists
    $check = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Username already exists!";
    } else {
        // Validate office_id only if it's not null
        if ($office_id !== null) {
            $office_check = $conn->prepare("SELECT office_id FROM offices WHERE office_id = ?");
            $office_check->bind_param("i", $office_id);
            $office_check->execute();
            $office_check->store_result();

            if ($office_check->num_rows == 0) {
                $error = "Invalid office ID!";
            }
            $office_check->close();
        }

        // If no errors, proceed to insert the user
        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO users (fname, mname, lname, username, password, role_id, office_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssi", $fname, $mname, $lname, $username, $password, $role_id, $office_id);

            if ($stmt->execute()) {
                $success = "Registration successful! <a href='login.php'>Click here to login</a>.";
            } else {
                $error = "Something went wrong. Please try again.";
            }

            $stmt->close();
        }
    }

    $check->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function toggleOffice() {
            const roleSelect = document.getElementById("role_id");
            const selectedOption = roleSelect.options[roleSelect.selectedIndex];
            const roleName = selectedOption.getAttribute("data-role-name");
            const officeGroup = document.getElementById("office-group");

            if (roleName && roleName.toLowerCase() === "admin") {
                officeGroup.style.display = "none";
            } else {
                officeGroup.style.display = "block";
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("role_id").addEventListener("change", toggleOffice);
            toggleOffice(); // initial load
        });
    </script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">User Registration</h4>
                </div>
                <div class="card-body">

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="fname" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="mname" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="lname" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role</label>
                                <select name="role_id" id="role_id" class="form-select" required>
                                    <option value="">Select Role</option>
                                    <?php
                                    $roles = $conn->query("SELECT role_id, role_name FROM roles");
                                    while ($row = $roles->fetch_assoc()) {
                                        echo "<option value='{$row['role_id']}' data-role-name='{$row['role_name']}'>{$row['role_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3" id="office-group">
                                <label class="form-label">Office</label>
                                <select name="office_id" class="form-select">
                                    <option value="">Select Office</option>
                                    <?php
                                    $offices = $conn->query("SELECT office_id, office_name FROM offices");
                                    while ($row = $offices->fetch_assoc()) {
                                        echo "<option value='{$row['office_id']}'>{$row['office_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                </div>
                <div class="card-footer text-center bg-light">
                    Already registered? <a href="login.php">Login here</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
