<?php
session_start();

require_once 'config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Modified query to include is_active check
    $stmt = $conn->prepare("SELECT user_id, username, password, role_id, office_id, is_active FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // First check if account is active
        if (!$user['is_active']) {
            $error = "Your account is disabled. Please contact the administrator.";
        }
        // Then verify password if account is active
        elseif (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['office_id'] = $user['office_id']; // Store office_id in session
            $_SESSION['is_active'] = $user['is_active']; // Store active status in session

            // Redirect based on role_id (adjust as needed)
            if ($user['role_id'] == 1) {
                $_SESSION['success'] = "Login successfully!";
                header("Location: user/dashboard.php");
                exit();
            } else {
                $_SESSION['success'] = "Login successfully!";
                header("Location: admin/dashboard.php");
                exit();
            }
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Username not found.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

    
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 20px;
        }

        .page-title {
            font-size: 22px;
            color: #333;
        }
    </style>
</head>
<body class="bg-light">

<!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Feed Forward</h2>
        </div>
        </div>
    </div>
<div class="main-content" style="margin-top: 150px;">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header  text-white text-center" style="background: #1a3c5e;">
                    <h4>Login</h4>
                </div>
                <div class="card-body">

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" id="username" placeholder="username" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="password" required>
                        </div>

                        <button type="submit" class="btn w-100" style="background-color: #1a3c5e; color:white;">Login</button>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
</div>

</body>
</html>

