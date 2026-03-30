<?php
session_start();
require_once 'php/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: register.php");
        exit();
    }

    // Check if email exists
    $stmt = executeQuery($conn, "SELECT id FROM users WHERE email = ?", [$email], "s");
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email already registered!";
        header("Location: register.php");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 'student';

    $insertStmt = executeQuery($conn, "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)", 
                                [$name, $email, $hashed_password, $role], "ssss");

    if ($insertStmt->affected_rows > 0) {
        $_SESSION['success'] = "Registration successful! Please login.";
        header("Location: student_login.php");
    } else {
        $_SESSION['error'] = "Something went wrong. Please try again.";
        header("Location: register.php");
    }
}
?>
