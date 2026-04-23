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

if (!isset($_GET['class_id'])) {
    header("Location: my_classes.php");
    exit();
}

$class_id = intval($_GET['class_id']);

// Fetch class info
$stmt = $pdo->prepare("SELECT c.*, u.username AS tutor_name FROM classes c JOIN users u ON c.tutor_id = u.id WHERE c.class_id=?");
$stmt->execute([$class_id]);
$class = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$class) { echo "Class not found!"; exit(); }

$meeting_link = $class['meeting_link'] ?? '#';
$schedule_datetime = $class['schedule_datetime'] ?? '';
$schedule_display = date('D, M d, Y H:i', strtotime($schedule_datetime));


$now = new DateTime();
$classTime = new DateTime($schedule_datetime);
$canJoin = $now >= $classTime;
?>

<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($class['class_name']) ?> - Beyond Learning</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/style2.css">
<style>
.dashboard-container { display:flex; }
.sidebar { width:250px; min-height:100vh; flex-shrink:0; }
.main-content { flex:1; padding:2rem; }
.class-card { background:#fff; padding:2rem; border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.2); width:100%; text-align:left; }
.class-card h1 { margin-bottom:1rem; color:#333; font-size:2rem; }
.class-card p { margin:0.5rem 0; font-size:1rem; color:#555; }
.join-btn { margin-top:1.5rem; padding:1rem 2rem; font-size:1.1rem; background:#ff6600; color:#fff; border:none; border-radius:8px; cursor:pointer; text-decoration:none; display:<?= $canJoin ? 'inline-block' : 'none' ?>; }
.join-btn:hover { background:#e65c00; }
.countdown { font-weight:bold; color:#007bff; margin-top:0.8rem; }
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
        <li><a href="my_classes.php" class="active">📚 My Classes</a></li>
        <li><a href="available_classes.php">🎓 Available Classes</a></li>
        <li><a href="archived_classes.php">📦 Archived Classes</a></li>
        <li><a href="activity_logs.php">📜 Activity Logs</a></li>
        <li><a href="settings.php">⚙️ Settings</a></li>
        <li><a href="logout.php">🚪 Logout</a></li>
    </ul>
</aside>

<main class="main-content">
<div class="class-card">
    <h1><?= htmlspecialchars($class['class_name']) ?></h1>
    <p><strong>Tutor:</strong> <?= htmlspecialchars($class['tutor_name']) ?> | 
       <strong>Schedule:</strong> <?= $schedule_display ?> | 
       <strong>Price:</strong> ₱<?= number_format($class['price'],2) ?></p>
    <p><?= htmlspecialchars($class['description']) ?></p>


<a id="joinBtn" href="<?= htmlspecialchars($meeting_link) ?>" target="_blank" class="join-btn">Join Online Class</a>
<div id="countdown" class="countdown"></div>


</div>
</main>
</div>

<script>
// Countdown timer
const classTime = new Date("<?= $schedule_datetime ?>").getTime();
const joinBtn = document.getElementById('joinBtn');
const countdownEl = document.getElementById('countdown');

if(countdownEl && !joinBtn.style.display || joinBtn.style.display === "none"){
    const timer = setInterval(()=>{
        const now = new Date().getTime();
        const distance = classTime - now;

        if(distance <= 0){
            clearInterval(timer);
            countdownEl.textContent = "Class is now available!";
            joinBtn.style.display = "inline-block";
        } else {
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000*60*60));
            const minutes = Math.floor((distance % (1000*60*60)) / (1000*60));
            const seconds = Math.floor((distance % (1000*60)) / 1000);
            countdownEl.textContent = `Class starts in ${hours}h ${minutes}m ${seconds}s`;
        }
    },1000);
}
</script>

</body>
</html>
