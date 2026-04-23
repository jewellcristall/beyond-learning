<?php
session_start();
require_once __DIR__ . '/includes/auth.php';

if (isset($_SESSION['admin_id'])) {
    log_activity($_SESSION['admin_id'], "Admin logged out");
    $redirectUrl = 'admin_login.php';
} elseif (isset($_SESSION['user_id'])) {
    log_activity($_SESSION['user_id'], "User logged out");
    $redirectUrl = 'index.php';
} else {
    $redirectUrl = 'index.php';
}

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Logging out...</title>
<meta http-equiv="refresh" content="2;url=<?= htmlspecialchars($redirectUrl) ?>">
<style>
body { 
    display:flex; 
    justify-content:center; 
    align-items:center; 
    height:100vh; 
    font-family:sans-serif; 
    background:#f5f5f5; 
}
.logout-msg { 
    text-align:center; 
    padding:2rem; 
    background:#fff; 
    border-radius:12px; 
    box-shadow:0 4px 12px rgba(0,0,0,0.1); 
}
</style>
</head>
<body>
<div class="logout-msg">
    <h2>Logging out...</h2>
    <p>You will be redirected shortly.</p>
</div>
<script>
setTimeout(() => { window.location.href = '<?= htmlspecialchars($redirectUrl) ?>'; }, 2000);
</script>
</body>
</html>
