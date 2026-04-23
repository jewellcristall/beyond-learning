<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$admin_email = $_SESSION['admin_email'] ?? 'admin@gmail.com';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_email = trim($_POST['email'] ?? '');
    $current_password = trim($_POST['current_password'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if ($new_email && $new_email !== $admin_email) {
        $stmt = $pdo->prepare("UPDATE users SET email=? WHERE id=?");
        if ($stmt->execute([$new_email, $admin_id])) {
            $_SESSION['admin_email'] = $new_email;
            $admin_email = $new_email;
            $success = "Email updated successfully.";

            log_activity($admin_id, "Updated email to $new_email");
        } else {
            $error = "Failed to update email. Try again.";
        }
    }

    if ($current_password && $new_password && $confirm_password) {
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id=?");
        $stmt->execute([$admin_id]);
        $row = $stmt->fetch();
        if ($row && password_verify($current_password, $row['password_hash'])) {
            if ($new_password === $confirm_password) {
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password_hash=? WHERE id=?");
                if ($stmt->execute([$new_hash, $admin_id])) {
                    $success = "Password updated successfully.";

                    log_activity($admin_id, "Updated account password");
                } else {
                    $error = "Failed to update password. Try again.";
                }
            } else {
                $error = "New password and confirm password do not match.";
            }
        } else {
            $error = "Current password is incorrect.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Settings - Beyond Learning</title>
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
.settings-form { max-width:500px; margin:auto; background:#fff; padding:2rem; border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,0.1); }
.settings-form h2 { color:#ff6600; margin-bottom:1.5rem; text-align:center; }
.settings-form input { width:100%; padding:0.75rem 1rem; margin-bottom:1rem; border-radius:10px; border:1px solid #ccc; font-size:1rem; }
.settings-form input:focus { border-color:#ff6600; outline:none; box-shadow:0 0 5px rgba(255,102,0,0.5); }
.settings-form button { width:100%; padding:0.75rem; border:none; border-radius:10px; background:#ff6600; color:#fff; font-weight:bold; cursor:pointer; font-size:1rem; }
.settings-form button:hover { background:#e65500; }
.message { padding:0.75rem; border-radius:8px; margin-bottom:1rem; text-align:center; font-size:0.95rem; }
.success { background:#d4edda; color:#155724; }
.error { background:#f8d7da; color:#721c24; }
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
            <li><a href="admin_settings.php" class="active">⚙️ Settings</a></li>
            <li><a href="ad_actlogs.php">📜 Activity Logs</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1>Settings</h1>
        <div class="settings-form">
            <?php if($success): ?><div class="message success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
            <?php if($error): ?><div class="message error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <form method="POST">
                <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($admin_email) ?>" required>
                <hr style="margin:1rem 0; border:0; border-top:1px solid #ccc;">
                <input type="password" name="current_password" placeholder="Current Password">
                <input type="password" name="new_password" placeholder="New Password">
                <input type="password" name="confirm_password" placeholder="Confirm New Password">
                <button type="submit">Save Changes</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
