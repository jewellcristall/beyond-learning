<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

require_once 'includes/db.php';

$user_id = $_SESSION["user_id"];
$search = trim($_GET['search'] ?? '');
$filter = trim($_GET['filter'] ?? '');


$sql = "SELECT action, created_at 
        FROM activity_logs 
        WHERE user_id = ?";
$params = [$user_id];

if ($search) {
    $sql .= " AND action LIKE ?";
    $params[] = "%$search%";
}

if ($filter && $filter !== 'all') {
    $sql .= " AND action LIKE ?";
    $params[] = "%$filter%";
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Activity Logs - Beyond Learning</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/style2.css">
<style>
.logs-list {
  list-style: none;
  padding: 0;
  margin: 1rem 0;
}
.log-item {
  background: #fff;
  padding: 0.8rem 1rem;
  border-radius: 8px;
  margin-bottom: 0.6rem;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  display: flex;
  justify-content: space-between;
}
.log-action { font-weight: 500; }
.log-time { color: #666; font-size: 0.9rem; }

.search-filter {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
}
.search-filter input[type="text"] {
  flex: 1;
  padding: 0.5rem;
  border-radius: 6px;
  border: 1px solid #ccc;
}
.search-filter select {
  padding: 0.5rem;
  border-radius: 6px;
  border: 1px solid #ccc;
}
.search-filter button {
  padding: 0.5rem 1rem;
  border-radius: 6px;
  border: none;
  background: #ff6600;
  color: #fff;
  cursor: pointer;
}
</style>
</head>
<body>
<div class="dashboard-container">

  <aside class="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo">
        <h2><span class="brand"><span class="highlight">BEYOND</span></span></h2>
      </div>
      <div class="welcome-user">Welcome, <?= htmlspecialchars($_SESSION["username"]); ?></div>
    </div>
    <ul class="sidebar-nav">
      <li><a href="dashboard.php">📊 Dashboard</a></li>
      <li><a href="my_classes.php">📚 My Classes</a></li>
      <li><a href="available_classes.php">🎓 Available Classes</a></li>
      <li><a href="archived_classes.php">📦 Archived Classes</a></li>
      <li><a href="activity_logs.php" class="active">📜 Activity Logs</a></li>
      <li><a href="settings.php">⚙️ Settings</a></li>
      <li><a href="logout.php" class="logout-link">🚪 Logout</a></li>
    </ul>
  </aside>


  <main class="main-content">
    <div class="dashboard-header">
      <h1 class="page-title">Activity Logs</h1>
    </div>


    <form method="GET" class="search-filter">
      <input type="text" name="search" placeholder="Search logs..." value="<?= htmlspecialchars($search) ?>">
      <select name="filter">
        <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Actions</option>
        <option value="enrolled" <?= $filter === 'enrolled' ? 'selected' : '' ?>>Enrolled</option>
        <option value="dropped" <?= $filter === 'dropped' ? 'selected' : '' ?>>Dropped</option>
        <option value="archived" <?= $filter === 'archived' ? 'selected' : '' ?>>Archived</option>
        <option value="restored" <?= $filter === 'restored' ? 'selected' : '' ?>>Restored</option>
        <option value="favorite" <?= $filter === 'favorite' ? 'selected' : '' ?>>Favorite</option>
      </select>
      <button type="submit">Search</button>
    </form>

    <?php if (count($logs) > 0): ?>
      <ul class="logs-list">
        <?php foreach ($logs as $log): ?>
          <li class="log-item">
            <span class="log-action"><?= htmlspecialchars($log['action']); ?></span>
            <span class="log-time"><?= htmlspecialchars($log['created_at']); ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <div class="empty-state"><h3>No activity logs found</h3></div>
    <?php endif; ?>
  </main>
</div>
</body>
</html>
