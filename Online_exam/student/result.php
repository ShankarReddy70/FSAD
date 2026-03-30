<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../student_login.php");
    exit();
}
require_once '../php/db_connect.php';

$student_id = $_SESSION['user_id'];
$result_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($result_id <= 0) {
    header("Location: dashboard.php");
    exit();
}

$query = "SELECT r.*, e.exam_name 
          FROM results r 
          JOIN exams e ON r.exam_id = e.exam_id 
          WHERE r.result_id = ? AND r.student_id = ?";
$resultObj = executeQuery($conn, $query, [$result_id, $student_id], "ii")->get_result();

if ($resultObj->num_rows === 0) {
    header("Location: dashboard.php");
    exit();
}

$result = $resultObj->fetch_assoc();
$percentage = $result['total_questions'] > 0 ? round(($result['score'] / $result['total_questions']) * 100, 2) : 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exam Result</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .result-card {
            max-width: 600px;
            margin: 2rem auto;
            text-align: center;
            padding: 3rem;
        }
        .score-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: #f8f9fa;
            border: 8px solid <?php echo $percentage >= 50 ? 'var(--success)' : 'var(--danger)'; ?>;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 2.5rem;
            font-weight: bold;
            color: <?php echo $percentage >= 50 ? 'var(--success)' : 'var(--danger)'; ?>;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container d-flex justify-between align-center">
            <a href="dashboard.php" class="navbar-brand">Student Portal</a>
            <ul class="navbar-nav d-flex">
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="../logout.php" class="btn btn-danger">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-2">
        <div class="card result-card">
            <h2>Result for: <?php echo htmlspecialchars($result['exam_name']); ?></h2>
            <br>
            <div class="score-circle">
                <?php echo $percentage; ?>%
            </div>
            
            <h3>Score: <?php echo $result['score']; ?> out of <?php echo $result['total_questions']; ?></h3>
            <p class="mt-1" style="color: var(--text-light)">Date Attempted: <?php echo date('d M Y h:i A', strtotime($result['attempted_at'])); ?></p>
            
            <div class="mt-2 text-center">
                <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
