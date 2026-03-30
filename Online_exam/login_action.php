<?php
session_start();
require_once 'php/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $login_type = sanitizeInput($_POST['login_type']); // 'student' or 'admin'

    $stmt = executeQuery($conn, "SELECT id, name, password, role FROM users WHERE email = ? AND role = ?", [$email, $login_type], "ss");
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: student/dashboard.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Invalid password!";
        }
    } else {
        $_SESSION['error'] = "User not found or role mismatch!";
    }

    // Redirect back
    if ($login_type == 'admin') {
        header("Location: admin_login.php");
    } else {
        header("Location: student_login.php");
    }
}
?>
