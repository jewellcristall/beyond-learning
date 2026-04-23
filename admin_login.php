<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $errorMessage = "Please enter both email and password.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin) {
            if (!$admin['is_active']) {
                $errorMessage = "Your account is inactive. Contact support.";
            } elseif (password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_email'] = $admin['email'];

                $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                $stmt_log = $pdo->prepare("INSERT INTO activity_logs (user_id, action, ip_address, created_at) VALUES (?, ?, ?, NOW())");
                $stmt_log->execute([$admin['id'], "Admin logged in", $ip]);

                header("Location: admin_dashboard.php");
                exit();
            } else {
                $errorMessage = "Invalid credentials. Try again.";
            }
        } else {
            $errorMessage = "Invalid credentials. Try again.";
        }
    }
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BEYOND - admin</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/style2.css">
<style>
body {
    margin:0; font-family: 'Segoe UI', sans-serif; 
    background:linear-gradient(135deg,#ff6600,#ff9966); 
    min-height:100vh; display:flex; align-items:center; justify-content:center;
}
.login-container {
    background:#fff; border-radius:16px; 
    box-shadow:0 10px 25px rgba(0,0,0,0.2); 
    width:100%; max-width:400px; padding:2rem; 
    animation:fadeIn 0.8s ease;
}
@keyframes fadeIn { from {opacity:0; transform:translateY(-20px);} to {opacity:1; transform:translateY(0);} }
.login-header { text-align:center; margin-bottom:2rem; }
.login-header h2 { color:#ff6600; margin:0; font-size:2rem; letter-spacing:1px; }
.login-container form input {
    width:100%; padding:0.75rem 1rem; margin-bottom:1rem; 
    border-radius:10px; border:1px solid #ccc; font-size:1rem; transition:0.3s;
}
.login-container form input:focus { 
    border-color:#ff6600; outline:none; box-shadow:0 0 5px rgba(255,102,0,0.5);
}
.login-container form button {
    width:100%; padding:0.75rem; border:none; border-radius:10px; 
    background:#ff6600; color:#fff; font-weight:bold; cursor:pointer; font-size:1rem; transition:0.3s;
}
.login-container form button:hover { background:#e65500; }
.error-box { background:#f8d7da; color:#721c24; padding:0.75rem; border-radius:8px; margin-bottom:1rem; text-align:center; font-size:0.95rem; }
</style>
</head>
<body>
<div class="login-container">
    <div class="login-header">
        <h2>Admin Login</h2>
    </div>
    <?php if($errorMessage): ?>
        <div class="error-box"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>
    <form method="POST" autocomplete="off">
        <input type="email" name="email" placeholder="Email" required autofocus>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
