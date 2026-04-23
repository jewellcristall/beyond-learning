<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch archived classes
$stmt = $pdo->prepare("
    SELECT uc.*, c.class_name, c.description, c.schedule, c.duration, t.username as tutor_name
    FROM user_classes uc
    JOIN classes c ON uc.class_id = c.class_id
    JOIN users t ON c.tutor_id = t.id
    WHERE uc.user_id = ? AND uc.archived = 1
    ORDER BY uc.enrolled_at DESC
");
$stmt->execute([$user_id]);
$archived_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Archived Classes - Beyond Learning</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/style2.css">
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2><span class="highlight">BEYOND</span></h2>
            <div class="welcome-user">Welcome, <?= htmlspecialchars($username) ?></div>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php">📊 Dashboard</a></li>
            <li><a href="my_classes.php">📚 My Classes</a></li>
            <li><a href="available_classes.php">🎓 Available Classes</a></li>
            <li><a href="archived_classes.php" class="active">📦 Archived Classes</a></li>
            <li><a href="activity_logs.php">📜 Activity Logs</a></li>
            <li><a href="settings.php">⚙️ Settings</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1>Archived Classes</h1>

        <?php if (count($archived_classes) > 0): ?>
            <div class="classes-grid">
                <?php foreach ($archived_classes as $class): ?>
                    <div class="class-card">
                        <h3><?= htmlspecialchars($class['class_name']) ?></h3>
                        <div class="class-meta">
                            Tutor: <?= htmlspecialchars($class['tutor_name']) ?> |
                            Schedule: <?= htmlspecialchars($class['schedule']) ?> |
                            Duration: <?= htmlspecialchars($class['duration']) ?> mins
                        </div>
                        <p><?= htmlspecialchars($class['description']) ?></p>
                        <form method="POST" action="my_classes.php" style="margin-top:0.5rem;">
                            <input type="hidden" name="class_id" value="<?= $class['class_id'] ?>">
                            <button type="submit" name="action" value="restore" class="btn-primary">Restore Class</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state"><h3>No archived classes yet</h3></div>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
