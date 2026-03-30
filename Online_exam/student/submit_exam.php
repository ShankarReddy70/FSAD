<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../student_login.php");
    exit();
}
require_once '../php/db_connect.php';

$student_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['exam_id'])) {
    $exam_id = (int)$_POST['exam_id'];
    $answers = isset($_POST['ans']) ? $_POST['ans'] : [];

    // Prevent re-submission
    $check = executeQuery($conn, "SELECT result_id FROM results WHERE student_id = ? AND exam_id = ?", [$student_id, $exam_id], "ii")->get_result();
    if ($check->num_rows > 0) {
        $res_id = $check->fetch_assoc()['result_id'];
        header("Location: result.php?id=" . $res_id);
        exit();
    }

    // Calculate score
    $score = 0;
    $questions = fetchAll(executeQuery($conn, "SELECT question_id, correct_answer FROM questions WHERE exam_id = ?", [$exam_id], "i"));
    $total_questions = count($questions);

    // Save individual answers and calculate score
    foreach ($questions as $q) {
        $q_id = $q['question_id'];
        $correct = $q['correct_answer'];
        $selected = isset($answers[$q_id]) ? sanitizeInput($answers[$q_id]) : null;

        if ($selected === $correct) {
            $score++;
        }

        if ($selected !== null) {
            executeQuery($conn, "INSERT INTO answers (student_id, exam_id, question_id, selected_option) VALUES (?, ?, ?, ?)", 
                        [$student_id, $exam_id, $q_id, $selected], "iiis");
        }
    }

    // Save result
    executeQuery($conn, "INSERT INTO results (student_id, exam_id, score, total_questions) VALUES (?, ?, ?, ?)", 
                 [$student_id, $exam_id, $score, $total_questions], "iiii");
    
    $result_id = $conn->insert_id;
    header("Location: result.php?id=" . $result_id);
    exit();
} else {
    header("Location: dashboard.php");
    exit();
}
?>
