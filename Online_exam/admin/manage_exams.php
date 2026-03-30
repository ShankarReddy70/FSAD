<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../admin_login.php");
    exit();
}
require_once '../php/db_connect.php';

// Handle Exam Creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $name = sanitizeInput($_POST['exam_name']);
    $date = sanitizeInput($_POST['date']);
    $time = sanitizeInput($_POST['time']);
    $duration = (int)$_POST['duration'];

    executeQuery($conn, "INSERT INTO exams (exam_name, date, time, duration) VALUES (?, ?, ?, ?)", 
        [$name, $date, $time, $duration], "sssi");
    $_SESSION['success'] = "Exam created successfully.";
    header("Location: manage_exams.php");
    exit();
}

// Handle Exam Deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    executeQuery($conn, "DELETE FROM exams WHERE exam_id = ?", [$id], "i");
    $_SESSION['success'] = "Exam deleted successfully.";
    header("Location: manage_exams.php");
    exit();
}

// Fetch all exams
$exams = fetchAll(executeQuery($conn, "SELECT * FROM exams ORDER BY date DESC, time DESC"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Exams</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container d-flex justify-between align-center">
            <a href="dashboard.php" class="navbar-brand">Admin Panel</a>
            <ul class="navbar-nav d-flex">
                <li><a href="manage_exams.php" class="nav-link active">Exams</a></li>
                <li><a href="manage_questions.php" class="nav-link">Questions</a></li>
                <li><a href="students_results.php" class="nav-link">Results</a></li>
                <li><a href="../logout.php" class="btn btn-danger">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-2">
        <h2>Manage Exams</h2>
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="card mb-2">
            <h3>Create New Exam</h3>
            <form action="manage_exams.php" method="POST" class="mt-1 d-flex" style="gap: 10px; flex-wrap: wrap;">
                <input type="hidden" name="action" value="add">
                <input type="text" name="exam_name" class="form-control" placeholder="Exam Name" required style="flex: 1; min-width: 200px;">
                <input type="date" name="date" class="form-control" required style="width: auto;">
                <input type="time" name="time" class="form-control" required style="width: auto;">
                <input type="number" name="duration" class="form-control" placeholder="Duration (mins)" required min="1" style="width: 150px;">
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Exam Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Duration</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($exams as $exam): ?>
                    <tr>
                        <td><?php echo $exam['exam_id']; ?></td>
                        <td><?php echo htmlspecialchars($exam['exam_name']); ?></td>
                        <td><?php echo date('d M Y', strtotime($exam['date'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($exam['time'])); ?></td>
                        <td><?php echo $exam['duration']; ?> mins</td>
                        <td>
                            <a href="manage_exams.php?delete=<?php echo $exam['exam_id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this exam?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($exams)): ?>
                    <tr><td colspan="6" class="text-center">No exams found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
