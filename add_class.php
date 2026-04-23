<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'] ?? 0;
$admin_email = $_SESSION['admin_email'] ?? 'Admin';

$successMessage = '';
$errorMessage = '';

// pre-fill from request if ?id=<request_id> is provided
$request_id = $_GET['id'] ?? null;
$class_name = '';
$description = '';
$price = '';
$schedule = '';
$duration = '';

if ($request_id) {
    $stmt = $pdo->prepare("SELECT * FROM class_requests WHERE request_id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch();
    if ($request) {
        $class_name = $request['class_title'];
        $description = $request['description'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = trim($_POST['class_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $schedule = trim($_POST['schedule'] ?? '');
    $duration = trim($_POST['duration'] ?? '');

    // validation
    if (!$class_name || !$description || $price === '' || !$schedule || !$duration) {
        $errorMessage = "Please fill in all fields.";
    } else if (!is_numeric($price) || $price <= 0) {
        $errorMessage = "Price must be a positive number.";
    } else if (!strtotime($schedule)) {
        $errorMessage = "Invalid schedule datetime.";
    } else if (!is_numeric($duration) || $duration <= 0) {
        $errorMessage = "Duration must be a positive number.";
    } else {
        // insert class
        $stmt = $pdo->prepare("
            INSERT INTO classes (class_name, description, tutor_id, price, schedule_datetime, duration_minutes)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        if ($stmt->execute([$class_name, $description, $admin_id, $price, $schedule, $duration])) {
            $successMessage = "Class successfully added!";

            if ($request_id) {
                $stmt_del = $pdo->prepare("DELETE FROM class_requests WHERE request_id = ?");
                $stmt_del->execute([$request_id]);
            }

            log_activity($admin_id, "Added new class: $class_name");
        } else {
            $errorMessage = "Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Class - Beyond Learning</title>
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
.form-container { background:#fff; padding:2rem; border-radius:12px; max-width:500px; margin:auto; box-shadow:0 6px 20px rgba(0,0,0,0.1); }
.form-container h2 { color:#ff6600; text-align:center; margin-bottom:1.5rem; }
.form-container input, .form-container textarea { width:100%; padding:0.75rem 1rem; margin-bottom:1rem; border:1px solid #ccc; border-radius:8px; font-size:1rem; }
.form-container button { width:100%; padding:0.75rem; border:none; border-radius:8px; background:#ff6600; color:#fff; font-weight:bold; cursor:pointer; font-size:1rem; transition:0.3s; }
.form-container button:hover { background:#e65500; }
.success-box { background:#d4edda; color:#155724; padding:0.75rem; border-radius:8px; margin-bottom:1rem; text-align:center; }
.error-box { background:#f8d7da; color:#721c24; padding:0.75rem; border-radius:8px; margin-bottom:1rem; text-align:center; }
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
            <li><a href="add_class.php" class="active">➕ Add Class</a></li>
            <li><a href="admin_settings.php">⚙️ Settings</a></li>
            <li><a href="ad_actlogs.php">📜 Activity Logs</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="form-container">
            <h2>Add New Class</h2>

            <?php if($successMessage): ?>
                <div class="success-box"><?= htmlspecialchars($successMessage) ?></div>
            <?php endif; ?>

            <?php if($errorMessage): ?>
                <div class="error-box"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="text" name="class_name" placeholder="Class Name" required value="<?= htmlspecialchars($class_name) ?>">
                <textarea name="description" placeholder="Class Description" rows="5" required><?= htmlspecialchars($description) ?></textarea>
                <input type="number" name="price" placeholder="Class Price (₱)" step="0.01" min="1" required value="<?= htmlspecialchars($price) ?>">
                <input type="datetime-local" name="schedule" required value="<?= htmlspecialchars($schedule) ?>">
                <input type="number" name="duration" placeholder="Duration (minutes)" min="1" required value="<?= htmlspecialchars($duration) ?>">
                <button type="submit">Add Class</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
