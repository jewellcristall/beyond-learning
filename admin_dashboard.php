<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_username = $_SESSION['admin_email'] ?? 'Admin';

// Fetch stats
$total_students = $pdo->query("SELECT COUNT(*) FROM users WHERE is_deleted=0")->fetchColumn();
$total_classes = $pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn();
$total_enrollments = $pdo->query("SELECT COUNT(*) FROM user_classes")->fetchColumn();
$total_requests = $pdo->query("SELECT COUNT(*) FROM class_requests")->fetchColumn(); // optional

?>

<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Beyond Learning</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/style2.css">
<style>
.dashboard-container { display:flex; min-height:100vh; }
.sidebar {
    width:220px; background:#ff6600; color:#fff; display:flex; flex-direction:column;
}
.sidebar-header { padding:1rem; text-align:center; font-weight:bold; font-size:1.2rem; }
.sidebar-nav { list-style:none; padding:0; margin:0; flex-grow:1; }
.sidebar-nav li { margin:0; }
.sidebar-nav li a {
    display:block; padding:0.75rem 1rem; color:#fff; text-decoration:none; transition:0.3s;
}
.sidebar-nav li a:hover, .sidebar-nav li a.active { background:#e65500; }
.main-content { flex-grow:1; padding:2rem; background:#f5f5f5; }
.cards-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1.5rem; margin-bottom:2rem; }
.card { background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 6px 20px rgba(0,0,0,0.1); text-align:center; }
.card h3 { margin:0 0 0.5rem; color:#ff6600; font-size:1.5rem; }
.card p { margin:0; font-size:0.9rem; color:#555; }
.quick-actions { display:flex; flex-wrap:wrap; gap:1rem; }
.action-btn { flex:1; min-width:150px; text-align:center; padding:1rem; background:#ff9966; color:#fff; border-radius:12px; text-decoration:none; font-weight:bold; transition:0.3s; }
.action-btn:hover { background:#ff6600; }
</style>
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>BEYOND</h2>
        </div>
        <ul class="sidebar-nav">
            <li><a href="admin_dashboard.php" class="active">📊 Dashboard</a></li>
            <li><a href="admin_classes.php">📚 My Classes</a></li>
            <li><a href="add_class.php">➕ Add Class</a></li>
            <li><a href="admin_settings.php">⚙️ Settings</a></li>
            <li><a href="ad_actlogs.php">📜 Activity Logs</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </aside>

```
<main class="main-content">
    <h1>Dashboard</h1>

    <div class="cards-grid">
        <div class="card">
            <h3><?= $total_students ?></h3>
            <p>Total Students</p>
        </div>
        <div class="card">
            <h3><?= $total_classes ?></h3>
            <p>Total Classes</p>
        </div>
        <div class="card">
            <h3><?= $total_enrollments ?></h3>
            <p>Total Enrollments</p>
        </div>
        <div class="card">
            <h3><?= $total_requests ?></h3>
            <p>Class Requests</p>
        </div>
    </div>

    

    <h2>Quick Actions</h2>
    <div class="quick-actions">
        <a href="add_class.php" class="action-btn">➕ Add Class</a>
        <a href="admin_classes.php" class="action-btn">📚 View Classes</a>
        <a href="ad_actlogs.php" class="action-btn">📜 Activity Logs</a>
        <a href="admin_classes.php" class="action-btn">👥 View Students</a>
    </div>
</main>
```

</div>
</body>
</html>
