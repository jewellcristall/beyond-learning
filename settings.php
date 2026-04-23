<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = '';

// Get user info
$stmt = $pdo->prepare("SELECT username, email, created_at FROM users WHERE id = ? AND is_deleted = 0");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) die("User not found");

// Helper to log activity
function logActivity($pdo, $user_id, $action) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, ip_address, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$user_id, $action, $ip]);
}

// Handle profile update
if (isset($_POST['update_profile'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    if ($username && $email) {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $user_id]);

        logActivity($pdo, $user_id, "Updated profile info");

        $msg = "Profile updated successfully!";
        $user['username'] = $username;
        $user['email'] = $email;
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($current, $row['password_hash'])) {
        if ($new === $confirm) {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$hash, $user_id]);

            logActivity($pdo, $user_id, "Changed password");

            $msg = "Password changed successfully!";
        } else {
            $msg = "New passwords do not match.";
        }
    } else {
        $msg = "Current password incorrect.";
    }
}

// Handle account deletion
if (isset($_POST['delete_account'])) {
    // Log deletion BEFORE removing rows
    logActivity($pdo, $user_id, "Deleted account");

    // Remove from enrolled classes
    $stmt = $pdo->prepare("DELETE FROM user_classes WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Remove class requests
    $stmt = $pdo->prepare("DELETE FROM class_requests WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Delete the user
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    // Destroy session first
session_destroy();

// Show buffer page
echo "
<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<title>Deleting Account...</title>
<style>
body { margin:0; padding:0; font-family:Arial,sans-serif; display:flex; justify-content:center; align-items:center; height:100vh; background:#f5f5f5; }
.container { text-align:center; padding:2rem; background:#fff; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
.loader { border:8px solid #f3f3f3; border-top:8px solid #dc3545; border-radius:50%; width:60px; height:60px; animation:spin 1s linear infinite; margin:0 auto; }
@keyframes spin { 0% { transform:rotate(0deg); } 100% { transform:rotate(360deg); } }
</style>
</head>
<body>
<div class='container'>
    <div class='loader'></div>
    <h2>Deleting your account...</h2>
    <p>You will be redirected shortly.</p>
</div>
<script>
setTimeout(function() { window.location.href = 'index.php?deleted=1'; }, 2000);
</script>
</body>
</html>
";
exit();

}

// Fetch user activity logs
$stmt = $pdo->prepare("SELECT action, ip_address, created_at FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
$stmt->execute([$user_id]);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Settings - Beyond Learning</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/style2.css">
<style>
.settings-container { display:flex; gap:1.5rem; }
.settings-card {
    background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.08);
    padding:1.5rem; width:100%;
}
.settings-card h3 { margin-bottom:1rem; }
.settings-card label { display:block; margin-top:0.8rem; font-weight:bold; }
.settings-card input { width:100%; padding:0.6rem; margin-top:0.3rem; border:1px solid #ccc; border-radius:8px; font-size:0.9rem; }
.settings-card button {
    margin-top:1rem; padding:0.7rem 1.2rem; border:none; border-radius:8px;
    background:#007bff; color:#fff; cursor:pointer; font-size:0.95rem;
}
.settings-card button.delete-btn { background:#dc3545; }
.msg { margin-bottom:1rem; color:#28a745; font-weight:bold; }
</style>
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2><span class="highlight">BEYOND</span></h2>
            <div class="welcome-user">Welcome, <?= htmlspecialchars($user['username']) ?></div>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php">📊 Dashboard</a></li>
            <li><a href="my_classes.php">📚 My Classes</a></li>
            <li><a href="available_classes.php">🎓 Available Classes</a></li>
            <li><a href="archived_classes.php">📦 Archived Classes</a></li>
            <li><a href="activity_logs.php">📜 Activity Logs</a></li>
            <li><a href="settings.php" class="active">⚙️ Settings</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </aside>

<main class="main-content">
    <div class="settings-container">
        <div class="settings-card">
            <h3>Account Info</h3>
            <?php if($msg): ?><div class="msg"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
            <form method="POST">
                <label>Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                <label>Account Created</label>
                <input type="text" value="<?= date('F j, Y, g:i a', strtotime($user['created_at'])) ?>" disabled>
                <button type="submit" name="update_profile">Update Profile</button>
            </form>
        </div>

        <div class="settings-card">
            <h3>Change Password</h3>
            <form method="POST">
                <label>Current Password</label>
                <input type="password" name="current_password" required>
                <label>New Password</label>
                <input type="password" name="new_password" required>
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required>
                <button type="submit" name="change_password">Change Password</button>
            </form>
            <h3 style="margin-top:2rem; color:#dc3545;">Delete Account</h3>
            <form method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.')">
                <button type="submit" name="delete_account" class="delete-btn">Delete Account</button>
            </form>
        </div>
    </div>
</main>

</div>
</body>
</html>
