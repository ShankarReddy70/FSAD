<?php 
session_start(); 
if(isset($_SESSION['user_id'])) header("Location: index.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="navbar-brand">Examin System</a>
            <ul class="navbar-nav">
                <li><a href="student_login.php" class="nav-link">Student Login</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h2 class="text-center">Admin Login</h2>
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            
            <form action="login_action.php" method="POST" class="validate-form">
                <input type="hidden" name="login_type" value="admin">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <!-- Pre-filled for easier testing as requested in verification plan -->
                    <input type="email" name="email" class="form-control" required value="admin@example.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required value="admin123">
                </div>
                <button type="submit" class="btn btn-danger" style="width: 100%">Login as Admin</button>
            </form>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
