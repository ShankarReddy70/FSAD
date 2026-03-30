<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../admin_login.php");
    exit();
}
require_once '../php/db_connect.php';

// Fetch all results
$query = "
    SELECT r.result_id, r.score, r.total_questions, r.attempted_at, 
           u.name as student_name, u.email,
           e.exam_name 
    FROM results r
    JOIN users u ON r.student_id = u.id
    JOIN exams e ON r.exam_id = e.exam_id
    ORDER BY r.attempted_at DESC
";
$results = fetchAll(executeQuery($conn, $query));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Results</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container d-flex justify-between align-center">
            <a href="dashboard.php" class="navbar-brand">Admin Panel</a>
            <ul class="navbar-nav d-flex">
                <li><a href="manage_exams.php" class="nav-link">Exams</a></li>
                <li><a href="manage_questions.php" class="nav-link">Questions</a></li>
                <li><a href="students_results.php" class="nav-link active">Results</a></li>
                <li><a href="../logout.php" class="btn btn-danger">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-2">
        <h2>Student Performance Reports</h2>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Exam</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Date Taken</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($results as $res): 
                        $percentage = $res['total_questions'] > 0 ? round(($res['score'] / $res['total_questions']) * 100, 2) : 0;
                        $pass_class = $percentage >= 50 ? 'text-success' : 'text-danger';
                    ?>
                    <tr>
                        <td><?php echo $res['result_id']; ?></td>
                        <td><?php echo htmlspecialchars($res['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($res['email']); ?></td>
                        <td><?php echo htmlspecialchars($res['exam_name']); ?></td>
                        <td><?php echo $res['score'] . ' / ' . $res['total_questions']; ?></td>
                        <td style="font-weight:bold; color: <?php echo $percentage >= 50 ? 'green' : 'red'; ?>;">
                            <?php echo $percentage; ?>%
                        </td>
                        <td><?php echo date('d M Y h:i A', strtotime($res['attempted_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($results)): ?>
                    <tr><td colspan="7" class="text-center">No results found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
