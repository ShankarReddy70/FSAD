<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Examination System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="navbar-brand">Examin System</a>
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="<?php echo $_SESSION['role'] == 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php'; ?>" class="nav-link">Dashboard</a></li>
                    <li><a href="logout.php" class="nav-link">Logout</a></li>
                <?php else: ?>
                    <li><a href="student_login.php" class="nav-link">Student Login</a></li>
                    <li><a href="admin_login.php" class="nav-link">Admin Login</a></li>
                    <li><a href="register.php" class="btn btn-primary">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container mt-2 text-center">
        <h1>Welcome to the Online Examination System</h1>
        <p class="mb-2">A comprehensive platform for taking and managing exams efficiently.</p>
        
        <div class="dashboard-grid">
            <div class="card">
                <h3 class="card-title">For Students</h3>
                <p>Register, login, attempt exams, and view your results instantly.</p>
                <a href="student_login.php" class="btn btn-primary mt-1">Student Portal</a>
            </div>
            <div class="card">
                <h3 class="card-title">For Administrators</h3>
                <p>Manage exams, add questions, and track student performance.</p>
                <a href="admin_login.php" class="btn btn-primary mt-1">Admin Portal</a>
            </div>
        </div>
    </div>
</body>
</html>
