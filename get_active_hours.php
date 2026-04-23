<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['labels'=>[], 'data'=>[]]);
    exit();
}

$user_id = $_SESSION['user_id'];
$days = intval($_GET['days'] ?? 7);

$labels = [];
$data = [];
for($i=$days-1; $i>=0; $i--){
    $date = date('Y-m-d', strtotime("-$i days"));
    $labels[] = $date;
    $data[$date] = 0;
}

$stmt = $pdo->prepare("
    SELECT DATE(created_at) as done_date, COUNT(*) as lessons_done
    FROM activity_logs
    WHERE user_id = ? AND action LIKE 'Completed % in %' AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
    GROUP BY DATE(created_at)
");
$stmt->execute([$user_id, $days]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($results as $row){
    $data[$row['done_date']] = (int)$row['lessons_done'];
}

echo json_encode([
    'labels' => $labels,
    'data' => array_values($data)
]);
