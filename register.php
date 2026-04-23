<?php
// register.php
session_start();
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($password !== $confirm) {
    $_SESSION['flash_error'] = 'Passwords do not match';
    header('Location: index.php?show=signup');
    exit;
}

$res = register_user($username, $email, $password);

if ($res['success']) {
    $_SESSION['flash_success'] = 'Account created successfully! Please log in.';
    header('Location: index.php?show=login');
    exit;
} else {
    $_SESSION['flash_error'] = $res['message'];
    header('Location: index.php?show=signup');
    exit;
}
