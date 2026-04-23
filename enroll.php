<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$_SESSION['enroll_success'] = '';
$_SESSION['enroll_error'] = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    $class_id = intval($_POST['class_id'] ?? 0);
    $payment_method = $_POST['payment_method'] ?? '';
    $errors = [];

    if (!$payment_method || !in_array($payment_method, ['gcash', 'maya'])) {
        $errors[] = "Please select a valid payment method.";
    }

    if ($payment_method === 'gcash') {
        $gcash_number = trim($_POST['gcash_number'] ?? '');
        $gcash_name = trim($_POST['gcash_name'] ?? '');

        if (!$gcash_number || !preg_match('/^\d{10,}$/', $gcash_number)) $errors[] = "GCash number must be at least 10 digits.";
        if (!$gcash_name || strlen($gcash_name) < 3) $errors[] = "GCash account name too short.";
    }

    if ($payment_method === 'maya') {
        $maya_number = trim($_POST['maya_number'] ?? '');
        $maya_name = trim($_POST['maya_name'] ?? '');

        if (!$maya_number || !preg_match('/^\d{10,}$/', $maya_number)) $errors[] = "Maya number must be at least 10 digits.";
        if (!$maya_name || strlen($maya_name) < 3) $errors[] = "Maya account name too short.";
    }

    $stmt = $pdo->prepare("SELECT * FROM classes WHERE class_id = ? LIMIT 1");
    $stmt->execute([$class_id]);
    $class = $stmt->fetch();

    if (!$class) {
        $errors[] = "Class not found.";
    } else {
        $stmt_check = $pdo->prepare("SELECT * FROM user_classes WHERE user_id=? AND class_id=?");
        $stmt_check->execute([$user_id, $class_id]);
        if ($stmt_check->fetch()) $errors[] = "You are already enrolled in this class.";
    }

    if ($errors) {
        $_SESSION['enroll_error'] = implode("<br>", $errors);
    } else {
        // Insert enrollment
        $stmt = $pdo->prepare("INSERT INTO user_classes (user_id, class_id, enrolled_at) VALUES (?, ?, NOW())");
        if ($stmt->execute([$user_id, $class_id])) {
            // Log enrollment
            $stmt_log = $pdo->prepare("INSERT INTO activity_logs (user_id, action, created_at) VALUES (?, ?, NOW())");
            $stmt_log->execute([$user_id, "Enrolled in class: {$class['class_name']} via $payment_method"]);

            $_SESSION['enroll_success'] = "🎉 Successfully enrolled in {$class['class_name']}!";
        } else {
            $_SESSION['enroll_error'] = "❌ Something went wrong. Please try again.";
        }
    }
}

header("Location: available_classes.php");
exit();
