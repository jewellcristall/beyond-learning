<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$admin_email = $_SESSION['admin_email'] ?? 'Admin';

$stmt = $pdo->prepare("SELECT * FROM classes WHERE tutor_id = ?");
$stmt->execute([$admin_id]);
$classes = $stmt->fetchAll();

$stmt_req = $pdo->prepare("
    SELECT cr.*, u.username AS student_name 
    FROM class_requests cr
    JOIN users u ON cr.student_id = u.id
    ORDER BY cr.created_at DESC
");
$stmt_req->execute();
$requested_classes = $stmt_req->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Classes - Beyond Learning</title>
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
.cards-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:1.5rem; }
.card { background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 6px 20px rgba(0,0,0,0.1); position:relative; }
.card h3 { margin:0 0 0.5rem; color:#ff6600; }
.card p { margin:0; color:#555; }
.empty-msg { padding:1rem; text-align:center; color:#555; }
.card .btn { display:inline-block; padding:0.4rem 0.8rem; margin-top:0.75rem; background:#ff9966; color:#fff; border-radius:8px; text-decoration:none; font-size:0.85rem; transition:0.3s; }
.card .btn:hover { background:#ff6600; }
.card .dropdown { position:absolute; top:1rem; right:1rem; cursor:pointer; font-size:1.25rem; user-select:none; }
.card .dropdown-content { display:none; position:absolute; right:0; top:100%; background:#fff; box-shadow:0 4px 12px rgba(0,0,0,0.15); border-radius:8px; overflow:hidden; z-index:10; }
.card .dropdown-content a { display:block; padding:0.5rem 1rem; text-decoration:none; color:#333; font-size:0.9rem; transition:0.2s; }
.card .dropdown-content a:hover { background:#ff6600; color:#fff; }
</style>
<script>
function toggleDropdown(id) {
    const el = document.getElementById(id);
    el.style.display = el.style.display === 'block' ? 'none' : 'block';
}
document.addEventListener('click', function(e) {
    document.querySelectorAll('.dropdown-content').forEach(dc => {
        if (!dc.parentElement.contains(e.target)) {
            dc.style.display = 'none';
        }
    });
});
</script>
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>BEYOND</h2>
        </div>
        <ul class="sidebar-nav">
            <li><a href="admin_dashboard.php">📊 Dashboard</a></li>
            <li><a href="admin_classes.php" class="active">📚 My Classes</a></li>
            <li><a href="add_class.php">➕ Add Class</a></li>
            <li><a href="admin_settings.php">⚙️ Settings</a></li>
            <li><a href="ad_actlogs.php">📜 Activity Logs</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1>Requested Classes</h1>

        <?php if(empty($requested_classes)): ?>
            <div class="empty-msg">No requested classes yet.</div>
        <?php else: ?>
            <div class="cards-grid">
                <?php foreach($requested_classes as $req): ?>
                    <div class="card">
                        <h3><?= htmlspecialchars($req['class_title']) ?></h3>
                        <p><strong>Student:</strong> <?= htmlspecialchars($req['student_name']) ?></p>
                        <p><strong>Submitted:</strong> <?= date('M d, Y H:i', strtotime($req['created_at'])) ?></p>
                        <p><strong>Description: </strong><?= htmlspecialchars($req['description']) ?></p>
                        <div style="margin-top:0.5rem;">
                            <a href="add_class.php?id=<?= $req['request_id'] ?>" class="btn">Add Class</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h1 style="margin-top:2rem;">My Classes</h1>

        <?php if(empty($classes)): ?>
            <div class="empty-msg">You don't have any classes yet.</div>
        <?php else: ?>
            <div class="cards-grid">
                <?php foreach($classes as $class): 
                    $dropdownId = "dropdown-".$class['class_id'];
                ?>
                    <div class="card">
                        <h3><?= htmlspecialchars($class['class_name']) ?></h3>
                        <p><?= htmlspecialchars($class['description']) ?></p>
                        <a href="admin_view.php?class_id=<?= $class['class_id'] ?>" class="btn">View Class</a>
                        <a<?= $class['class_id'] ?>" class="btn">Start session</a>
                        <div class="dropdown" onclick="toggleDropdown('<?= $dropdownId ?>')">⋮</div>
                        <div class="dropdown-content" id="<?= $dropdownId ?>">
                            <a href="edit_class.php?class_id=<?= $class['class_id'] ?>">Edit</a>
                            <a href="delete_class.php?class_id=<?= $class['class_id'] ?>" onclick="return confirm('Are you sure you want to delete this class?');">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
