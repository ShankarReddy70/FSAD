<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../admin_login.php");
    exit();
}
require_once '../php/db_connect.php';

// Fetch all exams for the dropdown
$exams = fetchAll(executeQuery($conn, "SELECT exam_id, exam_name FROM exams ORDER BY date DESC"));

// Handle Question Creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $exam_id = (int)$_POST['exam_id'];
    $question_text = sanitizeInput($_POST['question_text']);
    $opt1 = sanitizeInput($_POST['option1']);
    $opt2 = sanitizeInput($_POST['option2']);
    $opt3 = sanitizeInput($_POST['option3']);
    $opt4 = sanitizeInput($_POST['option4']);
    $correct = sanitizeInput($_POST['correct_answer']);

    executeQuery($conn, "INSERT INTO questions (exam_id, question_text, option1, option2, option3, option4, correct_answer) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)", 
        [$exam_id, $question_text, $opt1, $opt2, $opt3, $opt4, $correct], "issssss");
    $_SESSION['success'] = "Question added successfully.";
    header("Location: manage_questions.php" . ($exam_id ? "?exam_id=$exam_id" : ""));
    exit();
}

// Handle Question Deletion
if (isset($_GET['delete'])) {
    $q_id = (int)$_GET['delete'];
    executeQuery($conn, "DELETE FROM questions WHERE question_id = ?", [$q_id], "i");
    $_SESSION['success'] = "Question deleted successfully.";
    header("Location: manage_questions.php");
    exit();
}

// Fetch questions (optionally filtered by exam)
$selected_exam = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : 0;
$query = "SELECT q.*, e.exam_name FROM questions q JOIN exams e ON q.exam_id = e.exam_id";
$params = [];
$types = "";

if ($selected_exam > 0) {
    $query .= " WHERE q.exam_id = ?";
    $params[] = $selected_exam;
    $types = "i";
}
$query .= " ORDER BY q.question_id DESC";

$questions = fetchAll(executeQuery($conn, $query, $params, $types));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Questions</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .options-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container d-flex justify-between align-center">
            <a href="dashboard.php" class="navbar-brand">Admin Panel</a>
            <ul class="navbar-nav d-flex">
                <li><a href="manage_exams.php" class="nav-link">Exams</a></li>
                <li><a href="manage_questions.php" class="nav-link active">Questions</a></li>
                <li><a href="students_results.php" class="nav-link">Results</a></li>
                <li><a href="../logout.php" class="btn btn-danger">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-2">
        <h2>Manage Questions</h2>
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="card mb-2">
            <h3>Add New Question</h3>
            <form action="manage_questions.php" method="POST" class="mt-1">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <select name="exam_id" class="form-control" required>
                        <option value="">-- Select Exam --</option>
                        <?php foreach($exams as $ex): ?>
                            <option value="<?php echo $ex['exam_id']; ?>" <?php echo $selected_exam == $ex['exam_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ex['exam_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <textarea name="question_text" class="form-control" rows="3" placeholder="Enter question text here..." required></textarea>
                </div>
                <div class="options-grid mb-1">
                    <input type="text" name="option1" class="form-control" placeholder="Option 1" required>
                    <input type="text" name="option2" class="form-control" placeholder="Option 2" required>
                    <input type="text" name="option3" class="form-control" placeholder="Option 3" required>
                    <input type="text" name="option4" class="form-control" placeholder="Option 4" required>
                </div>
                <div class="form-group">
                    <select name="correct_answer" class="form-control" required>
                        <option value="">-- Select Correct Answer --</option>
                        <option value="1">Option 1</option>
                        <option value="2">Option 2</option>
                        <option value="3">Option 3</option>
                        <option value="4">Option 4</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Add Question</button>
            </form>
        </div>

        <div class="card mb-2">
            <form action="manage_questions.php" method="GET" class="d-flex" style="gap:10px;">
                <select name="exam_id" class="form-control" style="max-width:300px;">
                    <option value="0">All Exams</option>
                    <?php foreach($exams as $ex): ?>
                        <option value="<?php echo $ex['exam_id']; ?>" <?php echo $selected_exam == $ex['exam_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($ex['exam_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Exam</th>
                        <th>Question</th>
                        <th>Correct Option</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($questions as $q): ?>
                    <tr>
                        <td><?php echo $q['question_id']; ?></td>
                        <td><?php echo htmlspecialchars($q['exam_name']); ?></td>
                        <td><?php echo htmlspecialchars(substr($q['question_text'], 0, 50)) . '...'; ?></td>
                        <td>Option <?php echo htmlspecialchars($q['correct_answer']); ?></td>
                        <td>
                            <a href="manage_questions.php?delete=<?php echo $q['question_id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this question?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($questions)): ?>
                    <tr><td colspan="5" class="text-center">No questions found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
