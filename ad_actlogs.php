<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_email = $_SESSION['admin_email'] ?? 'admin@gmail.com';

$stmt = $pdo->query("SELECT * FROM activity_logs ORDER BY created_at DESC");
$logs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Activity Logs - Beyond Learning</title>
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
h1 { color:#ff6600; margin-bottom:1.5rem; }
.table-container { background:#fff; padding:1.5rem; border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,0.1); overflow-x:auto; }
table { width:100%; border-collapse:collapse; }
th, td { padding:0.75rem 1rem; text-align:left; border-bottom:1px solid #eee; font-size:0.95rem; }
th { background:#ff9966; color:#fff; }
tr:hover { background:#fff3e6; }
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
            <li><a href="admin_classes.php">📚 My Classes</a></li>
            <li><a href="add_class.php">➕ Add Class</a></li>
            <li><a href="admin_settings.php">⚙️ Settings</a></li>
            <li><a href="ad_actlogs.php" class="active">📜 Activity Logs</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1>Admin Activity Logs</h1>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User/Admin ID</th>
                        <th>Action</th>
                        <th>IP Address</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($logs): ?>
                        <?php foreach($logs as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['id']) ?></td>
                            <td><?= htmlspecialchars($log['user_id']) ?></td>
                            <td><?= htmlspecialchars($log['action']) ?></td>
                            <td><?= htmlspecialchars($log['ip_address']) ?></td>
                            <td><?= htmlspecialchars($log['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;">No activity logs found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
