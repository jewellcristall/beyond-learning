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

$successMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $class_id = intval($_POST['class_id']);

    try {
        $stmt_class = $pdo->prepare("SELECT class_name FROM classes WHERE class_id = ?");
        $stmt_class->execute([$class_id]);
        $class = $stmt_class->fetch();
        $class_name = $class['class_name'] ?? 'Unknown Class';

        if ($action === 'drop') {
            $stmt = $pdo->prepare("DELETE FROM user_classes WHERE user_id = ? AND class_id = ?");
            $stmt->execute([$user_id, $class_id]);
            $successMessage = "Class dropped!";

            // Log activity
            $stmt_log = $pdo->prepare("INSERT INTO activity_logs (user_id, action, created_at) VALUES (?, ?, NOW())");
            $stmt_log->execute([$user_id, "Dropped class: $class_name"]);

        } elseif ($action === 'archive') {
            $stmt = $pdo->prepare("UPDATE user_classes SET archived = 1 WHERE user_id = ? AND class_id = ?");
            $stmt->execute([$user_id, $class_id]);
            $successMessage = "Class archived!";

            $stmt_log = $pdo->prepare("INSERT INTO activity_logs (user_id, action, created_at) VALUES (?, ?, NOW())");
            $stmt_log->execute([$user_id, "Archived class: $class_name"]);

        } elseif ($action === 'favorite') {
            $stmt = $pdo->prepare("UPDATE user_classes SET favorite = 1 - favorite WHERE user_id = ? AND class_id = ?");
            $stmt->execute([$user_id, $class_id]);

            $stmt_check = $pdo->prepare("SELECT favorite FROM user_classes WHERE user_id = ? AND class_id = ?");
            $stmt_check->execute([$user_id, $class_id]);
            $fav_status = $stmt_check->fetchColumn();

            $successMessage = $fav_status ? "Class added to favorites!" : "Class removed from favorites!";

            $stmt_log = $pdo->prepare("INSERT INTO activity_logs (user_id, action, created_at) VALUES (?, ?, NOW())");
            $stmt_log->execute([$user_id, ($fav_status ? "Favorited class: $class_name" : "Unfavorited class: $class_name")]);

        } elseif ($action === 'restore') {
            $stmt = $pdo->prepare("UPDATE user_classes SET archived = 0 WHERE user_id = ? AND class_id = ?");
            $stmt->execute([$user_id, $class_id]);
            $successMessage = "Class restored!";

            $stmt_log = $pdo->prepare("INSERT INTO activity_logs (user_id, action, created_at) VALUES (?, ?, NOW())");
            $stmt_log->execute([$user_id, "Restored class: $class_name"]);
        }
    } catch (Exception $e) {
        $successMessage = "Error: " . $e->getMessage();
    }
}

// Fetch enrolled classes (excluding archived)
$stmt = $pdo->prepare("
    SELECT uc.*, c.class_name, c.description, c.schedule, c.duration, t.username as tutor_name
    FROM user_classes uc
    JOIN classes c ON uc.class_id = c.class_id
    JOIN users t ON c.tutor_id = t.id
    WHERE uc.user_id = ? AND uc.archived = 0
");
$stmt->execute([$user_id]);
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Classes - Beyond Learning</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/style2.css">
<style>
.notification {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: #28a745;
    color: #fff;
    padding: 0.8rem 1.2rem;
    border-radius: 6px;
    display: none;
    z-index: 9999;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
.notification.error { background: #dc3545; }

.settings-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: transparent;
    border: none;
    cursor: pointer;
    font-size: 18px;
}
.settings-menu {
    display: none;
    position: absolute;
    top: 30px;
    right: 10px;
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    min-width: 120px;
    z-index: 100;
}
.settings-menu form {
    margin: 0;
}
.settings-menu button {
    width: 100%;
    padding: 0.5rem;
    background: none;
    border: none;
    text-align: left;
    cursor: pointer;
}
.settings-menu button:hover {
    background: #f0f0f0;
}
.class-card {
    position: relative;
}
.favorite-star {
    font-size: 18px;
    color: gold;
    cursor: pointer;
    margin-left: 0.5rem;
}
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
        <h1>My Classes</h1>

        <?php if(count($classes) > 0): ?>
            <div class="classes-grid">
                <?php foreach($classes as $class): ?>
                    <div class="class-card">
                        <h3>
                            <a href="class_view.php?class_id=<?= $class['class_id'] ?>" style="text-decoration:none; color:inherit;">
                                <?= htmlspecialchars($class['class_name']) ?>
                            </a>
                            <?php if(($class['favorite'] ?? 0)): ?>
                                <span class="favorite-star">★</span>
                            <?php endif; ?>
                        </h3>
                        <div class="class-meta">
                            Tutor: <?= htmlspecialchars($class['tutor_name']) ?> |
                            Schedule: <?= htmlspecialchars($class['schedule']) ?> |
                            Duration: <?= htmlspecialchars($class['duration']) ?> mins
                        </div>
                        <p><?= htmlspecialchars($class['description']) ?></p>

                        <button class="settings-btn">⚙️</button>
                        <div class="settings-menu">
                            <form method="POST">
                                <input type="hidden" name="class_id" value="<?= $class['class_id'] ?>">
                                <button type="submit" name="action" value="drop">Drop Class</button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="class_id" value="<?= $class['class_id'] ?>">
                                <button type="submit" name="action" value="archive">Archive Class</button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="class_id" value="<?= $class['class_id'] ?>">
                                <button type="submit" name="action" value="favorite">
                                    <?= ($class['favorite'] ?? 0) ? 'Unfavorite' : 'Favorite' ?>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state"><h3>You haven't enrolled in any classes yet</h3></div>
        <?php endif; ?>
    </main>
</div>

<div class="notification" id="notification"></div>

<script>
const notification = document.getElementById('notification');
<?php if($successMessage): ?>
notification.textContent = "<?= $successMessage ?>";
notification.style.display = 'block';
setTimeout(() => { notification.style.display = 'none'; }, 2500);
<?php endif; ?> 

document.querySelectorAll('.settings-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const menu = this.nextElementSibling;
        const allMenus = document.querySelectorAll('.settings-menu');
        allMenus.forEach(m => { if(m !== menu) m.style.display = 'none'; });
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    });
});

document.addEventListener('click', function(e) {
    if(!e.target.closest('.class-card')) {
        document.querySelectorAll('.settings-menu').forEach(m => m.style.display = 'none');
    }
});
</script>
</body>
</html>
