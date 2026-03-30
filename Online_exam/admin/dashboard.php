<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../admin_login.php");
    exit();
}
require_once '../php/db_connect.php';

// Fetch stats
$studentsCount = executeQuery($conn, "SELECT COUNT(*) as count FROM users WHERE role='student'")->get_result()->fetch_assoc()['count'];
$examsCount = executeQuery($conn, "SELECT COUNT(*) as count FROM exams")->get_result()->fetch_assoc()['count'];
$resultsCount = executeQuery($conn, "SELECT COUNT(*) as count FROM results")->get_result()->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container d-flex justify-between align-center">
            <a href="dashboard.php" class="navbar-brand">Admin Panel</a>
            <ul class="navbar-nav d-flex">
                <li><a href="manage_exams.php" class="nav-link">Exams</a></li>
                <li><a href="manage_questions.php" class="nav-link">Questions</a></li>
                <li><a href="students_results.php" class="nav-link">Results</a></li>
                <li><a href="../logout.php" class="btn btn-danger">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-2">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
        <div class="dashboard-grid">
            <div class="card text-center">
                <h3>Total Students</h3>
                <h2><?php echo $studentsCount; ?></h2>
            </div>
            <div class="card text-center">
                <h3>Total Exams</h3>
                <h2><?php echo $examsCount; ?></h2>
            </div>
            <div class="card text-center">
                <h3>Exams Attempted</h3>
                <h2><?php echo $resultsCount; ?></h2>
            </div>
        </div>
    </div>
</body>
</html>
