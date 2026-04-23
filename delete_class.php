<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$class_id = $_GET['class_id'] ?? 0;


$stmt_fetch = $pdo->prepare("SELECT class_name FROM classes WHERE class_id=? AND tutor_id=?");
$stmt_fetch->execute([$class_id, $admin_id]);
$class = $stmt_fetch->fetch();

if ($class) {
  
    $stmt = $pdo->prepare("DELETE FROM classes WHERE class_id=? AND tutor_id=?");
    if ($stmt->execute([$class_id, $admin_id])) {
        
        log_activity($admin_id, "Deleted class: " . $class['class_name']);
    }
}

header("Location: admin_classes.php");
exit();
