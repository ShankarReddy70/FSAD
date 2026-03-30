<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../student_login.php");
    exit();
}
require_once '../php/db_connect.php';
$student_id = $_SESSION['user_id'];

// Get all future/current exams that the student hasn't taken yet
$query = "SELECT e.* FROM exams e 
          WHERE e.exam_id NOT IN (SELECT exam_id FROM results WHERE student_id = ?)
          ORDER BY e.date ASC, e.time ASC";
$available_exams = fetchAll(executeQuery($conn, $query, [$student_id], "i"));

// Get results for exams already taken
$history_query = "SELECT r.*, e.exam_name, e.date 
                  FROM results r 
                  JOIN exams e ON r.exam_id = e.exam_id 
                  WHERE r.student_id = ? 
                  ORDER BY r.attempted_at DESC";
$results = fetchAll(executeQuery($conn, $history_query, [$student_id], "i"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container d-flex justify-between align-center">
            <a href="dashboard.php" class="navbar-brand">Student Portal</a>
            <ul class="navbar-nav d-flex">
                <li><a href="dashboard.php" class="nav-link active">Dashboard</a></li>
                <li><a href="../logout.php" class="btn btn-danger">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-2">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>

        <div class="card mt-2">
            <h3 class="mb-1">Available Exams</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Exam Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Duration</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($available_exams as $ex): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ex['exam_name']); ?></td>
                            <td><?php echo date('d M Y', strtotime($ex['date'])); ?></td>
                            <td><?php echo date('h:i A', strtotime($ex['time'])); ?></td>
                            <td><?php echo $ex['duration']; ?> mins</td>
                            <td>
                                <a href="exam.php?id=<?php echo $ex['exam_id']; ?>" class="btn btn-primary" onclick="return confirm('Start the exam now? The timer will begin immediately.');">Start Exam</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($available_exams)): ?>
                        <tr><td colspan="5" class="text-center">No available exams at the moment.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mt-2">
            <h3 class="mb-1">Your Exam History</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Exam Name</th>
                            <th>Score</th>
                            <th>Percentage</th>
                            <th>Date Attempted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($results as $res): 
                            $percentage = $res['total_questions'] > 0 ? round(($res['score'] / $res['total_questions']) * 100, 2) : 0;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($res['exam_name']); ?></td>
                            <td><?php echo $res['score'] . ' / ' . $res['total_questions']; ?></td>
                            <td style="font-weight:bold; color: <?php echo $percentage >= 50 ? 'green' : 'red'; ?>;">
                                <?php echo $percentage; ?>%
                            </td>
                            <td><?php echo date('d M Y h:i A', strtotime($res['attempted_at'])); ?></td>
                            <td>
                                <a href="result.php?id=<?php echo $res['result_id']; ?>" class="btn btn-primary" style="padding: 0.3rem 1rem;">View Details</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($results)): ?>
                        <tr><td colspan="5" class="text-center">You haven't attempted any exams yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
