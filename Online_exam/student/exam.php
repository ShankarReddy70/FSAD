<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../student_login.php");
    exit();
}
require_once '../php/db_connect.php';

$student_id = $_SESSION['user_id'];
$exam_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($exam_id <= 0) {
    header("Location: dashboard.php");
    exit();
}

// Check if already attempted
$check = executeQuery($conn, "SELECT result_id FROM results WHERE student_id = ? AND exam_id = ?", [$student_id, $exam_id], "ii")->get_result();
if ($check->num_rows > 0) {
    $res_id = $check->fetch_assoc()['result_id'];
    header("Location: result.php?id=" . $res_id);
    exit();
}

// Get Exam Details
$exam_result = executeQuery($conn, "SELECT * FROM exams WHERE exam_id = ?", [$exam_id], "i")->get_result();
if ($exam_result->num_rows === 0) {
    header("Location: dashboard.php");
    exit();
}
$exam = $exam_result->fetch_assoc();

// Get questions
$questions = fetchAll(executeQuery($conn, "SELECT * FROM questions WHERE exam_id = ?", [$exam_id], "i"));
$durationInSeconds = $exam['duration'] * 60;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attempt Exam - <?php echo htmlspecialchars($exam['exam_name']); ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .timer-box {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #fff;
            padding: 15px 20px;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--danger);
            z-index: 1000;
        }
        .question-card { margin-bottom: 2rem; }
        .options-list { list-style: none; padding-left: 0; }
        .options-list li { margin-bottom: 0.8rem; }
        .options-list label { cursor: pointer; display: flex; align-items: center; gap: 10px; }
    </style>
</head>
<body style="background-color: #f1f3f5;">
    <!-- Timer -->
    <div class="timer-box">
        Time Left: <span id="time_left" data-duration="<?php echo $durationInSeconds; ?>"><?php echo sprintf("%02d:%02d:00", floor($exam['duration']/60), $exam['duration']%60); ?></span>
    </div>

    <div class="container mt-2">
        <h2><?php echo htmlspecialchars($exam['exam_name']); ?></h2>
        <p class="mb-2">Please answer all questions before submitting.</p>

        <?php if(empty($questions)): ?>
            <div class="alert alert-danger">No questions found for this exam.</div>
            <a href="dashboard.php" class="btn btn-primary">Return to Dashboard</a>
        <?php else: ?>
            <form id="examForm" action="submit_exam.php" method="POST">
                <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
                
                <?php $qNum = 1; foreach($questions as $q): ?>
                <div class="card question-card">
                    <h3 class="mb-1"><?php echo $qNum . '. ' . nl2br(htmlspecialchars($q['question_text'])); ?></h3>
                    <ul class="options-list">
                        <li>
                            <label>
                                <input type="radio" name="ans[<?php echo $q['question_id']; ?>]" value="1" required> 
                                <?php echo htmlspecialchars($q['option1']); ?>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input type="radio" name="ans[<?php echo $q['question_id']; ?>]" value="2"> 
                                <?php echo htmlspecialchars($q['option2']); ?>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input type="radio" name="ans[<?php echo $q['question_id']; ?>]" value="3"> 
                                <?php echo htmlspecialchars($q['option3']); ?>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input type="radio" name="ans[<?php echo $q['question_id']; ?>]" value="4"> 
                                <?php echo htmlspecialchars($q['option4']); ?>
                            </label>
                        </li>
                    </ul>
                </div>
                <?php $qNum++; endforeach; ?>

                <div class="text-center mb-2">
                    <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1.2rem;">Submit Exam</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
    <script src="../js/exam.js"></script>
</body>
</html>
