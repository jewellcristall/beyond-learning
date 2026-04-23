<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if(isset($_GET['action']) && isset($_GET['id'])){
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if($action === 'approve'){
        $stmt = $pdo->prepare("SELECT * FROM class_requests WHERE id=?");
        $stmt->execute([$id]);
        $request = $stmt->fetch();
        
        if($request){
            $stmt2 = $pdo->prepare("INSERT INTO classes (class_name, tutor_id, schedule_datetime, description) VALUES (?,?,?,?)");
            $stmt2->execute([$request['topic'], $_SESSION['admin_id'], $request['schedule'], $request['description']]);
            
            $stmt3 = $pdo->prepare("UPDATE class_requests SET status='approved' WHERE id=?");
            $stmt3->execute([$id]);
        }
    } elseif($action==='decline'){
        $stmt = $pdo->prepare("UPDATE class_requests SET status='declined' WHERE id=?");
        $stmt->execute([$id]);
    }
}

$stmt = $pdo->query("SELECT cr.*, u.username AS student_name FROM class_requests cr JOIN users u ON cr.student_id=u.id ORDER BY cr.id DESC");
$requests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Class Requests - Admin</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="dashboard-container">
<aside class="sidebar">
</aside>

<main class="main-content">
<h1>Class Requests</h1>
<table border="1" cellpadding="5">
<tr>
    <th>ID</th>
    <th>Student</th>
    <th>Topic</th>
    <th>Schedule</th>
    <th>Description</th>
    <th>Status</th>
    <th>Actions</th>
</tr>
<?php foreach($requests as $r): ?>
<tr>
    <td><?= $r['id'] ?></td>
    <td><?= htmlspecialchars($r['student_name']) ?></td>
    <td><?= htmlspecialchars($r['topic']) ?></td>
    <td><?= htmlspecialchars($r['schedule']) ?></td>
    <td><?= htmlspecialchars($r['description']) ?></td>
    <td><?= $r['status'] ?></td>
    <td>
        <?php if($r['status']=='pending'): ?>
            <a href="?action=approve&id=<?= $r['id'] ?>">Approve</a> |
            <a href="?action=decline&id=<?= $r['id'] ?>">Decline</a>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
</main>
</div>
</body>
</html>
