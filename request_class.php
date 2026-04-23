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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic = $_POST['topic'] ?? '';
    $schedule = $_POST['schedule'] ?? '';
    $description = $_POST['description'] ?? '';

    if($topic && $schedule){
        // make sure the 'schedule' column exists in class_requests table
        $stmt = $pdo->prepare("INSERT INTO class_requests (user_id, class_title, schedule, description, created_at, student_id) VALUES (?, ?, ?, ?, NOW(), ?)");
        $stmt->execute([$user_id, $topic, $schedule, $description, $user_id]);
        $success = "Your class request has been submitted!";
    } else {
        $error = "Topic and schedule are required!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Request Class - Beyond Learning</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/style2.css">
<style>
.main-content {
    flex:1;
    padding:2rem;
    display:flex;
    justify-content:center;
}

.request-card {
    background:#fff;
    padding:2rem;
    border-radius:12px;
    box-shadow:0 5px 20px rgba(0,0,0,0.1);
    width:100%;
    max-width:500px;
}

.request-card h1 {
    margin-bottom:1.5rem;
    color:#333;
    text-align:center;
    font-size:1.8rem;
}

.request-card label {
    display:block;
    margin-top:1rem;
    margin-bottom:0.3rem;
    font-weight:600;
    color:#555;
}

.request-card input,
.request-card textarea {
    width:100%;
    padding:0.6rem;
    border:1px solid #ccc;
    border-radius:6px;
    font-size:1rem;
    box-sizing:border-box;
}

.request-card textarea {
    min-height:100px;
    resize:vertical;
}

.request-card button {
    margin-top:1.5rem;
    width:100%;
    padding:0.8rem;
    background:#ff6600;
    color:#fff;
    border:none;
    border-radius:8px;
    font-size:1rem;
    cursor:pointer;
    transition: background 0.3s;
}

.request-card button:hover {
    background:#e65c00;
}

.message {
    text-align:center;
    font-weight:bold;
    margin-bottom:1rem;
}

.message.success { color: #155724; background:#d4edda; padding:0.5rem 1rem; border-radius:6px; }
.message.error { color: #721c24; background:#f8d7da; padding:0.5rem 1rem; border-radius:6px; }
</style>
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
        <li><a href="archived_classes.php">📦 Archived Classes</a></li>
        <li><a href="activity_logs.php">📜 Activity Logs</a></li>
        <li><a href="settings.php">⚙️ Settings</a></li>
        <li><a href="logout.php">🚪 Logout</a></li>
    </ul>
</aside>

<main class="main-content">
    <div class="request-card">
        <h1>Request a Class</h1>

        <?php if(isset($success)): ?>
            <div class="message success"><?= $success ?></div>
        <?php endif; ?>
        <?php if(isset($error)): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Class Topic</label>
            <input type="text" name="topic" placeholder="Enter topic" required>

            <label>Preferred Schedule</label>
            <input type="datetime-local" name="schedule" required>

            <label>Description (optional)</label>
            <textarea name="description" placeholder="Additional notes..."></textarea>

            <button type="submit">Submit Request</button>
        </form>
    </div>
</main>
</div>
</body>
</html>
