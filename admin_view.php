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

if (isset($_GET['delete_user_id'])) {
    $del_user_id = intval($_GET['delete_user_id']);

    $stmt = $pdo->prepare("DELETE FROM user_classes WHERE user_id = ?");
    $stmt->execute([$del_user_id]);

    $stmt = $pdo->prepare("DELETE FROM class_requests WHERE user_id = ?");
    $stmt->execute([$del_user_id]);

    // Delete user
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$del_user_id]);

    log_activity($admin_id, "Deleted user ID $del_user_id");

    header("Location: admin_view.php?class_id=$class_id");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM classes WHERE class_id = ? AND tutor_id = ? LIMIT 1");
$stmt->execute([$class_id, $admin_id]);
$class = $stmt->fetch();

if (!$class) {
    die("Class not found or you do not have permission to view it.");
}

// Fetch enrolled students
$stmt = $pdo->prepare("
    SELECT u.id, u.username, u.email 
    FROM users u
    INNER JOIN user_classes uc ON u.id = uc.user_id
    WHERE uc.class_id = ?
");
$stmt->execute([$class_id]);
$students = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Class - <?= htmlspecialchars($class['class_name']) ?></title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/style2.css">
<style>
.dashboard-container { display:flex; min-height:100vh; }
.sidebar { width:220px; background:#ff6600; color:#fff; display:flex; flex-direction:column; }
.sidebar-header { padding:1rem; text-align:center; font-weight:bold; font-size:1.2rem; }
.sidebar-header div { font-size:0.85rem; margin-top:0.5rem; }
.sidebar-nav { list-style:none; padding:0; margin:0; flex-grow:1; }
.sidebar-nav li { margin:0; }
.sidebar-nav li a { display:block; padding:0.75rem 1rem; color:#fff; text-decoration:none; transition:0.3s; }
.sidebar-nav li a:hover, .sidebar-nav li a.active { background:#e65500; }
.main-content { flex-grow:1; padding:2rem; background:#f5f5f5; }
.class-info { background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 6px 20px rgba(0,0,0,0.1); margin-bottom:2rem; }
.class-info h2 { margin:0 0 0.5rem; color:#ff6600; }
.class-info p { margin:0 0 0.25rem 0; color:#555; }
.students-table { width:100%; border-collapse:collapse; background:#fff; border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,0.1); overflow:hidden; }
.students-table th, .students-table td { padding:0.75rem 1rem; text-align:left; border-bottom:1px solid #eee; }
.students-table th { background:#ff6600; color:#fff; }
.delete-btn { display:inline-block; margin:0; padding:0.3rem 0.6rem; background:#dc3545; color:#fff; border-radius:8px; text-decoration:none; font-size:0.85rem; transition:0.3s; }
.delete-btn:hover { background:#c82333; }
.empty-msg { padding:1rem; color:#555; text-align:center; }
</style>
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>BEYOND</h2>
        </div>
        <ul class="sidebar-nav">
            <li><a href="admin_dashboard.php">📊 Dashboard</a></li>
            <li><a href="admin_classes.php" class="active">📚 My Classes</a></li>
            <li><a href="add_class.php">➕ Add Class</a></li>
            <li><a href="admin_settings.php">⚙️ Settings</a></li>
            <li><a href="ad_actlogs.php">📜 Activity Logs</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="class-info">
            <h2><?= htmlspecialchars($class['class_name']) ?></h2>
            <p><strong>Description:</strong> <?= htmlspecialchars($class['description']) ?></p>
            <p><strong>Schedule:</strong> <?= date("F d, Y - h:i A", strtotime($class['schedule_datetime'])) ?></p>
            <p><strong>Duration:</strong> <?= htmlspecialchars($class['duration_minutes']) ?> mins</p>
            <p><strong>Price:</strong> ₱<?= number_format($class['price'], 2) ?></p>
        </div>

        <h3>Enrolled Students</h3>
        <?php if(empty($students)): ?>
            <div class="empty-msg">No students enrolled yet.</div>
        <?php else: ?>
            <table class="students-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($students as $index => $student): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($student['username']) ?></td>
                            <td><?= htmlspecialchars($student['email']) ?></td>
                            <td>
                                <a href="admin_view.php?class_id=<?= $class_id ?>&delete_user_id=<?= $student['id'] ?>" 
                                   class="delete-btn" 
                                   onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
