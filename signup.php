<?php
session_start();
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    try {
        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
            throw new Exception("All fields are required.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        if (strlen($password) < 6) {
            throw new Exception("Password must be at least 6 characters.");
        }

        if ($password !== $confirm_password) {
            throw new Exception("Passwords do not match.");
        }


        
        $res = register_user($pdo, $username, $email, $password);

        if ($res['success']) {
            $_SESSION['flash_success'] = "Registration successful! Please log in.";
            $_SESSION['open_modal'] = 'login';
            header('Location: index.php');
            exit;
        } else {
            throw new Exception($res['message']);
        }

    } catch (Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
        $_SESSION['open_modal'] = 'signup';
        header('Location: index.php');
        exit;
    }
}
?>